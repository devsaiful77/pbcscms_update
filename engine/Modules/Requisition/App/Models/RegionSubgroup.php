<?php

namespace Modules\Requisition\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegionSubgroup extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'requisition_region_subgroup';
}
