<?php

namespace Modules\Requisition\App\Http\Controllers;

use App\User;
use App\Sequence;
use App\UserUnit;
use Carbon\Carbon;
use App\SubgroupInfo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Modules\Requisition\App\Models\Vendor;
use Modules\Requisition\App\Models\Requisition;
use Modules\Requisition\App\Models\RequisitionItem;
use Illuminate\Contracts\Encryption\DecryptException;
use Modules\Requisition\App\Models\RequisitionComments;
use Modules\Requisition\App\Models\RequisitionPrinting;
use Modules\Requisition\App\Models\RequisitionAttachment;
use Modules\Requisition\App\Models\RequisitionSubCategory;
use Modules\Requisition\App\Constants\RequisitionConstants;
use Modules\Requisition\App\Service\RequisitionEmailService;
use Modules\Requisition\App\Http\Requests\RequisitionRequest;
use Modules\Requisition\App\Models\RequisitionGeneralService;
use Modules\Requisition\App\Http\Requests\workingOnREQRequest;
use Modules\Requisition\App\Models\OutgoingVendorCommunication;
use Modules\Requisition\App\Service\VendorCommunicationService;
use Modules\Requisition\App\Models\RequisitionSupportServiceHistory;

class SupportServiceRequisitionController extends Controller
{
    protected $vendorCommunicationService;

    public function __construct(VendorCommunicationService $vendorCommunicationService)
    {
        $this->vendorCommunicationService = $vendorCommunicationService;
    }

    public function dayWiseSequence($sequenceFrom = "")
    {
        $sequenceModel = new Sequence($sequenceFrom);
        $lastSequence = $sequenceModel->where('created_at', '<', date('Y-m-d'))->orderBy('id', 'DESC')->first();
        if (!empty($lastSequence)) {
            $sequenceModel->truncate();
        }

        $sequenceModel = new Sequence($sequenceFrom);
        $sequenceModel->save();

        return $sequenceModel->id;
    }

    public function createSupportService($type)
    {
        if(!in_array($type, [RequisitionConstants::REPAIR_MAINTENANCE, RequisitionConstants::NEW_ASSET])) {
            flash('Invalid type selected.', 'danger');
            return redirect()->back();
        }

        $requItemType = $type == RequisitionConstants::REPAIR_MAINTENANCE
            ? RequisitionConstants::REPAIRITEM
            : RequisitionConstants::NEWASSETITEM;

        $items = RequisitionItem::with(['unit', 'subCategory', 'category'])
            ->select('id', 'product_name', 'unit_id', 'product_code', 'type', 'image', 'sub_category_id', 'category_id')
            ->where('status', 1)
            ->where('type', $requItemType)
            ->whereHas('subCategory', function ($query) use ($type) {
                $query->where('sub_category_type', $type);
            })->get();
        
        return view('requisition::requisition.support-service', compact('items', 'type'));
    }

