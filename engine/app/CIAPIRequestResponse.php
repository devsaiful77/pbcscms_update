<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CIAPIRequestResponse extends Model
{
	protected $table = 'ci_api_request_responses';

    protected $fillable = [
        'id',
        'cif_number',
        'product_type',
        'global_transaction_id',
        'status_code',
        'status_msg',
        'endpoint',
        'json_node',
        'execution_time',
    ];

}
