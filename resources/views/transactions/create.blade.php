@extends('layouts.app')

@section('title', 'Catat Transaksi')
@section('page-title', 'Catat Transaksi Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    
    <div class="bg-white border border-slate-100 rounded-2xl p-6 md:p-8 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-emerald-500 to-teal-500"></div>

        <div class="mb-6">
            <h3 class="font-bold text-slate-800 text-base md:text-lg">Catat Pengeluaran & Pemasukan</h3>
            <p class="text-xs text-slate-400 mt-0.5">Isi detail formulir transaksi di bawah ini dengan benar.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 text-xs rounded-r-xl space-y-1">
                @foreach ($errors->all() as $error)
                    <div class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0 text-rose-500"></i>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Grid 1: Tanggal & Jenis -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanggal Transaksi</label>
                    <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}" class="w-full bg-slate-50 border border-slate-100 text-slate-800 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Jenis Transaksi</label>
                    <select name="type" id="type-select" required class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                        <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>Pengeluaran (-)</option>
                        <option value="income" {{ old('type') === 'income' ? 'selected' : '' }}>Pemasukan (+)</option>
                    </select>
                </div>
            </div>

            <!-- Grid 2: Dompet & Kategori -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Dompet / Metode Pembayaran</label>
                    <select name="wallet_id" required class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                        @foreach ($wallets as $wallet)
                            <option value="{{ $wallet->id }}" {{ old('wallet_id') == $wallet->id ? 'selected' : '' }}>
                                {{ $wallet->name }} (Saldo: Rp{{ number_format($wallet->balance, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Kategori Keuangan</label>
                    <select name="category_id" id="category-select" required class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" data-type="{{ $cat->type }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Nama & Nominal -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Deskripsi Transaksi</label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="Contoh: Nasi Goreng, Pembelian Token Listrik" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nominal (Rupiah)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-xs font-semibold text-slate-400">Rp</span>
                        <input type="number" name="amount" required step="0.01" min="0.01" value="{{ old('amount') }}" placeholder="15000" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-3 pl-10 pr-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                    </div>
                </div>
            </div>

            <!-- Catatan -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" placeholder="Tuliskan catatan detail jika diperlukan..." rows="3" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-2.5 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">{{ old('notes') }}</textarea>
            </div>

            <!-- Upload Bukti (Optional) -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Upload Bukti / Nota (Opsional)</label>
                <div class="w-full bg-slate-50 border border-dashed border-slate-200 rounded-xl p-4 flex items-center gap-4 hover:bg-slate-100/50 transition">
                    <div class="w-10 h-10 bg-white border border-slate-100 text-slate-400 rounded-lg flex items-center justify-center text-base">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <div class="flex-1 text-left">
                        <input type="file" name="attachment" accept="image/*" class="text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-slate-900 file:text-white file:cursor-pointer">
                        <span class="text-[9px] text-slate-400 block mt-1">Hanya gambar (JPG, PNG, GIF), maks 2MB</span>
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center gap-3 pt-4 border-t border-slate-50">
                <a href="{{ route('transactions.index') }}" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs md:text-sm rounded-xl py-3 transition text-center">
                    Batal
                </a>
                <button type="submit" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold text-xs md:text-sm rounded-xl py-3 shadow-md shadow-emerald-100 transition active:scale-95 text-center">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>

</div>

<!-- FILTER DYNAMIC CATEGORY BY TYPE -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const typeSelect = document.getElementById('type-select');
        const categorySelect = document.getElementById('category-select');
        const allCategories = Array.from(categorySelect.options);

        function filterCategories() {
            const selectedType = typeSelect.value;
            categorySelect.innerHTML = '';
            
            allCategories.forEach(option => {
                if (option.getAttribute('data-type') === selectedType) {
                    categorySelect.appendChild(option.cloneNode(true));
                }
            });
        }

        // Initialize and listen to change
        typeSelect.addEventListener('change', filterCategories);
        filterCategories();
    });
</script>
@endsection
