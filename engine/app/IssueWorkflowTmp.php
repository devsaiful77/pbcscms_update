<?php
/**
 * User:Muajjam Hossain
 * Email:muajjam.imu@gmail.com
 * Created by Muajjam Hossain<muajjam.imu@gmail.com> on 12/09/2024.
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class IssueWorkflowTmp extends Model
{

    protected $table ='issue_workflows_tmp';
    protected $primaryKey = 'issue_workflow_id';
    protected $fillable =[
            'issue_workflow_id', 'flow_type', 'issue_id', 'log', 'execute', 'send_back', 'cant_reach_to_customer', 'complain_sla_time', 'touch_group_old', 'non_touch_group_old', 'action', 'master_id', 'status', 'created_by', 'modified_by', 'ip', 'is_active', 'created_at', 'updated_at'
    ];
    public function issue()
    {
        return $this->belongsTo('App\UnitItem','issue_id','id');
    }
}