    public function supportServiceStore(RequisitionRequest $request)
    {
        try {
            // DB::beginTransaction();
            $type = $request->type;
            if (!in_array($type, [RequisitionConstants::REPAIR_MAINTENANCE, RequisitionConstants::NEW_ASSET])) {
                flash('Invalid type selected.', 'danger');
                return redirect()->back();
            }

            $unitList = Auth::user()->user_unit;
            $subgroup_info_id = $unitList->subgroup_info_id;
            $subgroup_id = SubgroupInfo::find($subgroup_info_id)->group_info_id;

            $prodTypeAlpha = "";
            $requisition_number =
                "G" .
                date("ymd") . $prodTypeAlpha .
                userIdPadLeftWith0($this->dayWiseSequence('sr'), 6, '0');

            $requisition = new Requisition();
            $requisition->user_id = Auth::id();
            $requisition->requisition_number = $requisition_number;
            $requisition->type = $type == RequisitionConstants::REPAIR_MAINTENANCE
                ? RequisitionConstants::REPAIRITEM
                : RequisitionConstants::NEWASSETITEM;
            $requisition->created_by = Auth::user()->user_id;
            $requisition->unit_id = 53; // REQ Admin Maker
            $requisition->subgroup_id = 0;
            $requisition->sub_group_info_id = 0;
            $requisition->region_id = getUserRegion($unitList->subgroup_info_id);
            $requisition->source_unit_id = 25; // REQ Checker
            $requisition->source_subgroup_id = $subgroup_id; // Source Group
            $requisition->source_sub_group_info_id = $subgroup_info_id; // Source Subgroup
            $requisition->date = strtotime(date('d-m-Y h:i:s A'));
            $requisition->specification = $request->specification;
            $requisition->justification = $request->justification;
            $requisition->remark = $request->remark;
            $requisition->i_phone = $request->i_phone;
            $requisition->i_address = $request->i_address;
            $requisition->i_branch_id = $request->i_branch_id;
            $requisition->save();

            // General Services Save
            $product_ids = $request->product_ids;
            $quantities = $request->quantities;

            foreach ($product_ids as $index => $product_id) {
                $general_service = new RequisitionGeneralService();
                $general_service->requisition_number = $requisition->requisition_number;
                $general_service->product_id = $product_id;
                $general_service->quantity = $quantities[$index];
                $general_service->save();
            }

            $attachments = $request->attachments;
            $docDestPath = 'public/attachments';
            if (!empty($attachments)) {
                foreach ($attachments as $key => $files) {
                    if (!empty($files['file']) && !empty($files['title'])) {
                        $extension = $files['file']->getClientOriginalExtension();
                        $origin_name = pathinfo($files['file']->getClientOriginalName(), PATHINFO_FILENAME);
                        $origin_name = str_replace(' ', '_', $origin_name);
                        $origin_name = substr($origin_name, 0, 20);
                        $fileName = $origin_name . "_attach_nX_" . round(microtime(true) * 10) . "_" . ($key + 1) . '.' . $extension;

                        $attachment = new RequisitionAttachment();
                        $attachment->requisition_number = $requisition->requisition_number;
                        $attachment->attachment_title = $files['title'];
                        $attachment->attachment = $fileName;
                        $attachment->save();

                        // For Local
                        $files['file']->move($docDestPath, $fileName);

                        // For Live
                        // $fileContent = File::get($files['file']->getRealPath());
                        // Storage::disk('custom_storage')->put($fileName, $fileContent);
                    }
                }
            }

            if ($request->submit == 'draft') {
                $requisition->status = 2;
                $requisition->save();
                $message = 'Requisition saved as draft.' . $requisition->requisition_number;
                $redirect = redirect()->route('requisition.index');
            } elseif ($request->submit == 'submit') {
                $message = 'Requisition created successfully.' . ' Requisition number : ' . $requisition->requisition_number;
                $redirect = redirect()->route('requisition.index');
            }

            $this->audit([
                'requisition_number' => $requisition->requisition_number,
                'unit_id' => 25, //Unit -> REQ Checker
                'group_id' => $subgroup_id,
                'subgroup_id' => $subgroup_info_id,
                'user_id' => Auth::user()->user_id,
                'action' => Session::get('subgroupStr') . ' Logged',
                'comments' => '',
                'time' => strtotime(date('Y-m-d H:i:s')),
                'isapproved' => $request->submit == 'submit' ? '1' : ''
            ]);

            // DB::commit();
            flash($message, 'success');
            return $redirect;
        } catch (\Throwable $th) {
            // if (DB::transactionLevel() > 0) {
            //     DB::rollBack();
            // }
            Log::error('Error saving requisition: ', [$th->getMessage()]);
            flash('An error occured while creating the requisition.', 'danger');
            return back();
        }
    }

    public function details($requisition_number)
    {
        try {
            $requisition_number = decrypt($requisition_number);
        } catch (DecryptException $e) {
            abort(403, 'Un-Authorize Access!!!');
        }

        $tblData = array();
        $actionAccess = true;

        $unitList = Auth::user()->user_unit;

        $requisition = Requisition::with(
            ['generalServices.requItem',
             'user',
             'serviceRequHistories.user',
             'requAttachments',
            ])->select(
                "requisition.*",
                "subgroup_info.name as sub_group_name",
                "group_info.name as group_name",
                "requisition_region.name as region_name",
            )
            ->leftJoin('subgroup_info', 'subgroup_info.id', '=', 'requisition.sub_group_info_id')
            ->leftJoin('group_info', 'group_info.id', '=', 'requisition.subgroup_id')
            ->leftJoin('requisition_region', 'requisition_region.id', '=', 'requisition.region_id')
            ->where('requisition_number', $requisition_number)
            ->first();

        $vendors = Vendor::select('id', 'name')->where('status', 1)->get();
        $vendors_info = $requisition->vendors;

        $outgoing_vendor_mails = OutgoingVendorCommunication::where('requisition_number', $requisition_number)
            ->get();

        $vendor_mail_status = [];
        foreach ($outgoing_vendor_mails as $mail) {
            $vendor_mail_status[$mail->vendor_id] = $mail->status;
        }

        if (!empty($requisition)) {
            if ($unitList->unit_id == 25 || $requisition->status == RequisitionConstants::REQ_APPROVE) {
                $actionAccess = false;
            }
            $tblData = $requisition->toArray();
        }
        return view('requisition::requisition.general-service.details', compact(
            'requisition',
            'tblData',
            'actionAccess',
            'vendors',
            'vendors_info',
            'vendor_mail_status',
        ));
    }

