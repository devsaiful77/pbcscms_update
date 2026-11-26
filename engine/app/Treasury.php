<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Treasury extends Model
{
    protected $table = 'treasury_bills';
    protected $fillable = ['name', 'week', 'count', 'status', 'created_by', 'modified_by', 'ip'];
}
