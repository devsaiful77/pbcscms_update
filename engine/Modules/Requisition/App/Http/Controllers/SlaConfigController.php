<?php

namespace Modules\Requisition\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Requisition\App\Constants\RequisitionConstants;
use Modules\Requisition\App\Http\Requests\SlaRequest;
use Modules\Requisition\App\Models\SlaConfig;

class SlaConfigController extends Controller
{
    public function gss_index()
    {
        $req_roles = DB::table('roles')->where('module', 2)->get()->map(function ($item) {
            return (array)$item;
        })->toArray();

        $sla_config = SlaConfig::where('requisition_type', RequisitionConstants::SUPPORTSERVICEITEM)
            ->whereIn('role_name', ['requisitionChecker', 'requisitionAdminMaker', 'requisitionAdminChecker', 'requUserChecker', 'requisitionSupervisor'])
            ->get()->toArray();

        return view('requisition::SlaConfig.index', compact('req_roles', 'sla_config'));
    }

    public function pi_index()
    {
        $req_roles = DB::table('roles')->where('module', 2)->get()->map(function ($item) {
            return (array)$item;
        })->toArray();

        $sla_config = SlaConfig::where('requisition_type', RequisitionConstants::PRINTINGITEM)
            ->whereIn('role_name', ['requisitionChecker', 'requisitionAdminMaker', 'requisitionAdminChecker', 'requUserChecker', 'requisitionSupervisor'])
            ->get()->toArray();

        return view('requisition::SlaConfig.index', compact('req_roles', 'sla_config'));
    }

    public function npi_index()
    {
        $req_roles = DB::table('roles')->where('module', 2)->get()->map(function ($item) {
            return (array)$item;
        })->toArray();

        $sla_config = SlaConfig::where('requisition_type', RequisitionConstants::NONPRINTINGITEM)
            ->whereIn('role_name', ['requisitionChecker', 'requisitionAdminMaker', 'requisitionAdminChecker', 'requUserChecker', 'requisitionSupervisor'])
            ->get()->toArray();

        return view('requisition::SlaConfig.index', compact('req_roles', 'sla_config'));
    }

    public function storeOrUpdate(SlaRequest $request)
    {
        $sla_times = $request->input('sla_time');
        $requisition_type = $request->input('requisition_type');
        $redirectUrl = $request->input('redirectUrl');

        if ($requisition_type == "General Support Service") {
            $requisition_type = RequisitionConstants::SUPPORTSERVICEITEM;
        } elseif ($requisition_type == "Printing Item") {
            $requisition_type = RequisitionConstants::PRINTINGITEM;
        } elseif ($requisition_type == "Non Printing Item") {
            $requisition_type = RequisitionConstants::NONPRINTINGITEM;
        } else {
            flash('Invalid Requisition Type', 'danger');
            return redirect($redirectUrl);
        }

        try {
            foreach ($sla_times as $role_name => $sla_time) {
                $existSla = SlaConfig::where('role_name', $role_name)->where('requisition_type', $requisition_type)->first();

                if ($existSla) {
                    $existSla->delete();
                }

                $slaConfig = new SlaConfig();
                $slaConfig->role_name = $role_name;
                $slaConfig->requisition_type = $requisition_type;
                $slaConfig->sla_time = $sla_time;
                $slaConfig->save();
            }

            flash('Requisition SLA Updated Successfully.', 'success');
            return redirect($redirectUrl);
        } catch (\Throwable $th) {
            flash('Requisition SLA Updating Failed.', 'danger');
            return redirect($redirectUrl);
        }
    }
}
