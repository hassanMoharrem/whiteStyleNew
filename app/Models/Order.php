<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_phone',
        'city_name',
        'area_name',
        'street_name',
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

    // تم حذف علاقة city لأن city_id لم يعد موجودًا
}
