<?php

namespace App\Http\Controllers;


use App\BpId;
use App\OSBAPIResponse;
use App\Services\BPID\getCustomerDetailsService;
use App\Services\BPID\TokenApiService;
use App\Treasury;
use App\TreasuryBond;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BpIdController extends Controller
{

    public $checker = false;
    public $username;
    public $password;

    public function firstAccountApi(Request $request)
    {

        $accountDataArray = [];

        $account_number = $request->account_number;

        // Access Token
        $accessToken = TokenApiService::ApiCalling($account_number);


        if (!empty($accessToken)) {
            $responseAC = getCustomerDetailsService::getCustomerDetails(['account_number' => $account_number, 'accessToken' => $accessToken]);
            $accountDataArray = [];

            if (!empty($responseAC)) {
                $responseAC = json_decode($responseAC, true);

                $mainData = $responseAC['data'] ?? [];
                $apiKeys = [
                    'customerPhone',
                    'customerEmail',
                    'customerType',
                    'spouseName',
                    'branchName',
                    'customerTitle',
                    'accountTitle',
                    'accountId',
                    'branchCode',
                    'residence',

                    'gender',
                    'customerDoB',
                    'fatherName',
                    'motherName',
                    'customerNID',
                    'passportNumber',
                    'tinNumber',
                    'occupation',
                ];
                if (!empty($mainData)) {
                    foreach ($mainData as $key => $value) {
                        if (in_array($key, $apiKeys)) {
                            $accountDataArray[$key] = $value;
                        }
                    }
                    $addressParts = [];
                    if (!empty($mainData['contactAddress1'])) $addressParts[] = $mainData['contactAddress1'];
                    if (!empty($mainData['contactAddress2'])) $addressParts[] = $mainData['contactAddress2'];
                    if (!empty($mainData['contactAddress3'])) $addressParts[] = $mainData['contactAddress3'];
                    if (!empty($mainData['contactAddress4'])) $addressParts[] = $mainData['contactAddress4'];

                    $accountDataArray['presentAddress'] = implode(' ', $addressParts);
                    $accountDataArray['permanentAddress'] = implode(' ', $addressParts);
                    $accountDataArray['permanentAddress'] = implode(' ', $addressParts);

                    // set default value....
                    $accountDataArray['bankName'] = 'Prime Bank PLC';
                    $accountDataArray['residence'] = $accountDataArray['residence'] == 'BD' ? 'Resident' : 'Non-Resident';


                    $accountDataArray['customerDoB'] = date("Y-m-d", strtotime($accountDataArray['customerDoB']));
                    $accountDataArray['tinNumber'] = !empty($accountDataArray['tinNumber']) ? $accountDataArray['tinNumber'] : '827182199';
                    $accountDataArray['passportNumber'] = !empty($accountDataArray['passportNumber']) ? $accountDataArray['passportNumber'] : '1293827182199';
                }

                if (!empty($mainData['accounts'])) {
                    foreach ($mainData['accounts'] as $account) {
                        foreach ($account as $key => $value) {
                            if (in_array($key, $apiKeys)) {
                                $accountDataArray[$key] = $value;
                            }
                        }
                        break;
                    }
                }
            }
        }
        return response()->json([
            'data' => $accountDataArray,
        ]);
    }

    private function generateRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    public function getBpIdWithTreasury(Request $request)
    {
        $bpId = BpId::where('account_number', $request->account_number)->where('bp_id', '!=', null)->orderBy('id', 'DESC')->first();
        return response()->json([
            'status' => true,
            'bpId' => $bpId
        ]);
    }

    public function getBiddingDate(Request $req)
    {
        $biddingDate = '<option value="">Please Select</option>';
        if (!empty($req->treasury_type)) {
            if ($req->treasury_type == 'Bill') {
                $treasury_bills = Treasury::where('name', $req->treasury_bills)->where('status', 1)->first(['id', 'name', 'week', 'count']);
                if (!empty($treasury_bills)) {
                    $slots = generateAuctionSlots($treasury_bills->week, $treasury_bills->count, $req->bidding_month);
                    foreach ($slots as $bond) {
                        $biddingDate .= '<option value="' . $bond . '">' . $bond . '</option>';
                    }
                }
            }
            if ($req->treasury_type == 'Bond') {
                $treasury_bonds = TreasuryBond::where('name', $req->treasury_bonds)->where('status', 1)->first(['id', 'name', 'week', 'count']);
                if (!empty($treasury_bonds)) {
                    $slots = generateBondSlots($treasury_bonds->week, $treasury_bonds->count, $req->bidding_month);
                    foreach ($slots as $bond) {
                        $biddingDate .= '<option value="' . $bond . '">' . $bond . '</option>';
                    }
                }
            }

            return response()->json([
                'status' => true,
                'biddingDate' => $biddingDate,
            ]);
        }
    }

    public static function osbApiRequestResponse($params = array())
    {
        /*type Credit Card = 1, Account = 2, Debit Card = 3, Loan = 4, Trade = 5 */
        $modelName = new OSBAPIResponse;
        $modelName->reference_number = (!empty($params['reference_number'])) ? $params['reference_number'] : '';
        $modelName->account_number = (!empty($params['account_number'])) ? $params['account_number'] : '';
        $modelName->cif_number = (!empty($params['cif_number'])) ? $params['cif_number'] : '';
        $modelName->type = (!empty($params['type'])) ? $params['type'] : 0;
        $modelName->service = (!empty($params['service'])) ? $params['service'] : 0;
        $modelName->url = (!empty($params['url'])) ? $params['url'] : '';
        $modelName->json_node = (!empty($params['json_node'])) ? $params['json_node'] : '';
        $modelName->log_user = (!empty(Auth::user()->user_id)) ? Auth::user()->user_id : '';
        $modelName->status_code = (!empty($params['status_code'])) ? $params['status_code'] : '';
        $modelName->status_msg = (!empty($params['status_msg'])) ? $params['status_msg'] : '';
        $modelName->save();
    }
}
