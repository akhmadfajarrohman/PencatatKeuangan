@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('page-title', 'Riwayat Pencatatan Keuangan')

@section('content')
<div class="space-y-6">
    
    <!-- FILTERS PANEL -->
    <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
        <form action="{{ route('transactions.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                
                <!-- Search -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Cari Deskripsi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-2.5 pl-8.5 pr-4 text-xs focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                    </div>
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Jenis</label>
                    <select name="type" class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-2.5 px-3 text-xs focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                        <option value="">Semua Jenis</option>
                        <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pemasukan (+)</option>
                        <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Pengeluaran (-)</option>
                    </select>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Kategori</label>
                    <select name="category_id" class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-2.5 px-3 text-xs focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                ({{ $cat->type === 'income' ? 'IN' : 'OUT' }}) {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Wallet -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Dompet / Akun</label>
                    <select name="wallet_id" class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-2.5 px-3 text-xs focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                        <option value="">Semua Dompet</option>
                        @foreach ($wallets as $wallet)
                            <option value="{{ $wallet->id }}" {{ request('wallet_id') == $wallet->id ? 'selected' : '' }}>
                                {{ $wallet->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range (Start) -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-2.5 px-3 text-xs focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                </div>

                <!-- Date Range (End) -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-2.5 px-3 text-xs focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                </div>

            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-50">
                <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                    Reset Filter
                </a>
                <button type="submit" class="px-5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold rounded-xl shadow-sm transition active:scale-95">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- TRANSACTION RECORDS LIST -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-slate-400 text-xs uppercase border-b border-slate-100">
                        <th class="py-3 font-semibold">Tanggal</th>
                        <th class="py-3 font-semibold">Kategori</th>
                        <th class="py-3 font-semibold">Deskripsi</th>
                        <th class="py-3 font-semibold">Dompet</th>
                        <th class="py-3 font-semibold">Bukti</th>
                        <th class="py-3 font-semibold text-right">Nominal</th>
                        <th class="py-3 font-semibold text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($transactions as $tx)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 text-xs text-slate-500 font-medium">{{ $tx->date->translatedFormat('d M Y') }}</td>
                            <td class="py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold flex items-center gap-1.5 w-fit" 
                                      style="background-color: {{ $tx->category->color }}12; color: {{ $tx->category->color }}">
                                    <i class="fa-solid fa-{{ $tx->category->icon }} text-[9px]"></i>
                                    <span>{{ $tx->category->name }}</span>
                                </span>
                            </td>
                            <td class="py-4">
                                <div class="font-medium text-slate-800 text-xs md:text-sm">{{ $tx->name }}</div>
                                @if ($tx->notes)
                                    <span class="text-[10px] text-slate-400 block mt-0.5 max-w-xs truncate">{{ $tx->notes }}</span>
                                @endif
                            </td>
                            <td class="py-4 text-xs text-slate-600 font-semibold">{{ $tx->wallet->name }}</td>
                            <td class="py-4 text-xs">
                                @if ($tx->attachment)
                                    <a href="{{ asset('storage/' . $tx->attachment) }}" target="_blank" class="text-emerald-500 hover:text-emerald-600 hover:underline flex items-center gap-1">
                                        <i class="fa-regular fa-image text-sm"></i>
                                        <span>Lihat Bukti</span>
                                    </a>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="py-4 text-right font-bold text-xs md:text-sm
                                @if ($tx->type === 'income') text-emerald-600 @else text-rose-600 @endif">
                                {{ $tx->type === 'income' ? '+' : '-' }}Rp{{ number_format($tx->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('transactions.edit', $tx) }}" class="w-7 h-7 bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-lg flex items-center justify-center text-xs transition">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    
                                    <form action="{{ route('transactions.destroy', $tx) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini? Saldo dompet Anda akan disesuaikan kembali!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-7 h-7 bg-rose-50 hover:bg-rose-100 text-rose-500 rounded-lg flex items-center justify-center text-xs transition">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                <i class="fa-solid fa-receipt text-4xl text-slate-200 mb-3 block"></i>
                                Tidak ditemukan riwayat transaksi yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION LINK -->
        <div class="mt-6 border-t border-slate-50 pt-5">
            {{ $transactions->links() }}
        </div>
    </div>

</div>
@endsection
