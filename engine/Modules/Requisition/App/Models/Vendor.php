<?php

namespace Modules\Requisition\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Vendor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];
    
    public function products()
    {
        return $this->belongsToMany(
            RequisitionItem::class,
            'requisition_product_vendor',
            'vendor_id',
            'product_id'
        )->withTimestamps();
    }
}
