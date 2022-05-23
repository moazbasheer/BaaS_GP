<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;
    public $fillable = [
        'user_id',
        'profile_image_name',
        'profile_image_src',
        'name',
        'address',
        'postal_code',
        'phone_number'
    ];
    public function routes() {
        return $this->hasMany(Route::class);
    }
}
