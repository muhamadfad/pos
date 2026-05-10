<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchStock extends Model
{
    use HasFactory;

    protected $table = 'branch_stock';
    
    protected $fillable = [
        'branch_id',
        'ingredient_id', // Changed from product_id to ingredient_id
        'quantity',
        'min_stock',
        'max_stock',
        'last_updated'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'min_stock' => 'integer',
        'max_stock' => 'integer',
        'last_updated' => 'datetime'
    ];

    public $timestamps = false; // Karena tabel branch_stock tidak punya created_at/updated_at

    // Relasi ke Branch
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Relasi ke Ingredient (changed from Product)
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    // Scope untuk stok menipis
    public function scopeLowStock($query)
    {
        return $query->where('quantity', '>', 0)
                     ->whereColumn('quantity', '<=', 'min_stock');
    }

    // Scope untuk stok habis
    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', 0);
    }

    // Cek apakah stok mencukupi
    public function hasEnoughStock($requestedQuantity)
    {
        return $this->quantity >= $requestedQuantity;
    }

    // Kurangi stok
    public function decrementStock($quantity)
    {
        $this->decrement('quantity', $quantity);
        $this->update(['last_updated' => now()]);
    }

    // Tambah stok
    public function incrementStock($quantity)
    {
        $this->increment('quantity', $quantity);
        $this->update(['last_updated' => now()]);
    }
}