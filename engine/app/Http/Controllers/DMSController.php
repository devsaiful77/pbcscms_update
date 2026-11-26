<?php

namespace App\Http\Controllers;

use App\DMSRequestResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use App\Reference;
use App\WForm;
use App\Complaint;

use App\DMSCustMapping;
use App\DMSAttachMapping;
use App\DMSAttachUrl;

use Storage;
use SimpleXMLElement;
use Illuminate\Contracts\Encryption\DecryptException;

class DMSController extends Controller
{
    //requestResponsePayloadDMS type 1 = DMS API Request, 2 = DMS API Response,
    public function __construct()
    {
        parent::__construct();
        $this->middleware(['role_or_permission:superadmin|admin|accessDMSUrl'])
            ->only(['DMSUrlList','DMSUrlAdd','DMSUrlStore','DMSUrlEdit','DMSUrlUpdate','DMSUrlStatus']);
    }
    // form_status = 11 & days = -2
    //CIF Indexing
    public function CIFIndexing()
    {
        $dmsAttachUrl = DMSAttachUrl::where('status', 1)->select('name','url','request')->get();

        $referenceModel = new Reference();
        $referenceDataArr = $referenceModel
            ->select(
                'reference.reference_number'
                , DB::raw('IFNULL(w_form.product_type,complaint.product_type) AS product_type')
                , DB::raw('IFNULL(w_form.SIF_Number,complaint.SIF_Number) AS cif_number')
                , DB::raw('IFNULL(w_form.segment,complaint.segment) AS segment')
                , DB::raw('IFNULL(dmscustwform.status,dmscustcomplaint.status) AS mappingstatus')
            )
            ->leftJoin('w_form','w_form.reference_number','reference.reference_number')
            ->leftJoin('complaint','complaint.reference_number','reference.reference_number')
            ->leftJoin('dms_cust_mappings AS dmscustwform','dmscustwform.cif_number','w_form.SIF_Number')
            ->leftJoin('dms_cust_mappings AS dmscustcomplaint','dmscustcomplaint.cif_number','complaint.SIF_Number')
            ->leftJoin('attachments','attachments.reference_number','reference.reference_number')
            // ->where('attachments.attachment_date',">=", date('Y-m-d',strtotime("-180 days")))
            ->where(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"),">=", date('Y-m-d',strtotime("-2 days")))
            ->where('reference.form_status',"=", 11)
            ->where(function($q) {
                $q->where('w_form.SIF_Number','<>',null)
                    ->orWhere('complaint.SIF_Number','<>',null);
                }
            )
            /*->where(function($r) {
                $r->where('dmscustwform.status','<>',1)
                  ->orWhere('dmscustcomplaint.status','<>',1);
            })*/
            ->get()
            ->groupBy(['cif_number','product_type'])
            ->toArray();

        if(!empty($referenceDataArr)) {
            foreach($referenceDataArr AS $cifKey=>$refDatArr) {
                $code = '';
                $msg = '';
                $cifIdx = '';
                /********* Search in DB by CIF number for CIF Index *****/
                $dmsCustModel = new DMSCustMapping();
                $dmsCustDataObj = $dmsCustModel->where('cif_number', $cifKey)->first();
                if (!empty($dmsCustDataObj->cif_index)) {
                    $cifIdx = $dmsCustDataObj->cif_index;
                } else {
                    /*********
                    If CIF Index not found in DB then
                    Search CIF Index by API
                    If Not Exists Then Create CIF Index by API
                    Store `dms_cust_mappings` from response
                    *********/
                    // Search CIF Index by CIF no
                    $dms = $dmsAttachUrl->where('name', 'SearchFolder')->first();
                    $url = $dms->url;
                    $req = $dms->request;
                    $today = \Carbon\Carbon::now()->format('YmdHisv');
                    $sourceTimestamp = \Carbon\Carbon::now()->format('Y-m-d\TH:i:s.v');
                    $global_transaction_id = 'AYS'.$today;
                    $LookInFolder = 438;
                    $cifIdx = null;

                    $req = str_replace("#name#", $cifKey, $req);
                    $req = str_replace("#global_transaction_id#", $global_transaction_id, $req);
                    $req = str_replace("#sourceTimestamp#", $sourceTimestamp, $req);
                    $req = str_replace("#LookInFolder#", $LookInFolder, $req);

                    $this->requestResponsePayloadDMS(['type' => 1, 'json_node' => $req, 'url' => $url, 'global_transaction_id' => $global_transaction_id, 'cif_number' => $cifKey]);

                    $msc = microtime(true);
                    $response = $this->DMSCurl($req, $url);
                    $msc = microtime(true) - $msc;
                    $execution_time = number_format($msc,2);

                    if(!empty($response)) {
                        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
                        $xml = new SimpleXMLElement($response);
                        $body = $xml->xpath('//soapenvBody')[0];
                        $array = json_decode(json_encode((array)$body), TRUE);
                        $array = array_flatten($array);
                        foreach ($array as $key => $value) {
                            $ke = str_ireplace(['v11','v1','v2','ns1','ns2','ns4'], '', $key);
                            if ($ke == 'responseCode') {
                                $code = $value;
                            }
                            if ($ke == 'responseMessage') {
                                $msg = $value;
                            }
                            if ($code == '000') {
                                if ($ke == 'FolderIndex') {
                                    $cifIdx = $value;
                                }
                            }
                        }
                    }
                    $this->requestResponsePayloadDMS(['type' => 2, 'json_node' => $response, 'url' => $url, 'global_transaction_id' => $global_transaction_id, 'execution_time' => $execution_time, 'cif_number' => $cifKey, 'code' => $code, 'msg' => $msg ]);
                    // If CIF Index not found than
                    if(empty($cifIdx)){
                        // Add CIF Folder & get CIF Index from response
                        $dms = $dmsAttachUrl->where('name', 'AddFolder')->first();
                        $url = $dms->url;
                        $req = $dms->request;
                        $ParentFolderIndex = 438;
                        $cifIdx = null;

                        $today = \Carbon\Carbon::now()->format('YmdHisv');
                        $sourceTimestamp = \Carbon\Carbon::now()->format('Y-m-d\TH:i:s.v');
                        $global_transaction_id = 'AYS'.$today;

                        $req = str_replace("#folder_name#", $cifKey, $req);
                        $req = str_replace("#global_transaction_id#", $global_transaction_id, $req);
                        $req = str_replace("#sourceTimestamp#", $sourceTimestamp, $req);
                        $req = str_replace("#ParentFolderIndex#", $ParentFolderIndex, $req);

                        $this->requestResponsePayloadDMS(['type' => 1, 'json_node' => $req, 'url' => $url, 'global_transaction_id' => $global_transaction_id,  'cif_number' => $cifKey]);

                        $msc = microtime(true);
                        $response = $this->DMSCurl($req, $url);
                        $msc = microtime(true) - $msc;
                        $execution_time = number_format($msc,2);

                        if(!empty($response)) {
                            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
                            $xml = new SimpleXMLElement($response);
                            $body = $xml->xpath('//soapenvBody')[0];
                            $array = json_decode(json_encode((array)$body), TRUE);
                            $array = array_flatten($array);
                            foreach ($array as $key => $value) {
                                $ke = str_ireplace(['v11','v1','v2','ns1','ns2','ns4'], '', $key);
                                if ($ke == 'responseCode') {
                                    $code = $value;
                                }
                                if ($ke == 'responseMessage') {
                                    $msg = $value;
                                }
                                if ($code == '000') {
                                    if ($ke == 'FolderIndex') {
                                        $cifIdx = $value;
                                    }
                                }
                            }
                        }
                        $this->requestResponsePayloadDMS(['type' => 2, 'json_node' => $response, 'url' => $url, 'global_transaction_id' => $global_transaction_id,  'execution_time' => $execution_time, 'cif_number' => $cifKey, 'code' => $code, 'msg' => $msg ]);
                    }
                    $dmsCustModel = new DMSCustMapping();
                    $dmsCustDataObj = $dmsCustModel->where('cif_number', $cifKey)->first();

                    if (empty($dmsCustDataObj)) {
                        $dmsCustDataObj = new DMSCustMapping();
                    }
                    $dmsCustDataObj->cif_number = $cifKey;
                    $dmsCustDataObj->cif_index = $cifIdx;
                    $dmsCustDataObj->save();
                }
                foreach($refDatArr AS $prodKey=>$prodDatArr) {
                    /*********
                    Search Product Type Index by API
                    If Not Exists Then Create Product Type Index by API
                    Update `dms_cust_mappings` from response

                    # CC Index
                    Credit Card -->1

                    # CASA Index
                    Account -->2
                    Debit Card -->3

                    # Loan Index
                    SME Loan Index --> 4 with Segment Code 9
                    Retail Loan Index --> 4 with Segment Code 3
                    Corporate Loan Index --> 4 with Segment Code 4

                    # Trade Index
                    *********/

                    if(!empty($cifIdx)){
                        $ccidx = '';
                        $casaidx = '';
                        $smeloanidx = '';
                        $retailloanidx = '';
                        $corporateloanidx = '';

                        $ser = $dmsAttachUrl->where('name', 'SearchFolder')->first();
                        $serUrl = $ser->url;
                        $serReq = $ser->request;

                        $add = $dmsAttachUrl->where('name', 'AddFolder')->first();
                        $addUrl = $add->url;
                        $addReq = $add->request;

                        if ($prodKey == 1) {
                            foreach($prodDatArr AS $dataArr) {
                                $reference_number = $dataArr['reference_number'];
                                $folder_name = 'Credit Card';
                                // Search Credit Card Folder for CC Index
                                if (empty($dmsCustDataObj->cc_index)) {
                                    $ccidx = $this->searchSubFolder($serUrl, $serReq, $cifIdx, $folder_name, $reference_number, $cifKey);
                                    // If Credit Card Index not found
                                    if (empty($ccidx)) {
                                        // Add Credit Card Folder & get CC Index
                                        $ccidx = $this->addSubFolder($addUrl, $addReq, $cifIdx, $folder_name, $reference_number, $cifKey);
                                    }
                                    $dmsCustDataObj->cc_index = $ccidx;
                                }
                            }
                        }
                        if ($prodKey == 2 || $prodKey == 3) {
                            foreach($prodDatArr AS $dataArr) {
                                $reference_number = $dataArr['reference_number'];
                                $folder_name = 'CASA';
                                // Search CASA Folder for CASA Index
                                if (empty($dmsCustDataObj->casa_index)) {
                                    $casaidx = $this->searchSubFolder($serUrl, $serReq, $cifIdx, $folder_name, $reference_number, $cifKey);
                                    // If CASA Index not found
                                    if (empty($casaidx)) {
                                        // Add CASA Folder & get CASA Index
                                        $casaidx = $this->addSubFolder($addUrl, $addReq, $cifIdx, $folder_name, $reference_number, $cifKey);
                                    }
                                    $dmsCustDataObj->casa_index = $casaidx;
                                }
                            }
                        }
                        if ($prodKey == 4) {
                            foreach($prodDatArr AS $dataArr) {
                                $reference_number = $dataArr['reference_number'];
                                $segment = $dataArr['segment'];
                                $segment = substr($segment, 0, 1);
                                if ($segment == 9) {
                                    $folder_name = 'SME Loans';
                                    if (empty($dmsCustDataObj->sme_loan_index)) {
                                        // Search SME Loan Folder for SME Index
                                        $smeloanidx = $this->searchSubFolder($serUrl, $serReq, $cifIdx, $folder_name, $reference_number, $cifKey);
                                        // If SME Loan Index not found
                                        if(empty($smeloanidx)){
                                            // Add SME Loan Folder & get SME Index
                                            $smeloanidx = $this->addSubFolder($addUrl, $addReq, $cifIdx, $folder_name, $reference_number, $cifKey);
                                        }
                                        $dmsCustDataObj->sme_loan_index = $smeloanidx;
                                    }
                                } elseif ($segment == 3) {
                                    // Search Retail Loan Folder for Retail Index
                                    $folder_name = 'Retail Loans';
                                    if (empty($dmsCustDataObj->retail_loan_index)) {
                                        $retailloanidx = $this->searchSubFolder($serUrl, $serReq, $cifIdx, $folder_name, $reference_number, $cifKey);
                                        // If Retail Loan Index not found
                                        if(empty($retailloanidx)){
                                            // Add Retail Loan Folder & get SME Index
                                            $retailloanidx = $this->addSubFolder($addUrl, $addReq, $cifIdx, $folder_name, $reference_number, $cifKey);
                                        }
                                        $dmsCustDataObj->retail_loan_index = $retailloanidx;
                                    }
                                } elseif ($segment == 4) {
                                    // Search Corporate Loan Folder for Corporate Index
                                    $folder_name = 'Corporate Loans';
                                    if (empty($dmsCustDataObj->corporate_loan_index)) {
                                        $corporateloanidx = $this->searchSubFolder($serUrl, $serReq, $cifIdx, $folder_name, $reference_number, $cifKey);
                                        // If Corporate Loan Index not found
                                        if(empty($corporateloanidx)){
                                            // Add Corporate Loan Folder & get SME Index
                                            $corporateloanidx = $this->addSubFolder($addUrl, $addReq, $cifIdx, $folder_name, $reference_number, $cifKey);
                                        }
                                        $dmsCustDataObj->corporate_loan_index = $corporateloanidx;
                                    }
                                }
                            }
                        }
                        $dmsCustDataObj->save();
                    }
                }
            }
        }
    }

    //Document Counting
    public function DocCounting()
    {
        $referenceModel = new Reference();
        $referenceDataArr = $referenceModel
            ->select(
                'reference.reference_number'
                , DB::raw('IFNULL(w_form.product_type,complaint.product_type) AS product_type')
                , DB::raw('IFNULL(w_form.SIF_Number,complaint.SIF_Number) AS cif_number')
                , DB::raw('IFNULL(w_form.account_number,complaint.account_number) AS account_number')
                , DB::raw('IFNULL(w_form.mobile_number,complaint.mobile_number) AS mobile_number')
                , DB::raw('IFNULL(dmscustwform.cif_index,dmscustcomplaint.cif_index) AS cif_index')
                , DB::raw('IFNULL(dmscustwform.cc_index,dmscustcomplaint.cc_index) AS cc_index')
                , DB::raw('IFNULL(dmscustwform.casa_index,dmscustcomplaint.casa_index) AS casa_index')
                , DB::raw('IFNULL(dmscustwform.sme_loan_index,dmscustcomplaint.sme_loan_index) AS sme_loan_index')
                , DB::raw('IFNULL(dmscustwform.retail_loan_index,dmscustcomplaint.retail_loan_index) AS retail_loan_index')
                , DB::raw('IFNULL(dmscustwform.corporate_loan_index,dmscustcomplaint.corporate_loan_index) AS corporate_loan_index')
                , DB::raw('IFNULL(w_form.segment,complaint.segment) AS segment')
                ,'attachments.id AS attachment_id'
                ,'attachments.file_name'
            )
            ->leftJoin('w_form','w_form.reference_number','reference.reference_number')
            ->leftJoin('complaint','complaint.reference_number','reference.reference_number')
            ->leftJoin('attachments','attachments.reference_number','reference.reference_number')
            ->leftJoin('dms_attach_mapping','dms_attach_mapping.attachment_id','attachments.id')
            ->leftJoin('dms_cust_mappings AS dmscustwform','dmscustwform.cif_number','w_form.SIF_Number')
            ->leftJoin('dms_cust_mappings AS dmscustcomplaint','dmscustcomplaint.cif_number','complaint.SIF_Number')
            ->where(function($q) {
                $q->where('w_form.SIF_Number','<>',null)
                  ->orWhere('complaint.SIF_Number','<>',null);
            })
            ->where(function($q) {
                $q->whereNull('dms_attach_mapping.id')
                  ->orWhere('dms_attach_mapping.status', "=", 0);
            })
            ->where('reference.form_status',"=", 11)
            ->where(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"),">=", date('Y-m-d',strtotime("-2 days")))
            ->where('attachments.file_name','<>',null)
            ->count();
        if(!empty($referenceDataArr)) {
            if($referenceDataArr > 100) {
                $no = $referenceDataArr / 100;
                for($n = 1; $n <= $no; $n++) {
                    $this->DOCUploading();
                }
            } else {
                $this->DOCUploading();
            }
        }
    }

    //Document Uploading
    public function DOCUploading()
    {
        $code = '';
        $msg = '';
        $extraNode = '';

        $dmsAttachUrl = DMSAttachUrl::where('name', 'AddDocument')->first();
        $referenceModel = new Reference();
        $referenceDataArr = $referenceModel
            ->select(
                'reference.reference_number'
                , DB::raw('IFNULL(w_form.product_type,complaint.product_type) AS product_type')
                , DB::raw('IFNULL(w_form.SIF_Number,complaint.SIF_Number) AS cif_number')
                , DB::raw('IFNULL(w_form.account_number,complaint.account_number) AS account_number')
                , DB::raw('IFNULL(w_form.mobile_number,complaint.mobile_number) AS mobile_number')
                , DB::raw('IFNULL(dmscustwform.cif_index,dmscustcomplaint.cif_index) AS cif_index')
                , DB::raw('IFNULL(dmscustwform.cc_index,dmscustcomplaint.cc_index) AS cc_index')
                , DB::raw('IFNULL(dmscustwform.casa_index,dmscustcomplaint.casa_index) AS casa_index')
                , DB::raw('IFNULL(dmscustwform.sme_loan_index,dmscustcomplaint.sme_loan_index) AS sme_loan_index')
                , DB::raw('IFNULL(dmscustwform.retail_loan_index,dmscustcomplaint.retail_loan_index) AS retail_loan_index')
                , DB::raw('IFNULL(dmscustwform.corporate_loan_index,dmscustcomplaint.corporate_loan_index) AS corporate_loan_index')
                , DB::raw('IFNULL(w_form.segment,complaint.segment) AS segment')
                ,'attachments.id AS attachment_id'
                ,'attachments.file_name'
            )
            ->leftJoin('w_form','w_form.reference_number','reference.reference_number')
            ->leftJoin('complaint','complaint.reference_number','reference.reference_number')
            ->leftJoin('attachments','attachments.reference_number','reference.reference_number')
            ->leftJoin('dms_attach_mapping','dms_attach_mapping.attachment_id','attachments.id')
            ->leftJoin('dms_cust_mappings AS dmscustwform','dmscustwform.cif_number','w_form.SIF_Number')
            ->leftJoin('dms_cust_mappings AS dmscustcomplaint','dmscustcomplaint.cif_number','complaint.SIF_Number')
            ->where(function($q) {
                $q->where('w_form.SIF_Number','<>',null)
                  ->orWhere('complaint.SIF_Number','<>',null);
            })
            ->where(function($q) {
                $q->whereNull('dms_attach_mapping.id')
                  ->orWhere('dms_attach_mapping.status',"=", 0);
            })
            ->where('reference.form_status',"=", 11)
            ->where(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"),">=", date('Y-m-d',strtotime("-2 days")))
            ->where('attachments.file_name','<>',null)
            ->limit(100)
            ->get()
            ->toArray();

        if(!empty($referenceDataArr)) {
            foreach($referenceDataArr AS $key=>$refDatArr) {
                $reference_number = $refDatArr['reference_number'];
                $product_type = $refDatArr['product_type'];
                $cif_number = $refDatArr['cif_number'];
                $cif_index = $refDatArr['cif_index'];
                $segment = $refDatArr['segment'];
                $file_name = $refDatArr['file_name'];
                $attachment_id = $refDatArr['attachment_id'];
                $accno = $refDatArr['account_number'];
                $mblno = $refDatArr['mobile_number'];

                $cc_index = $refDatArr['cc_index'];
                $casa_index = $refDatArr['casa_index'];
                $sme_loan_index = $refDatArr['sme_loan_index'];
                $retail_loan_index = $refDatArr['retail_loan_index'];
                $corporate_loan_index = $refDatArr['corporate_loan_index'];

                $dmsAttachmentModel = new DMSAttachMapping();
                $dmsAttachmentModel->reference_number = $reference_number;
                $dmsAttachmentModel->cif_number = $cif_number;
                $dmsAttachmentModel->cif_index = $cif_index;
                $dmsAttachmentModel->attachment_id = $attachment_id;

                if ($product_type == 1) {
                    $smart_index = $cc_index;
                    $dataDefName = "CreditCard";
                    $dataDefIndex = 82;
                    $cif_id = 88;
                    $acc_id = 87;
                    $mbl_id = 90;
                    $extraNode = '<v1:NGOAddDocDataDefCriteriaDataBDO>
                                    <v1:indexId>' . $mbl_id . '</v1:indexId>
                                    <v1:indexType>S</v1:indexType>
                                    <v1:indexValue>' . $mblno . '</v1:indexValue>
                                </v1:NGOAddDocDataDefCriteriaDataBDO>';
                }
                if ($product_type == 2 || $product_type == 3) {
                    $smart_index = $casa_index;
                    $dataDefName = "CASA";
                    $dataDefIndex = 102;
                    $cif_id = 126;
                    $acc_id = 127;
                    $mbl_id = 125;
                    $extraNode = '<v1:NGOAddDocDataDefCriteriaDataBDO>
                                    <v1:indexId>' . $mbl_id . '</v1:indexId>
                                    <v1:indexType>S</v1:indexType>
                                    <v1:indexValue>' . $mblno . '</v1:indexValue>
                                </v1:NGOAddDocDataDefCriteriaDataBDO>';
                }
                if ($product_type == 4) {
                    $segment = substr($segment, 0, 1);
                    if ($segment == 9) {
                        $smart_index = $sme_loan_index;
                        $dataDefName = "SMELoan";
                        $dataDefIndex = 42;
                        $cif_id = 43;
                        $acc_id = 47;
                        $mbl_id = '';
                        $mblno = '';
                    } elseif ($segment == 3) {
                        $smart_index = $retail_loan_index;
                        $dataDefName = "RetailLoan";
                        $dataDefIndex = 362;
                        $cif_id = 1068;
                        $acc_id = 1069;
                        $mbl_id = '';
                        $mblno = '';
                    } elseif ($segment == 4) {
                        $smart_index = $corporate_loan_index;
                        $dataDefName = "Corporate_Loan";
                        $dataDefIndex = 162;
                        $cif_id = 472;
                        $acc_id = 503;
                        $mbl_id = '';
                        $mblno = '';
                    }
                    $extraNode = '';
                }
                $file_content = '';
                $basePath = str_replace('engine','',base_path());
                $fileURL = $basePath.'public/attachments/'.$file_name;
                if(file_exists($fileURL)){
                    $file_content = base64_encode(file_get_contents($fileURL));
                }
                $createdByAppName = pathinfo($file_name, PATHINFO_EXTENSION);
                $url = $dmsAttachUrl->url;
                $xml_post_string = $dmsAttachUrl->request;
                $today = \Carbon\Carbon::now()->format('YmdHisv');
                $sourceTimestamp = \Carbon\Carbon::now()->format('Y-m-d\TH:i:s.v');
                $global_transaction_id = 'AYS'.$today;

                $xml_post_string = str_replace("#folder_index#", $smart_index, $xml_post_string);
                $xml_post_string = str_replace("#document_name#", $file_name, $xml_post_string);
                $xml_post_string = str_replace("#global_transaction_id#", $global_transaction_id, $xml_post_string);
                $xml_post_string = str_replace("#sourceTimestamp#", $sourceTimestamp, $xml_post_string);
                $xml_post_string = str_replace("#file_content#", $file_content, $xml_post_string);
                $xml_post_string = str_replace("#dataDefName#", $dataDefName, $xml_post_string);
                $xml_post_string = str_replace("#dataDefIndex#", $dataDefIndex, $xml_post_string);
                $xml_post_string = str_replace("#cif_number#", $cif_number, $xml_post_string);
                $xml_post_string = str_replace("#createdByAppName#", $createdByAppName, $xml_post_string);
                $xml_post_string = str_replace("#cif_id#", $cif_id, $xml_post_string);
                $xml_post_string = str_replace("#acc_id#", $acc_id, $xml_post_string);
                $xml_post_string = str_replace("#account_no#", $accno, $xml_post_string);
                $xml_post_string = str_replace("#mobile_no#", $mblno, $xml_post_string);
                $xml_post_string = str_replace("#mobile_id#", $mbl_id, $xml_post_string);
                $xml_post_string = str_replace("#reference_number#", 'AYS - '.$reference_number, $xml_post_string);
                $extraNode = trim(preg_replace('/\s\s+/', ' ', $extraNode));
                $xml_post_string = str_replace("<extraNode>extraNode</extraNode>", $extraNode, $xml_post_string);

                $this->requestResponsePayloadDMS(['reference_number' => $reference_number, 'type' => 1, 'json_node' => $xml_post_string, 'cif_number' => $cif_number, 'url' => $url, 'global_transaction_id' => $global_transaction_id]);
                $msc = microtime(true);
                $response = $this->DMSCurl($xml_post_string, $url);
                $msc = microtime(true) - $msc;
                $execution_time = number_format($msc,2);

                if(!empty($response)) {
                    $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
                    $xml = new SimpleXMLElement($response);
                    $body = $xml->xpath('//soapenvBody')[0];
                    $array = json_decode(json_encode((array)$body), TRUE);
                    $array = array_flatten($array);
                    foreach ($array as $key => $value) {
                        $ke = str_ireplace(['v11','v1','v2','ns1','ns2','ns4'], '', $key);
                        if ($ke == 'documentIndex') {
                            $doc_index = $value;
                        } else {
                            $doc_index = null;
                        }
                        if ($ke == 'responseCode') {
                            $code = $value;
                        }
                        if ($ke == 'responseMessage') {
                            $msg = $value;
                        }
                    }
                }
                $this->requestResponsePayloadDMS(['reference_number' => $reference_number, 'attachment_id' => $attachment_id, 'type' => 2, 'json_node' => $response, 'execution_time' => $execution_time, 'cif_number' => $cif_number, 'url' => $url, 'global_transaction_id' => $global_transaction_id, 'code' => $code, 'msg' => $msg ]);
                if ($code == '000') {
                    $dmsAttachmentModel->status	= 1;
                    $dmsAttachmentModel->doc_index = $doc_index;
                } else {
                    $dmsAttachmentModel->status	= 0;
                    $dmsAttachmentModel->doc_index = null;
                }
                $dmsAttachmentModel->save();
            }
        }
    }

    //DMS search CIF sub folder
    public function searchSubFolder($url, $req, $cifIdx, $folder_name, $reference_number, $cifKey)
    {
        $index = '';
        $code = '';
        $msg = '';
        $today = \Carbon\Carbon::now()->format('YmdHisv');
        $sourceTimestamp = \Carbon\Carbon::now()->format('Y-m-d\TH:i:s.v');
        $global_transaction_id = 'AYS'.$today;

        $req = str_replace("#LookInFolder#", $cifIdx, $req);
        $req = str_replace("#name#", $folder_name, $req);
        $req = str_replace("#global_transaction_id#", $global_transaction_id, $req);
        $req = str_replace("#sourceTimestamp#", $sourceTimestamp, $req);

        $this->requestResponsePayloadDMS(['reference_number' => $reference_number, 'type' => 1, 'json_node' => $req, 'cif_number' => $cifKey, 'url' => $url, 'global_transaction_id' => $global_transaction_id]);
        $msc = microtime(true);
        $response = $this->DMSCurl($req, $url);
        $msc = microtime(true) - $msc;
        $execution_time = number_format($msc,2);

        if(!empty($response)) {
            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
            $xml = new SimpleXMLElement($response);
            $body = $xml->xpath('//soapenvBody')[0];
            $array = json_decode(json_encode((array)$body), TRUE);
            $array = array_flatten($array);
            foreach ($array as $key => $value) {
                $ke = str_ireplace(['v11','v1','v2','ns1','ns2','ns4'], '', $key);
                if ($ke == 'responseCode') {
                    $code = $value;
                }
                if ($ke == 'responseMessage') {
                    $msg = $value;
                }
                if ($ke == 'FolderIndex') {
                    if ($code == '000') {
                        $index = $value;
                    }
                }
            }
        }
        $this->requestResponsePayloadDMS(['reference_number' => $reference_number, 'type' => 2, 'json_node' => $response, 'execution_time' => $execution_time, 'cif_number' => $cifKey, 'url' => $url, 'global_transaction_id' => $global_transaction_id, 'code' => $code, 'msg' => $msg ]);
        return $index;
    }

    //DMS add CIF sub folder
    public function addSubFolder($url, $req, $cifIdx, $folder_name, $reference_number, $cifKey)
    {
        $index = '';
        $code = '';
        $msg = '';
        $today = \Carbon\Carbon::now()->format('YmdHisv');
        $sourceTimestamp = \Carbon\Carbon::now()->format('Y-m-d\TH:i:s.v');
        $global_transaction_id = 'AYS'.$today;

        $req = str_replace("#folder_name#", $folder_name, $req);
        $req = str_replace("#ParentFolderIndex#", $cifIdx, $req);
        $req = str_replace("#global_transaction_id#", $global_transaction_id, $req);
        $req = str_replace("#sourceTimestamp#", $sourceTimestamp, $req);

        $this->requestResponsePayloadDMS(['reference_number' => $reference_number, 'type' => 1, 'json_node' => $req, 'cif_number' => $cifKey, 'url' => $url, 'global_transaction_id' => $global_transaction_id]);
        $msc = microtime(true);
        $response = $this->DMSCurl($req, $url);
        $msc = microtime(true) - $msc;
        $execution_time = number_format($msc,2);

        if(!empty($response)) {
            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
            $xml = new SimpleXMLElement($response);
            $body = $xml->xpath('//soapenvBody')[0];
            $array = json_decode(json_encode((array)$body), TRUE);
            $array = array_flatten($array);
            foreach ($array as $key => $value) {
                $ke = str_ireplace(['v11','v1','v2','ns1','ns2','ns4'], '', $key);
                if ($ke == 'responseCode') {
                    $code = $value;
                }
                if ($ke == 'responseMessage') {
                    $msg = $value;
                }
                if ($ke == 'FolderIndex') {
                    if ($code == '000') {
                        $index = $value;
                    }
                }
            }
        }
        $this->requestResponsePayloadDMS(['reference_number' => $reference_number, 'type' => 2, 'json_node' => $response, 'execution_time' => $execution_time, 'cif_number' => $cifKey, 'url' => $url, 'global_transaction_id' => $global_transaction_id, 'code' => $code, 'msg' => $msg ]);
        return $index;
    }

    //DMS CURL API call
    public function DMSCurl($request, $url)
    {
        $headers = array("Content-type: text/xml;charset=\"utf-8\"", "Accept: text/xml", "Cache-Control: no-cache", "Pragma: no-cache", "SOAPAction: DMSAPIServices", "Content-length: ".strlen($request));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_errno($ch);
        }
        curl_close($ch);
        if (isset($error_msg)) {
            //print_r($error_msg);
        }
        return $response;
    }

    //DMS API Request & Response store
    public function requestResponsePayloadDMS($params = array())
    {
        $modelName = new DMSRequestResponse;
        $modelName->reference_number = (!empty($params['reference_number'])) ? $params['reference_number'] : '';
        $modelName->attachment_id = (!empty($params['attachment_id'])) ? $params['attachment_id'] : '';
        $modelName->type = (!empty($params['type'])) ? $params['type'] : '';
        $modelName->json_node = (!empty($params['json_node'])) ? $params['json_node'] : '';
        $modelName->url = (!empty($params['url'])) ? $params['url'] : '';
        $modelName->execution_time = (!empty($params['execution_time'])) ? $params['execution_time'] : '';
        $modelName->global_transaction_id = (!empty($params['global_transaction_id'])) ? $params['global_transaction_id'] : '';
        $modelName->cif_number = (!empty($params['cif_number'])) ? $params['cif_number'] : '';
        $modelName->code = (!empty($params['code'])) ? $params['code'] : null;
        $modelName->msg = (!empty($params['msg'])) ? $params['msg'] : '';
        $modelName->save();
    }

    //Dynamic Url Response
    public function DMSUrlRequest($para, Request $request)
    {
        $dmsModel = new DMSAttachUrl();
        $dmsDataArr = $dmsModel
            ->where('name', '=', $para)
            ->where('status', '=', 1)
            ->select('name', 'url', 'request')
            ->first();
        $soapName = $dmsDataArr->name;
        $soapUrl = $dmsDataArr->url;
        $soapRequest = $dmsDataArr->request;

        $look_folder = $request->look_folder;
        $customer = $request->customer;
        $folder_index = $request->folder_index;
        $parent_folder_index = $request->parent_folder_index;
        $folder_name = $request->folder_name;
        $document_name = $request->document_name;
        $file_content = $request->file_content;

        if (!empty($soapRequest)) {
            $soapRequest = str_replace("#look_folder#", $look_folder, $soapRequest);
            $soapRequest = str_replace("#customer#", $customer, $soapRequest);
            $soapRequest = str_replace("#folder_index#", $folder_index, $soapRequest);
            $soapRequest = str_replace("#parent_folder_index#", $parent_folder_index, $soapRequest);
            $soapRequest = str_replace("#folder_name#", $folder_name, $soapRequest);
            $soapRequest = str_replace("#document_name#", $document_name, $soapRequest);
            $soapRequest = str_replace("#file_content#", $file_content, $soapRequest);
        }
        $header = array(
            "Content-type: text/soap+xml;charset=\"utf-8\";action:\"urn:$soapName\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Content-length: " . strlen($soapRequest),
        );

        $soapClient = new SoapHelper();
        $response = $soapClient->soapCall($soapUrl, $soapRequest, $header, 'Response');
        echo '<pre>';
        print_r($response);
    }

    //DMS Url CRUD start
    public function DMSUrlList()
    {
        $title = "DMS Url Request List";
        $title_for_layout = "DMS Url Request List";
        $dmsModelName = new DMSAttachUrl;
        $tblData = array();
        $dataObj = $dmsModelName
            ->select("id","name","url","request",DB::raw("CASE WHEN status = 1 THEN 'Active' WHEN status = 0 THEN 'Inactive' ELSE 'Invalid' END AS status_name"),"status")
            ->orderBy("id","DESC")
            ->get();
        if (!empty($dataObj)) {
            $tblData = $dataObj->toArray();
        }
        return view('DMSUrl.list',compact('title','title_for_layout','tblData','dataObj'));
    }

    public function DMSUrlAdd()
    {
        $title = "Add DMS Url Request";
        $title_for_layout = "Add DMS Url Request";
        return view('DMSUrl.add',compact('title','title_for_layout'));
    }

    public function DMSUrlStore(Request $request)
    {
        $this->validate($request,[
            'name'      =>  'required',
            'url'       =>  'required',
            'request'   =>  'required',
        ]);
        $dmsModelName = new DMSAttachUrl;
        if ($request->isMethod('post')) {
            $dmsModelName->name = $request->get('name');
            $dmsModelName->url = $request->get('url');
            $dmsModelName->request = $request->get('request');
            $dmsModelName->status = 1;
            if ($dmsModelName->save()) {
                flash('DMS Url has been stored successfully', 'success');
                return redirect('/DMSAPIServices/DMSUrl/list');
            } else {
                flash('Failed to insert data', 'danger');
                return redirect('/DMSAPIServices/DMSUrl/add');
            }
        }
    }

    public function DMSUrlEdit($id = null)
    {
        $title = "Edit DMS Url Request";
        $title_for_layout = 'Edit DMS Url Request';
        $dmsModelName = new DMSAttachUrl;
        $dataForView = $dmsModelName->where('id', decrypt($id))->first();
        if ($dataForView->status == 0) {
            abort(403,'Edit Not Allowed !!!');
        }
        return view('DMSUrl.edit', compact('title', 'title_for_layout', 'dataForView'));
    }

    public function DMSUrlUpdate($id = null, Request $request)
    {
        $this->validate($request,[
            'name'      =>  'required',
            'url'       =>  'required',
            'request'   =>  'required',
        ]);
        $dmsModelName = new DMSAttachUrl;
        if ($request->isMethod('post')) {
            $dmsModelName = $dmsModelName->where('id', decrypt($id))->first();
            $dmsModelName->name = $request->get('name');
            $dmsModelName->url = $request->get('url');
            $dmsModelName->request = $request->get('request');
            $dmsModelName->status = 1;
            if ($dmsModelName->save()) {
                flash('DMS Url has been updated successfully', 'success');
                return redirect('/DMSAPIServices/DMSUrl/list');
            } else {
                flash('Failed to update data', 'danger');
                return redirect()->back();
            }
        }
    }

    public function DMSUrlStatus($id = null, $status)
    {
        $data = DMSAttachUrl::find(decrypt($id));
        if (!empty($data)) {
            $data->update([ 'status'  =>  $status ]);
            if($status == 1) {
                flash('DMS Url has been activated successfully', 'success');
            } else {
                flash('DMS Url has been inactivated !!', 'warning');
            }
        } else {
            flash('Not Found', 'danger');
        }
        return redirect()->back();
    }
    //DMS Url CRUD end

    //DMS Retry start
    public function DMSRetry(Request $request)
    {
        $title = "DMS Retry Document Uploading";
        $title_for_layout = "DMS Retry Document Uploading";
        $searchDataForView = $request->all();
        $searchDataForView['date_from'] = !empty($request->date_from) ? $request->date_from : date('d-m-Y');
        $searchDataForView['date_to'] = !empty($request->date_to) ? $request->date_to : date('d-m-Y');
        $wFormDataObj = array();
        $complaintDataObj = array();
        if ((!empty($request->active_tab))) {
            $searchDataForView['active_tab'] = $request->active_tab;
        } else {
            $searchDataForView['active_tab'] = 'wform';
        }
        if ($searchDataForView['active_tab'] == 'wform') {
            $wformModelName = new WForm;
            $wFormDataObj = $wformModelName
                ->select(
                    "w_form.*"
                    , "w_form.product_type as product_type_ext"
                    , "product_types.name as product_type"
                    , "unit_items.name as category_name"
                    , "reference.unit_id"
                    , "reference.created_by"
                    , "reference.date"
                    , "reference.status"
                    , "reference.form_status"
                    , "reference.access_by"
                    , "reference.access_date"
                    , DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d') AS UNXTIME")
                    ,'attachments.id AS attachment_id'
                    ,'attachments.file_name'
                    ,'attachments.attachment_date'
                    ,'dms_attach_mapping.id as attachID'
                )
                ->leftJoin('reference', 'reference.reference_number', '=', 'w_form.reference_number')
                ->leftJoin('product_types', 'product_types.id', '=', 'w_form.product_type')
                ->leftJoin('attachments','attachments.reference_number','reference.reference_number')
                ->leftJoin('dms_attach_mapping','dms_attach_mapping.attachment_id','attachments.id')
                ->leftJoin('unit_items', function($join) {
                    $join->on('unit_items.master_id', '=', 'w_form.w_form_type');
                    $join->on('unit_items.issues_from', '=', DB::raw("'wform'"));
                })
                // ->where('w_form.SIF_Number','<>',null)
                ->where('dms_attach_mapping.status',"=", 0)
                ->where('reference.form_status',"=", 11)
                ->where('attachments.file_name','<>',null);

            if (!empty($searchDataForView['date_from']) && empty($searchDataForView['date_to'])) {
                $wFormDataObj = $wFormDataObj
                    ->where('reference.date', ">=", strtotime($searchDataForView['date_from'].' 00:00:00'))
                    ->where('reference.date', "<=", strtotime($searchDataForView['date_from'].' 23:59:59'));
                $searchDataForView['date_to'] = date('d-m-Y', strtotime($searchDataForView['date_from']));
            } elseif (!empty($searchDataForView['date_to']) && empty($searchDataForView['date_from'])) {
                $wFormDataObj = $wFormDataObj
                    ->where('reference.date', ">=", strtotime($searchDataForView['date_to'].' 00:00:00'))
                    ->where('reference.date', "<=", strtotime($searchDataForView['date_to'].' 23:59:59'));
                $searchDataForView['date_from'] = date('d-m-Y', strtotime($searchDataForView['date_to']));
            } elseif (!empty($searchDataForView['date_to']) && !empty($searchDataForView['date_from'])) {
                $wFormDataObj = $wFormDataObj->where(function ($q) use ($searchDataForView) {
                    $q->where('reference.date', ">=", strtotime($searchDataForView['date_from'].' 00:00:00'))
                        ->where('reference.date', "<=", strtotime($searchDataForView['date_to'].' 23:59:59'));
                });
            } else {
            }
            $wFormDataObj = $wFormDataObj->get()->toArray();

        } elseif ($searchDataForView['active_tab'] == 'complaint') {
            $complaintModelName = new Complaint;
            $complaintDataObj = $complaintModelName
                ->select(
                    "complaint.*"
                    ,"complaint.product_type as product_type_ext"
                    ,"product_types.name as product_type"
                    ,"unit_items.name as issue_name"
                    ,"reference.unit_id"
                    ,"reference.created_by"
                    ,"reference.date"
                    ,"reference.status"
                    ,"reference.form_status"
                    ,"reference.access_by"
                    ,"reference.access_date"
                    ,DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d') AS UNXTIME")
                    ,'attachments.id AS attachment_id'
                    ,'attachments.file_name'
                    ,'attachments.attachment_date'
                    ,'dms_attach_mapping.id as attachID'
                )
                ->leftJoin('reference', 'reference.reference_number', '=', 'complaint.reference_number')
                ->leftJoin('attachments','attachments.reference_number','reference.reference_number')
                ->leftJoin('dms_attach_mapping','dms_attach_mapping.attachment_id','attachments.id')
                ->leftJoin('product_types', 'product_types.id', '=', 'complaint.product_type')
                ->leftJoin('unit_items', function($join) {
                    $join->on('unit_items.master_id', '=', 'complaint.complaint_type');
                    $join->on('unit_items.issues_from', '=', DB::raw("'complaint'"));
                })
                ->where('complaint.SIF_Number','<>',null)
                ->where('dms_attach_mapping.status',"=", 0)
                ->where('reference.form_status',"=", 11)
                ->where('attachments.file_name','<>',null);

            if (!empty($searchDataForView['date_from']) && empty($searchDataForView['date_to'])) {
                $complaintDataObj = $complaintDataObj
                    ->where('reference.date', ">=", strtotime($searchDataForView['date_from'].' 00:00:00'))
                    ->where('reference.date', "<=", strtotime($searchDataForView['date_from'].' 23:59:59'));
                $searchDataForView['date_to'] = date('d-m-Y', strtotime($searchDataForView['date_from']));
            } elseif (!empty($searchDataForView['date_to']) && empty($searchDataForView['date_from'])) {
                $complaintDataObj = $complaintDataObj
                    ->where('reference.date', ">=", strtotime($searchDataForView['date_to'].' 00:00:00'))
                    ->where('reference.date', "<=", strtotime($searchDataForView['date_to'].' 23:59:59'));
                $searchDataForView['date_from'] = date('d-m-Y', strtotime($searchDataForView['date_to']));
            } elseif (!empty($searchDataForView['date_to']) && !empty($searchDataForView['date_from'])) {
                $complaintDataObj = $complaintDataObj->where(function ($q) use ($searchDataForView) {
                    $q->where('reference.date', ">=", strtotime($searchDataForView['date_from'].' 00:00:00'))
                        ->where('reference.date', "<=", strtotime($searchDataForView['date_to'].' 23:59:59'));
                });
            } else {
            }
            $complaintDataObj = $complaintDataObj->get()->toArray();
        }

        return view('DMSUrl.retry_list',compact('title','title_for_layout','wFormDataObj','searchDataForView','complaintDataObj'));
    }

    public function DMSDocRetry(Request $request, $ref, $id)
    {
        try {
            $reference_number = decrypt($ref);
            $attachID = decrypt($id);
        } catch (DecryptException $e) {
            abort(403, 'Un-Authorize Access!!!');
        }
        $code = '';
        $msg = 'Error';
        $extraNode = '';
        $dmsAttachUrl = DMSAttachUrl::where('name', 'AddDocument')->first();
        $referenceModel = new Reference();
        $referenceDataArr = $referenceModel
            ->select(
                'reference.reference_number'
                , DB::raw('IFNULL(w_form.product_type,complaint.product_type) AS product_type')
                , DB::raw('IFNULL(w_form.SIF_Number,complaint.SIF_Number) AS cif_number')
                , DB::raw('IFNULL(w_form.account_number,complaint.account_number) AS account_number')
                , DB::raw('IFNULL(w_form.mobile_number,complaint.mobile_number) AS mobile_number')
                , DB::raw('IFNULL(dmscustwform.cif_index,dmscustcomplaint.cif_index) AS cif_index')
                , DB::raw('IFNULL(dmscustwform.cc_index,dmscustcomplaint.cc_index) AS cc_index')
                , DB::raw('IFNULL(dmscustwform.casa_index,dmscustcomplaint.casa_index) AS casa_index')
                , DB::raw('IFNULL(dmscustwform.sme_loan_index,dmscustcomplaint.sme_loan_index) AS sme_loan_index')
                , DB::raw('IFNULL(dmscustwform.retail_loan_index,dmscustcomplaint.retail_loan_index) AS retail_loan_index')
                , DB::raw('IFNULL(dmscustwform.corporate_loan_index,dmscustcomplaint.corporate_loan_index) AS corporate_loan_index')
                , DB::raw('IFNULL(w_form.segment,complaint.segment) AS segment')
                ,'attachments.id AS attachment_id'
                ,'attachments.file_name'
            )
            ->leftJoin('w_form','w_form.reference_number','reference.reference_number')
            ->leftJoin('complaint','complaint.reference_number','reference.reference_number')
            ->leftJoin('attachments','attachments.reference_number','reference.reference_number')
            ->leftJoin('dms_attach_mapping','dms_attach_mapping.attachment_id','attachments.id')
            ->leftJoin('dms_cust_mappings AS dmscustwform','dmscustwform.cif_number','w_form.SIF_Number')
            ->leftJoin('dms_cust_mappings AS dmscustcomplaint','dmscustcomplaint.cif_number','complaint.SIF_Number')
            ->where('dms_attach_mapping.id','=', $attachID)
            ->where('reference.reference_number', "=",  $reference_number)
            ->get()
            ->toArray();

        if(!empty($referenceDataArr)) {
            foreach($referenceDataArr AS $key => $refDatArr) {
                $reference_number = $refDatArr['reference_number'];
                $product_type = $refDatArr['product_type'];
                $cif_number = $refDatArr['cif_number'];
                $cif_index = $refDatArr['cif_index'];
                $segment = $refDatArr['segment'];
                $file_name = $refDatArr['file_name'];
                $attachment_id = $refDatArr['attachment_id'];
                $accno = $refDatArr['account_number'];
                $mblno = $refDatArr['mobile_number'];

                $cc_index = $refDatArr['cc_index'];
                $casa_index = $refDatArr['casa_index'];
                $sme_loan_index = $refDatArr['sme_loan_index'];
                $retail_loan_index = $refDatArr['retail_loan_index'];
                $corporate_loan_index = $refDatArr['corporate_loan_index'];

                $dmsAttachmentModel = new DMSAttachMapping();
                $dmsAttachmentObj = $dmsAttachmentModel->where('id', $attachID)->first();

                if ($product_type == 1) {
                    $smart_index = $cc_index;
                    $dataDefName = "CreditCard";
                    $dataDefIndex = 82;
                    $cif_id = 88;
                    $acc_id = 87;
                    $mbl_id = 90;
                    $extraNode = '<v1:NGOAddDocDataDefCriteriaDataBDO>
                                    <v1:indexId>' . $mbl_id . '</v1:indexId>
                                    <v1:indexType>S</v1:indexType>
                                    <v1:indexValue>' . $mblno . '</v1:indexValue>
                                </v1:NGOAddDocDataDefCriteriaDataBDO>';
                }
                if ($product_type == 2 || $product_type == 3) {
                    $smart_index = $casa_index;
                    $dataDefName = "CASA";
                    $dataDefIndex = 102;
                    $cif_id = 126;
                    $acc_id = 127;
                    $mbl_id = 125;
                    $extraNode = '<v1:NGOAddDocDataDefCriteriaDataBDO>
                                    <v1:indexId>' . $mbl_id . '</v1:indexId>
                                    <v1:indexType>S</v1:indexType>
                                    <v1:indexValue>' . $mblno . '</v1:indexValue>
                                </v1:NGOAddDocDataDefCriteriaDataBDO>';
                }
                if ($product_type == 4) {
                    $segment = substr($segment, 0, 1);
                    if ($segment == 9) {
                        $smart_index = $sme_loan_index;
                        $dataDefName = "SMELoan";
                        $dataDefIndex = 42;
                        $cif_id = 43;
                        $acc_id = 47;
                        $mbl_id = '';
                        $mblno = '';
                    } elseif ($segment == 3) {
                        $smart_index = $retail_loan_index;
                        $dataDefName = "RetailLoan";
                        $dataDefIndex = 362;
                        $cif_id = 1068;
                        $acc_id = 1069;
                        $mbl_id = '';
                        $mblno = '';
                    } elseif ($segment == 4) {
                        $smart_index = $corporate_loan_index;
                        $dataDefName = "Corporate_Loan";
                        $dataDefIndex = 162;
                        $cif_id = 472;
                        $acc_id = 503;
                        $mbl_id = '';
                        $mblno = '';
                    }
                    $extraNode = '';
                }
                $file_content = '';
                $basePath = str_replace('engine','',base_path());
                $fileURL = $basePath.'public/attachments/'.$file_name;
                if(file_exists($fileURL)){
                    $fileURL = $this->fileCompression($fileURL, $file_name);
                    $file_content = base64_encode(file_get_contents($fileURL));
                }
                $createdByAppName = pathinfo($file_name, PATHINFO_EXTENSION);
                $url = $dmsAttachUrl->url;
                $xml_post_string = $dmsAttachUrl->request;
                $today = \Carbon\Carbon::now()->format('YmdHisv');
                $sourceTimestamp = \Carbon\Carbon::now()->format('Y-m-d\TH:i:s.v');
                $global_transaction_id = 'AYS'.$today;
                $xml_post_string = str_replace("#folder_index#", $smart_index, $xml_post_string);
                $xml_post_string = str_replace("#document_name#", $file_name, $xml_post_string);
                $xml_post_string = str_replace("#global_transaction_id#", $global_transaction_id, $xml_post_string);
                $xml_post_string = str_replace("#sourceTimestamp#", $sourceTimestamp, $xml_post_string);
                $xml_post_string = str_replace("#file_content#", $file_content, $xml_post_string);
                $xml_post_string = str_replace("#dataDefName#", $dataDefName, $xml_post_string);
                $xml_post_string = str_replace("#dataDefIndex#", $dataDefIndex, $xml_post_string);
                $xml_post_string = str_replace("#cif_number#", $cif_number, $xml_post_string);
                $xml_post_string = str_replace("#createdByAppName#", $createdByAppName, $xml_post_string);
                $xml_post_string = str_replace("#cif_id#", $cif_id, $xml_post_string);
                $xml_post_string = str_replace("#acc_id#", $acc_id, $xml_post_string);
                $xml_post_string = str_replace("#account_no#", $accno, $xml_post_string);
                $xml_post_string = str_replace("#mobile_no#", $mblno, $xml_post_string);
                $xml_post_string = str_replace("#mobile_id#", $mbl_id, $xml_post_string);
                $xml_post_string = str_replace("#reference_number#", 'AYS - '.$reference_number, $xml_post_string);
                $extraNode = trim(preg_replace('/\s\s+/', ' ', $extraNode));
                $xml_post_string = str_replace("<extraNode>extraNode</extraNode>", $extraNode, $xml_post_string);
                $this->requestResponsePayloadDMS(['reference_number' => $reference_number, 'type' => 1, 'json_node' => $xml_post_string, 'cif_number' => $cif_number, 'url' => $url, 'global_transaction_id' => $global_transaction_id]);
                $msc = microtime(true);
                $response = $this->DMSCurl($xml_post_string, $url);
                $msc = microtime(true) - $msc;
                $execution_time = number_format($msc,2);
                if(!empty($response)) {
                    $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
                    $xml = new SimpleXMLElement($response);
                    $body = $xml->xpath('//soapenvBody')[0];
                    $array = json_decode(json_encode((array)$body), TRUE);
                    $array = array_flatten($array);
                    foreach ($array as $key => $value) {
                        $ke = str_ireplace(['v11','v1','v2','ns1','ns2','ns4'], '', $key);
                        if ($ke == 'documentIndex') {
                            $doc_index = $value;
                        } else {
                            $doc_index = null;
                        }
                        if ($ke == 'responseCode') {
                            $code = $value;
                        }
                        if ($ke == 'responseMessage') {
                            $msg = $value;
                        }
                    }
                }
                $this->requestResponsePayloadDMS(['reference_number' => $reference_number, 'attachment_id' => $attachment_id, 'type' => 2, 'json_node' => $response, 'execution_time' => $execution_time, 'cif_number' => $cif_number, 'url' => $url, 'global_transaction_id' => $global_transaction_id, 'code' => $code, 'msg' => $msg ]);
                if ($code == '000') {
                    $dmsAttachmentObj->status = 1;
                    $dmsAttachmentObj->doc_index = $doc_index;
                } else {
                    $dmsAttachmentObj->status = -1;
                    $dmsAttachmentObj->doc_index = null;
                }
                $dmsAttachmentObj->save();
            }
        }
        if ($code == '000') {
            flash('DMS Document Uploading retry successful', 'success');
            return redirect()->back();
        } else {
            flash($msg, 'danger');
            return redirect()->back();
        }
    }

    public function DMSDocRetryBulk(Request $request)
    {
        $code = '';
        $msg = '';
        $status = 0;
        $success = 1;
        $extraNode = '';
        foreach ($request->ref_nos as $value) {
            $ValArr = explode('?', $value);
            $reference_number = (!empty($ValArr[0])) ? $ValArr[0] : $value;
            $attachID = (!empty($ValArr[2])) ? $ValArr[2] : $value;
            $dmsAttachUrl = DMSAttachUrl::where('name', 'AddDocument')->first();
            $referenceModel = new Reference();
            $referenceDataArr = $referenceModel
                ->select(
                    'reference.reference_number'
                    , DB::raw('IFNULL(w_form.product_type,complaint.product_type) AS product_type')
                    , DB::raw('IFNULL(w_form.SIF_Number,complaint.SIF_Number) AS cif_number')
                    , DB::raw('IFNULL(w_form.account_number,complaint.account_number) AS account_number')
                    , DB::raw('IFNULL(w_form.mobile_number,complaint.mobile_number) AS mobile_number')
                    , DB::raw('IFNULL(dmscustwform.cif_index,dmscustcomplaint.cif_index) AS cif_index')
                    , DB::raw('IFNULL(dmscustwform.cc_index,dmscustcomplaint.cc_index) AS cc_index')
                    , DB::raw('IFNULL(dmscustwform.casa_index,dmscustcomplaint.casa_index) AS casa_index')
                    , DB::raw('IFNULL(dmscustwform.sme_loan_index,dmscustcomplaint.sme_loan_index) AS sme_loan_index')
                    , DB::raw('IFNULL(dmscustwform.retail_loan_index,dmscustcomplaint.retail_loan_index) AS retail_loan_index')
                    , DB::raw('IFNULL(dmscustwform.corporate_loan_index,dmscustcomplaint.corporate_loan_index) AS corporate_loan_index')
                    , DB::raw('IFNULL(w_form.segment,complaint.segment) AS segment')
                    ,'attachments.id AS attachment_id'
                    ,'attachments.file_name'
                )
                ->leftJoin('w_form','w_form.reference_number','reference.reference_number')
                ->leftJoin('complaint','complaint.reference_number','reference.reference_number')
                ->leftJoin('attachments','attachments.reference_number','reference.reference_number')
                ->leftJoin('dms_attach_mapping','dms_attach_mapping.attachment_id','attachments.id')
                ->leftJoin('dms_cust_mappings AS dmscustwform','dmscustwform.cif_number','w_form.SIF_Number')
                ->leftJoin('dms_cust_mappings AS dmscustcomplaint','dmscustcomplaint.cif_number','complaint.SIF_Number')
                ->where('dms_attach_mapping.id','=', $attachID)
                ->where('reference.reference_number', "=",  $reference_number)
                ->get()
                ->toArray();

            if(!empty($referenceDataArr)) {
                foreach($referenceDataArr AS $key => $refDatArr) {
                    $success = $key;
                    $reference_number = $refDatArr['reference_number'];
                    $product_type = $refDatArr['product_type'];
                    $cif_number = $refDatArr['cif_number'];
                    $cif_index = $refDatArr['cif_index'];
                    $segment = $refDatArr['segment'];
                    $file_name = $refDatArr['file_name'];
                    $attachment_id = $refDatArr['attachment_id'];
                    $accno = $refDatArr['account_number'];
                    $mblno = $refDatArr['mobile_number'];

                    $cc_index = $refDatArr['cc_index'];
                    $casa_index = $refDatArr['casa_index'];
                    $sme_loan_index = $refDatArr['sme_loan_index'];
                    $retail_loan_index = $refDatArr['retail_loan_index'];
                    $corporate_loan_index = $refDatArr['corporate_loan_index'];

                    $dmsAttachmentModel = new DMSAttachMapping();
                    $dmsAttachmentObj = $dmsAttachmentModel->where('id', $attachID)->first();

                    if ($product_type == 1) {
                        $smart_index = $cc_index;
                        $dataDefName = "CreditCard";
                        $dataDefIndex = 82;
                        $cif_id = 88;
                        $acc_id = 87;
                        $mbl_id = 90;
                        $extraNode = '<v1:NGOAddDocDataDefCriteriaDataBDO>
                                        <v1:indexId>' . $mbl_id . '</v1:indexId>
                                        <v1:indexType>S</v1:indexType>
                                        <v1:indexValue>' . $mblno . '</v1:indexValue>
                                    </v1:NGOAddDocDataDefCriteriaDataBDO>';
                    }
                    if ($product_type == 2 || $product_type == 3) {
                        $smart_index = $casa_index;
                        $dataDefName = "CASA";
                        $dataDefIndex = 102;
                        $cif_id = 126;
                        $acc_id = 127;
                        $mbl_id = 125;
                        $extraNode = '<v1:NGOAddDocDataDefCriteriaDataBDO>
                                        <v1:indexId>' . $mbl_id . '</v1:indexId>
                                        <v1:indexType>S</v1:indexType>
                                        <v1:indexValue>' . $mblno . '</v1:indexValue>
                                    </v1:NGOAddDocDataDefCriteriaDataBDO>';
                    }
                    if ($product_type == 4) {
                        $segment = substr($segment, 0, 1);
                        if ($segment == 9) {
                            $smart_index = $sme_loan_index;
                            $dataDefName = "SMELoan";
                            $dataDefIndex = 42;
                            $cif_id = 43;
                            $acc_id = 47;
                            $mbl_id = '';
                            $mblno = '';
                        } elseif ($segment == 3) {
                            $smart_index = $retail_loan_index;
                            $dataDefName = "RetailLoan";
                            $dataDefIndex = 362;
                            $cif_id = 1068;
                            $acc_id = 1069;
                            $mbl_id = '';
                            $mblno = '';
                        } elseif ($segment == 4) {
                            $smart_index = $corporate_loan_index;
                            $dataDefName = "Corporate_Loan";
                            $dataDefIndex = 162;
                            $cif_id = 472;
                            $acc_id = 503;
                            $mbl_id = '';
                            $mblno = '';
                        }
                        $extraNode = '';
                    }
                    $file_content = '';
                    $basePath = str_replace('engine','',base_path());
                    $fileURL = $basePath.'public/attachments/'.$file_name;
                    if(file_exists($fileURL)){
                        $file_content = base64_encode(file_get_contents($fileURL));
                    }
                    $createdByAppName = pathinfo($file_name, PATHINFO_EXTENSION);
                    $url = $dmsAttachUrl->url;
                    $xml_post_string = $dmsAttachUrl->request;
                    $today = \Carbon\Carbon::now()->format('YmdHisv');
                    $sourceTimestamp = \Carbon\Carbon::now()->format('Y-m-d\TH:i:s.v');
                    $global_transaction_id = 'AYS'.$today;
                    $xml_post_string = str_replace("#folder_index#", $smart_index, $xml_post_string);
                    $xml_post_string = str_replace("#document_name#", $file_name, $xml_post_string);
                    $xml_post_string = str_replace("#global_transaction_id#", $global_transaction_id, $xml_post_string);
                    $xml_post_string = str_replace("#sourceTimestamp#", $sourceTimestamp, $xml_post_string);
                    $xml_post_string = str_replace("#file_content#", $file_content, $xml_post_string);
                    $xml_post_string = str_replace("#dataDefName#", $dataDefName, $xml_post_string);
                    $xml_post_string = str_replace("#dataDefIndex#", $dataDefIndex, $xml_post_string);
                    $xml_post_string = str_replace("#cif_number#", $cif_number, $xml_post_string);
                    $xml_post_string = str_replace("#createdByAppName#", $createdByAppName, $xml_post_string);
                    $xml_post_string = str_replace("#cif_id#", $cif_id, $xml_post_string);
                    $xml_post_string = str_replace("#acc_id#", $acc_id, $xml_post_string);
                    $xml_post_string = str_replace("#account_no#", $accno, $xml_post_string);
                    $xml_post_string = str_replace("#mobile_no#", $mblno, $xml_post_string);
                    $xml_post_string = str_replace("#mobile_id#", $mbl_id, $xml_post_string);
                    $xml_post_string = str_replace("#reference_number#", 'AYS - '.$reference_number, $xml_post_string);
                    $extraNode = trim(preg_replace('/\s\s+/', ' ', $extraNode));
                    $xml_post_string = str_replace("<extraNode>extraNode</extraNode>", $extraNode, $xml_post_string);
                    $this->requestResponsePayloadDMS(['reference_number' => $reference_number, 'type' => 1, 'json_node' => $xml_post_string, 'cif_number' => $cif_number, 'url' => $url, 'global_transaction_id' => $global_transaction_id]);
                    $msc = microtime(true);
                    $response = $this->DMSCurl($xml_post_string, $url);
                    $msc = microtime(true) - $msc;
                    $execution_time = number_format($msc,2);
                    if(!empty($response)) {
                        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
                        $xml = new SimpleXMLElement($response);
                        $body = $xml->xpath('//soapenvBody')[0];
                        $array = json_decode(json_encode((array)$body), TRUE);
                        $array = array_flatten($array);
                        foreach ($array as $key => $value) {
                            $ke = str_ireplace(['v11','v1','v2','ns1','ns2','ns4'], '', $key);
                            if ($ke == 'documentIndex') {
                                $doc_index = $value;
                            } else {
                                $doc_index = null;
                            }
                            if ($ke == 'responseCode') {
                                $code = $value;
                            }
                            if ($ke == 'responseMessage') {
                                $msg = $value;
                            }
                        }
                    }
                    $this->requestResponsePayloadDMS(['reference_number' => $reference_number, 'attachment_id' => $attachment_id, 'type' => 2, 'json_node' => $response, 'execution_time' => $execution_time, 'cif_number' => $cif_number, 'url' => $url, 'global_transaction_id' => $global_transaction_id, 'code' => $code, 'msg' => $msg ]);
                    if ($code == '000') {
                        $dmsAttachmentObj->status = 1;
                        $dmsAttachmentObj->doc_index = $doc_index;
                        $success = $key - 1;
                    } else {
                        $dmsAttachmentObj->status = -1;
                        $dmsAttachmentObj->doc_index = null;
                    }
                    $dmsAttachmentObj->save();
                }
            }
        }
        if ($success == 0) {
            $status = 1;
        }
        return response()->json([$status]);
    }

    /* FileCompression Method (Zihad) 11/4/2023 */
    public function fileCompression($fileUrl, $file_name)
    {
        $sizeBytes = File::size($fileUrl);
        $siteMB =  number_format($sizeBytes / 1048576,2);
        $basePath = str_replace('engine','',base_path());

        $newUploadPath = $basePath.'public/attachments/compress/'.$file_name;
        $ext = pathinfo($fileUrl, PATHINFO_EXTENSION);

        if ($siteMB > 1.04 && $ext == "jpeg" || $ext == "png" || $ext == "gif" || $ext == "webp"){

            $imgInfo = getimagesize($fileUrl);
            $mime = $imgInfo['mime'];

            // Create a new image from file
            switch($mime){
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($fileUrl);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($fileUrl);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($fileUrl);
                    break;
                case 'image/webp':
                    $image = imagecreatefromwebp($fileUrl);
                    break;
                default:
                    $image = imagecreatefromjpeg($fileUrl);
            }
            if ($ext == "webp"){
                imagewebp($image, $newUploadPath, 30);
            }else{
                imagejpeg($image, $newUploadPath, 40);
            }

            return $newUploadPath;

        }else {
            return $fileUrl;
        }

    }

}
