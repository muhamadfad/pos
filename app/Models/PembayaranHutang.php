<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranHutang extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_hutang';

    protected $fillable = [
        'barang_masuk_id',
        'tanggal_bayar',
        'jumlah_bayar',
        'metode_bayar',
    ];

    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class, 'barang_masuk_id');
    }
}
