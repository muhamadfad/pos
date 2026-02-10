<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    protected $table = 'barang_keluar';

    protected $fillable = [
        'product_id',
        'quantity',
        'date',
    ];

    protected $casts = [
        'date' => 'date', // biar bisa pakai ->format()
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
