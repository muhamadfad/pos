<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReturnSummary extends Model
{
    protected $fillable = ['date', 'items', 'notes'];
    
    protected $casts = [
        'date' => 'date',
        'items' => 'array',
    ];
}
