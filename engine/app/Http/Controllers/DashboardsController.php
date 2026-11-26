<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Schema;

use App\Reference;
use App\WForm;
use App\Complaint;
use App\SubgroupInfo;

class DashboardsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
        parent::__construct();
       // $this->middleware(['role:superadmin|admin']);
        $this->middleware(['role_or_permission:superadmin|admin|accessDashboard']);

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {

        $searchDataForView = array();

        $searchDataForView['date_from'] = "";
        $searchDataForView['date_to'] = "";
        $searchDataForView['qty'] = 10;
        $searchDataForView["tl_based"] = "";


        $tlBasedOption = array(
            "gen"=>"General Dashboard",
            "srt"=>"Service Request Type",
            "ct"=>"Complain Type",
            "srpl"=>"Service Request Pending List",
            "cpl"=>"Complain Pending List",
            "srsb"=>"Service Request SLA Breach",
            "csb"=>"Complain SLA Breach"
        );


        $title = "Dashboards";
        $title_for_layout = "Dashboards";
        return view('Dashboard/dashboards',compact('title','title_for_layout','searchDataForView','tlBasedOption'));
    }

    public function getWStatCompRate(Request $request)
    {


        $response = array();
        /* Working Status wise Report */
        $workingStatRes = array();
        $newForm = 0;
        $newToGroup = 0;
        $formWip = 0;
        $formHold = 0;
        $formClose = 0;

        $getUserWiseRef = '';
        //pr($request->toArray());
        $searchDataForView = array();
        $searchDataForView['date_from']     = xss_cleaner($request->date_from);
        $searchDataForView['date_to']       = xss_cleaner($request->date_to);

        if (!empty($searchDataForView['date_from']) && !empty($searchDataForView['date_to'])) {

        $getUserWiseRef = $this->getUserWiseUnitCondition(0,$searchDataForView['date_from'],$searchDataForView['date_to']);
        $getUserWiseRefForColumn = $this->getUserWiseUnitConditionForColumn(0,$searchDataForView['date_from'],$searchDataForView['date_to']);

        }else{
            $getUserWiseRef = $this->getUserWiseUnitCondition(0);
            $getUserWiseRefForColumn = $this->getUserWiseUnitConditionForColumn(0);
        }
        //prd($getUserWiseRef);
        $referenceModel = new Reference;

            $refDataObj = $referenceModel
                        ->select("form_status",DB::raw('count(*) AS count'))
                        ->whereIn("reference_number",$getUserWiseRef)
                        ->groupBy('form_status')
                        ->get();
        if (!empty($refDataObj)) {
            $workingStatRes = $refDataObj->toArray();
            foreach ($workingStatRes as $row) {
                if($row['form_status'] == 0 || $row['form_status'] == "" || $row['form_status'] == null){
                    $newForm += $row['count'];
                } elseif($row['form_status'] == 8 || $row['form_status'] == 6 || $row['form_status'] == 2) {
                    $formWip += $row['count'];
                } elseif($row['form_status'] == 9) {
                    $newToGroup += $row['count'];
                } elseif($row['form_status'] == 10) {
                    $formHold += $row['count'];
                } elseif($row['form_status'] == 11) {
                    $formClose += $row['count'];
                }
            }
        }

        $response['ws'] =
            [
                ['name'=>'Work In Progress','y'=>$formWip],
                ['name'=>'Hold Form','y'=>$formHold],
                ['name'=>'Pending Form','y'=>$newForm],
              //  ['name'=>'New To Group','y'=>$newToGroup],
                ['name'=>'Close Form','y'=>$formClose]
            ];

        //$response['wsn'] = [$formWip, $formHold, $newForm, $newToGroup, $formClose];
        $response['wsn'] = [$formWip, $formHold, $newForm, $formClose];
        /* End of Working Status wise Report */

        /* Total Request and Complaint */
        $wformModel = new WForm;
        $totalReq = $wformModel->whereIn("reference_number",$getUserWiseRef)->count();

        $complaintModel = new Complaint;
        $totalComplaint = $complaintModel->whereIn("reference_number",$getUserWiseRef)->count();


        $response['totsr'] =
            [
                ['name'=>'Request','y'=>$totalReq],
                ['name'=>'Complaint','y'=>$totalComplaint]
            ];
        /* End of Total Request and Complaint */
        /* Yearly total SR */
        $referenceModel = new Reference;
        $refTotReqDataObj = $referenceModel
                        ->select(
                            DB::raw("COUNT(*) AS totalRequest"),
                            DB::raw("FROM_UNIXTIME(date, '%Y') AS year"),
                            DB::raw("SUBSTRING(reference_number, 1,1) AS ref")
                        )
                        ->whereIn("reference_number",$getUserWiseRefForColumn)
                        ->groupBy(DB::raw("FROM_UNIXTIME(date, '%Y')"))
                        ->groupBy(DB::raw("SUBSTRING(reference_number, 1,1)"))
                        /*->orderByRaw(DB::raw("FROM_UNIXTIME(date, '%Y') DESC"))
                        ->orderByRaw(DB::raw("SUBSTRING(reference_number, 1,1) DESC"))*/
                        ->orderByRaw("FROM_UNIXTIME(date, '%Y') DESC")
                        ->orderByRaw("SUBSTRING(reference_number, 1,1) DESC")
                        ->get();
        $refTotReqData = array();
        $totalSRPerYear = array();
        $tmpYearWiseData = array();
        $tmpYearWiseData = array();

        if (!empty($refTotReqDataObj)) {
            $idxCounter = -1;
            $refTotReqData = $refTotReqDataObj->toArray();
            foreach ($refTotReqData as $tspy) {
                if (empty($tmpYearWiseData[$tspy['year']])) {
                    $tmpYearWiseData[$tspy['year']] = array();
                    $tmpYearWiseData[$tspy['year']]['name'] = 'Year '.$tspy['year'];
                    $tmpYearWiseData[$tspy['year']]['data'] = array();

                    $idxCounter++;
                    $totalSRPerYear[$idxCounter] = array();
                    $totalSRPerYear[$idxCounter]['name'] = 'Year '.$tspy['year'];
                    $totalSRPerYear[$idxCounter]['data'] = array();
                }


                if (empty($tmpYearWiseData[$tspy['year']]['data']['S'])) {
                    $tmpYearWiseData[$tspy['year']]['data']['S'] = 0;
                }
                if (empty($tmpYearWiseData[$tspy['year']]['data']['C'])) {
                    $tmpYearWiseData[$tspy['year']]['data']['C'] = 0;
                }
                if (empty($tmpYearWiseData[$tspy['year']]['data']['N'])) {
                    $tmpYearWiseData[$tspy['year']]['data']['N'] = 0;
                }

                $tmpYearWiseData[$tspy['year']]['data'][$tspy['ref']] = $tspy['totalRequest'];


                $totalSRPerYear[$idxCounter]['data'][0] = (int)$tmpYearWiseData[$tspy['year']]['data']['S'];
                $totalSRPerYear[$idxCounter]['data'][1] = (int)$tmpYearWiseData[$tspy['year']]['data']['C'];
                $totalSRPerYear[$idxCounter]['data'][2] = (int)$tmpYearWiseData[$tspy['year']]['data']['N'];
            }
        }
        $response['totsrnc'] = $totalSRPerYear;
        /* End of Yearly total SR */

        return $response;
    }

    public function serviceRequestType(Request $request)
    {
        $response = array();

        $searchDataForView = array();
        $searchDataForView['date_from']     = xss_cleaner($request->date_from);
        $searchDataForView['date_to']       = xss_cleaner($request->date_to);
        $searchDataForView['qty']           = (!empty($request->qty)) ? xss_cleaner($request->qty) : 10;

        $getUserWiseRef = $this->getUserWiseUnitCondition(1, $searchDataForView['date_from'],$searchDataForView['date_to']);
        $where = " where 1 AND ref.reference_number IN($getUserWiseRef)";

        $limit = " limit ".$searchDataForView['qty'];

        // if (!empty($searchDataForView['date_from'])) {
        //     $searchDataForView['date_from'] = date('Y-m-d',strtotime($searchDataForView['date_from']));
        // }
        // if (!empty($searchDataForView['date_to'])) {
        //     $searchDataForView['date_to'] = date('Y-m-d',strtotime($searchDataForView['date_to']));
        // }

        // if (!empty($searchDataForView['date_from']) && empty($searchDataForView['date_to'])) {
        //     $where = $where." and FROM_UNIXTIME(ref.date,'%Y-%m-%d') >= '".$searchDataForView['date_from']."'";
        // } elseif (!empty($searchDataForView['date_to']) && empty($searchDataForView['date_from'])) {
        //     $where = $where." and FROM_UNIXTIME(ref.date,'%Y-%m-%d') <= '".$searchDataForView['date_to']."'";
        // } elseif (!empty($searchDataForView['date_from']) && !empty($searchDataForView['date_to'])) {
        //     $where = $where." and FROM_UNIXTIME(ref.date,'%Y-%m-%d') >= '".$searchDataForView['date_from']."' and FROM_UNIXTIME(ref.date,'%Y-%m-%d') <= '".$searchDataForView['date_to']."'";
        // } else {
        //     // $dataObj = $dataObj->where(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"),">=", date('Y-m-d'));
        // }

        $respData['srt_title'] = array();
        $respData['srt_data'] = DB::select(
            "SELECT
                unit_items.name,
                /*GROUP_CONCAT(ref.reference_number SEPARATOR ',') AS refs,*/
                count(*) as y
            FROM w_form wf
            LEFT JOIN reference ref ON (wf.reference_number = ref.reference_number)
            LEFT JOIN unit_items ON (unit_items.id = wf.w_form_type)
            $where
            GROUP BY wf.w_form_type
            ORDER BY y DESC $limit");

        if (!empty($respData['srt_data'])) {
            foreach ($respData['srt_data'] as $key => $srtVal) {
                $respData['srt_title'][] = $srtVal->name;

                $tmpData['name'] = $srtVal->name;
                $tmpData['y'] = (int)$srtVal->y;
                $respData['srt'][] = $tmpData;
            }
        }
        return $respData;
    }

    public function complaintType(Request $request)
    {
        $response = array();

        $searchDataForView = array();
        $searchDataForView['date_from']     = xss_cleaner($request->date_from);
        $searchDataForView['date_to']       = xss_cleaner($request->date_to);
        $searchDataForView['qty']           = (!empty($request->qty)) ? xss_cleaner($request->qty) : 10;

        $getUserWiseRef = $this->getUserWiseUnitCondition(1, $searchDataForView['date_from'],$searchDataForView['date_to']);
        $where = " where 1 AND ref.reference_number IN($getUserWiseRef)";

        $limit = " limit ".$searchDataForView['qty'];


        // if (!empty($searchDataForView['date_from'])) {
        //     $searchDataForView['date_from'] = date('Y-m-d',strtotime($searchDataForView['date_from']));
        // }
        // if (!empty($searchDataForView['date_to'])) {
        //     $searchDataForView['date_to'] = date('Y-m-d',strtotime($searchDataForView['date_to']));
        // }

        // if (!empty($searchDataForView['date_from']) && empty($searchDataForView['date_to'])) {
        //     $where = $where." and FROM_UNIXTIME(ref.date,'%Y-%m-%d') >= '".$searchDataForView['date_from']."'";
        // } elseif (!empty($searchDataForView['date_to']) && empty($searchDataForView['date_from'])) {
        //     $where = $where." and FROM_UNIXTIME(ref.date,'%Y-%m-%d') <= '".$searchDataForView['date_to']."'";
        // } elseif (!empty($searchDataForView['date_from']) && !empty($searchDataForView['date_to'])) {
        //     $where = $where." and FROM_UNIXTIME(ref.date,'%Y-%m-%d') >= '".$searchDataForView['date_from']."' and FROM_UNIXTIME(ref.date,'%Y-%m-%d') <= '".$searchDataForView['date_to']."'";
        // } else {
        //     // $dataObj = $dataObj->where(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"),">=", date('Y-m-d'));
        // }

        $respData['ct_title'] = array();
        $respData['ct_data'] = DB::select(
            "SELECT
                unit_items.name,
                count(*) as y
            FROM complaint c
            LEFT JOIN reference ref ON (c.reference_number = ref.reference_number)
            LEFT JOIN unit_items ON (unit_items.id = c.complaint_type)
            $where
            GROUP BY c.complaint_type
            ORDER BY y DESC $limit");

        if (!empty($respData['ct_data'])) {
            foreach ($respData['ct_data'] as $key => $ctVal) {
                $respData['ct_title'][] = $ctVal->name;

                $tmpData['name'] = $ctVal->name;
                $tmpData['y'] = (int)$ctVal->y;
                $respData['ct'][] = $tmpData;

            }
        }
        return $respData;
    }

    public function serviceRequestPendingList(Request $request)
    {
        $response = array();

        $searchDataForView = array();
        $searchDataForView['date_from']     = xss_cleaner($request->date_from);
        $searchDataForView['date_to']       = xss_cleaner($request->date_to);
        $searchDataForView['qty']           = (!empty($request->qty)) ? xss_cleaner($request->qty) : 10;

        $getUserWiseRef = $this->getUserWiseUnitCondition(1, $searchDataForView['date_from'],$searchDataForView['date_to']);

        $where = " where 1 AND ref.reference_number IN($getUserWiseRef)";
        $limit = " limit ".$searchDataForView['qty'];

        // if (!empty($searchDataForView['date_from'])) {
        //     $searchDataForView['date_from'] = date('Y-m-d',strtotime($searchDataForView['date_from']));
        // }
        // if (!empty($searchDataForView['date_to'])) {
        //     $searchDataForView['date_to'] = date('Y-m-d',strtotime($searchDataForView['date_to']));
        // }

        // if (!empty($searchDataForView['date_from']) && empty($searchDataForView['date_to'])) {
        //     $where = $where." and FROM_UNIXTIME(ref.date,'%Y-%m-%d') >= '".$searchDataForView['date_from']."'";
        // } elseif (!empty($searchDataForView['date_to']) && empty($searchDataForView['date_from'])) {
        //     $where = $where." and FROM_UNIXTIME(ref.date,'%Y-%m-%d') <= '".$searchDataForView['date_to']."'";
        // } elseif (!empty($searchDataForView['date_from']) && !empty($searchDataForView['date_to'])) {
        //     $where = $where." and FROM_UNIXTIME(ref.date,'%Y-%m-%d') >= '".$searchDataForView['date_from']."' and FROM_UNIXTIME(ref.date,'%Y-%m-%d') <= '".$searchDataForView['date_to']."'";
        // } else {
        //     // $dataObj = $dataObj->where(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"),">=", date('Y-m-d'));
        // }

        $respData['srpl_title'] = array();
        $respData['srpl_data'] = DB::select(
            "SELECT
                unit_items.name,
                count(*) as y
            FROM w_form wf
            LEFT JOIN reference ref ON (wf.reference_number = ref.reference_number)
            LEFT JOIN unit_items ON (unit_items.id = wf.w_form_type)
            $where
            AND ref.form_status != 11
            GROUP BY wf.w_form_type
            ORDER BY y DESC $limit");

        if (!empty($respData['srpl_data'])) {
            foreach ($respData['srpl_data'] as $key => $srplVal) {
                $respData['srpl_title'][] = $srplVal->name;
                $tmpData['name'] = $srplVal->name;
                $tmpData['y'] = (int)$srplVal->y;
                $respData['srpl'][] = $tmpData;
            }
        }
        return $respData;
    }

    public function complaintPendingList(Request $request)
    {
        $response = array();

        $searchDataForView = array();
        $searchDataForView['date_from']     = xss_cleaner($request->date_from);
        $searchDataForView['date_to']       = xss_cleaner($request->date_to);
        $searchDataForView['qty']           = (!empty($request->qty)) ? xss_cleaner($request->qty) : 10;

        $getUserWiseRef = $this->getUserWiseUnitCondition(1, $searchDataForView['date_from'],$searchDataForView['date_to']);

        $where = " where 1 AND ref.reference_number IN($getUserWiseRef)";
        $limit = " limit ".$searchDataForView['qty'];

        $respData['cpl_title'] = array();
        $respData['cpl_data'] = DB::select(
            "SELECT
                unit_items.id,
                unit_items.name,
                count(*) as y
            FROM complaint c
            LEFT JOIN reference ref ON (c.reference_number = ref.reference_number)
            LEFT JOIN unit_items ON (unit_items.id = c.complaint_type)
            $where
            AND ref.form_status != 11
            GROUP BY c.complaint_type
            ORDER BY y DESC $limit");

        if (!empty($respData['cpl_data'])) {
            foreach ($respData['cpl_data'] as $key => $cplVal) {
                $respData['cpl_title'][] = $cplVal->name;

                $tmpData['issue_id'] = $cplVal->id;
                $tmpData['name'] = $cplVal->name;
                $tmpData['y'] = (int)$cplVal->y;
                $respData['cpl'][] = $tmpData;

            }
        }

        return $respData;
    }

    public function serviceRequestSLABreach(Request $request)
    {
        $response = array();

        $searchDataForView = array();
        $searchDataForView['date_from']     = xss_cleaner($request->date_from);
        $searchDataForView['date_to']       = xss_cleaner($request->date_to);
        $searchDataForView['qty']           = (!empty($request->qty)) ? xss_cleaner($request->qty) : 10;

        $getUserWiseRef = $this->getUserWiseUnitCondition(1, $searchDataForView['date_from'],$searchDataForView['date_to']);

        $where = " where 1 AND wf.reference_number IN($getUserWiseRef)";
        $limit = " limit ".$searchDataForView['qty'];

        // if (!empty($searchDataForView['date_from'])) {
        //     $searchDataForView['date_from'] = date('Y-m-d',strtotime($searchDataForView['date_from']));
        // }
        // if (!empty($searchDataForView['date_to'])) {
        //     $searchDataForView['date_to'] = date('Y-m-d',strtotime($searchDataForView['date_to']));
        // }

        // if (!empty($searchDataForView['date_from']) && empty($searchDataForView['date_to'])) {
        //     $where = $where." and mv.created_at >= '".$searchDataForView['date_from']."'";
        // } elseif (!empty($searchDataForView['date_to']) && empty($searchDataForView['date_from'])) {
        //     $where = $where." and mv.created_at <= '".$searchDataForView['date_to']."'";
        // } elseif (!empty($searchDataForView['date_from']) && !empty($searchDataForView['date_to'])) {
        //     $where = $where." and mv.created_at >= '".$searchDataForView['date_from']."' and mv.created_at <= '".$searchDataForView['date_to']."'";
        // } else {
        //     // $dataObj = $dataObj->where(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"),">=", date('Y-m-d'));
        // }

        $respData['srsb_title'] = array();
        $respData['srsb_data'] = DB::select(
            "SELECT
                unit_items.name,
                count(*) as y
            FROM w_form wf
            JOIN issue_breaches mv ON (wf.reference_number = mv.reference_no)
            JOIN unit_items ON (unit_items.id = wf.w_form_type)

            $where

            GROUP BY w_form_type
            ORDER BY y DESC $limit");

        if (!empty($respData['srsb_data'])) {
            foreach ($respData['srsb_data'] as $key => $srsbVal) {
                $respData['srsb_title'][] = $srsbVal->name;

                $tmpData['name'] = $srsbVal->name;
                $tmpData['y'] = (int)$srsbVal->y;
                $respData['srsb'][] = $tmpData;

            }
        }
        return $respData;
    }

    public function complaintSLABreach(Request $request)
    {
        $response = array();

        $searchDataForView = array();
        $searchDataForView['date_from']     = xss_cleaner($request->date_from);
        $searchDataForView['date_to']       = xss_cleaner($request->date_to);
        $searchDataForView['qty']           = (!empty($request->qty)) ? xss_cleaner($request->qty) : 10;

        $getUserWiseRef = $this->getUserWiseUnitCondition(1, $searchDataForView['date_from'],$searchDataForView['date_to']);

        $where = " where 1 AND c.reference_number IN($getUserWiseRef)";
        $limit = " limit ".$searchDataForView['qty'];

        // if (!empty($searchDataForView['date_from'])) {
        //     $searchDataForView['date_from'] = date('Y-m-d',strtotime($searchDataForView['date_from']));
        // }
        // if (!empty($searchDataForView['date_to'])) {
        //     $searchDataForView['date_to'] = date('Y-m-d',strtotime($searchDataForView['date_to']));
        // }

        // if (!empty($searchDataForView['date_from']) && empty($searchDataForView['date_to'])) {
        //     $where = $where." and mv.created_at >= '".$searchDataForView['date_from']."'";
        // } elseif (!empty($searchDataForView['date_to']) && empty($searchDataForView['date_from'])) {
        //     $where = $where." and mv.created_at <= '".$searchDataForView['date_to']."'";
        // } elseif (!empty($searchDataForView['date_from']) && !empty($searchDataForView['date_to'])) {
        //     $where = $where." and mv.created_at >= '".$searchDataForView['date_from']."' and mv.created_at <= '".$searchDataForView['date_to']."'";
        // } else {
        //     // $dataObj = $dataObj->where(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"),">=", date('Y-m-d'));
        // }

        $respData['csb_title'] = array();
        $respData['csb_data'] = DB::select(
            "SELECT
                unit_items.name,
                count(*) as y
            FROM complaint c
            JOIN issue_breaches mv ON (c.reference_number = mv.reference_no)
            JOIN unit_items ON (unit_items.id = c.complaint_type)

            $where

            GROUP BY complaint_type
            ORDER BY y DESC $limit");

        if (!empty($respData['csb_data'])) {
            foreach ($respData['csb_data'] as $key => $csbVal) {
                $respData['csb_title'][] = $csbVal->name;

                $tmpData['name'] = $csbVal->name;
                $tmpData['y'] = (int)$csbVal->y;
                $respData['csb'][] = $tmpData;
            }
        }
        return $respData;
    }

    public function getDetailsReport(Request $request) {
        $title = "Reports";
        $title_for_layout = 'Dashboard Reports';
        $home_menu_icon = "fa fa-file-text-o";

        $dataForView = array();
        $searchDataForView = array();
        $searchDataForView['report_type']     = xss_cleaner($request->tlbased);
        $searchDataForView['date_from']     = xss_cleaner($request->datefrom);
        $searchDataForView['date_to']       = xss_cleaner($request->dateto);
        $searchDataForView['req_id']       = xss_cleaner($request->req_id);
        $searchDataForView['qty']           = (!empty($request->qty)) ? xss_cleaner($request->qty) : 10;

        $getUserWiseRef = $this->getUserWiseUnitCondition(1, $searchDataForView['date_from'],$searchDataForView['date_to']);
        $where = " where 1 AND ref.reference_number IN($getUserWiseRef)";

        $limit = " limit ".$searchDataForView['qty'];

        // if (!empty($searchDataForView['date_from'])) {
        //     $searchDataForView['date_from'] = date('Y-m-d',strtotime($searchDataForView['date_from']));
        // }
        // if (!empty($searchDataForView['date_to'])) {
        //     $searchDataForView['date_to'] = date('Y-m-d',strtotime($searchDataForView['date_to']));
        // }

        // if (!empty($searchDataForView['date_from']) && empty($searchDataForView['date_to'])) {
        //     $where = $where." and FROM_UNIXTIME(ref.date,'%Y-%m-%d') >= '".$searchDataForView['date_from']."'";
        // } elseif (!empty($searchDataForView['date_to']) && empty($searchDataForView['date_from'])) {
        //     $where = $where." and FROM_UNIXTIME(ref.date,'%Y-%m-%d') <= '".$searchDataForView['date_to']."'";
        // } elseif (!empty($searchDataForView['date_from']) && !empty($searchDataForView['date_to'])) {
        //     $where = $where." and FROM_UNIXTIME(ref.date,'%Y-%m-%d') >= '".$searchDataForView['date_from']."' and FROM_UNIXTIME(ref.date,'%Y-%m-%d') <= '".$searchDataForView['date_to']."'";
        // } else {
        //     // $dataObj = $dataObj->where(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"),">=", date('Y-m-d'));
        // }
        if (!empty($searchDataForView['req_id'])) {
            $where = $where." and unit_items.id = '".$searchDataForView['req_id']."'";
        }

        if ($searchDataForView['report_type'] == 'srt') {
            $dataForView = DB::select(
                "SELECT
                    ref.*,
                    wf.*,
                    product_types.name AS product_type,
                    unit_items.name AS form_type,
                    subgroup_info.name,
                    FROM_UNIXTIME(ref.date,'%Y-%m-%d') AS UNXTIME,
                    FROM_UNIXTIME(ref.access_date,'%Y-%m-%d') AS last_access_dt
                FROM w_form wf
                LEFT JOIN reference ref ON (wf.reference_number = ref.reference_number)
                LEFT JOIN subgroup_info ON (subgroup_info.id = ref.sub_group_info_id)
                LEFT JOIN unit_items ON (unit_items.id = wf.w_form_type)
                LEFT JOIN product_types ON (product_types.id = wf.product_type)
                $where
                ORDER BY ref.reference_number DESC");

        } elseif ($searchDataForView['report_type'] == 'ct') {
            $dataForView = DB::select(
                "SELECT
                    ref.*,
                    c.*,
                    product_types.name AS product_type,
                    unit_items.name AS form_type,
                    subgroup_info.name,
                    FROM_UNIXTIME(ref.date,'%Y-%m-%d') AS UNXTIME,
                    FROM_UNIXTIME(ref.access_date,'%Y-%m-%d') AS last_access_dt
                FROM complaint c
                LEFT JOIN reference ref ON (c.reference_number = ref.reference_number)
                LEFT JOIN subgroup_info ON (subgroup_info.id = ref.sub_group_info_id)
                LEFT JOIN unit_items ON (unit_items.id = c.complaint_type)
                LEFT JOIN product_types ON (product_types.id = c.product_type)
                $where
                ORDER BY ref.reference_number DESC");
        } elseif ($searchDataForView['report_type'] == 'srpl') {
            $dataForView = DB::select(
                "SELECT
                    ref.*,
                    wf.*,
                    product_types.name AS product_type,
                    unit_items.name AS form_type,
                    subgroup_info.name,
                    FROM_UNIXTIME(ref.date,'%Y-%m-%d') AS UNXTIME,
                    FROM_UNIXTIME(ref.access_date,'%Y-%m-%d') AS last_access_dt
                FROM w_form wf
                LEFT JOIN reference ref ON (wf.reference_number = ref.reference_number)
                LEFT JOIN subgroup_info ON (subgroup_info.id = ref.sub_group_info_id)
                LEFT JOIN unit_items ON (unit_items.id = wf.w_form_type)
                LEFT JOIN product_types ON (product_types.id = wf.product_type)
                $where
                AND ref.form_status != 11
                ORDER BY ref.reference_number DESC");
        } elseif ($searchDataForView['report_type'] == 'cpl') {
            $dataForView = DB::select(
                "SELECT
                    ref.*,
                    c.*,
                    product_types.name AS product_type,
                    unit_items.name AS form_type,
                    subgroup_info.name,
                    FROM_UNIXTIME(ref.date,'%Y-%m-%d') AS UNXTIME,
                    FROM_UNIXTIME(ref.access_date,'%Y-%m-%d') AS last_access_dt
                FROM complaint c
                LEFT JOIN reference ref ON (c.reference_number = ref.reference_number)
                LEFT JOIN subgroup_info ON (subgroup_info.id = ref.sub_group_info_id)
                LEFT JOIN unit_items ON (unit_items.id = c.complaint_type)
                LEFT JOIN product_types ON (product_types.id = c.product_type)
                $where
                AND ref.form_status != 11
                ORDER BY ref.reference_number DESC");
        } elseif ($searchDataForView['report_type'] == 'srsb') {
            $dataForView = DB::select(
                "SELECT
                    ref.*,
                    wf.*,
                    product_types.name AS product_type,
                    unit_items.name AS form_type,
                    mv.breach_time,
                    subgroup_info.name,
                    FROM_UNIXTIME(ref.date,'%Y-%m-%d') AS UNXTIME,
                    FROM_UNIXTIME(ref.access_date,'%Y-%m-%d') AS last_access_dt
                FROM w_form wf
                INNER JOIN issue_breaches mv ON (wf.reference_number = mv.reference_no)
                LEFT JOIN reference ref ON (wf.reference_number = ref.reference_number)
                LEFT JOIN subgroup_info ON (subgroup_info.id = ref.sub_group_info_id)
                LEFT JOIN unit_items ON (unit_items.id = wf.w_form_type)
                LEFT JOIN product_types ON (product_types.id = wf.product_type)
                $where
                ORDER BY ref.reference_number DESC");

        } elseif ($searchDataForView['report_type'] == 'csb') {
            $dataForView = DB::select(
                "SELECT
                    ref.*,
                    c.*,
                    product_types.name AS product_type,
                    unit_items.name AS form_type,
                    mv.breach_time,
                    subgroup_info.name,
                    FROM_UNIXTIME(ref.date,'%Y-%m-%d') AS UNXTIME,
                    FROM_UNIXTIME(ref.access_date,'%Y-%m-%d') AS last_access_dt
                FROM complaint c
                INNER JOIN issue_breaches mv ON (c.reference_number = mv.reference_no)
                LEFT JOIN reference ref ON (c.reference_number = ref.reference_number)
                LEFT JOIN subgroup_info ON (subgroup_info.id = ref.sub_group_info_id)
                LEFT JOIN unit_items ON (unit_items.id = c.complaint_type)
                LEFT JOIN product_types ON (product_types.id = c.product_type)
                $where
                ORDER BY ref.reference_number DESC");
        }

        return view('Dashboard.report_index', compact('title','title_for_layout','home_menu_icon','searchDataForView','dataForView'));
    }

    public function getUserWiseUnitCondition($reqType = 0, $sdate = 0, $edate = 0) {

        $wFormData = array();
        $wFormDataObj = array();
        $complaintData = array();
        $complaintDataObj = array();
        $nonCustomerData = array();
        $nonCustomerDataObj = array();

        $unitDeptCondition = array();

        $isAdmin = false;
        $getUnitId = "";
        $getUnitIdArr = array();
        $getSubGroupIdArr = array();
        $getDepartmentId = "";
        $getDivisionId = "";
        $getGroupId = "";
        $get_subgroup_info_id = "";

        if ($reqType == 0) {
            $dataObj = DB::table('comments')->select('reference_number');
        } else {
            $dataObj = DB::table('comments')->select(DB::raw('CONCAT("\'",reference_number, "\'") AS reference_number'));
        }

        if (Auth::user()->hasRole(['superadmin', 'admin'])) {
            $isAdmin = true;
        } else {
            $unitList = Auth::user()->user_unit;

            if (!empty($unitList)) {
                $getUnitId = $unitList->unit_id;
                $getDepartmentId = $unitList->department_id;
                $getDivisionId = $unitList->division_id;
                $get_subgroup_info_id = $unitList->subgroup_info_id;
                $getGroupId = $unitList->group_info_id;
            }
            if (!empty($getUnitId)) {
                $getUnitIdArr  = explode(',', $getUnitId);
                if($getUnitId != "1,2" && $getUnitId != "2,1" && $getUnitId != "1" && $getUnitId != "2") {
                    $getUnitIdArr = array();
                }
                /*$getGroup = SubgroupInfo::find($get_subgroup_info_id);
                $getGroupId='';
                if(!empty($getGroup)){
                    $getGroupId=$getGroup->group_info_id;
                }*/
            }
            if (!empty($getGroupId)) {
                $subGroupInfoModel = new SubgroupInfo;
                $subGroupInfoArr = $subGroupInfoModel->where('group_info_id',$getGroupId)->pluck('id','id');
                if (!empty($subGroupInfoArr)) {
                    $getSubGroupIdArr = $subGroupInfoArr->toArray();;
                }
            }
            if (!empty($getDepartmentId)) {
                $subGroupInfoModel = new SubgroupInfo;
                $subGroupInfoArr = $subGroupInfoModel->where('department_id',$getDepartmentId)->pluck('id','id');
                if (!empty($subGroupInfoArr)) {
                    $tmpGetSubGroupIdArr = $subGroupInfoArr->toArray();
                    $getSubGroupIdArr = array_merge($getSubGroupIdArr,$tmpGetSubGroupIdArr);
                }
            }

            if (!empty($getUnitIdArr)) {
                $dataObj = $dataObj->whereIn('comments.unit_id',$getUnitIdArr);
            }

            if (!empty($getSubGroupIdArr) || !empty($get_subgroup_info_id)) {
                $dataObj = $dataObj->where(function($q) use ($get_subgroup_info_id,$getSubGroupIdArr) {
                      $q->where("comments.subgroup_id",$get_subgroup_info_id)
                        ->orWhereIn("comments.subgroup_id",$getSubGroupIdArr)
                        ;
                });
            }

            /*
            if (!empty($getDepartmentId)) {
                $unitChildModel = new UnitChild;
                $unitChildArr = $unitChildModel->where('department_id',$getDepartmentId)->pluck('unit_id','unit_id')->toArray();
                if (!empty($unitChildArr)) {
                    $getUnitIdArr = array_merge($getUnitIdArr,$unitChildArr);
                }
            }
            */
            /*if (!empty($getDivisionId)) {
                $unitChildModel = new UnitChild;
                $unitChildArr =
                    $unitChildModel
                        ->select('unit_childs.unit_id')
                        ->leftJoin('departments','departments.id','unit_childs.department_id')
                        ->where('departments.division_id',$getDivisionId)
                        ->pluck('unit_childs.unit_id','unit_childs.unit_id')
                        ->toArray();
                if (!empty($unitChildArr)) {
                    $getUnitIdArr = array_merge($getUnitIdArr,$unitChildArr);
                }
            }*/
        }
        if ($reqType == 0) {

             if (!empty($sdate) && !empty($edate)) {
                $dataObj = $dataObj->whereBetween('time', [strtotime($sdate.' 00:00:00'), strtotime($edate.' 23:59:59')])->pluck('reference_number');
            }else{
                $dataObj = $dataObj->pluck('reference_number');
            }
        } else {
            if (!empty($sdate) && !empty($edate)) {
                $dataObj = $dataObj->whereBetween('time', [strtotime($sdate.' 00:00:00'), strtotime($edate.' 23:59:59')])->pluck('reference_number')->implode(',');
            }else{
                $dataObj = $dataObj->pluck('reference_number')->implode(',');
            }
            $dataObj = (!empty($dataObj)) ? $dataObj : "'ZZZZZZZ','XXXXXXX'";
        }
        return $dataObj;
    }

    public function getUserWiseUnitConditionForColumn($reqType = 0, $sdate = 0, $edate = 0) {

        $wFormData = array();
        $wFormDataObj = array();
        $complaintData = array();
        $complaintDataObj = array();
        $nonCustomerData = array();
        $nonCustomerDataObj = array();

        $unitDeptCondition = array();

        $isAdmin = false;
        $getUnitId = "";
        $getUnitIdArr = array();
        $getSubGroupIdArr = array();
        $getDepartmentId = "";
        $getDivisionId = "";
        $getGroupId = "";
        $get_subgroup_info_id = "";

        if ($reqType == 0) {
            $dataObj = DB::table('reference')->select('reference_number');
        } else {
            $dataObj = DB::table('reference')->select(DB::raw('CONCAT("\'",reference_number, "\'") AS reference_number'));
        }

        if (Auth::user()->hasRole(['superadmin', 'admin'])) {
            $isAdmin = true;
        } else {
            $unitList = Auth::user()->user_unit;

            if (!empty($unitList)) {
                $getUnitId = $unitList->unit_id;
                $getDepartmentId = $unitList->department_id;
                $getDivisionId = $unitList->division_id;
                $get_subgroup_info_id = $unitList->subgroup_info_id;
                $getGroupId = $unitList->group_info_id;
            }
            if (!empty($getUnitId)) {
                $getUnitIdArr  = explode(',', $getUnitId);
                if($getUnitId != "1,2" && $getUnitId != "2,1" && $getUnitId != "1" && $getUnitId != "2") {
                    $getUnitIdArr = array();
                }
                /*$getGroup = SubgroupInfo::find($get_subgroup_info_id);
                $getGroupId='';
                if(!empty($getGroup)){
                    $getGroupId=$getGroup->group_info_id;
                }*/
            }
            if (!empty($getGroupId)) {
                $subGroupInfoModel = new SubgroupInfo;
                $subGroupInfoArr = $subGroupInfoModel->where('group_info_id',$getGroupId)->pluck('id','id');
                if (!empty($subGroupInfoArr)) {
                    $getSubGroupIdArr = $subGroupInfoArr->toArray();;
                }
            }
            if (!empty($getDepartmentId)) {
                $subGroupInfoModel = new SubgroupInfo;
                $subGroupInfoArr = $subGroupInfoModel->where('department_id',$getDepartmentId)->pluck('id','id');
                if (!empty($subGroupInfoArr)) {
                    $tmpGetSubGroupIdArr = $subGroupInfoArr->toArray();
                    $getSubGroupIdArr = array_merge($getSubGroupIdArr,$tmpGetSubGroupIdArr);
                }
            }

            if (!empty($getUnitIdArr)) {
                $dataObj = $dataObj->whereIn('reference.unit_id',$getUnitIdArr);
            }

            if (!empty($getSubGroupIdArr) || !empty($get_subgroup_info_id)) {
                $dataObj = $dataObj->where(function($q) use ($get_subgroup_info_id,$getSubGroupIdArr) {
                      $q->where("reference.sub_group_info_id",$get_subgroup_info_id)
                        ->orWhereIn("reference.subgroup_id",$getSubGroupIdArr)
                        ;
                });
            }

            /*
            if (!empty($getDepartmentId)) {
                $unitChildModel = new UnitChild;
                $unitChildArr = $unitChildModel->where('department_id',$getDepartmentId)->pluck('unit_id','unit_id')->toArray();
                if (!empty($unitChildArr)) {
                    $getUnitIdArr = array_merge($getUnitIdArr,$unitChildArr);
                }
            }
            */
            /*if (!empty($getDivisionId)) {
                $unitChildModel = new UnitChild;
                $unitChildArr =
                    $unitChildModel
                        ->select('unit_childs.unit_id')
                        ->leftJoin('departments','departments.id','unit_childs.department_id')
                        ->where('departments.division_id',$getDivisionId)
                        ->pluck('unit_childs.unit_id','unit_childs.unit_id')
                        ->toArray();
                if (!empty($unitChildArr)) {
                    $getUnitIdArr = array_merge($getUnitIdArr,$unitChildArr);
                }
            }*/
        }
        if ($reqType == 0) {

             if (!empty($sdate) && !empty($edate)) {
                $dataObj = $dataObj->whereBetween('date', [strtotime($sdate.' 00:00:00'), strtotime($edate.' 23:59:59')])->pluck('reference_number');
            }else{
                $dataObj = $dataObj->pluck('reference_number');
            }
        } else {
            if (!empty($sdate) && !empty($edate)) {
                $dataObj = $dataObj->whereBetween('date', [strtotime($sdate.' 00:00:00'), strtotime($edate.' 23:59:59')])->pluck('reference_number')->implode(',');
            }else{
                $dataObj = $dataObj->pluck('reference_number')->implode(',');
            }
            $dataObj = (!empty($dataObj)) ? $dataObj : "'ZZZZZZZ','XXXXXXX'";
        }
        return $dataObj;
    }
}
