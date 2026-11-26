<?php

namespace Modules\Requisition\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OutgoingVendorCommunication extends Model
{
    use HasFactory;

    protected $table = 'outgoing_vendor_email';
    protected $guarded = ['id'];
}
