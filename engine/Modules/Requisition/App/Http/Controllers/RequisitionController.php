<?php

namespace Modules\Requisition\App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Requisition\App\Models\Requisition;
use Modules\Requisition\App\Models\RegionSubgroup;
use Illuminate\Contracts\Encryption\DecryptException;
use Modules\Requisition\App\Exports\RequisitionExport;
use Modules\Requisition\App\Models\RequisitionAttachment;
use Modules\Requisition\App\Constants\RequisitionConstants;
use Modules\Requisition\App\Http\Requests\AttachmentUpload;
use Modules\Requisition\App\Models\RequisitionPrintingHistory;
use Modules\Requisition\App\Models\RequisitionSupportServiceHistory;
use Modules\Requisition\App\Models\RequisitionTableHistory;

class RequisitionController extends Controller
{
    public function index(Request $request)
    {
        // dd($request->all());

        $getUnitId = "";
        $getUnitIdArr = array();
        $getSubGroupIdArr = array();
        $getRegionIdArr = array();
        $getDepartmentId = "";
        $getDivisionId = "";
        $getGroupId = "";
        $isLogger = false;
        $isSupervisor = false;
        $historyRange = "";

        if (request()->history_range) {
            $historyRange = request()->history_range;
        }

        // dd($historyRange);

        $searchDataForView = $request->all();

        if ((!empty($request->active_tab))) {
            $searchDataForView['active_tab'] = $request->active_tab;
        } else {
            $searchDataForView['active_tab'] = 'my_req';
        }

        $unitList = Auth::user()->user_unit;

        $unitsIdArr = explode(',', $unitList->unit_id);

        if (in_array(25, $unitsIdArr)) {
            $isLogger = true;
        }

        if (in_array(33, $unitsIdArr)) {
            $isSupervisor = true;
        }

        if (!empty($unitList)) {
            $getRegionIdArr = explode(',', $unitList->region_id);
            $getUnitId = $unitList->unit_id;
            $get_subgroup_info_id = $unitList->subgroup_info_id;
            $getGroupId = $unitList->group_info_id;
        }

        if (!empty($getUnitId)) {
            $getUnitIdArr = explode(',', $getUnitId);
        }


        $query = Requisition::with('user', 'subgroup')
            ->select(
                "requisition.*",
                "subgroup_info.name as sub_group_name",
                "group_info.name as group_name",
                "requisition_region.name as region_name",
                DB::raw("FROM_UNIXTIME(requisition.date,'%Y-%m-%d & %h:%i:%s %p') AS date_UNXTIME")
            )
            ->leftJoin('subgroup_info', 'subgroup_info.id', '=', 'requisition.sub_group_info_id')
            ->leftJoin('group_info', 'group_info.id', '=', 'requisition.subgroup_id')
            ->leftJoin('requisition_region', 'requisition_region.id', '=', 'requisition.region_id');

        if ($isLogger) {
            if (!empty($getUnitIdArr)) {
                $query = $query->whereIn("requisition.source_unit_id", $getUnitIdArr);
            }
            $query = $query->where(function ($q) use ($getUnitIdArr, $getGroupId, $get_subgroup_info_id, $getSubGroupIdArr) {
                $q->where("requisition.source_sub_group_info_id", $get_subgroup_info_id);
            });

            if ($searchDataForView['active_tab'] == 'my_req') {
                $query = $query->whereIn("requisition.status", [
                    RequisitionConstants::REQ_PENDING,
                    RequisitionConstants::REQ_WIP,
                    RequisitionConstants::REQ_DRAFT
                ]);
            } else {

                // check 7 days check
                $query = $query->whereBetween('requisition.created_at', [
                    Carbon::today()->subDays($historyRange - 1)->startOfDay(),
                    Carbon::today()->endOfDay()
                ]);

                

                $query = $query->whereIn("requisition.status", [
                    RequisitionConstants::REQ_PENDING,
                    RequisitionConstants::REQ_WIP,
                    RequisitionConstants::REQ_DRAFT
                ]);

                // dd($query->count());  
            }
        } else {
            if ($searchDataForView['active_tab'] == 'my_req') {
                if (!empty($getUnitIdArr)) {
                    if (!$isSupervisor) {
                        $query = $query->whereIn("requisition.unit_id", $getUnitIdArr);
                    }
                }

                $query = $query->where(function ($q) use ($getUnitIdArr, $getGroupId, $get_subgroup_info_id, $getSubGroupIdArr) {
                    $q->where("requisition.sub_group_info_id", $get_subgroup_info_id)
                        ->orWhereIn("requisition.sub_group_info_id", $getSubGroupIdArr)
                    ;
                });

                $query = $query->whereNotIn("requisition.status", [
                    RequisitionConstants::REQ_CLOSE,
                    RequisitionConstants::REQ_DRAFT,
                    RequisitionConstants::REQ_APPROVE,
                    RequisitionConstants::REQ_CANCEL
                ]);
            } else {
                $sevenDaysAgo = Carbon::now()->subDays($historyRange)->timestamp;
                if ($get_subgroup_info_id == 390) { // For Printing History
                    $query = $query->join('requisition_printing_histories', 'requisition_printing_histories.requisition_number', '=', 'requisition.requisition_number');
                    $query = $query->addSelect(
                        'requisition_printing_histories.time',
                        'requisition_printing_histories.action',
                        'requisition_printing_histories.subgroup_id as subgroup_id_history',
                        'requisition_printing_histories.user_id as user_id_history',
                        'requisition_printing_histories.unit_id as unit_id_history',
                    );

                    $query = $query->where('requisition_printing_histories.subgroup_id', '=', $get_subgroup_info_id);
                    $query = $query->whereIn('requisition_printing_histories.unit_id', $getUnitIdArr);
                    $query = $query->where('requisition_printing_histories.user_id', '=', Auth::user()->user_id);

                    if ($request->filled('date_from') && $request->filled('date_to')) {
                        $dateFromUnix = strtotime($request->date_from . ' 00:00:00');
                        $dateToUnix = strtotime($request->date_to . ' 23:59:59');
                        $query = $query->whereBetween('requisition_printing_histories.time', [$dateFromUnix, $dateToUnix]);
                    } else {
                        $query = $query->where('requisition_printing_histories.time', '>=', $sevenDaysAgo);
                    }

                    $query = $query->groupBy('requisition.requisition_number');
                } elseif ($get_subgroup_info_id == 389) { // For Table History
                    $query = $query->join('requisition_table_histories', 'requisition_table_histories.requisition_number', '=', 'requisition.requisition_number');
                    $query = $query->addSelect(
                        'requisition_table_histories.time',
                        'requisition_table_histories.action',
                        'requisition_table_histories.subgroup_id as subgroup_id_history',
                        'requisition_table_histories.user_id as user_id_history',
                        'requisition_table_histories.unit_id as unit_id_history',
                    );

                    $query = $query->where('requisition_table_histories.subgroup_id', '=', $get_subgroup_info_id);
                    $query = $query->whereIn('requisition_table_histories.unit_id', $getUnitIdArr);
                    $query = $query->where('requisition_table_histories.user_id', '=', Auth::user()->user_id);

                    if ($request->filled('date_from') && $request->filled('date_to')) {
                        $dateFromUnix = strtotime($request->date_from . ' 00:00:00');
                        $dateToUnix = strtotime($request->date_to . ' 23:59:59');

                        $query = $query->whereBetween('requisition_table_histories.time', [$dateFromUnix, $dateToUnix]);
                    } else {
                        $query = $query->where('requisition_table_histories.time', '>=', $sevenDaysAgo);
                    }

                    $query = $query->groupBy('requisition.requisition_number');
                } else { // For Support Service History
                    $query = $query->join('requisition_support_service_histories', 'requisition_support_service_histories.requisition_number', '=', 'requisition.requisition_number');
                    $query = $query->addSelect(
                        'requisition_support_service_histories.time',
                        'requisition_support_service_histories.action',
                        'requisition_support_service_histories.subgroup_id as subgroup_id_history',
                        'requisition_support_service_histories.user_id as user_id_history',
                        'requisition_support_service_histories.unit_id as unit_id_history',
                    );

                    $query = $query->where('requisition_support_service_histories.subgroup_id', '=', $get_subgroup_info_id);
                    $query = $query->whereIn('requisition_support_service_histories.unit_id', $getUnitIdArr);
                    $query = $query->where('requisition_support_service_histories.user_id', '=', Auth::user()->user_id);

                    if ($request->filled('date_from') && $request->filled('date_to')) {
                        $dateFromUnix = strtotime($request->date_from . ' 00:00:00');
                        $dateToUnix = strtotime($request->date_to . ' 23:59:59');

                        $query = $query->whereBetween('requisition_support_service_histories.time', [$dateFromUnix, $dateToUnix]);
                    } else {
                        $query = $query->where('requisition_support_service_histories.time', '>=', $sevenDaysAgo);
                    }

                    $query = $query->groupBy('requisition.requisition_number');
                }
            }
        }

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('requisition.requisition_number', 'like', '%' . $searchTerm . '%');
        }

