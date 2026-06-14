<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingredient extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ingredients';

    protected $fillable = [
        'name',
        'unit_id',
        'current_stock',
        'min_stock',
        'unit_cost',
        'branch_id',
        'is_active'
    ];

    protected $casts = [
        'current_stock' => 'decimal:6',
        'min_stock' => 'decimal:6',
        'unit_cost' => 'decimal:6',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime'
    ];

    // Relasi ke Unit
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Relasi ke Branch
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Relasi ke Recipe Items
    public function recipeItems()
    {
        return $this->hasMany(RecipeItem::class);
    }

    // Relasi ke Purchase Items (pembelian bahan baku)
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class, 'ingredient_id');
    }

    // Scope untuk stok menipis
    public function scopeLowStock($query)
    {
        return $query->where('current_stock', '>', 0)
                     ->whereColumn('current_stock', '<=', 'min_stock');
    }

    // Scope untuk stok habis
    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', '<=', 0);
    }

    // Cek apakah stok mencukupi
    public function hasEnoughStock($quantity)
    {
        return $this->current_stock >= $quantity;
    }

    // Kurangi stok
    public function decrementStock($quantity)
    {
        $this->decrement('current_stock', $quantity);
    }

    // Tambah stok
    public function incrementStock($quantity)
    {
        $this->increment('current_stock', $quantity);
    }

    // Hitung total cost untuk quantity tertentu
    public function getTotalCost($quantity)
    {
        return $quantity * $this->unit_cost;
    }
}