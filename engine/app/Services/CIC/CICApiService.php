<?php

namespace App\Services\CIC;

use App\OSBAPIResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CIC Card APIs (wbs/CIC APIs.docx, updated by wbs/Update APIs-CIC.docx)
 * Local environment => mock response from app/APIDemoResponse/CIC/*.json
 * Otherwise        => real curl call
 *
 * Every method returns the decoded response array:
 * ['data' => [...], 'responseCode' => 200, 'messages' => [...]]
 */
class CICApiService
{
    private $localEnvironment = true;
    private $apiCredential;
    private $username;
    private $password;
    private $accessToken = null;

    private $encryptionKey = '59367512309356D8AC80BF0807DA5D49';
    private $encryptionIv = 'D12C4GJ5917638D9';

    public function __construct()
    {
        $this->apiCredential = DB::table('api_credential')->first();
        if (!empty($this->apiCredential)) {
            $this->username = $this->apiCredential->user_name;
            $this->password = base64_decode($this->apiCredential->user_password);
        }

        if (config('app.env') != 'local') {
            $this->localEnvironment = false;
        }
    }

    /* ======= 1) Card Status Change ========== */
    // Success => data.successCode == '00000'
    public function cardStatusChange($cardNumber, $cifNumber, $statusCode, $statusReasonCode, $startDt = null, $endDt = null)
    {
        $payload = [
            'cardNumber' => $cardNumber,
            'cifNumber' => $cifNumber,
            'statusCode' => $statusCode,
            'statusReasonCode' => $statusReasonCode,
        ];
        if (!empty($startDt)) {
            $payload['startDt'] = $startDt;
        }
        if (!empty($endDt)) {
            $payload['endDt'] = $endDt;
        }

        return $this->call('Card Status Change', 'CIC_CARD_STATUS_CHANGE_API_URL', $payload, 'cardStatusChange.json');
    }

    /* ======= 2) Card Details By Mobile Number (returns mask card number) ========== */
    public function cardDetailsByMobile($mobileNumber)
    {
        return $this->call('Card Details By Mobile', 'CIC_CARD_DETAILS_BY_MOBILE_API_URL', [
            'mobileNumber' => $mobileNumber,
        ], 'cardDetailsByMobile.json');
    }

    /* ======= 2.1) Card Details By Account Number (wbs/AC_2118216001481.txt) ========== */
    // Returns every card of the account holder (debit cards of the account + linked credit cards)
    public function cardDetailsByAccount($accountNumber)
    {
        return $this->call('Card Details By Account', 'CIC_CARD_DETAILS_BY_ACCOUNT_API_URL', [
            'accountNumber' => $accountNumber,
        ], 'cardDetailsByAccount.json');
    }

    /* ======= 3) Credit Card Transaction History ========== */
    // $startDate / $endDate format: yyyy-MM-dd
    public function cardTransactions($cardNumber, $cifNumber, $startDate, $endDate, $cardCurrency = '050')
    {
        return $this->call('Card Transactions', 'CIC_CARD_TRANSACTIONS_API_URL', [
            'cardNumber' => $cardNumber,
            'cifNumber' => $cifNumber,
            'cardCurrency' => $cardCurrency,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ], 'cardTransactions.json');
    }

    /* ======= 4) Credit Card Details ========== */
    public function cardDetails($cardNumber, $cifNumber, $cardCurrency = '050')
    {
        return $this->call('Card Details', 'CIC_CARD_DETAILS_API_URL', [
            'cardNumber' => $cardNumber,
            'cifNumber' => $cifNumber,
            'cardCurrency' => $cardCurrency,
        ], 'cardDetails.json');
    }

    /* ======= 5) Credit Card Mini Statement ========== */
    public function miniStatement($cardNumber, $cifNumber, $cardCurrency = '050')
    {
        return $this->call('Card Mini Statement', 'CIC_MINI_STATEMENT_API_URL', [
            'cardNumber' => $cardNumber,
            'cifNumber' => $cifNumber,
            'cardCurrency' => $cardCurrency,
        ], 'miniStatement.json');
    }

