<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'is_active'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function branchStock()
    {
        return $this->hasMany(BranchStock::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    // Relasi ke ingredients
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }

    // Relasi ke kaspro_predictions
    public function kasproPredictions()
    {
        return $this->hasMany(KasproPrediction::class);
}
}