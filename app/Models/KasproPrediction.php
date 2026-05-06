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
        'product_id',
        'prediction_date',
        'predicted_demand',
        'confidence_level',
        'actual_demand',
        'accuracy_score',
        'model_used',
        'status'
    ];

    protected $casts = [
        'prediction_date' => 'date',
        'predicted_demand' => 'integer',
        'confidence_level' => 'decimal:2',
        'actual_demand' => 'integer',
        'accuracy_score' => 'decimal:2'
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