<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceivablePayment extends Model
{
    protected $fillable = [
        'stock_out_id',
        'payment_date',
        'amount',
        'payment_method',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function stockOut()
    {
        return $this->belongsTo(StockOut::class);
    }
}
