<?php

namespace App\Http\Controllers;

use App\Models\SavingsTarget;
use App\Models\SavingsTransaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SavingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $targets = $user->savingsTargets()->orderBy('target_date', 'asc')->get();
        $wallets = $user->wallets;

        return view('savings.index', compact('targets', 'wallets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:0.01'],
            'target_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        Auth::user()->savingsTargets()->create([
            'name' => $request->name,
            'target_amount' => $request->target_amount,
            'current_amount' => 0,
            'target_date' => $request->target_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('savings.index')->with('success', 'Target tabungan berhasil ditambahkan.');
    }

    public function storeTransaction(Request $request, SavingsTarget $target)
    {
        if ($target->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'wallet_id' => ['required', 'exists:wallets,id'],
            'type' => ['required', 'string', 'in:add,withdraw'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $wallet = Wallet::where('id', $request->wallet_id)->where('user_id', Auth::id())->firstOrFail();

        // If withdrawing, make sure we don't withdraw more than what is saved
        if ($request->type === 'withdraw' && $request->amount > $target->current_amount) {
            return redirect()->back()->with('error', 'Jumlah penarikan melebihi dana yang terkumpul saat ini.');
        }

        // If adding, make sure the wallet has enough money
        if ($request->type === 'add' && $request->amount > $wallet->balance) {
            return redirect()->back()->with('error', 'Saldo dompet tidak mencukupi untuk menabung sejumlah tersebut.');
        }

        DB::transaction(function () use ($request, $target, $wallet) {
            // Record savings transaction
            SavingsTransaction::create([
                'savings_target_id' => $target->id,
                'wallet_id' => $wallet->id,
                'type' => $request->type,
                'amount' => $request->amount,
                'date' => $request->date,
                'notes' => $request->notes,
            ]);

            if ($request->type === 'add') {
                // Menabung -> Uang di dompet berkurang -> Tabungan bertambah
                $wallet->decrement('balance', $request->amount);
                $target->increment('current_amount', $request->amount);
            } else {
                // Mengambil tabungan -> Uang di dompet bertambah -> Tabungan berkurang
                $wallet->increment('balance', $request->amount);
                $target->decrement('current_amount', $request->amount);
            }
        });

        return redirect()->route('savings.index')->with('success', 'Dana tabungan berhasil diperbarui.');
    }

    public function destroy(SavingsTarget $target)
    {
        if ($target->user_id !== Auth::id()) {
            abort(403);
        }

        // If deleting, should we refund the savings back to wallets?
        // To be safe and simple, we just delete the target. Any transaction details are deleted cascade.
        $target->delete();

        return redirect()->route('savings.index')->with('success', 'Target tabungan berhasil dihapus.');
    }
}
