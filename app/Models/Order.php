<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'cashier_shift_id',
        'user_id',
        'date',
        'source',
        'subtotal',
        'discount',
        'total_amount',
        'total_paid',
        'remaining_receivable',
        'payment_status',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'date'                 => 'date',
        'subtotal'             => 'decimal:2',
        'discount'             => 'decimal:2',
        'total_amount'         => 'decimal:2',
        'total_paid'           => 'decimal:2',
        'remaining_receivable' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashierShift()
    {
        return $this->belongsTo(CashierShift::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }
}
