@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Utama')

@section('content')
<div class="space-y-6">

    <!-- WARNING BANNER IF TODAY'S EXPENSE OVER DAILY SAFE LIMIT -->
    @if ($totalBalance > 0 && $todayExpense > $safeDailyLimit)
        <div class="p-5 bg-gradient-to-r from-amber-500/10 to-orange-500/10 border-l-4 border-orange-500 text-slate-800 rounded-r-2xl shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4 animate-pulse">
            <div class="flex gap-3.5">
                <div class="w-12 h-12 bg-orange-500 text-white rounded-xl flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="font-bold text-orange-800 text-sm md:text-base">Pengeluaran Hari Ini Melewati Batas Aman!</h3>
                    <p class="text-xs text-slate-600 mt-1">
                        Batas aman harian Anda adalah <span class="font-semibold text-slate-900">Rp{{ number_format($safeDailyLimit, 0, ',', '.') }}</span>. Hari ini Anda sudah membelanjakan <span class="font-semibold text-rose-600">Rp{{ number_format($todayExpense, 0, ',', '.') }}</span>. Ayo hemat hingga gajian berikutnya!
                    </p>
                </div>
            </div>
            <div class="text-xs bg-orange-500 text-white px-3 py-1.5 rounded-lg font-semibold shrink-0">
                Sisa {{ $daysRemaining }} Hari Gajian
            </div>
        </div>
    @endif

    <!-- QUICK HIGHLIGHT STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- CARD 1: SALDO (Biru) -->
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white p-6 rounded-2xl shadow-lg relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 text-white/10 text-8xl pointer-events-none">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <span class="text-xs text-blue-100 uppercase tracking-wider font-semibold block mb-1">Total Saldo Saat Ini</span>
            <h3 class="text-2xl md:text-3xl font-extrabold tracking-tight">Rp{{ number_format($totalBalance, 0, ',', '.') }}</h3>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-blue-100 bg-white/10 w-fit px-2.5 py-1 rounded-full backdrop-blur-sm">
                <i class="fa-solid fa-vault"></i>
                <span>Aktif di {{ $wallets->count() }} Dompet</span>
            </div>
        </div>

        <!-- CARD 2: PEMASUKAN BULAN INI (Hijau) -->
        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-slate-500 uppercase tracking-wider font-medium">Pemasukan Bulan Ini</span>
                    <div class="w-8 h-8 bg-emerald-50 text-emerald-500 rounded-lg flex items-center justify-center text-sm">
                        <i class="fa-solid fa-circle-arrow-down"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 tracking-tight">Rp{{ number_format($monthlyIncome, 0, ',', '.') }}</h3>
            </div>
            <span class="text-[10px] text-slate-400 mt-4 block"><i class="fa-regular fa-clock mr-1"></i>Periode {{ Carbon\Carbon::today()->translatedFormat('F Y') }}</span>
        </div>

        <!-- CARD 3: PENGELUARAN BULAN INI (Merah) -->
        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-slate-500 uppercase tracking-wider font-medium">Pengeluaran Bulan Ini</span>
                    <div class="w-8 h-8 bg-rose-50 text-rose-500 rounded-lg flex items-center justify-center text-sm">
                        <i class="fa-solid fa-circle-arrow-up"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 tracking-tight">Rp{{ number_format($monthlyExpense, 0, ',', '.') }}</h3>
            </div>
            <span class="text-[10px] text-slate-400 mt-4 block"><i class="fa-regular fa-clock mr-1"></i>Periode {{ Carbon\Carbon::today()->translatedFormat('F Y') }}</span>
        </div>

        <!-- CARD 4: BATAS AMAN HARIAN (Oranye / Biru muda) -->
        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-slate-500 uppercase tracking-wider font-medium">Batas Aman Harian</span>
                    <div class="w-8 h-8 bg-amber-50 text-amber-500 rounded-lg flex items-center justify-center text-sm">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 tracking-tight">
                    Rp{{ number_format($safeDailyLimit, 0, ',', '.') }}
                </h3>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-slate-500 bg-slate-550 w-fit">
                <i class="fa-regular fa-calendar-days text-slate-400"></i>
                <span>{{ $daysRemaining }} hari ke gajian ({{ $nextPayday->translatedFormat('d M') }})</span>
            </div>
        </div>

    </div>

    <!-- MAIN GRAPHICS & STATS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Line Chart: Pengeluaran Harian 7 Hari Terakhir (Span 2) -->
        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="font-bold text-slate-800 text-sm md:text-base">Pengeluaran 7 Hari Terakhir</h4>
                    <p class="text-xs text-slate-400">Tren pengeluaran harian Anda satu minggu ke belakang.</p>
                </div>
            </div>
            <div class="h-64 relative">
                <canvas id="dailyExpenseChart"></canvas>
            </div>
        </div>

        <!-- Pie Chart: Pengeluaran Kategori Bulan Ini -->
        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="font-bold text-slate-800 text-sm md:text-base">Alokasi Kategori</h4>
                    <p class="text-xs text-slate-400">Pembagian pengeluaran bulan ini.</p>
                </div>
            </div>
            <div class="h-64 relative flex items-center justify-center">
                @if ($categoryExpenses->isEmpty())
                    <div class="text-center p-6 text-slate-400">
                        <i class="fa-solid fa-chart-pie text-4xl mb-2 text-slate-200"></i>
                        <p class="text-xs">Belum ada pengeluaran dicatat bulan ini.</p>
                    </div>
                @else
                    <canvas id="categoryExpenseChart"></canvas>
                @endif
            </div>
        </div>
    </div>

    <!-- WALLETS, SAVINGS AND DEBTS GRID -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Wallets Card -->
        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between mb-4 border-b border-slate-50 pb-3">
                <h4 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-wallet mr-2 text-blue-500"></i>Dompet Saya</h4>
                <a href="{{ route('wallets.index') }}" class="text-xs text-emerald-500 hover:underline font-semibold">Semua</a>
            </div>
            <div class="space-y-3.5">
                @forelse ($wallets as $wallet)
                    <div class="flex items-center justify-between p-3 bg-slate-50 hover:bg-slate-100/70 rounded-xl transition duration-150">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 text-white rounded-lg flex items-center justify-center text-sm shadow-sm
                                @if ($wallet->type === 'cash') bg-amber-500
                                @elseif ($wallet->type === 'bank') bg-blue-500
                                @else bg-purple-500 @endif">
                                @if ($wallet->type === 'cash') <i class="fa-solid fa-money-bill"></i>
                                @elseif ($wallet->type === 'bank') <i class="fa-solid fa-building-columns"></i>
                                @else <i class="fa-solid fa-mobile-screen-button"></i> @endif
                            </div>
                            <div>
                                <h5 class="text-xs font-semibold text-slate-700">{{ $wallet->name }}</h5>
                                <span class="text-[10px] text-slate-400 uppercase">{{ $wallet->type }}</span>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-slate-850">Rp{{ number_format($wallet->balance, 0, ',', '.') }}</span>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400">
                        <p class="text-xs">Belum ada dompet.</p>
                        <a href="{{ route('wallets.index') }}" class="text-xs text-emerald-500 hover:underline mt-2 inline-block font-semibold">Buat Dompet</a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Savings Goals Target -->
        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between mb-4 border-b border-slate-50 pb-3">
                <h4 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-piggy-bank mr-2 text-purple-500"></i>Target Tabungan</h4>
                <a href="{{ route('savings.index') }}" class="text-xs text-emerald-500 hover:underline font-semibold">Kelola</a>
            </div>
            <div class="space-y-4">
                @forelse ($savings as $saving)
                    @php
                        $percent = $saving->target_amount > 0 ? min(100, ($saving->current_amount / $saving->target_amount) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between items-center text-xs mb-1.5">
                            <span class="font-semibold text-slate-700">{{ $saving->name }}</span>
                            <span class="text-[10px] text-slate-400">Rp{{ number_format($saving->current_amount, 0, ',', '.') }} / Rp{{ number_format($saving->target_amount, 0, ',', '.') }}</span>
                        </div>
                        
                        <!-- Progress bar -->
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-purple-500 h-full rounded-full transition duration-500" style="width: {{ $percent }}%"></div>
                        </div>
                        <div class="flex justify-between items-center mt-1 text-[10px]">
                            <span class="text-purple-600 font-semibold">{{ number_format($percent, 1) }}% Terkumpul</span>
                            @if ($saving->target_date)
                                <span class="text-slate-400">Target: {{ $saving->target_date->translatedFormat('d M Y') }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400">
                        <p class="text-xs">Belum ada target tabungan.</p>
                        <a href="{{ route('savings.index') }}" class="text-xs text-emerald-500 hover:underline mt-2 inline-block font-semibold">Set Target</a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Debts Liabilities -->
        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between mb-4 border-b border-slate-50 pb-3">
                <h4 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-hand-holding-dollar mr-2 text-rose-500"></i>Hutang & Piutang</h4>
                <a href="{{ route('debts.index') }}" class="text-xs text-emerald-500 hover:underline font-semibold">Semua</a>
            </div>
            <div class="space-y-3.5">
                @forelse ($debts as $debt)
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs font-bold text-slate-700">{{ $debt->name }}</span>
                                <span class="text-[9px] px-1.5 py-0.5 rounded font-semibold uppercase
                                    @if ($debt->type === 'debt') bg-rose-50 text-rose-600
                                    @else bg-emerald-50 text-emerald-600 @endif">
                                    {{ $debt->type === 'debt' ? 'Hutang' : 'Piutang' }}
                                </span>
                            </div>
                            <span class="text-[10px] text-slate-400 block mt-0.5">{{ $debt->type === 'debt' ? 'Ke: ' : 'Dari: ' }} {{ $debt->lender_receiver }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold block text-slate-800">Rp{{ number_format($debt->remaining_amount, 0, ',', '.') }}</span>
                            @if ($debt->due_date)
                                <span class="text-[9px] text-slate-400">Jatuh Tempo: {{ $debt->due_date->translatedFormat('d/m/y') }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400">
                        <p class="text-xs">Bersih dari hutang & piutang.</p>
                        <a href="{{ route('debts.index') }}" class="text-xs text-emerald-500 hover:underline mt-2 inline-block font-semibold">Catat Hutang</a>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- RECENT TRANSACTIONS TABLE -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-50 pb-4 mb-4">
            <h4 class="font-bold text-slate-800 text-sm md:text-base"><i class="fa-solid fa-clock-rotate-left mr-2 text-emerald-500"></i>5 Transaksi Terakhir</h4>
            <a href="{{ route('transactions.index') }}" class="text-xs text-emerald-500 hover:underline font-semibold">Lihat Semua Riwayat</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-slate-400 text-xs uppercase border-b border-slate-100">
                        <th class="py-3 font-semibold">Tanggal</th>
                        <th class="py-3 font-semibold">Nama</th>
                        <th class="py-3 font-semibold">Kategori</th>
                        <th class="py-3 font-semibold">Dompet</th>
                        <th class="py-3 font-semibold text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($recentTransactions as $tx)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 text-xs text-slate-550">{{ $tx->date->translatedFormat('d M Y') }}</td>
                            <td class="py-3.5">
                                <div class="font-medium text-slate-700 text-xs md:text-sm">{{ $tx->name }}</div>
                                @if ($tx->notes)
                                    <span class="text-[10px] text-slate-400 block mt-0.5 truncate max-w-xs">{{ $tx->notes }}</span>
                                @endif
                            </td>
                            <td class="py-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold flex items-center gap-1.5 w-fit" 
                                      style="background-color: {{ $tx->category->color }}12; color: {{ $tx->category->color }}">
                                    <i class="fa-solid fa-{{ $tx->category->icon }} text-[9px]"></i>
                                    <span>{{ $tx->category->name }}</span>
                                </span>
                            </td>
                            <td class="py-3.5 text-xs text-slate-600 font-medium">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-credit-card text-[10px] text-slate-400"></i>
                                    <span>{{ $tx->wallet->name }}</span>
                                </span>
                            </td>
                            <td class="py-3.5 text-right font-bold text-xs md:text-sm
                                @if ($tx->type === 'income') text-emerald-600 @else text-rose-600 @endif">
                                {{ $tx->type === 'income' ? '+' : '-' }}Rp{{ number_format($tx->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                                <i class="fa-solid fa-receipt text-3xl text-slate-200 mb-2 block"></i>
                                Belum ada riwayat transaksi. Mulai catat sekarang!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- CHARTS LOGIC -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        
        // 1. Daily Expense Line Chart
        const ctxDaily = document.getElementById('dailyExpenseChart').getContext('2d');
        const dailyLabels = {!! json_encode($last7Days) !!};
        const dailyData = {!! json_encode($last7DaysExpenseData) !!};

        new Chart(ctxDaily, {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Pengeluaran (Rp)',
                    data: dailyData,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#ef4444',
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp' + value.toLocaleString('id-ID');
                            },
                            font: { size: 10 }
                        },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });

        // 2. Category Pie Chart
        @if (!$categoryExpenses->isEmpty())
            const ctxCat = document.getElementById('categoryExpenseChart').getContext('2d');
            const catLabels = [
                @foreach ($categoryExpenses as $ce)
                    "{{ $ce->category->name }}",
                @endforeach
            ];
            const catData = [
                @foreach ($categoryExpenses as $ce)
                    {{ $ce->total }},
                @endforeach
            ];
            const catColors = [
                @foreach ($categoryExpenses as $ce)
                    "{{ $ce->category->color }}",
                @endforeach
            ];

            new Chart(ctxCat, {
                type: 'doughnut',
                data: {
                    labels: catLabels,
                    datasets: [{
                        data: catData,
                        backgroundColor: catColors,
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                font: { size: 10 }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        @endif

    });
</script>
@endsection
