<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepartmentTmp extends Model
{

    // protected $fillable = ['name', 'description',  'status', 'created_by', 'modified_by', 'ip'];
    protected $fillable = ['name', 'description', 'division_id', 'status', 'created_by', 'modified_by', 'ip'];

    protected $table = 'departments_tmp';
}
