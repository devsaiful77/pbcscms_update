<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class IssueCategories extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $fillable = ['issues_from','product_type_id','name','status'];
	public $timestamps = false;

	public function productType(){
        return $this->hasOne('App\ProductType','id','product_type_id');
    }
}
