<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TreasuryBond extends Model
{
    protected $table = 'treasury_bonds';
    protected $fillable = ['name', 'week', 'drow', 'status', 'created_by', 'modified_by', 'ip'];
}
