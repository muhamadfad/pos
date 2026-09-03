<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'reference_number',
        'supplier_id',
        'branch_id',
        'date',
        'total_amount',
        'total_paid',
        'remaining_debt',
        'payment_status',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'date'           => 'date',
        'total_amount'   => 'decimal:2',
        'total_paid'     => 'decimal:2',
        'remaining_debt' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }
}
