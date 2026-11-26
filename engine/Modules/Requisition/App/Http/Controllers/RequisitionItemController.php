<?php

namespace Modules\Requisition\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Modules\Requisition\App\Models\RequisitionItem;
use Modules\Requisition\App\Models\RequisitionCategory;
use Modules\Requisition\App\Models\RequisitionSubCategory;
use Modules\Requisition\App\Http\Requests\RequisitionItemRequest;

class RequisitionItemController extends Controller
{
    public function requisitionCategory()
    {
        $requisitionCategories = RequisitionCategory::select('id', 'category_name')->get();
        return view('requisition::requisition_items.requisition_category', compact('requisitionCategories'));
    }

    public function requisitionSubCategory()
    {
        $requisitionSubCategories = RequisitionSubCategory::with('category')
            ->select('id', 'category_id', 'sub_category_name')
            ->get();

        return view('requisition::requisition_items.requisition_sub_category', compact('requisitionSubCategories'));
    }

    public function index()
    {
        $requisitionItems = RequisitionItem::with(['category', 'subCategory'])
            ->select('id', 'category_id', 'sub_category_id', 'product_name', 'unit_type', 'product_code', 'status')
            ->get();

        return view('requisition::requisition_items.requisition_items', compact('requisitionItems'));
    }

    public function getSubCategory(Request $request)
    {
        $requisitionSubCategories = RequisitionSubCategory::where('category_id', $request->category_id)
            ->select('id', 'category_id', 'sub_category_name')
            ->get();
        if ($requisitionSubCategories->isNotEmpty()) {
            return response()->json([
                'status' => true,
                'data' => $requisitionSubCategories
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No sub categories found'
            ]);
        }
    }

    public function create(Request $request)
    {
        $requisitionCategories = RequisitionCategory::select('id', 'category_name')->get();

        $requisitionSubCategories = [];
        if (old('category_id')) {
            $requisitionSubCategories = RequisitionSubCategory::where('category_id', old('category_id'))
                ->select('id', 'category_id', 'sub_category_name')
                ->get();
        }

        return view('requisition::requisition_items.create_items', compact('requisitionCategories', 'requisitionSubCategories'));
    }

    public function store(RequisitionItemRequest $request)
    {
        dd($request->image);
        try {
            $validated = $request->validated();

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');

                $extension = $file->getClientOriginalExtension();
                $origin_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $origin_name = str_replace(' ', '_', $origin_name);
                $origin_name = substr($origin_name, 0, 20);
                $fileName = $origin_name . "_attach_nX_" . round(microtime(true) * 10) . '.' . $extension;

                // For Local
                $docDestPath = 'public/attachments';
                if (!file_exists($docDestPath)) {
                    mkdir($docDestPath, 0755, true);
                }

                $file->move($docDestPath, $fileName);
                $validated['image'] = 'public/attachments/' . $fileName;
                // For Live
                // $fileContent = File::get($file->getRealPath());
                // Storage::disk('custom_storage')->put($fileName, $fileContent);
                // $validated['image'] = $fileName;
            }

            $data = RequisitionItem::create($validated);
            flash('Requisition Item Created Successfully', 'success');

            if ($data['type'] == 'supportService') {
                return redirect()->route('requisition.support-service.index');
            } elseif ($data['type'] == 'printingStationary') {
                return redirect()->route('requisition.printing-stationary.index');
            } elseif ($data['type'] == 'tableStationary') {
                return redirect()->route('requisition.table-stationary.index');
            }
        } catch (\Throwable $th) {
            flash('Requisition Item Created Failed', 'error');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        $item = RequisitionItem::findOrFail($id);
        $requisitionCategories = RequisitionCategory::select('id', 'category_name')->get();
        $requisitionSubCategories = RequisitionSubCategory::where('category_id', $item->category_id)
            ->select('id', 'category_id', 'sub_category_name')
            ->get();

        return view('requisition::requisition_items.edit_items', compact('item', 'requisitionCategories', 'requisitionSubCategories'));
    }

    public function update(RequisitionItemRequest $request, $id)
    {
        try {
            $requisitionItem = RequisitionItem::findOrFail($id);
            $requisitionItem->update($request->validated());

            flash('Requisition Item Updated Successfully', 'success');
            return redirect()->route('requisition.items');
        } catch (\Throwable $th) {
            flash('Requisition Item Not Updated.', 'error');
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
