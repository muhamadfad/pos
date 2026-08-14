<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    protected $fillable = [
        'branch_id',
        'result_ingredient_id',
        'reference_number',
        'date',
        'result_quantity',
        'total_cost',
        'notes',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function resultIngredient()
    {
        return $this->belongsTo(Ingredient::class, 'result_ingredient_id');
    }

    public function items()
    {
        return $this->hasMany(ProductionItem::class);
    }
}
