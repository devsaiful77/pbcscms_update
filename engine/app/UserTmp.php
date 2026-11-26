<?php

namespace App;

use App\UserUnitTmp;
use Adldap\Laravel\Traits\HasLdapUser;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasPermissions;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserTmp extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use Notifiable, HasLdapUser, HasRoles, HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table='users_tmp';
    protected $fillable = [
        'user_id', 'role_id', 'name', 'designation', 'national_id',
        'outlet_id', 'mobile_no', 'email', 'password',
        'status', 'profile_picture',
        'remember_token',
        'is_active',
        'is_block',
        'password_changed_at'

    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function roleName()
    {
        return $this->belongsTo('App\Role', 'role_id', 'id');
    }


    /*public function roles()
    {
        return $this->belongsToMany('App\Role');
    }

    public function role_user()
    {
        return $this->hasOne('App\RoleUser');
    }*/

    /**
     * Get the user_type that owns the user.
     */
    public function user_type()
    {
        return $this->belongsTo('App\UserType');
    }
    /**
     * Get the branch_users that owns the user.
     */
    public function branch_users()
    {
        return $this->hasOne('App\BranchUser','user_id','id');
    }
    public function userUnit()
    {
        return $this->belongsTo(UserUnitTmp::class,'user_id','user_id');
    }
    public function user_subgroupinfo()
    {
        return $this->hasOne('App\UserSubgroupInfo','user_id','id');
    }



    public function passwordHistories()
    {
        return $this->hasMany('App\PasswordHistory');
    }
}
