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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
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
use Modules\Requisition\App\Models\RequisitionNonPrinting;
use Modules\Requisition\App\Models\RequisitionSubCategory;
use Modules\Requisition\App\Constants\RequisitionConstants;
use Modules\Requisition\App\Models\RequisitionTableHistory;
use Modules\Requisition\App\Service\RequisitionEmailService;
use Modules\Requisition\App\Http\Requests\RequisitionRequest;
use Modules\Requisition\App\Http\Requests\workingOnREQRequest;
use Modules\Requisition\App\Models\OutgoingVendorCommunication;
use Modules\Requisition\App\Service\VendorCommunicationService;

class NonPrintingRequisitionController extends Controller
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

    public function create()
    {
        $items = RequisitionItem::with(['unit', 'subCategory', 'category'])
            ->select('id', 'product_name', 'unit_id', 'product_code', 'type', 'image', 'sub_category_id', 'category_id')
            ->where('status', 1)
            ->where('type', RequisitionConstants::NONPRINTINGITEM)
            ->get();
        return view('requisition::requisition.table-stationery', compact('items'));
    }

    public function nonPrintingItemStore(RequisitionRequest $request)
    {
        try {
            // DB::beginTransaction();
            $unitList = Auth::user()->user_unit;

            $prodTypeAlpha = "";
            $requisition_number =
                "T" .
                date("ymd") . $prodTypeAlpha .
                userIdPadLeftWith0($this->dayWiseSequence('sr'), 6, '0');

            $requisition = new Requisition();
            $requisition->user_id = Auth::id();
            $requisition->requisition_number = $requisition_number;
            $requisition->type = RequisitionConstants::NONPRINTINGITEM;
            $requisition->specification = $request->specification;
            $requisition->justification = $request->justification;
            $requisition->remark = $request->remark;
            $requisition->created_by = Auth::user()->user_id;
            $requisition->unit_id = 53; // REQ Admin Maker
            $requisition->subgroup_id = 205; // Table Stationery
            $requisition->sub_group_info_id = 389; // Table Stationery
            $requisition->source_unit_id = 25; // REQ Admin Maker
            $requisition->source_subgroup_id = $unitList->group_info_id; // Source Group
            $requisition->source_sub_group_info_id = $unitList->subgroup_info_id; // Source Subgroup
            $requisition->date = strtotime(date('d-m-Y h:i:s A'));
            $requisition->i_phone = $request->i_phone;
            $requisition->i_address = $request->i_address;
            $requisition->i_branch_id = $request->i_branch_id;
            $requisition->save();

            $product_ids = $request->product_ids;
            $quantities = $request->quantities;

            foreach ($product_ids as $index => $product_id) {
                $printingItem = new RequisitionNonPrinting();
                $printingItem->requisition_number = $requisition->requisition_number;
                $printingItem->product_id = $product_id;
                $printingItem->quantity = $quantities[$index] ?? 1;
                $printingItem->save();
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
                $message = 'Requisition saved as draft.' . ' Requisition Number : ' . $requisition->requisition_number;
                $redirect = redirect()->route('requisition.index');
            } elseif ($request->submit == 'submit') {
                $message = 'Requisition created successfully.' . ' Requisition Number : ' . $requisition->requisition_number;
                $redirect = redirect()->route('requisition.index');
            }

            $this->audit([
                'requisition_number' => $requisition->requisition_number,
                'unit_id' => 25, //Unit -> REQ Checker
                'group_id' => '',
                'subgroup_id' => $unitList->subgroup_info_id,
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

    public function list(Request $request)
    {
        $query = Requisition::with('user')->select('id', 'requisition_number', 'user_id', 'specification', 'remark', 'status', 'created_at')
            ->where('type', RequisitionConstants::NONPRINTINGITEM);

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('requisition_number', 'like', '%' . $searchTerm . '%');
        }

        $tblData = $query->paginate(10);

        return view('requisition::requisition.table-stationery.index', compact('tblData'));
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
        $isSupervisor = false;

        $unitList = Auth::user()->user_unit;

        $requisition = Requisition::with(
            [
                'tableStationeries.requItem.stock',
                'user',
                'tableRequHistories.user',
                'requAttachments.requisitionItem',
            ]
        )->select(
            "requisition.*",
            "subgroup_info.name as sub_group_name",
            "group_info.name as group_name",
        )
            ->leftJoin('subgroup_info', 'subgroup_info.id', '=', 'requisition.sub_group_info_id')
            ->leftJoin('group_info', 'group_info.id', '=', 'requisition.subgroup_id')
            ->where('requisition_number', $requisition_number)
            ->first();

        $vendors = Vendor::select('id', 'name')->where('status', 1)->get();
        $vendors_info = $requisition->vendors;
        $outgoing_vendor_mails = OutgoingVendorCommunication::where('requisition_number', $requisition_number)->get();

        $vendor_mail_status = [];
        foreach ($outgoing_vendor_mails as $mail) {
            $vendor_mail_status[$mail->vendor_id] = $mail->status;
        }

        if (!empty($requisition)) {
            if ($unitList->unit_id == 25 || $requisition->status == RequisitionConstants::REQ_APPROVE) {
                $actionAccess = false;
            }

            if ($unitList->unit_id == 33) {
                $actionAccess = true;
                $isSupervisor = true;
            }
            $tblData = $requisition->toArray();
        }

        return view('requisition::requisition.table-stationery.details', compact(
            'requisition',
            'tblData',
            'actionAccess',
            'isSupervisor',
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

        $requisition = Requisition::with(
            [
                'tableStationeries.requItem.stock',
                'user',
                'tableRequHistories.user',
                'requAttachments',
            ]
        )->select(
            "requisition.*",
            "subgroup_info.name as sub_group_name",
            "group_info.name as group_name",
        )
            ->leftJoin('subgroup_info', 'subgroup_info.id', '=', 'requisition.sub_group_info_id')
            ->leftJoin('group_info', 'group_info.id', '=', 'requisition.subgroup_id')
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

        return view('requisition::requisition.table-stationery.history_details', compact(
            'requisition',
            'tblData',
            'actionAccess',
            'vendors',
            'vendors_info',
            'vendor_mail_status',
        ));
    }

    public function getVendorDetails(Request $request)
    {
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

        $items = RequisitionItem::with(['unit', 'subCategory', 'category'])
            ->select('id', 'product_name', 'unit_id', 'product_code', 'type', 'sub_category_id', 'image', 'category_id')
            ->where('status', 1)
            ->where('type', RequisitionConstants::NONPRINTINGITEM)
            ->get();

        $requisition = Requisition::with(['tableStationeries.requItem', 'requAttachments'])->where('requisition_number', $requisition_number)->first();

        Requisition::where('requisition_number', $requisition_number)->update([
            'vendor_product_map' => null,
            'vendors_id' => null
        ]);
        return view('requisition::requisition.table-stationery.edit', compact('requisitionSubCategories', 'items', 'requisition'));
    }

    public function update(RequisitionRequest $request, $id)
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

            if ($requisition->is_sendback == 1) {
                $requisition->unit_id = 53; // REQ Admin Maker
                $requisition->subgroup_id = 205; // Table Stationery
                $requisition->sub_group_info_id = 389; // Table Stationery
                $requisition->status = RequisitionConstants::REQ_PENDING;
                $requisition->access_by = null;
                $requisition->access_date = null;
                $requisition->is_sendback = 0;

                $this->audit([
                    'requisition_number' => $requisition->requisition_number,
                    'unit_id' => 53,
                    'group_id' => $unitList->group_info_id,
                    'subgroup_id' => $unitList->subgroup_info_id,
                    'region_id' =>  0,
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
            $existingProductIds = RequisitionNonPrinting::where('requisition_number', $requisition->requisition_number)
                ->pluck('product_id')
                ->toArray();

            $productIdsToDelete = array_diff($existingProductIds, $product_ids);

            if (!empty($productIdsToDelete)) {
                RequisitionNonPrinting::where('requisition_number', $requisition->requisition_number)
                    ->whereIn('product_id', $productIdsToDelete)
                    ->delete();
            }

            // update
            foreach ($product_ids as $index => $product_id) {
                RequisitionNonPrinting::updateOrCreate(
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

            flash('Requisition updated successfully.', 'success');
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

    public function workingOnTableREQ(workingOnREQRequest $request, RequisitionEmailService $emailSender)
    {
        $isVendorEmail = false;
        $requisition_no = $request->requisition_no;
        $redirect_url = "requisition/my-requisition-list";
        $flash_message = "";

        $requisitionModelName = new Requisition();
        $requisitionModelObj = $requisitionModelName->where("requisition_number", $requisition_no)->first();
        $createUserEmail = User::where('id', $requisitionModelObj->user_id)->pluck('email')->first();

        $access_by_check = $requisitionModelName->where("requisition_number", $requisition_no)->where('access_by', '<>', Auth::user()->user_id)->first();
        //pr($access_by_check);
        if (!empty($access_by_check) && $request->action != 'close') {
            flash("Requisition No: $requisition_no is not available in your queue!!!", 'danger');
            return redirect($redirect_url);
        }

        $unit = UserUnit::where('user_id', Auth::id())->first();
        $unitIdArray = explode(",", $unit->unit_id);

        if ($request->action == 'approve_forward') {
            $vendor_data = $request->vendor_data ?? [];

            if (in_array(53, $unitIdArray) || in_array(33, $unitIdArray)) {
                $requisitionModelObj->status = RequisitionConstants::REQ_PENDING;
                $requisitionModelObj->unit_id = 54;
                $requisitionModelObj->access_by = null;
                $requisitionModelObj->access_date = null;

                if ($request->approve_qty) {
                    foreach ($request->approve_qty as $key => $qty) {
                        RequisitionNonPrinting::where('id', $key)->update(['approve_qty' => $qty[0]]);
                    }
                }


                if (empty($vendor_data)) {
                    $existingVendorIds = $requisitionModelObj->vendors_id;

                    if (!empty($existingVendorIds)) {
                        $flash_message = "Requisition No: $requisition_no has been Approved & forwarded successfully (using existing vendor data).";
                    } else {
                        $redirect_url = "requisition/table-stationery/details/" . encrypt($requisition_no);
                        $flash_message = "Requisition No: $requisition_no Please assign a vendor first!!.";
                        flash($flash_message, 'danger');
                        return redirect($redirect_url);
                    }
                } else {
                    foreach ($vendor_data as $index => $data) {
                        if (empty($data['vendor_id']) || empty($data['product_id']) || empty($data['product_id'][0])) {
                            $redirect_url = "requisition/table-stationery/details/" . encrypt($requisition_no);
                            $flash_message = "Requisition No: $requisition_no Vendor and Product must be selected for all rows.";
                            flash($flash_message, 'danger');
                            return redirect($redirect_url);
                        }
                    }

                    $requ_products = RequisitionNonPrinting::select('product_id')->where('requisition_number', $requisition_no)->get()->toArray();
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

                    if (!empty($missingProducts)) {
                        $redirect_url = "requisition/table-stationery/details/" . encrypt($requisition_no);
                        $flash_message = "Requisition No: $requisition_no Please assign vendor for all products.";
                        flash($flash_message, 'danger');
                        return redirect($redirect_url);
                    }

                    $vendorIds = array_map(function ($item) {
                        return $item['vendor_id'];
                    }, $request->vendor_data);


                    $existingVendorIds = $requisitionModelObj->vendors_id;
                    if (!empty($existingVendorIds)) {
                        $existingArray = explode(', ', $existingVendorIds);
                        $vendorIds = array_unique(array_merge($existingArray, $vendorIds));
                    }

                    $vendors_id_string = implode(', ', $vendorIds);
                    $requisitionModelObj->vendors_id = $vendors_id_string;

                    $flash_message = "Requisition No: $requisition_no has been Approved & forwarded successfully.";
                }

                $this->audit([
                    'requisition_number' => $requisition_no,
                    'unit_id' => 53,
                    'group_id' => $requisitionModelObj->subgroup_id,
                    'subgroup_id' => $requisitionModelObj->sub_group_info_id,
                    'user_id' => Auth::user()->user_id,
                    'action' => Session::get('subgroupStr') . " Approved & Forwarded",
                    'comments' => $request->comments,
                    'time' => strtotime(date('Y-m-d H:i:s')),
                    'isapproved' => '1'
                ]);

                $isVendorEmail = true;
            } else {
                flash("You don't have permission to approve & forward this requisition No: $requisition_no!!!", 'danger');
                return redirect($redirect_url);
            }
        } elseif ($request->action == 'approve') {
            $flash_message = "Requisition No: $requisition_no has been Final Approved successfully.";
            if (in_array(54, $unitIdArray) || in_array(33, $unitIdArray)) {
                $requisitionModelObj->status = RequisitionConstants::REQ_PENDING;
                // $requisitionModelObj->status = RequisitionConstants::REQ_APPROVE;
                $requisitionModelObj->delivery_date = Carbon::now();
                $requisitionModelObj->unit_id = 25;
                $requisitionModelObj->is_sendback = 10;
                $requisitionModelObj->access_by = null;
                $requisitionModelObj->access_date = null;
                $requisitionModelObj->subgroup_id = $requisitionModelObj->source_subgroup_id;
                $requisitionModelObj->sub_group_info_id = $requisitionModelObj->source_sub_group_info_id;

                if ($request->approve_qty) {
                    foreach ($request->approve_qty as $key => $qty) {
                        RequisitionNonPrinting::where('id', $key)->update(['approve_qty' => $qty[0]]);
                    }
                }

                $outgoingEmailMessage = $emailSender->outgoingSMSEmail('Table Stationary', $requisition_no, "approve");
                if (!empty($outgoingEmailMessage['mail'])) {
                    if (!empty($createUserEmail)) {
                        $emailSender->sendEMAIL($createUserEmail, $outgoingEmailMessage['mail'], $requisition_no, 0);
                    }
                }

                $this->outgoingVendorMail($requisition_no);

                $this->audit([
                    'requisition_number' => $requisition_no,
                    'unit_id' => 54,
                    'group_id' => $requisitionModelObj->subgroup_id,
                    'subgroup_id' => $requisitionModelObj->sub_group_info_id,
                    'user_id' => Auth::user()->user_id,
                    'action' => Session::get('subgroupStr') . " Final Approved",
                    'comments' => $request->comments,
                    'time' => strtotime(date('Y-m-d H:i:s')),
                    'isapproved' => ''
                ]);
            } else {
                flash("You don't have permission to approve & forward this requisition No: $requisition_no!!!", 'danger');
                return redirect($redirect_url);
            }
        } elseif ($request->action == 'cancel') {
            $flash_message = "Requisition No: $requisition_no has been cancelled successfully.";
            $requisitionModelObj->status = RequisitionConstants::REQ_CANCEL;

            $outgoingEmailMessage = $emailSender->outgoingSMSEmail('Table Stationary', $requisition_no, "cancel");
            if (!empty($outgoingEmailMessage['mail'])) {
                if (!empty($createUserEmail)) {
                    $emailSender->sendEMAIL($createUserEmail, $outgoingEmailMessage['mail'], $requisition_no, 0);
                }
            }

            $this->audit([
                'requisition_number' => $requisition_no,
                'unit_id' => $requisitionModelObj->unit_id, // Unit -> REQ Admin Maker or REQ Admin Checker
                'group_id' => $requisitionModelObj->subgroup_id,
                'subgroup_id' => $requisitionModelObj->sub_group_info_id,
                'user_id' => Auth::user()->user_id,
                'action' => Session::get('subgroupStr') . " Cancel Requisition",
                'comments' => $request->comments,
                'time' => strtotime(date('Y-m-d H:i:s')),
                'isapproved' => ''
            ]);
        } elseif ($request->action == 'sendback') {
            $flash_message = "Requisition No: $requisition_no has been sent back successfully.";

            $requisitionModelObj->status = RequisitionConstants::REQ_PENDING;
            $requisitionModelObj->unit_id = 25; //REQ Admin Maker
            $requisitionModelObj->access_by = null;
            $requisitionModelObj->access_date = null;
            $requisitionModelObj->subgroup_id = $requisitionModelObj->source_subgroup_id;
            $requisitionModelObj->sub_group_info_id = $requisitionModelObj->source_sub_group_info_id;
            $requisitionModelObj->is_sendback = 1;

            $outgoingEmailMessage = $emailSender->outgoingSMSEmail('Table Stationary', $requisition_no, "sendback");
            if (!empty($outgoingEmailMessage['mail'])) {
                if (!empty($createUserEmail)) {
                    $emailSender->sendEMAIL($createUserEmail, $outgoingEmailMessage['mail'], $requisition_no, 0);
                }
            }

            $this->audit([
                'requisition_number' => $requisition_no,
                'unit_id' => $requisitionModelObj->unit_id, // Unit -> REQ Admin Maker or REQ Admin Checker
                'group_id' => $requisitionModelObj->subgroup_id,
                'subgroup_id' => $requisitionModelObj->sub_group_info_id,
                'user_id' => Auth::user()->user_id,
                'action' => Session::get('subgroupStr') . " send back to source",
                'comments' => $request->comments,
                'time' => strtotime(date('Y-m-d H:i:s')),
                'isapproved' => '1'
            ]);
        } elseif ($request->action == 'close') {
            $user_unit = "";
            if (in_array($requisitionModelObj->unit_id, userUnits())) {
                $user_unit = $requisitionModelObj->unit_id;
            } else {
                $user_unit = 25;
            }

            $flash_message = "Requisition No: $requisition_no has been closed successfully.";
            $requisitionModelObj->status = RequisitionConstants::REQ_CLOSE;
            $requisitionModelObj->receive_date = $request->receive_date;

            $outgoingEmailMessage = $emailSender->outgoingSMSEmail('Table Stationary', $requisition_no, "close");
            if (!empty($outgoingEmailMessage['mail'])) {
                if (!empty($createUserEmail)) {
                    $emailSender->sendEMAIL($createUserEmail, $outgoingEmailMessage['mail'], $requisition_no, 0);
                }
            }

            $this->audit([
                'requisition_number' => $requisition_no,
                'unit_id' => $user_unit,
                'group_id' => $requisitionModelObj->subgroup_id,
                'subgroup_id' => $requisitionModelObj->sub_group_info_id,
                'user_id' => Auth::user()->user_id,
                'action' => Session::get('subgroupStr') . " close requisition",
                'comments' => $request->comments,
                'time' => strtotime(date('Y-m-d H:i:s')),
                'isapproved' => ''
            ]);
        } elseif ($request->action == 'hold') {

            $user_id = Auth::user()->user_id;
            $subgroup_id = Auth::user()->user_unit->subgroup_info_id;
            $group_info_id = SubgroupInfo::find($subgroup_id);

            $requisitionModelObj = $requisitionModelObj->where('requisition_number', $requisition_no)->first();

            if ($requisitionModelObj->is_sendback == 1) {
                $redirectUrl = 'requisition/my-requisition-list';
            } else {
                $redirectUrl = "requisition/table-stationery/details/" . encrypt($requisition_no);
            }

            if ($requisitionModelObj->status == RequisitionConstants::REQ_PENDING) {
                flash("Requisition No: $requisition_no is not assigned to you!", 'danger');
                return redirect($redirectUrl);
            }

            if ($requisitionModelObj->status == RequisitionConstants::REQ_HOLD) {
                flash("Requisition No: $requisition_no this ticket already held to you!", 'danger');
                return redirect($redirectUrl);
            }

            $vendor_data = $request->vendor_data ?? [];

            if (in_array(53, $unitIdArray) || in_array(33, $unitIdArray)) {
                if ($request->approve_qty) {
                    foreach ($request->approve_qty as $key => $qty) {
                        RequisitionNonPrinting::where('id', $key)->update(['approve_qty' => $qty[0]]);
                    }
                }

                if (count($vendor_data) > 0) {
                    foreach ($vendor_data as $index => $data) {
                        if (empty($data['vendor_id']) || empty($data['product_id']) || empty($data['product_id'][0])) {
                            $redirect_url = "requisition/table-stationery/details/" . encrypt($requisition_no);
                            $flash_message = "Requisition No: $requisition_no Vendor and Product must be selected for all rows.";
                            flash($flash_message, 'danger');
                            return redirect($redirect_url);
                        }
                    }

                    if ($request->vendor_data) {
                        $requ_products = RequisitionNonPrinting::select('product_id')->where('requisition_number', $requisition_no)->get()->toArray();
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

                        if (!empty($missingProducts)) {
                            $redirect_url = "requisition/table-stationery/details/" . encrypt($requisition_no);
                            $flash_message = "Requisition No: $requisition_no Please assign vendor for all products.";
                            flash($flash_message, 'danger');
                            return redirect($redirect_url);
                        }

                        $vendorIds = array_map(function ($item) {
                            return $item['vendor_id'];
                        }, $request->vendor_data);

                        $vendors_id_string = implode(', ', $vendorIds);
                        $requisitionModelObj->vendors_id = $vendors_id_string;
                    }
                }
            }



            $requisitionModelObj->status = RequisitionConstants::REQ_HOLD;
            $flash_message = "Requisition No: $requisition_no has been held successfully.";
            $requisitionModelObj->access_date = strtotime(date('Y-m-d H:i:s'));

            $this->audit([
                'requisition_number' => $requisition_no,
                'unit_id' => $requisitionModelObj->unit_id,
                'group_id' => $group_info_id->group_info_id ?? 0,
                'subgroup_id' => $subgroup_id ?? 0,
                'region_id' => $requisitionModelObj->region_id ?? 0,
                'user_id' => $user_id,
                'action' => 'Held',
                'comments' => '',
                'time' => strtotime(date('Y-m-d H:i:s')),
                'isapproved' => '0'
            ]);
        }

        if ($requisitionModelObj->save()) {
            if ($isVendorEmail) {
                $this->saveVendorEmail($request->all());
            }
            flash($flash_message, 'success');
            return redirect($redirect_url);
        } else {
            flash('Failed to change status', 'danger');
            return Redirect::back();
        }
    }

    private function outgoingVendorMail($requisition_number)
    {
        try {
            $requisition = Requisition::where('requisition_number', $requisition_number)->first();
            $vendor_data = $requisition->vendor_product_map;
            $requisition_number = $requisition->requisition_number;

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

    private function saveVendorEmail($data)
    {
        $requisition = Requisition::where('requisition_number', $data['requisition_number'])->first();
        $requisition->vendor_product_map = $data['vendor_data'];
        if ($requisition->save()) {
            return true;
        } else {
            return false;
        }
    }

    public function assign($requisition_number)
    {
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

        if ($requisition->is_sendback == 1) {
            $redirectUrl = 'requisition/my-requisition-list';
        } else {
            $redirectUrl = "requisition/table-stationery/details/" . encrypt($requisition_number);
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
                'isapproved' => '0'
            ]);

            flash('Successfully Assigned', 'success');
        } else {
            flash('Failed to assign', 'danger');
        }

        return redirect($redirectUrl);
    }

    public function audit($params = array())
    {
        $commentModel = new RequisitionTableHistory();
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
