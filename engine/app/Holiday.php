<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Holiday extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
	protected $table = 'holidays';
	public $timestamps = false;
    protected $fillable = ['id','day','months','year','dates','type','remarks','status'];
}
