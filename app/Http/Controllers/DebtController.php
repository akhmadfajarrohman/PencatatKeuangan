<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DebtController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $debts = $user->debts()->orderBy('due_date', 'asc')->get();
        $wallets = $user->wallets;

        // Reminder: due date < 7 days and status is pending
        $reminders = $user->debts()
            ->where('status', 'pending')
            ->whereNotNull('due_date')
            ->where('due_date', '<=', Carbon::today()->addDays(7))
            ->where('due_date', '>=', Carbon::today())
            ->get();

        return view('debts.index', compact('debts', 'wallets', 'reminders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => ['required', 'string', 'in:debt,receivable'],
            'name' => ['required', 'string', 'max:255'],
            'lender_receiver' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        Auth::user()->debts()->create([
            'type' => $request->type,
            'name' => $request->name,
            'lender_receiver' => $request->lender_receiver,
            'amount' => $request->amount,
            'remaining_amount' => $request->amount,
            'due_date' => $request->due_date,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return redirect()->route('debts.index')->with('success', 'Catatan Hutang/Piutang berhasil disimpan.');
    }

    public function storePayment(Request $request, Debt $debt)
    {
        if ($debt->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'wallet_id' => ['required', 'exists:wallets,id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:' . $debt->remaining_amount],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $wallet = Wallet::where('id', $request->wallet_id)->where('user_id', Auth::id())->firstOrFail();

        DB::transaction(function () use ($request, $debt, $wallet) {
            // Create payment record
            DebtPayment::create([
                'debt_id' => $debt->id,
                'wallet_id' => $wallet->id,
                'amount' => $request->amount,
                'date' => $request->date,
                'notes' => $request->notes,
            ]);

            // Deduct or Add to wallet balance
            if ($debt->type === 'debt') {
                // Hutang kita berkurang karena kita bayar keluar -> Uang di dompet berkurang
                $wallet->decrement('balance', $request->amount);
            } else {
                // Piutang kita dibayar masuk -> Uang di dompet bertambah
                $wallet->increment('balance', $request->amount);
            }

            // Update remaining debt amount
            $debt->remaining_amount -= $request->amount;
            if ($debt->remaining_amount <= 0) {
                $debt->status = 'paid';
            }
            $debt->save();
        });

        return redirect()->route('debts.index')->with('success', 'Pembayaran Hutang/Piutang berhasil dicatat.');
    }

    public function destroy(Debt $debt)
    {
        if ($debt->user_id !== Auth::id()) {
            abort(403);
        }

        $debt->delete();

        return redirect()->route('debts.index')->with('success', 'Catatan Hutang/Piutang berhasil dihapus.');
    }
}
