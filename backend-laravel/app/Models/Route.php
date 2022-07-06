<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;
    public $fillable = [
        'organization_id',
        'name',
        'source',
        'source_longitude',
        'source_latitude',
        'destination',
        'destination_longitude',
        'destination_latitude'
    ];
}
