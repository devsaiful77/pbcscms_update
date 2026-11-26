<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DivisionTmp extends Model
{
    protected $fillable = ['name', 'description',  'status', 'created_by', 'modified_by', 'ip'];

    protected $table = 'divisions_tmp';
}
