<?php

namespace App\Http\Controllers;

use App\Exports\CardTransactionsExport;
use App\IssueConfig;
use App\Services\CIC\CICApiService;
use App\UnitItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\WForm;
use App\WFormType;
use App\Reference;
use Maatwebsite\Excel\Facades\Excel;

class CICController extends Controller
{
    // Modal type => list stored in session by SupportsController@index
    private $listKeys = [
        'ac' => 'multiACData',
        'loan' => 'multiLoanData',
        'cc' => 'multiCCData',
        'dc' => 'multiDCData',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->middleware(['role_or_permission:superadmin|admin|wformComplain'])->only('index', 'show', 'miniStatement', 'cardTransactions', 'cardTransactionsExport', 'disputeStatement', 'disputeTicket', 'ticketStatement');
    }

    public function index(Request $request)
    {
        return $this->show($request);
    }

    public function show(Request $request, $type = null, $key = null)
    {
        $info = Session::get('supportCustomerInfo');

        // If no type/key provided, default to the first available account or card in session
        if ($type === null || $key === null) {
            if (!empty($info)) {
                foreach (['ac' => 'multiACData', 'loan' => 'multiLoanData', 'cc' => 'multiCCData', 'dc' => 'multiDCData'] as $t => $k) {
                    if (!empty($info[$k]) && isset($info[$k][0])) {
                        $type = $t;
                        $key = 0;
                        break;
                    }
                }
            }
        }

        $row = ($type !== null && $key !== null) ? $this->getSessionRow($info, $type, $key) : null;

        $tblData = $info['tblData'] ?? [];
        $account = [];
        $loan = [];
        $card = [];

        if ($row !== null) {
            if ($type == 'ac') {
                $account = $row;
            } elseif ($type == 'loan') {
                $loan = $row;
                // Account selected from the A/C modal before the loan modal
                $acKey = $request->query('ac');
                if ($acKey !== null && is_array($info['multiACData'] ?? null) && isset($info['multiACData'][$acKey])) {
                    $account = $info['multiACData'][$acKey];
                }
            } else {
                $card = $row;
                if ($type == 'dc') {
                    $card['availableBalance'] = '******';
                }
            }
        }

        $title = "Customer Information";
        $title_for_layout = "Customer Information";

        $acQuery = $type == 'loan' && $request->query('ac') !== null ? '?ac=' . (int) $request->query('ac') : '';
        $miniStatementUrl = ($type !== null && $key !== null) ? (route('cic.miniStatement', [$type, $key]) . $acQuery) : '';
        $cardTransactionsUrl = ($type !== null && $key !== null) ? (route('cic.cardTransactions', [$type, $key]) . $acQuery) : '';
        $cardTransactionsExportUrl = ($type !== null && $key !== null) ? (route('cic.cardTransactionsExport', [$type, $key]) . $acQuery) : '';
        $disputeStatementUrl = ($type !== null && $key !== null) ? (route('cic.disputeStatement', [$type, $key]) . $acQuery) : '';

        $searchDataForView = $info['searchDataForView'] ?? [
            'account_type' => 2,
            'account_number' => '',
            'cif_mobile' => '',
            'cardSearchingType' => 'Mobile',
        ];
        $multiACData = $info['multiACData'] ?? [];
        $multiCCData = $info['multiCCData'] ?? [];
        $multiDCData = $info['multiDCData'] ?? [];
        $multiLoanData = $info['multiLoanData'] ?? [];

        $cicOpenModal = Session::pull('cicOpenModal', false);

        return view('CIC.customer_info', compact(
            'title', 'title_for_layout', 'type', 'key', 'tblData', 'account', 'loan', 'card', 'row',
            'miniStatementUrl', 'cardTransactionsUrl', 'cardTransactionsExportUrl', 'disputeStatementUrl',
            'searchDataForView', 'multiACData', 'multiCCData', 'multiDCData', 'multiLoanData', 'cicOpenModal'
        ));
    }

