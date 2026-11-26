<?php
/**
 * User:Muajjam Hossain
 * Email:muajjam.imu@gmail.com
 * Created by Muajjam Hossain<muajjam.imu@gmail.com> on 12/09/2024.
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class IssueGroupWorkflowTmp extends Model
{

    protected $table ='issue_group_workflows_tmp';
    protected $primaryKey = 'issue_group_workflow_id';
    protected $fillable =[
                'issue_workflow_id',
                'group_info_id',
                'touch_maker',
                'touch_checker',
                'hold_maker',
                'hold_checker',
                'sla_maker',
                'sla_checker',
                'attach_maker',
                'attach_maker_item',
                'attach_checker',
                'attach_checker_item',
                'order_by',
                'is_touch_point'
    ];
    public function group_info()
    {
        return $this->belongsTo('App\GroupInfo','group_info_id','id');
    }
}
