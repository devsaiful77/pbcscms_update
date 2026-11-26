<?php

namespace Modules\Requisition\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Requisition\App\Models\RequisitionCategory;

class SlaConfig extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'requisition_sla_config';
}