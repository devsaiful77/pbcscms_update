<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerInterfaceAPI extends Model
{
	protected $table = 'customer_interface_apis';

    protected $fillable = [
        'id', 
        'name', 
        'endpoint', 
        'product_type', 
        'request_body', 
        'status', 
        'date_format'
    ];

    public function productType()
    {
        return $this->belongsTo('App\ProductType', 'product_type', 'id');
    }

}
