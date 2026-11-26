<?php

namespace Modules\Requisition\App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitionSMSEmail extends Model
{
    protected $table = 'requisition_sms_email_templates';
    protected $fillable = [
        'issue_opening_sms', 
        'issue_opening_email', 
        'issue_closing_sms', 
        'issue_closing_email',
        'requisition_approve_sms', 
        'requisition_approve_email', 
        'requisition_cancel_sms', 
        'requisition_cancel_email', 
        'requisition_sendback_sms', 
        'requisition_sendback_email'
    ];
}
