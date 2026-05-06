<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'barcode',
        'name',
        'category_id',
        'unit_id',
        'selling_price',
        'purchase_price',
        'stock',
        'min_stock',
        'is_active',
    ];

    protected $casts = [
        'selling_price'  => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'is_active'      => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }
    // Relasi ke recipe_items
    public function recipeItems()
    {
        return $this->hasMany(RecipeItem::class);
    }

    // Relasi ke ingredients melalui recipe_items
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_items')
                    ->withPivot('quantity', 'unit_id', 'waste_percentage')
                    ->withTimestamps();
    }
}
