<?php

namespace Modules\Requisition\App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequisitionSupportServiceHistory extends Model
{
    use HasFactory;

    protected $table = 'requisition_support_service_histories';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
