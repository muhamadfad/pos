<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id',
        'ingredient_id',
        'quantity',
        'purchase_price',
        'subtotal',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'subtotal'       => 'decimal:2',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
