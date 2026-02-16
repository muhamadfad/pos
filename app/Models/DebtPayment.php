<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebtPayment extends Model
{
    use HasFactory;

    protected $table = 'debt_payments';

    protected $fillable = [
        'stock_entry_id',
        'payment_date',
        'amount',
        'payment_method',
    ];

    public function stockEntry()
    {
        return $this->belongsTo(StockEntry::class, 'stock_entry_id');
    }
}
