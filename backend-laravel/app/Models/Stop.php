<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stop extends Model
{
    use HasFactory;
    public $fillable = [
        'path_id',
        'name',
        'longitude',
        'latitude'
    ];
}