    // Ajax: mini statement of one card (?card=<index> from the dropdown, cards resolved by resolveCards)
    // API has no date fields, rows are filtered by transactionDate between from / to
    public function miniStatement(Request $request, $type, $key)
    {
        $info = Session::get('supportCustomerInfo');
        $row = $this->getSessionRow($info, $type, $key);

        if ($row === null) {
            return response()->json(['status' => false, 'message' => 'Customer information not found. Please search again.']);
        }

        $range = $this->dateRange($request);
        if (isset($range['error'])) {
            return response()->json(['status' => false, 'message' => $range['error']]);
        }
        list($fromDate, $toDate) = $range;

        $cicApi = new CICApiService();

        $resolved = $this->resolveCards($request, $cicApi, $info, $type, $row);
        if (isset($resolved['error'])) {
            return response()->json(['status' => false, 'message' => $resolved['error']]);
        }
        $accountNumber = $resolved['accountNumber'];
        $cardIndex = $this->cardIndex($request, $resolved['cards']);
        $card = $resolved['cards'][$cardIndex];

        $response = $cicApi->miniStatement(trim($card['cardNumber']), $this->cardCif($card, $info), $card['cardCurrency'] ?? '050');
        $isSuccess = ($response['responseCode'] ?? null) == 200;

        $statements = [[
            'card' => $card,
            'info' => $this->statementInfo($request, $info, $type, $row, $card, $accountNumber),
            'statement' => $isSuccess ? ($response['data'] ?? []) : [],
            'rows' => $isSuccess ? $this->filterByDate($response['data']['miniStatementDetails'] ?? [], 'transactionDate', $fromDate, $toDate) : [],
            'error' => $isSuccess ? '' : ($response['messages'][0] ?? 'Mini statement not found.'),
        ]];

        $html = view('CIC.partials.mini_statement', compact('accountNumber', 'statements', 'fromDate', 'toDate'))->render();

        return response()->json(['status' => true, 'html' => $html, 'cards' => $this->cardOptions($resolved['cards']), 'card' => $cardIndex]);
    }

    // Ajax: dispute statement => card transaction log (card/transactions API) of one card between two dates (max 1 year)
    // Cards resolved like the mini statement (cc/dc => selected card, ac/loan => cards of the account),
    // ?card=<index> picks the card from the dropdown, its clientCode is sent as the CIF
    public function disputeStatement(Request $request, $type, $key)
    {
        $info = Session::get('supportCustomerInfo');
        $row = $this->getSessionRow($info, $type, $key);

        if ($row === null) {
            return response()->json(['status' => false, 'message' => 'Customer information not found. Please search again.']);
        }

        $range = $this->dateRange($request);
        if (isset($range['error'])) {
            return response()->json(['status' => false, 'message' => $range['error']]);
        }
        list($fromDate, $toDate) = $range;

        $cicApi = new CICApiService();

        $resolved = $this->resolveCards($request, $cicApi, $info, $type, $row);
        if (isset($resolved['error'])) {
            return response()->json(['status' => false, 'message' => $resolved['error']]);
        }
        $accountNumber = $resolved['accountNumber'];

        $cardIndex = $this->cardIndex($request, $resolved['cards']);
        $card = $resolved['cards'][$cardIndex];

        // One row per transaction, card fields merged in so the ticket page can be filled from both
        $rows = [];
        $error = '';
        $response = $cicApi->cardTransactions(trim($card['cardNumber']), $this->cardCif($card, $info), $fromDate->format('Y-m-d'), $toDate->format('Y-m-d'), $card['cardCurrency'] ?? '050');
        if (($response['responseCode'] ?? null) != 200) {
            $error = ($card['cardNumber'] ?? '') . ': ' . ($response['messages'][0] ?? 'Card transaction not found.');
        } else {
            foreach ($this->filterByDate($response['data']['statementDetailList'] ?? [], 'tranDate', $fromDate, $toDate) as $txn) {
                $rows[] = array_merge($card, $txn);
            }
        }

        // Rows kept in session so the Action buttons can open the ticket page without trusting request data
        Session::put('cicDisputeStatement', compact('type', 'key', 'accountNumber', 'rows'));

        $disputeIssues = $this->disputeIssues();
        $html = view('CIC.partials.dispute_statement', compact('type', 'key', 'accountNumber', 'card', 'rows', 'error', 'disputeIssues', 'fromDate', 'toDate'))->render();

        return response()->json(['status' => true, 'html' => $html, 'cards' => $this->cardOptions($resolved['cards']), 'card' => $cardIndex]);
    }

