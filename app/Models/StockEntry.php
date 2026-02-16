<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEntry extends Model
{
    use HasFactory;

    protected $table = 'stock_entries';

    protected $fillable = [
        'supplier_id',
        'date',
        'total_amount',
        'total_paid',
        'remaining_debt',
        'payment_status',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(StockEntryItem::class);
    }

    public function payments()
    {
        return $this->hasMany(DebtPayment::class, 'stock_entry_id');
    }
}