    public function history($requisition_number)
    {
        try {
            $requisition_number = decrypt($requisition_number);
        } catch (DecryptException $e) {
            abort(403, 'Un-Authorize Access!!!');
        }

        $tblData = array();
        $actionAccess = true;

        $unitList = Auth::user()->user_unit;

        $requisition = Requisition::with(['generalServices.requItem', 'user', 'serviceRequHistories.user', 'requAttachments'])
            ->select(
                "requisition.*",
                "subgroup_info.name as sub_group_name",
                "group_info.name as group_name",
                "requisition_region.name as region_name",
            )
            ->leftJoin('subgroup_info', 'subgroup_info.id', '=', 'requisition.sub_group_info_id')
            ->leftJoin('group_info', 'group_info.id', '=', 'requisition.subgroup_id')
            ->leftJoin('requisition_region', 'requisition_region.id', '=', 'requisition.region_id')
            ->where('requisition_number', $requisition_number)
            ->first();

        $vendors_info = $requisition->vendors;

        $outgoing_vendor_mails = OutgoingVendorCommunication::where('requisition_number', $requisition_number)
            ->get();

        $vendor_mail_status = [];
        foreach ($outgoing_vendor_mails as $mail) {
            $vendor_mail_status[$mail->vendor_id] = $mail->status;
        }

        if (!empty($requisition)) {
            if ($unitList->unit_id == 25 || $requisition->status == RequisitionConstants::REQ_APPROVE) {
                $actionAccess = false;
            }
            $tblData = $requisition->toArray();
        }
        return view('requisition::requisition.general-service.history_details', compact(
            'requisition',
            'tblData',
            'actionAccess',
            'vendors_info',
            'vendor_mail_status',
        ));
    }

