<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BondInformationTmp extends Model
{
    use HasFactory;

    public function category(){
        return $this->belongsTo(BondInfoCategory::class, 'binfo_category_id');
    }

    public function subCategory(){
        return $this->belongsTo(BondInfoSubCategory::class, 'binfo_subcategory_id');
    }
}
