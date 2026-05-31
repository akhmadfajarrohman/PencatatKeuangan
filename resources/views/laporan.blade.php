@extends('layouts.app')

@section('title', 'Laporan Bulanan')
@section('page-title', 'Laporan Keuangan Bulanan')

@section('content')
<div class="space-y-6">

    <!-- MONTH & YEAR FILTER HEADER -->
    <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h4 class="font-bold text-slate-800 text-sm md:text-base">Laporan Periode {{ $dateString }}</h4>
            <p class="text-xs text-slate-400">Analisis dan breakdown pemasukan serta pengeluaran bulanan Anda.</p>
        </div>

        <form action="{{ route('laporan') }}" method="GET" class="flex items-center gap-2">
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
                Tampilkan
            </button>
        </form>
    </div>

    <!-- METRICS SUMMARY CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        
        <!-- Pemasukan vs Pengeluaran & Selisih -->
        <div class="bg-white border border-slate-100 p-5 rounded-2xl shadow-sm space-y-4">
            <h5 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Breakdown Arus Kas</h5>
            
            <div class="space-y-2.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500">Total Pemasukan</span>
                    <span class="font-bold text-emerald-600">+Rp{{ number_format($monthlyIncome, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500">Total Pengeluaran</span>
                    <span class="font-bold text-rose-600">-Rp{{ number_format($monthlyExpense, 0, ',', '.') }}</span>
                </div>
                <div class="border-t border-slate-50 pt-2.5 flex items-center justify-between text-sm">
                    <span class="font-semibold text-slate-800">Sisa / Selisih</span>
                    @php $net = $monthlyIncome - $monthlyExpense; @endphp
                    <span class="font-extrabold @if ($net >= 0) text-emerald-600 @else text-rose-600 @endif">
                        {{ $net >= 0 ? '+' : '' }}Rp{{ number_format($net, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Rata-Rata Harian -->
        <div class="bg-white border border-slate-100 p-5 rounded-2xl shadow-sm flex flex-col justify-between">
            <div>
                <h5 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Rata-Rata Pengeluaran Harian</h5>
                <h3 class="text-xl md:text-2xl font-black text-slate-800">Rp{{ number_format($averageDaily, 0, ',', '.') }}</h3>
            </div>
            <p class="text-[10px] text-slate-400 mt-4"><i class="fa-solid fa-info-circle mr-1 text-slate-350"></i>Dihitung berdasarkan jumlah hari belanja di bulan terpilih.</p>
        </div>

        <!-- Kategori Terboros & Hari Paling Boros -->
        <div class="bg-white border border-slate-100 p-5 rounded-2xl shadow-sm space-y-3.5 flex flex-col justify-between">
            <div>
                <h5 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Highlight Terboros</h5>
                
                <div class="space-y-2 mt-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 flex items-center gap-1"><i class="fa-solid fa-tags text-rose-400 text-[10px]"></i>Kategori Terboros</span>
                        <span class="font-bold text-slate-700">
                            @if ($topCategory)
                                {{ $topCategory->category->name }} (Rp{{ number_format($topCategory->total, 0, ',', '.') }})
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 flex items-center gap-1"><i class="fa-solid fa-calendar-day text-rose-400 text-[10px]"></i>Hari Paling Boros</span>
                        <span class="font-bold text-slate-700">
                            @if ($topExpenseDay)
                                {{ $topExpenseDay->date->translatedFormat('d F') }} (Rp{{ number_format($topExpenseDay->total, 0, ',', '.') }})
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- GRAPHICS BREAKDOWN -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Bar Chart: Pemasukan vs Pengeluaran 6 Bulan Terakhir -->
        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm lg:col-span-2">
            <h4 class="font-bold text-slate-800 text-sm md:text-base mb-2">Tren Bulanan (6 Bulan Terakhir)</h4>
            <p class="text-xs text-slate-400 mb-4">Perbandingan pendapatan vs belanja per bulan Anda.</p>
            <div class="h-64 relative">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>

        <!-- Pie Chart: Pengeluaran Per Kategori -->
        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm">
            <h4 class="font-bold text-slate-800 text-sm md:text-base mb-2">Breakdown Pengeluaran</h4>
            <p class="text-xs text-slate-400 mb-4">Alokasi anggaran belanja per kategori bulan ini.</p>
            <div class="h-64 relative flex items-center justify-center">
                @if ($categoryExpenses->isEmpty())
                    <div class="text-center p-6 text-slate-400">
                        <i class="fa-solid fa-chart-pie text-4xl mb-2 text-slate-200"></i>
                        <p class="text-xs">Belum ada data pengeluaran.</p>
                    </div>
                @else
                    <canvas id="categoryReportChart"></canvas>
                @endif
            </div>
        </div>

    </div>

    <!-- MONTH TRANSACTION DETAIL LIST -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
        <h4 class="font-bold text-slate-800 text-sm md:text-base mb-4 border-b border-slate-50 pb-3 flex items-center gap-2">
            <i class="fa-solid fa-receipt text-emerald-500"></i>
            <span>Detail Transaksi Periode {{ $dateString }}</span>
        </h4>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-slate-400 text-xs uppercase border-b border-slate-100">
                        <th class="py-3 font-semibold">Tanggal</th>
                        <th class="py-3 font-semibold">Deskripsi</th>
                        <th class="py-3 font-semibold">Kategori</th>
                        <th class="py-3 font-semibold">Dompet</th>
                        <th class="py-3 font-semibold text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($transactions as $tx)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 text-xs text-slate-500">{{ $tx->date->translatedFormat('d M Y') }}</td>
                            <td class="py-3.5">
                                <span class="font-medium text-slate-700 text-xs md:text-sm">{{ $tx->name }}</span>
                                @if ($tx->notes)
                                    <span class="text-[10px] text-slate-400 block mt-0.5 max-w-xs truncate">{{ $tx->notes }}</span>
                                @endif
                            </td>
                            <td class="py-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold flex items-center gap-1.5 w-fit" 
                                      style="background-color: {{ $tx->category->color }}12; color: {{ $tx->category->color }}">
                                    <i class="fa-solid fa-{{ $tx->category->icon }} text-[9px]"></i>
                                    <span>{{ $tx->category->name }}</span>
                                </span>
                            </td>
                            <td class="py-3.5 text-xs text-slate-600 font-semibold">{{ $tx->wallet->name }}</td>
                            <td class="py-3.5 text-right font-bold text-xs md:text-sm
                                @if ($tx->type === 'income') text-emerald-600 @else text-rose-600 @endif">
                                {{ $tx->type === 'income' ? '+' : '-' }}Rp{{ number_format($tx->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-xs">
                                <i class="fa-solid fa-receipt text-3xl text-slate-200 mb-2 block"></i>
                                Tidak ada transaksi di bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- CHARTS CONFIGURATION -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        
        // 1. Monthly Bar Chart (Pemasukan vs Pengeluaran)
        const ctxTrend = document.getElementById('monthlyTrendChart').getContext('2d');
        const trendLabels = {!! json_encode($sixMonthsLabels) !!};
        const trendIncome = {!! json_encode($sixMonthsIncome) !!};
        const trendExpense = {!! json_encode($sixMonthsExpense) !!};

        new Chart(ctxTrend, {
            type: 'bar',
            data: {
                labels: trendLabels,
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: trendIncome,
                        backgroundColor: '#10b981',
                        borderRadius: 6
                    },
                    {
                        label: 'Pengeluaran',
                        data: trendExpense,
                        backgroundColor: '#ef4444',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 10 } }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp' + value.toLocaleString('id-ID');
                            },
                            font: { size: 9 }
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
            const ctxCat = document.getElementById('categoryReportChart').getContext('2d');
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
