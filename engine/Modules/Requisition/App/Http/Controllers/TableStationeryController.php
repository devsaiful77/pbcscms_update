<?php

namespace Modules\Requisition\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;
use Modules\Requisition\App\Models\ProductUnit;
use Modules\Requisition\App\Models\RequisitionItem;
use Modules\Requisition\App\Models\RequisitionCategory;
use Modules\Requisition\App\Constants\RequisitionConstants;

class TableStationeryController extends Controller
{
    public function index()
    {
        $tableStationaries = RequisitionItem::with(['category', 'unit'])
            ->select('id', 'category_id', 'sub_category_id', 'product_name', 'unit_price', 'unit_id', 'image', 'status')
            ->where('type', RequisitionConstants::NONPRINTINGITEM)
            ->get();

        return view('requisition::table_stationery.index', compact('tableStationaries'));
    }

    public function create()
    {
        $unitTypes = ProductUnit::select('id', 'name', 'status')->where('status', 1)->get();
        return view('requisition::table_stationery.create', compact('unitTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:250',
            'unit_id' => 'required|exists:product_units,id',
            'unit_price' => 'nullable|numeric',
            'image' => 'nullable|mimes:jpg,jpeg,png,gif|max:3072',
        ]);

        $category = RequisitionCategory::select('id', 'category_name')
            ->where('category_name', 'like', '%Table Stationeries%')
            ->first();

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

            RequisitionItem::create([
                'category_id' => $category->id,
                'product_name' => $request->product_name,
                'unit_id' => $request->unit_id,
                'unit_price' => $request->unit_price,
                'image' => $fileName,
                'type' => RequisitionConstants::NONPRINTINGITEM,
            ]);

            flash('Table Stationery created successfully', 'success');

            return redirect()->route('requisition.table-stationery-item.index');
        } catch (\Throwable $th) {
            flash('Error creating table Stationery: ' . $th->getMessage(), 'danger');
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $item = RequisitionItem::findOrFail($id);
        $unitTypes = ProductUnit::select('id', 'name', 'status')->where('status', 1)->get();
        return view('requisition::table_stationery.edit', compact('unitTypes', 'item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'product_name' => 'required|string|max:250',
            'unit_id' => 'required|exists:product_units,id',
            'unit_price' => 'nullable|numeric',
            'image' => 'nullable|mimes:jpg,jpeg,png,gif|max:3072',
        ]);

        $item = RequisitionItem::findOrFail($id);

        $category = RequisitionCategory::select('id', 'category_name')
            ->where('category_name', 'like', '%Table Stationeries%')
            ->first();

        try {
            $imageFile = $request->file('image');
            $docDestPath = 'public/items';
            $fileName = $item->image;

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

            $item->update([
                'category_id' => $category->id,
                'product_name' => $request->product_name,
                'unit_price' => $request->unit_price,
                'unit_id' => $request->unit_id,
                'image' => $fileName,
            ]);

            flash('Table Stationery updated successfully', 'success');

            return redirect()->route('requisition.table-stationery-item.index');
        } catch (\Throwable $th) {
            flash('Error updating Table Stationery: ' . $th->getMessage(), 'danger');
            return redirect()->back()->withInput();
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
