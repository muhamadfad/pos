<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\PembayaranHutang;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk';

    protected $fillable = [
        'mitra_id',
        'tanggal',
        'total_harga',
        'total_dibayar',
        'status_pembayaran',
    ];

    public function mitra()
{
    return $this->belongsTo(Mitra::class);
}

public function items()
{
    return $this->hasMany(BarangMasukItem::class);
}

  

    public function pembayaran()
{
    return $this->hasMany(PembayaranHutang::class, 'barang_masuk_id');
}
}

