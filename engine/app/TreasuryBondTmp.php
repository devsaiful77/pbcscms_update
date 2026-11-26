<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TreasuryBondTmp extends Model
{
    protected $table = 'treasury_bond_tmp';
    protected $fillable = ['name', 'week', 'action', 'count', 'status', 'created_by', 'comments', 'modified_by', 'ip'];
}
