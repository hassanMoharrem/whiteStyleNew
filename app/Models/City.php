<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name',
        'delivery_price',
    ];

    protected $casts = [
        'delivery_price' => 'decimal:2',
    ];
}