        if ($request->filled('category')) {
            $category = $request->input('category');

            $categoryMap = [
                'repair' => RequisitionConstants::REPAIRITEM,
                'newAsset' => RequisitionConstants::NEWASSETITEM,
                'printingStationery' => RequisitionConstants::PRINTINGITEM,
                'tableStationery'    => RequisitionConstants::NONPRINTINGITEM,
            ];

            if (isset($categoryMap[$category])) {
                $query->where('type', $categoryMap[$category]);
            }
        }

        if ($request->filled('status')) {
            $statuses = $request->input('status');

            // Map the string values to their constant equivalents
            $statusMap = [
                'wip' => RequisitionConstants::REQ_WIP,
                'pending' => RequisitionConstants::REQ_PENDING,
                'draft' => RequisitionConstants::REQ_DRAFT,
                'approved' => RequisitionConstants::REQ_APPROVE,
                'closed' => RequisitionConstants::REQ_CLOSE,
                'cancelled' => RequisitionConstants::REQ_CANCEL,
            ];

            $filteredStatuses = collect($statuses)
                ->filter(fn($s) => isset($statusMap[$s]))
                ->map(fn($s) => $statusMap[$s])
                ->values()
                ->toArray();

            if (!empty($filteredStatuses)) {
                $query->whereIn('requisition.status', $filteredStatuses);
            }
        }