    // Dispute statement Action => New Service Request page with issue, category, issue_config fields (by api_key) and customer info filled
    public function disputeTicket($type, $key, $row, $issue)
    {
        $canCreateTicket = (function_exists('user_touch_permission') && user_touch_permission()) 
            || (Auth::check() && (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('admin')));
        if (!$canCreateTicket) {
            flash('Only touch point users are allowed to create dispute tickets.', 'warning');
            return redirect()->route('cic.customerInfo', [$type, $key]);
        }

        $info = Session::get('supportCustomerInfo');
        $dispute = Session::get('cicDisputeStatement');
        $issues = $this->disputeIssues();

        if (empty($info) || empty($dispute) || $dispute['type'] != $type || $dispute['key'] != $key || !isset($dispute['rows'][$row])) {
            flash('Dispute statement not found. Please load the dispute statement again.', 'warning');
            return redirect()->route('cic.customerInfo', [$type, $key]);
        }
        if (!isset($issues[$issue])) {
            flash('Invalid dispute issue.', 'warning');
            return redirect()->route('cic.customerInfo', [$type, $key]);
        }

        $unitItem = UnitItem::where('id', $issue)->first();
        if (empty($unitItem)) {
            flash($issues[$issue] . ' issue not found.', 'warning');
            return redirect()->route('cic.customerInfo', [$type, $key]);
        }

        $card = $dispute['rows'][$row];
        $tblData = $info['tblData'] ?? [];
        $customerName = trim($card['cardHolderName'] ?? '') ?: ($tblData['customer_name'] ?? '');
        $mobileNumber = $card['mobileNumber'] ?? ($tblData['mobile_number'] ?? '');
        $email = $card['email'] ?? ($tblData['def_email_addr'] ?? '');
        // Dispute Account => the searched account, Dispute Credit Card => the card account
        $accountNumber = $issue == getId('DISPUTE_ACCOUNT') ? $dispute['accountNumber'] : ($card['accountNumber'] ?? $dispute['accountNumber']);

        // Customer info for the ticket page (read by SupportsController@newWForm)
        $customer = [
            'account_type' => $unitItem->product_type_id,
            'accountNumber' => $accountNumber,
            'accountId' => $accountNumber,
            'cardNo' => $card['cardNumber'] ?? '',
            'mask_card_no' => $card['cardNumber'] ?? '',
            'customer_name' => $customerName,
            'accountTitle' => $customerName,
            'CIF_number' => $tblData['CIF_number'] ?? '',
            'customer_mobile' => $mobileNumber,
            'mobileNumber' => $mobileNumber,
            'def_email_addr' => $email,
            'email' => $email,
            'dob' => $card['dob'] ?? '',
            'cardType' => $card['cardType'] ?? '',
            'cardStatus' => $card['cardStatus'] ?? '',
            'card_status' => $card['cardStatus'] ?? '',
            'cardProductName' => $card['cardProductName'] ?? '',
            'product_name' => $card['cardProductName'] ?? '',
            'clientCode' => $card['clientCode'] ?? '',
            'branchCode' => $card['branchCode'] ?? '',
        ];
        $isComplaint = ($unitItem->issues_from ?? '') === 'complaint';
        $redirectUrl = $isComplaint ? 'Supports/NewComplaint' : 'Supports/NewWForm';
        Session::put('searchDataForView', url($redirectUrl) . '?' . http_build_query($customer));

        // Old input for the ticket page: category + issue selected, issue_config fields filled by api_key ("field_name:apiKey")
        $prefill = [
            'service_category' => $unitItem->issue_categories_id,
            'w_form_type' => $unitItem->id,
            'complaint_type' => $unitItem->id,
        ];
        foreach (IssueConfig::where('issue_id', $unitItem->id)->get() as $config) {
            $keys = explode(':', (string) $config->api_key);
            if (count($keys) == 2 && isset($card[$keys[1]]) && !is_array($card[$keys[1]])) {
                $prefill[$config->field_name] = $card[$keys[1]];
            } elseif ($config->field_name == 'tr_date' && empty($prefill['tr_date'])) {
                $txnDate = !empty($card['tranDate']) ? \DateTime::createFromFormat('!Ymd', substr((string) $card['tranDate'], 0, 8)) : false;
                $prefill['tr_date'] = $txnDate ? $txnDate->format('d-m-Y') : date('d-m-Y');
            } elseif ($config->field_name == 'tr_time' && empty($prefill['tr_time'])) {
                $txnTime = !empty($card['transactionTime']) ? \DateTime::createFromFormat('H:i:s', $card['transactionTime']) : false;
                $prefill['tr_time'] = $txnTime ? $txnTime->format('h:i A') : date('h:i A');
            }
        }
        Session::put('cicDisputePrefill', $prefill);
        Session::put('cic_ticket_return_url', route('cic.customerInfo', [$type, $key]));

        return redirect($redirectUrl);
    }

