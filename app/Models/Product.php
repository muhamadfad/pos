<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'barcode',
        'name',
        'category_id',
        'price',
        'purchase_price',
        'stock',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
