<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Route;
use App\Models\Wallet;
use App\Models\User;

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
    public function wallet() {
        return $this->hasOne(Wallet::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
}
