<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;
    public $fillable = [
        'message',
        'trip_id',
        'passenger_id',
        'client_id'
    ];
}
