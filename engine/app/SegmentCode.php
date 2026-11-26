<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegmentCode extends Model
{
    use HasFactory;
    protected $table = 'segment_codes';
    protected $fillable = [
        'code',
        'name',
        'status',
    ];
}
