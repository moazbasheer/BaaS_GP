<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDetails extends Model
{
    use HasFactory;
    public $fillable = [
        'trip_id',
        'amount',
        'currency',
        'date'
    ];
}
