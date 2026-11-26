<?php

namespace App;

use App\ProductType;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class IssueCategoriesTmp extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    // protected $fillable = ['issues_from','product_type_id','name','status'];
    protected $fillable = ['issues_from', 'product_type_id', 'name', 'status', 'created_by', 'modified_by', 'ip'];

    protected $table = 'issue_categories_tmp';

	public $timestamps = false;

    public function productType(){
        return $this->belongsTo(ProductType::class, 'product_type_id', 'id')
                    ->withDefault([
                        'name' => 'Product Type Not Available',
                    ]);
    }
    
}



