<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'phone', 'type'];

    public function stockEntries()
    {
        return $this->hasMany(StockEntry::class);
    }
}
