<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CIUserSession extends Model
{
    use HasFactory;
    protected $table = 'ci_user_sessions';

    protected $guarded = ['id'];
}
