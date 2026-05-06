<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeItem extends Model
{
    use HasFactory;

    protected $table = 'recipe_items';

    protected $fillable = [
        'product_id',
        'ingredient_id',
        'quantity',
        'unit_id',
        'waste_percentage',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'waste_percentage' => 'decimal:2'
    ];

    public $timestamps = true;

    // Relasi ke Product (menu)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi ke Ingredient (bahan baku)
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    // Relasi ke Unit
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Hitung total biaya bahan untuk 1 porsi
    public function getCostPerPortionAttribute()
    {
        if (!$this->ingredient) {
            return 0;
        }
        
        $wasteMultiplier = 1 + ($this->waste_percentage / 100);
        return $this->quantity * $this->ingredient->unit_cost * $wasteMultiplier;
    }

    // Hitung total kebutuhan bahan untuk X porsi
    public function getTotalNeeded($portions)
    {
        $wasteMultiplier = 1 + ($this->waste_percentage / 100);
        return $this->quantity * $portions * $wasteMultiplier;
    }

    // Scope untuk recipe aktif
    public function scopeActive($query)
    {
        return $query->whereHas('product', function ($q) {
            $q->where('is_active', true);
        })->whereHas('ingredient', function ($q) {
            $q->where('is_active', true);
        });
    }
}