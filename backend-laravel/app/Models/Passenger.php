<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Trip;
use App\Models\Organization;
class Passenger extends Model
{
    use HasFactory;
    public $fillable = [
        'user_id',
        'organization_id',
        'name',
        'address',
        'phone'
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function wallet() {
        return $this->hasOne(Wallet::class);
    }
    public function trips() {
        return $this->belongsToMany(Trip::class, 'bookings');
    }
    public function organization() {
        return $this->belongsTo(Organization::class);
    }
}
