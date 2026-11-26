<?php

namespace Modules\Requisition\App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequisitionComments extends Model
{
    use HasFactory;

    protected $table = 'requisition_comments';
    protected $fillable = [
        'id',
        'requisition_number',
        'unit_id',
        'group_id',
        'subgroup_id',
        'comment',
        'action',
        'commented_by',
        'time',
        'user_id',
        'isapproved',
        'issendback',
        'status',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
