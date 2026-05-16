<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

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

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }
}
