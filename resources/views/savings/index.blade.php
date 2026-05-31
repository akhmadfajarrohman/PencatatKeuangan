@extends('layouts.app')

@section('title', 'Target Tabungan')
@section('page-title', 'Kelola Target Tabungan')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- LEFT & CENTER: SAVINGS INDEX (Grid, Span 2) -->
    <div class="lg:col-span-2 space-y-6">
        <h3 class="font-bold text-slate-800 text-sm md:text-base border-b border-slate-100 pb-3 flex items-center gap-2">
            <i class="fa-solid fa-piggy-bank text-emerald-500"></i>
            <span>Daftar Impian & Target Tabungan</span>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @forelse ($targets as $target)
                @php
                    $percentage = $target->target_amount > 0 ? min(100, ($target->current_amount / $target->target_amount) * 100) : 0;
                @endphp
                
                <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm relative overflow-hidden group hover:shadow transition duration-200">
                    <!-- Top color accent -->
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-purple-500"></div>

                    <!-- Header -->
                    <div class="flex items-center justify-between mb-3.5">
                        <div class="w-9 h-9 bg-purple-50 text-purple-500 rounded-lg flex items-center justify-center text-sm shadow-sm">
                            <i class="fa-solid fa-bullseye"></i>
                        </div>
                        
                        <!-- Delete action -->
                        <form action="{{ route('savings.destroy', $target) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus target ini? Semua histori tabungan terkait juga akan terhapus!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-6.5 h-6.5 bg-slate-50 hover:bg-rose-50 text-slate-400 hover:text-rose-500 rounded flex items-center justify-center text-[10px] transition">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>

                    <h4 class="font-bold text-slate-800 text-base">{{ $target->name }}</h4>
                    
                    @if ($target->target_date)
                        <span class="text-[10px] text-slate-400 font-semibold block mt-0.5"><i class="fa-regular fa-calendar-days mr-1 text-slate-350"></i>Hingga {{ $target->target_date->translatedFormat('d M Y') }}</span>
                    @endif

                    <!-- Numbers -->
                    <div class="grid grid-cols-2 gap-4 border-t border-b border-slate-50 py-3 mt-4 mb-4">
                        <div>
                            <span class="text-[9px] text-slate-400 block font-semibold uppercase">Dana Terkumpul</span>
                            <span class="text-sm font-bold text-purple-600">Rp{{ number_format($target->current_amount, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 block font-semibold uppercase">Target Nominal</span>
                            <span class="text-sm font-bold text-slate-800">Rp{{ number_format($target->target_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="space-y-1.5 mb-5">
                        <div class="flex justify-between items-center text-[10px] font-semibold text-slate-500">
                            <span>Progress Tabungan</span>
                            <span>{{ number_format($percentage, 1) }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-500 to-indigo-600 h-full rounded-full transition duration-500" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>

                    <!-- Actions Contribution / Draw -->
                    <div class="flex gap-2">
                        <!-- Contribution button -->
                        <button onclick="openSavingsModal({{ $target->id }}, '{{ addslashes($target->name) }}', 'add')" class="flex-1 bg-purple-50 hover:bg-purple-100 text-purple-700 font-semibold text-xs rounded-xl py-2 transition active:scale-95 text-center flex items-center justify-center gap-1">
                            <i class="fa-solid fa-plus-circle"></i>
                            <span>Menabung</span>
                        </button>
                        
                        <!-- Withdraw button -->
                        @if ($target->current_amount > 0)
                            <button onclick="openSavingsModal({{ $target->id }}, '{{ addslashes($target->name) }}', 'withdraw')" class="flex-1 bg-slate-50 hover:bg-slate-100 text-slate-600 font-semibold text-xs rounded-xl py-2 transition active:scale-95 text-center flex items-center justify-center gap-1">
                                <i class="fa-solid fa-circle-arrow-up"></i>
                                <span>Ambil</span>
                            </button>
                        @endif
                    </div>

                    @if ($target->notes)
                        <p class="text-[10px] text-slate-400 mt-3.5 italic bg-slate-50 p-2 rounded-lg truncate">{{ $target->notes }}</p>
                    @endif
                </div>
            @empty
                <div class="col-span-full bg-slate-50 p-8 text-center rounded-2xl text-slate-400 text-sm">
                    <i class="fa-solid fa-piggy-bank text-4xl text-slate-200 mb-3 block"></i>
                    Belum ada impian / target tabungan dicatat. Buat satu di sebelah kanan sekarang!
                </div>
            @endforelse
        </div>
    </div>

    <!-- RIGHT SIDE: CREATION FORM -->
    <div class="space-y-6">
        <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm sticky top-6">
            <h3 class="font-bold text-slate-800 text-base mb-4 border-b border-slate-50 pb-3 flex items-center gap-2">
                <i class="fa-solid fa-circle-plus text-emerald-500"></i>
                <span>Tambah Target Impian</span>
            </h3>

            <form action="{{ route('savings.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nama Impian</label>
                    <input type="text" name="name" required placeholder="Contoh: Beli Laptop Baru, Liburan Bali" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Target Nominal</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-xs font-semibold text-slate-400">Rp</span>
                        <input type="number" name="target_amount" required min="0.01" step="0.01" placeholder="3000000" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-3 pl-10 pr-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Target Waktu (Opsional)</label>
                    <input type="date" name="target_date" class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Catatan Keterangan (Opsional)</label>
                    <textarea name="notes" placeholder="Catatan motivasi atau detil impian..." rows="3" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-2.5 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition"></textarea>
                </div>

                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold text-xs md:text-sm rounded-xl py-3 shadow-md shadow-emerald-100 transition active:scale-95 text-center">
                    Buat Target Tabungan
                </button>
            </form>
        </div>
    </div>

</div>

<!-- SAVINGS TRANSACTION DRAWER MODAL -->
<div id="savings-modal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl relative border border-slate-100 animate-scale-up">
        <div class="absolute top-0 left-0 right-0 h-1 bg-purple-500"></div>

        <div class="p-5 border-b border-slate-50 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-base" id="modal-savings-title">Catat Setoran Tabungan</h3>
            <button onclick="closeSavingsModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <form id="savings-form" action="" method="POST" class="p-5 space-y-4">
            @csrf
            
            <input type="hidden" name="type" id="modal-savings-type">

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama Target Tabungan</label>
                <input type="text" id="modal-savings-target-name" readonly class="w-full bg-slate-100 border border-slate-100 text-slate-500 rounded-xl py-2.5 px-4 text-xs focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Pilih Dompet Sumber / Tujuan</label>
                <select name="wallet_id" required class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-2.5 px-3.5 text-xs focus:outline-none focus:border-purple-500 focus:bg-white transition">
                    @foreach ($wallets as $wallet)
                        <option value="{{ $wallet->id }}">
                            {{ $wallet->name }} (Rp{{ number_format($wallet->balance, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 font-medium">Nominal Dana (Rupiah)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-xs font-semibold text-slate-400">Rp</span>
                    <input type="number" name="amount" id="modal-savings-amount" required min="0.01" step="0.01" placeholder="50000" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-3 pl-10 pr-4 text-xs focus:outline-none focus:border-purple-500 focus:bg-white transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanggal Transaksi</label>
                <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-2.5 px-3.5 text-xs focus:outline-none focus:border-purple-500 focus:bg-white transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Keterangan Catatan (Opsional)</label>
                <input type="text" name="notes" placeholder="Contoh: Nabung uang kembalian belanja" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-2.5 px-3.5 text-xs focus:outline-none focus:border-purple-500 focus:bg-white transition">
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeSavingsModal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs md:text-sm rounded-xl py-2.5 transition text-center">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-purple-650 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs md:text-sm rounded-xl py-2.5 shadow-md shadow-purple-100 transition active:scale-95 text-center">
                    Proses
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openSavingsModal(targetId, targetName, type) {
        document.getElementById('modal-savings-target-name').value = targetName;
        document.getElementById('modal-savings-type').value = type;
        
        const title = document.getElementById('modal-savings-title');
        if (type === 'add') {
            title.innerText = 'Catat Tabungan (Menabung)';
        } else {
            title.innerText = 'Ambil Dana Tabungan';
        }
        
        document.getElementById('savings-form').action = `/savings/${targetId}/transaction`;
        
        document.getElementById('savings-modal').classList.remove('hidden');
    }

    function closeSavingsModal() {
        document.getElementById('savings-modal').classList.add('hidden');
    }
</script>
@endsection
