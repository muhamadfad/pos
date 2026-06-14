<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasproPrediction extends Model
{
    use HasFactory;

    protected $table = 'kaspro_predictions';

    protected $fillable = [
        'branch_id',
        'user_id',
        'month',
        'predicted_income',
        'predicted_expense',
        'predicted_net',
        'details'
    ];

    protected $casts = [
        'predicted_income' => 'decimal:2',
        'predicted_expense' => 'decimal:2',
        'predicted_net' => 'decimal:2',
        'details' => 'array'
    ];

    // Relasi ke Branch
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Relasi ke Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Scope prediksi valid (belum lewat)
    public function scopeUpcoming($query)
    {
        return $query->where('prediction_date', '>=', now()->toDateString());
    }

    // Scope prediksi sudah lewat
    public function scopePast($query)
    {
        return $query->where('prediction_date', '<', now()->toDateString());
    }

    // Scope dengan akurasi tinggi
    public function scopeHighAccuracy($query, $threshold = 80)
    {
        return $query->where('accuracy_score', '>=', $threshold);
    }

    // Hitung error prediksi
    public function getPredictionErrorAttribute()
    {
        if (!$this->actual_demand) {
            return null;
        }
        return abs($this->predicted_demand - $this->actual_demand);
    }

    // Hitung persentase akurasi jika belum diisi
    public function calculateAccuracy()
    {
        if (!$this->actual_demand || $this->actual_demand == 0) {
            return 0;
        }
        
        $error = abs($this->predicted_demand - $this->actual_demand);
        $accuracy = (1 - ($error / $this->actual_demand)) * 100;
        
        return round($accuracy, 2);
    }
}