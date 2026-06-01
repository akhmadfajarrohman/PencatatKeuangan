<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Query setup
        $query = $user->transactions()->with(['wallet', 'category']);

        // Filters
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('wallet_id')) {
            $query->where('wallet_id', $request->wallet_id);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $transactions = $query->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Data for filters
        $wallets = $user->wallets;
        $categories = Category::whereNull('user_id')
            ->orWhere('user_id', $user->id)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('transactions.index', compact('transactions', 'wallets', 'categories'));
    }

    public function create()
    {
        $user = Auth::user();
        $wallets = $user->wallets;
        
        if ($wallets->isEmpty()) {
            return redirect()->route('wallets.index')->with('warning', 'Silakan buat minimal satu Dompet terlebih dahulu sebelum mencatat transaksi.');
        }

        $categories = Category::whereNull('user_id')
            ->orWhere('user_id', $user->id)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('transactions.create', compact('wallets', 'categories'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'date' => ['required', 'date'],
            'type' => ['required', 'string', 'in:income,expense'],
            'wallet_id' => ['required', 'exists:wallets,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string'],
            'attachment' => ['nullable', 'image', 'max:10048'], // Max 2MB image
        ]);

        // Security check
        $wallet = Wallet::where('id', $request->wallet_id)->where('user_id', $user->id)->firstOrFail();
        
        DB::transaction(function () use ($request, $user, $wallet) {
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('attachments', 'public');
            }

            // Create Transaction
            Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $request->wallet_id,
                'category_id' => $request->category_id,
                'date' => $request->date,
                'type' => $request->type,
                'name' => $request->name,
                'amount' => $request->amount,
                'notes' => $request->notes,
                'attachment' => $attachmentPath,
            ]);

            // Update Wallet Balance
            if ($request->type === 'income') {
                $wallet->increment('balance', $request->amount);
            } else {
                $wallet->decrement('balance', $request->amount);
            }
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dicatat.');
    }

    public function edit(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $user = Auth::user();
        $wallets = $user->wallets;
        $categories = Category::whereNull('user_id')
            ->orWhere('user_id', $user->id)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('transactions.edit', compact('transaction', 'wallets', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $user = Auth::user();

        $request->validate([
            'date' => ['required', 'date'],
            'type' => ['required', 'string', 'in:income,expense'],
            'wallet_id' => ['required', 'exists:wallets,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string'],
            'attachment' => ['nullable', 'image', 'max:10048'],
        ]);

        // Security check for wallet
        $newWallet = Wallet::where('id', $request->wallet_id)->where('user_id', $user->id)->firstOrFail();
        $oldWallet = Wallet::findOrFail($transaction->wallet_id);

        DB::transaction(function () use ($request, $transaction, $newWallet, $oldWallet) {
            // Revert old wallet changes
            if ($transaction->type === 'income') {
                $oldWallet->decrement('balance', $transaction->amount);
            } else {
                $oldWallet->increment('balance', $transaction->amount);
            }

            // Handle new attachment
            $attachmentPath = $transaction->attachment;
            if ($request->hasFile('attachment')) {
                // Delete old attachment if exists
                if ($attachmentPath) {
                    Storage::disk('public')->delete($attachmentPath);
                }
                $attachmentPath = $request->file('attachment')->store('attachments', 'public');
            }

            // Update Transaction
            $transaction->update([
                'wallet_id' => $request->wallet_id,
                'category_id' => $request->category_id,
                'date' => $request->date,
                'type' => $request->type,
                'name' => $request->name,
                'amount' => $request->amount,
                'notes' => $request->notes,
                'attachment' => $attachmentPath,
            ]);

            // Apply new wallet changes
            if ($request->type === 'income') {
                $newWallet->increment('balance', $request->amount);
            } else {
                $newWallet->decrement('balance', $request->amount);
            }
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        DB::transaction(function () use ($transaction) {
            $wallet = Wallet::findOrFail($transaction->wallet_id);

            // Revert wallet changes
            if ($transaction->type === 'income') {
                $wallet->decrement('balance', $transaction->amount);
            } else {
                $wallet->increment('balance', $transaction->amount);
            }

            // Delete attachment if exists
            if ($transaction->attachment) {
                Storage::disk('public')->delete($transaction->attachment);
            }

            // Delete transaction
            $transaction->delete();
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
