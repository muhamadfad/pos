<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    protected $fillable = [
        'reference_number',
        'from_branch_id',
        'to_branch_id',
        'user_id',
        'transfer_date',
        'notes',
        'status',
        'source_ingredient_id',
        'source_quantity',
    ];

    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function sourceIngredient()
    {
        return $this->belongsTo(Ingredient::class, 'source_ingredient_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(StockTransferItem::class);
    }
}
