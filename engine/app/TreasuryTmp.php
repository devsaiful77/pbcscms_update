<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TreasuryTmp extends Model
{
    protected $table = 'treasury_bills_tmp';
    protected $fillable = ['name', 'week', 'action', 'count', 'status', 'created_by', 'comments', 'modified_by', 'ip'];
}
