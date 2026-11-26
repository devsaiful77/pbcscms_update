<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtpCode extends Model
{
    use HasFactory ,SoftDeletes;

    protected $AUTO_INCREMENT = true;
    public $modelNotFoundMessage = 'Data not be found with given id(s).';

    protected $fillable = ['id','sif_number','service_type','reference_number','mobile','otp','expire_at','status'];
}
