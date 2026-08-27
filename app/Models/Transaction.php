<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'total_items',
        'subtotal',
        'discount',
        'tax',
        'total_price',
        'paid_amount',
        'change_amount',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