    public function getVendorDetails(Request $request){
        $vendors = Vendor::whereIn('id', $request->vendorsId)->get();
        $vendorsData = [];

        if ($vendors) {
            foreach ($vendors as $key => $vendor) {
                $vendorsData[] = [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'address' => $vendor->office_address,
                    'phone' => $vendor->phone,
                    'email' => $vendor->email,
                ];
            }

            return response()->json([
                'status' => 'success',
                'data' => $vendorsData,
            ]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Vendor not found.'], 404);
        }
    }

    public function edit($requisition_number)
    {
        try {
            $requisition_number = decrypt($requisition_number);
        } catch (DecryptException $e) {
            abort(403, 'Un-Authorize Access!!!');
        }

        $requisitionSubCategories = RequisitionSubCategory::with('category')
            ->select('id', 'category_id', 'sub_category_name')
            ->whereHas('category', function ($query) {
                $query->where('category_name', 'General Support Service');
            })->get();
        
        $requisition = Requisition::with(['generalServices.requItem', 'requAttachments'])->where('requisition_number', $requisition_number)->first();

        $type = RequisitionConstants::NEWASSETITEM;
        if($requisition->type == RequisitionConstants::REPAIRITEM){
            $type = RequisitionConstants::REPAIRITEM;
        }
            
        $items = RequisitionItem::with(['unit', 'subCategory', 'category'])
            ->select('id', 'product_name', 'unit_id', 'product_code', 'type', 'image', 'sub_category_id', 'category_id')
            ->where('status', 1)
            ->where('type', $type)
            ->get();
        
        return view('requisition::requisition.general-service.edit', compact('requisitionSubCategories', 'items', 'requisition'));
    }

    public function supportServiceUpdate(RequisitionRequest $request, $id)
    {
        try {
            // DB::beginTransaction();
            $unitList = Auth::user()->user_unit;

            $requisition = Requisition::find($id);
            $requisition->user_id = Auth::id();
            $requisition->specification = $request->specification;
            $requisition->justification = $request->justification;
            $requisition->remark = $request->remark;
            $requisition->i_phone = $request->i_phone;
            $requisition->i_address = $request->i_address;
            $requisition->i_branch_id = $request->i_branch_id;

            if ($requisition->is_sendback == 1){
                $requisition->unit_id = 53; // REQ Admin Maker
                $requisition->subgroup_id = 0;
                $requisition->sub_group_info_id = 0;
                $requisition->region_id = getUserRegion($unitList->subgroup_info_id);
                $requisition->status = RequisitionConstants::REQ_PENDING;
                $requisition->access_by = null;
                $requisition->access_date = null;
                $requisition->is_sendback = 0;

                $this->audit([
                    'requisition_number' => $requisition->requisition_number,
                    'unit_id' => 53,
                    'group_id' => 0,
                    'subgroup_id' => 0,
                    'region_id' =>  getUserRegion($unitList->subgroup_info_id),
                    'user_id' => Auth::user()->user_id,
                    'action' => 'Modified & Approved!',
                    'comments' => '',
                    'time' => strtotime(date('Y-m-d H:i:s')),
                    'isapproved' => '1'
                ]);
            }

            $requisition->save();

            // existing General Services delete
            $product_ids = $request->product_ids;

            $existingProductIds = RequisitionGeneralService::where('requisition_number', $requisition->requisition_number)
                ->pluck('product_id')
                ->toArray();

            $productIdsToDelete = array_diff($existingProductIds, $product_ids);

            if (!empty($productIdsToDelete)) {
                RequisitionGeneralService::where('requisition_number', $requisition->requisition_number)
                    ->whereIn('product_id', $productIdsToDelete)
                    ->delete();
            }

            // update
            foreach ($product_ids as $index => $product_id) {
                RequisitionGeneralService::updateOrCreate(
                    [
                        'requisition_number' => $requisition->requisition_number,
                        'product_id' => $product_id,
                    ],
                    [
                        'quantity' => $request->quantities[$index] ?? 0,
                    ]
                );
            }

            $attachments = $request->attachments ?? [];
            $titles = [];

            foreach ($attachments as $attachment) {
                $titles[] = $attachment['title'];
            }

            $existingTitles = RequisitionAttachment::where('requisition_number', $requisition->requisition_number)
                ->pluck('attachment_title')
                ->toArray();

            $titlesToDelete = array_diff($existingTitles, $titles);

            if (!empty($titlesToDelete)) {
                RequisitionAttachment::where('requisition_number', $requisition->requisition_number)
                    ->whereIn('attachment_title', $titlesToDelete)
                    ->delete();
            }

            $docDestPath = 'public/attachments';
            if (!empty($attachments)) {
                foreach ($attachments as $key => $files) {
                    if (!empty($files['file']) && !empty($files['title'])) {
                        $extension = $files['file']->getClientOriginalExtension();
                        $origin_name = pathinfo($files['file']->getClientOriginalName(), PATHINFO_FILENAME);
                        $origin_name = str_replace(' ', '_', $origin_name);
                        $origin_name = substr($origin_name, 0, 20);
                        $fileName = $origin_name . "_attach_nX_" . round(microtime(true) * 10) . "_" . ($key + 1) . '.' . $extension;

                        // Find if an attachment already exists
                        $existing = RequisitionAttachment::where([
                            'requisition_number' => $requisition->requisition_number,
                            'attachment_title' => $files['title'],
                        ])->first();

                        // Delete old file if exists
                        if ($existing && !empty($existing->attachment)) {
                            $oldFilePath = realpath(base_path('../public')) . '/attachments/' . $existing->attachment;

                            if (File::exists($oldFilePath)) {
                                File::delete($oldFilePath);
                            } else {
                                Log::warning('File not found: ' . $oldFilePath);
                            }
                        }

                        RequisitionAttachment::updateOrCreate(
                            [
                                'requisition_number' => $requisition->requisition_number,
                                'attachment_title' => $files['title'],
                            ],
                            [
                                'attachment' => $fileName,
                            ]
                        );

                        // For Local
                        $files['file']->move($docDestPath, $fileName);

                        // For Live
                        // $fileContent = File::get($files->getRealPath());
                        // Storage::disk('custom_storage')->put($fileName, $fileContent);
                    }
                }
            }

            // DB::commit();

            flash('Requisition update successfully.', 'success');
            return redirect()->route('requisition.index');
        } catch (\Throwable $th) {
            // if (DB::transactionLevel() > 0) {
            //     DB::rollBack();
            // }
            Log::error('Error updating requisition: ', [$th->getMessage()]);
            flash('An error occurred while updating the requisition.', 'danger');
            return back();
        }
    }

    public function workingOnREQ(workingOnREQRequest $request, RequisitionEmailService $emailSender)
    {

        $requisition_no = $request->requisition_no;
        $redirect_url = "requisition/my-requisition-list";

        $requisitionModelName = new Requisition();
        $requisitionModelObj = $requisitionModelName->where("requisition_number", $requisition_no)->first();
        $createUserEmail = User::where('id', $requisitionModelObj->user_id)->pluck('email')->first();

        $access_by_check = $requisitionModelName->where("requisition_number", $requisition_no)->where('access_by', '<>', Auth::user()->user_id)->first();
        //pr($access_by_check);

        if (!empty($access_by_check) && $request->action != 'close') {
            flash("Requisition No: $requisition_no is not available in your queue!!!", 'danger');
            return redirect($redirect_url);
        }

        // Here need multiple check doing later

        $unit = UserUnit::where('user_id', Auth::id())->first();
        $unitIdArray = explode(",", $unit->unit_id);

        if ($request->action == 'approve_forward') {
            $vendor_data = $request->vendor_data ?? [];

            if (empty($vendor_data)) {
                return redirect()->back()->withErrors([
                    'no_vendor_error' => 'Please assign a vendor first!!'
                ]);
            }

            foreach ($vendor_data as $index => $data) {
                if (empty($data['vendor_id']) || empty($data['product_id']) || empty($data['product_id'][0])) {
                    return redirect()->back()->withErrors([
                        'no_vendor_error' => "Vendor and Product must be selected for all rows."
                    ]);
                }
            }

            $regionIdArray = explode(",", $unit->region_id);
            if (in_array($requisitionModelObj->region_id, $regionIdArray)) {
                $matchedRegionId = $requisitionModelObj->region_id;
            } else {
                flash("Requisition No: $requisition_no is not available in your region!!!", 'danger');
                return redirect($redirect_url);
            }

            $flash_message = "Requisition No: $requisition_no has been Approved & forwarded successfully.";

            if (in_array(53, $unitIdArray)) {
                $requisitionModelObj->status = RequisitionConstants::REQ_PENDING;
                $requisitionModelObj->unit_id = 54;
                $requisitionModelObj->access_by = null;
                $requisitionModelObj->access_date = null;

                foreach ($request->approve_qty as $id => $qtyArray) {
                    $approveQty = $qtyArray[0] ?? null;

                    $req_support_service = RequisitionGeneralService::find($id);
                    if ($req_support_service) {
                        $req_support_service->approve_qty = $approveQty;
                        $req_support_service->save();
                    }
                }

                if ($request->vendor_data){
                    $requ_products = RequisitionGeneralService::select('product_id')->where('requisition_number', $requisition_no)->get()->toArray();
                    $requProductIds = array_column($requ_products, 'product_id');

                    $vendorProductIds = [];
                    foreach ($request->vendor_data as $item) {
                        if (isset($item['product_id']) && is_array($item['product_id'])) {
                            foreach ($item['product_id'] as $pid) {
                                $vendorProductIds[] = $pid;
                            }
                        }
                    }

                    $missingProducts = array_diff($requProductIds, $vendorProductIds);

                    if(!empty($missingProducts)){
                        return redirect()->back()->withErrors([
                            'no_vendor_error' => "Please assign vendor for all products."
                        ]);
                    }

                    $vendorIds = array_map(function($item) {
                        return $item['vendor_id'];
                    }, $request->vendor_data);

                    $vendors_id_string = implode(', ', $vendorIds);
                    $requisitionModelObj->vendors_id = $vendors_id_string;
                }

                if($requisitionModelObj->type == RequisitionConstants::NEWASSETITEM){
                    $this->saveVendorEmail($request->all());
                }else{
                    $this->createOutgoingVendorComm($request->all());
                }

                $this->audit([
                    'requisition_number' => $requisition_no,
                    'unit_id' => 53, // Unit -> REQ Admin Maker
                    'group_id' => 0,
                    'subgroup_id' => 0,
                    'user_id' => Auth::user()->user_id,
                    'region_id' => $matchedRegionId,
                    'action' => Session::get('subgroupStr') . " Approved & Forwarded",
                    'comments' => $request->comments,
                    'time' => strtotime(date('Y-m-d H:i:s')),
                    'isapproved' => '1'
                ]);
            }
        } elseif ($request->action == 'approve') {
            $vendor_data = $request->vendor_data ?? [];

            if (empty($vendor_data)) {
                return redirect()->back()->withErrors([
                    'no_vendor_error' => 'Please assign a vendor first!!'
                ]);
            }

            foreach ($vendor_data as $index => $data) {
                if (empty($data['vendor_id']) || empty($data['product_id']) || empty($data['product_id'][0])) {
                    return redirect()->back()->withErrors([
                        'no_vendor_error' => "Vendor and Product must be selected for all rows."
                    ]);
                }
            }

            $regionIdArray = explode(",", $unit->region_id);
            if (in_array($requisitionModelObj->region_id, $regionIdArray)) {
                $matchedRegionId = $requisitionModelObj->region_id;
            } else {
                flash("Requisition No: $requisition_no is not available in your region!!!", 'danger');
                return redirect($redirect_url);
            }

            $flash_message = "Requisition No: $requisition_no has been Final Approved successfully.";

            //unit id 53 will approve both repair and new asset
            $inArrayUnitId = 53;
            // if (!empty($request->requisition_product_type)) {
            //     if ($request->requisition_product_type == RequisitionConstants::REPAIRITEM) {
            //         $inArrayUnitId = 53;
            //     } else {
            //         $inArrayUnitId = 54;
            //     }
            // }
            if (in_array($inArrayUnitId, $unitIdArray)) {
                // $requisitionModelObj->status = RequisitionConstants::REQ_APPROVE;
                $requisitionModelObj->status = RequisitionConstants::REQ_PENDING;
                $requisitionModelObj->delivery_date = Carbon::now();
                $requisitionModelObj->is_sendback = 10;
                $requisitionModelObj->unit_id = 25;

                foreach ($request->approve_qty as $id => $qtyArray) {
                    $approveQty = $qtyArray[0] ?? null;

                    $req_support_service = RequisitionGeneralService::find($id);
                    if ($req_support_service) {
                        $req_support_service->approve_qty = $approveQty;
                        $req_support_service->save();
                    }
                }

                $outgoingEmailMessage = $emailSender->outgoingSMSEmail('General Support Service', $requisition_no, "approve");
                if (!empty($outgoingEmailMessage['mail'])) {
                    if (!empty($createUserEmail)) {
                        $emailSender->sendEMAIL($createUserEmail, $outgoingEmailMessage['mail'], $requisition_no, 0);
                    }
                }

                if ($request->vendor_data){
                    $requ_products = RequisitionGeneralService::select('product_id')->where('requisition_number', $requisition_no)->get()->toArray();
                    $requProductIds = array_column($requ_products, 'product_id');

                    $vendorProductIds = [];
                    foreach ($request->vendor_data as $item) {
                        if (isset($item['product_id']) && is_array($item['product_id'])) {
                            foreach ($item['product_id'] as $pid) {
                                $vendorProductIds[] = $pid;
                            }
                        }
                    }

                    $missingProducts = array_diff($requProductIds, $vendorProductIds);

                    if(!empty($missingProducts)){
                        return redirect()->back()->withErrors([
                            'no_vendor_error' => "Please assign vendor for all products."
                        ]);
                    }

                    $vendorIds = array_map(function($item) {
                        return $item['vendor_id'];
                    }, $request->vendor_data);

                    $vendors_id_string = implode(', ', $vendorIds);
                    $requisitionModelObj->vendors_id = $vendors_id_string;
                }

                // if($requisitionModelObj->type == RequisitionConstants::NEWASSETITEM){
                //     $this->createOutgoingVendorComm($data = null, $requisitionModelObj->requisition_number);
                // }else{
                //     $this->createOutgoingVendorComm($request->all());
                // }

                $this->createOutgoingVendorComm($request->all());

                $this->audit([
                    'requisition_number' => $requisition_no,
                    'unit_id' => $inArrayUnitId,
                    'group_id' => 0,
                    'subgroup_id' => 0,
                    'region_id' => $matchedRegionId,
                    'user_id' => Auth::user()->user_id,
                    'action' => Session::get('subgroupStr') . " Final Approved",
                    'comments' => $request->comments,
                    'time' => strtotime(date('Y-m-d H:i:s')),
                    'isapproved' => ''
                ]);
            }
        } elseif ($request->action == 'cancel') {
            $regionIdArray = explode(",", $unit->region_id);
            if (in_array($requisitionModelObj->region_id, $regionIdArray)) {
                $matchedRegionId = $requisitionModelObj->region_id;
            } else {
                flash("Requisition No: $requisition_no is not available in your region!!!", 'danger');
                return redirect($redirect_url);
            }

            $flash_message = "Requisition No: $requisition_no has been cancelled successfully.";
            $requisitionModelObj->status = RequisitionConstants::REQ_CANCEL;

            $outgoingEmailMessage = $emailSender->outgoingSMSEmail('General Support Service', $requisition_no, "cancel");
            if (!empty($outgoingEmailMessage['mail'])) {
                if (!empty($createUserEmail)) {
                    $emailSender->sendEMAIL($createUserEmail, $outgoingEmailMessage['mail'], $requisition_no, 0);
                }
            }

            $this->audit([
                'requisition_number' => $requisition_no,
                'unit_id' => $requisitionModelObj->unit_id,
                'group_id' => 0,
                'subgroup_id' => 0,
                'region_id' => $matchedRegionId,
                'user_id' => Auth::user()->user_id,
                'action' => Session::get('subgroupStr') . " Cancel Requisition",
                'comments' => $request->comments,
                'time' => strtotime(date('Y-m-d H:i:s')),
                'isapproved' => ''
            ]);
        }elseif($request->action == 'close'){
            $user_unit = "";
            if(in_array($requisitionModelObj->unit_id, userUnits())){
                $user_unit = $requisitionModelObj->unit_id;
            }else{
                $user_unit = 25;
            }

            $flash_message = "Requisition No: $requisition_no has been closed successfully.";
            $requisitionModelObj->status = RequisitionConstants::REQ_CLOSE;
            $requisitionModelObj->receive_date = $request->receive_date;
            
            $outgoingEmailMessage = $emailSender->outgoingSMSEmail('General Support Service', $requisition_no, "close");
            if (!empty($outgoingEmailMessage['mail'])) {
                if (!empty($createUserEmail)) {
                    $emailSender->sendEMAIL($createUserEmail, $outgoingEmailMessage['mail'], $requisition_no, 0);
                }
            }

            $this->audit([
                'requisition_number' => $requisition_no,
                'unit_id' => $user_unit,
                'group_id' => 0,
                'subgroup_id' => 0,
                'region_id' => $requisitionModelObj->region_id,
                'user_id' => Auth::user()->user_id,
                'action' => Session::get('subgroupStr') . " Close Requisition",
                'comments' => $request->comments,
                'time' => strtotime(date('Y-m-d H:i:s')),
                'isapproved' => ''
            ]);
        }elseif($request->action == 'sendback'){
            $regionIdArray = explode(",", $unit->region_id);
            if (in_array($requisitionModelObj->region_id, $regionIdArray)) {
                $matchedRegionId = $requisitionModelObj->region_id;
            } else {
                flash("Requisition No: $requisition_no is not available in your region!!!", 'danger');
                return redirect($redirect_url);
            }

            $flash_message = "Requisition No: $requisition_no has been sent back successfully.";

            $requisitionModelObj->status = RequisitionConstants::REQ_PENDING;
            $requisitionModelObj->unit_id = 25; //REQ Initiator
            $requisitionModelObj->subgroup_id = $requisitionModelObj->source_subgroup_id;
            $requisitionModelObj->sub_group_info_id = $requisitionModelObj->source_sub_group_info_id;
            $requisitionModelObj->region_id = null;
            $requisitionModelObj->access_by = null;
            $requisitionModelObj->access_date = null;
            $requisitionModelObj->is_sendback = 1;

            $outgoingEmailMessage = $emailSender->outgoingSMSEmail('General Support Service', $requisition_no, "sendback");
            if (!empty($outgoingEmailMessage['mail'])) {
                if (!empty($createUserEmail)) {
                    $emailSender->sendEMAIL($createUserEmail, $outgoingEmailMessage['mail'], $requisition_no, 0);
                }
            }

            $this->audit([
                'requisition_number' => $requisition_no,
                'unit_id' => $requisitionModelObj->unit_id, // Unit -> REQ Admin Maker or REQ Admin Checker
                'group_id' => 0,
                'subgroup_id' => 0,
                'user_id' => Auth::user()->user_id,
                'region_id' => $matchedRegionId,
                'action' => Session::get('subgroupStr') . " send back to source",
                'comments' => $request->comments,
                'time' => strtotime(date('Y-m-d H:i:s')),
                'isapproved' => '1'
            ]);
        }

        if ($requisitionModelObj->save()) {
            flash($flash_message, 'success');
            return redirect($redirect_url);
        } else {
            flash('Failed to change status', 'danger');
            return Redirect::back();
        }
    }

    private function saveVendorEmail($data){
        $requisition = Requisition::where('requisition_number', $data['requisition_number'])->first();
        $requisition->vendor_product_map = $data['vendor_data'];
        if($requisition->save()){
            return true;
        }else{
            return false;
        }
    }

    // private function assignVendors($data){
    //     $vendor_data = $data['vendor_data'];
    //     $requisition_number = $data['requisition_number'];

    //     try {
    //         $vendorIds = array_map(function($item) {
    //             return $item['vendor_id'];
    //         }, $vendor_data);

    //         $vendors_id_string = implode(', ', $vendorIds);

    //         $requisition = Requisition::where('requisition_number', $requisition_number)->first();
    //         $requisition->vendors_id = $vendors_id_string;
    //         $requisition->save();

    //         return true;
    //     } catch (\Throwable $th) {
    //         Log::error('Error assigning vendor: ', [$th->getMessage()]);
    //         return false;
    //     }
    // }

    private function createOutgoingVendorComm($data, $requisition_number = null){
        try {
            if(!empty($requisition_number) && empty($data)){
                $requisition = Requisition::where('requisition_number', $requisition_number)->first();
                $vendor_data = $requisition->vendor_product_map;
                $requisition_number = $requisition->requisition_number;
            }else{
                $vendor_data = $data['vendor_data'];
                $requisition_number = $data['requisition_number'];
            }

            foreach ($vendor_data as $value) {
                $vendorComm = new OutgoingVendorCommunication();
                $vendorComm->requisition_number = $requisition_number;
                $vendorComm->vendor_id = $value['vendor_id'];
                $vendorComm->products_id = implode(', ', $value['product_id']);
                $vendorComm->save_time = strtotime(date('Y-m-d H:i:s'));
                $vendorComm->status = 3;
                $vendorComm->save();
            }
            return true;
        } catch (\Throwable $th) {
            Log::error('Error saving vendor communication: ', [$th->getMessage()]);
            return false;
        }
    }

    public function vendorMailGenerate()
    {
        $data = [];

        // Get the first pending vendor mail
        $vendorMail = OutgoingVendorCommunication::where('status', 3)->first();
        if (!$vendorMail) {
            return; // Nothing to process
        }

        $requisition = Requisition::with('generalServices.requItem')
            ->where('requisition_number', $vendorMail->requisition_number)
            ->first();

        if (!$requisition) {
            return; // No matching requisition
        }

        $data['requisition_number'] = $requisition->requisition_number;

        // Get bank/branch info from user
        if ($requisition->user_id) {
            $user = User::with('user_subgroupinfo')->find($requisition->user_id);
            $data['bank_info'] = [
                'contact_person' => $user->name ?? 'N/A',
                'contact_person_email' => $user->email ?? 'N/A',
                'branch' => optional($user->user_subgroupinfo)->subgroup_name ?? 'N/A',
            ];
        }

        // Prepare vendor data
        $vendor_data = [
            'vendor_id' => $vendorMail->vendor_id,
            'products_id' => array_map('trim', explode(',', $vendorMail->products_id)),
        ];

        $final_vendors_data = [];
        $vendor = Vendor::find($vendor_data['vendor_id']);
        $vendorArray = $vendor ? $vendor->toArray() : [];

        // Join product + requisition_general_service + unit
        $products = DB::table('requisition_items as ri')
            ->join('requisition_general_service as rgs', function ($join) use ($requisition) {
                $join->on('ri.id', '=', 'rgs.product_id')
                    ->where('rgs.requisition_number', '=', $requisition->requisition_number);
            })
            ->leftJoin('product_units as pu', 'ri.unit_id', '=', 'pu.id')
            ->whereIn('ri.id', $vendor_data['products_id'])
            ->select('ri.*', 'rgs.approve_qty', 'pu.name as unit_name')
            ->get();

        $final_vendors_data = [
            'vendor' => $vendorArray,
            'products' => json_decode(json_encode($products), true),
        ];

        $data['vendor_info'] = $final_vendors_data;

        // Requisition Attachments
        $attachments = RequisitionAttachment::where('requisition_number', $requisition->requisition_number)
            ->pluck('attachment')
            ->map(function ($path) {
                return $_SERVER['DOCUMENT_ROOT'] . '/public/attachments/' . $path;
            })
            ->toArray();

        $data['attachments'] = $attachments;

        // Send mail to vendors
        $this->sendRequisitionEmail($data);
    }

    private function sendRequisitionEmail($data)
    {
        $emailData = $data;
        $emailData['current_vendor'] = $data['vendor_info']; // contains 'vendor' and 'products'

        $html = View::make('requisition::requisition.general-service.mail', $emailData)->render();

        $to = $data['vendor_info']['vendor']['email'] ?? '';
        $subject = 'Requisition Request - ' . ($data['bank_info']['branch'] ?? 'N/A') . ' - ' . ($data['vendor_info']['vendor']['name'] ?? 'Vendor');
        $body = $html;

        $result = $this->vendorCommunicationService->send(
            $to,
            $subject,
            $body,
            $data['attachments'] ?? []
        );

        $outgoingVendorMail = OutgoingVendorCommunication::where('requisition_number', $data['requisition_number'])->first();
        if ($result) {
            $outgoingVendorMail->status = 7;
            $outgoingVendorMail->sent_time = strtotime(date('Y-m-d H:i:s'));
            $outgoingVendorMail->save();
        } else {
            $outgoingVendorMail->status = -1;
            $outgoingVendorMail->save();
            Log::error('Failed to send email to vendor: ' . $to);
        }

        return $result;
    }


    public function assign($requisition_number){
        try {
            $requisition_number = decrypt($requisition_number);
        } catch (DecryptException $e) {
            abort(403, 'Un-Authorize Access!!!');
        }

        $user_id = Auth::user()->user_id;
        $subgroup_id = Auth::user()->user_unit->subgroup_info_id;
        $group_info_id = SubgroupInfo::find($subgroup_id);

        $requisitionModelName = new Requisition();
        $requisition = $requisitionModelName->where('requisition_number', $requisition_number)->first();

        if ($requisition->is_sendback == 1){
            $redirectUrl = 'requisition/my-requisition-list';
        }else{
            $redirectUrl = "requisition/general-service/details/" . encrypt($requisition_number);
        }

        $row = $requisitionModelName->where("requisition_number", $requisition_number)->where('access_by', '<>', '')->first();
        if ($row) {
            flash("Requisition No: $requisition_number already assigned!!!", 'danger');
            return redirect('requisition/my-requisition-list');
        }

        $requisition->status = RequisitionConstants::REQ_WIP;
        $requisition->access_by = $user_id;
        $requisition->access_date = strtotime(date('Y-m-d H:i:s'));
        if ($requisition->save()) {
            $this->audit([
                'requisition_number' => $requisition_number,
                'unit_id' => $requisition->unit_id,
                'group_id' => $group_info_id->group_info_id ?? 0,
                'subgroup_id' => $subgroup_id ?? 0,
                'region_id' => $requisition->region_id ?? 0,
                'user_id' => $user_id,
                'action' => 'Assigned',
                'comments' => '',
                'time' => strtotime(date('Y-m-d H:i:s')),
                'isapproved' => ''
            ]);

            flash('Successfully Assigned', 'success');
        } else {
            flash('Failed to assign', 'danger');
        }

        return redirect($redirectUrl);
    }

    public function audit($params = array())
    {
        $commentModel = new RequisitionSupportServiceHistory();
        $commentModel->requisition_number = (!empty($params['requisition_number'])) ? $params['requisition_number'] : '';
        $commentModel->comment = (!empty($params['comments'])) ? $params['comments'] : '';
        $commentModel->time = strtotime(date('Y-m-d H:i:s'));
        $commentModel->user_id = (!empty($params['user_id'])) ? $params['user_id'] : 0;
        $commentModel->unit_id = (!empty($params['unit_id'])) ? $params['unit_id'] : 0;
        $commentModel->group_id = (!empty($params['group_id'])) ? $params['group_id'] : 0;
        $commentModel->action = (!empty($params['action'])) ? $params['action'] : 'INVALID';
        $commentModel->isapproved = (!empty($params['isapproved'])) ? $params['isapproved'] : 0;
        $commentModel->subgroup_id = (!empty($params['subgroup_id'])) ? $params['subgroup_id'] : 0;
        $commentModel->region_id = (!empty($params['region_id'])) ? $params['region_id'] : 0;
        $commentModel->save();
    }
}
