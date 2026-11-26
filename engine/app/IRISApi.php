<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IRISApi extends Model
{
    use HasFactory;
    protected $table = 'iris_apis';
    protected $fillable =[
        'name',
        'url',
        'request',
        'status'
      ];
}