    // Dispute issues (LOCALIDEnum / UATIDEnum / LIVEIDEnum by environment) => button label
    private function disputeIssues()
    {
        return array_filter([
            getId('DISPUTE_ACCOUNT') => 'Dispute Account',
            getId('DISPUTE_CREDIT_CARD') => 'Dispute Credit Card',
        ], function ($label, $issueId) {
            return $issueId !== '';
        }, ARRAY_FILTER_USE_BOTH);
    }

    // Ajax: card transaction history between two dates (max 1 year), same card selection as miniStatement
    public function cardTransactions(Request $request, $type, $key)
    {
        $result = $this->fetchCardTransactions($request, $type, $key);
        if (isset($result['error'])) {
            return response()->json(['status' => false, 'message' => $result['error']]);
        }

        $html = view('CIC.partials.card_transactions', $result)->render();

        // Account statement has no card dropdown
        return response()->json(['status' => true, 'html' => $html, 'cards' => $result['cards'] ?? [], 'card' => $result['card'] ?? null]);
    }

    // Excel download of the same card transaction history
    public function cardTransactionsExport(Request $request, $type, $key)
    {
        $result = $this->fetchCardTransactions($request, $type, $key);
        if (isset($result['error'])) {
            flash($result['error'], 'warning');
            return redirect()->back();
        }

        $result['customerName'] = $type == 'cc' || $type == 'dc'
            ? ($result['statements'][0]['card']['cardHolderName'] ?? '')
            : (Session::get('supportCustomerInfo')['tblData']['customer_name'] ?? '');

        $fileName = ($type == 'ac' ? 'Account_Statement_' : 'Card_Transactions_') . $result['fromDate']->format('Ymd') . '_' . $result['toDate']->format('Ymd') . '.xlsx';

        return Excel::download(new CardTransactionsExport($result), $fileName);
    }

