<?php

namespace Modules\Requisition\App\Models;

use App\SubgroupInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Region extends Model
{
    use HasFactory;

    protected $table = 'requisition_region';
    protected $guarded = ['id'];

    public function subgroupInfo()
    {
        return $this->hasManyThrough(
            SubgroupInfo::class,
            RegionSubgroup::class,
            'region_id',           // Foreign key on RegionSubgroup table
            'id',                  // Foreign key on SubgroupInfo table (usually its primary key)
            'id',                  // Local key on Region table
            'subgroup_info_id'     // Local key on RegionSubgroup table
        );
    }
}
