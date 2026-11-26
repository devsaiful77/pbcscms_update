<?php

namespace App\Http\Controllers;

use App\CIFParentUrl;
use App\Comment;
use App\Complaint;
use App\IRISApi;
use App\IRISApiResponses;
use App\IRISWorkflow;
use App\OutgoingEMAIL;
use App\OutgoingSMS;
use App\Reference;
use App\SMSEmail;
use App\SubgroupInfo;
use App\UnitItem;
use App\WForm;
use DateTime;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use SimpleXMLElement;

class IRISApiController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(['role_or_permission:superadmin|admin|iris_api'])
            ->only('irisApiList', 'apiEdit', 'irisApiAdd', 'irisApiStore', 'apiModificationUpdate', 'irisApiStatus');
    }
    public function quotaWorkflow(Request $request)
    {
        $issue_id = $request->issue_id;
        $issue = UnitItem::where('master_id',$issue_id)->where('issues_from',$request->type)->first();
        if($issue->master_id == $issue_id){
            return view('partials.quota_fields');
        } else {
            return '';
        }
    }

    public function manipulateData(Request $request)
    {
        $maskCardNumber = $request->get('mask_card_number');
        $accountNumber = $request->get('account_number');

        $irisApiModel = IRISApi::where('name','MasterInquiryES')->first();
        $endpoint = $irisApiModel->url;
        $request_body = $irisApiModel->request;

        $sourceTimestamp = now()->format('Y-m-d\TH:i:s.v');
        $globalTransactionId = 'AYS_INQ_' . now()->format('YmdHisv');

        $request_body = str_replace("#globalTransactionId#", $globalTransactionId, $request_body);
        $request_body = str_replace("#sourceTimestamp#", $sourceTimestamp, $request_body);
        $request_body = str_replace("#mask_card_number#", $maskCardNumber, $request_body);
        $request_body = str_replace("#account_number#", $accountNumber, $request_body);
        /*$request_body = str_replace("#customer_id#", $customerId, $request_body);*/

        $msc = microtime(true);

        $this->IRISApiRequestResponse([
            'endpoint' => $endpoint,
            'json_node' => $request_body,
            'service' => 'MasterInquiryES',
            'card_number' => $maskCardNumber,
            'account_number' => $accountNumber,
            'type' => 1,
            'global_transaction_id' => (!empty($globalTransactionId)) ? $globalTransactionId : '',
        ]);

        $headers = array("Content-type: text/xml;charset=\"utf-8\"", "Accept: text/xml", "Cache-Control: no-cache", "Pragma: no-cache", "SOAPAction: casaListAndDetails", "Content-length: ".strlen($request_body));

        /*$api_credential = DB::table('api_credential')->first();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if (!empty($api_credential->esb_api_cert_path)) {
            curl_setopt($ch, CURLOPT_PORT, "4444");
            curl_setopt($ch, CURLOPT_CAINFO, $api_credential->esb_api_cert_path);
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_errno($ch);
        }
        curl_close($ch);*/

       /* dd(curl_error($ch));*/

        $msc = microtime(true) - $msc;
        $execution_time = number_format($msc,2);

        $testResponse1 = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope
    xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
    <soapenv:Header
        xmlns:v11="http://bracbank.com/utilityCommon/V1"
        xmlns:v1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1"/>
        <soapenv:Body
            xmlns:v11="http://bracbank.com/utilityCommon/V1"
            xmlns:v1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1">
            <ns1:MasterInquiryResp
                xmlns:ns1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1">
                <ns1:MasterInquiryRespDetails>
                    <ns1:header>
                        <ns5:serviceId
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">BBL-006
                        </ns5:serviceId>
                        <ns5:serviceName
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">MasterInquiryES
                        </ns5:serviceName>
                        <ns5:sourceTimestamp
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">2024-04-29T23:19:34.586
                        </ns5:sourceTimestamp>
                        <ns5:password
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">Ahj67#12i89kT!z
                        </ns5:password>
                        <ns5:channelName
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">AYS
                        </ns5:channelName>
                        <ns5:channelCode
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">AYS
                        </ns5:channelCode>
                        <ns5:globalTransactionId
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">AYS_INQ_20240429231934586
                        </ns5:globalTransactionId>
                    </ns1:header>
                    <ns1:errorCode>000</ns1:errorCode>
                    <ns1:errorMessage>Success</ns1:errorMessage>
                    <ns1:customerList>
                        <ns1:customerInfo>
                            <ns1:customer>
                                <ns1:customerId>10000003539493</ns1:customerId>
                                <ns1:cardInterfacingNo>4777920002015479</ns1:cardInterfacingNo>
                                <ns1:cardStatus>00</ns1:cardStatus>
                                <ns1:relationshipId>4777920002015479=2903</ns1:relationshipId>
                            </ns1:customer>
                            <ns1:product>
                                <ns1:productLimit>12000.00</ns1:productLimit>
                                <ns1:productType>1</ns1:productType>
                                <ns1:ecomLimit>300.00</ns1:ecomLimit>
                                <ns1:isEcomEnabled>1</ns1:isEcomEnabled>
                                <ns1:isEnable>1</ns1:isEnable>
                            </ns1:product>
                        </ns1:customerInfo>
                    </ns1:customerList>
                    <ns1:quotaList>
                        <ns1:quotaInfo>
                            <ns1:quotaId>2255</ns1:quotaId>
                            <ns1:cardNumber>477792******5479</ns1:cardNumber>
                            <ns1:accountNumber>1503102646539001</ns1:accountNumber>
                            <ns1:limitType>Travel</ns1:limitType>
                            <ns1:threshodLimit>12000.00</ns1:threshodLimit>
                            <ns1:assignedLimit>174</ns1:assignedLimit>
                            <ns1:limitStartDate>2024/01/01</ns1:limitStartDate>
                            <ns1:limitEndDate>2024/12/31</ns1:limitEndDate>
                            <ns1:remainingLimitAmount>12000.00</ns1:remainingLimitAmount>
                            <ns1:ecomIsActive>1</ns1:ecomIsActive>
                            <ns1:ecomThrActive>1</ns1:ecomThrActive>
                            <ns1:unUsagePercentage>5</ns1:unUsagePercentage>
                            <ns1:spentAmount>0.00</ns1:spentAmount>
                            <ns1:availableQuota>162.91</ns1:availableQuota>
                            <ns1:blockageAmount>857</ns1:blockageAmount>
                            <ns1:limitAmount>161</ns1:limitAmount>
                            <ns1:isActive>1</ns1:isActive>
                            <ns1:ecomthr_from_date/>
                            <ns1:ecomthr_to_date/>
                        </ns1:quotaInfo>
                    </ns1:quotaList>
                    <ns1:passportInfo>
                        <ns1:passportNo>A22223300</ns1:passportNo>
                        <ns1:passportName>Ziad Nasim</ns1:passportName>
                        <ns1:passportExpiryDT>2027/04/30</ns1:passportExpiryDT>
                        <ns1:customerId>10000003539493</ns1:customerId>
                        <ns1:passportIssueDT>2014/04/07</ns1:passportIssueDT>
                        <ns1:passportIsssueplace>Dhaka</ns1:passportIsssueplace>
                        <ns1:passportPreviousNumber>.</ns1:passportPreviousNumber>
                        <ns1:passportAddress>Dhaka</ns1:passportAddress>
                        <ns1:passportRenewalDT>2024/01/01</ns1:passportRenewalDT>
                    </ns1:passportInfo>
                </ns1:MasterInquiryRespDetails>
            </ns1:MasterInquiryResp>
        </soapenv:Body>
    </soapenv:Envelope>';

$testResponse2 = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
   <soapenv:Header xmlns:v11="http://bracbank.com/utilityCommon/V1" xmlns:v1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1"/>
   <soapenv:Body xmlns:v11="http://bracbank.com/utilityCommon/V1" xmlns:v1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1">
      <ns1:MasterInquiryResp xmlns:ns1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1">
         <ns1:MasterInquiryRespDetails>
            <ns1:header>
               <ns5:serviceId xmlns:ns5="http://bracbank.com/utilityCommon/V1">BBL-006</ns5:serviceId>
               <ns5:serviceName xmlns:ns5="http://bracbank.com/utilityCommon/V1">MasterInquiryES</ns5:serviceName>
               <ns5:sourceTimestamp xmlns:ns5="http://bracbank.com/utilityCommon/V1">2010-08-17T00:00:00.000+05:00</ns5:sourceTimestamp>
               <ns5:password xmlns:ns5="http://bracbank.com/utilityCommon/V1">Ahj67#12i89kT!z</ns5:password>
               <ns5:channelName xmlns:ns5="http://bracbank.com/utilityCommon/V1">AYS</ns5:channelName>
               <ns5:channelCode xmlns:ns5="http://bracbank.com/utilityCommon/V1">AYS</ns5:channelCode>
               <ns5:globalTransactionId xmlns:ns5="http://bracbank.com/utilityCommon/V1">AYS_INQ_0000000000000001</ns5:globalTransactionId>
            </ns1:header>
            <ns1:errorCode>000</ns1:errorCode>
            <ns1:errorMessage>Success</ns1:errorMessage>
            <ns1:customerList>
               <ns1:customerInfo>
                  <ns1:customer>
                     <ns1:customerId>10000003535661</ns1:customerId>
                     <ns1:cardInterfacingNo>4777920002014860</ns1:cardInterfacingNo>
                     <ns1:cardStatus>00</ns1:cardStatus>
                     <ns1:relationshipId>4777920002014860=2812</ns1:relationshipId>
                  </ns1:customer>
                  <ns1:product>
                     <ns1:productLimit>12000.00</ns1:productLimit>
                     <ns1:productType>1</ns1:productType>
                     <ns1:ecomLimit>3000.00</ns1:ecomLimit>
                     <ns1:isEcomEnabled>1</ns1:isEcomEnabled>
                     <ns1:isEnable>1</ns1:isEnable>
                  </ns1:product>
               </ns1:customerInfo>
            </ns1:customerList>
            <ns1:quotaList>
               <ns1:quotaInfo>
                  <ns1:quotaId>2095</ns1:quotaId>
                  <ns1:cardNumber>477792******4860</ns1:cardNumber>
                  <ns1:accountNumber>1501101600267001</ns1:accountNumber>
                  <ns1:limitType>Travel</ns1:limitType>
                  <ns1:threshodLimit>12000.00</ns1:threshodLimit>
                  <ns1:assignedLimit>2000.00</ns1:assignedLimit>
                  <ns1:limitStartDate>2024/01/02</ns1:limitStartDate>
                  <ns1:limitEndDate>2024/12/31</ns1:limitEndDate>
                  <ns1:remainingLimitAmount>11967.00</ns1:remainingLimitAmount>
                  <ns1:ecomIsActive>1</ns1:ecomIsActive>
                  <ns1:ecomThrActive>0</ns1:ecomThrActive>
                  <ns1:unUsagePercentage>5</ns1:unUsagePercentage>
                  <ns1:spentAmount>33.00</ns1:spentAmount>
                  <ns1:availableQuota>1867.00</ns1:availableQuota>
                  <ns1:blockageAmount>100.00</ns1:blockageAmount>
                  <ns1:limitAmount>1900.00</ns1:limitAmount>
                  <ns1:isActive>0</ns1:isActive>
                  <ns1:test>test</ns1:test>
                  <ns1:ecomthr_from_date/>
                  <ns1:ecomthr_to_date/>
               </ns1:quotaInfo>
            </ns1:quotaList>
            <ns1:passportInfo>
               <ns1:passportNo>T01234567</ns1:passportNo>
               <ns1:passportName>TEST IB MULTI 1</ns1:passportName>
               <ns1:passportExpiryDT>2032/12/10</ns1:passportExpiryDT>
               <ns1:customerId>10000003535661</ns1:customerId>
               <ns1:passportIssueDT>2022/01/01</ns1:passportIssueDT>
               <ns1:passportIsssueplace>DHAKA</ns1:passportIsssueplace>
               <ns1:passportPreviousNumber>.</ns1:passportPreviousNumber>
               <ns1:passportAddress>.</ns1:passportAddress>
               <ns1:passportRenewalDT>2032/12/10</ns1:passportRenewalDT>
            </ns1:passportInfo>
         </ns1:MasterInquiryRespDetails>
      </ns1:MasterInquiryResp>
   </soapenv:Body>
</soapenv:Envelope>';


        $medical_response = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
   <soapenv:Header xmlns:v11="http://bracbank.com/utilityCommon/V1" xmlns:v1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1"/>
   <soapenv:Body xmlns:v11="http://bracbank.com/utilityCommon/V1" xmlns:v1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1">
      <ns1:MasterInquiryResp xmlns:ns1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1">
         <ns1:MasterInquiryRespDetails>
            <ns1:header>
               <ns5:serviceId xmlns:ns5="http://bracbank.com/utilityCommon/V1">BBL-006</ns5:serviceId>
               <ns5:serviceName xmlns:ns5="http://bracbank.com/utilityCommon/V1">MasterInquiryES</ns5:serviceName>
               <ns5:sourceTimestamp xmlns:ns5="http://bracbank.com/utilityCommon/V1">2010-08-17T00:00:00.000+05:00</ns5:sourceTimestamp>
               <ns5:password xmlns:ns5="http://bracbank.com/utilityCommon/V1">Ahj67#12i89kT!z</ns5:password>
               <ns5:channelName xmlns:ns5="http://bracbank.com/utilityCommon/V1">AYS</ns5:channelName>
               <ns5:channelCode xmlns:ns5="http://bracbank.com/utilityCommon/V1">AYS</ns5:channelCode>
               <ns5:globalTransactionId xmlns:ns5="http://bracbank.com/utilityCommon/V1">AYS_INQ_000001</ns5:globalTransactionId>
            </ns1:header>
            <ns1:errorCode>000</ns1:errorCode>
            <ns1:errorMessage>Success</ns1:errorMessage>
            <ns1:customerList>
               <ns1:customerInfo>
                  <ns1:customer>
                     <ns1:customerId>10000003539405</ns1:customerId>
                     <ns1:cardInterfacingNo>4777920002015594</ns1:cardInterfacingNo>
                     <ns1:cardStatus>00</ns1:cardStatus>
                     <ns1:relationshipId>4777920002015594=3403</ns1:relationshipId>
                  </ns1:customer>
                  <ns1:product>
                     <ns1:productLimit>10000.00</ns1:productLimit>
                     <ns1:productType>0</ns1:productType>
                     <ns1:ecomLimit>300.00</ns1:ecomLimit>
                     <ns1:isEcomEnabled>1</ns1:isEcomEnabled>
                     <ns1:isEnable>1</ns1:isEnable>
                     <ns1:ecomthr_to_date/>
                  </ns1:product>
               </ns1:customerInfo>
            </ns1:customerList>
            <ns1:quotaList>
               <ns1:quotaInfo>
                  <ns1:quotaId>2314</ns1:quotaId>
                  <ns1:cardNumber>477792******5594</ns1:cardNumber>
                  <ns1:accountNumber>1509103853741001</ns1:accountNumber>
                  <ns1:limitType>Medical</ns1:limitType>
                  <ns1:threshodLimit>10000.00</ns1:threshodLimit>
                  <ns1:assignedLimit>410.00</ns1:assignedLimit>
                  <ns1:limitStartDate>2024/05/15</ns1:limitStartDate>
                  <ns1:limitEndDate>2024/12/31</ns1:limitEndDate>
                  <ns1:remainingLimitAmount>10000.00</ns1:remainingLimitAmount>
                  <ns1:ecomIsActive>1</ns1:ecomIsActive>
                  <ns1:ecomThrActive>1</ns1:ecomThrActive>
                  <ns1:unUsagePercentage>5</ns1:unUsagePercentage>
                  <ns1:spentAmount>0.00</ns1:spentAmount>
                  <ns1:availableQuota>389.50</ns1:availableQuota>
                  <ns1:blockageAmount>20.50</ns1:blockageAmount>
                  <ns1:limitAmount>389.50</ns1:limitAmount>
                  <ns1:isActive>1</ns1:isActive>
                  <ns1:ecomthr_from_date/>
                  <ns1:ecomthr_to_date/>
               </ns1:quotaInfo>
            </ns1:quotaList>
            <ns1:passportInfo>
               <ns1:passportNo>ZX0000000</ns1:passportNo>
               <ns1:passportName>Ziad Nasim</ns1:passportName>
               <ns1:passportExpiryDT>2026/07/31</ns1:passportExpiryDT>
               <ns1:customerId>10000003539405</ns1:customerId>
               <ns1:passportIssueDT>2016/04/15</ns1:passportIssueDT>
               <ns1:passportIsssueplace>Dhaka</ns1:passportIsssueplace>
               <ns1:passportPreviousNumber/>
               <ns1:passportAddress/>
               <ns1:passportRenewalDT/>
            </ns1:passportInfo>
         </ns1:MasterInquiryRespDetails>
      </ns1:MasterInquiryResp>
   </soapenv:Body>
</soapenv:Envelope>';

        $medical_response_empty = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
   <soapenv:Header xmlns:v11="http://bracbank.com/utilityCommon/V1" xmlns:v1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1"/>
   <soapenv:Body xmlns:v11="http://bracbank.com/utilityCommon/V1" xmlns:v1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1">
      <ns1:MasterInquiryResp xmlns:ns1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1">
         <ns1:MasterInquiryRespDetails>
            <ns1:header>
               <ns5:serviceId xmlns:ns5="http://bracbank.com/utilityCommon/V1">BBL-006</ns5:serviceId>
               <ns5:serviceName xmlns:ns5="http://bracbank.com/utilityCommon/V1">MasterInquiryES</ns5:serviceName>
               <ns5:sourceTimestamp xmlns:ns5="http://bracbank.com/utilityCommon/V1">2010-08-17T00:00:00.000+05:00</ns5:sourceTimestamp>
               <ns5:password xmlns:ns5="http://bracbank.com/utilityCommon/V1">Ahj67#12i89kT!z</ns5:password>
               <ns5:channelName xmlns:ns5="http://bracbank.com/utilityCommon/V1">AYS</ns5:channelName>
               <ns5:channelCode xmlns:ns5="http://bracbank.com/utilityCommon/V1">AYS</ns5:channelCode>
               <ns5:globalTransactionId xmlns:ns5="http://bracbank.com/utilityCommon/V1">AYS_INQ_000001</ns5:globalTransactionId>
            </ns1:header>
            <ns1:errorCode>000</ns1:errorCode>
            <ns1:errorMessage>Success</ns1:errorMessage>
            <ns1:customerList>
               <ns1:customerInfo>
                  <ns1:customer>
                     <ns1:customerId>10000003539405</ns1:customerId>
                     <ns1:cardInterfacingNo>4777920002015594</ns1:cardInterfacingNo>
                     <ns1:cardStatus>00</ns1:cardStatus>
                     <ns1:relationshipId>4777920002015594=3403</ns1:relationshipId>
                  </ns1:customer>
                  <ns1:product>
                     <ns1:productLimit>10000.00</ns1:productLimit>
                     <ns1:productType>0</ns1:productType>
                     <ns1:ecomLimit>300.00</ns1:ecomLimit>
                     <ns1:isEcomEnabled>1</ns1:isEcomEnabled>
                     <ns1:isEnable>1</ns1:isEnable>
                     <ns1:ecomthr_to_date/>
                  </ns1:product>
               </ns1:customerInfo>
            </ns1:customerList>
            <ns1:quotaList>
            </ns1:quotaList>
            <ns1:passportInfo>
               <ns1:passportNo>ZX0000000</ns1:passportNo>
               <ns1:passportName>Ziad Nasim</ns1:passportName>
               <ns1:passportExpiryDT>2026/07/31</ns1:passportExpiryDT>
               <ns1:customerId>10000003539405</ns1:customerId>
               <ns1:passportIssueDT>2016/04/15</ns1:passportIssueDT>
               <ns1:passportIsssueplace>Dhaka</ns1:passportIsssueplace>
               <ns1:passportPreviousNumber/>
               <ns1:passportAddress/>
               <ns1:passportRenewalDT/>
            </ns1:passportInfo>
         </ns1:MasterInquiryRespDetails>
      </ns1:MasterInquiryResp>
   </soapenv:Body>
</soapenv:Envelope>';

        $testResponse3 = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
   <soapenv:Header xmlns:v11="http://bracbank.com/utilityCommon/V1" xmlns:v1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1"/>
   <soapenv:Body xmlns:v11="http://bracbank.com/utilityCommon/V1" xmlns:v1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1">
      <ns1:MasterInquiryResp xmlns:ns1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1">
         <ns1:MasterInquiryRespDetails>
            <ns1:header>
               <ns5:serviceId xmlns:ns5="http://bracbank.com/utilityCommon/V1">BBL-006</ns5:serviceId>
               <ns5:serviceName xmlns:ns5="http://bracbank.com/utilityCommon/V1">MasterInquiryES</ns5:serviceName>
               <ns5:sourceTimestamp xmlns:ns5="http://bracbank.com/utilityCommon/V1">2010-08-17T00:00:00.000+05:00</ns5:sourceTimestamp>
               <ns5:password xmlns:ns5="http://bracbank.com/utilityCommon/V1">Ahj67#12i89kT!z</ns5:password>
               <ns5:channelName xmlns:ns5="http://bracbank.com/utilityCommon/V1">AYS</ns5:channelName>
               <ns5:channelCode xmlns:ns5="http://bracbank.com/utilityCommon/V1">AYS</ns5:channelCode>
               <ns5:globalTransactionId xmlns:ns5="http://bracbank.com/utilityCommon/V1">AYS_INQ_0000000000000001</ns5:globalTransactionId>
            </ns1:header>
            <ns1:errorCode>000</ns1:errorCode>
            <ns1:errorMessage>Success</ns1:errorMessage>
            <ns1:customerList>
               <ns1:customerInfo>
                  <ns1:customer>
                     <ns1:customerId>10000003535661</ns1:customerId>
                     <ns1:cardInterfacingNo>4777920002014860</ns1:cardInterfacingNo>
                     <ns1:cardStatus>00</ns1:cardStatus>
                     <ns1:relationshipId>4777920002014860=2812</ns1:relationshipId>
                  </ns1:customer>
                  <ns1:product>
                     <ns1:productLimit>12000.00</ns1:productLimit>
                     <ns1:productType>1</ns1:productType>
                     <ns1:ecomLimit>3000.00</ns1:ecomLimit>
                     <ns1:isEcomEnabled>1</ns1:isEcomEnabled>
                     <ns1:isEnable>1</ns1:isEnable>
                  </ns1:product>
               </ns1:customerInfo>
            </ns1:customerList>
            <ns1:quotaList>
            </ns1:quotaList>
            <ns1:passportInfo>
               <ns1:passportNo>T01234567</ns1:passportNo>
               <ns1:passportName>TEST IB MULTI 1</ns1:passportName>
               <ns1:passportExpiryDT>2032/12/10</ns1:passportExpiryDT>
               <ns1:customerId>10000003535661</ns1:customerId>
               <ns1:passportIssueDT>2024/06/01</ns1:passportIssueDT>
               <ns1:passportIsssueplace>DHAKA</ns1:passportIsssueplace>
               <ns1:passportPreviousNumber>.</ns1:passportPreviousNumber>
               <ns1:passportAddress>.</ns1:passportAddress>
               <ns1:passportRenewalDT>2032/12/10</ns1:passportRenewalDT>
            </ns1:passportInfo>
         </ns1:MasterInquiryRespDetails>
      </ns1:MasterInquiryResp>
   </soapenv:Body>
</soapenv:Envelope>';

        $testResponse7 = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope
    xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
    <soapenv:Header
        xmlns:v11="http://bracbank.com/utilityCommon/V1"
        xmlns:v1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1"/>
        <soapenv:Body
            xmlns:v11="http://bracbank.com/utilityCommon/V1"
            xmlns:v1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1">
            <ns1:MasterInquiryResp
                xmlns:ns1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1">
                <ns1:MasterInquiryRespDetails>
                    <ns1:header>
                        <ns5:serviceId
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">BBL-006
                        </ns5:serviceId>
                        <ns5:serviceName
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">MasterInquiryES
                        </ns5:serviceName>
                        <ns5:sourceTimestamp
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">2024-05-05T18:56:56.055
                        </ns5:sourceTimestamp>
                        <ns5:password
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">Ahj67#12i89kT!z
                        </ns5:password>
                        <ns5:channelName
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">AYS
                        </ns5:channelName>
                        <ns5:channelCode
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">AYS
                        </ns5:channelCode>
                        <ns5:globalTransactionId
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">AYS_INQ_20240505185656055
                        </ns5:globalTransactionId>
                    </ns1:header>
                    <ns1:errorCode>000</ns1:errorCode>
                    <ns1:errorMessage>Success</ns1:errorMessage>
                    <ns1:customerList>
                        <ns1:customerInfo>
                            <ns1:customer>
                                <ns1:customerId>10000003539497</ns1:customerId>
                                <ns1:cardInterfacingNo>4777920002015511</ns1:cardInterfacingNo>
                                <ns1:cardStatus>00</ns1:cardStatus>
                                <ns1:relationshipId>4777920002015511=2903</ns1:relationshipId>
                            </ns1:customer>
                            <ns1:product>
                                <ns1:productLimit>12000.00</ns1:productLimit>
                                <ns1:productType>1</ns1:productType>
                                <ns1:ecomLimit>300.00</ns1:ecomLimit>
                                <ns1:isEcomEnabled>1</ns1:isEcomEnabled>
                                <ns1:isEnable>1</ns1:isEnable>
                            </ns1:product>
                        </ns1:customerInfo>
                    </ns1:customerList>
                    <ns1:quotaList>
                        <ns1:quotaInfo>
                            <ns1:quotaId>2275</ns1:quotaId>
                            <ns1:cardNumber>477792******5511</ns1:cardNumber>
                            <ns1:accountNumber>1506201719028001</ns1:accountNumber>
                            <ns1:limitType>Travel</ns1:limitType>
                            <ns1:threshodLimit>12000.00</ns1:threshodLimit>
                            <ns1:assignedLimit>220.00</ns1:assignedLimit>
                            <ns1:limitStartDate>2025/04/01</ns1:limitStartDate>
                            <ns1:limitEndDate>2025/12/31</ns1:limitEndDate>
                            <ns1:remainingLimitAmount>12000.00</ns1:remainingLimitAmount>
                            <ns1:ecomIsActive>1</ns1:ecomIsActive>
                            <ns1:ecomThrActive>1</ns1:ecomThrActive>
                            <ns1:unUsagePercentage>5</ns1:unUsagePercentage>
                            <ns1:spentAmount>0.00</ns1:spentAmount>
                            <ns1:availableQuota>209.00</ns1:availableQuota>
                            <ns1:blockageAmount>11.00</ns1:blockageAmount>
                            <ns1:limitAmount>209.00</ns1:limitAmount>
                            <ns1:isActive>1</ns1:isActive>
                            <ns1:ecomthr_from_date/>
                            <ns1:ecomthr_to_date/>
                        </ns1:quotaInfo>
                        <ns1:quotaInfo>
                            <ns1:quotaId>2274</ns1:quotaId>
                            <ns1:cardNumber>477792******5511</ns1:cardNumber>
                            <ns1:accountNumber>1506201719028001</ns1:accountNumber>
                            <ns1:limitType>Travel</ns1:limitType>
                            <ns1:threshodLimit>12000.00</ns1:threshodLimit>
                            <ns1:assignedLimit>410.00</ns1:assignedLimit>
                            <ns1:limitStartDate>2024/04/30</ns1:limitStartDate>
                            <ns1:limitEndDate>2024/12/31</ns1:limitEndDate>
                            <ns1:remainingLimitAmount>12000.00</ns1:remainingLimitAmount>
                            <ns1:ecomIsActive>1</ns1:ecomIsActive>
                            <ns1:ecomThrActive>1</ns1:ecomThrActive>
                            <ns1:unUsagePercentage>5</ns1:unUsagePercentage>
                            <ns1:spentAmount>0.00</ns1:spentAmount>
                            <ns1:availableQuota>389.50</ns1:availableQuota>
                            <ns1:blockageAmount>20.50</ns1:blockageAmount>
                            <ns1:limitAmount>389.50</ns1:limitAmount>
                            <ns1:isActive>1</ns1:isActive>
                            <ns1:ecomthr_from_date/>
                            <ns1:ecomthr_to_date/>
                        </ns1:quotaInfo>
                    </ns1:quotaList>
                    <ns1:passportInfo>
                        <ns1:passportNo>ZN000001</ns1:passportNo>
                        <ns1:passportName>ZIAD NASIM</ns1:passportName>
                        <ns1:passportExpiryDT>2026/09/30</ns1:passportExpiryDT>
                        <ns1:customerId>10000003539497</ns1:customerId>
                        <ns1:passportIssueDT>2024/04/30</ns1:passportIssueDT>
                        <ns1:passportIsssueplace>DHAKA</ns1:passportIsssueplace>
                        <ns1:passportPreviousNumber/>
                        <ns1:passportAddress/>
                        <ns1:passportRenewalDT/>
                    </ns1:passportInfo>
                </ns1:MasterInquiryRespDetails>
            </ns1:MasterInquiryResp>
        </soapenv:Body>
    </soapenv:Envelope>';

        $testResponse4 = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope
    xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
    <soapenv:Header
        xmlns:v11="http://bracbank.com/utilityCommon/V1"
        xmlns:v1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1"/>
        <soapenv:Body
            xmlns:v11="http://bracbank.com/utilityCommon/V1"
            xmlns:v1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1">
            <ns1:MasterInquiryResp
                xmlns:ns1="http://bracbank.com/BBLQuotaAndPassportService/MasterInquiry/V1">
                <ns1:MasterInquiryRespDetails>
                    <ns1:header>
                        <ns5:serviceId
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">BBL-006
                        </ns5:serviceId>
                        <ns5:serviceName
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">MasterInquiryES
                        </ns5:serviceName>
                        <ns5:sourceTimestamp
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">2024-03-24T16:51:44.103
                        </ns5:sourceTimestamp>
                        <ns5:password
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">Ahj67#12i89kT!z
                        </ns5:password>
                        <ns5:channelName
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">AYS
                        </ns5:channelName>
                        <ns5:channelCode
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">AYS
                        </ns5:channelCode>
                        <ns5:globalTransactionId
                            xmlns:ns5="http://bracbank.com/utilityCommon/V1">aysMasterInquiryALL001
                        </ns5:globalTransactionId>
                    </ns1:header>
                    <ns1:errorCode>MW-1003-05-0004</ns1:errorCode>
                    <ns1:errorMessage>CustomerID and CardNumber Anomaly Found</ns1:errorMessage>
                    <ns1:customerList/>
                    <ns1:quotaList/>
                    <ns1:passportInfo/>
                </ns1:MasterInquiryRespDetails>
            </ns1:MasterInquiryResp>
        </soapenv:Body>
    </soapenv:Envelope>';


        if (!empty($testResponse3)){
            // $responseData = preg_replace("/(<\/?)(ns1:)(\w+)([^>]*>)/", "$1$3$4"y, $response);
            $responseData = preg_replace("/(<\/?)(ns1:|ns5:)(\w+)([^>]*>)/", "$1$3$4", $testResponse3);
            $xml = new SimpleXMLElement($responseData);
            $body = $xml->xpath('//soapenv:Body')[0];
            $responseArrays = json_decode(json_encode((array)$body), TRUE);
            $error = [];
            $customerInfo = [];
            $passportInfo = [];
            $currentYearTravelQuota = [];
            $nextYearTravelQuota = [];
            $medicalQuota = [];

            $globalTransactionId = $xml->xpath('//*[local-name()="globalTransactionId"]')[0];
            $errorCode = (string) $body->xpath('//*[local-name()="errorCode"]')[0];
            $errorMessage = (string) $xml->xpath('//*[local-name()="errorMessage"]')[0];

            $this->IRISApiRequestResponse([
                'endpoint' => $endpoint,
                'json_node' => $testResponse3,
                'execution_time' => $execution_time,
                'card_number' => $maskCardNumber,
                'account_number' => $accountNumber,
                'service' => 'MasterInquiryES',
                'type' => 2,
                'status_code' => (!empty($errorCode)) ? $errorCode : '',
                'status_msg' => (!empty($errorMessage)) ? $errorMessage : '',
                'global_transaction_id' => (!empty($globalTransactionId)) ? $globalTransactionId : '',
            ]);

            foreach ($responseArrays as $responseArray){
                foreach ($responseArray as $RespDetails){
                    $error = [
                        'code' => $RespDetails['errorCode'],
                        'message' => $RespDetails['errorMessage'],
                    ];
                    $customerInfo = [
                        'customer' => (!empty($RespDetails['customerList']))? $RespDetails['customerList']['customerInfo']['customer'] : '',
                        'product' =>  (!empty($RespDetails['customerList'])) ? $RespDetails['customerList']['customerInfo']['product'] : '',
                    ];
                    $passportInfo = (!empty($RespDetails['passportInfo'])) ? $RespDetails['passportInfo'] : '';

                    if (!empty($RespDetails['quotaList'])){
                        if(isset($RespDetails['quotaList']['quotaInfo'][0])){
                            foreach ($RespDetails['quotaList']['quotaInfo'] as $row){
                                if (array_key_exists('limitType', $row) && $row['limitType'] == 'Travel'){
                                    // extract current year
                                    $limitEndDate = date('Y', strtotime($row['limitEndDate']));
                                    $nowYear = date('Y', strtotime(now()));

                                    if ($nowYear == $limitEndDate){
                                        $currentYearTravelQuota = $row;
                                    } elseif ($nowYear < $limitEndDate){
                                        $nextYearTravelQuota = $row;
                                    }else{}
                                } elseif (array_key_exists('limitType', $row) && $row['limitType'] == 'Medical'){
                                    $medicalQuota = $row;
                                }
                            }
                        }else{
                           $quotaInfo =  $RespDetails['quotaList']['quotaInfo'];
                            if (array_key_exists('limitType', $quotaInfo) && $quotaInfo['limitType'] == 'Travel'){
                                // extract current year
                                $limitEndDate = date('Y', strtotime($quotaInfo['limitEndDate']));
                                $nowYear = date('Y', strtotime(now()));

                                if ($nowYear == $limitEndDate){
                                    $currentYearTravelQuota = $quotaInfo;
                                } elseif ($nowYear < $limitEndDate){
                                    $nextYearTravelQuota = $quotaInfo;
                                }else{}
                            } elseif (array_key_exists('limitType', $quotaInfo) && $quotaInfo['limitType'] == 'Medical'){
                                $medicalQuota = $quotaInfo;
                            }
                        }
                    }
                }
            }

           $resultArray = [
                'error' => $error,
                'customerInfo' => $customerInfo,
                'encryptCustomerInfo' => encrypt($customerInfo),
                'currentYearTravelQuota' => $currentYearTravelQuota,
                'encryptCurrentYearTravelQuota' => encrypt($currentYearTravelQuota),
                'nextYearTravelQuota' => $nextYearTravelQuota,
                'encryptNextYearTravelQuota' => encrypt($nextYearTravelQuota),
                'passportInfo' => $passportInfo,
                'encryptPassportInfo' => encrypt($passportInfo),
                'medicalQuota' => $medicalQuota,
                'encryptMedicalQuota' => encrypt($medicalQuota),
            ];

           return response()->json(['data' => $resultArray]);

        } else{
            $error = [
                'code' => '999',
                'message' => 'No API Response',
            ];
            $this->IRISApiRequestResponse([
                'endpoint' => $endpoint,
                'json_node' => '',
                'execution_time' => $execution_time,
                'card_number' => $maskCardNumber,
                'account_number' => $accountNumber,
                'service' => 'MasterInquiryES',
                'type' => 2,
                'status_code' => '999',
                'status_msg' => 'No API Response',
                'global_transaction_id' => '',
            ]);
            $resultArray = [
                 'error' => $error,
            ];
            return response()->json(['data' => $resultArray]);
        }
    }

    public function apiUpdate(Request $request)
    {
        $passportRes = [];
        $resultArray = [];

        $subgroup_id = Auth::user()->user_unit->subgroup_info_id;
        $group_info_id = SubgroupInfo::find($subgroup_id);

        try {
            $encrypted_ref_no = xss_cleaner($request->ref_no);
            $encrypted_account_number = xss_cleaner($request->account_number);
            $req_from = xss_cleaner($request->req_from);
            $ref_no = decrypt($encrypted_ref_no);
            $account_number = decrypt($encrypted_account_number);
        } catch (DecryptException $e) {
            $response['success'] = 0;
            $response['msg'] = 'Something went wrong!!!. Please refresh this page';
            return response(['msg' => $response['msg'], 'status' => $response['success']]);
        }

        $referenceModel = new Reference();
        $referenceData = $referenceModel->where('reference_number', $ref_no)->first();

        $dataObj = array();
        if ($req_from == "wform") {
            $wCompModel = new Reference;
            $dataObj = $wCompModel
                ->select('w_form.*', 'w_form_type.extra_field', 'reference.issue_id', 'reference.memo')
                ->leftJoin('w_form', 'reference.reference_number', '=', 'w_form.reference_number')
                ->leftJoin('w_form_type', 'w_form_type.reference_number', '=', 'w_form.reference_number')
                ->where('reference.reference_number', $ref_no)
                ->first();

        } elseif ($req_from == "complaint") {
            $wCompModel = new Reference;
            $dataObj = $wCompModel
                ->select('complaint.*', 'complaint_form_type.extra_field', 'reference.issue_id', 'reference.memo')
                ->leftJoin('complaint', 'reference.reference_number', '=', 'complaint.reference_number')
                ->leftJoin('complaint_form_type', 'complaint_form_type.reference_number', '=', 'complaint.reference_number')
                ->where('reference.reference_number', $ref_no)
                ->first();
        }

        $extra_fields = (array)json_decode($dataObj->extra_field, true);

        // Passport ADD/MOD API Calling
        if (!empty($extra_fields["P"])){
            $irisApiModel = IRISApi::where('name','PassportAddMod')->where('status', 1)->first();

            $endpoint = $irisApiModel->url;
            $request_body_P = $irisApiModel->request;

            $sourceTimestamp = now()->format('Y-m-d\TH:i:s.v');
            $req_type = $extra_fields["P"]['request_type'];
            $customer_id = $extra_fields["P"]['customer_id'];
            $p_response = $extra_fields["P"]['response'];
            $passport_mod_call = true;

            $request_body_P = str_replace("#sourceTimestamp#", $sourceTimestamp, $request_body_P);
            $request_body_P = str_replace("%customerId%", $customer_id, $request_body_P);

            unset($extra_fields["P"]['request_type']);
            unset($extra_fields["P"]['customer_id']);
            unset($extra_fields["P"]['response']);

            $newArray = [];
            foreach ($extra_fields["P"] as $item) {
                // Extracting keys from "api_key" and values from the first key
                $keys = explode(':', $item['api_key']);
                $newArray[$keys[0]] = reset($item);
            }

            foreach ($newArray as $key => $item){
                $request_body_P = str_replace("#" . $key . "#", $item, $request_body_P);
            }

            if ($req_type === 'ADD'){
                $globalTransactionId = 'AYS_Passport_Add_' . now()->format('YmdHisv');
                $request_body_P = str_replace("#globalTransactionId#", $globalTransactionId, $request_body_P);
                $request_body_P = str_replace("%actionType%", 'ADD', $request_body_P);
                $request_body_P = str_replace("%serviceName%", 'PassportAdd', $request_body_P);
            }else{
                $passport_mod_call = false;
                foreach (decrypt($p_response) as $key1 => $value1){
                    foreach ($newArray as $key2 => $value2){
                       if ($key1 == $key2){
                           if ($value1 != $value2){
                               $passport_mod_call = true;
                           }
                       }
                    }
                }

                $globalTransactionId = 'AYS_Passport_Mod_' . now()->format('YmdHisv');
                $request_body_P = str_replace("#globalTransactionId#", $globalTransactionId, $request_body_P);
                $request_body_P = str_replace("%actionType%", 'MOD', $request_body_P);
                $request_body_P = str_replace("%serviceName%", 'PassportMod', $request_body_P);
            }

            $this->IRISApiRequestResponse(['endpoint' => $endpoint, 'json_node' => $request_body_P, 'reference_number' => $ref_no, 'account_number' => $account_number,'card_number' => $dataObj->mask_card_no,'type'=> 1 , 'service' => $req_type === 'ADD' ? 'PassportAdd' : 'PassportMod', 'global_transaction_id' => (!empty($globalTransactionId)) ? $globalTransactionId : '']);

            if ($passport_mod_call){
                $headers = array("Content-type: text/xml;charset=\"utf-8\"", "Accept: text/xml", "Cache-Control: no-cache", "Pragma: no-cache", "SOAPAction: casaListAndDetails", "Content-length: ".strlen($request_body_P));

                $msc = microtime(true);

                /*$ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($ch, CURLOPT_URL, $endpoint);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body_P);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $response = curl_exec($ch);

                if (curl_errno($ch)) {
                    $error_msg = curl_errno($ch);
                }
                curl_close($ch);*/

                $msc = microtime(true) - $msc;
                $execution_time = number_format($msc, 2);

                if (!empty($response)) {
                    $responseData = preg_replace("/(<\/?)(ns1:|ns5:)(\w+)([^>]*>)/", "$1$3$4", $response);
                    $xml = new SimpleXMLElement($responseData);
                    $body = $xml->xpath('//soapenv:Body')[0];
                    $responseArrays = json_decode(json_encode((array)$body), TRUE);

                    $globalTransactionId = $xml->xpath('//*[local-name()="globalTransactionId"]')[0];
                    $errorCode = (string)$xml->xpath('//*[local-name()="errorCode"]')[0];
                    $errorMessage = (string)$xml->xpath('//*[local-name()="errorMessage"]')[0];
                    $resultArray['P'] = [
                        'errorCode' => $errorCode,
                        'errorMessage' => $errorMessage,
                    ];
                    $this->IRISApiRequestResponse(['endpoint' => $endpoint, 'json_node' => $response, 'reference_number' => $ref_no,'type' => 2, 'account_number' => $account_number,'card_number' => $dataObj->mask_card_no, 'service' => $req_type === 'ADD' ? 'PassportAdd' : 'PassportMod', 'status_code' => (!empty($errorCode)) ? $errorCode : '', 'status_msg' => (!empty($errorMessage)) ? $errorMessage : '', 'global_transaction_id' => (!empty($globalTransactionId)) ? $globalTransactionId : '', 'execution_time' => $execution_time]);
                    $this->audit(['reference_number' => $ref_no, 'unit_id' => $referenceData->unit_id,
                        'group_id' => $group_info_id->group_info_id, 'user_id' => Auth::user()->user_id,
                        'action' => 'Passport ' .$req_type, 'comments' => $errorMessage, 'subgroup_id' => $subgroup_id]);
                }else{
                    $resultArray['P'] = [
                        'errorCode' => '999',
                        'errorMessage' => 'No API Response',
                    ];
                    $this->IRISApiRequestResponse(['endpoint' => $endpoint, 'json_node' => '', 'reference_number' => $ref_no,'type' => 2, 'account_number' => $account_number,'card_number' => $dataObj->mask_card_no, 'service' => $req_type === 'ADD' ? 'PassportAdd' : 'PassportMod', 'status_code' => '999', 'status_msg' => 'No API Response', 'global_transaction_id' => '', 'execution_time' => $execution_time]);
                }
            }else{
                $resultArray['P'] = [
                    'errorCode' => '000',
                    'errorMessage' => 'No API Calling',
                ];
                $this->IRISApiRequestResponse(['endpoint' => $endpoint, 'json_node' => '', 'reference_number' => $ref_no,'type' => 2, 'account_number' => $account_number,'card_number' => $dataObj->mask_card_no, 'service' => $req_type === 'ADD' ? 'PassportAdd' : 'PassportMod', 'status_code' => '000', 'status_msg' => 'No API Calling', 'global_transaction_id' => '']);
            }
        }

        // TQ Current Year API Calling
        if (!empty($extra_fields["C"])){
            $req_type = (!empty($extra_fields["C"]['request_type'])) ? $extra_fields["C"]['request_type'] : '';
            $customer_info = (!empty($extra_fields['CInfo'])) ? $extra_fields['CInfo'] : '';
            $old_res_c = (!empty($extra_fields["C"]['response'])) ? $extra_fields["C"]['response'] : '';
            $CY_mod_call = true;

            $sourceTimestamp = now()->format('Y-m-d\TH:i:s.v');

            $irisApiModel = IRISApi::where('status', 1);

            if($req_type == "ADD") {
                $irisApiModel = $irisApiModel->where('name', 'BBLQuotaAdd')->first();
                $endpoint = $irisApiModel->url;
                $request_body_C = $irisApiModel->request;

                $globalTransactionId = 'AYS_Quota_Add_' . now()->format('YmdHisv');
                $request_body_C = str_replace("#globalTransactionId#", $globalTransactionId, $request_body_C);
                $request_body_C = str_replace("#sourceTimestamp#", $sourceTimestamp, $request_body_C);
                $request_body_C = str_replace("%whichYear%", 0, $request_body_C);

                unset($extra_fields["C"]['request_type']);
                unset($extra_fields["C"]['customer_info']);
                unset($extra_fields["C"]['quota_id']);
                unset($extra_fields["C"]['response']);

                $newArray = [];
                foreach ($extra_fields["C"] as $item) {
                    // Extracting keys from "api_key" and values from the first key
                    $keys = explode(':', $item['api_key']);
                    $newArray[$keys[0]] = reset($item);
                }

                foreach ($newArray as $key => $item){
                    $request_body_C = str_replace("#" . $key . "#", $item, $request_body_C);
                }

                if (!empty($customer_info)){
                    $encryptInfo =  decrypt($customer_info);
                    $request_body_C = str_replace("%productType%", $encryptInfo['product']['productType'], $request_body_C);
                    $request_body_C = str_replace("%customerId%", $encryptInfo['customer']['customerId'], $request_body_C);
                    $request_body_C = str_replace("%relationshipId%", $encryptInfo['customer']['relationshipId'], $request_body_C);
                    $request_body_C = str_replace("%cardInterfacingNo%", $encryptInfo['customer']['cardInterfacingNo'], $request_body_C);
                }

                $this->IRISApiRequestResponse(['endpoint' => $endpoint, 'json_node' => $request_body_C,'type' => 1, 'reference_number' => $ref_no, 'account_number' => $account_number,'card_number' => $dataObj->mask_card_no, 'service' => 'BBLQuotaPassportES', 'global_transaction_id' => (!empty($globalTransactionId)) ? $globalTransactionId : '']);
            } else {
                $irisApiModel = $irisApiModel->where('name', 'BBLQuotaMod')->first();
                $endpoint = $irisApiModel->url;
                $request_body_C = $irisApiModel->request;

                try {
                    $old_res_c = decrypt($old_res_c);
                } catch (DecryptException $e) {
                    $resultArray['CY'] = [
                        'errorCode' => "403",
                        'errorMessage' => $e->getMessage(),
                    ];
                }

                $quota_id = (!empty($extra_fields["C"]['quota_id'])) ? $extra_fields["C"]['quota_id'] : '';

                $globalTransactionId = 'AYS_Quota_Mod_' . now()->format('YmdHisv');
                $request_body_C = str_replace("#globalTransactionId#", $globalTransactionId, $request_body_C);
                $request_body_C = str_replace("#sourceTimestamp#", $sourceTimestamp, $request_body_C);
                $request_body_C = str_replace("%whichYear%", 0, $request_body_C);
                $request_body_C = str_replace("%quotaId%", $quota_id, $request_body_C);
                /*$request_body_C = str_replace("%limitAmount%", !empty($old_res_c['limitAmount']) ? $old_res_c['limitAmount'] : '', $request_body_C);*/
                /*$request_body_C = str_replace("%unUsagePercentage%", !empty($old_res_c['unUsagePercentage']) ? $old_res_c['unUsagePercentage'] : '', $request_body_C);*/
                $request_body_C = str_replace("%ecomThrActive_from_date%", !empty($old_res_c['ecomthr_from_date']) ? $old_res_c['ecomthr_from_date'] : '', $request_body_C);
                $request_body_C = str_replace("%ecomThrActive_to_date%", !empty($old_res_c['ecomthr_to_date']) ? $old_res_c['ecomthr_to_date'] : '', $request_body_C);

                unset($extra_fields["C"]['request_type']);
                unset($extra_fields["C"]['customer_info']);
                unset($extra_fields["C"]['quota_id']);
                unset($extra_fields["C"]['response']);

                $newArray = [];
                foreach ($extra_fields["C"] as $item) {
                    // Extracting keys from "api_key" and values from the first key
                    $keys = explode(':', $item['api_key']);
                    $newArray[$keys[0]] = reset($item);
                }

                $CY_mod_call = false;
                foreach ($old_res_c as $key1 => $value1){
                    foreach ($newArray as $key2 => $value2){
                        if ($key1 == $key2){
                            if ($value1 != $value2){
                                $CY_mod_call = true;
                            }
                        }
                    }
                }

                foreach ($newArray as $key => $item){
                    if ($key == 'assignedLimit'){
                        $request_body_C = str_replace("%limitAmount%",$item , $request_body_C);
                    }elseif ($key == 'unUsagePercentage'){
                        $request_body_C = str_replace("%limitUnUsagePercentage%",$item , $request_body_C);
                    } else{
                        $request_body_C = str_replace("#" . $key . "#", $item, $request_body_C);
                    }

                }

                $this->IRISApiRequestResponse(['endpoint' => $endpoint, 'json_node' => $request_body_C,'type' => 1, 'reference_number' => $ref_no, 'account_number' => $account_number,'card_number' => $dataObj->mask_card_no, 'service' => 'BBLQuotaPassportES', 'global_transaction_id' => (!empty($globalTransactionId)) ? $globalTransactionId : '']);
            }

            if ($CY_mod_call){
                $headers = array("Content-type: text/xml;charset=\"utf-8\"", "Accept: text/xml", "Cache-Control: no-cache", "Pragma: no-cache", "SOAPAction: casaListAndDetails", "Content-length: ".strlen($request_body_C));
                $msc = microtime(true);

                /*$ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($ch, CURLOPT_URL, $endpoint);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body_C);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $response = curl_exec($ch);

                if (curl_errno($ch)) {
                    $error_msg = curl_errno($ch);
                }
                curl_close($ch);*/

                $msc = microtime(true) - $msc;
                $execution_time = number_format($msc, 2);

                if (!empty($response)) {
                    $responseData = preg_replace("/(<\/?)(ns1:|ns5:)(\w+)([^>]*>)/", "$1$3$4", $response);
                    $xml = new SimpleXMLElement($responseData);
                    $body = $xml->xpath('//soapenv:Body')[0];
                    $globalTransactionId = $xml->xpath('//*[local-name()="globalTransactionId"]')[0];
                    $errorCode = (string)$xml->xpath('//*[local-name()="errorCode"]')[0];
                    $errorMessage = (string)$xml->xpath('//*[local-name()="errorMessage"]')[0];
                    /*$responseArrays = json_decode(json_encode((array)$body), TRUE);*/
                    $resultArray['CY'] = [
                        'errorCode' => $errorCode,
                        'errorMessage' => $errorMessage,
                    ];
                    $this->IRISApiRequestResponse(['endpoint' => $endpoint, 'json_node' => $response,'type' => 2, 'reference_number' => $ref_no, 'account_number' => $account_number,'card_number' => $dataObj->mask_card_no, 'service' => 'BBLQuotaPassportES', 'status_code' => (!empty($errorCode)) ? $errorCode : '', 'status_msg' => (!empty($errorMessage)) ? $errorMessage : '', 'global_transaction_id' => (!empty($globalTransactionId)) ? $globalTransactionId : '', 'execution_time' => $execution_time]);
                    $this->audit(['reference_number' => $ref_no, 'unit_id' => $referenceData->unit_id,
                        'group_id' => $group_info_id->group_info_id, 'user_id' => Auth::user()->user_id,
                        'action' => 'TQ Current Year ' .$req_type, 'comments' => $errorMessage, 'subgroup_id' => $subgroup_id]);
                }else{
                    $resultArray['CY'] = [
                        'errorCode' => '999',
                        'errorMessage' => 'No API Response',
                    ];
                    $this->IRISApiRequestResponse(['endpoint' => $endpoint, 'json_node' => '', 'reference_number' => $ref_no, 'type' => 2, 'account_number' => $account_number,'card_number' => $dataObj->mask_card_no, 'service' => 'BBLQuotaPassportES', 'status_code' => '999', 'status_msg' => 'No API Response', 'global_transaction_id' => '', 'execution_time' => $execution_time]);
                }
            }else{
                $resultArray['CY'] = [
                    'errorCode' => '000',
                    'errorMessage' => 'No API Calling',
                ];
                $this->IRISApiRequestResponse(['endpoint' => $endpoint, 'json_node' => '', 'reference_number' => $ref_no, 'type' => 2, 'account_number' => $account_number,'card_number' => $dataObj->mask_card_no, 'service' => 'BBLQuotaPassportES', 'status_code' => '000', 'status_msg' => 'No API Calling', 'global_transaction_id' => '']);
            }
        }

        // TQ Next Year API Calling
        if (!empty($extra_fields["N"])){
            $req_type = (!empty($extra_fields["N"]['request_type'])) ? $extra_fields["N"]['request_type'] : '';
            $customer_info = (!empty($extra_fields['CInfo'])) ? $extra_fields['CInfo'] : '';
            $sourceTimestamp = now()->format('Y-m-d\TH:i:s.v');
            $old_res_n = (!empty($extra_fields["N"]['response'])) ? $extra_fields["N"]['response'] : '';
            $NY_mod_call = true;

            $irisApiModel = IRISApi::where('status', 1);

            if($req_type == "ADD") {
                $irisApiModel = $irisApiModel->where('name', 'BBLQuotaAdd')->first();
                $endpoint = $irisApiModel->url;
                $request_body_N = $irisApiModel->request;

                $globalTransactionId = 'AYS_Quota_Add_' . now()->format('YmdHisv');
                $request_body_N = str_replace("#globalTransactionId#", $globalTransactionId, $request_body_N);
                $request_body_N = str_replace("#sourceTimestamp#", $sourceTimestamp, $request_body_N);
                $request_body_N = str_replace("%whichYear%", 1, $request_body_N);

                unset($extra_fields["N"]['request_type']);
                unset($extra_fields["N"]['customer_info']);
                unset($extra_fields["N"]['quota_id']);
                unset($extra_fields["N"]['response']);

                $newArray = [];
                foreach ($extra_fields["N"] as $item) {
                    // Extracting keys from "api_key" and values from the first key
                    $keys = explode(':', $item['api_key']);
                    $newArray[$keys[0]] = reset($item);
                }

                foreach ($newArray as $key => $item){
                    $request_body_N = str_replace("#" . $key . "#", $item, $request_body_N);
                }

                if (!empty($customer_info)){
                    $encryptInfo =  decrypt($customer_info);
                    $request_body_N = str_replace("%productType%", $encryptInfo['product']['productType'], $request_body_N);
                    $request_body_N = str_replace("%customerId%", $encryptInfo['customer']['customerId'], $request_body_N);
                    $request_body_N = str_replace("%relationshipId%", $encryptInfo['customer']['relationshipId'], $request_body_N);
                    $request_body_N = str_replace("%cardInterfacingNo%", $encryptInfo['customer']['cardInterfacingNo'], $request_body_N);
                }

                $this->IRISApiRequestResponse(['endpoint' => $endpoint, 'json_node' => $request_body_N, 'type' => 1 , 'reference_number' => $ref_no, 'account_number' => $account_number,'card_number' => $dataObj->mask_card_no, 'service' => 'BBLQuotaPassportES', 'global_transaction_id' => (!empty($globalTransactionId)) ? $globalTransactionId : '']);
            } else {
                $irisApiModel = $irisApiModel->where('name', 'BBLQuotaMod')->first();
                $endpoint = $irisApiModel->url;
                $request_body_N = $irisApiModel->request;
                try {
                    $old_res_n = decrypt($old_res_n);
                } catch (DecryptException $e) {
                    $resultArray['NY'] = [
                        'errorCode' => "403",
                        'errorMessage' => $e->getMessage(),
                    ];
                }

                $quota_id = (!empty($extra_fields["N"]['quota_id'])) ? $extra_fields["N"]['quota_id'] : '';

                $globalTransactionId = 'AYS_Quota_Mod_' . now()->format('YmdHisv');
                $request_body_N = str_replace("#globalTransactionId#", $globalTransactionId, $request_body_N);
                $request_body_N = str_replace("#sourceTimestamp#", $sourceTimestamp, $request_body_N);
                $request_body_N = str_replace("%whichYear%", 1, $request_body_N);
                $request_body_N = str_replace("%quotaId%", $quota_id, $request_body_N);
                /*$request_body_N = str_replace("%limitAmount%", !empty($old_res_n['limitAmount']) ? $old_res_n['limitAmount'] : '', $request_body_N);
                $request_body_N = str_replace("%unUsagePercentage%", !empty($old_res_n['unUsagePercentage']) ? $old_res_n['unUsagePercentage'] : '', $request_body_N);*/
                $request_body_N = str_replace("%ecomThrActive_from_date%", !empty($old_res_n['ecomthr_from_date']) ? $old_res_n['ecomthr_from_date'] : '', $request_body_N);
                $request_body_N = str_replace("%ecomThrActive_to_date%", !empty($old_res_n['ecomthr_to_date']) ? $old_res_n['ecomthr_to_date'] : '', $request_body_N);

                unset($extra_fields["N"]['request_type']);
                unset($extra_fields["N"]['customer_info']);
                unset($extra_fields["N"]['quota_id']);
                unset($extra_fields["N"]['response']);

                $newArray = [];
                foreach ($extra_fields["N"] as $item) {
                    // Extracting keys from "api_key" and values from the first key
                    $keys = explode(':', $item['api_key']);
                    $newArray[$keys[0]] = reset($item);
                }

                $NY_mod_call = false;
                foreach ($old_res_n as $key1 => $value1){
                    foreach ($newArray as $key2 => $value2){
                        if ($key1 == $key2){
                            if ($value1 != $value2){
                                $NY_mod_call = true;
                            }
                        }
                    }
                }

                foreach ($newArray as $key => $item){
                    if ($key == 'assignedLimit'){
                        $request_body_N = str_replace("%limitAmount%",$item , $request_body_N);
                    }elseif ($key == 'unUsagePercentage'){
                        $request_body_N = str_replace("%limitUnUsagePercentage%",$item , $request_body_N);
                    }else{
                        $request_body_N = str_replace("#" . $key . "#", $item, $request_body_N);
                    }
                }

                $this->IRISApiRequestResponse(['endpoint' => $endpoint, 'json_node' => $request_body_N, 'type' => 1 , 'reference_number' => $ref_no, 'account_number' => $account_number,'card_number' => $dataObj->mask_card_no, 'service' => 'BBLQuotaPassportES', 'global_transaction_id' => (!empty($globalTransactionId)) ? $globalTransactionId : '']);
            }

            if ($NY_mod_call){
                $headers = array("Content-type: text/xml;charset=\"utf-8\"", "Accept: text/xml", "Cache-Control: no-cache", "Pragma: no-cache", "SOAPAction: casaListAndDetails", "Content-length: ".strlen($request_body_N));
                $msc = microtime(true);

                /*$ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($ch, CURLOPT_URL, $endpoint);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body_N);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $response = curl_exec($ch);

                if (curl_errno($ch)) {
                    $error_msg = curl_errno($ch);
                }
                curl_close($ch);*/

                $msc = microtime(true) - $msc;
                $execution_time = number_format($msc, 2);

                if (!empty($response)) {
                    $responseData = preg_replace("/(<\/?)(ns1:|ns5:)(\w+)([^>]*>)/", "$1$3$4", $response);
                    $xml = new SimpleXMLElement($responseData);
                    $body = $xml->xpath('//soapenv:Body')[0];
                    $responseArrays = json_decode(json_encode((array)$body), TRUE);

                    $globalTransactionId = $xml->xpath('//*[local-name()="globalTransactionId"]')[0];
                    $errorCode = (string)$xml->xpath('//*[local-name()="errorCode"]')[0];
                    $errorMessage = (string)$xml->xpath('//*[local-name()="errorMessage"]')[0];
                    $resultArray['NY'] = [
                        'errorCode' => $errorCode,
                        'errorMessage' => $errorMessage,
                    ];
                    $this->IRISApiRequestResponse(['endpoint' => $endpoint, 'json_node' => $response, 'type' => 2 , 'reference_number' => $ref_no, 'account_number' => $account_number,'card_number' => $dataObj->mask_card_no, 'service' => 'BBLQuotaPassportES', 'status_code' => (!empty($errorCode)) ? $errorCode : '', 'status_msg' => (!empty($errorMessage)) ? $errorMessage : '', 'global_transaction_id' => (!empty($globalTransactionId)) ? $globalTransactionId : '', 'execution_time' => $execution_time]);
                    $this->audit(['reference_number' => $ref_no, 'unit_id' => $referenceData->unit_id,
                        'group_id' => $group_info_id->group_info_id, 'user_id' => Auth::user()->user_id,
                        'action' => 'TQ Next Year ' .$req_type, 'comments' => $errorMessage, 'subgroup_id' => $subgroup_id]);
                }else{
                    $resultArray['NY'] = [
                        'errorCode' => '999',
                        'errorMessage' => 'No API Response',
                    ];
                    $this->IRISApiRequestResponse(['endpoint' => $endpoint, 'json_node' => '', 'reference_number' => $ref_no, 'type' => 2, 'account_number' => $account_number,'card_number' => $dataObj->mask_card_no, 'service' => 'BBLQuotaPassportES', 'status_code' => '999', 'status_msg' => 'No API Response', 'global_transaction_id' => '', 'execution_time' => $execution_time]);
                }
            }else{
                $resultArray['NY'] = [
                    'errorCode' => '000',
                    'errorMessage' => 'No API Calling',
                ];
                $this->IRISApiRequestResponse(['endpoint' => $endpoint, 'json_node' => '', 'reference_number' => $ref_no, 'type' => 2, 'account_number' => $account_number,'card_number' => $dataObj->mask_card_no, 'service' => 'BBLQuotaPassportES', 'status_code' => '000', 'status_msg' => 'No API Calling', 'global_transaction_id' => '']);
            }
        }

        // Medical Quota(MQ) API Calling
        if (!empty($extra_fields["MQ"])){
            $req_type = (!empty($extra_fields["MQ"]['request_type'])) ? $extra_fields["MQ"]['request_type'] : '';
            $customer_info = (!empty($extra_fields['CInfo'])) ? $extra_fields['CInfo'] : '';
            $sourceTimestamp = now()->format('Y-m-d\TH:i:s.v');
            $old_res_MQ = (!empty($extra_fields["MQ"]['response'])) ? $extra_fields["MQ"]['response'] : '';
            $MQ_mod_call = true;

            $irisApiModel = IRISApi::where('status', 1);

            if($req_type == "ADD") {
                $irisApiModel = $irisApiModel->where('name', 'BBLQuotaAdd')->first();
                $endpoint = $irisApiModel->url;
                $request_body_MQ = $irisApiModel->request;

                $globalTransactionId = 'AYS_Quota_Add_' . now()->format('YmdHisv');
                $request_body_MQ = str_replace("#globalTransactionId#", $globalTransactionId, $request_body_MQ);
                $request_body_MQ = str_replace("#sourceTimestamp#", $sourceTimestamp, $request_body_MQ);
                $request_body_MQ = str_replace("%whichYear%", " ", $request_body_MQ);

                unset($extra_fields["MQ"]['request_type']);
                unset($extra_fields["MQ"]['customer_info']);
                unset($extra_fields["MQ"]['quota_id']);
                unset($extra_fields["MQ"]['response']);

                $newArray = [];
                foreach ($extra_fields["MQ"] as $item) {
                    // Extracting keys from "api_key" and values from the first key
                    $keys = explode(':', $item['api_key']);
                    $newArray[$keys[0]] = reset($item);
                }

                foreach ($newArray as $key => $item){
                    $request_body_MQ = str_replace("#" . $key . "#", $item, $request_body_MQ);
                }

                if (!empty($customer_info)){
                    $encryptInfo =  decrypt($customer_info);
                    $request_body_MQ = str_replace("%productType%", $encryptInfo['product']['productType'], $request_body_MQ);
                    $request_body_MQ = str_replace("%customerId%", $encryptInfo['customer']['customerId'], $request_body_MQ);
                    $request_body_MQ = str_replace("%relationshipId%", $encryptInfo['customer']['relationshipId'], $request_body_MQ);
                    $request_body_MQ = str_replace("%cardInterfacingNo%", $encryptInfo['customer']['cardInterfacingNo'], $request_body_MQ);
                }

                $this->IRISApiRequestResponse(['endpoint' => $endpoint, 'type' => 1 , 'json_node' => $request_body_MQ, 'reference_number' => $ref_no, 'account_number' => $account_number,'card_number' => $dataObj->mask_card_no, 'service' => 'BBLQuotaPassportES', 'global_transaction_id' => (!empty($globalTransactionId)) ? $globalTransactionId : '']);
            } else {
                $irisApiModel = $irisApiModel->where('name', 'BBLQuotaMod')->first();
                $endpoint = $irisApiModel->url;
                $request_body_MQ = $irisApiModel->request;
                try {
                    $old_res_MQ = decrypt($old_res_MQ);
                } catch (DecryptException $e) {
                    $resultArray['MQ'] = [
                        'errorCode' => "403",
                        'errorMessage' => $e->getMessage(),
                    ];
                }

                $quota_id = (!empty($extra_fields["MQ"]['quota_id'])) ? $extra_fields["MQ"]['quota_id'] : '';

                $globalTransactionId = 'AYS_Quota_Mod_' . now()->format('YmdHisv');
                $request_body_MQ = str_replace("#globalTransactionId#", $globalTransactionId, $request_body_MQ);
                $request_body_MQ = str_replace("#sourceTimestamp#", $sourceTimestamp, $request_body_MQ);
                $request_body_MQ = str_replace("%whichYear%", " ", $request_body_MQ);
                $request_body_MQ = str_replace("%quotaId%", $quota_id, $request_body_MQ);
//                $request_body_MQ = str_replace("%limitAmount%", !empty($old_res_MQ['limitAmount']) ? $old_res_MQ['limitAmount'] : '', $request_body_MQ);
//                $request_body_MQ = str_replace("%unUsagePercentage%", !empty($old_res_MQ['unUsagePercentage']) ? $old_res_MQ['unUsagePercentage'] : '', $request_body_MQ);
                $request_body_MQ = str_replace("%ecomThrActive_from_date%", !empty($old_res_MQ['ecomthr_from_date']) ? $old_res_MQ['ecomthr_from_date'] : '', $request_body_MQ);
                $request_body_MQ = str_replace("%ecomThrActive_to_date%", !empty($old_res_MQ['ecomthr_to_date']) ? $old_res_MQ['ecomthr_to_date'] : '', $request_body_MQ);

                unset($extra_fields["MQ"]['request_type']);
                unset($extra_fields["MQ"]['customer_info']);
                unset($extra_fields["MQ"]['quota_id']);
                unset($extra_fields["MQ"]['response']);

                $newArray = [];
                foreach ($extra_fields["MQ"] as $item) {
                    $keys = explode(':', $item['api_key']);
                    $newArray[$keys[0]] = reset($item);
                }

                $MQ_mod_call = false;
                foreach ($old_res_MQ as $key1 => $value1){
                    foreach ($newArray as $key2 => $value2){
                        if ($key1 == $key2){
                            if ($value1 != $value2){
                                $MQ_mod_call = true;
                            }
                        }
                    }
                }

                foreach ($newArray as $key => $item){
                    if ($key == 'assignedLimit'){
                        $request_body_MQ = str_replace("%limitAmount%",$item , $request_body_MQ);
                    }elseif ($key == 'unUsagePercentage'){
                        $request_body_MQ = str_replace("%limitUnUsagePercentage%",$item , $request_body_MQ);
                    }else{
                        $request_body_MQ = str_replace("#" . $key . "#", $item, $request_body_MQ);
                    }
                }

                $this->IRISApiRequestResponse(['endpoint' => $endpoint, 'type' => 1 , 'json_node' => $request_body_MQ, 'reference_number' => $ref_no, 'account_number' => $account_number,'card_number' => $dataObj->mask_card_no, 'service' => 'BBLQuotaPassportES', 'global_transaction_id' => (!empty($globalTransactionId)) ? $globalTransactionId : '']);
            }

            if ($MQ_mod_call){
                $headers = array("Content-type: text/xml;charset=\"utf-8\"", "Accept: text/xml", "Cache-Control: no-cache", "Pragma: no-cache", "SOAPAction: casaListAndDetails", "Content-length: ".strlen($request_body_MQ));
                $msc = microtime(true);

                /*$ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($ch, CURLOPT_URL, $endpoint);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body_MQ);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $response = curl_exec($ch);

                if (curl_errno($ch)) {
                    $error_msg = curl_errno($ch);
                }
                curl_close($ch);*/

                $msc = microtime(true) - $msc;
                $execution_time = number_format($msc, 2);

                if (!empty($response)) {
                    $responseData = preg_replace("/(<\/?)(ns1:|ns5:)(\w+)([^>]*>)/", "$1$3$4", $response);
                    $xml = new SimpleXMLElement($responseData);
                    $body = $xml->xpath('//soapenv:Body')[0];
                    $responseArrays = json_decode(json_encode((array)$body), TRUE);

                    $globalTransactionId = $xml->xpath('//*[local-name()="globalTransactionId"]')[0];
                    $errorCode = (string)$xml->xpath('//*[local-name()="errorCode"]')[0];
                    $errorMessage = (string)$xml->xpath('//*[local-name()="errorMessage"]')[0];
                    $resultArray['MQ'] = [
                        'errorCode' => $errorCode,
                        'errorMessage' => $errorMessage,
                    ];
                    $this->IRISApiRequestResponse(['endpoint' => $endpoint, 'type' => 2, 'json_node' => $response, 'reference_number' => $ref_no, 'account_number' => $account_number,'card_number' => $dataObj->mask_card_no, 'service' => 'BBLQuotaPassportES', 'status_code' => (!empty($errorCode)) ? $errorCode : '', 'status_msg' => (!empty($errorMessage)) ? $errorMessage : '', 'global_transaction_id' => (!empty($globalTransactionId)) ? $globalTransactionId : '', 'execution_time' => $execution_time]);
                    $this->audit(['reference_number' => $ref_no, 'unit_id' => $referenceData->unit_id,
                        'group_id' => $group_info_id->group_info_id, 'user_id' => Auth::user()->user_id,
                        'action' => 'Medical Quota ' .$req_type, 'comments' => $errorMessage, 'subgroup_id' => $subgroup_id]);
                }else{
                    $resultArray['MQ'] = [
                        'errorCode' => '999',
                        'errorMessage' => 'No API Response',
                    ];
                    $this->IRISApiRequestResponse(['endpoint' => $endpoint, 'type' => 2, 'json_node' => '', 'reference_number' => $ref_no, 'account_number' => $account_number,'card_number' => $dataObj->mask_card_no, 'service' => 'BBLQuotaPassportES', 'status_code' => '999', 'status_msg' => 'No Api Response!', 'global_transaction_id' => '', 'execution_time' => $execution_time]);
                }
            }else{
                $resultArray['MQ'] = [
                    'errorCode' => '000',
                    'errorMessage' => 'No API Calling',
                ];
                $this->IRISApiRequestResponse(['endpoint' => $endpoint, 'json_node' => '', 'reference_number' => $ref_no, 'type' => 2, 'account_number' => $account_number,'card_number' => $dataObj->mask_card_no, 'service' => 'BBLQuotaPassportES', 'status_code' => '000', 'status_msg' => 'No API Calling', 'global_transaction_id' => '']);
            }
        }

        if (!empty($resultArray)){
            $allSuccess = 1;
            foreach ($resultArray as $subArray) {
                if (!isset($subArray['errorCode']) || $subArray['errorCode'] != '000') {
                    $allSuccess = 0;
                    break;
                }
            }
            if ($allSuccess == 0){
                $refs = $referenceData->failed_retry;
                $referenceData->failed_retry = $refs + 1;
            }
            $referenceData->iris_api_status = 1;
            $referenceData->save();

            $failedRetryCount = $referenceData->failed_retry;


            return response()->json(['resultArray' => $resultArray, 'successStatus' => $allSuccess, 'failedRetryCount' => $failedRetryCount]);
        }
    }

    public function api_update_check(Request $request)
    {
        $status = 0;
        $ref_no = '';
        try {
            $encrypted_ref_no = xss_cleaner($request->ref_no);
            $ref_no = decrypt($encrypted_ref_no);
        } catch (DecryptException $e) {
            return response(['status' => $status]);
        }
        $referenceModel = new Reference();
        $referenceData = $referenceModel->where('reference_number', $ref_no)->first();
        if (!empty($referenceData)) {
            if ($referenceData->failed_retry == 1) {
                $status = 1;
            }elseif ($referenceData->failed_retry == 2){
                $status = 2;
            }
        }
        return response(['status' => $status]);
    }


    public function irisApiList()
    {
        $title = "IRIS Api Lists";
        $title_for_layout = "IRIS Api Lists";
        $cifModelName = new IRISApi;
        $tblData = array();
        $dataObj = $cifModelName
            ->select("iris_apis.id", "iris_apis.name", "iris_apis.url", "iris_apis.request",
                DB::raw("CASE WHEN iris_apis.status = 1 THEN 'Active' WHEN iris_apis.status = 2 THEN 'Inactive' ELSE 'Invalid' END AS status_name"), "iris_apis.status")

            ->orderBy("iris_apis.id", "DESC")
            ->get();
        if (!empty($dataObj)) {
            $tblData = $dataObj->toArray();
        }
        return view('IRIS.list', compact('title', 'title_for_layout', 'tblData', 'dataObj'));
    }

    public function apiEdit($id = null)
    {
        $title = "IRIS Apis Request";
        $title_for_layout = 'IRIS Apis Request';
        $dataForView = IRISApi::find(decrypt($id));
        return view('IRIS.edit',  compact('title', 'title_for_layout', 'dataForView'));
    }

    public function irisApiAdd()
    {
        $title = "Add IRIS Apis Request";
        $title_for_layout = "Add IRIS Apis Request";
        $cifParent = CIFParentUrl::select("id", "name")
            ->where("status", 1)
            ->orderBy("id", "DESC")
            ->get();
        return view('IRIS.add', compact('title', 'title_for_layout', 'cifParent'));
    }

    public function irisApiStore(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'url' => 'required',
            'request' => 'required',
        ]);
        $cifModelName = new IRISApi;
        if ($request->isMethod('post')) {
            $cifModelName->name = $request->get('name');
            $cifModelName->url = $request->get('url');
            //$cifModelName->request = $request->get('request');
            $cifModelName->request = trim(preg_replace('/\s\s+/', ' ', $request->get('request')));
            $cifModelName->status = 1;
            if ($cifModelName->save()) {
                flash('IRIS Api has been stored successfully', 'success');
                return redirect('/iris/api/list');
            } else {
                flash('Failed to insert data', 'danger');
                return redirect('/iris/api/create');
            }
        }
    }

    public function apiModificationUpdate($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'url' => 'required',
            'request' => 'required',
        ]);
        $apiModelName = IRISApi::find($id);
        if ($request->isMethod('post')) {
            $apiModelName->name = $request->get('name');
            $apiModelName->url = $request->get('url');
            $apiModelName->status = $request->get('status');
            //$apiModelName->request = $request->get('request');
            $apiModelName->request = trim(preg_replace('/\s\s+/', ' ', $request->get('request')));
            if ($apiModelName->save()) {
                flash('IRIS Api has been updated successfully', 'success');
                return redirect('iris/api/list');
            } else {
                flash('Failed to update data', 'danger');
                return redirect()->back();
            }
        }
    }

    public function irisApiStatus($id = null, $status)
    {
        $data = IRISApi::find(decrypt($id));
        // dd($status);
        if (!empty($data)) {
            $data->update(['status' => $status]);
            if ($status == 1) {
                flash('IRIS Url has been activated successfully', 'success');
            } else {
                flash('IRIS Url has been inactivated !!', 'warning');
            }
        } else {
            flash('Not Found', 'danger');
        }
        return redirect()->back();
    }

    public function inquiryData(Request $request)
    {
        try {
            $passport = !empty($request->passport) ? decrypt($request->passport) : '';
            $customerInfo = !empty($request->customerInfo) ? decrypt($request->customerInfo) : '';
            $CQuota = !empty($request->CQuota) ? decrypt($request->CQuota) : '';
            $NQuota = !empty($request->NQuota) ? decrypt($request->NQuota) : '';
            $MQQuota = !empty($request->MQQuota) ? decrypt($request->MQQuota) : '';
        } catch (DecryptException $e) {
            return response()->json(['data' => 'something went wrong']);
        }
        $data = '';

        if (!empty($customerInfo)){
            $data .= '<h5> Customer Info </h5>';
            foreach ($customerInfo as $key => $a){
               foreach ($a as $key => $row){
                   $value = !is_array($row) ? $row : '';
                   if ($key == 'cardStatus'){
                       if ($value == '00'){
                           $value = 'Active';
                       }elseif ($value == '01'){
                           $value = 'Warm';
                       }elseif ($value == '02'){
                           $value = 'HOT';
                       }elseif ($value == '03'){
                           $value = 'Fresh';
                       }elseif ($value == '05'){
                           $value = 'Produced';
                       }elseif ($value == '08'){
                           $value = 'Force PIN';
                       }elseif ($value == '11'){
                           $value = 'Not Produced';
                       }
                   }
                   if ($key != 'relationshipId' && $key != 'productType' && $key != 'isEcomEnabled' && $key != 'isEnable'){
                       $normalText = $this->camelCaseToNormalText($key);
                       $data .= "<p style='margin: 0'>  $normalText  : $value </p>";
                   }
               }
            }
        }

        if (!empty($passport)){
            $data .= '<h5> Passport </h5>';
            foreach ($passport as $key => $row){
                if ($key != 'customerId'){
                    $value = !is_array($row) ? $row : '';
                    $normalText = $this->camelCaseToNormalText($key);
                    $data .= "<p style='margin: 0'>  $normalText  : $value </p>";
                }
            }
        }

        if (!empty($CQuota)){
            $data .= '<h5> Current Year Travel Quota </h5>';
            foreach ($CQuota as $key => $row){
                if ($key != 'threshodLimit' && $key != 'remainingLimitAmount' && $key != 'ecomIsActive' && $key != 'ecomThrActive' && $key != 'isActive' && $key != 'limitAmount'){
                    $value = !is_array($row) ? $row : '';
                    $normalText = $this->camelCaseToNormalText($key);
                    $data .= "<p style='margin: 0'>  $normalText  : $value </p>";
                }
            }
        }

        if (!empty($NQuota)){
            $data .= '<h4> Next Year Travel Quota </h4>';
            foreach ($NQuota as $key => $row){
                if ($key != 'threshodLimit' && $key != 'remainingLimitAmount' && $key != 'ecomIsActive' && $key != 'ecomThrActive' && $key != 'isActive' && $key != 'limitAmount'){
                    $value = !is_array($row) ? $row : '';
                    $normalText = $this->camelCaseToNormalText($key);
                    $data .= "<p style='margin: 0'>  $normalText  : $value </p>";
                }
            }
        }

        if (!empty($MQQuota)){
            $data .= '<h4> Medical Quota </h4>';
            foreach ($MQQuota as $key => $row){
                if ($key != 'threshodLimit' && $key != 'remainingLimitAmount' && $key != 'ecomIsActive' && $key != 'ecomThrActive' && $key != 'isActive' && $key != 'limitAmount'){
                    $value = !is_array($row) ? $row : '';
                    $normalText = $this->camelCaseToNormalText($key);
                    $data .= "<p style='margin: 0'>  $normalText  : $value </p>";
                }
            }
        }

        return response()->json(['data' => $data]);
    }

    public function setWorkflow($id = null)
    {
        $title = "Set IRIS Workflow";
        $title_for_layout = 'Set IRIS Workflow';

        $tmpIssueItems = DB::table('issue_workflows')
            ->select('issue_workflows.issue_workflow_id', 'unit_items.id', 'unit_items.master_id', 'unit_items.name', 'issue_categories.name AS issue_cat_name')
            ->leftJoin('unit_items', 'unit_items.id', '=', 'issue_workflows.issue_id')
            ->leftJoin('issue_categories', 'issue_categories.id', '=', 'unit_items.issue_categories_id')
            ->where('unit_items.id', $id)
            ->orderBy('issue_categories.name', 'DESC')
            ->orderBy('unit_items.name', 'ASC')
            ->get();

        if (!empty($tmpIssueItems[0])) {
            $issueItems = $tmpIssueItems[0];
        } else {
            $issueItems = '';
            abort(403, 'Un-Authorize Access');
        }

        $wFlowNextLevelGroup = DB::table('issue_workflows')
            ->select(
                'issue_group_workflows.group_info_id',
                'group_info.name AS group_name',
                'group_info.group_level_id',
                DB::raw("CASE
            WHEN group_info.group_level_id = 1 THEN 'Touch Group'
            WHEN group_info.group_level_id = 0 THEN 'Next Level Group'
            ELSE 'Invalid'
            END AS group_level")
            )
            ->leftJoin('issue_group_workflows', 'issue_group_workflows.issue_workflow_id', '=', 'issue_workflows.issue_workflow_id')
            ->leftJoin('group_info', 'group_info.id', '=', 'issue_group_workflows.group_info_id')
            ->where('issue_workflows.issue_id', $id)
            ->orderBy('issue_group_workflows.issue_group_workflow_id', 'ASC')
            ->get();

        $issue_id = encrypt($id);

        return view('IRIS.iris_workflow_set', compact('issueItems', 'wFlowNextLevelGroup', 'issue_id', 'title', 'title_for_layout'));
    }

    public function UpdateSetWorkflow(Request $request, $id = null)
    {
        try {
            $id = decrypt($id);
        } catch (DecryptException $e) {
            abort(403, 'Un-Authorize Access!!!');
        }

        if ($request->isMethod('post')) {
            $group_info_id = $request->newdata;
            if (!empty($group_info_id)) {
                IRISWorkflow::where('issue_id', $id)->delete();
                foreach ($group_info_id as $group_info) {
                    $modelName = new IRISWorkflow();
                    $modelName->issue_id = $id;
                    $modelName->group_info_id = $group_info['group_info_id'];
                    $modelName->status = !empty($group_info['status']) ? 1 : 0;
                    $modelName->is_inquiry = !empty($group_info['is_inquiry']) ? 1 : 0;
                    $modelName->save();
                }
                flash('IRIS Workflow has been updated successfully', 'success');
                return redirect('/Issues/');
            } else {
                flash('Failed to update data', 'danger');
                return redirect()->back();
            }
        }
    }

    public function IRISApiRequestResponse($params = array())
    {
        /*type Credit Card = 1, Account = 2, Debit Card = 3, Loan = 4, Trade = 5 */
        $modelName = new IRISApiResponses;
        $modelName->service = (!empty($params['service'])) ? $params['service'] : 0;
        $modelName->endpoint = (!empty($params['endpoint'])) ? $params['endpoint'] : '';
        $modelName->reference_number = (!empty($params['reference_number'])) ? $params['reference_number'] : '';
        $modelName->account_number = (!empty($params['account_number'])) ? $params['account_number'] : '';
        $modelName->card_number = (!empty($params['card_number'])) ? $params['card_number'] : '';
        $modelName->type = (!empty($params['type'])) ? $params['type'] : '';
        $modelName->json_node = (!empty($params['json_node'])) ? $params['json_node'] : '';
        $modelName->log_user = (!empty(Auth::user()->user_id)) ? Auth::user()->user_id : '';
        $modelName->status_code = (!empty($params['status_code'])) ? $params['status_code'] : '';
        $modelName->global_transaction_id = (!empty($params['global_transaction_id'])) ? $params['global_transaction_id'] : '';
        $modelName->status_msg = (!empty($params['status_msg'])) ? $params['status_msg'] : '';
        $modelName->execution_time = (!empty($params['execution_time'])) ? $params['execution_time'] : 0;
        $modelName->save();
    }

    public function audit($params = array())
    {
        $params['duration_in_minutes'] = (!empty($params['duration_in_minutes'])) ? $params['duration_in_minutes'] : 0;

        if (!empty($params['form_load'])) {
            $formLoadTimeObj = new DateTime($params['form_load']);
            $currentTimeObj = new DateTime(date('Y-m-d H:i:s'));
            $interval = $formLoadTimeObj->diff($currentTimeObj);

            $hoursPenalty = $interval->format('%h');
            $minutesPenalty = $interval->format('%i');

            $totalPenalty = ($hoursPenalty * 60) + $minutesPenalty;

            $params['duration_in_minutes'] = $params['duration_in_minutes'] + $totalPenalty;

        }

        $commentModel = new Comment;
        $commentModel->reference_number = (!empty($params['reference_number'])) ? $params['reference_number'] : '';
        $commentModel->comments = (!empty($params['comments'])) ? $params['comments'] : '';
        $commentModel->time = strtotime(date('Y-m-d H:i:s'));
        $commentModel->user_id = (!empty($params['user_id'])) ? $params['user_id'] : 0;
        $commentModel->unit_id = (!empty($params['unit_id'])) ? $params['unit_id'] : 0;
        $commentModel->group_id = (!empty($params['group_id'])) ? $params['group_id'] : 0;
        $commentModel->action = (!empty($params['action'])) ? $params['action'] : 'INVALID';
        $commentModel->duration_in_minutes = $params['duration_in_minutes'];
        $commentModel->isapproved = (!empty($params['isapproved'])) ? $params['isapproved'] : 0;
        $commentModel->issendback = (!empty($params['issendback'])) ? $params['issendback'] : 0;
        $commentModel->subgroup_id = (!empty($params['subgroup_id'])) ? $params['subgroup_id'] : 0;
        $commentModel->ip = $this->getClientIp();
        $commentModel->save();
    }

    function camelCaseToNormalText($camelCaseText) {
        $normalText = trim(preg_replace('/([a-z])([A-Z])/', '$1 $2', $camelCaseText));
        return ucfirst($normalText);
    }

}
