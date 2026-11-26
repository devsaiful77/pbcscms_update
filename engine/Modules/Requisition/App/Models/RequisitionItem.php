<?php

namespace Modules\Requisition\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequisitionItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'requisition_items';

    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(RequisitionCategory::class);
    }

    // Relationship with SubCategory
    public function subCategory()
    {
        return $this->belongsTo(RequisitionSubCategory::class);
    }

    //product unit
    public function unit()
    {
        return $this->belongsTo(ProductUnit::class);
    }

    public function stock()
    {
        return $this->hasOne(Stock::class, 'requisition_item_id', 'id');
    }

    public function vendors()
    {
        return $this->belongsToMany(
            Vendor::class,
            'requisition_product_vendor',
            'product_id',
            'vendor_id'
        )->withTimestamps();
    }
}