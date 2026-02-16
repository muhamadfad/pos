<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEntryItem extends Model
{
    use HasFactory;

    protected $table = 'stock_entry_items';

    protected $fillable = [
        'stock_entry_id',
        'product_id',
        'quantity',
        'purchase_price',
        'subtotal',
    ];

    public function stockEntry()
    {
        return $this->belongsTo(StockEntry::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
