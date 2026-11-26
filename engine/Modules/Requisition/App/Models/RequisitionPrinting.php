<?php

namespace Modules\Requisition\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequisitionPrinting extends Model
{
    use HasFactory;

    protected $table = 'requisition_printing';

    protected $fillable = [
        'id',
        'requisition_number',
        'product_id',
        'quantity',
        'approve_qty',
        'status',
        'created_at',
        'updated_at',
    ];

    public function requItem()
    {
        return $this->belongsTo(RequisitionItem::class, 'product_id', 'id');
    }
}
