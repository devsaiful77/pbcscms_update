<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class LdapUserLog extends Model
{
    protected $table ='ldap_api_logs';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id'
    ];

    /**
     * Get the log_user that owns the user.
     */
    public function ldap_user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}
