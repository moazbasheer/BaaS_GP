<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Wallet;
use App\Models\Trip;
use App\Models\User;
class Client extends Model
{
    use HasFactory;
    public $fillable = [
        'user_id',
        'profile_image_name',
        'profile_image_src',
        'first_name',
        'last_name',
        'username',
        'phone_number'
    ];
    public function wallet() {
        return $this->hasOne(Wallet::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function trips() {
        return $this->belongsToMany(Trip::class, 'bookings');
    }
}
