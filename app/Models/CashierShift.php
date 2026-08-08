<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashierShift extends Model
{
    protected $fillable = [
        'user_id',
        'cashier_name',
        'shift_time',
        'branch_id',           // Tambahkan branch_id
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

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Scope untuk shift yang sedang berjalan (open)
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope untuk shift yang sudah ditutup (closed)
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope berdasarkan cabang
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope berdasarkan user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check apakah shift masih aktif (open)
     */
    public function isOpen()
    {
        return $this->status === 'open';
    }

    /**
     * Check apakah shift sudah ditutup (closed)
     */
    public function isClosed()
    {
        return $this->status === 'closed';
    }

    /**
     * Hitung selisih kas (actual - expected)
     */
    public function getDifferenceAttribute()
    {
        return $this->actual_cash - $this->expected_cash;
    }

    /**
     * Format status untuk display
     */
    public function getStatusLabelAttribute()
    {
        return $this->status === 'open' 
            ? '<span class="badge badge-success">🟢 Buka</span>' 
            : '<span class="badge badge-secondary">🔴 Tutup</span>';
    }

    /**
     * Update total sales dan transaction count
     */
    public function updateSales($amount)
    {
        $this->increment('total_sales', $amount);
        $this->increment('transaction_count');
        $this->expected_cash = $this->opening_balance + $this->total_sales;
        $this->save();
    }
}