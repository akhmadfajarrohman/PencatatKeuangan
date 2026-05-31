<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['savings_target_id', 'wallet_id', 'type', 'amount', 'date', 'notes'])]
class SavingsTransaction extends Model
{
    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function savingsTarget()
    {
        return $this->belongsTo(SavingsTarget::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
