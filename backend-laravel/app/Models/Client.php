<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Wallet;
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
}
