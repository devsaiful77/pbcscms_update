<?php

namespace Modules\Requisition\App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Modules\Requisition\App\Models\Vendor;
use Modules\Requisition\App\Models\Requisition;
use Modules\Requisition\App\Models\RequisitionAttachment;
use Modules\Requisition\App\Constants\RequisitionConstants;
use Modules\Requisition\App\Models\RequisitionTableHistory;
use Modules\Requisition\App\Models\OutgoingVendorCommunication;
use Modules\Requisition\App\Service\VendorCommunicationService;
use Modules\Requisition\App\Models\RequisitionSupportServiceHistory;


class SendVendorEmailController extends Controller
{
    protected $vendorCommunicationService;

    public function __construct(VendorCommunicationService $vendorCommunicationService)
    {
        $this->vendorCommunicationService = $vendorCommunicationService;
    }

    public function sendVendorEmail(Request $request)
    {
        if ($request->user == "VendormailUsr" && $request->passwd == "PwdVendor@!123" && !empty($request->id)) {
            $requisitionRelation = "";
            $requisitionJoinString = "";
            $data = [];

            // Get the first pending vendor mail
            $vendorMail = OutgoingVendorCommunication::where('id', $request->id)->first();
            if (!$vendorMail) {
                return response()->json(['status' => false, 'message' => 'Vendor mail record not found.'], 404);
            }

            $requType = Requisition::select('type')->where('requisition_number', $vendorMail->requisition_number)->first();
            if (!$requType) {
                return response()->json(['status' => false, 'message' => "Requisition type not found for number: {$vendorMail->requisition_number}"], 404);
            }

            if ($requType->type == RequisitionConstants::REPAIRITEM || $requType->type == RequisitionConstants::NEWASSETITEM) {
                $requisitionRelation = "generalServices.requItem";
                $requisitionJoinString = "requisition_general_service";
            } elseif ($requType->type == RequisitionConstants::NONPRINTINGITEM) {
                $requisitionRelation = "tableStationeries.requItem";
                $requisitionJoinString = "requisition_non_printing";
            } else {
                return response()->json(['status' => false, 'message' => 'Unknown requisition type.'], 400);
            }

            $requisition = Requisition::with($requisitionRelation)
                ->where('requisition_number', $vendorMail->requisition_number)
                ->first();

            if (!$requisition) {
                return response()->json(['status' => false, 'message' => "Main Requisition row missing for number: {$vendorMail->requisition_number}"], 404);
            }

            $data['requisition_number'] = $requisition->requisition_number;

            // Get bank/branch info from user
            if ($requisition->user_id) {
                $user = User::with('user_subgroupinfo')->find($requisition->user_id);
                $data['bank_info'] = [
                    'contact_person' => $user->name ?? 'N/A',
                    'contact_person_phone' => $requisition->i_phone ?? 'N/A',
                    'contact_person_email' => $user->email ?? 'N/A',
                    'branch' => optional($user->user_subgroupinfo)->subgroup_name ?? 'N/A',
                    'branch_address' => $requisition->i_address ?? 'N/A',
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
                ->join($requisitionJoinString, function ($join) use ($requisition, $requisitionJoinString) {
                    $join->on('ri.id', '=', "{$requisitionJoinString}.product_id")
                        ->where("{$requisitionJoinString}.requisition_number", '=', $requisition->requisition_number);
                })
                ->leftJoin('product_units as pu', 'ri.unit_id', '=', 'pu.id')
                ->whereIn('ri.id', $vendor_data['products_id'])
                ->select('ri.*', "{$requisitionJoinString}.approve_qty", 'pu.name as unit_name')
                ->get();

            $final_vendors_data = [
                'vendor' => $vendorArray,
                'products' => json_decode(json_encode($products), true),
            ];

            $data['vendor_info'] = $final_vendors_data;
            // Requisition Attachments
            // $attachments = RequisitionAttachment::where('requisition_number', $requisition->requisition_number)
            //     ->where('is_admin_attach', 1)
            //     ->pluck('attachment')
            //     ->map(function ($path) {
            //         return $_SERVER['DOCUMENT_ROOT'] . '/public/attachments/' . $path;
            //     })
            //     ->toArray();

            $vendorProductIds = $vendor_data['products_id'];

            // Get attachments for ALL products assigned to this vendor
            $attachments = RequisitionAttachment::where('requisition_number', $requisition->requisition_number)
                ->where('is_admin_attach', 1)
                ->where(function ($query) use ($vendorProductIds) {
                    $query->whereNull('product_id')
                        ->orWhereIn('product_id', $vendorProductIds);
                })
                ->pluck('attachment')
                ->map(function ($path) {
                    return $_SERVER['DOCUMENT_ROOT'] . '/public/attachments/' . $path;
                })
                ->toArray();

            $data['attachments'] = $attachments;
            $data['outgoing_row_id'] = $request->id;

            $requ_comment = null;

            // get requisition remark and comments
            if ($requType->type == RequisitionConstants::NONPRINTINGITEM) {
                $requAdmin = RequisitionTableHistory::select('id', 'requisition_number', 'unit_id', 'user_id')
                    ->where('requisition_number', $requisition->requisition_number)
                    ->where('unit_id', 26)
                    ->first();

                if (!empty($requAdmin)) {
                    $admin_user_name = User::select('name')->where('user_id', $requAdmin->user_id)->first();
                    $data['admin_user_name'] = $admin_user_name->name ?? "";
                }

                $requ_comment = RequisitionTableHistory::select('comment')
                    ->where('requisition_number', $requisition->requisition_number)
                    ->where('unit_id', 26)
                    ->where('isapproved', 1)
                    ->first();
            } elseif ($requType->type == RequisitionConstants::NEWASSETITEM || $requType->type == RequisitionConstants::REPAIRITEM) {
                $requAdmin = RequisitionSupportServiceHistory::select('id', 'requisition_number', 'unit_id', 'user_id')
                    ->where('requisition_number', $requisition->requisition_number)
                    ->where('unit_id', 26)
                    ->first();

                if (!empty($requAdmin)) {
                    $admin_user_name = User::select('name')->where('user_id', $requAdmin->user_id)->first();
                    $data['admin_user_name'] = $admin_user_name->name ?? "";
                }

                $requ_comment = RequisitionSupportServiceHistory::select('comment')
                    ->where('requisition_number', $requisition->requisition_number)
                    ->where('unit_id', 26)
                    ->where('action', 'LIKE', '%Final Approved%')
                    ->first();
            }

            $data['admin_user_comment'] = $requ_comment->comment ?? "";
            $data['branch_user_comment'] = $requisition->remark ?? "";

            // Send mail to vendors
            $is_send = $this->sendRequisitionEmail($data);

            if ($is_send) {
                return response()->json(['status' => true, 'message' => 'Email sent successfully.']);
            } else {
                return response()->json(['status' => false, 'message' => 'Failed to send email.']);
            }
        }
        return response()->json(['status' => false, 'message' => 'Unauthorized or invalid parameters.'], 401);
    }

    private function sendRequisitionEmail($data)
    {
        $emailData = $data;
        $emailData['current_vendor'] = $data['vendor_info']; // contains 'vendor' and 'products'

        // $primeLogo = $_SERVER['DOCUMENT_ROOT'] . "/public/login_asset_v3/images/Logo-prime.png";

        // $imageContents = file_get_contents($primeLogo);
        // $logo = 'data:image/png;base64,' . base64_encode($imageContents);
        // $emailData['logo'] = $logo;

        $html = View::make('requisition::requisition.mail', $emailData)->render();

        $to = $data['vendor_info']['vendor']['email'] ?? '';
        $subject = 'Requisition Request - ' . ($data['bank_info']['branch'] ?? 'N/A') . ' - ' . ($data['vendor_info']['vendor']['name'] ?? 'Vendor');
        $body = $html;

        $result = $this->vendorCommunicationService->send(
            $to,
            $subject,
            $body,
            $data['attachments'] ?? []
        );

        $outgoingVendorMail = OutgoingVendorCommunication::where('id', $data['outgoing_row_id'])->first();

        if (!$outgoingVendorMail) {
            Log::error('OutgoingVendorCommunication not found for ID: ' . $data['outgoing_row_id']);
            return false;
        }

        if ($outgoingVendorMail->status == -1) {
            $outgoingVendorMail->repeat_time += 1;
        }

        if ($result) {
            $outgoingVendorMail->status = 7;
            $outgoingVendorMail->sent_time = strtotime(date('Y-m-d H:i:s'));
        } else {
            $outgoingVendorMail->status = -1;
            Log::error('Failed to send email to vendor: ' . $to);
        }
        $outgoingVendorMail->save();

        return $result;
    }
}
