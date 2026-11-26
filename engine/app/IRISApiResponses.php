<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IRISApiResponses extends Model
{
    use HasFactory;

    protected $table = 'iris_api_responses';

    protected $guarded = ['id'];
}