    /* ======= 6) Account Statement (wbs/Account_Statement_API_Spec.docx) ========== */
    // $startDate / $endDate format: yyyyMMdd, request carries only these three fields
    public function getAccountStatement($accountNumber, $startDate, $endDate)
    {
        return $this->call('Account Statement', 'CIC_ACCOUNT_STATEMENT_API_URL', [
            'accountNumber' => $accountNumber,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ], 'accountStatement.json', 2, false);
    }

    // Updated card APIs take the mask card number (e.g. 457354xxxxxx0122); kept for older encrypted-PAN APIs
    public function encryptCardNumber($cardNumber)
    {
        return base64_encode(openssl_encrypt($cardNumber, 'AES-256-CBC', $this->encryptionKey, OPENSSL_RAW_DATA, $this->encryptionIv));
    }

    public function decryptCardNumber($encryptedCardNumber)
    {
        return openssl_decrypt(base64_decode($encryptedCardNumber), 'AES-256-CBC', $this->encryptionKey, OPENSSL_RAW_DATA, $this->encryptionIv);
    }

    public function getAccessToken()
    {
        if ($this->accessToken !== null) {
            return $this->accessToken;
        }

        if ($this->localEnvironment == true) {
            $response = '{"data": {"accessToken": "e7253c93e2f66709*****77912a954fb6d0acfbbc"}, "responseCode": 200, "messages": ["Operation Successful"]}';
        } else {
            $response = $this->curlPost($this->apiCredential->token_url, json_encode([
                'username' => $this->username,
                'password' => $this->password,
            ]), ['Content-Type: application/json'], 10);
        }

        $this->accessToken = json_decode($response, true);
        return $this->accessToken;
    }

    // $logType: Credit Card = 1, Account = 2 (osbApiRequestResponse)
    // $withChannelFields: card APIs need requestDateTime / requestId / sourceChannel, core APIs do not
    private function call($service, $urlColumn, $payload, $mockFile, $logType = 1, $withChannelFields = true)
    {
        $url = $this->apiCredential->$urlColumn ?? '';
        if ($withChannelFields) {
            $payload = array_merge([
                'requestDateTime' => date('d/m/Y'),
                'requestId' => $this->generateRequestId(),
                'sourceChannel' => 'MIB',
            ], $payload);
        }

        if ($this->localEnvironment == true) {
            $response = file_get_contents(base_path('app/APIDemoResponse/CIC/' . $mockFile));
        } else {
            $accessToken = $this->getAccessToken();
            if (!isset($accessToken['responseCode']) || $accessToken['responseCode'] != 200) {
                return $this->errorResponse('Token Issue. Please Contact API Team !');
            }

            $response = $this->curlPost($url, json_encode($payload), [
                'Content-Type: application/json',
                'Authorization: ' . $accessToken['data']['accessToken'],
            ]);
        }

        $responseData = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $responseData = $this->errorResponse('Invalid JSON in API response.');
        }

        $this->osbApiRequestResponse([
            'type' => $logType,
            'account_number' => $logType == 2 ? ($payload['accountNumber'] ?? '') : '',
            'url' => $url,
            'service' => $service,
            'json_node' => $response,
            'status_code' => $responseData['responseCode'] ?? '',
            'status_msg' => $responseData['messages'][0] ?? '',
        ]);

        return $responseData;
    }

    private function curlPost($url, $postData, $headers, $timeout = 30)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_HTTPAUTH       => CURLAUTH_ANY,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_TIMEOUT        => $timeout,
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            Log::error('CIC API Curl Error:', [$url, curl_error($ch)]);
            if (curl_errno($ch) == CURLE_OPERATION_TIMEDOUT) {
                $response = json_encode($this->errorResponse('API operation timed out after ' . ($timeout * 1000) . ' milliseconds. Please contact API Team.'));
            } else {
                $response = json_encode($this->errorResponse('API operation issue. Please contact API Team.'));
            }
        }
        curl_close($ch);

        return $response;
    }

    private function errorResponse($message)
    {
        return [
            'responseCode' => 999,
            'messages' => [$message],
        ];
    }

    // "MIB" + 17 unique chars = 20 (fixed length)
    private function generateRequestId()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $requestId = 'MIB';
        for ($i = 0; $i < 17; $i++) {
            $requestId .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $requestId;
    }

    private function osbApiRequestResponse($params = array())
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
