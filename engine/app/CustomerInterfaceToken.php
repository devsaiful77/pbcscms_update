<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerInterfaceToken extends Model
{
    use HasFactory;

    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', now());
    }
}
