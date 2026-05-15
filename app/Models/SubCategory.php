<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
