<?php

namespace Modules\Requisition\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequisitionGeneralService extends Model
{
    use HasFactory;

    protected $table = 'requisition_general_service';
    protected $fillable = [
        'id',
        'requisition_number',
        'product_id',
        'quantity',
        'approved_qty',
        'status',
        'created_at',
        'updated_at',
    ];

    public function requItem()
    {
        return $this->belongsTo(RequisitionItem::class, 'product_id', 'id');
    }

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_number', 'requisition_number');
    }
}
