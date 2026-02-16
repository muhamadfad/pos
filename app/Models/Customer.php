<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'type',
    ];

    public function stockOuts()
    {
        return $this->hasMany(StockOut::class);
    }
}
