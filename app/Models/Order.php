<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_phone',
        'city_id',
        'address',
        'description',
        'items',
        'subtotal',
        'delivery_price',
        'total',
        'status',
    ];

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'decimal:2',
        'delivery_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
