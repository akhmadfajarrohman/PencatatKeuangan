@extends('layouts.app')

@section('title', 'Anggaran Bulanan')
@section('page-title', 'Batas Anggaran Bulanan')

@section('content')
<div class="space-y-6">
    
    <!-- MONTH & YEAR SELECTOR -->
    <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h4 class="font-bold text-slate-800 text-sm md:text-base">Target Pengeluaran Bulanan</h4>
            <p class="text-xs text-slate-400">Atur batas maksimal pengeluaran per kategori agar tidak boros.</p>
        </div>

        <form action="{{ route('budgets.index') }}" method="GET" class="flex items-center gap-2">
            <select name="month" class="bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-2 px-3 text-xs focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>

            <select name="year" class="bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-2 px-3 text-xs focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                @for ($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>

            <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold rounded-xl transition">
                Pilih
            </button>
        </form>
    </div>

    <!-- BUDGET LIST GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach ($budgetData as $item)
            @php
                $statusColor = 'bg-blue-500';
                $statusText = '';
                $isWarning = false;
                
                if ($item->limit > 0) {
                    if ($item->percentage >= 100) {
                        $statusColor = 'bg-rose-500';
                        $statusText = 'Melebihi Anggaran!';
                        $isWarning = true;
                    } elseif ($item->percentage >= 80) {
                        $statusColor = 'bg-amber-500';
                        $statusText = 'Mendekati Batas (>=80%)';
                        $isWarning = true;
                    } else {
                        $statusColor = 'bg-emerald-500';
                        $statusText = 'Aman';
                    }
                }
            @endphp
            
            <div class="bg-white border rounded-2xl p-5 shadow-sm relative overflow-hidden transition hover:shadow-md
                @if ($isWarning && $item->percentage >= 100) border-rose-200 bg-rose-50/5
                @elseif ($isWarning && $item->percentage >= 80) border-amber-200 bg-amber-50/5
                @else border-slate-100 @endif">
                
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-base shadow-sm" style="background-color: {{ $item->category->color }}">
                            <i class="fa-solid fa-{{ $item->category->icon }}"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm md:text-base">{{ $item->category->name }}</h4>
                            <span class="text-[10px] text-slate-400 font-semibold uppercase">KATEGORI</span>
                        </div>
                    </div>

                    <!-- Edit Limit Trigger -->
                    <button onclick="openBudgetModal({{ $item->category->id }}, '{{ addslashes($item->category->name) }}', {{ $item->limit }})" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-semibold rounded-lg transition flex items-center gap-1">
                        <i class="fa-solid fa-sliders"></i>
                        <span>Set Batas</span>
                    </button>
                </div>

                <!-- Numbers -->
                <div class="grid grid-cols-2 gap-4 border-t border-b border-slate-50 py-3.5 mb-4">
                    <div>
                        <span class="text-[10px] text-slate-400 block font-semibold uppercase">Sudah Dipakai</span>
                        <span class="text-sm font-bold text-slate-800">Rp{{ number_format($item->spent, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block font-semibold uppercase">Batas Anggaran</span>
                        <span class="text-sm font-bold text-slate-800">
                            @if ($item->limit > 0)
                                Rp{{ number_format($item->limit, 0, ',', '.') }}
                            @else
                                <span class="text-slate-300 font-normal italic">Belum diset</span>
                            @endif
                        </span>
                    </div>
                </div>

                @if ($item->limit > 0)
                    <!-- Progress bar -->
                    <div>
                        <div class="flex justify-between items-center text-xs mb-1.5">
                            <span class="font-semibold text-slate-600">{{ number_format($item->percentage, 1) }}% Terpakai</span>
                            <span class="font-semibold text-xs 
                                @if ($item->percentage >= 100) text-rose-600
                                @elseif ($item->percentage >= 80) text-amber-600
                                @else text-emerald-600 @endif">
                                {{ $statusText }}
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition duration-500 {{ $statusColor }}" style="width: {{ min(100, $item->percentage) }}%"></div>
                        </div>
                    </div>
                @else
                    <p class="text-[11px] text-slate-400 italic"><i class="fa-solid fa-info-circle mr-1 text-slate-300"></i>Batas belanja bulanan untuk kategori ini belum didefinisikan.</p>
                @endif

            </div>
        @endforeach
    </div>

</div>

<!-- BUDGET SETTING DRAWER MODAL -->
<div id="budget-modal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl relative border border-slate-100 animate-scale-up">
        <div class="absolute top-0 left-0 right-0 h-1 bg-emerald-500"></div>

        <div class="p-5 border-b border-slate-50 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-base">Atur Anggaran</h3>
            <button onclick="closeBudgetModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <form action="{{ route('budgets.store') }}" method="POST" class="p-5 space-y-4">
            @csrf
            
            <input type="hidden" name="category_id" id="modal-category-id">
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Kategori</label>
                <input type="text" id="modal-category-name" readonly class="w-full bg-slate-100 border border-slate-100 text-slate-500 rounded-xl py-2.5 px-4 text-xs md:text-sm focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Batas Pengeluaran (Rupiah)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-xs font-semibold text-slate-400">Rp</span>
                    <input type="number" name="amount" id="modal-amount" required min="0" placeholder="0" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-3 pl-10 pr-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                </div>
                <p class="text-[9px] text-slate-400 mt-1.5">Masukkan angka 0 untuk menghapus batas anggaran kategori ini.</p>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeBudgetModal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs md:text-sm rounded-xl py-2.5 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold text-xs md:text-sm rounded-xl py-2.5 shadow-md shadow-emerald-100 transition active:scale-95">
                    Simpan Anggaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openBudgetModal(categoryId, categoryName, limit) {
        document.getElementById('modal-category-id').value = categoryId;
        document.getElementById('modal-category-name').value = categoryName;
        document.getElementById('modal-amount').value = limit;
        
        document.getElementById('budget-modal').classList.remove('hidden');
    }

    function closeBudgetModal() {
        document.getElementById('budget-modal').classList.add('hidden');
    }
</script>
@endsection
