<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    protected $table = 'barang_masuk';

    protected $fillable = ['product_id', 'quantity', 'date'];

    protected $casts = [
        'date' => 'date', // <- penting juga
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
