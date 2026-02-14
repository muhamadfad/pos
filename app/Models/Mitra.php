<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    use HasFactory;

    protected $fillable = ['nama_mitra', 'alamat', 'no_hp', 'jenis_mitra'];

    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class);
    }

    public function terutang()
    {
        return $this->hasMany(Terutang::class);
    }
}
