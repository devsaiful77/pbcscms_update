<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IRISWorkflow extends Model
{
    use HasFactory;

    protected $table = 'iris_workflows';
    protected $fillable = ['id','issue_id','group_info_id','status'];
}
