<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Stop;
class Path extends Model
{
    use HasFactory;
    public $fillable = [
        'route_id',
        'name', 
        'distance',
        'time',
        'price'
    ];
    public function stops() {
        return $this->hasMany(Stop::class);
    }
}
