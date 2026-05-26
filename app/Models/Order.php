<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    
    protected $fillable = [ 
        'client_uuid',
        'invoice_number',
        'customer_id',
        'cashier_shift_id',
        'user_id',
        'branch_id',              // Tambahkan branch_id
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

    // Status constants
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_PARTIAL = 'partial';
    const PAYMENT_STATUS_UNPAID = 'unpaid';

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
        return $this->belongsTo(CashierShift::class, 'cashier_shift_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }

    // Scopes
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForShift($query, $shiftId)
    {
        return $query->where('cashier_shift_id', $shiftId);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_STATUS_PAID);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_STATUS_UNPAID);
    }

    public function scopePartial($query)
    {
        return $query->where('payment_status', self::PAYMENT_STATUS_PARTIAL);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear('date', now()->year)
                     ->whereMonth('date', now()->month);
    }

    // Helper methods
    public function isPaid()
    {
        return $this->payment_status === self::PAYMENT_STATUS_PAID;
    }

    public function isPartial()
    {
        return $this->payment_status === self::PAYMENT_STATUS_PARTIAL;
    }

    public function isUnpaid()
    {
        return $this->payment_status === self::PAYMENT_STATUS_UNPAID;
    }

    public function updatePaymentStatus()
    {
        if ($this->remaining_receivable <= 0) {
            $this->payment_status = self::PAYMENT_STATUS_PAID;
        } elseif ($this->total_paid > 0) {
            $this->payment_status = self::PAYMENT_STATUS_PARTIAL;
        } else {
            $this->payment_status = self::PAYMENT_STATUS_UNPAID;
        }
        $this->saveQuietly();
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::PAYMENT_STATUS_PAID => '<span class="badge bg-success">Lunas</span>',
            self::PAYMENT_STATUS_PARTIAL => '<span class="badge bg-warning">Cicilan</span>',
            self::PAYMENT_STATUS_UNPAID => '<span class="badge bg-danger">Belum Bayar</span>',
        ];
        return $badges[$this->payment_status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    // Generate invoice number
    public static function generateInvoiceNumber($branchId = null)
    {
        $prefix = 'INV';
        $branchCode = $branchId ? sprintf('%03d', $branchId) : '000';
        $date = now()->format('Ymd');
        $lastOrder = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastOrder ? (intval(substr($lastOrder->invoice_number, -4)) + 1) : 1;
        
        return sprintf('%s/%s/%s/%04d', $prefix, $branchCode, $date, $sequence);
    }
}