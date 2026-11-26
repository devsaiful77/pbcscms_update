<?php

namespace Modules\Requisition\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequisitionAttachment extends Model
{
    use HasFactory;

    protected $table = 'requisition_attachment';
    protected $fillable = [
        'id',
        'requisition_number',
        'attachment',
        'attachment_title',
        'created_at',
        'updated_at',
    ];

    public function requisitionItem(){
        return $this->belongsTo(RequisitionItem::class, 'product_id', 'id');
    }
}
