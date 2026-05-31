<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['debt_id', 'wallet_id', 'amount', 'date', 'notes'])]
class DebtPayment extends Model
{
    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
