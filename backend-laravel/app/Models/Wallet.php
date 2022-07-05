<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Organization;
use App\Models\Passenger;
use App\Models\Client;
class Wallet extends Model
{
    use HasFactory;
    public $fillable = [
        'organization_id',
        'client_id',
        'passenger_id',
        'balance'
    ];
    public function organization() {
        return $this->belongsTo(Organization::class);
    }
    public function client() {
        return $this->belongsTo(Client::class);
    }
    public function passenger() {
        return $this->belongsTo(Passenger::class);
    }
}
