<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationDish extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id', 'dish_id', 'quantity'
    ];
}
