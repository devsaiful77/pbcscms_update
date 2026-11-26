<?php

namespace Modules\Requisition\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stock';
    protected $guarded = ['id'];

    public function requItem()
    {
        return $this->belongsTo(RequisitionItem::class, 'requisition_item_id');
    }
}
