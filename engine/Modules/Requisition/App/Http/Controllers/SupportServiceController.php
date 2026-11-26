<?php

namespace Modules\Requisition\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;
use Modules\Requisition\App\Models\RequisitionItem;
use Modules\Requisition\App\Models\RequisitionSubCategory;
use Modules\Requisition\App\Constants\RequisitionConstants;

class SupportServiceController extends Controller
{
    public function index()
    {
        $supportServices = RequisitionItem::with('subCategory')
            ->select('id', 'sub_category_id', 'product_name', 'image', 'status')
            ->whereIn('type', [RequisitionConstants::REPAIRITEM, RequisitionConstants::NEWASSETITEM])
            ->get();

        return view('requisition::support_service.index', compact('supportServices'));
    }

    public function create()
    {
        $supportServiceCategories = RequisitionSubCategory::with('category')
            ->select('id', 'category_id', 'sub_category_name')
            ->whereHas('category', function ($query) {
                $query->where('category_name', 'General Support Service');
            })
            ->get();

        return view('requisition::support_service.create', compact('supportServiceCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'sub_category_id' => 'required|exists:requisition_sub_categories,id',
            'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        try {
            $imageFile = $request->file('image');
            $docDestPath = 'public/items';
            $fileName = null;
            if (!empty($imageFile) && $imageFile->isValid()) {
                $extension = $imageFile->getClientOriginalExtension();
                $origin_name = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $origin_name = str_replace(' ', '_', $origin_name);
                $origin_name = substr($origin_name, 0, 20);
                $fileName = $origin_name . "_attach_nX_" . round(microtime(true) * 10) . '_1.' . $extension;

                // For Local
                $imageFile->move($docDestPath, $fileName);

                // For Live (optional)
                // $fileContent = File::get($imageFile->getRealPath());
                // Storage::disk('custom_storage')->put($fileName, $fileContent);
            }

            $subCategoryType = RequisitionSubCategory::where('id', $request->sub_category_id)->value('sub_category_type');

            $supportService = new RequisitionItem();
            $supportService->product_name = $request->product_name;
            $supportService->sub_category_id = $request->sub_category_id;
            $supportService->image = $fileName;
            $supportService->type = $subCategoryType == 1 ? RequisitionConstants::REPAIRITEM : RequisitionConstants::NEWASSETITEM;
            $supportService->save();

            flash('Requisition Item Created Successfully', 'success');
            return redirect()->route('requisition.support-service.index');
        } catch (\Throwable $th) {
            flash('Requisition Item Created Failed.' . $th->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        $supportService = RequisitionItem::findOrFail($id);
        $supportServiceCategories = RequisitionSubCategory::with('category')
            ->select('id', 'category_id', 'sub_category_name')
            ->whereHas('category', function ($query) {
                $query->where('category_name', 'General Support Service');
            })
            ->get();

        return view('requisition::support_service.edit', compact('supportService', 'supportServiceCategories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'sub_category_id' => 'required|exists:requisition_sub_categories,id',
            'image' => 'nullable|mimes:jpg,jpeg,png,gif|max:3072',
        ]);

        try {
            $supportService = RequisitionItem::findOrFail($id);

            $imageFile = $request->file('image');
            $docDestPath = 'public/items';
            $fileName = $supportService->image;

            if (!empty($imageFile) && $imageFile->isValid()) {
                // Remove old image
                if (!empty($fileName)) {
                    $oldImagePath = $docDestPath . '/' . $fileName;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Upload new image
                $extension = $imageFile->getClientOriginalExtension();
                $origin_name = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $origin_name = str_replace(' ', '_', $origin_name);
                $origin_name = substr($origin_name, 0, 20);
                $fileName = $origin_name . "_attach_nX_" . round(microtime(true) * 10) . '_1.' . $extension;

                $imageFile->move($docDestPath, $fileName);

                // For Live (optional)
                // $fileContent = File::get($imageFile->getRealPath());
                // Storage::disk('custom_storage')->put($fileName, $fileContent);
            }

            $subCategoryType = RequisitionSubCategory::where('id', $request->sub_category_id)->value('sub_category_type');
            
            $supportService->product_name = $request->product_name;
            $supportService->sub_category_id = $request->sub_category_id;
            $supportService->image = $fileName;
            $supportService->type = $subCategoryType == 1 ? RequisitionConstants::REPAIRITEM : RequisitionConstants::NEWASSETITEM;
            $supportService->save();

            flash('Requisition Item Updated Successfully', 'success');
            return redirect()->route('requisition.support-service.index');
        } catch (\Throwable $th) {
            flash('Requisition Item Update Failed', 'error');
            return redirect()->back();
        }
    }

    public function status($id = null, $status = "")
    {
        if ($status != 1 && $status != 0) {
            flash($status . ' is not Allowed!!!', 'danger');
            return Redirect::back();
        }

        $requisitionItem = RequisitionItem::findOrFail($id);

        if ($status === "0") {
            $requisitionItem->status = 0;
            $message = "Requisition item has been inactive.";
            $alert = "danger";
        } else {
            $requisitionItem->status = 1;
            $message = "Requisition item been activated.";
            $alert = "success";
        }

        if ($requisitionItem->save()) {
            flash($message, $alert);
            return Redirect::back();
        } else {
            flash("Requisition status not updated.", "danger");
            return Redirect::back();
        }
    }
}
