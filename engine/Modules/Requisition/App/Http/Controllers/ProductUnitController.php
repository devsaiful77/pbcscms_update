<?php

namespace Modules\Requisition\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Modules\Requisition\App\Http\Requests\ProductUnitRequest;
use Modules\Requisition\App\Models\ProductUnit;

class ProductUnitController extends Controller
{
    public function index()
    {
        $tblData = array();
        $dataObj = ProductUnit::select(
            "id",
            "name",
            DB::raw("CASE WHEN status = 1 THEN 'Active' WHEN status = 0 THEN 'Inactive' ELSE 'Invalid' END AS status_name"),
            "status"
        )
            ->orderBy("id", "DESC")
            ->get();

        if (!empty($dataObj)) {
            $tblData = $dataObj->toArray();
        }
        return view('requisition::unit/index', compact('tblData', 'dataObj'));
    }

    public function create()
    {
        return view('requisition::unit/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductUnitRequest $request)
    {
        $validatedData = $request->validated();
        try {
            ProductUnit::create($validatedData);
            flash('Unit created successfully.', 'success');
            return redirect()->route('requisition.unit.index');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', 'Failed to create Unit. Please try again.');
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
        $unit = ProductUnit::findOrFail($id);
        return view('requisition::unit/edit', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUnitRequest $request, $id)
    {
        $validatedData = $request->validated();
        $unit = ProductUnit::findOrFail($id);

        try {
            $unit->update($validatedData);
            flash("Unit data updated successfully.", "success");
            return redirect()->route('requisition.unit.index');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', 'Failed to update Unit. Please try again.');
        }
    }

    public function status($id = null, $status = "")
    {
        if ($status != 1 && $status != 0) {
            flash($status . ' is not Allowed!!!', 'danger');
            return Redirect::back();
        }

        $unit = ProductUnit::findOrFail($id);

        if ($status === "0") {
            $unit->status = 0;
            $message = "Unit has been inactive.";
            $alert = "danger";
        } else {
            $unit->status = 1;
            $message = "Unit has been activated.";
            $alert = "success";
        }

        if ($unit->save()) {
            flash($message, $alert);
            return Redirect::back();
        } else {
            flash("Unit status not updated.", "danger");
            return Redirect::back();
        }
    }
}
