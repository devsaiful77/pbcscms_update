<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApiCredential extends Model
{
    protected $table = 'api_credential';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_name',
        'user_password',
        'token_url',
        'Pull_API_URL',
        'SMS_API_URL',
        'loan_api_request',
    ];

}
