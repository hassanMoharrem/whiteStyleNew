<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'price',
        'discount_price',
        'review_count',
        'brand_id',
        'sub_category_id',
        'images',
        'sizes',
    ];

    protected $casts = [
        'images' => 'array',
        'sizes' => 'array',
        'price' => 'float',
        'discount_price' => 'float',
        'review_count' => 'integer',
    ];

    protected $appends = ['images_url','image_url', 'sizes_details'];

    public function getImagesUrlAttribute(): array
    {
        if (!$this->images) return [];

        return array_map(function ($image) {
            return [
                'name' => $image['name'],
                'url'  => asset('storage/' . $image['url']),
            ];
        }, $this->images);
    }

    public function getSizesDetailsAttribute(): array
    {
        if (!$this->sizes || !is_array($this->sizes)) return [];

        return Size::whereIn('id', $this->sizes)->get(['id', 'name'])->toArray();
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->images || empty($this->images)) {
            return null;
        }

        $firstImage = $this->images[0];
        return asset('storage/' . $firstImage['url']);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function size()
    {
        return $this->belongsTo(Size::class);
    }
}
