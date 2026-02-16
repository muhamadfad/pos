<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOut extends Model
{
    protected $fillable = [
        'date',
        'customer_id',
        'cashier_shift_id',
        'total_amount',
        'total_paid',
        'remaining_receivable',
        'payment_status',
    ];

    protected $casts = [
        'date' => 'date',
        'total_amount' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'remaining_receivable' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(StockOutItem::class);
    }

    public function payments()
    {
        return $this->hasMany(ReceivablePayment::class);
    }

    public function cashierShift()
    {
        return $this->belongsTo(CashierShift::class);
    }
}
