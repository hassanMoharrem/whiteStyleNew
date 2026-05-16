<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'visible',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): string|null
    {
        return $this->image ? asset($this->image) : null;
    }
}
