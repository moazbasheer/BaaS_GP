<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Path;
use App\Models\Client;
use App\Models\Passenger;
use App\Models\Organization;
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
    public function clients() {
        return $this->belongsToMany(Client::class, 'bookings');
    }
    public function passengers() {
        return $this->belongsToMany(Passenger::class, 'bookings');
    }
    public function organization() {
        return $this->belongsTo(Organization::class);
    }
}
