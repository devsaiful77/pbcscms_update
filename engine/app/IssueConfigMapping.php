<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class IssueConfigMapping extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
	protected $table = 'issue_config_mapping';
    protected $fillable = ['id','issue_id','field_name','api_parameter','inquiry_field'];
}
