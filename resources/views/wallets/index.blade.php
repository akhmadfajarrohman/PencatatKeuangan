@extends('layouts.app')

@section('title', 'Dompetku')
@section('page-title', 'Kelola Dompet & Metode Pembayaran')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- LEFT & CENTER: WALLET DIRECTORY (Grid, Span 2) -->
    <div class="lg:col-span-2 space-y-6">
        <h3 class="font-bold text-slate-800 text-sm md:text-base border-b border-slate-100 pb-3 flex items-center gap-2">
            <i class="fa-solid fa-vault text-emerald-500"></i>
            <span>Daftar 1 Dompet & Akun Aktif</span>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @forelse ($wallets as $wallet)
                <!-- Wallet Card -->
                <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm relative overflow-hidden group hover:shadow-md transition duration-200">
                    <!-- Colored top border accent -->
                    <div class="absolute top-0 left-0 right-0 h-1.5 
                        @if ($wallet->type === 'cash') bg-amber-500
                        @elseif ($wallet->type === 'bank') bg-blue-500
                        @else bg-purple-500 @endif"></div>

                    <!-- Type icon -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-base shadow-sm
                            @if ($wallet->type === 'cash') bg-amber-500
                            @elseif ($wallet->type === 'bank') bg-blue-500
                            @else bg-purple-500 @endif">
                            @if ($wallet->type === 'cash') <i class="fa-solid fa-money-bill"></i>
                            @elseif ($wallet->type === 'bank') <i class="fa-solid fa-building-columns"></i>
                            @else <i class="fa-solid fa-mobile-screen-button"></i> @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-1.5 opacity-60 group-hover:opacity-100 transition">
                            <!-- Toggle Edit Form View -->
                            <button onclick="toggleEditForm({{ $wallet->id }}, '{{ addslashes($wallet->name) }}', '{{ $wallet->type }}', {{ $wallet->balance }}, '{{ addslashes($wallet->notes) }}')" class="w-7 h-7 bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-lg flex items-center justify-center text-xs transition">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            
                            <form action="{{ route('wallets.destroy', $wallet) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dompet ini? Semua data transaksi yang terkait juga akan dihapus!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-7 h-7 bg-rose-50 hover:bg-rose-100 text-rose-500 rounded-lg flex items-center justify-center text-xs transition">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <h4 class="font-bold text-slate-800 text-base">{{ $wallet->name }}</h4>
                    <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider block mt-0.5">{{ $wallet->type }}</span>

                    <div class="mt-4 border-t border-slate-50 pt-4 flex justify-between items-center">
                        <span class="text-xs text-slate-400">Saldo Saat Ini</span>
                        <span class="text-lg font-bold text-slate-900">Rp{{ number_format($wallet->balance, 0, ',', '.') }}</span>
                    </div>

                    @if ($wallet->notes)
                        <p class="text-[11px] text-slate-400 mt-3.5 italic bg-slate-50 p-2 rounded-lg truncate">{{ $wallet->notes }}</p>
                    @endif
                </div>
            @empty
                <div class="col-span-full bg-slate-50 p-8 text-center rounded-2xl text-slate-400 text-sm">
                    <i class="fa-solid fa-vault text-4xl text-slate-200 mb-3 block"></i>
                    Belum ada dompet / metode pembayaran. Silakan tambah satu di kanan untuk memulai!
                </div>
            @endforelse
        </div>
    </div>

    <!-- RIGHT SIDE: CREATION / EDIT CARD -->
    <div class="space-y-6">
        
        <!-- ADD WALLET CARD -->
        <div id="add-wallet-card" class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm sticky top-6">
            <h3 class="font-bold text-slate-800 text-base mb-4 border-b border-slate-50 pb-3 flex items-center gap-2">
                <i class="fa-solid fa-circle-plus text-emerald-500"></i>
                <span>Tambah Dompet Baru</span>
            </h3>

            <form action="{{ route('wallets.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nama Dompet</label>
                    <input type="text" name="name" required placeholder="Contoh: Dompet Cash, Rekening Mandiri, GoPay" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Jenis Akun</label>
                    <select name="type" required class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                        <option value="cash">Cash (Uang Tunai)</option>
                        <option value="bank">Bank (Mandiri, BCA, dll.)</option>
                        <option value="e-wallet">E-Wallet (GoPay, DANA, OVO, dll.)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Saldo Awal</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-xs font-semibold text-slate-400">Rp</span>
                        <input type="number" name="balance" required min="0" value="0" placeholder="0" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-3 pl-10 pr-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" placeholder="Contoh: Digunakan untuk pengeluaran harian, simpanan darurat, dll." rows="3" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-2.5 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition"></textarea>
                </div>

                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold text-xs md:text-sm rounded-xl py-3 shadow-md shadow-emerald-100 transition active:scale-95">
                    Buat Dompet
                </button>
            </form>
        </div>

        <!-- EDIT WALLET CARD (Hidden by Default) -->
        <div id="edit-wallet-card" class="hidden bg-white border border-slate-200 rounded-2xl p-6 shadow-md sticky top-6">
            <div class="flex items-center justify-between border-b border-slate-50 pb-3 mb-4">
                <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-blue-500"></i>
                    <span>Ubah Dompet</span>
                </h3>
                <button onclick="cancelEdit()" class="text-xs text-slate-400 hover:text-slate-600 font-semibold"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form id="edit-wallet-form" action="" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nama Dompet</label>
                    <input type="text" name="name" id="edit-name" required placeholder="Nama Dompet" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Jenis Akun</label>
                    <select name="type" id="edit-type" required class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition">
                        <option value="cash">Cash (Uang Tunai)</option>
                        <option value="bank">Bank (Mandiri, BCA, dll.)</option>
                        <option value="e-wallet">E-Wallet (GoPay, DANA, OVO, dll.)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Saldo</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-xs font-semibold text-slate-400">Rp</span>
                        <input type="number" name="balance" id="edit-balance" required min="0" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-3 pl-10 pr-4 text-xs md:text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" id="edit-notes" placeholder="Catatan" rows="3" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-2.5 px-4 text-xs md:text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition"></textarea>
                </div>

                <div class="flex gap-2">
                    <button type="button" onclick="cancelEdit()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs md:text-sm rounded-xl py-3 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs md:text-sm rounded-xl py-3 shadow-md shadow-blue-100 transition active:scale-95">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>

<!-- INTERACTIVE ACTIONS SCRIPT -->
<script>
    function toggleEditForm(id, name, type, balance, notes) {
        document.getElementById('add-wallet-card').classList.add('hidden');
        
        const editCard = document.getElementById('edit-wallet-card');
        editCard.classList.remove('hidden');
        
        // Fill form fields
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-type').value = type;
        document.getElementById('edit-balance').value = balance;
        document.getElementById('edit-notes').value = notes;
        
        // Set Action URL
        document.getElementById('edit-wallet-form').action = `/wallets/${id}`;
        
        // Scroll to card on mobile
        editCard.scrollIntoView({ behavior: 'smooth' });
    }

    function cancelEdit() {
        document.getElementById('edit-wallet-card').classList.add('hidden');
        document.getElementById('add-wallet-card').classList.remove('hidden');
    }
</script>
@endsection
