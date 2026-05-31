@extends('layouts.app')

@section('title', 'Hutang & Cicilan')
@section('page-title', 'Kelola Hutang & Piutang')

@section('content')
<div class="space-y-6">

    <!-- REMINDERS (DUE < 7 DAYS) -->
    @if (!$reminders->isEmpty())
        <div class="bg-gradient-to-r from-amber-500/10 to-orange-500/10 border-l-4 border-amber-500 p-5 rounded-r-2xl shadow-sm space-y-3">
            <h4 class="font-bold text-amber-800 text-sm md:text-base flex items-center gap-2">
                <i class="fa-solid fa-bell text-lg text-amber-600 animate-bounce"></i>
                <span>Pengingat Jatuh Tempo (&lt; 7 Hari)</span>
            </h4>
            <div class="space-y-2">
                @foreach ($reminders as $rem)
                    <div class="flex items-center justify-between text-xs text-slate-700 bg-white/60 p-2.5 rounded-xl border border-amber-100">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-800">{{ $rem->name }}</span>
                            <span class="px-1.5 py-0.5 rounded font-semibold uppercase text-[9px] bg-amber-100 text-amber-800">
                                {{ $rem->type === 'debt' ? 'Hutang' : 'Piutang' }}
                            </span>
                        </div>
                        <div class="font-semibold text-rose-600">
                            Jatuh Tempo: {{ $rem->due_date->translatedFormat('d M Y') }} (Sisa {{ Carbon\Carbon::today()->diffInDays($rem->due_date) }} Hari!)
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- LEFT/CENTER: DEBT LISTING (Grid, Span 2) -->
        <div class="lg:col-span-2 space-y-6">
            <h3 class="font-bold text-slate-800 text-sm md:text-base border-b border-slate-100 pb-3 flex items-center gap-2">
                <i class="fa-solid fa-hand-holding-dollar text-emerald-500"></i>
                <span>Daftar Hutang & Piutang Berjalan</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @forelse ($debts as $debt)
                    <div class="bg-white border rounded-2xl p-5 shadow-sm relative overflow-hidden group hover:shadow transition duration-200
                        @if ($debt->status === 'paid') border-slate-200 opacity-70 @else border-slate-100 @endif">
                        
                        <!-- Colored Top Accent -->
                        <div class="absolute top-0 left-0 right-0 h-1.5 
                            @if ($debt->status === 'paid') bg-slate-300
                            @elseif ($debt->type === 'debt') bg-rose-500
                            @else bg-emerald-500 @endif"></div>

                        <!-- Header -->
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <span class="px-2 py-0.5 rounded font-bold uppercase text-[9px]
                                    @if ($debt->status === 'paid') bg-slate-100 text-slate-500
                                    @elseif ($debt->type === 'debt') bg-rose-50 text-rose-600
                                    @else bg-emerald-50 text-emerald-600 @endif">
                                    {{ $debt->type === 'debt' ? 'Hutang Kita' : 'Piutang Kita' }}
                                </span>
                            </div>
                            
                            <!-- Delete -->
                            <form action="{{ route('debts.destroy', $debt) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-6.5 h-6.5 bg-slate-50 hover:bg-rose-50 text-slate-400 hover:text-rose-500 rounded flex items-center justify-center text-[10px] transition">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>

                        <h4 class="font-bold text-slate-800 text-base">{{ $debt->name }}</h4>
                        <p class="text-[10px] text-slate-400 font-semibold block mt-0.5 uppercase tracking-wider">
                            {{ $debt->type === 'debt' ? 'Pemberi Pinjaman: ' : 'Penerima Pinjaman: ' }} {{ $debt->lender_receiver }}
                        </p>

                        <!-- Numbers -->
                        <div class="grid grid-cols-2 gap-4 border-t border-b border-slate-50 py-3 mt-4 mb-4">
                            <div>
                                <span class="text-[9px] text-slate-400 block font-semibold uppercase">Total Nilai</span>
                                <span class="text-xs font-bold text-slate-500">Rp{{ number_format($debt->amount, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] text-slate-400 block font-semibold uppercase">Sisa Tagihan</span>
                                <span class="text-xs font-bold text-slate-800">Rp{{ number_format($debt->remaining_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Date & Notes -->
                        <div class="space-y-2 mb-4 text-[11px] text-slate-500">
                            @if ($debt->due_date)
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-regular fa-calendar text-slate-400"></i>
                                    <span>Jatuh Tempo: {{ $debt->due_date->translatedFormat('d M Y') }}</span>
                                </div>
                            @endif

                            @if ($debt->notes)
                                <div class="flex items-start gap-1.5 bg-slate-50 p-2 rounded-lg truncate">
                                    <i class="fa-solid fa-info-circle text-slate-400 mt-0.5"></i>
                                    <span>{{ $debt->notes }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        @if ($debt->status === 'pending')
                            <button onclick="openPaymentModal({{ $debt->id }}, '{{ addslashes($debt->name) }}', {{ $debt->remaining_amount }})" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold text-xs rounded-xl py-2 shadow-sm transition active:scale-95 text-center flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-circle-dollar-to-slot"></i>
                                <span>Bayar / Cicil</span>
                            </button>
                        @else
                            <div class="w-full bg-slate-100 text-slate-500 font-semibold text-xs rounded-xl py-2 transition text-center flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                <span>Lunas / Selesai</span>
                            </div>
                        @endif

                    </div>
                @empty
                    <div class="col-span-full bg-slate-50 p-8 text-center rounded-2xl text-slate-400 text-sm">
                        <i class="fa-solid fa-handshake-slash text-4xl text-slate-200 mb-3 block"></i>
                        Belum ada catatan hutang / piutang terdaftar. Tambah baru di sebelah kanan!
                    </div>
                @endforelse
            </div>
        </div>

        <!-- RIGHT SIDE: CREATION FORM -->
        <div class="space-y-6">
            <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm sticky top-6">
                <h3 class="font-bold text-slate-800 text-base mb-4 border-b border-slate-50 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-circle-plus text-emerald-500"></i>
                    <span>Catat Hutang / Piutang</span>
                </h3>

                <form action="{{ route('debts.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tipe Transaksi</label>
                        <select name="type" required class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                            <option value="debt">Hutang (Kita meminjam uang)</option>
                            <option value="receivable">Piutang (Kita meminjamkan uang)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nama Catatan / Keperluan</label>
                        <input type="text" name="name" required placeholder="Contoh: Beli Laptop Asus, Pinjaman Budi" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nama Orang Lain</label>
                        <input type="text" name="lender_receiver" required placeholder="Contoh: Toko Elektronik, Budi Santoso" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Total Nominal Utama</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-xs font-semibold text-slate-400">Rp</span>
                            <input type="number" name="amount" required min="0.01" step="0.01" placeholder="500000" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-3 pl-10 pr-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanggal Jatuh Tempo (Opsional)</label>
                        <input type="date" name="due_date" class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Catatan Keterangan (Opsional)</label>
                        <textarea name="notes" placeholder="Catatan tambahan..." rows="3" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-2.5 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold text-xs md:text-sm rounded-xl py-3 shadow-md shadow-emerald-100 transition active:scale-95 text-center">
                        Simpan Catatan
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>

<!-- PAYMENT TRANSACTION DRAWER MODAL -->
<div id="payment-modal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl relative border border-slate-100 animate-scale-up">
        <div class="absolute top-0 left-0 right-0 h-1 bg-emerald-500"></div>

        <div class="p-5 border-b border-slate-50 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-base">Catat Pembayaran</h3>
            <button onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <form id="payment-form" action="" method="POST" class="p-5 space-y-4">
            @csrf
            
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama Hutang/Piutang</label>
                <input type="text" id="modal-debt-name" readonly class="w-full bg-slate-100 border border-slate-100 text-slate-500 rounded-xl py-2.5 px-4 text-xs focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Pilih Dompet Pendebitan / Kredit</label>
                <select name="wallet_id" required class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-2.5 px-3.5 text-xs focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                    @foreach ($wallets as $wallet)
                        <option value="{{ $wallet->id }}">
                            {{ $wallet->name }} (Rp{{ number_format($wallet->balance, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 font-medium">Nominal Angsuran (Rupiah)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-xs font-semibold text-slate-400">Rp</span>
                    <input type="number" name="amount" id="modal-payment-amount" required min="0.01" step="0.01" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-3 pl-10 pr-4 text-xs focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                </div>
                <p class="text-[9px] text-slate-400 mt-1.5">Maksimal pembayaran sebesar sisa tagihan.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanggal Pembayaran</label>
                <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-2.5 px-3.5 text-xs focus:outline-none focus:border-emerald-500 focus:bg-white transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Catatan (Opsional)</label>
                <input type="text" name="notes" placeholder="Contoh: Cicilan ke-1" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-2.5 px-3.5 text-xs focus:outline-none focus:border-emerald-500 focus:bg-white transition">
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closePaymentModal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs md:text-sm rounded-xl py-2.5 transition text-center">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold text-xs md:text-sm rounded-xl py-2.5 shadow-md shadow-emerald-100 transition active:scale-95 text-center">
                    Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPaymentModal(debtId, debtName, maxAmount) {
        document.getElementById('modal-debt-name').value = debtName;
        
        const amountInput = document.getElementById('modal-payment-amount');
        amountInput.value = maxAmount;
        amountInput.max = maxAmount;
        
        document.getElementById('payment-form').action = `/debts/${debtId}/payment`;
        
        document.getElementById('payment-modal').classList.remove('hidden');
    }

    function closePaymentModal() {
        document.getElementById('payment-modal').classList.add('hidden');
    }
</script>
@endsection
