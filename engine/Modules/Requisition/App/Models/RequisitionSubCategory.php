<?php

namespace Modules\Requisition\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Requisition\App\Models\RequisitionCategory;

class RequisitionSubCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];
    protected $table = 'requisition_sub_categories';
    public function category()
    {
        return $this->belongsTo(RequisitionCategory::class);
    }

}
