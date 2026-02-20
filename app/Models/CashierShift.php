<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashierShift extends Model
{
    protected $fillable = [
        'user_id',
        'opened_at',
        'closed_at',
        'opening_balance',
        'total_sales',
        'transaction_count',
        'expected_cash',
        'actual_cash',
        'cash_difference',
        'status',
        'notes',
    ];

    protected $casts = [
        'opened_at'       => 'datetime',
        'closed_at'       => 'datetime',
        'opening_balance' => 'decimal:2',
        'total_sales'     => 'decimal:2',
        'expected_cash'   => 'decimal:2',
        'actual_cash'     => 'decimal:2',
        'cash_difference' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