    // Validates the date range (max 1 year) and calls the card transaction API for the selected card (?card=<index>)
    // Returns ['accountNumber', 'statements', 'fromDate', 'toDate'] (+ 'cards', 'card' for cards) or ['error' => message]
    private function fetchCardTransactions(Request $request, $type, $key)
    {
        $info = Session::get('supportCustomerInfo');
        $row = $this->getSessionRow($info, $type, $key);

        if ($row === null) {
            return ['error' => 'Customer information not found. Please search again.'];
        }

        $range = $this->dateRange($request);
        if (isset($range['error'])) {
            return $range;
        }
        list($fromDate, $toDate) = $range;

        $cicApi = new CICApiService();

        // Account => account statement (core getAccountStatement), cc/dc => card transaction history
        if ($type == 'ac') {
            return $this->fetchAccountStatement($request, $cicApi, $info, $row, $fromDate, $toDate);
        }

        $resolved = $this->resolveCards($request, $cicApi, $info, $type, $row);
        if (isset($resolved['error'])) {
            return $resolved;
        }
        $accountNumber = $resolved['accountNumber'];
        $card = $this->cardIndex($request, $resolved['cards']);
        $cards = $this->cardOptions($resolved['cards']);
        $selected = $resolved['cards'][$card];

        $response = $cicApi->cardTransactions(trim($selected['cardNumber']), $this->cardCif($selected, $info), $fromDate->format('Y-m-d'), $toDate->format('Y-m-d'), $selected['cardCurrency'] ?? '050');
        $isSuccess = ($response['responseCode'] ?? null) == 200;

        $statements = [[
            'card' => $selected,
            'info' => $this->statementInfo($request, $info, $type, $row, $selected, $accountNumber),
            'statement' => $isSuccess ? ($response['data'] ?? []) : [],
            'rows' => $isSuccess ? $this->filterByDate($response['data']['statementDetailList'] ?? [], 'tranDate', $fromDate, $toDate) : [],
            'error' => $isSuccess ? '' : ($response['messages'][0] ?? 'Card transaction not found.'),
        ]];

        return compact('accountNumber', 'statements', 'fromDate', 'toDate', 'cards', 'card');
    }

    // Card dropdown options: index => "cardNumber · product · DC/CR"
    private function cardOptions($cards)
    {
        $options = [];
        foreach ($cards as $i => $card) {
            $options[] = [
                'value' => $i,
                'label' => collect([$card['cardNumber'] ?? '', $card['cardProductName'] ?? '', $card['typeOfCard'] ?? ''])
                    ->map(function ($text) {
                        return trim((string) $text);
                    })
                    ->filter()
                    ->implode(' · '),
            ];
        }

        return $options;
    }

    // Selected card index from ?card=, first card when missing or out of range
    private function cardIndex(Request $request, $cards)
    {
        $index = (int) $request->query('card', 0);

        return isset($cards[$index]) ? $index : 0;
    }

    // Card APIs do not apply the date range, rows outside from / to are dropped here
    // Dates come as yyyymmdd (optionally with time) or dd-mm-yyyy, rows with an unreadable date are kept
    private function filterByDate($rows, $field, $fromDate, $toDate)
    {
        return collect(is_array($rows) ? $rows : [])
            ->filter(function ($row) use ($field, $fromDate, $toDate) {
                $date = $this->apiDate($row[$field] ?? null);
                return !$date || ($date >= $fromDate && $date <= $toDate);
            })
            ->values()
            ->all();
    }

    private function apiDate($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return false;
        }

