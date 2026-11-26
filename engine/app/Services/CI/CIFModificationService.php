<?php

namespace App\Services\CI;

use App\ApiCommonConfig;
use App\Comment;
use App\DynamicAPICredential;
use App\IssueConfigMapping;
use App\SubgroupInfo;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Reference;
use App\WForm;
use App\WFormType;
use App\Complaint;
use App\ComplaintType;
use App\ComplaintFormType;

use App\CIFModificationUrl;
use App\CIFParentUrl;
use App\CIFWorkflow;
use App\CIFApi;
use App\CIFRequestResponse;

use GuzzleHttp\Client;
use DateTime;
use Illuminate\Support\Facades\Session;
use Storage;
use SimpleXMLElement;
use DomXPath;
use DomDocument;
use App\Http\Controllers\Controller;

class CIFModificationService
{

    public static function apiUpdate($payload)
    {
        $response = array();
        $response['success'] = 0;
        $response['msg'] = '';
        $response['failed_api'] = [];
        $status = [];
        $tmp_status = 1;
        $status_code = 0;
        $status_msg = '';
        $globalTransactionId = '';
        $msg = '';
        $ref_no = '';
        $req_from = '';
        $cif_no = '';
        $account_number = '';
        $failed_api_arr = array();
        $subgroup_id = 0;
        $group_info_id = 0;
        $extraNode = '';
        $extraNodeArray = [];
        $type = '';
        $num = '';
        $eml = '';
        $cun = '';
        $cty = '';
        $pref = '';
        $add1 = '';
        $add2 = '';
        $add3 = '';
        $stat = '';
        $twn = '';
        $posCode = '';
        $prefAddr = '';
        $addressType = '';
        $placeOfIssue = '';
        $nreplaceOfIssue = '';
        $refNum = '';
        $nrerefNum = '';
        $nrecountryOfIssue = '';
        $countryOfIssue = '';
        $docExpDt = '';
        $docIssueDt = '';
        $nredocIssueDt = '';
        $isCustNRE = '';
        $nreBecomingDt = '';
        $firstname = '';
        $middlename = '';
        $lastname = '';
        $fullName = '';
        $shortName = '';
        $shortName1 = '';
        $shortName2 = '';
        $shortName3 = '';
        $accRes = [];
        $segment = '';
        $tpCashDepTranAmtTot = 0;
        $tpXferDepTranAmtTot = 0;
        $tpCashWdrTranAmtTot = 0;
        $tpXferWdrTranAmtTot = 0;
        $tpTotlDepTranAmtTot = 0;
        $tpTotlWdrTranCnt = 0;
        $tpTotlWdrTranAmtHigh = 0;
        $tpTotlWdrTranAmtTot = 0;
        $tpRemtDepTranAmtTot = 0;
        $tpRemtWdrTranAmtTot = 0;
        $tpCashDepTranAmtHigh = 0;
        $tpXferDepTranAmtHigh = 0;
        $tpCashWdrTranAmtHigh = 0;
        $tpXferWdrTranAmtHigh = 0;
        $regNum = '00000000';

        $memo = 'Data updated from AYS';

        try {
            $encrypted_ref_no = xss_cleaner($payload['ref_no']);
            $encrypted_cif_no = xss_cleaner($payload['cif_no']);
            $encrypted_account_number = xss_cleaner($payload['account_number']);
            $req_from = xss_cleaner($payload['req_from']);
            $ref_no = decrypt($encrypted_ref_no);
            $cif_no = decrypt($encrypted_cif_no);
            $account_number = decrypt($encrypted_account_number);
            $failed_api = xss_cleaner($payload['failed_api']);
        } catch (DecryptException $e) {
            $response['success'] = 0;
            $response['msg'] = 'Something went wrong!!!. Please refresh this page';
            return response(['msg' => $response['msg'], 'status' => $response['success'], 'failed_api' => $response['failed_api']]);
        }

        $referenceModel = new Reference();
        $referenceData = $referenceModel->where('reference_number', $ref_no)->first();

        if (!empty($referenceData)) {
            if ($referenceData->api_status == 1) {
                $response['success'] = 1;
                $response['msg'] = 'API data already updated !!!';
                return response(['msg' => $response['msg'], 'status' => $response['success'], 'failed_api' => $response['failed_api']]);
            // } elseif ($referenceData->failed_retry >= 5) {
            //     $response['success'] = 2;
            //     $response['msg'] = 'You have already tried updating this API data. Please Contact with Administrator !!!';
            //     return response(['msg' => $response['msg'], 'status' => $response['success'], 'failed_api' => $response['failed_api']]);
            } else {
                $failed_api = $referenceData->failed_api;
            }
        }
        $failed_api_arr = explode(',', $failed_api);

        $dataObj = array();
        $dataForView = array();
        if ($req_from == "wform") {
            $wCompModel = new Reference;
            $dataObj = $wCompModel
                ->select('w_form.*', 'w_form_type.extra_field', 'reference.issue_id', 'reference.memo')
                ->with(['issueConfigForApi'])
                ->leftJoin('w_form', 'reference.reference_number', '=', 'w_form.reference_number')
                ->leftJoin('w_form_type', 'w_form_type.reference_number', '=', 'w_form.reference_number')
                ->where('reference.reference_number', $ref_no)
                ->first();

        } elseif ($req_from == "complaint") {
            $wCompModel = new Reference;
            $dataObj = $wCompModel
                ->select('complaint.*', 'complaint_form_type.extra_field', 'reference.issue_id', 'reference.memo')
                ->with(['issueConfigForApi'])
                ->leftJoin('complaint', 'reference.reference_number', '=', 'complaint.reference_number')
                ->leftJoin('complaint_form_type', 'complaint_form_type.reference_number', '=', 'complaint.reference_number')
                ->where('reference.reference_number', $ref_no)
                ->first();
        }

        if (!empty($dataObj)) {

            $dataForSave = array();
            $dataForArr = $dataObj->toArray();

            $extraField = json_decode($dataForArr['extra_field'], true);
            $extraFieldIdx = array();

            $issue_id = $dataForArr['issue_id'];
            $segment = $dataForArr['segment'];
            $segment = substr($segment, 0, 1);

            $getChildApi = DB::table('cif_api')
                ->select('cif_modification_url.*')
                ->leftJoin('cif_parent_url', 'cif_parent_url.id', 'cif_api.parent_api')
                ->leftJoin('cif_modification_url', 'cif_parent_url.id', 'cif_modification_url.parent_id')
                ->where('cif_api.issue_id', $issue_id)
                ->where('cif_api.type', 1)
                ->where('cif_parent_url.status', 1)
                ->where('cif_modification_url.status', 1);
            if (!empty($failed_api)) {
                $getChildApi = $getChildApi->whereIn('cif_modification_url.id', $failed_api_arr);
            }
            /*if (!empty($segment)) {
                if ($segment == 3) {
                    // Retail CIF
                    $getChildApi = $getChildApi->where('cif_modification_url.id', 8);
                } elseif ($segment == 4) {
                    // Corporate CIF
                    $getChildApi = $getChildApi->where('cif_modification_url.id', 17);
                }
            }*/
            $getChildApi = $getChildApi
                ->get()
                ->toArray();

            $issueConfigMap = DB::table('issue_config_mapping')
                ->select('issue_config_mapping.*', 'issue_config.label_name')
                ->leftJoin('issue_config', function ($join) {
                    $join->on('issue_config_mapping.issue_id', '=', 'issue_config.issue_id');
                    $join->on('issue_config_mapping.field_name', '=', 'issue_config.field_name');
                })
                ->where('issue_config_mapping.issue_id', $issue_id)
                ->get()
                ->toArray();

            $issueAPICommonConfig = DB::table('api_common_config')
                ->select('api_common_config.*')
                ->where('api_common_config.issue_id', $issue_id)
                ->where('api_common_config.type', 1)
                ->pluck('value', 'api_parameter')
                ->toArray();

            $getChildApi = json_decode(json_encode($getChildApi), true);
            $issueConfigMap = json_decode(json_encode($issueConfigMap), true);

            $extraFieldWithIssueConfig = array();

            foreach ($extraField as $ext) {
                if (!empty($ext['Address Type'])) {
                    $addressType = $ext['Address Type'];
                }
                if (!empty($ext['First Name'])) {
                    $firstname = $ext['First Name'];
                }
                if (!empty($ext['Middle Name'])) {
                    $middlename = $ext['Middle Name'];
                }
                if (!empty($ext['Last Name'])) {
                    $lastname = $ext['Last Name'];
                }
                /* Finacle Api value | Cardpro Api value | Frontend value show
                Ex:- 0004|BD~Bangladesh */
                foreach ($issueConfigMap as $issueCfg) {
                    if (isset($ext[$issueCfg['label_name']])) {
                        $tmpExtValue = $ext[$issueCfg['label_name']];
                        if (str_contains($tmpExtValue, '~')) {
                            $tmpExtValueArr = explode('~', $tmpExtValue);
                            $tmpExtValue = (!empty($tmpExtValueArr[0])) ? $tmpExtValueArr[0] : $tmpExtValue;
                        }
                        $extraFieldWithIssueConfig[$issueCfg['api_parameter']] = $tmpExtValue;
                    }
                }
            }
            if (!empty($firstname)) {
                $fullName .= $firstname;
                $fullName .= ' ';
                $shortName1 = substr($firstname, 0, 1);
                $shortName = $shortName1;
            }
            if (!empty($middlename)) {
                $fullName .= $middlename;
                $fullName .= ' ';
                $shortName2 = substr($middlename, 0, 1);
                $shortName = $shortName.$shortName2;
            }
            if (!empty($lastname)) {
                $fullName .= $lastname;
                $shortName3 = substr($lastname, 0, 8);
                $shortName = $shortName3.$shortName;
            }

            $testArr = array();
            $requestToPush = array();
            foreach ($getChildApi as $childRow) {
                $api_id = $childRow['id'];
                $url = $childRow['url'];
                $req = $childRow['request'];
                $date_format = !empty($childRow['date_format']) ? $childRow['date_format'] : 'Y-m-d\TH:i:s.v';
                $password = DynamicAPICredential::where('api', $api_id)->first();
                $password = !empty($password->password) ? decrypt($password->password) : '';
                $sourceTimestamp = now()->format('Y-m-d\TH:i:s.v');
                $acctStmtNxtPrintDt = $sourceTimestamp;
                $acctActDate = now()->format('d-m-Y');
                if ($sourceTimestamp >= date('Y-m-d', strtotime("01/01")) && $sourceTimestamp <= date('Y-m-d', strtotime("06/30"))) {
                    $acctStmtNxtPrintDt = date('Y-m-d\TH:i:s.v',strtotime("this year June 30th"));
                    $acctActDate = date('d-m-Y',strtotime("this year June 30th"));
                } elseif ($sourceTimestamp > date('Y-m-d', strtotime("06/30")) && $sourceTimestamp <= date('Y-m-d', strtotime("12/31"))) {
                    $acctStmtNxtPrintDt = date('Y-m-d\TH:i:s.v',strtotime("this year December 31th"));
                    $acctActDate = date('d-m-Y',strtotime("this year December 31th"));
                }
                $global_transaction_id = 'AYS' . now()->format('YmdHisv');
                $req = str_replace("#cif_number#", $cif_no, $req);
                $req = str_replace("#account_number#", $account_number, $req);
                $req = str_replace("#global_transaction_id#", $global_transaction_id, $req);
                $req = str_replace("#sourceTimestamp#", $sourceTimestamp, $req);

                /***************************
                 * Address Issue ID UAT : 1019,1078   *
                 * Address Issue ID Live : 1169,1172   *
                 * Card Address API ID UAT : 14 *
                 ****************************/
                if (($issue_id == 1019 || $issue_id == 1078) && $api_id == 14) {
                    $req = cifApiNodeRemover($req, $addressType);
                }
                /***************************
                 * Name change Issue UAT ID: 128,1080  *
                 * Name change Issue Live ID: 1165,1173  *
                 ****************************/
                if ($issue_id == 128 || $issue_id == 1080) {
                    $req = str_replace("#fullName#", $fullName, $req);
                    $req = str_replace("#name#", $fullName, $req);
                    $req = str_replace("#firstName#", 'firstName', $req);
                    $req = str_replace("#middleName#", 'middleName', $req);
                    $req = str_replace("#lastName#", 'lastName', $req);
                    $req = str_replace("#shortName#", 'shortName', $req);
                }
                /***************************
                 * Memo In card update api *
                 ****************************/
                if (!empty($referenceData->memo)) {
                    $req = str_replace("#memo#", $referenceData->memo, $req);
                } else {
                    $memoname = DB::table('unit_items')
                        ->where('master_id', $issue_id)
                        ->select('name')
                        ->first();
                    if (!empty($memoname)) {
                        $memo = substr($memoname->name,0,100) . ' ' . $memo;
                    }
                    $req = str_replace("#memo#", $memo, $req);
                    $referenceData->memo = $memo;
                }

                /* Finacle Api value | Cardpro Api value ~ Frontend value show
                Ex:- 0004|BD~Bangladesh */
                foreach ($extraFieldWithIssueConfig as $keyField => $configVal) {
                    if (validateDate($configVal)) {
                        $configVal = date($date_format, strtotime($configVal));
                    }
                    if (str_contains($configVal, '|')) {
                        $configValArr = explode('|', $configVal);
                        $configVal1 = (!empty($configValArr[0])) ? $configValArr[0] : $configVal;
                        $configVal2 = (!empty($configValArr[1])) ? $configValArr[1] : $configVal;
                        /* Cardpro API ID UAT : 13, 14 */
                        if ($api_id == 13 || $api_id == 14) {
                            $configVal = $configVal2;
                        } else {
                            $configVal = $configVal1;
                        }
                    }
                    /*
                    Cardpro addrline missing Address Change Issue ID UAT 1019,1078
                    Cardpro addrline missing Address Change Issue ID Live 1169,1172
                    TAX RETURN SLIP UPDATE REQUEST UAT issue id 1084
                    TAX RETURN SLIP UPDATE REQUEST Live issue id 1166
                    NOMINEE CHANGE/UPDATE REQUEST UAT issue id 544
                    NOMINEE CHANGE/UPDATE STP Live issue id 1176
                    CIF level API ID UAT : 14
                    Account level API ID UAT : 16
                    */
                    if ($issue_id == 1019 || $issue_id == 1078 || $issue_id == 1084 || $issue_id == 544) {
                        if (str_contains($configVal, '#')) {
                            $configVal = str_replace('#', '`', $configVal);
                        }
                        if (str_contains($configVal, '&')) {
                            $configVal = str_replace('&', '^', $configVal);
                        }
                    }
                    /*
                        Phone Number Change Issue: UAT ID 119,518,1081
                        Foreign Phone Number Change Issue: Live ID 1177
                        Phone Number Change Issue: Live ID 1153,1162
                        Address Change Issue UAT ID: 1019,1078
                        Address Change Issue Live ID: 1169,1172
                        Foreign Address Change Issue UAT ID: 1091
                        Foreign Address Change Issue Live ID: 1178
                        Email Address Change Issue: UAT ID 1077,120
                        Email Address Change Issue: Live ID 1160,1161
                    */
                    $iIds = [119, 120, 518, 1019, 1077, 1078, 1081, 1091];
                    /*  Cardpro API ID UAT : 13  */
                    if (($issue_id == 119 || $issue_id == 518) && $api_id == 13) {
                        $extraNodeArray[$keyField] = $configVal;
                    }
                    /*  Finacle API ID UAT : 8  */
                    if (in_array($issue_id, $iIds) && $api_id == 8) {
                        $extraNodeArray[$keyField] = $configVal;
                    } else {
                        $req = str_replace('#' . $keyField . '#', $configVal, $req);
                    }
                    /*  Spouse Name Issue IDs UAT : 127,129,523  */
                    /*  Spouse Name Issue IDs Live : 1163,1164  */
                    if (($issue_id == 127 || $issue_id == 129 || $issue_id == 523) && $api_id == 8) {
                        if ($keyField == 'spouseName') {
                            $req = str_replace("<v1:spouseName>#spouseName#</v1:spouseName>", "<v1:spouseName>null</v1:spouseName>", $req);
                            $req = str_replace("<v1:spouseName></v1:spouseName>", "<v1:spouseName>null</v1:spouseName>", $req);
                        }
                        if ($keyField == 'spousesName') {
                            $req = str_replace("<v1:spousesName>#spousesName#</v1:spousesName>", "<v1:spousesName>null</v1:spousesName>", $req);
                            $req = str_replace("<v1:spousesName></v1:spousesName>", "<v1:spousesName>null</v1:spousesName>", $req);
                        }
                    }
                    /*  Employer Name Issue IDs UAT : 891  */
                    if ($issue_id == 891 && $api_id == 8) {
                        if ($keyField == 'nameOfEmployer') {
                            $req = str_replace("<v1:nameOfEmployer>#nameOfEmployer#</v1:nameOfEmployer>", "<v1:nameOfEmployer>null</v1:nameOfEmployer>", $req);
                            $req = str_replace("<v1:nameOfEmployer></v1:nameOfEmployer>", "<v1:nameOfEmployer>null</v1:nameOfEmployer>", $req);
                        }
                        if ($keyField == 'designation') {
                            $req = str_replace("<v1:designation>#designation#</v1:designation>", "<v1:designation>null</v1:designation>", $req);
                            $req = str_replace("<v1:designation></v1:designation>", "<v1:designation>null</v1:designation>", $req);
                        }
                    }
                    /* Address change Data Nullify Issue IDs UAT : 1019,1078  CardPro API ID UAT : 14 */
                    /* Address change Data Nullify Issue IDs Live : 1169,1172  CardPro API ID UAT : 14 */
                    if (($issue_id == 1019 || $issue_id == 1078) && $api_id == 14) {
                        if ($keyField == 'address1') {
                            $req = str_replace("<v1:address1>#address1#</v1:address1>", "<v1:address1>null</v1:address1>", $req);
                            $req = str_replace("<v1:address1></v1:address1>", "<v1:address1>null</v1:address1>", $req);
                        }
                        if ($keyField == 'address2') {
                            $req = str_replace("<v1:address2>#address2#</v1:address2>", "<v1:address2>null</v1:address2>", $req);
                            $req = str_replace("<v1:address2></v1:address2>", "<v1:address2>null</v1:address2>", $req);
                        }
                        if ($keyField == 'address3') {
                            $req = str_replace("<v1:address3>#address3#</v1:address3>", "<v1:address3>null</v1:address3>", $req);
                            $req = str_replace("<v1:address3></v1:address3>", "<v1:address3>null</v1:address3>", $req);
                        }
                        if ($keyField == 'address4') {
                            $req = str_replace("<v1:address4>#address4#</v1:address4>", "<v1:address4>null</v1:address4>", $req);
                            $req = str_replace("<v1:address4></v1:address4>", "<v1:address4>null</v1:address4>", $req);
                        }
                        if ($keyField == 'address5') {
                            $req = str_replace("<v1:address5>#address5#</v1:address5>", "<v1:address5>null</v1:address5>", $req);
                            $req = str_replace("<v1:address5></v1:address5>", "<v1:address5>null</v1:address5>", $req);
                        }
                        if ($keyField == 'city') {
                            $req = str_replace("<v1:city>#city#</v1:city>", "<v1:city>null</v1:city>", $req);
                            $req = str_replace("<v1:city></v1:city>", "<v1:city>null</v1:city>", $req);
                        }
                        if ($keyField == 'state') {
                            $req = str_replace("<v1:state>#state#</v1:state>", "<v1:state>null</v1:state>", $req);
                            $req = str_replace("<v1:state></v1:state>", "<v1:state>null</v1:state>", $req);
                        }
                        if ($keyField == 'postCode') {
                            $req = str_replace("<v1:postCode>#postCode#</v1:postCode>", "<v1:postCode>null</v1:postCode>", $req);
                            $req = str_replace("<v1:postCode></v1:postCode>", "<v1:postCode>null</v1:postCode>", $req);
                        }
                        if ($keyField == 'country') {
                            $req = str_replace("<v1:country>#country#</v1:country>", "<v1:country>null</v1:country>", $req);
                            $req = str_replace("<v1:country></v1:country>", "<v1:country>null</v1:country>", $req);
                        }
                        if ($keyField == 'designation') {
                            $req = str_replace("<v1:designation>#designation#</v1:designation>", "<v1:designation>null</v1:designation>", $req);
                            $req = str_replace("<v1:designation></v1:designation>", "<v1:designation>null</v1:designation>", $req);
                        }
                        if ($keyField == 'companyName') {
                            $req = str_replace("<v1:companyName>#companyName#</v1:companyName>", "<v1:companyName>null</v1:companyName>", $req);
                            $req = str_replace("<v1:companyName></v1:companyName>", "<v1:companyName>null</v1:companyName>", $req);
                        }
                    }
                    /*  Transaction Profile Update (A) Issue IDs UAT : 890  */
                    /*  Transaction Profile Update (A) Issue IDs Live : 1179  */
                    if ($issue_id == 890 && $api_id == 16) {
                        if ($keyField == 'tpCashDepTranAmtTot') {
                            $tpCashDepTranAmtTot = !empty($configVal) ? $configVal : 0;
                            $tpTotlDepTranAmtTot += $configVal;
                        }
                        if ($keyField == 'tpXferDepTranAmtTot') {
                            $tpXferDepTranAmtTot = !empty($configVal) ? $configVal : 0;
                            $tpTotlDepTranAmtTot += $configVal;
                        }
                        if ($keyField == 'tpRemtDepTranAmtTot') {
                            $tpRemtDepTranAmtTot = !empty($configVal) ? $configVal : 0;
                            $tpTotlDepTranAmtTot += !empty($configVal) ? $configVal : 0;
                        }
                        if ($keyField == 'tpCashWdrTranAmtTot') {
                            $tpCashWdrTranAmtTot = !empty($configVal) ? $configVal : 0;
                            $tpTotlWdrTranAmtTot += $configVal;
                        }
                        if ($keyField == 'tpXferWdrTranAmtTot') {
                            $tpXferWdrTranAmtTot = !empty($configVal) ? $configVal : 0;
                            $tpTotlWdrTranAmtTot += $configVal;
                        }
                        if ($keyField == 'tpRemtWdrTranAmtTot') {
                            $tpRemtWdrTranAmtTot = !empty($configVal) ? $configVal : 0;
                            $tpTotlWdrTranAmtTot += !empty($configVal) ? $configVal : 0;
                        }
                    }
                }
                if ($issue_id == 890 && $api_id == 16) {
                    if ($tpTotlDepTranAmtTot < $tpTotlWdrTranAmtTot) {
                        $response['success'] = 0;
                        $response['msg'] = 'Total deposit cannot be less than total withdrawal.';
                        return response(['msg' => $response['msg'], 'status' => $response['success'], 'failed_api' => $response['failed_api']]);
                    }
                    $req = str_replace('#tpCashDepTranCnt#', 0, $req);
                    $req = str_replace('#tpXferDepTranCnt#', 0, $req);
                    $req = str_replace('#tpXferDepTranAmtHigh#', 0, $req);
                    $req = str_replace('#tpTotlDepTranCnt#', 0, $req);
                    $req = str_replace('#tpTotlDepTranAmtHigh#', 0, $req);
                    $req = str_replace('#tpCashWdrTranCnt#', 0, $req);
                    $req = str_replace('#tpCashWdrTranAmtHigh#', 0, $req);
                    $req = str_replace('#tpXferWdrTranAmtHigh#', 0, $req);
                    $req = str_replace('#tpXferWdrTranCnt#', 0, $req);
                    $req = str_replace('#tpTotlWdrTranCnt#', 0, $req);
                    $req = str_replace('#tpTotlWdrTranAmtHigh#', 0, $req);
                    $req = str_replace('#tpCashDepTranAmtTot#', $tpCashDepTranAmtTot, $req);
                    $req = str_replace('#tpXferDepTranAmtTot#', $tpXferDepTranAmtTot, $req);
                    $req = str_replace('#tpRemtDepTranAmtTot#', $tpRemtDepTranAmtTot, $req);
                    $req = str_replace('#tpCashWdrTranAmtTot#', $tpCashWdrTranAmtTot, $req);
                    $req = str_replace('#tpXferWdrTranAmtTot#', $tpXferWdrTranAmtTot, $req);
                    $req = str_replace('#tpRemtWdrTranAmtTot#', $tpRemtWdrTranAmtTot, $req);
                    $req = str_replace('#tpTotlDepTranAmtTot#', $tpTotlDepTranAmtTot, $req);
                    $req = str_replace('#tpTotlWdrTranAmtTot#', $tpTotlWdrTranAmtTot, $req);
                    $req = str_replace('#tpRemtDepTranCnt#', 0, $req);
                    $req = str_replace('#tpRemtDepTranAmtHigh#', 0, $req);
                    $req = str_replace('#tpRemtWdrTranCnt#', 0, $req);
                    $req = str_replace('#tpRemtWdrTranAmtHigh#', 0, $req);
                    $tpAsonDate = now()->format('d-m-Y');
                    $req = str_replace("#tpAsonDate#", $tpAsonDate, $req);
                }

                foreach ($issueAPICommonConfig as $ikeyField => $commConfigVal) {
                    $req = str_replace($ikeyField, $commConfigVal, $req);
                }
                /*  Acc Lvl API ID UAT : 16  */
                if ($api_id == 16) {
                    $accRes = CIFModificationService::accInquiryApiResponse(['issue_id' => $issue_id, 'acc_no' => $account_number, 'ref_no' => $ref_no, 'cif_no' => $cif_no]);
                    if (!empty($accRes)) {
                        $req = str_replace('#acctStmtNxtPrintDt#', $acctStmtNxtPrintDt, $req);
                        $req = str_replace('#name#', $accRes['name'], $req);
                        $req = str_replace('#schmCode#', $accRes['schmCode'], $req);
                        $req = str_replace('#schmType#', $accRes['schmType'], $req);
                        $req = str_replace('#acctCurr#', $accRes['acctInqAcctCurr'], $req);
                        $req = str_replace('#branchId#', $accRes['branchId'], $req);
                        $req = str_replace('#acctName#', $accRes['acctName'], $req);
                        $req = str_replace('#acctShortName#', $accRes['acctShortName'], $req);
                        $req = str_replace('#acctStmtMode#', $accRes['acctStmtMode'], $req);
                        $req = str_replace('#cal#', 'G', $req);
                        $req = str_replace('#type#', 'H', $req);
                        $req = str_replace('#startDt#', '31', $req);
                        $req = str_replace('#holStat#', 'P', $req);
                        $req = str_replace('#weekDay#', '0', $req);
                        $req = str_replace('#weekNum#', '', $req);
                        if (!empty($accRes['relEmailAddress'])) {
                            $req = str_replace('#despatchMode#', 'A', $req);
                        } else {
                            $req = str_replace('#despatchMode#', 'P', $req);
                        }
                        $req = str_replace('#modeOfOperCode#', $accRes['modeOfOperCode'], $req);
                        $req = str_replace('#servChargeCollFlag#', $accRes['servChargeCollFlag'], $req);
                        $req = str_replace('#localCalFlag#', $accRes['localCalFlag'], $req);
                        $req = str_replace('#intCrAcctFlg#', $accRes['intCrAcctFlg'], $req);
                        $req = str_replace('#intDrAcctFlg#', $accRes['intDrAcctFlg'], $req);
                        $req = str_replace('#wTaxAmountScopeFlg#', 'P', $req);
                        $req = str_replace('#nextIntTrunDateCr#', $accRes['nextIntTrunDateCr'], $req);
                        $req = str_replace('#nextInTrunDateDr#', $accRes['nextInTrunDateDr'], $req);
                        $req = str_replace('#nomAvlblFlag#', $accRes['nomAvlblFlag'], $req);
                        $req = str_replace('#chqProvByBankflg#', $accRes['chqProvByBankflg'], $req);
                        $req = str_replace('#atmFlg#', $accRes['atmFlg'], $req);
                        $req = str_replace('#bkSectorCode#', $accRes['bkSectorCode'], $req);
                        $req = str_replace('#cenBkSectorCode#', $accRes['cenBkSectorCode'], $req);
                        $req = str_replace('#norPsoUnitId#', $accRes['norPsoUnitId'], $req);
                        $req = str_replace('#monitoringPsoUnitId#', $accRes['monitoringPsoUnitId'], $req);
                        $req = str_replace('#tpCashDepTranCnt#', $accRes['tpCashDepTranCnt'], $req);
                        $req = str_replace('#tpCashDepTranAmtHigh#', $accRes['tpCashDepTranAmtHigh'], $req);
                        $req = str_replace('#tpCashDepTranAmtTot#', $accRes['tpCashDepTranAmtTot'], $req);
                        $req = str_replace('#tpXferDepTranCnt#', $accRes['tpXferDepTranCnt'], $req);
                        $req = str_replace('#tpXferDepTranAmtHigh#', $accRes['tpXferDepTranAmtHigh'], $req);
                        $req = str_replace('#tpXferDepTranAmtTot#', $accRes['tpXferDepTranAmtTot'], $req);
                        $req = str_replace('#tpTotlDepTranCnt#', $accRes['tpTotlDepTranCnt'], $req);
                        $req = str_replace('#tpTotlDepTranAmtHigh#', $accRes['tpTotlDepTranAmtHigh'], $req);
                        $req = str_replace('#tpTotlDepTranAmtTot#', $accRes['tpTotlDepTranAmtTot'], $req);
                        $req = str_replace('#tpCashWdrTranCnt#', $accRes['tpCashWdrTranCnt'], $req);
                        $req = str_replace('#tpCashWdrTranAmtHigh#', $accRes['tpCashWdrTranAmtHigh'], $req);
                        $req = str_replace('#tpCashWdrTranAmtTot#', $accRes['tpCashWdrTranAmtTot'], $req);
                        $req = str_replace('#tpXferWdrTranCnt#', $accRes['tpXferWdrTranCnt'], $req);
                        $req = str_replace('#tpXferWdrTranAmtHigh#', $accRes['tpXferWdrTranAmtHigh'], $req);
                        $req = str_replace('#tpXferWdrTranAmtTot#', $accRes['tpXferWdrTranAmtTot'], $req);
                        $req = str_replace('#tpTotlWdrTranCnt#', $accRes['tpTotlWdrTranCnt'], $req);
                        $req = str_replace('#tpTotlWdrTranAmtHigh#', $accRes['tpTotlWdrTranAmtHigh'], $req);
                        $req = str_replace('#tpTotlWdrTranAmtTot#', $accRes['tpTotlWdrTranAmtTot'], $req);
                        $req = str_replace('#callCFlg#', $accRes['callCFlg'], $req);
                        $req = str_replace('#creditCFlg#', $accRes['creditCFlg'], $req);
                        $req = str_replace('#visaFlg#', $accRes['visaFlg'], $req);
                        $req = str_replace('#pcashFlg#', $accRes['pcashFlg'], $req);
                        $req = str_replace('#monTotDepLmt#', $accRes['monTotDepLmt'], $req);
                        $req = str_replace('#monCashDepLmt#', $accRes['monCashDepLmt'], $req);
                        $req = str_replace('#mnetFlg#', $accRes['mnetFlg'], $req);
                        $req = str_replace('#smsFlg#', $accRes['smsFlg'], $req);
                        $req = str_replace('#posFlg#', $accRes['posFlg'], $req);
                        $req = str_replace('#inetFlg#', $accRes['inetFlg'], $req);
                        $req = str_replace('#ecoSectorCode#', $accRes['ecoSectorCode'], $req);
                        $req = str_replace('#fundTfrLmt#', $accRes['fundTfrLmt'], $req);
                        $req = str_replace('#pCashLmt#', $accRes['pCashLmt'], $req);
                        $req = str_replace('#purchaseLmt#', $accRes['purchaseLmt'], $req);
                        $req = str_replace('#atmAmtLmt#', $accRes['atmAmtLmt'], $req);
                        $req = str_replace('#norPsoUnitId#', $accRes['norPsoUnitId'], $req);
                        $req = str_replace('#monitoringPsoUnitId#', $accRes['monitoringPsoUnitId'], $req);
                        $req = str_replace('#nomineeDetails#', $accRes['nomineeDetails'], $req);
                        $req = str_replace('#rmks#', $accRes['rmks'], $req);
                        $req = str_replace('#acctActDate#', $acctActDate, $req);
                        $req = str_replace('#ccCode#', $accRes['ccCode'], $req);
                        $req = str_replace('#bkSectorName#', $accRes['bkSectorName'], $req);
                        $req = str_replace('#cenBkSectorName#', $accRes['cenBkSectorName'], $req);
                        $req = str_replace('#ecoSectorName#', $accRes['ecoSectorName'], $req);
                        $req = str_replace('#norPsoUnitName#', $accRes['norPsoUnitName'], $req);
                        $req = str_replace('#monitoringPsoUnitName#', $accRes['monitoringPsoUnitName'], $req);
                        $req = str_replace('#ccDesc#', $accRes['ccDesc'], $req);
                        $req = str_replace('#tpRemtDepTranCnt#', $accRes['tpRemtDepTranCnt'], $req);
                        $req = str_replace('#tpRemtDepTranAmtHigh#', $accRes['tpRemtDepTranAmtHigh'], $req);
                        $req = str_replace('#tpRemtDepTranAmtTot#', $accRes['tpRemtDepTranAmtTot'], $req);
                        $req = str_replace('#tpExptDepTranCnt#', $accRes['tpExptDepTranCnt'], $req);
                        $req = str_replace('#tpExptDepTranAmtHigh#', $accRes['tpExptDepTranAmtHigh'], $req);
                        $req = str_replace('#tpExptDepTranAmtTot#', $accRes['tpExptDepTranAmtTot'], $req);
                        $req = str_replace('#tpOthrDepTranCnt#', $accRes['tpOthrDepTranCnt'], $req);
                        $req = str_replace('#tpOthrDepTranAmtHigh#', $accRes['tpOthrDepTranAmtHigh'], $req);
                        $req = str_replace('#tpOthrDepTranAmtTot#', $accRes['tpOthrDepTranAmtTot'], $req);
                        $req = str_replace('#tpOthrDepReason#', $accRes['tpOthrDepReason'], $req);
                        $req = str_replace('#tpRemtWdrTranCnt#', $accRes['tpRemtWdrTranCnt'], $req);
                        $req = str_replace('#tpRemtWdrTranAmtHigh#', $accRes['tpRemtWdrTranAmtHigh'], $req);
                        $req = str_replace('#tpRemtWdrTranAmtTot#', $accRes['tpRemtWdrTranAmtTot'], $req);
                        $req = str_replace('#tpExptWdrTranCnt#', $accRes['tpExptWdrTranCnt'], $req);
                        $req = str_replace('#tpExptWdrTranAmtHigh#', $accRes['tpExptWdrTranAmtHigh'], $req);
                        $req = str_replace('#tpExptWdrTranAmtTot#', $accRes['tpExptWdrTranAmtTot'], $req);
                        $req = str_replace('#tpOthrWdrTranCnt#', $accRes['tpOthrWdrTranCnt'], $req);
                        $req = str_replace('#tpOthrWdrTranAmtHigh#', $accRes['tpOthrWdrTranAmtHigh'], $req);
                        $req = str_replace('#tpOthrWdrTranAmtTot#', $accRes['tpOthrWdrTranAmtTot'], $req);
                        $req = str_replace('#tpOthrWdrReason#', $accRes['tpOthrWdrReason'], $req);
                        $req = str_replace('#tpSharDepTranCnt#', $accRes['tpSharDepTranCnt'], $req);
                        $req = str_replace('#tpSharDepTranAmtHigh#', $accRes['tpSharDepTranAmtHigh'], $req);
                        $req = str_replace('#tpSharDepTranAmtTot#', $accRes['tpSharDepTranAmtTot'], $req);
                        $req = str_replace('#tpSharWdrTranCnt#', $accRes['tpSharWdrTranCnt'], $req);
                        $req = str_replace('#tpSharWdrTranAmtHigh#', $accRes['tpSharWdrTranAmtHigh'], $req);
                        $req = str_replace('#tpSharWdrTranAmtTot#', $accRes['tpSharWdrTranAmtTot'], $req);
                        $req = str_replace('#smsEnrolementDate#', $accRes['smsEnrolementDate'], $req);
                        $req = str_replace('#smsDeEnrolementDate#', $accRes['smsDeEnrolementDate'], $req);
                        $req = str_replace('#smsWaiverFlg#', $accRes['smsWaiverFlg'], $req);
                        $req = str_replace('#smsWaiverDate#', $accRes['smsWaiverDate'], $req);
                        $req = str_replace('#refPsoUnitId#', $accRes['refPsoUnitId'], $req);
                        $req = str_replace('#refPsoUnitName#', $accRes['refPsoUnitName'], $req);
                        $req = str_replace('#aquAgentId#', $accRes['aquAgentId'], $req);
                        $req = str_replace('#monAgentId#', $accRes['monAgentId'], $req);
                        $req = str_replace('#cardType#', $accRes['cardType'], $req);
                        $req = str_replace('#emboseName#', $accRes['emboseName'], $req);
                        $req = str_replace('#freeCode1#', $accRes['freeCode1'], $req);
                        $req = str_replace('#freeCode2#', $accRes['freeCode2'], $req);
                        $req = str_replace('#freeCode3#', $accRes['freeCode3'], $req);
                        $req = str_replace('#freeCode4#', $accRes['freeCode4'], $req);
                        $req = str_replace('#freeCode5#', $accRes['freeCode5'], $req);
                        $req = str_replace('#freeCode6#', $accRes['freeCode6'], $req);
                        $req = str_replace('#freeCode7#', $accRes['freeCode7'], $req);
                        $req = str_replace('#freeCode8#', $accRes['freeCode8'], $req);
                        $req = str_replace('#freeCode9#', $accRes['freeCode9'], $req);
                        $req = str_replace('#freeText1#', $accRes['freeText1'], $req);
                        $req = str_replace('#freeText2#', $accRes['freeText2'], $req);
                        $req = str_replace('#freeText3#', $accRes['freeText3'], $req);
                        $req = str_replace('#freeText4#', $accRes['freeText4'], $req);
                        $req = str_replace('#freeText5#', $accRes['freeText5'], $req);
                        $req = str_replace('#freeText6#', $accRes['freeText6'], $req);
                        $req = str_replace('#freeCode1Desc#', $accRes['freeCode1Desc'], $req);
                        $req = str_replace('#freeCode2Desc#', $accRes['freeCode2Desc'], $req);
                        $req = str_replace('#freeCode3Desc#', $accRes['freeCode3Desc'], $req);
                        $req = str_replace('#freeCode4Desc#', $accRes['freeCode4Desc'], $req);
                        $req = str_replace('#freeCode5Desc#', $accRes['freeCode5Desc'], $req);
                        $req = str_replace('#freeCode6Desc#', $accRes['freeCode6Desc'], $req);
                        $req = str_replace('#tpAsonDate#', $accRes['tpAsonDate'], $req);
                        $req = str_replace('#cibRptFlg#', $accRes['cibRptFlg'], $req);
                        $req = str_replace('#cibDataStatus#', $accRes['cibDataStatus'], $req);
                        $req = str_replace('#cibDataModDate#', $accRes['cibDataModDate'], $req);
                        $req = str_replace('#relPartyRecDelFlg#', $accRes['relPartyRecDelFlg'], $req);
                        $req = str_replace('#relPartyType#', $accRes['relPartyType'], $req);
                        $req = str_replace('#relPartyTypeDesc#', $accRes['relPartyTypeDesc'], $req);
                        $req = str_replace('#relPartyCode#', $accRes['relPartyCode'], $req);
                        $req = str_replace('#relPartyCodeDesc#', $accRes['relPartyCodeDesc'], $req);
                        $req = str_replace('#relPartyRecCustId#', $accRes['relPartyRecCustId'], $req);
                        $req = str_replace('#relPartyRecLastName#', $accRes['relPartyRecLastName'], $req);
                        $req = str_replace('#relPartyRecFirstName#', $accRes['relPartyRecFirstName'], $req);
                        $req = str_replace('#relPartyRecMiddleName#', $accRes['relPartyRecMiddleName'], $req);
                        $req = str_replace('#relPartyRecName#', $accRes['relPartyRecName'], $req);
                        $req = str_replace('#relPartyRecTitlePrefix#', $accRes['relPartyRecTitlePrefix'], $req);
                        $req = str_replace('#relPartyRecTelephoneNum#', $accRes['relPartyRecTelephoneNum'], $req);
                        $req = str_replace('#relPartyRecEmailAddr#', $accRes['relPartyRecEmailAddr'], $req);
                        $req = str_replace('#relPartyFaxNum#', $accRes['relPartyFaxNum'], $req);
                        $req = str_replace('#relPartyTelexNum#', $accRes['relPartyTelexNum'], $req);
                        $req = str_replace('#relPartyRecAddr1#', $accRes['relPartyRecAddr1'], $req);
                        $req = str_replace('#relPartyRecAddr2#', $accRes['relPartyRecAddr2'], $req);
                        $req = str_replace('#relPartyRecAddr3#', $accRes['relPartyRecAddr3'], $req);
                        $req = str_replace('#relPartyRecCity#', $accRes['relPartyRecCity'], $req);
                        $req = str_replace('#relPartyRecStateProv#', $accRes['relPartyRecStateProv'], $req);
                        $req = str_replace('#relPartyRecPostalCode#', $accRes['relPartyRecPostalCode'], $req);
                        $req = str_replace('#relPartyRecCountry#', $accRes['relPartyRecCountry'], $req);
                        $req = str_replace('#relPartyRecAddrType#', $accRes['relPartyRecAddrType'], $req);

                        /*  Nominee Update Issue ID UAT 544  */
                        /*  Nominee Update Issue ID Live 1176  */
                        if ($issue_id == 544) {
                            if (!empty($accRes['nomineeinfo'])) {
                                foreach($accRes['nomineeinfo'] AS $nomineeInfo) {
                                    if (trim($nomineeInfo['regNum']) != '') {
                                        $regNum = $nomineeInfo['regNum'];
                                        $extraNode .= '<v1:nomineeInfoDetails>
                                                  <v1:regNum>' . $nomineeInfo['regNum'] . '</v1:regNum>
                                                  <v1:nomineeName>' . $nomineeInfo['nomineeName'] . '</v1:nomineeName>
                                                  <v1:relType>' . $nomineeInfo['relType'] . '</v1:relType>
                                                  <v1:NomineeContactInfo>
                                                     <v1:nomineeTelephoneNumDetails>
                                                        <v1:nomineeTelephoneNum>' . $nomineeInfo['nomineeTelephoneNum'] . '</v1:nomineeTelephoneNum>
                                                     </v1:nomineeTelephoneNumDetails>
                                                     <v1:nomineeEmailAddr>' . $nomineeInfo['nomineeEmailAddr'] . '</v1:nomineeEmailAddr>
                                                     <v1:nomineeFaxNum>' . $nomineeInfo['nomineeFaxNum'] . '</v1:nomineeFaxNum>
                                                     <v1:nomineeTelexNum>' . $nomineeInfo['nomineeTelexNum'] . '</v1:nomineeTelexNum>
                                                     <v1:nomineePostAddrdetails>
                                                        <v1:nomineeAddr1>' . $nomineeInfo['nomineeAddressTypeAddress1'] . '</v1:nomineeAddr1>
                                                        <v1:nomineeAddr2>' . $nomineeInfo['nomineeAddressTypeAddress2'] . '</v1:nomineeAddr2>
                                                        <v1:nomineeAddr3>' . $nomineeInfo['nomineeAddressTypeAddress3'] . '</v1:nomineeAddr3>
                                                        <v1:nomineeCity>' . $nomineeInfo['nomineeAddressTypeCity'] . '</v1:nomineeCity>
                                                        <v1:nomineeStateProv>' . $nomineeInfo['nomineeAddressTypeStateProv'] . '</v1:nomineeStateProv>
                                                        <v1:nomineePostalCode>' . $nomineeInfo['nomineeAddressTypePostalCode'] . '</v1:nomineePostalCode>
                                                        <v1:nomineeCountry>' . $nomineeInfo['nomineeAddressTypeCountry'] . '</v1:nomineeCountry>
                                                        <v1:nomineeAddrType>' . $nomineeInfo['nomineeAddrType'] . '</v1:nomineeAddrType>
                                                     </v1:nomineePostAddrdetails>
                                                  </v1:NomineeContactInfo>
                                                  <v1:nomineeMinorFlg>' . $nomineeInfo['nomineeMinorFlg'] . '</v1:nomineeMinorFlg>
                                                  <v1:nomineeBirthDt>' . $nomineeInfo['nomineeBirthDt'] . '</v1:nomineeBirthDt>
                                                  <v1:nomineePercentValue>' . $nomineeInfo['nomineePercent'] . '</v1:nomineePercentValue>
                                                  <v1:recDelFlg>Y</v1:recDelFlg>
                                                  <v1:guardianInfoDetails>
                                                     <v1:guardianCode>' . $nomineeInfo['guardianCode'] . '</v1:guardianCode>
                                                     <v1:guardianName>' . $nomineeInfo['guardianName'] . '</v1:guardianName>
                                                     <v1:guardianContactInfoDetails>
                                                        <v1:guardianTelephoneNum>' . $nomineeInfo['guardianTelephoneNum'] . '</v1:guardianTelephoneNum>
                                                     </v1:guardianContactInfoDetails>
                                                     <v1:guardianEmailAddr>' . $nomineeInfo['guardianEmailAddr'] . '</v1:guardianEmailAddr>
                                                     <v1:guardianFaxNum>' . $nomineeInfo['guardianFaxNum'] . '</v1:guardianFaxNum>
                                                     <v1:guardianTelexNum>' . $nomineeInfo['guardianTelexNum'] . '</v1:guardianTelexNum>
                                                     <v1:guardianPostAddrDetails>
                                                        <v1:guardianAddr1>' . $nomineeInfo['guardianAddressTypeAddress1'] . '</v1:guardianAddr1>
                                                        <v1:guardianAddr2>' . $nomineeInfo['guardianAddressTypeAddress2'] . '</v1:guardianAddr2>
                                                        <v1:guardianAddr3>' . $nomineeInfo['guardianAddressTypeAddress3'] . '</v1:guardianAddr3>
                                                        <v1:guardianCity>' . $nomineeInfo['guardianAddressTypeCity'] . '</v1:guardianCity>
                                                        <v1:guardianStateProv>' . $nomineeInfo['guardianAddressTypeStateProv'] . '</v1:guardianStateProv>
                                                        <v1:guardianPostalCode>' . $nomineeInfo['guardianAddressTypePostalCode'] . '</v1:guardianPostalCode>
                                                        <v1:guardianCountry>' . $nomineeInfo['guardianAddressTypeCountry'] . '</v1:guardianCountry>
                                                        <v1:guardianAddrType>' . $nomineeInfo['guardianAddrType'] . '</v1:guardianAddrType>
                                                     </v1:guardianPostAddrDetails>
                                                  </v1:guardianInfoDetails>
                                               </v1:nomineeInfoDetails>';
                                    }
                                }
                            }
                            $req = str_replace('#regNum#', $regNum, $req);
                        }
                    }
                    $extraNode = trim(preg_replace('/\s\s+/', ' ', $extraNode));
                    $req = str_replace("<extraNode>extraNode</extraNode>", $extraNode, $req);
                    $req = cifApiNullExtractorAcc($req, $extraFieldWithIssueConfig);
                } elseif ($api_id == 18) {
                    /*  Lien Update Issue ID 893 &  API ID UAT : 18  */
                    $accRes = CIFModificationService::accInquiryApiResponse(['issue_id' => $issue_id, 'acc_no' => $account_number, 'ref_no' => $ref_no, 'cif_no' => $cif_no]);
                    if (!empty($accRes)) {
                        $req = str_replace('#name#', $accRes['name'], $req);
                        $req = str_replace('#schmCode#', $accRes['schmCode'], $req);
                        $req = str_replace('#schmType#', $accRes['schmType'], $req);
                        $req = str_replace('#acctCurr#', $accRes['acctCurr'], $req);
                        $req = str_replace('#currencyCode#', $accRes['acctCurr'], $req);
                        $req = str_replace('#bankId#', $accRes['bankId'], $req);
                        $req = str_replace('#branchId#', $accRes['branchId'], $req);
                        $req = str_replace('#branchName#', $accRes['branchName'], $req);
                        $req = str_replace('#originalGlobalTransactionId#', '', $req);
                        $req = str_replace('#originalTimestamp#', '', $req);
                        $req = str_replace('#addr1#', $accRes['addr1'], $req);
                        $req = str_replace('#addr2#', $accRes['addr2'], $req);
                        $req = str_replace('#addr3#', $accRes['addr3'], $req);
                        $req = str_replace('#city#', $accRes['city'], $req);
                        $req = str_replace('#stateProv#', $accRes['stateProv'], $req);
                        $req = str_replace('#postalCode#', $accRes['postalCode'], $req);
                        $req = str_replace('#country#', $accRes['country'], $req);
                        $req = str_replace('#addrType#', $accRes['addrType'], $req);
                    }
                    $req = cifApiNullExtractorAcc($req, $extraFieldWithIssueConfig);
                } else {
                    $req = cifApiNullExtractor($req);
                }

                /*  Name change Issue ID UAT : 128,1080  */
                /*  Name change Issue ID Live : 1165,1173  */
                if (($issue_id == 128 || $issue_id == 1080) && ($api_id == 8)) {
                    if (!empty($firstname)) {
                        $req = str_replace("<v1:firstName>firstName</v1:firstName>", '<v1:firstName>'.$firstname.'</v1:firstName>', $req);
                    } else {
                        $req = str_replace("<v1:firstName>firstName</v1:firstName>", '<v1:firstName>null</v1:firstName>', $req);
                    }
                    if (!empty($middlename)) {
                        $req = str_replace("<v1:middleName>middleName</v1:middleName>", '<v1:middleName>'.$middlename.'</v1:middleName>', $req);
                    } else {
                        $req = str_replace("<v1:middleName>middleName</v1:middleName>", '<v1:middleName>null</v1:middleName>', $req);
                    }
                    if (!empty($lastname)) {
                        $req = str_replace("<v1:lastName>lastName</v1:lastName>", '<v1:lastName>'.$lastname.'</v1:lastName>', $req);
                    } else {
                        $req = str_replace("<v1:lastName>lastName</v1:lastName>", '', $req);
                    }
                    if (!empty($shortName)) {
                        $req = str_replace("<v1:shortName>shortName</v1:shortName>", '<v1:shortName>'.$shortName.'</v1:shortName>', $req);
                    } else {
                        $req = str_replace("<v1:shortName>shortName</v1:shortName>", '', $req);
                    }
                }
                /*  Mobile Phone Change Issue ID UAT 119,518,1081  */
                /*  Foreign Mobile Phone Change Issue ID Live 1177  */
                /*  Mobile Phone Change Issue ID Live 1153,1162  */
                if (($issue_id == 119 || $issue_id == 518 || $issue_id == 1081) && ($api_id == 8)) {
                    foreach ($extraNodeArray as $k => $v) {
                        if ($k == 'phoneEmailType') {
                            $type = $v;
                        }
                        if ($k == 'phoneNumCountryCode') {
                            $cun = $v;
                        }
                        if ($k == 'phoneNumCityCode') {
                            $cty = $v;
                        }
                        if ($k == 'phoneNum') {
                            $num = $v;
                        }
                    }
                    if ($type == 'CELLPH') {
                        $extraNode .= '<v1:phoneEmailDetailsList>';
                        $extraNode .= '<v1:phoneEmailDetails>
                                        <v1:phoneOrEmail>PHONE</v1:phoneOrEmail>
                                        <v1:phoneEmailType>CELLPH</v1:phoneEmailType>
                                        <v1:startDt>' . $sourceTimestamp . '</v1:startDt>
                                        <v1:phoneNum>' . $num . '</v1:phoneNum>
                                        <v1:phoneNumCountryCode>88</v1:phoneNumCountryCode>
                                        <v1:phoneNumCityCode>00</v1:phoneNumCityCode>
                                        <v1:phoneNumLocalCode>' . $num . '</v1:phoneNumLocalCode>
                                        <v1:prefFlag>N</v1:prefFlag>
                                    </v1:phoneEmailDetails>';
                        $extraNode .= '<v1:phoneEmailDetails>
                                        <v1:phoneOrEmail>PHONE</v1:phoneOrEmail>
                                        <v1:phoneEmailType>COMMPH1</v1:phoneEmailType>
                                        <v1:startDt>' . $sourceTimestamp . '</v1:startDt>
                                        <v1:phoneNum>' . $num . '</v1:phoneNum>
                                        <v1:phoneNumCountryCode>88</v1:phoneNumCountryCode>
                                        <v1:phoneNumCityCode>00</v1:phoneNumCityCode>
                                        <v1:phoneNumLocalCode>' . $num . '</v1:phoneNumLocalCode>
                                        <v1:prefFlag>Y</v1:prefFlag>
                                    </v1:phoneEmailDetails>';
                        $extraNode .= '</v1:phoneEmailDetailsList>';
                    } elseif ($type == 'COMMPH1') {
                        $extraNode .= '<v1:phoneEmailDetailsList>';
                        $extraNode .= '<v1:phoneEmailDetails>
                                        <v1:phoneOrEmail>PHONE</v1:phoneOrEmail>
                                        <v1:phoneEmailType>CELLPH</v1:phoneEmailType>
                                        <v1:startDt>' . $sourceTimestamp . '</v1:startDt>
                                        <v1:phoneNum>' . $num . '</v1:phoneNum>
                                        <v1:phoneNumCountryCode>88</v1:phoneNumCountryCode>
                                        <v1:phoneNumCityCode>00</v1:phoneNumCityCode>
                                        <v1:phoneNumLocalCode>' . $num . '</v1:phoneNumLocalCode>
                                        <v1:prefFlag>N</v1:prefFlag>
                                    </v1:phoneEmailDetails>';
                        $extraNode .= '<v1:phoneEmailDetails>
                                        <v1:phoneOrEmail>PHONE</v1:phoneOrEmail>
                                        <v1:phoneEmailType>COMMPH1</v1:phoneEmailType>
                                        <v1:startDt>' . $sourceTimestamp . '</v1:startDt>
                                        <v1:phoneNum>' . $num . '</v1:phoneNum>
                                        <v1:phoneNumCountryCode>88</v1:phoneNumCountryCode>
                                        <v1:phoneNumCityCode>00</v1:phoneNumCityCode>
                                        <v1:phoneNumLocalCode>' . $num . '</v1:phoneNumLocalCode>
                                        <v1:prefFlag>Y</v1:prefFlag>
                                    </v1:phoneEmailDetails>';
                        $extraNode .= '</v1:phoneEmailDetailsList>';
                    } elseif ($type == 'COMMPH2') {
                        $extraNode .= '<v1:phoneEmailDetailsList>';
                        $extraNode .= '<v1:phoneEmailDetails>
                                        <v1:phoneOrEmail>PHONE</v1:phoneOrEmail>
                                        <v1:phoneEmailType>COMMPH2</v1:phoneEmailType>
                                        <v1:startDt>' . $sourceTimestamp . '</v1:startDt>
                                        <v1:phoneNum>' . $num . '</v1:phoneNum>
                                        <v1:phoneNumCountryCode>88</v1:phoneNumCountryCode>
                                        <v1:phoneNumCityCode>00</v1:phoneNumCityCode>
                                        <v1:phoneNumLocalCode>' . $num . '</v1:phoneNumLocalCode>
                                        <v1:prefFlag>N</v1:prefFlag>
                                    </v1:phoneEmailDetails>';
                        $extraNode .= '</v1:phoneEmailDetailsList>';
                    } elseif ($type == 'CPPH1') {
                        $extraNode .= '<v1:phoneEmailDetailsList>';
                        $extraNode .= '<v1:phoneEmailDetails>
                                        <v1:phoneOrEmail>PHONE</v1:phoneOrEmail>
                                        <v1:phoneEmailType>CPPH1</v1:phoneEmailType>
                                        <v1:startDt>' . $sourceTimestamp . '</v1:startDt>
                                        <v1:phoneNum>' . $num . '</v1:phoneNum>
                                        <v1:phoneNumCountryCode>88</v1:phoneNumCountryCode>
                                        <v1:phoneNumCityCode>00</v1:phoneNumCityCode>
                                        <v1:phoneNumLocalCode>' . $num . '</v1:phoneNumLocalCode>
                                        <v1:prefFlag>N</v1:prefFlag>
                                    </v1:phoneEmailDetails>';
                        $extraNode .= '</v1:phoneEmailDetailsList>';
                    } elseif ($type == 'CPPH2') {
                        $extraNode .= '<v1:phoneEmailDetailsList>';
                        $extraNode .= '<v1:phoneEmailDetails>
                                        <v1:phoneOrEmail>PHONE</v1:phoneOrEmail>
                                        <v1:phoneEmailType>CPPH2</v1:phoneEmailType>
                                        <v1:startDt>' . $sourceTimestamp . '</v1:startDt>
                                        <v1:phoneNum>' . $num . '</v1:phoneNum>
                                        <v1:phoneNumCountryCode>88</v1:phoneNumCountryCode>
                                        <v1:phoneNumCityCode>00</v1:phoneNumCityCode>
                                        <v1:phoneNumLocalCode>' . $num . '</v1:phoneNumLocalCode>
                                        <v1:prefFlag>N</v1:prefFlag>
                                    </v1:phoneEmailDetails>';
                        $extraNode .= '</v1:phoneEmailDetailsList>';
                    } elseif ($type == 'FORGNPHN01') {
                        $extraNode .= '<v1:phoneEmailDetailsList>';
                        $extraNode .= '<v1:phoneEmailDetails>
                                        <v1:phoneOrEmail>PHONE</v1:phoneOrEmail>
                                        <v1:phoneEmailType>' . $type . '</v1:phoneEmailType>
                                        <v1:startDt>' . $sourceTimestamp . '</v1:startDt>
                                        <v1:phoneNum>' . $num . '</v1:phoneNum>
                                        <v1:phoneNumCountryCode>' . $cun . '</v1:phoneNumCountryCode>
                                        <v1:phoneNumCityCode>' . $cty . '</v1:phoneNumCityCode>
                                        <v1:phoneNumLocalCode>' . $num . '</v1:phoneNumLocalCode>
                                        <v1:prefFlag>Y</v1:prefFlag>
                                    </v1:phoneEmailDetails>';
                        $extraNode .= '</v1:phoneEmailDetailsList>';
                    } else {
                        $extraNode .= '<v1:phoneEmailDetailsList>';
                        $extraNode .= '<v1:phoneEmailDetails>
                                        <v1:phoneOrEmail>PHONE</v1:phoneOrEmail>
                                        <v1:phoneEmailType>' . $type . '</v1:phoneEmailType>
                                        <v1:startDt>' . $sourceTimestamp . '</v1:startDt>
                                        <v1:phoneNum>' . $num . '</v1:phoneNum>
                                        <v1:phoneNumCountryCode>88</v1:phoneNumCountryCode>
                                        <v1:phoneNumCityCode>00</v1:phoneNumCityCode>
                                        <v1:phoneNumLocalCode>' . $num . '</v1:phoneNumLocalCode>
                                        <v1:prefFlag>N</v1:prefFlag>
                                    </v1:phoneEmailDetails>';
                        $extraNode .= '</v1:phoneEmailDetailsList>';
                    }
                    $extraNode = trim(preg_replace('/\s\s+/', ' ', $extraNode));
                    $req = str_replace("<extraNode>extraNode</extraNode>", $extraNode, $req);
                }
                /*  Email Address Change Issue ID UAT 1077,120  */
                /*  Email Address Change Issue ID Live 1160,1161  */
                if (($issue_id == 1077 || $issue_id == 120) && ($api_id == 8)) {
                    foreach ($extraNodeArray as $k => $v) {
                        if ($k == 'phoneEmailType') {
                            $type = $v;
                        }
                        if ($k == 'emailInfo') {
                            $eml = $v;
                        }
                    }
                    if ($type == 'COMMEML') {
                        $extraNode .= '<v1:phoneEmailDetailsList>';
                        $extraNode .= '<v1:phoneEmailDetails>
                                        <v1:phoneOrEmail>EMAIL</v1:phoneOrEmail>
                                        <v1:phoneEmailType>' . $type . '</v1:phoneEmailType>
                                        <v1:startDt>' . $sourceTimestamp . '</v1:startDt>
                                        <v1:emailInfo>' . $eml . '</v1:emailInfo>
                                        <v1:prefFlag>Y</v1:prefFlag>
                                    </v1:phoneEmailDetails>';
                        $extraNode .= '</v1:phoneEmailDetailsList>';
                    } else {
                        $extraNode .= '<v1:phoneEmailDetailsList>';
                        $extraNode .= '<v1:phoneEmailDetails>
                                        <v1:phoneOrEmail>EMAIL</v1:phoneOrEmail>
                                        <v1:phoneEmailType>' . $type . '</v1:phoneEmailType>
                                        <v1:startDt>' . $sourceTimestamp . '</v1:startDt>
                                        <v1:emailInfo>' . $eml . '</v1:emailInfo>
                                        <v1:prefFlag>N</v1:prefFlag>
                                    </v1:phoneEmailDetails>';
                        $extraNode .= '</v1:phoneEmailDetailsList>';
                    }
                    $extraNode = trim(preg_replace('/\s\s+/', ' ', $extraNode));
                    $req = str_replace("<extraNode>extraNode</extraNode>", $extraNode, $req);
                }
                /*  Address Change Issue ID UAT 1019,1078 for finacle */
                /*  Address Change Issue ID Live 1169,1172 for finacle */
                if (($issue_id == 1019 || $issue_id == 1078) && $api_id == 8) {
                    foreach ($extraNodeArray as $k => $v) {
                        if ($k == 'addrCategory') {
                            $type = $v;
                        }
                        if ($k == 'prefAddr') {
                            $pref = $v;
                        }
                        if ($k == 'addrLine1') {
                            $add1 = $v;
                        }
                        if ($k == 'addrLine2') {
                            $add2 = $v;
                        }
                        if ($k == 'addrLine3') {
                            $add3 = $v;
                        }
                        if ($k == 'country') {
                            $cun = $v;
                        }
                        if ($k == 'city') {
                            $cty = $v;
                        }
                        if ($k == 'state') {
                            $stat = $v;
                        }
                        if ($k == 'postalCode') {
                            $posCode = $v;
                        }
                        if ($k == 'town') {
                            $twn = $v;
                        }
                    }
                    if (empty($add1)) {
                        $add1 = 'null';
                    }
                    if (empty($add2)) {
                        $add2 = 'null';
                    }
                    if (empty($add3)) {
                        $add3 = 'null';
                    }
                    if ($pref == 'Y') {
                        $extraNode .= '<v1:addressDetailsList>';
                        $extraNode .= '<v1:addressDetails>
                                        <v1:addrLine1>' . $add1 . '</v1:addrLine1>
                                        <v1:addrLine2>' . $add2 . '</v1:addrLine2>
                                        <v1:addrLine3>' . $add3 . '</v1:addrLine3>
                                        <v1:addrStartDt>' . $sourceTimestamp . '</v1:addrStartDt>
                                        <v1:addrCategory>' . $type . '</v1:addrCategory>
                                        <v1:freeTextLabel>' . $type . '</v1:freeTextLabel>
                                        <v1:city>' . $cty . '</v1:city>
                                        <v1:country>' . $cun . '</v1:country>
                                        <v1:holdMailFlag>N</v1:holdMailFlag>
                                        <v1:prefAddr>N</v1:prefAddr>
                                        <v1:prefFormat>FREE_TEXT_FORMAT</v1:prefFormat>
                                        <v1:state>' . $stat . '</v1:state>
                                        <v1:town>' . $twn . '</v1:town>
                                        <v1:postalCode>' . $posCode . '</v1:postalCode>
                                    </v1:addressDetails>';
                        $extraNode .= '<v1:addressDetails>
                                        <v1:addrLine1>' . $add1 . '</v1:addrLine1>
                                        <v1:addrLine2>' . $add2 . '</v1:addrLine2>
                                        <v1:addrLine3>' . $add3 . '</v1:addrLine3>
                                        <v1:addrStartDt>' . $sourceTimestamp . '</v1:addrStartDt>
                                        <v1:addrCategory>Mailing</v1:addrCategory>
                                        <v1:freeTextLabel>Mailing</v1:freeTextLabel>
                                        <v1:city>' . $cty . '</v1:city>
                                        <v1:country>' . $cun . '</v1:country>
                                        <v1:holdMailFlag>N</v1:holdMailFlag>
                                        <v1:prefAddr>Y</v1:prefAddr>
                                        <v1:prefFormat>FREE_TEXT_FORMAT</v1:prefFormat>
                                        <v1:state>' . $stat . '</v1:state>
                                        <v1:town>' . $twn . '</v1:town>
                                        <v1:postalCode>' . $posCode . '</v1:postalCode>
                                    </v1:addressDetails>';
                        $extraNode .= '</v1:addressDetailsList>';
                    } else {
                        $extraNode .= '<v1:addressDetailsList>';
                        $extraNode .= '<v1:addressDetails>
                                        <v1:addrLine1>' . $add1 . '</v1:addrLine1>
                                        <v1:addrLine2>' . $add2 . '</v1:addrLine2>
                                        <v1:addrLine3>' . $add3 . '</v1:addrLine3>
                                        <v1:addrStartDt>' . $sourceTimestamp . '</v1:addrStartDt>
                                        <v1:addrCategory>' . $type . '</v1:addrCategory>
                                        <v1:freeTextLabel>' . $type . '</v1:freeTextLabel>
                                        <v1:city>' . $cty . '</v1:city>
                                        <v1:country>' . $cun . '</v1:country>
                                        <v1:holdMailFlag>N</v1:holdMailFlag>
                                        <v1:prefAddr>N</v1:prefAddr>
                                        <v1:prefFormat>FREE_TEXT_FORMAT</v1:prefFormat>
                                        <v1:state>' . $stat . '</v1:state>
                                        <v1:town>' . $twn . '</v1:town>
                                        <v1:postalCode>' . $posCode . '</v1:postalCode>
                                    </v1:addressDetails>';
                        $extraNode .= '</v1:addressDetailsList>';
                    }
                    $extraNode = trim(preg_replace('/\s\s+/', ' ', $extraNode));
                    $req = str_replace("<extraNode>extraNode</extraNode>", $extraNode, $req);
                    if (str_contains($req, '&')) {
                        $req = str_replace('&', '&amp;', $req);
                    }
                }
                /*  Foreign Address Change Issue ID UAT 1091  for finacle */
                /*  Foreign Address Change Issue ID Live 1178  for finacle */
                if ($issue_id == 1091 && $api_id == 8) {
                    foreach ($extraNodeArray as $k => $v) {
                        if ($k == 'prefAddr') {
                            $prefAddr = $v;
                        }
                        if ($k == 'placeOfIssue') {
                            $placeOfIssue = $v;
                        }
                        if ($k == 'addrLine1') {
                            $add1 = $v;
                        }
                        if ($k == 'addrLine2') {
                            $add2 = $v;
                        }
                        if ($k == 'addrLine3') {
                            $add3 = $v;
                        }
                        if ($k == 'country') {
                            $cun = $v;
                        }
                        if ($k == 'city') {
                            $cty = $v;
                        }
                        if ($k == 'state') {
                            $stat = $v;
                        }
                        if ($k == 'postalCode') {
                            $posCode = $v;
                        }
                        if ($k == 'countryOfIssue') {
                            $countryOfIssue = $v;
                        }
                        if ($k == 'nrecountryOfIssue') {
                            $nrecountryOfIssue = $v;
                        }
                        if ($k == 'docExpDt') {
                            $docExpDt = $v;
                        }
                        if ($k == 'docIssueDt') {
                            $docIssueDt = $v;
                        }
                        if ($k == 'refNum') {
                            $refNum = $v;
                        }
                        if ($k == 'nredocIssueDt') {
                            $nredocIssueDt = $v;
                        }
                        if ($k == 'nreplaceOfIssue') {
                            $nreplaceOfIssue = $v;
                        }
                        if ($k == 'nrerefNum') {
                            $nrerefNum = $v;
                        }
                        if ($k == 'isCustNRE') {
                            $isCustNRE = $v;
                        }
                        if ($k == 'nreBecomingDt') {
                            $nreBecomingDt = $v;
                        }
                    }
                    if (empty($add1)) {
                        $add1 = 'null';
                    }
                    if (empty($add2)) {
                        $add2 = 'null';
                    }
                    if (empty($add3)) {
                        $add3 = 'null';
                    }
                    $extraNode .= '<v1:isCustNRE>' . $isCustNRE . '</v1:isCustNRE>';
                    $extraNode .= '<v1:nreBecomingDt>' . $nreBecomingDt . '</v1:nreBecomingDt>';
                    if ($prefAddr == 'Y') {
                        $extraNode .= '<v1:addressDetailsList>';
                        $extraNode .= '<v1:addressDetails>
                                        <v1:addrLine1>' . $add1 . '</v1:addrLine1>
                                        <v1:addrLine2>' . $add2 . '</v1:addrLine2>
                                        <v1:addrLine3>' . $add3 . '</v1:addrLine3>
                                        <v1:addrStartDt>' . $sourceTimestamp . '</v1:addrStartDt>
                                        <v1:addrCategory>NRERelative</v1:addrCategory>
                                        <v1:freeTextLabel>NRERelative</v1:freeTextLabel>
                                        <v1:city>' . $cty . '</v1:city>
                                        <v1:country>' . $cun . '</v1:country>
                                        <v1:holdMailFlag>N</v1:holdMailFlag>
                                        <v1:prefAddr>N</v1:prefAddr>
                                        <v1:prefFormat>FREE_TEXT_FORMAT</v1:prefFormat>
                                        <v1:state>' . $stat . '</v1:state>
                                        <v1:postalCode>' . $posCode . '</v1:postalCode>
                                    </v1:addressDetails>';
                        $extraNode .= '<v1:addressDetails>
                                        <v1:addrLine1>' . $add1 . '</v1:addrLine1>
                                        <v1:addrLine2>' . $add2 . '</v1:addrLine2>
                                        <v1:addrLine3>' . $add3 . '</v1:addrLine3>
                                        <v1:addrStartDt>' . $sourceTimestamp . '</v1:addrStartDt>
                                        <v1:addrCategory>Mailing</v1:addrCategory>
                                        <v1:freeTextLabel>Mailing</v1:freeTextLabel>
                                        <v1:city>' . $cty . '</v1:city>
                                        <v1:country>' . $cun . '</v1:country>
                                        <v1:holdMailFlag>N</v1:holdMailFlag>
                                        <v1:prefAddr>Y</v1:prefAddr>
                                        <v1:prefFormat>FREE_TEXT_FORMAT</v1:prefFormat>
                                        <v1:state>' . $stat . '</v1:state>
                                        <v1:postalCode>' . $posCode . '</v1:postalCode>
                                    </v1:addressDetails>';
                        $extraNode .= '</v1:addressDetailsList>';
                    } else {
                        $extraNode .= '<v1:addressDetailsList>';
                        $extraNode .= '<v1:addressDetails>
                                        <v1:addrLine1>' . $add1 . '</v1:addrLine1>
                                        <v1:addrLine2>' . $add2 . '</v1:addrLine2>
                                        <v1:addrLine3>' . $add3 . '</v1:addrLine3>
                                        <v1:addrStartDt>' . $sourceTimestamp . '</v1:addrStartDt>
                                        <v1:addrCategory>NRERelative</v1:addrCategory>
                                        <v1:freeTextLabel>NRERelative</v1:freeTextLabel>
                                        <v1:city>' . $cty . '</v1:city>
                                        <v1:country>' . $cun . '</v1:country>
                                        <v1:holdMailFlag>N</v1:holdMailFlag>
                                        <v1:prefAddr>N</v1:prefAddr>
                                        <v1:prefFormat>FREE_TEXT_FORMAT</v1:prefFormat>
                                        <v1:state>' . $stat . '</v1:state>
                                        <v1:postalCode>' . $posCode . '</v1:postalCode>
                                    </v1:addressDetails>';
                        $extraNode .= '</v1:addressDetailsList>';
                    }
                    $extraNode .= '<v1:entityDocDetailsList>';
                    $extraNode .= '<v1:entityDocDetails>
                                    <v1:countryOfIssue>' . $countryOfIssue . '</v1:countryOfIssue>
                                    <v1:docCode>PP</v1:docCode>
                                    <v1:docExpDt>' . $docExpDt . '</v1:docExpDt>
                                    <v1:docIssueDt>' . $docIssueDt . '</v1:docIssueDt>
                                    <v1:docTypeCode>RCID</v1:docTypeCode>
                                    <v1:docTypeDesc>RCIF-ID DOC</v1:docTypeDesc>
                                    <v1:entityType>CIFRetCust</v1:entityType>
                                    <v1:identificationType>Passport Number</v1:identificationType>
                                    <v1:placeOfIssue>' . $placeOfIssue . '</v1:placeOfIssue>
                                    <v1:refNum>' . $refNum . '</v1:refNum>
                                    <v1:status>Received</v1:status>
                                    <v1:preferredUniqueId>Y</v1:preferredUniqueId>
                                    <v1:type>CIF</v1:type>
                                    <v1:isMandatory>N</v1:isMandatory>
                                    <v1:docDescr>PASSPORTNO</v1:docDescr>
                                </v1:entityDocDetails>';

                    $extraNode .= '</v1:entityDocDetailsList>';
                    $extraNode = trim(preg_replace('/\s\s+/', ' ', $extraNode));
                    $req = str_replace("<extraNode>extraNode</extraNode>", $extraNode, $req);
                    if (str_contains($req, '&')) {
                        $req = str_replace('&', '&amp;', $req);
                    }
                }
                /*  Address change Data Nullify Issue IDs UAT : 1019,1078  CardPro API ID UAT : 14  */
                /*  Address change Data Nullify Issue IDs Live : 1169,1172  CardPro API ID UAT : 14  */
                if (($issue_id == 1019 || $issue_id == 1078) && $api_id == 14) {
                    $req = str_replace("<v1:address1>null</v1:address1>", "<v1:address1> </v1:address1>", $req);
                    $req = str_replace("<v1:address2>null</v1:address2>", "<v1:address2> </v1:address2>", $req);
                    $req = str_replace("<v1:address3>null</v1:address3>", "<v1:address3> </v1:address3>", $req);
                    $req = str_replace("<v1:address4>null</v1:address4>", "<v1:address4> </v1:address4>", $req);
                    $req = str_replace("<v1:address5>null</v1:address5>", "<v1:address5> </v1:address5>", $req);
                    $req = str_replace("<v1:city>null</v1:city>", "<v1:city> </v1:city>", $req);
                    $req = str_replace("<v1:state>null</v1:state>", "<v1:state> </v1:state>", $req);
                    $req = str_replace("<v1:postCode>null</v1:postCode>", "<v1:postCode> </v1:postCode>", $req);
                    $req = str_replace("<v1:country>null</v1:country>", "<v1:country> </v1:country>", $req);
                    $req = str_replace("<v1:designation>null</v1:designation>", "<v1:designation> </v1:designation>", $req);
                    $req = str_replace("<v1:companyName>null</v1:companyName>", "<v1:companyName> </v1:companyName>", $req);
                }
                /*  Address Change Issue ID UAT 1019,1078  */
                /*  Address Change Issue ID Live 1169,1172  */
                if ($issue_id == 1019 || $issue_id == 1078 || $issue_id == 1084 || $issue_id == 544) {
                    if (str_contains($req, '`')) {
                        $req = str_replace('`', '#', $req);
                    }
                    if (str_contains($req, '^')) {
                        $req = str_replace('^', '&amp;', $req);
                    }
                }
                /**** Remove Extra Nodes before sending API ****/
                $req = str_replace("<extraNode>extraNode</extraNode>", '', $req);
                $req = str_replace("<v1:tdsTbl>TDS-0</v1:tdsTbl>", '', $req);
                $req = str_replace("<v1:tdsTbl>.</v1:tdsTbl>", '', $req);
                $req = str_replace("<v1:strText10>0</v1:strText10>", '', $req);
                $req = str_replace("<v1:strText10>.</v1:strText10>", '', $req);

                /* No API call Logic : Mobile Phone Change Issue ID UAT 119,518  API id :13   */
                /* No API call Logic : Mobile Phone Change Issue ID Live 1153,1162  API id :13   */
                if (($issue_id == 119 || $issue_id == 518) && $api_id == 13) {
                    foreach ($extraNodeArray as $k => $v) {
                        if ($k == 'phoneEmailType') {
                            $type = $v;
                        }
                    }
                    if ($type == 'CELLPH' || $type == 'COMMPH1') {
                    } else {
                        $api_id = null;
                    }
                }

                $req = str_replace("cif_api_password", $password, $req);
                $tmpArr['api_id'] = $api_id;
                $tmpArr['url'] = $url;
                $tmpArr['request'] = $req;
                $tmpArr['global_transaction_id'] = $global_transaction_id;
                $requestToPush[] = $tmpArr;
            }

            foreach ($requestToPush as $data) {
                if (isset($data['api_id'])) {
                    $api_id = $data['api_id'];
                    $url = $data['url'];
                    $xml_post_string = $data['request'];
                    $gtid = $data['global_transaction_id'];
                    CIFModificationService::requestResponsePayload(['reference_number' => $ref_no, 'type' => 1, 'url' => $url, 'json_node' => $xml_post_string, 'account_number' => $account_number, 'cif_number' => $cif_no, 'global_transaction_id' => $gtid]);

                    $headers = array("Content-type: text/xml;charset=\"utf-8\"", "Accept: text/xml", "Cache-Control: no-cache", "Pragma: no-cache", "SOAPAction: corporateCustomerInquiryReq", "Content-length: " . strlen($xml_post_string));
                    $msc = microtime(true);

                    // $ch = curl_init();
                    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                    // curl_setopt($ch, CURLOPT_URL, $url);
                    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    // curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                    // curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                    // curl_setopt($ch, CURLOPT_POST, true);
                    // curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
                    // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    // $resp = curl_exec($ch);
                    // curl_close($ch);

                    $api_credential = DB::table('api_credential')->first();
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                    if (empty($api_credential->esb_api_cert_path)) {
                    } else {
                        curl_setopt($ch, CURLOPT_PORT, "4444");
                        curl_setopt($ch, CURLOPT_CAINFO, $api_credential->esb_api_cert_path);
                    }
                    $resp = curl_exec($ch);
                    curl_close($ch);

                    $msc = microtime(true) - $msc;
                    $execution_time = number_format($msc, 2);

                    if (!empty($resp)) {
                        $doc = new DomDocument();
                        $doc->loadXML($resp);
                        $res = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $resp);
                        $xmlArray = simplexml_load_string($res);
                        $array = json_decode(json_encode($xmlArray), TRUE);
                        $array = array_flatten($array);
                        foreach ($array as $key => $value) {
                            $ke = str_ireplace(['v11', 'v1', 'v2', 'ns1', 'ns2', 'ns3', 'ns4'], '', $key);
                            if ($ke == 'errorCode') {
                                $status_code = $value;
                            }
                            if ($ke == 'errorMessage') {
                                $status_msg = $value;
                            }
                            if ($ke == 'globalTransactionId') {
                                $globalTransactionId = $value;
                            }
                        }
                    } else {
                        $status_code = '';
                        $status_msg = 'No API Response';
                        $globalTransactionId = '';
                    }
                    if (!empty($status_msg)) {
                        /* Finacle API ID UAT : 8 */
                        if ($api_id == 8) {
                            $msg .= '<b>Finacle :</b> ' . $status_msg . '. <br>';
                        } /* CardPro API ID UAT : 13,14 */
                        elseif ($api_id == 13 || $api_id == 14) {
                            $msg .= '<b>Cardpro :</b> ' . $status_msg . '. <br>';
                        } else {
                            $msg .= $status_msg . '. <br>';
                        }
                    }
                    if ($status_code != '000') {
                        $status[$api_id] = $api_id;
                        $tmp_status = 0;
                    }
                    CIFModificationService::requestResponsePayload(['reference_number' => $ref_no, 'type' => 2, 'url' => $url, 'json_node' => $resp, 'execution_time' => $execution_time, 'global_transaction_id' => $globalTransactionId, 'account_number' => $account_number, 'cif_number' => $cif_no, 'status_code' => $status_code, 'status_msg' => $status_msg]);
                }
            }
            // $refs = $referenceData->failed_retry;
            // $referenceData->failed_retry = $refs + 1;
            if ($tmp_status == 1) {
                // Local CI Group ID UAT 178 & LIVE 186
                // Local CI Sub-Group ID UAT 383 & LIVE 710
                // Local CI user_id UAT 'ci' & LIVE 'ci'
                CIFModificationService::audit(['reference_number' => $ref_no, 'unit_id' => 1, 'group_id' => 178, 'user_id' => 'ci', 'action' => "CIF API Updated from CI", 'comments' => 'Success', 'subgroup_id' => 383]);
                $response['success'] = 1;
                $referenceData->api_status = 1;
                $referenceData->failed_api = '';
            } else {
                // if ($referenceData->failed_retry == 5) {
                //     CIFModificationService::audit(['reference_number' => $ref_no, 'unit_id' => $referenceData->unit_id, 'group_id' => '', 'user_id' => '', 'action' => "API Update Attempted Twice", 'comments' => $msg, 'subgroup_id' => '']);
                //     $response['success'] = 2;
                //     $response['msg'] = $msg;
                // } else {

                // Local CI Group ID UAT 178 & LIVE 186
                // Local CI Sub-Group ID UAT 383 & LIVE 710
                // Local CI user_id UAT 'ci' & LIVE 'ci'
                    CIFModificationService::audit(['reference_number' => $ref_no, 'unit_id' => 1, 'group_id' => 178, 'user_id' => 'ci', 'action' => "CIF API Update Failed", 'comments' => $msg, 'subgroup_id' => 383]);
                    $response['success'] = 0;
                    $response['msg'] = $msg;
                    $response['failed_api'] = implode(',', $status);
                    $referenceData->failed_api = implode(',', $status);
                // }
            }
            $referenceData->save();
        }
        return response(['msg' => $response['msg'], 'status' => $response['success'], 'failed_api' => $response['failed_api']]);
    }

    public static function audit($params = array())
    {
        $commentModel = new Comment;
        $commentModel->reference_number = (!empty($params['reference_number'])) ? $params['reference_number'] : '';
        $commentModel->comments = (!empty($params['comments'])) ? $params['comments'] : '';
        $commentModel->time = strtotime(date('Y-m-d H:i:s'));
        $commentModel->user_id = (!empty($params['user_id'])) ? $params['user_id'] : 0;
        $commentModel->unit_id = (!empty($params['unit_id'])) ? $params['unit_id'] : 0;
        $commentModel->group_id = (!empty($params['group_id'])) ? $params['group_id'] : 0;
        $commentModel->action = (!empty($params['action'])) ? $params['action'] : 'INVALID';
        $commentModel->duration_in_minutes = (!empty($params['duration_in_minutes'])) ? $params['duration_in_minutes'] : 0;
        $commentModel->isapproved = (!empty($params['isapproved'])) ? $params['isapproved'] : 0;
        $commentModel->issendback = (!empty($params['issendback'])) ? $params['issendback'] : 0;
        $commentModel->subgroup_id = (!empty($params['subgroup_id'])) ? $params['subgroup_id'] : 0;
        $commentModel->ip = null;
        $commentModel->save();
    }

    public static function requestResponsePayload($params = array())
    {
        /*type 1 = CIF Update request,
            2 = CIF Update response,
            3 = Inquiry API request,
            4 = Inquiry API response*/

        $modelName = new CIFRequestResponse;
        $modelName->reference_number = (!empty($params['reference_number'])) ? $params['reference_number'] : '';
        $modelName->account_number = (!empty($params['account_number'])) ? $params['account_number'] : '';
        $modelName->type = (!empty($params['type'])) ? $params['type'] : '';
        $modelName->json_node = (!empty($params['json_node'])) ? $params['json_node'] : '';
        $modelName->url = (!empty($params['url'])) ? $params['url'] : '';
        $modelName->execution_time = (!empty($params['execution_time'])) ? $params['execution_time'] : '';
        $modelName->requested_by = '';
        $modelName->global_transaction_id = (!empty($params['global_transaction_id'])) ? $params['global_transaction_id'] : '';
        $modelName->cif_number = (!empty($params['cif_number'])) ? $params['cif_number'] : '';
        $modelName->status_code = (!empty($params['status_code'])) ? $params['status_code'] : '';
        $modelName->status_msg = (!empty($params['status_msg'])) ? $params['status_msg'] : '';
        $modelName->save();
    }

    public static function accInquiryApiResponse($params = array())
    {
        $issue_id = $params['issue_id'];
        $acc_no = $params['acc_no'];
        $ref_no = $params['ref_no'];
        $cif_no = $params['cif_no'];
        $data = [];
        $data['name'] = '';
        $data['acctName'] = '';
        $data['acctShortName'] = '';
        $data['schmCode'] = '';
        $data['schmType'] = '';
        $data['acctCurr'] = '';
        $data['acctInqAcctCurr'] = '';
        $data['branchId'] = '';
        $data['branchName'] = '';
        $data['bankId'] = '';
        $data['acctStmtMode'] = '';
        $data['startDt'] = '';
        $data['modeOfOperCode'] = '';
        $data['servChargeCollFlag'] = '';
        $data['localCalFlag'] = '';
        $data['intCrAcctFlg'] = '';
        $data['intDrAcctFlg'] = '';
        $data['wTaxAmountScopeFlg'] = '';
        $data['nextIntTrunDateCr'] = '';
        $data['nextInTrunDateDr'] = '';
        $data['nomAvlblFlag'] = '';
        $data['chqProvByBankflg'] = '';
        $data['atmFlg'] = '';
        $data['bkSectorCode'] = '';
        $data['cenBkSectorCode'] = '';
        $data['norPsoUnitId'] = '';
        $data['monitoringPsoUnitId'] = '';
        $data['ccCode'] = '';
        $data['tpCashDepTranCnt'] = 0;
        $data['tpCashDepTranAmtHigh'] = 0;
        $data['tpTotlWdrTranAmtTot'] = 0;
        $data['regNum'] = '00000000';
        $data['nomineeName'] = '';
        $data['relType'] = '';
        $data['nomineeTelephoneNum'] = '';
        $data['nomineeFaxNum'] = '';
        $data['nomineeTelexNum'] = '';
        $data['nomineeEmailAddr'] = '';
        $data['nomineeAddressTypeAddress1'] = '';
        $data['nomineeAddressTypeAddress2'] = '';
        $data['nomineeAddressTypeAddress3'] = '';
        $data['nomineeAddressTypeCity'] = '';
        $data['nomineeAddressTypeStateProv'] = '';
        $data['nomineeAddressTypePostalCode'] = '';
        $data['nomineeAddressTypeCountry'] = '';
        $data['nomineeAddrType'] = '';
        $data['nomineeMinorFlg'] = '';
        $data['nomineeBirthDt'] = '';
        $data['nomineePercent'] = '';
        $data['guardianCode'] = '';
        $data['guardianName'] = '';
        $data['guardianTelephoneNum'] = '';
        $data['guardianFaxNum'] = '';
        $data['guardianTelexNum'] = '';
        $data['guardianEmailAddr'] = '';
        $data['guardianAddressTypeAddress1'] = '';
        $data['guardianAddressTypeAddress2'] = '';
        $data['guardianAddressTypeAddress3'] = '';
        $data['guardianAddressTypeCity'] = '';
        $data['guardianAddressTypeStateProv'] = '';
        $data['guardianAddressTypePostalCode'] = '';
        $data['guardianAddressTypeCountry'] = '';
        $data['guardianAddrType'] = '';
        $data['relEmailAddress'] = '';
        $data['addr1'] = '';
        $data['addr2'] = '';
        $data['addr3'] = '';
        $data['city'] = '';
        $data['stateProv'] = '';
        $data['postalCode'] = '';
        $data['country'] = '';
        $data['addrType'] = '';
        $nomineeData = array();
        $data['callCFlg'] = '';
        $data['creditCFlg'] = '';
        $data['visaFlg'] = '';
        $data['pcashFlg'] = '';
        $data['monTotDepLmt'] = '';
        $data['monCashDepLmt'] = '';
        $data['mnetFlg'] = '';
        $data['smsFlg'] = '';
        $data['posFlg'] = '';
        $data['inetFlg'] = '';
        $data['ecoSectorCode'] = '';
        $data['fundTfrLmt'] = '';
        $data['pCashLmt'] = '';
        $data['purchaseLmt'] = '';
        $data['atmAmtLmt'] = '';
        $data['nomineeDetails'] = '';
        $data['rmks'] = '';
        $data['acctActDate'] = '';
        $data['bkSectorName'] = '';
        $data['cenBkSectorName'] = '';
        $data['ecoSectorName'] = '';
        $data['norPsoUnitName'] = '';
        $data['monitoringPsoUnitName'] = '';
        $data['ccDesc'] = '';
        $data['tpCashDepTranAmtTot'] = '';
        $data['tpXferDepTranCnt'] = 0;
        $data['tpXferDepTranAmtHigh'] = 0;
        $data['tpXferDepTranAmtTot'] = '';
        $data['tpRemtDepTranCnt'] = 0;
        $data['tpRemtDepTranAmtHigh'] = 0;
        $data['tpRemtDepTranAmtTot'] = '';
        $data['tpExptDepTranCnt'] = '';
        $data['tpExptDepTranAmtHigh'] = '';
        $data['tpExptDepTranAmtTot'] = '';
        $data['tpOthrDepTranCnt'] = '';
        $data['tpOthrDepTranAmtHigh'] = '';
        $data['tpOthrDepTranAmtTot'] = '';
        $data['tpOthrDepReason'] = '';
        $data['tpTotlDepTranCnt'] = 0;
        $data['tpTotlDepTranAmtHigh'] = 0;
        $data['tpTotlDepTranAmtTot'] = '';
        $data['tpCashWdrTranCnt'] = 0;
        $data['tpCashWdrTranAmtHigh'] = 0;
        $data['tpCashWdrTranAmtTot'] = '';
        $data['tpXferWdrTranCnt'] = 0;
        $data['tpXferWdrTranAmtHigh'] = 0;
        $data['tpXferWdrTranAmtTot'] = '';
        $data['tpRemtWdrTranCnt'] = 0;
        $data['tpRemtWdrTranAmtHigh'] = 0;
        $data['tpRemtWdrTranAmtTot'] = '';
        $data['tpExptWdrTranCnt'] = '';
        $data['tpExptWdrTranAmtHigh'] = '';
        $data['tpExptWdrTranAmtTot'] = '';
        $data['tpOthrWdrTranCnt'] = '';
        $data['tpOthrWdrTranAmtHigh'] = '';
        $data['tpOthrWdrTranAmtTot'] = '';
        $data['tpOthrWdrReason'] = '';
        $data['tpTotlWdrTranCnt'] = 0;
        $data['tpTotlWdrTranAmtHigh'] = 0;
        $data['tpSharDepTranCnt'] = '';
        $data['tpSharDepTranAmtHigh'] = '';
        $data['tpSharDepTranAmtTot'] = '';
        $data['tpSharWdrTranCnt'] = '';
        $data['tpSharWdrTranAmtHigh'] = '';
        $data['tpSharWdrTranAmtTot'] = '';
        $data['smsEnrolementDate'] = '';
        $data['smsDeEnrolementDate'] = '';
        $data['smsWaiverFlg'] = '';
        $data['smsWaiverDate'] = '';
        $data['refPsoUnitId'] = '';
        $data['refPsoUnitName'] = '';
        $data['aquAgentId'] = '';
        $data['monAgentId'] = '';
        $data['cardType'] = '';
        $data['freeCode1'] = '';
        $data['freeCode2'] = '';
        $data['freeCode3'] = '';
        $data['freeCode4'] = '';
        $data['freeCode5'] = '';
        $data['freeCode6'] = '';
        $data['freeCode7'] = '';
        $data['freeCode8'] = '';
        $data['freeCode9'] = '';
        $data['freeText1'] = '';
        $data['freeText2'] = '';
        $data['freeText3'] = '';
        $data['freeText4'] = '';
        $data['freeText5'] = '';
        $data['freeText6'] = '';
        $data['freeCode1Desc'] = '';
        $data['freeCode2Desc'] = '';
        $data['freeCode3Desc'] = '';
        $data['freeCode4Desc'] = '';
        $data['freeCode5Desc'] = '';
        $data['freeCode6Desc'] = '';
        $data['emboseName'] = '';
        $data['cibRptFlg'] = '';
        $data['cibDataStatus'] = '';
        $data['cibDataModDate'] = '';
        $data['tpAsonDate'] = '';
        $data['relPartyRecDelFlg'] = '';
        $data['relPartyType'] = '';
        $data['relPartyTypeDesc'] = '';
        $data['relPartyCode'] = '';
        $data['relPartyCodeDesc'] = '';
        $data['relPartyRecCustId'] = '';
        $data['relPartyRecLastName'] = '';
        $data['relPartyRecFirstName'] = '';
        $data['relPartyRecMiddleName'] = '';
        $data['relPartyRecName'] = '';
        $data['relPartyRecTitlePrefix'] = '';
        $data['relPartyRecTelephoneNum'] = '';
        $data['relPartyRecEmailAddr'] = '';
        $data['relPartyFaxNum'] = '';
        $data['relPartyTelexNum'] = '';
        $data['relPartyRecAddr1'] = '';
        $data['relPartyRecAddr2'] = '';
        $data['relPartyRecAddr3'] = '';
        $data['relPartyRecCity'] = '';
        $data['relPartyRecStateProv'] = '';
        $data['relPartyRecPostalCode'] = '';
        $data['relPartyRecCountry'] = '';
        $data['relPartyRecAddrType'] = '';

        $tpAsonDate = now()->format('d-m-Y');

        $inquiry = DB::table('cif_api')
            ->leftJoin('cif_modification_url', 'cif_api.parent_api', 'cif_modification_url.parent_id')
            ->where('cif_api.issue_id', $issue_id)
            ->where('cif_api.type', 2)
            ->where('cif_modification_url.status', 1)
            ->select('cif_modification_url.url', 'cif_modification_url.request', 'cif_modification_url.id')
            ->first();

        if (!empty($inquiry)) {
            $inquiry_id = $inquiry->id;
            $api = $inquiry->request;
            $url = $inquiry->url;
            $sourceTimestamp = now()->format('Y-m-d\TH:i:s.v');
            $password = DynamicAPICredential::where('api', $inquiry_id)->first();
            $password = !empty($password->password) ? decrypt($password->password) : '';
            $global_transaction_id = 'AYS' . now()->format('YmdHisv');
            $api = str_replace("#global_transaction_id#", $global_transaction_id, $api);
            $api = str_replace("cif_api_password", $password, $api);
            $api = str_replace("#sourceTimestamp#", $sourceTimestamp, $api);
            $api = str_replace("#cb_idno#", $acc_no, $api);
            $api = str_replace("#account_number#", $acc_no, $api);
            $api = str_replace("#cif_number#", $cif_no, $api);
            CIFModificationService::requestResponsePayload(['reference_number' => $ref_no, 'type' => 5, 'json_node' => $api, 'account_number' => $acc_no, 'url' => $url, 'cif_number' => $cif_no, 'global_transaction_id' => $global_transaction_id]);

            $headers = array("Content-type: text/xml;charset=\"utf-8\"", "Accept: text/xml", "Cache-Control: no-cache", "Pragma: no-cache", "SOAPAction: corporateCustomerInquiryReq", "Content-length: " . strlen($api));
            $msc = microtime(true);

            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            // curl_setopt($ch, CURLOPT_URL, $url);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            // curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            // curl_setopt($ch, CURLOPT_POST, true);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $api);
            // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            // $response = curl_exec($ch);
            // curl_close($ch);

            $api_credential = DB::table('api_credential')->first();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $api);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            if (empty($api_credential->esb_api_cert_path)) {
            } else {
                curl_setopt($ch, CURLOPT_PORT, "4444");
                curl_setopt($ch, CURLOPT_CAINFO, $api_credential->esb_api_cert_path);
            }
            $response = curl_exec($ch);
            curl_close($ch);

            $msc = microtime(true) - $msc;
            $execution_time = number_format($msc, 2);

            if (!empty($response)) {
                $resp = str_replace('&','&amp;',$response);

                $doc = new DomDocument();
                $doc->loadXML($resp);
                $res = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $resp);
                $xmlArray = simplexml_load_string($res);
                $xmlArray = json_decode(json_encode($xmlArray), TRUE);
                $array = array_flatten($xmlArray);
                foreach ($array as $key => $value) {
                    $ke = str_ireplace(['v11', 'v1', 'v2', 'ns1', 'ns2', 'ns3', 'ns4'], '', $key);
                    if ($ke == 'name') {
                        $data['name'] = $value;
                    }
                    if ($ke == 'acctInqSchmCode') {
                        $data['schmCode'] = $value;
                    }
                    if ($ke == 'acctInqSchmType') {
                        $data['schmType'] = $value;
                    }
                    if ($ke == 'acctInqAcctCurr') {
                        $data['acctInqAcctCurr'] = $value;
                    }
                    if ($ke == 'AcctCurr') {
                        $data['acctCurr'] = $value;
                    }
                    if ($ke == 'acctInqBranchId') {
                        $data['branchId'] = $value;
                    }
                    if ($ke == 'acctInqBranchName') {
                        $data['branchName'] = $value;
                    }
                    if ($ke == 'acctInqBankId') {
                        $data['bankId'] = $value;
                    }
                    if ($ke == 'acctInqName') {
                        $data['acctName'] = $value;
                    }
                    if ($ke == 'acctShortName') {
                        $data['acctShortName'] = $value;
                    }
                    if ($ke == 'acctStmtMode') {
                        $data['acctStmtMode'] = $value;
                    }
                    if ($ke == 'startDt') {
                        $data['startDt'] = $value;
                    }
                    if ($ke == 'modeOfOper') {
                        $data['modeOfOperCode'] = $value;
                    }
                    if ($ke == 'servChrgCollFlg') {
                        $data['servChargeCollFlag'] = $value;
                    }
                    if ($ke == 'localCalFlg') {
                        $data['localCalFlag'] = $value;
                    }
                    if ($ke == 'intCrAcctFlg') {
                        $data['intCrAcctFlg'] = $value;
                    }
                    if ($ke == 'intDrAcctFlg') {
                        $data['intDrAcctFlg'] = $value;
                    }
                    if ($ke == 'nextIntrunDateCR') {
                        $data['nextIntTrunDateCr'] = $value;
                    }
                    if ($ke == 'nextIntrunDateDR') {
                        $data['nextInTrunDateDr'] = $value;
                    }
                    if ($ke == 'chqalWdFlg') {
                        $data['chqalWdFlg'] = $value;
                    }
                    if ($ke == 'nomAvailableFlg') {
                        $data['nomAvlblFlag'] = $value;
                    }
                    if ($ke == 'chqProvByBankFlg') {
                        $data['chqProvByBankflg'] = $value;
                    }
                    if ($ke == 'taxCategory') {
                        $data['wTaxAmountScopeFlg'] = $value;
                    }
                    if ($ke == 'atmFlg') {
                        $data['atmFlg'] = $value;
                    }
                    if ($ke == 'bkSectorCode') {
                        $data['bkSectorCode'] = $value;
                    }
                    if ($ke == 'cenBkSectorCode') {
                        $data['cenBkSectorCode'] = $value;
                    }
                    if ($ke == 'norPsoUnitId') {
                        $data['norPsoUnitId'] = $value;
                    }
                    if ($ke == 'monitoringPsoUnitId') {
                        $data['monitoringPsoUnitId'] = $value;
                    }
                    if ($ke == 'ccCode') {
                        $data['ccCode'] = $value;
                    }
                    if ($ke == 'tpCashDepTranCnt') {
                        $data['tpCashDepTranCnt'] = !empty($value) ? $value : 0;
                    }
                    if ($ke == 'tpCashDepTranAmtHigh') {
                        $data['tpCashDepTranAmtHigh'] = !empty($value) ? $value : 0;
                    }
                    if ($ke == 'tpCashDepTranAmtTot') {
                        $data['tpCashDepTranAmtTot'] = $value;
                    }
                    if ($ke == 'tpXferDepTranCnt') {
                        $data['tpXferDepTranCnt'] = !empty($value) ? $value : 0;
                    }
                    if ($ke == 'tpXferDepTranAmtHigh') {
                        $data['tpXferDepTranAmtHigh'] = !empty($value) ? $value : 0;
                    }
                    if ($ke == 'tpXferDepTranAmtTot') {
                        $data['tpXferDepTranAmtTot'] = $value;
                    }
                    if ($ke == 'tpTotlDepTranCnt') {
                        $data['tpTotlDepTranCnt'] = !empty($value) ? $value : 0;
                    }
                    if ($ke == 'tpTotlDepTranAmtHigh') {
                        $data['tpTotlDepTranAmtHigh'] = !empty($value) ? $value : 0;
                    }
                    if ($ke == 'tpTotlDepTranAmtTot') {
                        $data['tpTotlDepTranAmtTot'] = $value;
                    }
                    if ($ke == 'tpCashWdrTranCnt') {
                        $data['tpCashWdrTranCnt'] = !empty($value) ? $value : 0;
                    }
                    if ($ke == 'tpCashWdrTranAmtHigh') {
                        $data['tpCashWdrTranAmtHigh'] = !empty($value) ? $value : 0;
                    }
                    if ($ke == 'tpCashWdrTranAmtTot') {
                        $data['tpCashWdrTranAmtTot'] = $value;
                    }
                    if ($ke == 'tpXferWdrTranCnt') {
                        $data['tpXferWdrTranCnt'] = !empty($value) ? $value : 0;
                    }
                    if ($ke == 'tpXferWdrTranAmtHigh') {
                        $data['tpXferWdrTranAmtHigh'] = !empty($value) ? $value : 0;
                    }
                    if ($ke == 'tpXferWdrTranAmtTot') {
                        $data['tpXferWdrTranAmtTot'] = $value;
                    }
                    if ($ke == 'tpTotlWdrTranCnt') {
                        $data['tpTotlWdrTranCnt'] = !empty($value) ? $value : 0;
                    }
                    if ($ke == 'tpTotlWdrTranAmtHigh') {
                        $data['tpTotlWdrTranAmtHigh'] = !empty($value) ? $value : 0;
                    }
                    if ($ke == 'tpTotlWdrTranAmtTot') {
                        $data['tpTotlWdrTranAmtTot'] = $value;
                    }
                    if ($ke == 'nomineeRegNum') {
                        $data['regNum'] = $value;
                    }
                    if ($ke == 'nomineeName') {
                        $data['nomineeName'] = $value;
                    }
                    if ($ke == 'relType') {
                        $data['relType'] = $value;
                    }
                    if ($ke == 'nomineeTelephoneNum') {
                        $data['nomineeTelephoneNum'] = $value;
                    }
                    if ($ke == 'nomineeFaxNum') {
                        $data['nomineeFaxNum'] = $value;
                    }
                    if ($ke == 'nomineeTelexNum') {
                        $data['nomineeTelexNum'] = $value;
                    }
                    if ($ke == 'nomineeEmailAddr') {
                        $data['nomineeEmailAddr'] = $value;
                    }
                    if ($ke == 'nomineeAddressTypeAddress1') {
                        $data['nomineeAddressTypeAddress1'] = $value;
                    }
                    if ($ke == 'nomineeAddressTypeAddress2') {
                        $data['nomineeAddressTypeAddress2'] = $value;
                    }
                    if ($ke == 'nomineeAddressTypeAddress3') {
                        $data['nomineeAddressTypeAddress3'] = $value;
                    }
                    if ($ke == 'nomineeAddressTypeCity') {
                        $data['nomineeAddressTypeCity'] = $value;
                    }
                    if ($ke == 'nomineeAddressTypeStateProv') {
                        $data['nomineeAddressTypeStateProv'] = $value;
                    }
                    if ($ke == 'nomineeAddressTypePostalCode') {
                        $data['nomineeAddressTypePostalCode'] = $value;
                    }
                    if ($ke == 'nomineeAddressTypeCountry') {
                        $data['nomineeAddressTypeCountry'] = $value;
                    }
                    if ($ke == 'nomineeAddrType') {
                        $data['nomineeAddrType'] = $value;
                    }
                    if ($ke == 'nomineeMinorFlg') {
                        $data['nomineeMinorFlg'] = $value;
                    }
                    if ($ke == 'nomineeBirthDt') {
                        $data['nomineeBirthDt'] = $value;
                    }
                    if ($ke == 'nomineePercent') {
                        $data['nomineePercent'] = $value;
                    }
                    if ($ke == 'guardianCode') {
                        $data['guardianCode'] = $value;
                    }
                    if ($ke == 'guardianName') {
                        $data['guardianName'] = $value;
                    }
                    if ($ke == 'guardianTelephoneNum') {
                        $data['guardianTelephoneNum'] = $value;
                    }
                    if ($ke == 'guardianFaxNum') {
                        $data['guardianFaxNum'] = $value;
                    }
                    if ($ke == 'guardianTelexNum') {
                        $data['guardianTelexNum'] = $value;
                    }
                    if ($ke == 'guardianEmailAddr') {
                        $data['guardianEmailAddr'] = $value;
                    }
                    if ($ke == 'guardianAddressTypeAddress1') {
                        $data['guardianAddressTypeAddress1'] = $value;
                    }
                    if ($ke == 'guardianAddressTypeAddress2') {
                        $data['guardianAddressTypeAddress2'] = $value;
                    }
                    if ($ke == 'guardianAddressTypeAddress3') {
                        $data['guardianAddressTypeAddress3'] = $value;
                    }
                    if ($ke == 'guardianAddressTypeCity') {
                        $data['guardianAddressTypeCity'] = $value;
                    }
                    if ($ke == 'guardianAddressTypeStateProv') {
                        $data['guardianAddressTypeStateProv'] = $value;
                    }
                    if ($ke == 'guardianAddressTypePostalCode') {
                        $data['guardianAddressTypePostalCode'] = $value;
                    }
                    if ($ke == 'guardianAddressTypeCountry') {
                        $data['guardianAddressTypeCountry'] = $value;
                    }
                    if ($ke == 'guardianAddrType') {
                        $data['guardianAddrType'] = $value;
                    }
                    if ($ke == 'relEmailAddress') {
                        $data['relEmailAddress'] = $value;
                    }
                    if ($ke == 'addressType_address1') {
                        $data['addr1'] = $value;
                    }
                    if ($ke == 'addressType_address2') {
                        $data['addr2'] = $value;
                    }
                    if ($ke == 'addressType_address3') {
                        $data['addr3'] = $value;
                    }
                    if ($ke == 'addressType_city') {
                        $data['city'] = $value;
                    }
                    if ($ke == 'addressType_stateProv') {
                        $data['stateProv'] = $value;
                    }
                    if ($ke == 'addressType_postalCode') {
                        $data['postalCode'] = $value;
                    }
                    if ($ke == 'addressType_country') {
                        $data['country'] = $value;
                    }
                    if ($ke == 'callCFlg') {
                        $data['callCFlg'] = $value;
                    }
                    if ($ke == 'creditCFlg') {
                        $data['creditCFlg'] = $value;
                    }
                    if ($ke == 'visaFlg') {
                        $data['visaFlg'] = $value;
                    }
                    if ($ke == 'pcashFlg') {
                        $data['pcashFlg'] = $value;
                    }
                    if ($ke == 'monTotDepLmt') {
                        $data['monTotDepLmt'] = $value;
                    }
                    if ($ke == 'monCashDepLmt') {
                        $data['monCashDepLmt'] = $value;
                    }
                    if ($ke == 'mnetFlg') {
                        $data['mnetFlg'] = $value;
                    }
                    if ($ke == 'smsFlg') {
                        $data['smsFlg'] = $value;
                    }
                    if ($ke == 'posFlg') {
                        $data['posFlg'] = $value;
                    }
                    if ($ke == 'inetFlg') {
                        $data['inetFlg'] = $value;
                    }
                    if ($ke == 'ecoSectorCode') {
                        $data['ecoSectorCode'] = $value;
                    }
                    if ($ke == 'fundTfrLmt') {
                        $data['fundTfrLmt'] = $value;
                    }
                    if ($ke == 'pCashLmt') {
                        $data['pCashLmt'] = $value;
                    }
                    if ($ke == 'purchaseLmt') {
                        $data['purchaseLmt'] = $value;
                    }
                    if ($ke == 'atmAmtLmt') {
                        $data['atmAmtLmt'] = $value;
                    }
                    if ($ke == 'nomineeDetails') {
                        $data['nomineeDetails'] = $value;
                    }
                    if ($ke == 'rmks') {
                        $data['rmks'] = $value;
                    }
                    if ($ke == 'acctActDate') {
                        $data['acctActDate'] = $value;
                    }
                    if ($ke == 'bkSectorName') {
                        $data['bkSectorName'] = $value;
                    }
                    if ($ke == 'cenBkSectorName') {
                        $data['cenBkSectorName'] = $value;
                    }
                    if ($ke == 'ecoSectorName') {
                        $data['ecoSectorName'] = $value;
                    }
                    if ($ke == 'norPsoUnitName') {
                        $data['norPsoUnitName'] = $value;
                    }
                    if ($ke == 'monitoringPsoUnitName') {
                        $data['monitoringPsoUnitName'] = $value;
                    }
                    if ($ke == 'ccDesc') {
                        $data['ccDesc'] = $value;
                    }
                    if ($ke == 'tpRemtDepTranCnt') {
                        $data['tpRemtDepTranCnt'] = !empty($value) ? $value : 0;
                    }
                    if ($ke == 'tpRemtDepTranAmtHigh') {
                        $data['tpRemtDepTranAmtHigh'] = !empty($value) ? $value : 0;
                    }
                    if ($ke == 'tpRemtDepTranAmtTot') {
                        $data['tpRemtDepTranAmtTot'] = $value;
                    }
                    if ($ke == 'tpExptDepTranCnt') {
                        $data['tpExptDepTranCnt'] = $value;
                    }
                    if ($ke == 'tpExptDepTranAmtHigh') {
                        $data['tpExptDepTranAmtHigh'] = $value;
                    }
                    if ($ke == 'tpExptDepTranAmtTot') {
                        $data['tpExptDepTranAmtTot'] = $value;
                    }
                    if ($ke == 'tpOthrDepTranCnt') {
                        $data['tpOthrDepTranCnt'] = $value;
                    }
                    if ($ke == 'tpOthrDepTranAmtHigh') {
                        $data['tpOthrDepTranAmtHigh'] = $value;
                    }
                    if ($ke == 'tpOthrDepTranAmtTot') {
                        $data['tpOthrDepTranAmtTot'] = $value;
                    }
                    if ($ke == 'tpOthrDepReason') {
                        $data['tpOthrDepReason'] = $value;
                    }
                    if ($ke == 'tpRemtWdrTranCnt') {
                        $data['tpRemtWdrTranCnt'] = !empty($value) ? $value : 0;
                    }
                    if ($ke == 'tpRemtWdrTranAmtHigh') {
                        $data['tpRemtWdrTranAmtHigh'] = !empty($value) ? $value : 0;
                    }
                    if ($ke == 'tpRemtWdrTranAmtTot') {
                        $data['tpRemtWdrTranAmtTot'] = $value;
                    }
                    if ($ke == 'tpExptWdrTranCnt') {
                        $data['tpExptWdrTranCnt'] = $value;
                    }
                    if ($ke == 'tpExptWdrTranAmtHigh') {
                        $data['tpExptWdrTranAmtHigh'] = $value;
                    }
                    if ($ke == 'tpExptWdrTranAmtTot') {
                        $data['tpExptWdrTranAmtTot'] = $value;
                    }
                    if ($ke == 'tpOthrWdrTranCnt') {
                        $data['tpOthrWdrTranCnt'] = $value;
                    }
                    if ($ke == 'tpOthrWdrTranAmtHigh') {
                        $data['tpOthrWdrTranAmtHigh'] = $value;
                    }
                    if ($ke == 'tpOthrWdrTranAmtTot') {
                        $data['tpOthrWdrTranAmtTot'] = $value;
                    }
                    if ($ke == 'tpOthrWdrReason') {
                        $data['tpOthrWdrReason'] = $value;
                    }
                    if ($ke == 'tpSharDepTranCnt') {
                        $data['tpSharDepTranCnt'] = $value;
                    }
                    if ($ke == 'tpSharDepTranAmtHigh') {
                        $data['tpSharDepTranAmtHigh'] = $value;
                    }
                    if ($ke == 'tpSharDepTranAmtTot') {
                        $data['tpSharDepTranAmtTot'] = $value;
                    }
                    if ($ke == 'tpSharWdrTranCnt') {
                        $data['tpSharWdrTranCnt'] = $value;
                    }
                    if ($ke == 'tpSharWdrTranAmtHigh') {
                        $data['tpSharWdrTranAmtHigh'] = $value;
                    }
                    if ($ke == 'tpSharWdrTranAmtTot') {
                        $data['tpSharWdrTranAmtTot'] = $value;
                    }
                    if ($ke == 'smsEnrolementDate') {
                        $data['smsEnrolementDate'] = $value;
                    }
                    if ($ke == 'smsDeEnrolementDate') {
                        $data['smsDeEnrolementDate'] = $value;
                    }
                    if ($ke == 'smsWaiverFlg') {
                        $data['smsWaiverFlg'] = $value;
                    }
                    if ($ke == 'smsWaiverDate') {
                        $data['smsWaiverDate'] = $value;
                    }
                    if ($ke == 'refPsoUnitId') {
                        $data['refPsoUnitId'] = $value;
                    }
                    if ($ke == 'refPsoUnitName') {
                        $data['refPsoUnitName'] = $value;
                    }
                    if ($ke == 'aquAgentId') {
                        $data['aquAgentId'] = $value;
                    }
                    if ($ke == 'monAgentId') {
                        $data['monAgentId'] = $value;
                    }
                    if ($ke == 'cardType') {
                        $data['cardType'] = $value;
                    }
                    if ($ke == 'emboseName') {
                        $data['emboseName'] = $value;
                    }
                    if ($ke == 'tpAsonDate') {
                        $data['tpAsonDate'] = !empty($value) ? $value : $tpAsonDate;
                    }
                    if ($ke == 'cibRptFlg') {
                        $data['cibRptFlg'] = $value;
                    }
                    if ($ke == 'cibDataStatus') {
                        $data['cibDataStatus'] = $value;
                    }
                    if ($ke == 'cibDataModDate') {
                        $data['cibDataModDate'] = $value;
                    }
                    if ($ke == 'relPartyRecDelFlg') {
                        $data['relPartyRecDelFlg'] = $value;
                    }
                    if ($ke == 'relPartyType') {
                        $data['relPartyType'] = $value;
                    }
                    if ($ke == 'relPartyTypeDesc') {
                        $data['relPartyTypeDesc'] = $value;
                    }
                    if ($ke == 'relPartyCode') {
                        $data['relPartyCode'] = $value;
                    }
                    if ($ke == 'relPartyCodeDesc') {
                        $data['relPartyCodeDesc'] = $value;
                    }
                    if ($ke == 'relCustId') {
                        $data['relPartyRecCustId'] = $value;
                    }
                    if ($ke == 'relLastName') {
                        $data['relPartyRecLastName'] = $value;
                    }
                    if ($ke == 'relFirstName') {
                        $data['relPartyRecFirstName'] = $value;
                    }
                    if ($ke == 'relMiddleName') {
                        $data['relPartyRecMiddleName'] = $value;
                    }
                    if ($ke == 'relName') {
                        $data['relPartyRecName'] = $value;
                    }
                    if ($ke == 'relTitlePrefix') {
                        $data['relPartyRecTitlePrefix'] = $value;
                    }
                    if ($ke == 'relTelephoneNum') {
                        $data['relPartyRecTelephoneNum'] = $value;
                    }
                    if ($ke == 'relFaxNum') {
                        $data['relPartyFaxNum'] = $value;
                    }
                    if ($ke == 'relTelexNum') {
                        $data['relPartyTelexNum'] = $value;
                    }
                    if ($ke == 'relEmailAddress') {
                        $data['relPartyRecEmailAddr'] = $value;
                    }
                    if ($ke == 'relAddressTypeAddress1') {
                        $data['relPartyRecAddr1'] = $value;
                    }
                    if ($ke == 'relAddressTypeAddress2') {
                        $data['relPartyRecAddr2'] = $value;
                    }
                    if ($ke == 'relAddressTypeAddress3') {
                        $data['relPartyRecAddr3'] = $value;
                    }
                    if ($ke == 'relAddressTypeCity') {
                        $data['relPartyRecCity'] = $value;
                    }
                    if ($ke == 'relAddressTypeStateProv') {
                        $data['relPartyRecStateProv'] = $value;
                    }
                    if ($ke == 'relAddressTypePostalCode') {
                        $data['relPartyRecCountry'] = $value;
                    }
                    if ($ke == 'reladdressTypeCountry') {
                        $data['relPartyRecAddrType'] = $value;
                    }

                }

                $fullLienNode = arrayNodeSearch($xmlArray, 'ns1NomineeInfoDetailType');
                if (!empty($fullLienNode)) {
                    if (empty($fullLienNode[0])) {
                        $tmpFullLienNode = $fullLienNode;
                        $fullLienNode = array();
                        $fullLienNode[0] = $tmpFullLienNode;
                    }

                    foreach ($fullLienNode as $fln1Key => $fLNode) {

                        $nomineeData[$fln1Key]['regNum'] ='';
                        $nomineeData[$fln1Key]['nomineeName'] ='';
                        $nomineeData[$fln1Key]['relType'] ='';
                        $nomineeData[$fln1Key]['nomineeTelephoneNum'] ='';
                        $nomineeData[$fln1Key]['nomineeEmailAddr'] ='';
                        $nomineeData[$fln1Key]['nomineeFaxNum'] ='';
                        $nomineeData[$fln1Key]['nomineeTelexNum'] ='';
                        $nomineeData[$fln1Key]['nomineeAddressTypeAddress1'] ='';
                        $nomineeData[$fln1Key]['nomineeAddressTypeAddress2'] ='';
                        $nomineeData[$fln1Key]['nomineeAddressTypeAddress3'] ='';
                        $nomineeData[$fln1Key]['nomineeAddressTypeCity'] ='';
                        $nomineeData[$fln1Key]['nomineeAddressTypeStateProv'] ='';
                        $nomineeData[$fln1Key]['nomineeAddressTypePostalCode'] ='';
                        $nomineeData[$fln1Key]['nomineeAddressTypeCountry'] ='';
                        $nomineeData[$fln1Key]['nomineeAddrType'] ='';
                        $nomineeData[$fln1Key]['nomineeMinorFlg'] ='';
                        $nomineeData[$fln1Key]['nomineeBirthDt'] ='';
                        $nomineeData[$fln1Key]['nomineePercent'] ='';
                        $nomineeData[$fln1Key]['guardianCode'] ='';
                        $nomineeData[$fln1Key]['guardianName'] ='';
                        $nomineeData[$fln1Key]['guardianTelephoneNum'] ='';
                        $nomineeData[$fln1Key]['guardianEmailAddr'] ='';
                        $nomineeData[$fln1Key]['guardianFaxNum'] ='';
                        $nomineeData[$fln1Key]['guardianTelexNum'] ='';
                        $nomineeData[$fln1Key]['guardianAddressTypeAddress1'] ='';
                        $nomineeData[$fln1Key]['guardianAddressTypeAddress2'] ='';
                        $nomineeData[$fln1Key]['guardianAddressTypeAddress3'] ='';
                        $nomineeData[$fln1Key]['guardianAddressTypeCity'] ='';
                        $nomineeData[$fln1Key]['guardianAddressTypeStateProv'] ='';
                        $nomineeData[$fln1Key]['guardianAddressTypePostalCode'] ='';
                        $nomineeData[$fln1Key]['guardianAddressTypeCountry'] ='';
                        $nomineeData[$fln1Key]['guardianAddrType'] ='';

                        $fLNode1 = array_flatten($fLNode);

                        foreach ($fLNode1 as $fln2Key => $value) {
                            $ke = str_ireplace(['v11', 'v1', 'v2', 'ns1', 'ns2', 'ns3', 'ns4'], '', $fln2Key);

                            if ($ke == 'nomineeRegNum') {
                                $nomineeData[$fln1Key]['regNum'] = $value;
                            }
                            if ($ke == 'nomineeName') {
                                $nomineeData[$fln1Key]['nomineeName'] = $value;
                            }
                            if ($ke == 'relType') {
                                $nomineeData[$fln1Key]['relType'] = $value;
                            }
                            if ($ke == 'nomineeTelephoneNum') {
                                $nomineeData[$fln1Key]['nomineeTelephoneNum'] = $value;
                            }
                            if ($ke == 'nomineeFaxNum') {
                                $nomineeData[$fln1Key]['nomineeFaxNum'] = $value;
                            }
                            if ($ke == 'nomineeTelexNum') {
                                $nomineeData[$fln1Key]['nomineeTelexNum'] = $value;
                            }
                            if ($ke == 'nomineeEmailAddr') {
                                $nomineeData[$fln1Key]['nomineeEmailAddr'] = $value;
                            }
                            if ($ke == 'nomineeAddressTypeAddress1') {
                                $nomineeData[$fln1Key]['nomineeAddressTypeAddress1'] = $value;
                            }
                            if ($ke == 'nomineeAddressTypeAddress2') {
                                $nomineeData[$fln1Key]['nomineeAddressTypeAddress2'] = $value;
                            }
                            if ($ke == 'nomineeAddressTypeAddress3') {
                                $nomineeData[$fln1Key]['nomineeAddressTypeAddress3'] = $value;
                            }
                            if ($ke == 'nomineeAddressTypeCity') {
                                $nomineeData[$fln1Key]['nomineeAddressTypeCity'] = $value;
                            }
                            if ($ke == 'nomineeAddressTypeStateProv') {
                                $nomineeData[$fln1Key]['nomineeAddressTypeStateProv'] = $value;
                            }
                            if ($ke == 'nomineeAddressTypePostalCode') {
                                $nomineeData[$fln1Key]['nomineeAddressTypePostalCode'] = $value;
                            }
                            if ($ke == 'nomineeAddressTypeCountry') {
                                $nomineeData[$fln1Key]['nomineeAddressTypeCountry'] = $value;
                            }
                            if ($ke == 'nomineeAddrType') {
                                $nomineeData[$fln1Key]['nomineeAddrType'] = $value;
                            }
                            if ($ke == 'nomineeMinorFlg') {
                                $nomineeData[$fln1Key]['nomineeMinorFlg'] = $value;
                            }
                            if ($ke == 'nomineeBirthDt') {
                                $nomineeData[$fln1Key]['nomineeBirthDt'] = $value;
                            }
                            if ($ke == 'nomineePercent') {
                                $nomineeData[$fln1Key]['nomineePercent'] = $value;
                            }
                            if ($ke == 'guardianCode') {
                                $nomineeData[$fln1Key]['guardianCode'] = $value;
                            }
                            if ($ke == 'guardianName') {
                                $nomineeData[$fln1Key]['guardianName'] = $value;
                            }
                            if ($ke == 'guardianTelephoneNum') {
                                $nomineeData[$fln1Key]['guardianTelephoneNum'] = $value;
                            }
                            if ($ke == 'guardianFaxNum') {
                                $nomineeData[$fln1Key]['guardianFaxNum'] = $value;
                            }
                            if ($ke == 'guardianTelexNum') {
                                $nomineeData[$fln1Key]['guardianTelexNum'] = $value;
                            }
                            if ($ke == 'guardianEmailAddr') {
                                $nomineeData[$fln1Key]['guardianEmailAddr'] = $value;
                            }
                            if ($ke == 'guardianAddressTypeAddress1') {
                                $nomineeData[$fln1Key]['guardianAddressTypeAddress1'] = $value;
                            }
                            if ($ke == 'guardianAddressTypeAddress2') {
                                $nomineeData[$fln1Key]['guardianAddressTypeAddress2'] = $value;
                            }
                            if ($ke == 'guardianAddressTypeAddress3') {
                                $nomineeData[$fln1Key]['guardianAddressTypeAddress3'] = $value;
                            }
                            if ($ke == 'guardianAddressTypeCity') {
                                $nomineeData[$fln1Key]['guardianAddressTypeCity'] = $value;
                            }
                            if ($ke == 'guardianAddressTypeStateProv') {
                                $nomineeData[$fln1Key]['guardianAddressTypeStateProv'] = $value;
                            }
                            if ($ke == 'guardianAddressTypePostalCode') {
                                $nomineeData[$fln1Key]['guardianAddressTypePostalCode'] = $value;
                            }
                            if ($ke == 'guardianAddressTypeCountry') {
                                $nomineeData[$fln1Key]['guardianAddressTypeCountry'] = $value;
                            }
                            if ($ke == 'guardianAddrType') {
                                $nomineeData[$fln1Key]['guardianAddrType'] = $value;
                            }
                        }
                    }
                }
                $data['nomineeinfo'] = $nomineeData;

                $xmlString = str_ireplace(['v11:', 'v1:', 'v2:', 'ns1:', 'ns2:', 'ns3:', 'ns4:'], '', $response);
                // Load the XML string
                $xml = simplexml_load_string($xmlString);
                // Find the <freeCodeDetails> element
                $freeCodeDetails = $xml->xpath('//freeCodeDetails');
                $freeTextDetails = $xml->xpath('//freeTextDetails');
                $freeCodeDescDetails = $xml->xpath('//freeCodeDescDetails');
                // Check if the element is found and extract its child nodes
                $freeCodeChildren = array();
                $freeTextChildren = array();
                $freeCodeDescChildren = array();
                if (!empty($freeCodeDetails)) {
                    $freeCodeChildren = $freeCodeDetails[0]->children();
                }
                if (!empty($freeTextDetails)) {
                    $freeTextChildren = $freeTextDetails[0]->children();
                }
                if (!empty($freeCodeDescDetails)) {
                    $freeCodeDescChildren = $freeCodeDescDetails[0]->children();
                }
                // Convert the SimpleXMLElement objects to an associative array
                $freeCodeArray = array();
                $freeTestArray = array();
                $freeCodeDescArray = array();
                foreach ($freeCodeChildren as $child) {
                    $freeCodeId = (string)$child->freeCodeId;
                    $freeCodeValue = (string)$child->freeCodeValue;
                    // Only add the entry if freeCodeId is not empty
                    if (!empty($freeCodeId)) {
                        $freeCodeArray[$freeCodeId] = $freeCodeValue;
                    }
                }
                foreach ($freeTextChildren as $child) {
                    $freeTextId = (string)$child->freeTextId;
                    $freeTextValue = (string)$child->freeTextValue;
                    if (!empty($freeTextId)) {
                        $freeTestArray[$freeTextId] = $freeTextValue;
                    }
                }
                foreach ($freeCodeDescChildren as $child) {
                    $freeCodeDescId = (string)$child->freeCodeDescId;
                    $freeCodeDescValue = (string)$child->freeCodeDescValue;
                    if (!empty($freeCodeDescId)) {
                        $freeCodeDescArray[$freeCodeDescId] = $freeCodeDescValue;
                    }
                }
                foreach ($freeCodeArray as $key => $value) {
                    if ($key == 1) {
                        $data['freeCode1'] = $value;
                    }
                    if ($key == 2) {
                        $data['freeCode2'] = $value;
                    }
                    if ($key == 3) {
                        $data['freeCode3'] = $value;
                    }
                    if ($key == 4) {
                        $data['freeCode4'] = $value;
                    }
                    if ($key == 5) {
                        $data['freeCode5'] = $value;
                    }
                    if ($key == 6) {
                        $data['freeCode6'] = $value;
                    }
                    if ($key == 7) {
                        $data['freeCode7'] = $value;
                    }
                    if ($key == 8) {
                        $data['freeCode8'] = $value;
                    }
                    if ($key == 9) {
                        $data['freeCode9'] = $value;
                    }
                }
                foreach ($freeTestArray as $key => $value) {
                    if ($key == 1) {
                        $data['freeText1'] = $value;
                    }
                    if ($key == 2) {
                        $data['freeText2'] = $value;
                    }
                    if ($key == 3) {
                        $data['freeText3'] = $value;
                    }
                    if ($key == 4) {
                        $data['freeText4'] = $value;
                    }
                    if ($key == 5) {
                        $data['freeText5'] = $value;
                    }
                    if ($key == 6) {
                        $data['freeText6'] = $value;
                    }
                }
                foreach ($freeCodeDescArray as $key => $value) {
                    if ($key == 1) {
                        $data['freeCode1Desc'] = $value;
                    }
                    if ($key == 2) {
                        $data['freeCode2Desc'] = $value;
                    }
                    if ($key == 3) {
                        $data['freeCode3Desc'] = $value;
                    }
                    if ($key == 4) {
                        $data['freeCode4Desc'] = $value;
                    }
                    if ($key == 5) {
                        $data['freeCode5Desc'] = $value;
                    }
                    if ($key == 6) {
                        $data['freeCode6Desc'] = $value;
                    }
                }
                CIFModificationService::requestResponsePayload(['reference_number' => $ref_no, 'type' => 6, 'json_node' => $response, 'account_number' => $acc_no, 'url' => $url, 'execution_time' => $execution_time, 'cif_number' => $cif_no, 'global_transaction_id' => $global_transaction_id]);
            }
        }
        return $data;
    }


}
