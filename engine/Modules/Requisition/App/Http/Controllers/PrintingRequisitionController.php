<?php

namespace Modules\Requisition\App\Http\Controllers;

use App\OutgoingEMAIL;
use App\OutgoingSMS;
use App\Sequence;
use App\SMSEmail;
use App\UserUnit;
use Carbon\Carbon;
use App\SubgroupInfo;
use App\UnitItem;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Modules\Requisition\App\Models\RequisitionPrintingHistory;
use Modules\Requisition\App\Models\Stock;
use Modules\Requisition\App\Models\Requisition;
use Modules\Requisition\App\Models\RequisitionItem;
use Illuminate\Contracts\Encryption\DecryptException;
use Modules\Requisition\App\Models\RequisitionComments;
use Modules\Requisition\App\Models\RequisitionPrinting;
use Modules\Requisition\App\Models\RequisitionAttachment;
use Modules\Requisition\App\Models\RequisitionNonPrinting;
use Modules\Requisition\App\Models\RequisitionSubCategory;
use Modules\Requisition\App\Constants\RequisitionConstants;
use Modules\Requisition\App\Http\Requests\RequisitionRequest;
use Modules\Requisition\App\Http\Requests\workingOnREQRequest;
use Modules\Requisition\App\Service\RequisitionEmailService;

class PrintingRequisitionController extends Controller
{
    // public function __construct(private RequisitionEmailService $emailSender) {}

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

    public function list(Request $request)
    {
        $query = Requisition::with('user')->select('id', 'requisition_number', 'type', 'user_id', 'specification', 'remark', 'status', 'created_at')
            ->where('type', RequisitionConstants::PRINTINGITEM);

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('requisition_number', 'like', '%' . $searchTerm . '%');
        }

        $tblData = $query->paginate(10);

