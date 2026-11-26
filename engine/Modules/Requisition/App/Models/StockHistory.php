<?php

namespace Modules\Requisition\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockHistory extends Model
{
    use HasFactory;

    protected $table = 'stock_history';
    protected $fillable = ['requisition_item_id', 'vendor_id', 'create_id', 'quantity', 'challan_no', 'created_at'];

    public function requItem()
    {
        return $this->belongsTo(RequisitionItem::class, 'requisition_item_id');
    }
}