        if ($searchDataForView['active_tab'] == 'my_req' || $isLogger) {
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $startDate = Carbon::parse($request->input('date_from'))->startOfDay();
                $endDate = Carbon::parse($request->input('date_to'))->endOfDay();
                $query->whereBetween('requisition.created_at', [$startDate, $endDate]);
            }
        }

        $tblData = $query->paginate(10);

        $data = [
            'searchDataForView' => $searchDataForView,
            'tblData' => $tblData,
        ];
        // dd($tblData);
        return view('requisition::requisition.handler', $data);
    }

    public function create()
    {
        $hasRegion = false;
        $subgroup_info_id = Auth::user()->user_unit->subgroup_info_id;

        $region_id = RegionSubgroup::where('subgroup_info_id', $subgroup_info_id)->first();

        if (!empty($region_id)) {
            $region_id = $region_id->region_id;
        }

        if ($region_id) {
            $hasRegion = true;
        } else {
            $hasRegion = false;
        }

        return view('requisition::requisition.create', compact('hasRegion'));
    }

    public function publish($requisition_number)
    {
        try {
            $requisition_number = decrypt($requisition_number);
        } catch (DecryptException $e) {
            abort(403, 'Un-Authorize Access!!!');
        }

        $charArr = str_split($requisition_number);

        if ($charArr['0'] === 'G') {
            $commentModel = new RequisitionSupportServiceHistory();
        } elseif ($charArr['0'] === 'P') {
            $commentModel = new RequisitionPrintingHistory();
        } elseif ($charArr['0'] === 'T') {
            $commentModel = new RequisitionTableHistory();
        }

        try {
            $commentUpdate = $commentModel->where('requisition_number', $requisition_number)->first();
            $commentUpdate->isapproved = 1;
            $commentUpdate->save();

            $requisition = Requisition::where('requisition_number', $requisition_number)->first();
            $requisition->status = 1;
            $requisition->save();

            flash('Requisition published successfully.', 'success');
            return redirect()->route('requisition.index');
        } catch (\Throwable $th) {
            Log::error('Error publishing requisition: ', [$th->getMessage()]);
            flash('An error occurred while publishing the requisition.', 'danger');
            return redirect()->back();
        }
    }

    public function attachUpload(AttachmentUpload $request)
    {
        $attachmentFile = $request->file('attachment');
        $attachmentTitle = $request->input('attachment_title');
        $item_id = $request->input('item_id');

        $docDestPath = 'public/attachments';

        $extension = $attachmentFile->getClientOriginalExtension();
        $origin_name = pathinfo($attachmentFile->getClientOriginalName(), PATHINFO_FILENAME);
        $origin_name = str_replace(' ', '_', $origin_name);
        $origin_name = substr($origin_name, 0, 20);
        $fileName = $origin_name . "_admin_attach_" . round(microtime(true) * 10) . '.' . $extension;

        $attachment = new RequisitionAttachment();
        $attachment->requisition_number = $request->requisition_number;
        $attachment->product_id = $item_id;
        $attachment->attachment_title = $attachmentTitle;
        $attachment->is_admin_attach = "1";
        $attachment->attachment = $fileName;
        $attachment->save();

        // Save the file
        $attachmentFile->move($docDestPath, $fileName);

        // For Live
        // $fileContent = File::get($attachmentFile->getRealPath());
        // Storage::disk('custom_storage')->put($fileName, $fileContent);
        return response()->json([
            'message' => 'Attachment uploaded successfully.',
            'status' => true
        ], 200);
    }

    public function deleteAttachment($id)
    {
        try {
            $attachment = RequisitionAttachment::findOrFail($id);
            $filePath = 'public/attachments/' . $attachment->attachment;

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $attachment->delete();

            return response()->json([
                'message' => 'Attachment deleted successfully.',
                'success' => true
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong.',
                'success' => false
            ], 500);
        }
    }

    public function handlerExport(Request $request)
    {
        $activeTab = $request->query('active_tab');
        if (empty($activeTab)) {
            $activeTab = "my_req";
        }

        $getUnitId = "";
        $getUnitIdArr = array();
        $getSubGroupIdArr = array();
        $getRegionIdArr = array();
        $getDepartmentId = "";
        $getDivisionId = "";
        $getGroupId = "";
        $isLogger = false;

        $unitList = Auth::user()->user_unit;

        $unitsIdArr = explode(',', $unitList->unit_id);

        if (in_array(25, $unitsIdArr)) {
            $isLogger = true;
        }

        if (!empty($unitList)) {
            $getRegionIdArr = explode(',', $unitList->region_id);
            $getUnitId = $unitList->unit_id;
            $get_subgroup_info_id = $unitList->subgroup_info_id;
            $getGroupId = $unitList->group_info_id;
        }

        if (!empty($getUnitId)) {
            $getUnitIdArr = explode(',', $getUnitId);
        }

        $query = Requisition::with('user', 'subgroup')
            ->select(
                "requisition.*",
                "subgroup_info.name as sub_group_name",
                "group_info.name as group_name",
                "requisition_region.name as region_name",
                DB::raw("FROM_UNIXTIME(requisition.date,'%Y-%m-%d & %h:%i:%s %p') AS date_UNXTIME")
            )
            ->leftJoin('subgroup_info', 'subgroup_info.id', '=', 'requisition.sub_group_info_id')
            ->leftJoin('group_info', 'group_info.id', '=', 'requisition.subgroup_id')
            ->leftJoin('requisition_region', 'requisition_region.id', '=', 'requisition.region_id');

        if ($isLogger) {
            if (!empty($getUnitIdArr)) {
                $query = $query->whereIn("requisition.source_unit_id", $getUnitIdArr);
            }
            $query = $query->where(function ($q) use ($getUnitIdArr, $getGroupId, $get_subgroup_info_id, $getSubGroupIdArr) {
                $q->where("requisition.source_sub_group_info_id", $get_subgroup_info_id);
            });

            if ($activeTab == 'my_req') {
                $query = $query->whereIn("requisition.status", [RequisitionConstants::REQ_PENDING, RequisitionConstants::REQ_WIP, RequisitionConstants::REQ_DRAFT]);
            } else {
                $query = $query->whereBetween('requisition.created_at', [
                    Carbon::today()->subDays(6)->startOfDay(),
                    Carbon::today()->endOfDay()
                ]);

                $query = $query->whereIn("requisition.status", [RequisitionConstants::REQ_APPROVE, RequisitionConstants::REQ_CANCEL, RequisitionConstants::REQ_CLOSE]);
            }
        } else {
            if ($activeTab == 'my_req') {
                if (!empty($getUnitIdArr)) {
                    $query = $query->whereIn("requisition.unit_id", $getUnitIdArr);
                }

                $query = $query->where(function ($q) use ($getUnitIdArr, $getGroupId, $get_subgroup_info_id, $getSubGroupIdArr) {
                    $q->where("requisition.sub_group_info_id", $get_subgroup_info_id)
                        ->orWhereIn("requisition.sub_group_info_id", $getSubGroupIdArr)
                    ;
                });

                $query = $query->whereNotIn("requisition.status", [RequisitionConstants::REQ_CLOSE, RequisitionConstants::REQ_DRAFT, RequisitionConstants::REQ_APPROVE, RequisitionConstants::REQ_CANCEL]);
            } else {
                $sevenDaysAgo = Carbon::now()->subDays(7)->timestamp;

                if ($get_subgroup_info_id == 390) { // For Printing History
                    $query = $query->join('requisition_printing_histories', 'requisition_printing_histories.requisition_number', '=', 'requisition.requisition_number');
                    $query = $query->addSelect(
                        'requisition_printing_histories.time',
                        'requisition_printing_histories.action',
                        'requisition_printing_histories.subgroup_id as subgroup_id_history',
                        'requisition_printing_histories.user_id as user_id_history',
                        'requisition_printing_histories.unit_id as unit_id_history',
                    );

                    $query = $query->where('requisition_printing_histories.subgroup_id', '=', $get_subgroup_info_id);
                    $query = $query->whereIn('requisition_printing_histories.unit_id', $getUnitIdArr);
                    $query = $query->where('requisition_printing_histories.user_id', '=', Auth::user()->user_id);
                    $query = $query->where('requisition_printing_histories.time', '>=', $sevenDaysAgo);
                    $query = $query->groupBy('requisition.requisition_number');
                } elseif ($get_subgroup_info_id == 389) { // For Table History
                    $query = $query->join('requisition_table_histories', 'requisition_table_histories.requisition_number', '=', 'requisition.requisition_number');
                    $query = $query->addSelect(
                        'requisition_table_histories.time',
                        'requisition_table_histories.action',
                        'requisition_table_histories.subgroup_id as subgroup_id_history',
                        'requisition_table_histories.user_id as user_id_history',
                        'requisition_table_histories.unit_id as unit_id_history',
                    );

                    $query = $query->where('requisition_table_histories.subgroup_id', '=', $get_subgroup_info_id);
                    $query = $query->whereIn('requisition_table_histories.unit_id', $getUnitIdArr);
                    $query = $query->where('requisition_table_histories.user_id', '=', Auth::user()->user_id);
                    $query = $query->where('requisition_table_histories.time', '>=', $sevenDaysAgo);
                    $query = $query->groupBy('requisition.requisition_number');
                } else { // For Support Service History
                    $query = $query->join('requisition_support_service_histories', 'requisition_support_service_histories.requisition_number', '=', 'requisition.requisition_number');
                    $query = $query->addSelect(
                        'requisition_support_service_histories.time',
                        'requisition_support_service_histories.action',
                        'requisition_support_service_histories.subgroup_id as subgroup_id_history',
                        'requisition_support_service_histories.user_id as user_id_history',
                        'requisition_support_service_histories.unit_id as unit_id_history',
                    );

                    $query = $query->where('requisition_support_service_histories.subgroup_id', '=', $get_subgroup_info_id);
                    $query = $query->whereIn('requisition_support_service_histories.unit_id', $getUnitIdArr);
                    $query = $query->where('requisition_support_service_histories.user_id', '=', Auth::user()->user_id);
                    $query = $query->where('requisition_support_service_histories.time', '>=', $sevenDaysAgo);
                    $query = $query->groupBy('requisition.requisition_number');
                }
            }
        }

        $tblData = $query->get()->map(function ($item) {
            // Convert status code to text
            $status = 'N/A';
            if ($item->status == RequisitionConstants::REQ_PENDING) {
                $status = "Pending";
            } elseif ($item->status == RequisitionConstants::REQ_DRAFT) {
                $status = "Draft";
            } elseif ($item->status == RequisitionConstants::REQ_WIP) {
                $status = "WIP";
            } elseif ($item->status == RequisitionConstants::REQ_APPROVE) {
                $status = "Approved";
            } elseif ($item->status == RequisitionConstants::REQ_CLOSE) {
                $status = "Closed";
            } elseif ($item->status == RequisitionConstants::REQ_CANCEL) {
                $status = "Cancel";
            }

            // Convert type code to text
            $type = 'N/A';
            if ($item->type == RequisitionConstants::NONPRINTINGITEM) {
                $type = "Table Stationery";
            } elseif ($item->type == RequisitionConstants::PRINTINGITEM) {
                $type = "Printing Stationery";
            } elseif ($item->type == RequisitionConstants::REPAIRITEM) {
                $type = "Repair & Maintenance";
            } elseif ($item->type == RequisitionConstants::NEWASSETITEM) {
                $type = "Replacement/New Asset";
            }

            // Convert unit_id to role
            $role = '';
            if ($item->unit_id == 53) {
                $role = "REQ Admin Maker";
            } elseif ($item->unit_id == 54) {
                $role = "REQ Admin Checker";
            } elseif ($item->unit_id == 25) {
                $role = "REQ Initiator";
            }

            $position = $item->region_name ?? $item->sub_group_name ?? 'N/A';
            $currentPosition = $position;
            if (!empty($role)) {
                $currentPosition = $position . ' [' . $role . ']';
            }

            $lastWorkBy = $item->access_by ?? 'N/A';
            $lastWorkBy = 'assigned to ' . $lastWorkBy;

            return [
                'requisition_number' => (string) ($item->requisition_number ?? 'N/A'),
                'subgroup_name'      => (string) ($item->subgroup->name ?? 'N/A'),
                'user_id'            => (string) ($item->user->user_id ?? 'N/A'),
                'date_UNXTIME'       => (string) ($item->date_UNXTIME ?? 'N/A'),
                'status'             => $status,
                'type'               => $type,
                'current_position'   => $currentPosition,
                'last_work_by'       => $lastWorkBy,
            ];
        })->toArray();

        return Excel::download(new RequisitionExport($tblData, $activeTab), 'requisition.xlsx');
    }
}
