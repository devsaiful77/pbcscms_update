<?php

namespace Modules\Requisition\App\Http\Controllers;

use App\SubgroupInfo;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Modules\Requisition\App\Models\Region;
use Modules\Requisition\App\Models\RegionSubgroup;
use Modules\Requisition\App\Http\Requests\RegionRequest;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::select('id', 'name', 'status')->get();
        return view('requisition::region.index', compact('regions'));
    }

    public function create()
    {
        $subgroups = SubgroupInfo::select('id', 'name')->where('is_active', 1)->get()->toArray();
        $assignedSubgroups = RegionSubgroup::select('subgroup_info_id')->get()->toArray();
        $assignedSubgroups = array_column($assignedSubgroups, 'subgroup_info_id');

        $subgroups = array_filter($subgroups, function ($subgroup) use ($assignedSubgroups) {
            return !in_array($subgroup['id'], $assignedSubgroups);
        });    

        return view('requisition::region.create', compact('subgroups'));
    }

    public function store(RegionRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $region = new Region();
            $region->name = $data['name'];
            $region->save();

            foreach ($data['subgroup_info_id'] as $value) {
                $regionSubGroup = new RegionSubgroup();
                $regionSubGroup->subgroup_info_id = $value;
                $regionSubGroup->region_id = $region->id;
                $regionSubGroup->save();
            }

            DB::commit();

            flash('Region created successfully', 'success');
            return redirect()->route('requisition.region.index');
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error('Error creating region: ' . $th->getMessage());
            flash('Error creating region: ', 'danger');
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $region = Region::findOrFail($id);

        // All subgroups
        $allSubgroups = SubgroupInfo::select('id', 'name')->where('is_active', 1)->get()->toArray();

        // Subgroups assigned to other regions
        $assignedSubgroups = RegionSubgroup::where('region_id', '!=', $region->id)
            ->pluck('subgroup_info_id')
            ->toArray();

        // Subgroups assigned to current region
        $selectedSubgroups = $region->subgroupInfo->pluck('id')->toArray();

        // Filter out subgroups assigned to other regions
        $availableSubgroups = array_filter($allSubgroups, function ($subgroup) use ($assignedSubgroups, $selectedSubgroups) {
            return !in_array($subgroup['id'], $assignedSubgroups) || in_array($subgroup['id'], $selectedSubgroups);
        });

        return view('requisition::region.edit', compact('region', 'availableSubgroups', 'selectedSubgroups'));
    }

    public function update(RegionRequest $request, $id)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $region = Region::findOrFail($id);
            $region->name = $data['name'];
            $region->save();

            // Clear previous region_id for the selected subgroups
            RegionSubgroup::where('region_id', $region->id)->delete();

            // Reassign selected subgroups
            foreach ($data['subgroup_info_id'] as $value) {
                RegionSubgroup::create([
                    'region_id' => $region->id,
                    'subgroup_info_id' => $value,
                ]);
            }

            DB::commit();

            flash('Region updated successfully', 'success');
            return redirect()->route('requisition.region.index');
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error('Error updating region: ' . $th->getMessage());
            flash('Error updating region'.$th->getMessage(), 'danger');
            return redirect()->back()->withInput();
        }
    }

    public function status($id = null, $status = ""){
        if ($status != 1 && $status != 0) {
            flash($status . ' is not Allowed!!!', 'danger');
            return Redirect::back();
        }

        $regionStatus = Region::findOrFail($id);

        if($status === "0"){
            $regionStatus->status = 0;
            $message = "Region has been inactive.";
            $alert = "danger";
        }else{
            $regionStatus->status = 1;
            $message = "Region been activated.";
            $alert = "success";
        }

        if($regionStatus->save()){
            flash($message, $alert);
            return Redirect::back();
        }else{
            flash("Region status not updated.", "danger");
            return Redirect::back();
        }
    }

    public function view($id)
    {
        $region = Region::with(['subgroupInfo.groupInfo:id,name', 'subgroupInfo.dept:id,name'])->findOrFail($id);
        return view('requisition::region.view', compact('region'));
    }

}