<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductTypeTmp extends Model
{
    protected $table ='product_types_tmp';
    protected $fillable = ['name','description',  'status', 'created_by', 'modified_by', 'ip'];
}
