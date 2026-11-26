<?php

namespace App\Services\CI;

use DomDocument;
use SimpleXMLElement;
use App\CustomerInterfaceAPI;
use App\Services\CI\CIRequestResponseService;

class CICURLServiceLive
{
    public static function ciCurl($cif_number, $product_type)
    {
        $accountNumberList = [];
        $ciApimodel = new CustomerInterfaceAPI;
        $ciApiObj = $ciApimodel->where('product_type', $product_type)->first();

        $endpoint = $ciApiObj->endpoint;
        $request_body = $ciApiObj->request_body;

        $sourceTimestamp = now()->format('Y-m-d\TH:i:s.v');
        $globalTransactionId = 'CI' . now()->format('YmdHisv');

        $request_body = str_replace("#cif_number#", $cif_number, $request_body);
        $request_body = str_replace("#globalTransactionId#", $globalTransactionId, $request_body);
        $request_body = str_replace("#sourceTimestamp#", $sourceTimestamp, $request_body);

        $msc = microtime(true);

        CIRequestResponseService::ciRequestResponse([
            'cif_number' => $cif_number,
            'product_type' => $product_type,
            'global_transaction_id' => (!empty($globalTransactionId)) ? $globalTransactionId : '',
            'endpoint' => $endpoint,
            'json_node' => $request_body,
        ]);

        $headers = array("Content-type: text/xml;charset=\"utf-8\"", "Accept: text/xml", "Cache-Control: no-cache", "Pragma: no-cache", "SOAPAction: casaListAndDetails", "Content-length: ".strlen($request_body));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_errno($ch);
        }
        curl_close($ch);
        if (isset($error_msg)) {
            //print_r($error_msg);
        }

        $msc = microtime(true) - $msc;
        $execution_time = number_format($msc,2);

        if (!empty($response)) {
            $xml = new SimpleXMLElement($response);

            $errorCode = $xml->xpath('//*[local-name()="errorCode"]')[0];
            $errorMessage = $xml->xpath('//*[local-name()="errorMessage"]')[0];
            $globalTransactionId = $xml->xpath('//*[local-name()="globalTransactionId"]')[0];

            if ($product_type == 2 || $product_type == 4) {
                $accountNumbers = $xml->xpath('//*[local-name()="accountNumber"]');
                $productDescriptions = $xml->xpath('//*[local-name()="productDescription"]');
                foreach ($accountNumbers as $index => $accountNumber) {
                    $accountNumberValue = (string)$accountNumber;
                    $productDescriptionValue = (string)$productDescriptions[$index];
                    $accountNumberList[$accountNumberValue] = "$accountNumberValue | $productDescriptionValue";
                }
            }
            if ($product_type == 1) {
                $maskedCardNumbers = $xml->xpath('//*[local-name()="maskedCardNumber"]');
                $productDescriptions = $xml->xpath('//*[local-name()="productDesc"]');
                foreach ($maskedCardNumbers as $index => $maskedCardNumber) {
                    $maskedCardNumberValue = (string)$maskedCardNumber;
                    $productDescriptionValue = (string)$productDescriptions[$index];
                    $accountNumberList[(string)$maskedCardNumberValue] = "$maskedCardNumberValue | $productDescriptionValue";
                }
            }
            if ($product_type == 3) {
                $maskedCardNumbers = $xml->xpath('//*[local-name()="maskedCardNumber"]');
                $productDescriptions = $xml->xpath('//*[local-name()="schemeCode"]');
                foreach ($maskedCardNumbers as $index => $maskedCardNumber) {
                    $maskedCardNumberValue = (string)$maskedCardNumber;
                    $productDescriptionValue = (string)$productDescriptions[$index];
                    $accountNumberList[(string)$maskedCardNumberValue] = "$maskedCardNumberValue | $productDescriptionValue";
                }
            }
            CIRequestResponseService::ciRequestResponse([
                'cif_number' => $cif_number,
                'product_type' => $product_type,
                'global_transaction_id' => (!empty($globalTransactionId)) ? $globalTransactionId : '',
                'status_code' => (!empty($errorCode)) ? $errorCode : '',
                'status_msg' => (!empty($errorMessage)) ? $errorMessage : '',
                'endpoint' => $endpoint,
                'json_node' => $response,
                'execution_time' => $execution_time,
            ]);
        }
        return response()->json([
            'accountNumbers' => $accountNumberList,
            'data' => $response,
        ]);
    }

    public static function apiResponse($jsonResponse, $accountNumber, $product_type)
    {
        $accountHolderName = null;
        $maskedCardNumber = null;

        if (!empty($jsonResponse)) {

            $xml = simplexml_load_string($jsonResponse);
            $namespaces = $xml->getNamespaces(true);

            foreach ($namespaces as $prefix => $namespace) {

                $xml->registerXPathNamespace($prefix, $namespace);

                if ($product_type == 2) {
                    $result = $xml->xpath("//*[local-name()='accountNumber' and text()='$accountNumber']/../$prefix:accountHolderName");
                    if (!empty($result)) {
                        $accountHolderName = (string)$result[0];
                    }
                }

                if ($product_type == 4) {
                    $result = $xml->xpath("//*[local-name()='accountNumber' and text()='$accountNumber']/../$prefix:accountHolderName");
                    if (!empty($result)) {
                        $accountHolderName = (string)$result[0];
                    }
                }

                if ($product_type == 1) {
                    $result = $xml->xpath("//*[local-name()='maskedCardNumber' and text()='$accountNumber']/../$prefix:cardHolderName");
                    $result2 = $xml->xpath("//*[local-name()='maskedCardNumber' and text()='$accountNumber']/../$prefix:customerId");
                    if (!empty($result)) {
                        $accountHolderName = (string)$result[0];
                    }
                    if (!empty($result2)) {
                        $maskedCardNumber = (string)$result2[0];
                    }
                }

                if ($product_type == 3) {
                    $result = $xml->xpath("//*[local-name()='maskedCardNumber' and text()='$accountNumber']/../$prefix:accountName");
                    $result2 = $xml->xpath("//*[local-name()='maskedCardNumber' and text()='$accountNumber']/../$prefix:accountNumber");
                    if (!empty($result)) {
                        $accountHolderName = (string)$result[0];
                    }
                    if (!empty($result2)) {
                        $maskedCardNumber = (string)$result2[0];
                    }
                }
            }
        }

        return response()->json([
            'accountHolderName' => $accountHolderName,
            'maskedCardNumber' => $maskedCardNumber,
        ]);
    }

}
