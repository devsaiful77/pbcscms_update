<?php

namespace App\Services\CI;

use App\Services\CI\CIRequestResponseService;
use DomDocument;
use SimpleXMLElement;
use App\CustomerInterfaceAPI;

class CICURLService
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

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        // curl_setopt($ch, CURLOPT_URL, $endpoint);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        // curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // $response = curl_exec($ch);

        // if (curl_errno($ch)) {
        //     $error_msg = curl_errno($ch);
        // }
        // curl_close($ch);
        // if (isset($error_msg)) {
        //     //print_r($error_msg);
        // }

        $response = '';
    //     $response = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
    //     <soap:Body xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    //        <ns2:casaListAndDetailsResp xmlns:ns2="http://bracbank.com/UBACasaListAndDetailsES/V1">
    //           <ns2:casaListAndDetailsResp>
    //              <ns2:header>
    //                 <ns4:serviceId xmlns:ns4="http://bracbank.com/utilityCommon/V1">UBAES-0018</ns4:serviceId>
    //                 <ns4:serviceName xmlns:ns4="http://bracbank.com/utilityCommon/V1">UBACASAListAndDetailsES</ns4:serviceName>
    //                 <ns4:sourceTimestamp xmlns:ns4="http://bracbank.com/utilityCommon/V1">2023-08-14T13:07:46.985+06:00</ns4:sourceTimestamp>
    //                 <ns4:password xmlns:ns4="http://bracbank.com/utilityCommon/V1">Ahj67#12i89kT!z</ns4:password>
    //                 <ns4:channelName xmlns:ns4="http://bracbank.com/utilityCommon/V1">UBA</ns4:channelName>
    //                 <ns4:channelCode xmlns:ns4="http://bracbank.com/utilityCommon/V1">MB</ns4:channelCode>
    //                 <ns4:globalTransactionId xmlns:ns4="http://bracbank.com/utilityCommon/V1">Test_CASALIST_000000100019154</ns4:globalTransactionId>
    //              </ns2:header>
    //              <ns2:errorCode>000</ns2:errorCode>
    //              <ns2:errorMessage>Success</ns2:errorMessage>
    //              <ns2:casaAccountDetails>
    //                 <ns2:casaAccountDetail>
    //                    <ns2:accountNumber>1006732350003</ns2:accountNumber>
    //                    <ns2:accountCurrency>BDT</ns2:accountCurrency>
    //                    <ns2:schemeType>SBA</ns2:schemeType>
    //                    <ns2:schemeCode>SRTBF</ns2:schemeCode>
    //                    <ns2:dormancyStatus>A</ns2:dormancyStatus>
    //                    <ns2:accountHolderName>Zahidul islam</ns2:accountHolderName>
    //                    <ns2:accountOpeningDate/>
    //                    <ns2:branchCode>1549</ns2:branchCode>
    //                    <ns2:availableBalance>0.00</ns2:availableBalance>
    //                    <ns2:currentBalance/>
    //                    <ns2:unclearedAmount/>
    //                    <ns2:overDraftLimit/>
    //                    <ns2:holdAmount/>
    //                    <ns2:creditInterestRate/>
    //                    <ns2:debitInterestRate/>
    //                    <ns2:availableRewardPoints/>
    //                    <ns2:nomineeName/>
    //                    <ns2:freezeStatus/>
    //                    <ns2:limitAvailability/>
    //                    <ns2:loginCIF>00673235</ns2:loginCIF>
    //                    <ns2:accountCIF>00673235</ns2:accountCIF>
    //                    <ns2:loginCIFType>R</ns2:loginCIFType>
    //                    <ns2:accountCIFType>R</ns2:accountCIFType>
    //                    <ns2:cifRelation>M</ns2:cifRelation>
    //                    <ns2:accountRelation>M</ns2:accountRelation>
    //                    <ns2:modeOfOperation>MOP01</ns2:modeOfOperation>
    //                    <ns2:productDescription>SBA ACC TRIPLE BENEFITS</ns2:productDescription>
    //                    <ns2:costCenter/>
    //                    <ns2:freezeDate/>
    //                    <ns2:freezeReasonCode/>
    //                    <ns2:ledgerBalance/>
    //                    <ns2:limitAmount/>
    //                 </ns2:casaAccountDetail>
    //                 <ns2:casaAccountDetail>
    //                    <ns2:accountNumber>1006732350004</ns2:accountNumber>
    //                    <ns2:accountCurrency>BDT</ns2:accountCurrency>
    //                    <ns2:schemeType>SBA</ns2:schemeType>
    //                    <ns2:schemeCode>SACLA</ns2:schemeCode>
    //                    <ns2:dormancyStatus>A</ns2:dormancyStatus>
    //                    <ns2:accountHolderName>Zahidul Islam</ns2:accountHolderName>
    //                    <ns2:accountOpeningDate/>
    //                    <ns2:branchCode>1555</ns2:branchCode>
    //                    <ns2:availableBalance>0.00</ns2:availableBalance>
    //                    <ns2:currentBalance/>
    //                    <ns2:unclearedAmount/>
    //                    <ns2:overDraftLimit/>
    //                    <ns2:holdAmount/>
    //                    <ns2:creditInterestRate/>
    //                    <ns2:debitInterestRate/>
    //                    <ns2:availableRewardPoints/>
    //                    <ns2:nomineeName/>
    //                    <ns2:freezeStatus/>
    //                    <ns2:limitAvailability/>
    //                    <ns2:loginCIF>00673235</ns2:loginCIF>
    //                    <ns2:accountCIF>00673235</ns2:accountCIF>
    //                    <ns2:loginCIFType>R</ns2:loginCIFType>
    //                    <ns2:accountCIFType>R</ns2:accountCIFType>
    //                    <ns2:cifRelation>M</ns2:cifRelation>
    //                    <ns2:accountRelation>M</ns2:accountRelation>
    //                    <ns2:modeOfOperation>MOP01</ns2:modeOfOperation>
    //                    <ns2:productDescription>SAVINGS A/C CLASSIC</ns2:productDescription>
    //                    <ns2:costCenter/>
    //                    <ns2:freezeDate/>
    //                    <ns2:freezeReasonCode/>
    //                    <ns2:ledgerBalance/>
    //                    <ns2:limitAmount/>
    //                 </ns2:casaAccountDetail>
    //                 <ns2:casaAccountDetail>
    //                    <ns2:accountNumber>1501100673235001</ns2:accountNumber>
    //                    <ns2:accountCurrency>BDT</ns2:accountCurrency>
    //                    <ns2:schemeType>SBA</ns2:schemeType>
    //                    <ns2:schemeCode>SRSTF</ns2:schemeCode>
    //                    <ns2:dormancyStatus>A</ns2:dormancyStatus>
    //                    <ns2:accountHolderName>Zahidul islam</ns2:accountHolderName>
    //                    <ns2:accountOpeningDate/>
    //                    <ns2:branchCode>1501</ns2:branchCode>
    //                    <ns2:availableBalance>363637.66</ns2:availableBalance>
    //                    <ns2:currentBalance/>
    //                    <ns2:unclearedAmount/>
    //                    <ns2:overDraftLimit/>
    //                    <ns2:holdAmount/>
    //                    <ns2:creditInterestRate/>
    //                    <ns2:debitInterestRate/>
    //                    <ns2:availableRewardPoints/>
    //                    <ns2:nomineeName/>
    //                    <ns2:freezeStatus/>
    //                    <ns2:limitAvailability/>
    //                    <ns2:loginCIF>00673235</ns2:loginCIF>
    //                    <ns2:accountCIF>00673235</ns2:accountCIF>
    //                    <ns2:loginCIFType>R</ns2:loginCIFType>
    //                    <ns2:accountCIFType>R</ns2:accountCIFType>
    //                    <ns2:cifRelation>M</ns2:cifRelation>
    //                    <ns2:accountRelation>M</ns2:accountRelation>
    //                    <ns2:modeOfOperation>MOP01</ns2:modeOfOperation>
    //                    <ns2:productDescription>SAVINGS ACCOUNT STAFF</ns2:productDescription>
    //                    <ns2:costCenter/>
    //                    <ns2:freezeDate/>
    //                    <ns2:freezeReasonCode/>
    //                    <ns2:ledgerBalance/>
    //                    <ns2:limitAmount/>
    //                 </ns2:casaAccountDetail>
    //                 <ns2:casaAccountDetail>
    //                    <ns2:accountNumber>1501100673235002</ns2:accountNumber>
    //                    <ns2:accountCurrency>BDT</ns2:accountCurrency>
    //                    <ns2:schemeType>SBA</ns2:schemeType>
    //                    <ns2:schemeCode>SRTBF</ns2:schemeCode>
    //                    <ns2:dormancyStatus>A</ns2:dormancyStatus>
    //                    <ns2:accountHolderName>Zahidul islam</ns2:accountHolderName>
    //                    <ns2:accountOpeningDate/>
    //                    <ns2:branchCode>1501</ns2:branchCode>
    //                    <ns2:availableBalance>14879122.32</ns2:availableBalance>
    //                    <ns2:currentBalance/>
    //                    <ns2:unclearedAmount/>
    //                    <ns2:overDraftLimit/>
    //                    <ns2:holdAmount/>
    //                    <ns2:creditInterestRate/>
    //                    <ns2:debitInterestRate/>
    //                    <ns2:availableRewardPoints/>
    //                    <ns2:nomineeName/>
    //                    <ns2:freezeStatus/>
    //                    <ns2:limitAvailability/>
    //                    <ns2:loginCIF>00673235</ns2:loginCIF>
    //                    <ns2:accountCIF>00673235</ns2:accountCIF>
    //                    <ns2:loginCIFType>R</ns2:loginCIFType>
    //                    <ns2:accountCIFType>R</ns2:accountCIFType>
    //                    <ns2:cifRelation>M</ns2:cifRelation>
    //                    <ns2:accountRelation>M</ns2:accountRelation>
    //                    <ns2:modeOfOperation>MOP01</ns2:modeOfOperation>
    //                    <ns2:productDescription>SBA ACC TRIPLE BENEFITS</ns2:productDescription>
    //                    <ns2:costCenter/>
    //                    <ns2:freezeDate/>
    //                    <ns2:freezeReasonCode/>
    //                    <ns2:ledgerBalance/>
    //                    <ns2:limitAmount/>
    //                 </ns2:casaAccountDetail>
    //                 <ns2:casaAccountDetail>
    //                    <ns2:accountNumber>2006732350001</ns2:accountNumber>
    //                    <ns2:accountCurrency>BDT</ns2:accountCurrency>
    //                    <ns2:schemeType>CAA</ns2:schemeType>
    //                    <ns2:schemeCode>CREZE</ns2:schemeCode>
    //                    <ns2:dormancyStatus>A</ns2:dormancyStatus>
    //                    <ns2:accountHolderName>UTTAM ADHIKARI</ns2:accountHolderName>
    //                    <ns2:accountOpeningDate/>
    //                    <ns2:branchCode>1544</ns2:branchCode>
    //                    <ns2:availableBalance>0.00</ns2:availableBalance>
    //                    <ns2:currentBalance/>
    //                    <ns2:unclearedAmount/>
    //                    <ns2:overDraftLimit/>
    //                    <ns2:holdAmount/>
    //                    <ns2:creditInterestRate/>
    //                    <ns2:debitInterestRate/>
    //                    <ns2:availableRewardPoints/>
    //                    <ns2:nomineeName/>
    //                    <ns2:freezeStatus/>
    //                    <ns2:limitAvailability/>
    //                    <ns2:loginCIF>00673235</ns2:loginCIF>
    //                    <ns2:accountCIF>00673235</ns2:accountCIF>
    //                    <ns2:loginCIFType>R</ns2:loginCIFType>
    //                    <ns2:accountCIFType>R</ns2:accountCIFType>
    //                    <ns2:cifRelation>M</ns2:cifRelation>
    //                    <ns2:accountRelation>M</ns2:accountRelation>
    //                    <ns2:modeOfOperation>MOP01</ns2:modeOfOperation>
    //                    <ns2:productDescription>CURRENT EZEE RETAIL</ns2:productDescription>
    //                    <ns2:costCenter/>
    //                    <ns2:freezeDate/>
    //                    <ns2:freezeReasonCode/>
    //                    <ns2:ledgerBalance/>
    //                    <ns2:limitAmount/>
    //                 </ns2:casaAccountDetail>
    //              </ns2:casaAccountDetails>
    //           </ns2:casaListAndDetailsResp>
    //        </ns2:casaListAndDetailsResp>
    //     </soap:Body>
    //     <ns2:Body xmlns:ns2="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns4="http://bracbank.com/utilityCommon/V1" xmlns:ns3="http://bracbank.com/UBAFetchCardByStatusES/V1">
    //        <ns1:fetchCardByStatusResp xmlns:ns1="http://bracbank.com/UBAFetchCardByStatusES/V1">
    //           <ns1:fetchCardByStatusDetailsResp>
    //              <ns1:header>
    //                 <ns2:serviceId xmlns:ns2="http://bracbank.com/utilityCommon/V1">UBAES-0023</ns2:serviceId>
    //                 <ns2:serviceName xmlns:ns2="http://bracbank.com/utilityCommon/V1">UBAFetchCardByStatusES</ns2:serviceName>
    //                 <ns2:sourceTimestamp xmlns:ns2="http://bracbank.com/utilityCommon/V1">2023-03-19T13:17:24.014+06:00</ns2:sourceTimestamp>
    //                 <ns2:password xmlns:ns2="http://bracbank.com/utilityCommon/V1">Ahj67#12i89kT!z</ns2:password>
    //                 <ns2:channelName xmlns:ns2="http://bracbank.com/utilityCommon/V1">UBA</ns2:channelName>
    //                 <ns2:channelCode xmlns:ns2="http://bracbank.com/utilityCommon/V1">IB</ns2:channelCode>
    //                 <ns2:globalTransactionId xmlns:ns2="http://bracbank.com/utilityCommon/V1">01781297031917240014</ns2:globalTransactionId>
    //              </ns1:header>
    //              <ns1:errorCode>000</ns1:errorCode>
    //              <ns1:errorMessage>Success</ns1:errorMessage>
    //              <ns1:fetchCardDetails>
    //                 <ns1:creditOrPrepaidCardDetails>
    //                    <ns1:creditOrPrepaidCardDetail>
    //                       <ns1:maskedCardNumber>432147******9096</ns1:maskedCardNumber>
    //                       <ns1:cif>04464462</ns1:cif>
    //                       <ns1:customerIdType>1</ns1:customerIdType>
    //                       <ns1:customerId>13011935200070</ns1:customerId>
    //                       <ns1:shadowAccountNumber>00000000000000315159</ns1:shadowAccountNumber>
    //                       <ns1:cardHolderName>Ziad Nasim</ns1:cardHolderName>
    //                       <ns1:expiryDate>202412</ns1:expiryDate>
    //                       <ns1:plasticStatus>L</ns1:plasticStatus>
    //                       <ns1:cardType>C</ns1:cardType>
    //                       <ns1:productCode>1302</ns1:productCode>
    //                       <ns1:productDesc>CONTACTLESS DUAL GOLD STAFF</ns1:productDesc>
    //                       <ns1:basicSuppInd>B</ns1:basicSuppInd>
    //                       <ns1:cardCCY>50</ns1:cardCCY>
    //                       <ns1:cardBrand>V</ns1:cardBrand>
    //                       <ns1:annivDate>20191218</ns1:annivDate>
    //                       <ns1:activationDate>20200101</ns1:activationDate>
    //                       <ns1:embossDate>20191218</ns1:embossDate>
    //                       <ns1:applicationDate>20191218</ns1:applicationDate>
    //                       <ns1:cardOutstd>-7990.71</ns1:cardOutstd>
    //                       <ns1:creditLimit>300000</ns1:creditLimit>
    //                       <ns1:mobilePhone>+8801711223344</ns1:mobilePhone>
    //                       <ns1:emailAddress>ansari.hamid@gmail.com</ns1:emailAddress>
    //                       <ns1:emailAddress2>IZIADNASIM@YAHOO.COM</ns1:emailAddress2>
    //                       <ns1:emailAddress3>IZIADNASIM@YMAIL.COM</ns1:emailAddress3>
    //                       <ns1:cardStatus>LOST</ns1:cardStatus>
    //                       <ns1:cardCcyIso>BDT</ns1:cardCcyIso>
    //                       <ns1:cardAccountStatus>ACTIVE</ns1:cardAccountStatus>
    //                    </ns1:creditOrPrepaidCardDetail>
    //                 </ns1:creditOrPrepaidCardDetails>
    //              </ns1:fetchCardDetails>
    //           </ns1:fetchCardByStatusDetailsResp>
    //        </ns1:fetchCardByStatusResp>
    //     </ns2:Body>
    //  </soapenv:Envelope>';
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
        $accountNumberList = [
            '134254367899' => '134254367899',
            '131224567834' => '131224567834'
        ];
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