        return view('requisition::requisition.printing.index', compact('tblData'));
    }

    public function createPrintingStationery()
    {
        $items = RequisitionItem::with(['unit', 'subCategory', 'category'])
            ->select('id', 'product_name', 'unit_id', 'product_code', 'type', 'sub_category_id', 'image', 'category_id')
            ->where('status', 1)
            ->where('type', RequisitionConstants::PRINTINGITEM)
            ->get();

        return view('requisition::requisition.printing-stationery', compact('items'));
    }

    public function printingItemStore(RequisitionRequest $request)
    {
        try {
            // DB::beginTransaction();
            $unitList = Auth::user()->user_unit;

            $prodTypeAlpha = "";
            $requisition_number =
                "P" .
                date("ymd") . $prodTypeAlpha .
                userIdPadLeftWith0($this->dayWiseSequence('sr'), 6, '0');

            $requisition = new Requisition();
            $requisition->requisition_number = $requisition_number;
            $requisition->user_id = Auth::id();
            $requisition->type = RequisitionConstants::PRINTINGITEM;
            $requisition->specification = $request->specification;
            $requisition->created_by = Auth::user()->user_id;
            $requisition->unit_id = 53; // REQ Admin Maker
            $requisition->subgroup_id = 206; // Painting Stationery
            $requisition->sub_group_info_id = 390; // Painting Stationery
            $requisition->source_unit_id = 25; // REQ Checker
            $requisition->source_subgroup_id = $unitList->group_info_id; // Source Group
            $requisition->source_sub_group_info_id = $unitList->subgroup_info_id; // Source Subgroup
            $requisition->date = strtotime(date('d-m-Y h:i:s A'));
            $requisition->justification = $request->justification;
            $requisition->remark = $request->remark;
            $requisition->i_phone = $request->i_phone;
            $requisition->i_address = $request->i_address;
            $requisition->i_branch_id = $request->i_branch_id;
            $requisition->save();

            $product_ids = $request->product_ids;
            $quantities = $request->quantities;

            foreach ($product_ids as $index => $product_id) {
                $printingItem = new RequisitionPrinting();
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
                        // $fileContent = File::get(files['file']->getRealPath());
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
                // $createUserEmail = User::where('id', $requisition->user_id)->pluck('email')->first();

                // // dd($createUserEmail);
                // $outgoingEmailMessage = $emailSender->outgoingSMSEmail('Printing Stationary', $requisition->requisition_number, "opening");
                // if (!empty($outgoingEmailMessage['mail'])) {
                //     if (!empty($createUserEmail)) {
                //         $emailSender->sendEMAIL($createUserEmail, $outgoingEmailMessage['mail'], $requisition->requisition_number, 0);
                //     }
                // }
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

        $requisition = Requisition::with(['printings.requItem.stock', 'user', 'printingRequHistories.user', 'requAttachments'])
            ->select(
                "requisition.*",
                "subgroup_info.name as sub_group_name",
                "group_info.name as group_name",
            )
            ->leftJoin('subgroup_info', 'subgroup_info.id', '=', 'requisition.sub_group_info_id')
            ->leftJoin('group_info', 'group_info.id', '=', 'requisition.subgroup_id')
            ->where('requisition_number', $requisition_number)
            ->first();


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
        return view('requisition::requisition.printing.details', compact('requisition', 'tblData', 'actionAccess', 'isSupervisor'));
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

        $requisition = Requisition::with(['printings.requItem.stock', 'user', 'printingRequHistories.user', 'requAttachments'])
            ->select(
                "requisition.*",
                "subgroup_info.name as sub_group_name",
                "group_info.name as group_name",
            )
            ->leftJoin('subgroup_info', 'subgroup_info.id', '=', 'requisition.sub_group_info_id')
            ->leftJoin('group_info', 'group_info.id', '=', 'requisition.subgroup_id')
            ->where('requisition_number', $requisition_number)
            ->first();


        if (!empty($requisition)) {
            if ($unitList->unit_id == 25 || $requisition->status == RequisitionConstants::REQ_APPROVE) {
                $actionAccess = false;
            }
            $tblData = $requisition->toArray();
        }
        return view('requisition::requisition.printing.history_details', compact('requisition', 'tblData', 'actionAccess'));
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
            ->where('type', RequisitionConstants::PRINTINGITEM)
            ->get();

        $requisition = Requisition::with(['printings.requItem', 'requAttachments'])->where('requisition_number', $requisition_number)->first();

        return view('requisition::requisition.printing.edit', compact('requisitionSubCategories', 'items', 'requisition'));
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
                $requisition->subgroup_id = 206; // Painting Stationery
                $requisition->sub_group_info_id = 390; // Painting Stationery
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
            $existingProductIds = RequisitionPrinting::where('requisition_number', $requisition->requisition_number)
                ->pluck('product_id')
                ->toArray();

            $productIdsToDelete = array_diff($existingProductIds, $product_ids);

            if (!empty($productIdsToDelete)) {
                RequisitionPrinting::where('requisition_number', $requisition->requisition_number)
                    ->whereIn('product_id', $productIdsToDelete)
                    ->delete();
            }
            // update
            foreach ($product_ids as $index => $product_id) {
                RequisitionPrinting::updateOrCreate(
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

    public function workingOnPrintREQ(workingOnREQRequest $request, RequisitionEmailService $emailSender)
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
        $subgroup_id = Auth::user()->user_unit->subgroup_info_id;
        $group_info_id = SubgroupInfo::find($subgroup_id);

        $unitIdArray = explode(",", $unit->unit_id);

        if ($request->action == 'approve_forward') {

            $flash_message = "Requisition No: $requisition_no has been Approved & forwarded successfully.";

            if (in_array(53, $unitIdArray) || in_array(33, $unitIdArray)) {
                $requisitionModelObj->status = RequisitionConstants::REQ_PENDING;
                $requisitionModelObj->unit_id = 54;
                $requisitionModelObj->access_by = null;
                $requisitionModelObj->access_date = null;

                foreach ($request->approve_qty as $id => $qtyArray) {
                    $approveQty = $qtyArray[0] ?? null;
                    $productId = $request->product_id[$id] ?? null;

                    if (!$productId || !is_numeric($approveQty)) continue;

                    $stock = Stock::where('requisition_item_id', $productId)->first();
                    $stockQty = isset($stock->quantity) ? $stock->quantity : 0;

                    if ($approveQty <= $stockQty) {
                        $printing = RequisitionPrinting::find($id);
                        if ($printing) {
                            $printing->approve_qty = $approveQty;
                            $printing->save();
                        }

                        // Reduce stock by approved quantity
                        $stock->quantity -= $approveQty;
                        $stock->save();
                    } else {
                        flash("Requisition No: $requisition_no is stock not available!!!", 'danger');
                        return back();
                    }
                }


                $outgoingEmailMessage = $emailSender->outgoingSMSEmail('Printing Stationary', $requisition_no, "approve");
                // dd($outgoingEmailMessage,1);
                if (!empty($outgoingEmailMessage['mail'])) {
                    // dd($createUserEmail);
                    if (!empty($createUserEmail)) {
                        $emailSender->sendEMAIL($createUserEmail, $outgoingEmailMessage['mail'], $requisition_no, 0);
                    }
                }



                $this->audit([
                    'requisition_number' => $requisition_no,
                    'unit_id' => 53, // Unit -> REQ Admin Maker
                    'group_id' => $requisitionModelObj->subgroup_id,
                    'subgroup_id' => $requisitionModelObj->sub_group_info_id,
                    'user_id' => Auth::user()->user_id,
                    'action' => Session::get('subgroupStr') . " Approved & Forwarded",
                    'comments' => $request->comments,
                    'time' => strtotime(date('Y-m-d H:i:s')),
                    'isapproved' => '1'
                ]);
            }
        } elseif ($request->action == 'approve') {
            $flash_message = "Requisition No: $requisition_no has been Final Approved successfully.";
            if (in_array(54, $unitIdArray)) {
                $requisitionModelObj->status = RequisitionConstants::REQ_PENDING;
                // $requisitionModelObj->status = RequisitionConstants::REQ_APPROVE;
                $requisitionModelObj->delivery_date = Carbon::now();
                $requisitionModelObj->unit_id = 25;
                $requisitionModelObj->is_sendback = 10;
                $requisitionModelObj->access_by = null;
                $requisitionModelObj->access_date = null;
                $requisitionModelObj->subgroup_id = $requisitionModelObj->source_subgroup_id;
                $requisitionModelObj->sub_group_info_id = $requisitionModelObj->source_sub_group_info_id;

                // foreach ($request->approve_qty as $id => $qtyArray) {
                //     $approveQty = $qtyArray[0] ?? null;
                //     $productId = $request->product_id[$id] ?? null;

                //     if (!$productId || !is_numeric($approveQty)) continue;

                //     $stockQty = Stock::where('requisition_item_id', $productId)->value('quantity') ?? 0;
                //     if ($approveQty <= $stockQty) {
                //         $printing = RequisitionPrinting::find($id);
                //         if ($printing) {
                //             $printing->approve_qty = $approveQty;
                //             $printing->save();
                //         }
                //     } else {
                //         flash("Requisition No: $requisition_no is stock not available!!!", 'danger');
                //         return back();
                //     }
                // }

                $outgoingEmailMessage = $emailSender->outgoingSMSEmail('Printing Stationary', $requisition_no, "approve");
                if (!empty($outgoingEmailMessage['mail'])) {
                    if (!empty($createUserEmail)) {
                        $emailSender->sendEMAIL($createUserEmail, $outgoingEmailMessage['mail'], $requisition_no, 0);
                    }
                }

                $this->audit([
                    'requisition_number' => $requisition_no,
                    'unit_id' => 54,
                    'group_id' => $requisitionModelObj->subgroup_id,
                    'subgroup_id' => $requisitionModelObj->sub_group_info_id,
                    'user_id' => Auth::user()->user_id,
                    'action' => Session::get('subgroupStr') . " Final Approved",
                    'comments' => $request->comments,
                    'time' => strtotime(date('Y-m-d H:i:s')),
                    'isapproved' => '0'
                ]);
            }
        } elseif ($request->action == 'cancel') {
            $flash_message = "Requisition No: $requisition_no has been cancelled successfully.";
            $requisitionModelObj->status = RequisitionConstants::REQ_CANCEL;

            $outgoingEmailMessage = $emailSender->outgoingSMSEmail('Printing Stationary', $requisition_no, "cancel");
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

            $outgoingEmailMessage = $emailSender->outgoingSMSEmail('Printing Stationary', $requisition_no, "sendback");
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

            $outgoingEmailMessage = $emailSender->outgoingSMSEmail('Printing Stationary', $requisition_no, "close");
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

            $requisitionModelObj->status = RequisitionConstants::REQ_HOLD;
            $flash_message = "Requisition No: $requisition_no has been held successfully.";
            $requisitionModelObj->access_date = strtotime(date('Y-m-d H:i:s'));

            if (in_array(53, $unitIdArray) || in_array(33, $unitIdArray)) {
                foreach ($request->approve_qty as $id => $qtyArray) {
                    $approveQty = $qtyArray[0] ?? null;
                    $productId = $request->product_id[$id] ?? null;

                    if (!$productId || !is_numeric($approveQty)) continue;

                    $stock = Stock::where('requisition_item_id', $productId)->first();
                    $stockQty = isset($stock->quantity) ? $stock->quantity : 0;

                    if ($approveQty <= $stockQty) {
                        $printing = RequisitionPrinting::find($id);
                        if ($printing) {
                            $printing->approve_qty = $approveQty;
                            $printing->save();
                        }

                        // Reduce stock by approved quantity
                        $stock->quantity -= $approveQty;
                        $stock->save();
                    } else {
                        flash("Requisition No: $requisition_no is stock not available!!!", 'danger');
                        return back();
                    }
                }
            }


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
            flash($flash_message, 'success');
            return redirect($redirect_url);
        } else {
            flash('Failed to change status', 'danger');
            return Redirect::back();
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
            $redirectUrl = "requisition/printing-item/details/" . encrypt($requisition_number);
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
        $commentModel = new RequisitionPrintingHistory();
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


    public function quickAction(Request $request, RequisitionEmailService $emailSender)
    {
        $request->validate([
            'requisition_number' => 'required',
            'action' => 'required|in:approve,reject',
            'comments' => 'required|string|max:1000',
        ]);

        $requisitionNo = $request->requisition_number;
        $actionType = $request->action; // approve | reject
        $comments = $request->comments;
        $redirectUrl = "requisition/my-requisition-list";

        $requisitionModelObj = Requisition::where('requisition_number', $requisitionNo)->first();

        if (!$requisitionModelObj) {
            flash("Requisition No: $requisitionNo not found!", 'danger');
            return Redirect::back();
        }

        // Already someone else holding it
        if (!empty($requisitionModelObj->access_by) && $requisitionModelObj->access_by != Auth::user()->user_id) {
            flash("Requisition No: $requisitionNo already assigned to someone else!", 'danger');
            return Redirect::back();
        }

        // Already closed/approved/cancelled - guard
        if (in_array($requisitionModelObj->status, [
            RequisitionConstants::REQ_APPROVE,
            RequisitionConstants::REQ_CANCEL,
            RequisitionConstants::REQ_CLOSE,
        ])) {
            flash("Requisition No: $requisitionNo already processed!", 'danger');
            return Redirect::back();
        }

        $createUserEmail = User::where('id', $requisitionModelObj->user_id)->pluck('email')->first();

        $user_id = Auth::user()->user_id;
        $subgroup_id = Auth::user()->user_unit->subgroup_info_id;
        $group_info_id = SubgroupInfo::find($subgroup_id);

        $unit = UserUnit::where('user_id', Auth::id())->first();
        $unitIdArray = explode(",", $unit->unit_id ?? '');

        DB::beginTransaction();
        try {
            // ---------- STEP 1: ASSIGN ----------
            if (empty($requisitionModelObj->access_by)) {
                $requisitionModelObj->status = RequisitionConstants::REQ_WIP;
                $requisitionModelObj->access_by = $user_id;
                $requisitionModelObj->access_date = strtotime(date('Y-m-d H:i:s'));
                $requisitionModelObj->save();

                $this->audit([
                    'requisition_number' => $requisitionNo,
                    'unit_id' => $requisitionModelObj->unit_id,
                    'group_id' => $group_info_id->group_info_id ?? 0,
                    'subgroup_id' => $subgroup_id ?? 0,
                    'region_id' => $requisitionModelObj->region_id ?? 0,
                    'user_id' => $user_id,
                    'action' => 'Assigned',
                    'comments' => '',
                    'time' => strtotime(date('Y-m-d H:i:s')),
                    'isapproved' => ''
                ]);
            }

            // ---------- STEP 2: APPROVE / REJECT (SENDBACK) ----------
            if ($actionType === 'approve') {

                if (in_array(54, $unitIdArray) || in_array(33, $unitIdArray)) {
                    $requisitionModelObj->status = RequisitionConstants::REQ_PENDING;
                    $requisitionModelObj->delivery_date = Carbon::now();
                    $requisitionModelObj->unit_id = 25;
                    $requisitionModelObj->is_sendback = 10;
                    $requisitionModelObj->access_by = null;
                    $requisitionModelObj->access_date = null;
                    $requisitionModelObj->subgroup_id = $requisitionModelObj->source_subgroup_id;
                    $requisitionModelObj->sub_group_info_id = $requisitionModelObj->source_sub_group_info_id;

                    $outgoingEmailMessage = $emailSender->outgoingSMSEmail('Printing Stationary', $requisitionNo, "approve");
                    if (!empty($outgoingEmailMessage['mail'])) {
                        if (!empty($createUserEmail)) {
                            $emailSender->sendEMAIL($createUserEmail, $outgoingEmailMessage['mail'], $requisitionNo, 0);
                        }
                    }

                    $this->audit([
                        'requisition_number' => $requisitionNo,
                        'unit_id' => 54,
                        'group_id' => $requisitionModelObj->subgroup_id,
                        'subgroup_id' => $requisitionModelObj->sub_group_info_id,
                        'user_id' => Auth::user()->user_id,
                        'action' => Session::get('subgroupStr') . " Final Approved",
                        'comments' => $request->comments,
                        'time' => strtotime(date('Y-m-d H:i:s')),
                        'isapproved' => '0'
                    ]);

                    $flashMessage = "Requisition No: $requisitionNo has been Approved & forwarded successfully.";
                } else {
                    DB::rollBack();
                    flash("Your unit is not authorized to approve this requisition!", 'danger');
                    return Redirect::back();
                }
            } elseif ($actionType === 'reject') {
                // Send Back to source
                $requisitionModelObj->status = RequisitionConstants::REQ_PENDING;
                $requisitionModelObj->unit_id = 25; // REQ Admin Maker
                $requisitionModelObj->access_by = null;
                $requisitionModelObj->access_date = null;
                $requisitionModelObj->subgroup_id = $requisitionModelObj->source_subgroup_id;
                $requisitionModelObj->sub_group_info_id = $requisitionModelObj->source_sub_group_info_id;
                $requisitionModelObj->is_sendback = 1;

                $outgoingEmailMessage = $emailSender->outgoingSMSEmail('Printing Stationary', $requisitionNo, "sendback");
                if (!empty($outgoingEmailMessage['mail']) && !empty($createUserEmail)) {
                    $emailSender->sendEMAIL($createUserEmail, $outgoingEmailMessage['mail'], $requisitionNo, 0);
                }

                $this->audit([
                    'requisition_number' => $requisitionNo,
                    'unit_id' => $requisitionModelObj->unit_id,
                    'group_id' => $requisitionModelObj->subgroup_id,
                    'subgroup_id' => $requisitionModelObj->sub_group_info_id,
                    'user_id' => $user_id,
                    'action' => Session::get('subgroupStr') . " send back to source",
                    'comments' => $comments,
                    'time' => strtotime(date('Y-m-d H:i:s')),
                    'isapproved' => '1'
                ]);

                $flashMessage = "Requisition No: $requisitionNo has been sent back successfully.";
            }

            if (!$requisitionModelObj->save()) {
                DB::rollBack();
                flash("Failed to change status", 'danger');
                return Redirect::back();
            }

            DB::commit();
            flash($flashMessage, 'success');
            return Redirect::back();
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('quickAction error: ' . $e->getMessage());
            flash("Something went wrong. Please try again.", 'danger');
            return Redirect::back();
        }
    }
}
