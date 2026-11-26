<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class RoleUserTmp extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'model_has_roles_tmp';

	protected $fillable = ['name', 'display_name', 'description'];
    public $timestamps = false;
	public function roles()
    {
        return $this->belongsTo('App\Role');
    }

}
