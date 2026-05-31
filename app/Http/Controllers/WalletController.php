<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $wallets = Auth::user()->wallets;
        return view('wallets.index', compact('wallets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:cash,bank,e-wallet'],
            'balance' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        Auth::user()->wallets()->create([
            'name' => $request->name,
            'type' => $request->type,
            'balance' => $request->balance,
            'notes' => $request->notes,
        ]);

        return redirect()->route('wallets.index')->with('success', 'Dompet berhasil ditambahkan.');
    }

    public function update(Request $request, Wallet $wallet)
    {
        if ($wallet->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:cash,bank,e-wallet'],
            'balance' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $wallet->update([
            'name' => $request->name,
            'type' => $request->type,
            'balance' => $request->balance,
            'notes' => $request->notes,
        ]);

        return redirect()->route('wallets.index')->with('success', 'Dompet berhasil diperbarui.');
    }

    public function destroy(Wallet $wallet)
    {
        if ($wallet->user_id !== Auth::id()) {
            abort(403);
        }

        $wallet->delete();

        return redirect()->route('wallets.index')->with('success', 'Dompet berhasil dihapus.');
    }
}
