<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitItemTmp extends Model
{
    use HasFactory;
    protected $table = 'unit_items_tmp';

    public function productType(){
        return $this->hasOne('App\ProductType','id','product_type_id');
    }

    public function issueCategories(){
        return $this->hasOne('App\IssueCategories','id','issue_categories_id');
    }
}
