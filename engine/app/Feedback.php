<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;
    protected $table = 'feedback' ;
    protected $fillable = [
        'cif_number',
        'ticket_number',
        'comments',
        'mobile_no',
        'email',
        'log_date',
        'status',
        'loger',
    ] ;
}
