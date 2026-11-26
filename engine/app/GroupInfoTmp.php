<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupInfoTmp extends Model
{
    use HasFactory;

    public function department(){
        return $this->belongsTo(Department::class);
    }
}
