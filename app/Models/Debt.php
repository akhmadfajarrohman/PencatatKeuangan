<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'type', 'name', 'lender_receiver', 'amount', 'remaining_amount', 'due_date', 'notes', 'status'])]
class Debt extends Model
{
    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(DebtPayment::class);
    }
}
