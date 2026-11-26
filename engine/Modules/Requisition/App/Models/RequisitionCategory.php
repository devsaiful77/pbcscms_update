<?php

namespace Modules\Requisition\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Requisition\App\Models\RequisitionSubCategory;

class RequisitionCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];
    protected $table = 'requisition_categories';

    public function subcategories()
    {
        return $this->hasMany(RequisitionSubCategory::class, 'category_id', 'id');
    }

}
