<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'name', 'type', 'balance', 'notes'])]
class Wallet extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function debtPayments()
    {
        return $this->hasMany(DebtPayment::class);
    }

    public function savingsTransactions()
    {
        return $this->hasMany(SavingsTransaction::class);
    }
}
