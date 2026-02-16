<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOutItem extends Model
{
    protected $fillable = [
        'stock_out_id',
        'product_id',
        'quantity',
        'selling_price',
        'subtotal',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function stockOut()
    {
        return $this->belongsTo(StockOut::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
