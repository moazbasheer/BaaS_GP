<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Path;
class Trip extends Model
{
    use HasFactory;
    public $fillable = [
        'path_id',
        'organization_id',
        'repitition',
        'date',
        'time',
        'status',
        'price'
    ];
    public function path() {
        return $this->belongsTo(Path::class);
    }
}
