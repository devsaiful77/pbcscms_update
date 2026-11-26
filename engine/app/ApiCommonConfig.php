<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ApiCommonConfig extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
	protected $table = 'api_common_config';
    protected $fillable = ['id','issue_id','api_parameter','value','type'];
}
