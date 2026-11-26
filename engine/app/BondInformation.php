<?php

namespace App;

use App\BondInfoCategory;
use Illuminate\Database\Eloquent\Model;

class BondInformation extends Model
{
    protected $table = 'bond_informations';
    protected $fillable = ['binfo_category_id','binfo_subcategory_id','title','description','file_name', 'status','created_by','updated_by','ip'];
    public function category(){
        return $this->belongsTo(BondInfoCategory::class, 'binfo_category_id');
    }

    public function subCategory(){
        return $this->belongsTo(BondInfoSubCategory::class, 'binfo_subcategory_id');
    }
}
