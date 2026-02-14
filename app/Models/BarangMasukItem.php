<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasukItem extends Model
{
    use HasFactory;

    protected $fillable = [
    'barang_masuk_id',
    'product_id',
    'qty',
    'harga_beli',
    'subtotal'
];


   public function barangMasuk()
{
    return $this->belongsTo(BarangMasuk::class);
}

public function product()
{
    return $this->belongsTo(Product::class);
}
}
