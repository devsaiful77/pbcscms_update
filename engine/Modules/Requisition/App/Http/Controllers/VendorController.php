<?php

namespace Modules\Requisition\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Modules\Requisition\App\Http\Requests\VendorRequest;
use Modules\Requisition\App\Models\RequisitionItem;
use Modules\Requisition\App\Models\Vendor;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tblData = array();
        $dataObj = Vendor::select("id", "name", "office_address", "email", "phone",
        DB::raw("CASE WHEN status = 1 THEN 'Active' WHEN status = 0 THEN 'Inactive' ELSE 'Invalid' END AS status_name"), "status")
            ->orderBy("id", "DESC")
            ->get();

        if (!empty($dataObj)) {
            $tblData = $dataObj->toArray();
        }

        $title = "Vendor List";

        return view('requisition::vendors.index', compact('title', 'tblData', 'dataObj'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('requisition::vendors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VendorRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        try {
            Vendor::create($validatedData);
            flash('Vendor created successfully.', 'success');
            return redirect()->route('requisition.vendor.index');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', 'Failed to create vendor. Please try again.');
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('requisition::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('requisition::vendors.edit', compact('vendor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VendorRequest $request, $id): RedirectResponse
    {
        $validatedData = $request->validated();
        $vendor = Vendor::findOrFail($id);

        try {
            $vendor->update($validatedData);
            flash("Vendor data updated successfully.", "success");
            return redirect()->route('requisition.vendor.index');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', 'Failed to update vendor. Please try again.');
        }
    }

    public function status($id = null, $status = ""){
        if ($status != 1 && $status != 0) {
            flash($status . ' is not Allowed!!!', 'danger');
            return Redirect::back();
        }

        $vendor = Vendor::findOrFail($id);

        if($status === "0"){
            $vendor->status = 0;
            $message = "Vendor has been inactive.";
            $alert = "danger";
        }else{
            $vendor->status = 1;
            $message = "Vendor has been activated.";
            $alert = "success";
        }

        if($vendor->save()){
            flash($message, $alert);
            return Redirect::back();
        }else{
            flash("Vendor status not updated.", "danger");
            return Redirect::back();
        }
    }


    public function productsView($id)
    {
        $vendor = Vendor::with('products')->findOrFail($id);
        $vendorProducts = $vendor->products;
        $products = RequisitionItem::all('id', 'product_name');
    
        return view('requisition::vendors.products', compact('vendor', 'products', 'vendorProducts'));
    }

    public function productStore(Request $request){
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'product_id' => 'required|exists:requisition_items,id',
        ]);

        // Find Vendor
        $vendor = Vendor::findOrFail($request->vendor_id);

        $vendor->products()->syncWithoutDetaching([
            $request->product_id
        ]);

        flash("Product Added Successfully.", 'success');
        // Return Response
        return redirect()->back();
    }

    public function deleteVendorProduct($vendorId, $productId){
        $vendor = Vendor::findOrFail($vendorId);
        // Delete from pivot table
        $vendor->products()->detach($productId);

        flash('Product removed successfully.', 'success');

        return redirect()->back();
    }

    public function vendorProductValidation($vendorId, $productId){

        $exists = DB::table('requisition_product_vendor')
                ->where('vendor_id', $vendorId)
                ->where('product_id', $productId)
                ->exists();
        
        return response()->json([
            'success' => $exists,
            'message' => $exists 
                ? 'Product belongs to this vendor.' 
                : 'Product does not belong to this vendor.',
        ]);
    }

    
    public function destroy($id)
    {
        //
    }


}