        return \DateTime::createFromFormat('!d-m-Y', $value) ?: \DateTime::createFromFormat('!Ymd', substr($value, 0, 8));
    }

    // Validates the from/to query dates (Y-m-d, no future date, max 1 year)
    // Returns [fromDate, toDate] or ['error' => message]
    private function dateRange(Request $request)
    {
        $fromDate = \DateTime::createFromFormat('!Y-m-d', (string) $request->query('from'));
        $toDate = \DateTime::createFromFormat('!Y-m-d', (string) $request->query('to'));

        if (!$fromDate || !$toDate) {
            return ['error' => 'Please select a valid From Date and To Date.'];
        }
        if ($fromDate > $toDate) {
            return ['error' => 'From Date can not be greater than To Date.'];
        }
        if ($toDate > new \DateTime('today')) {
            return ['error' => 'To Date can not be a future date.'];
        }
        // Same day one year back, clamped to month end (29 Feb => 28 Feb), matches the date picker
        $minFromDate = (clone $toDate)->modify('first day of -12 months');
        $minFromDate->setDate($minFromDate->format('Y'), $minFromDate->format('m'), min($toDate->format('d'), $minFromDate->format('t')));
        if ($fromDate < $minFromDate) {
            return ['error' => 'Date range can not be more than 1 year.'];
        }

        return [$fromDate, $toDate];
    }

    // Account statement in the same shape as the card statements so both views/excel render it
    private function fetchAccountStatement(Request $request, CICApiService $cicApi, $info, $row, $fromDate, $toDate)
    {
        $accountNumber = trim($row['accountId'] ?? '');
        if ($accountNumber === '') {
            return ['error' => 'Account number not found.'];
        }

        $response = $cicApi->getAccountStatement($accountNumber, $fromDate->format('Ymd'), $toDate->format('Ymd'));
        $isSuccess = ($response['responseCode'] ?? null) == 200;
        $data = $isSuccess ? $this->trimKeys($response['data'] ?? []) : [];

        $rows = array_map(function ($txn) {
            $txn = $this->trimKeys($txn);
            $value = is_numeric($txn['transactionAmount'] ?? null) ? (float) $txn['transactionAmount'] : 0;
            $description = collect([$txn['transactionNarration'] ?? '', $txn['transactionDesc1'] ?? '', $txn['transactionDesc2'] ?? '', $txn['transactionDesc3'] ?? ''])
                ->map(function ($text) {
                    return trim((string) $text);
                })
                ->filter()
                ->implode(' / ');

            // Negative amount => debit
            return [
                'tranDate' => $txn['transactionPostDate'] ?? '',
                'postDate' => $txn['transactionPostDate'] ?? '',
                'tranDesc' => $description,
                'billingAmount' => abs($value),
                'transSign' => $value < 0 ? 'D' : 'C',
                'transId' => $txn['transactionRefNo'] ?? '',
                'chequeNo' => $txn['transactionChequeNo'] ?? '',
            ];
        }, is_array($data['transactionList'] ?? null) ? $data['transactionList'] : []);

        $statements = [[
            'card' => [],
            'info' => $this->statementInfo($request, $info, 'ac', $row, [], $accountNumber),
            'statement' => ['openingBalance' => $data['balanceAtPeriodStart'] ?? null],
            'rows' => $rows,
            'error' => $isSuccess ? '' : ($response['messages'][0] ?? 'Account statement not found.'),
        ]];

        return compact('accountNumber', 'statements', 'fromDate', 'toDate');
    }

    // Spec sample has padded keys (" balanceAtPeriodStart ")
    private function trimKeys($data)
    {
        if (!is_array($data)) {
            return [];
        }

        $result = [];
        foreach ($data as $k => $v) {
            $result[is_string($k) ? trim($k) : $k] = $v;
        }

        return $result;
    }

    // Card numbers are taken from session / API, never from the request
    // cc/dc   => the selected card
    // ac/loan => every card of the customer linked to the selected account number
    // Returns ['accountNumber' => ..., 'cards' => [...]] or ['error' => message]
    private function resolveCards(Request $request, CICApiService $cicApi, $info, $type, $row)
    {
        if ($type == 'cc' || $type == 'dc') {
            if (empty($row['cardNumber'])) {
                return ['error' => 'Card number not found.'];
            }
            $accountNumber = $row['accountNumber'] ?? '';
            $cards = [$row];
        } else {
            $account = $type == 'ac' ? $row : [];
            // Loan page: account selected from the A/C modal before the loan modal
            $acKey = $request->query('ac');
            if ($type == 'loan' && $acKey !== null && is_array($info['multiACData']) && isset($info['multiACData'][$acKey])) {
                $account = $info['multiACData'][$acKey];
            }

            $accountNumber = $account['accountId'] ?? '';

            if (empty($accountNumber)) {
                return ['error' => 'Account number not found. Please select an account first.'];
            }

            // Account API also returns the holder's linked credit cards (their accountNumber is the card account)
            $cardResponse = $cicApi->cardDetailsByAccount(trim($accountNumber));
            if (($cardResponse['responseCode'] ?? null) != 200) {
                return ['error' => $cardResponse['messages'][0] ?? 'Card list not found.'];
            }

            $cards = collect($cardResponse['data']['cardDetails'] ?? [])
                ->filter(function ($card) {
                    return !empty($card['cardNumber']);
                })
                ->values()
                ->all();

            if (empty($cards)) {
                return ['error' => 'No card found for account number ' . $accountNumber . '.'];
            }
        }

        return ['accountNumber' => $accountNumber, 'cards' => $cards];
    }

    // Statement header (Branch, A/C Number, Customer ID, A/C Title, A/C Type, Current Working Balance)
    // Searched account when the card belongs to it, otherwise the card's own account
    private function statementInfo(Request $request, $info, $type, $row, $card, $accountNumber)
    {
        if ($type == 'ac') {
            $account = $row;
        } elseif ($type == 'loan') {
            $acKey = $request->query('ac');
            $account = ($acKey !== null && isset($info['multiACData'][$acKey])) ? $info['multiACData'][$acKey] : [];
        } else {
            $account = collect($info['multiACData'] ?? [])->firstWhere('accountId', $row['accountNumber'] ?? null) ?: [];
        }

        $cardAccount = trim($card['accountNumber'] ?? '');
        $isOwnAccount = !empty($account) && ($cardAccount === '' || ltrim($cardAccount, '0') == ltrim($account['accountId'] ?? '', '0'));
        $isCreditCard = $type == 'cc' || strtoupper($card['typeOfCard'] ?? '') == 'CR';

        // Account balance is masked in session for some searches, real value kept in real_account_balance
        $accountBalance = $account['balance'] ?? null;
        if (!is_numeric($accountBalance)) {
            $accountBalance = Session::get('real_account_balance');
        }
        $workingBalance = $isCreditCard || !$isOwnAccount ? ($card['availableBalance'] ?? null) : $accountBalance;

        return [
            'branch' => $isOwnAccount ? ($account['branchName'] ?? '') : ($card['branchCode'] ?? ''),
            'accountNumber' => $isOwnAccount ? ($account['accountId'] ?? $accountNumber) : ($cardAccount ?: $accountNumber),
            'customerId' => $this->cardCif($card, $info),
            'title' => $isOwnAccount ? ($account['accountTitle'] ?? '') : trim($card['cardHolderName'] ?? ($card['embossName'] ?? '')),
            'accountType' => $isOwnAccount ? ($account['categoryTitle'] ?? '') : ($card['cardProductName'] ?? ''),
            'workingBalance' => is_numeric($workingBalance) ? (float) $workingBalance : null,
        ];
    }

    // CIF for the card APIs: card row clientCode, otherwise the searched customer's CIF
    private function cardCif($card, $info)
    {
        if (!empty($card['clientCode'])) {
            return trim($card['clientCode']);
        }

        return trim($info['tblData']['CIF_number'] ?? '');
    }

    private function getSessionRow($info, $type, $key)
    {
        $listKey = $this->listKeys[$type] ?? null;

        if (empty($info) || empty($listKey) || !is_array($info[$listKey]) || !isset($info[$listKey][$key])) {
            return null;
        }

        return $info[$listKey][$key];
    }

    // Ticket details statement check (ajax view for Supports/WFormDetails)
    // Defaults to loading statement rows where transaction date matches the ticket's TR Date
    public function ticketStatement(Request $request, $reference_number)
    {
        try {
            $refNumber = decrypt($reference_number);
        } catch (\Exception $e) {
            $refNumber = $reference_number;
        }

        $wform = WForm::where('reference_number', $refNumber)->first();
        if (empty($wform)) {
            return response()->json(['status' => false, 'message' => 'Ticket not found.']);
        }

        $wtype = WFormType::where('reference_number', $refNumber)->first();
        $extra = [];
        if (!empty($wtype->extra_field)) {
            $extra = json_decode($wtype->extra_field, true) ?: [];
        }

        $trDate = null;
        $trTime = null;
        $trAmount = null;
        $tnxNumber = null;
        foreach ($extra as $item) {
            if (isset($item['TR Date'])) $trDate = trim($item['TR Date']);
            if (isset($item['TR Time'])) $trTime = trim($item['TR Time']);
            if (isset($item['Amount'])) $trAmount = trim($item['Amount']);
            if (isset($item['TNX Number'])) $tnxNumber = trim($item['TNX Number']);
        }

        // Format default date: TR Date is typically d-m-Y (e.g. 01-10-2026) -> convert to Y-m-d
        $defaultDateYmd = '';
        if (!empty($trDate)) {
            $parsed = \DateTime::createFromFormat('!d-m-Y', $trDate);
            if (!$parsed) {
                $parsed = \DateTime::createFromFormat('!Y-m-d', $trDate);
            }
            if ($parsed) {
                $defaultDateYmd = $parsed->format('Y-m-d');
            }
        }
        if (empty($defaultDateYmd)) {
            $defaultDateYmd = date('Y-m-d');
        }

        $fromDate = $request->query('from', $defaultDateYmd);
        $toDate = $request->query('to', $defaultDateYmd);

        // Find the card number to query card transactions
        $cicApi = new CICApiService();
        $cardNumber = '';
        $cifNumber = $wform->SIF_Number ?? '';
        if ($wform->product_type == 1) {
            // Credit card ticket: account_number or mask_card_no holds the card number
            $cardNumber = !empty($wform->account_number) ? $wform->account_number : $wform->mask_card_no;
        } else {
            // Account ticket: find card by account
            $accountNumber = $wform->account_number;
            $cardRes = $cicApi->cardDetailsByAccount(trim($accountNumber));
            if (($cardRes['responseCode'] ?? null) == 200 && !empty($cardRes['data']['cardDetails'])) {
                $cards = $cardRes['data']['cardDetails'];
                $selected = $cards[0];
                foreach ($cards as $c) {
                    if (!empty($wform->mask_card_no) && ($c['cardNumber'] ?? '') == $wform->mask_card_no) {
                        $selected = $c;
                        break;
                    }
                }
                $cardNumber = $selected['cardNumber'] ?? '';
                $cifNumber = !empty($selected['clientCode']) ? $selected['clientCode'] : $cifNumber;
            }
        }

        if (empty($cardNumber)) {
            return response()->json([
                'status' => false,
                'message' => 'No linked card found for this account/ticket to pull statement.',
            ]);
        }

        // Query card transactions around the window
        $dtFrom = \DateTime::createFromFormat('!Y-m-d', $fromDate) ?: new \DateTime($fromDate);
        $dtTo = \DateTime::createFromFormat('!Y-m-d', $toDate) ?: new \DateTime($toDate);

        $apiStart = (clone $dtFrom)->modify('-15 days')->format('Y-m-d');
        $apiEnd = (clone $dtTo)->modify('+15 days')->format('Y-m-d');

        $response = $cicApi->cardTransactions($cardNumber, $cifNumber, $apiStart, $apiEnd);
        $isSuccess = ($response['responseCode'] ?? null) == 200;

        $rawList = $isSuccess ? ($response['data']['statementDetailList'] ?? []) : [];
        $error = $isSuccess ? '' : ($response['messages'][0] ?? 'Card transactions not found.');

        // Filter transactions strictly to the user requested [fromDate, toDate]
        $fromYmd = str_replace('-', '', $fromDate);
        $toYmd = str_replace('-', '', $toDate);
        $rows = [];
        foreach ($rawList as $tx) {
            $txDate = (string) ($tx['tranDate'] ?? '');
            if ($txDate >= $fromYmd && $txDate <= $toYmd) {
                $rows[] = $tx;
            }
        }

        $html = view('CIC.partials.ticket_statement', compact(
            'refNumber',
            'wform',
            'trDate',
            'trTime',
            'trAmount',
            'tnxNumber',
            'fromDate',
            'toDate',
            'defaultDateYmd',
            'cardNumber',
            'rows',
            'rawList',
            'error'
        ))->render();

        return response()->json([
            'status' => true,
            'html' => $html,
            'count' => count($rows),
            'total_in_window' => count($rawList),
        ]);
    }
}
