@extends('layouts.app')

@section('title', 'Kategori')
@section('page-title', 'Kelola Kategori Keuangan')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- LEFT & CENTER: CATEGORY INDEX (Grid, Span 2) -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- EXPENSE CATEGORIES -->
        <div>
            <h3 class="font-bold text-slate-800 text-sm md:text-base border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-circle-arrow-up text-rose-500"></i>
                <span>Kategori Pengeluaran (Expense)</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($categories->where('type', 'expense') as $category)
                    <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-sm flex items-center justify-between group hover:shadow transition duration-150">
                        <div class="flex items-center gap-3">
                            <!-- Icon block with dynamic color -->
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center text-white" style="background-color: {{ $category->color }}">
                                <i class="fa-solid fa-{{ $category->icon }}"></i>
                            </div>
                            <div>
                                <h4 class="text-xs md:text-sm font-semibold text-slate-700">{{ $category->name }}</h4>
                                <span class="text-[9px] text-slate-400 font-medium">
                                    @if ($category->user_id === null)
                                        <i class="fa-solid fa-shield mr-1"></i>Sistem
                                    @else
                                        <i class="fa-solid fa-user mr-1"></i>Kustom
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Actions (Only show for custom categories) -->
                        @if ($category->user_id !== null)
                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition">
                                <button onclick="toggleEditCategory({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ $category->type }}', '{{ $category->color }}', '{{ $category->icon }}')" class="w-6.5 h-6.5 bg-slate-50 hover:bg-slate-100 text-slate-500 rounded flex items-center justify-center text-[10px] transition">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Semua transaksi terkait juga akan terpengaruh!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-6.5 h-6.5 bg-rose-50 hover:bg-rose-100 text-rose-500 rounded flex items-center justify-center text-[10px] transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- INCOME CATEGORIES -->
        <div class="pt-4">
            <h3 class="font-bold text-slate-800 text-sm md:text-base border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-circle-arrow-down text-emerald-500"></i>
                <span>Kategori Pemasukan (Income)</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($categories->where('type', 'income') as $category)
                    <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-sm flex items-center justify-between group hover:shadow transition duration-150">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center text-white" style="background-color: {{ $category->color }}">
                                <i class="fa-solid fa-{{ $category->icon }}"></i>
                            </div>
                            <div>
                                <h4 class="text-xs md:text-sm font-semibold text-slate-700">{{ $category->name }}</h4>
                                <span class="text-[9px] text-slate-400 font-medium">
                                    @if ($category->user_id === null)
                                        <i class="fa-solid fa-shield mr-1"></i>Sistem
                                    @else
                                        <i class="fa-solid fa-user mr-1"></i>Kustom
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Actions -->
                        @if ($category->user_id !== null)
                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition">
                                <button onclick="toggleEditCategory({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ $category->type }}', '{{ $category->color }}', '{{ $category->icon }}')" class="w-6.5 h-6.5 bg-slate-50 hover:bg-slate-100 text-slate-500 rounded flex items-center justify-center text-[10px] transition">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Semua transaksi terkait juga akan terpengaruh!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-6.5 h-6.5 bg-rose-50 hover:bg-rose-100 text-rose-500 rounded flex items-center justify-center text-[10px] transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- RIGHT SIDE: CREATION / EDIT SIDEBAR -->
    <div class="space-y-6">
        
        <!-- ADD CATEGORY CARD -->
        <div id="add-category-card" class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm sticky top-6">
            <h3 class="font-bold text-slate-800 text-base mb-4 border-b border-slate-50 pb-3 flex items-center gap-2">
                <i class="fa-solid fa-circle-plus text-emerald-500"></i>
                <span>Tambah Kategori Kustom</span>
            </h3>

            <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nama Kategori</label>
                    <input type="text" name="name" required placeholder="Contoh: Sedekah, Listrik, Investasi" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Jenis Kategori</label>
                    <select name="type" required class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                        <option value="expense">Pengeluaran (Expense)</option>
                        <option value="income">Pemasukan (Income)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Warna Identitas (Hex)</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="color" required value="#10b981" class="w-12 h-12 bg-slate-50 border border-slate-100 rounded-xl cursor-pointer p-1">
                        <span class="text-xs text-slate-400 font-semibold">Pilih warna untuk grafik Chart.js</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Pilih Icon (FontAwesome)</label>
                    <select name="icon" class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                        <option value="tags">🏷️ Tags (Default)</option>
                        <option value="utensils">🍴 Makan & Minum</option>
                        <option value="car">🚗 Transportasi</option>
                        <option value="gamepad">🎮 Hiburan & Jajan</option>
                        <option value="file-invoice-dollar">🧾 Tagihan / Invoice</option>
                        <option value="shopping-cart">🛒 Belanja</option>
                        <option value="heartbeat">🩺 Kesehatan</option>
                        <option value="graduation-cap">🎓 Pendidikan</option>
                        <option value="gift">🎁 Hadiah & THR</option>
                        <option value="money-bill-wave">💵 Gaji / Pendapatan</option>
                        <option value="laptop-code">💻 Sampingan / Freelance</option>
                        <option value="chart-line">📈 Investasi</option>
                        <option value="hand-holding-usd">🤲 Sedekah / Uang Saku</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold text-xs md:text-sm rounded-xl py-3 shadow-md shadow-emerald-100 transition active:scale-95">
                    Buat Kategori
                </button>
            </form>
        </div>

        <!-- EDIT CATEGORY CARD (Hidden by Default) -->
        <div id="edit-category-card" class="hidden bg-white border border-slate-200 rounded-2xl p-6 shadow-md sticky top-6">
            <div class="flex items-center justify-between border-b border-slate-50 pb-3 mb-4">
                <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-blue-500"></i>
                    <span>Ubah Kategori</span>
                </h3>
                <button onclick="cancelEditCategory()" class="text-xs text-slate-400 hover:text-slate-600 font-semibold"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form id="edit-category-form" action="" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nama Kategori</label>
                    <input type="text" name="name" id="edit-cat-name" required class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Jenis Kategori</label>
                    <select name="type" id="edit-cat-type" required class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition">
                        <option value="expense">Pengeluaran (Expense)</option>
                        <option value="income">Pemasukan (Income)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Warna Identitas (Hex)</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="color" id="edit-cat-color" required class="w-12 h-12 bg-slate-50 border border-slate-100 rounded-xl cursor-pointer p-1">
                        <span class="text-xs text-slate-400 font-semibold">Ubah warna penanda grafik</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Pilih Icon (FontAwesome)</label>
                    <select name="icon" id="edit-cat-icon" class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition">
                        <option value="tags">🏷️ Tags (Default)</option>
                        <option value="utensils">🍴 Makan & Minum</option>
                        <option value="car">🚗 Transportasi</option>
                        <option value="gamepad">🎮 Hiburan & Jajan</option>
                        <option value="file-invoice-dollar">🧾 Tagihan / Invoice</option>
                        <option value="shopping-cart">🛒 Belanja</option>
                        <option value="heartbeat">🩺 Kesehatan</option>
                        <option value="graduation-cap">🎓 Pendidikan</option>
                        <option value="gift">🎁 Hadiah & THR</option>
                        <option value="money-bill-wave">💵 Gaji / Pendapatan</option>
                        <option value="laptop-code">💻 Sampingan / Freelance</option>
                        <option value="chart-line">📈 Investasi</option>
                        <option value="hand-holding-usd">🤲 Sedekah / Uang Saku</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="button" onclick="cancelEditCategory()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs md:text-sm rounded-xl py-3 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs md:text-sm rounded-xl py-3 shadow-md shadow-blue-100 transition active:scale-95">
                        Simpan
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>

<!-- INTERACTIVE ACTIONS SCRIPT -->
<script>
    function toggleEditCategory(id, name, type, color, icon) {
        document.getElementById('add-category-card').classList.add('hidden');
        
        const editCard = document.getElementById('edit-category-card');
        editCard.classList.remove('hidden');
        
        // Fill form fields
        document.getElementById('edit-cat-name').value = name;
        document.getElementById('edit-cat-type').value = type;
        document.getElementById('edit-cat-color').value = color;
        document.getElementById('edit-cat-icon').value = icon;
        
        // Set Action URL
        document.getElementById('edit-category-form').action = `/categories/${id}`;
        
        // Scroll to card on mobile
        editCard.scrollIntoView({ behavior: 'smooth' });
    }

    function cancelEditCategory() {
        document.getElementById('edit-category-card').classList.add('hidden');
        document.getElementById('add-category-card').classList.remove('hidden');
    }
</script>
@endsection
