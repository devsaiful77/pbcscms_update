<?php
/**
 * User:Tanay Kumar Roy
 * Email:tanayroy12@gmail.com
 * Created by Tanay Kumar Roy<tanayroy12@gmail.com> on 4/14/2020.
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SubWorkflow extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table ='issue_sub_workflows';
    protected $fillable =[
            'issue_workflow_id',
            'issue_id',
            'group_info_id',
            'options',
            'status',
            'created_by',
            'updated_by',
            'ip'
    ];

}
