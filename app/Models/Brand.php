<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'image',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): string|null
    {
        return $this->image ? asset($this->image) : null;
    }
}
