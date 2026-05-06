<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'abbreviation'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
        // Relasi ke ingredients
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }

    // Relasi ke recipe_items
    public function recipeItems()
    {
        return $this->hasMany(RecipeItem::class);
    }
}
