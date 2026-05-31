<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\SavingsTarget;
use App\Models\Debt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // 1. Wallets
        $wallets = $user->wallets;
        $totalBalance = $wallets->sum('balance');

        // 2. Monthly Stats (Pemasukan / Pengeluaran Bulan Ini)
        $currentMonth = $today->month;
        $currentYear = $today->year;

        $monthlyIncome = $user->transactions()
            ->where('type', 'income')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        $monthlyExpense = $user->transactions()
            ->where('type', 'expense')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        // 3. Today's Expense
        $todayExpense = $user->transactions()
            ->where('type', 'expense')
            ->whereDate('date', $today)
            ->sum('amount');

        // 4. Payday / Sisa Hari ke Gajian & Batas Aman
        $paydayDay = $user->payday_day;
        
        // Find next payday
        $thisMonthPayday = Carbon::create($today->year, $today->month, 1)
            ->setDay(min($paydayDay, Carbon::create($today->year, $today->month, 1)->daysInMonth));

        if ($today->lt($thisMonthPayday)) {
            $nextPayday = $thisMonthPayday;
        } else {
            $nextMonth = $today->copy()->addMonth();
            $nextPayday = Carbon::create($nextMonth->year, $nextMonth->month, 1)
                ->setDay(min($paydayDay, Carbon::create($nextMonth->year, $nextMonth->month, 1)->daysInMonth));
        }

        $daysRemaining = $today->diffInDays($nextPayday);
        
        // Sisa hari = 0 berarti hari ini gajian, kita hitung 1 hari biar tidak division by zero
        $daysForDivision = $daysRemaining > 0 ? $daysRemaining : 1;
        $safeDailyLimit = $totalBalance > 0 ? ($totalBalance / $daysForDivision) : 0;
        
        $isOverlimit = $todayExpense > $safeDailyLimit;

        // 5. Recent 5 Transactions
        $recentTransactions = $user->transactions()
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        // 6. Savings progress (top 3)
        $savings = $user->savingsTargets()->take(3)->get();

        // 7. Debts summary (top 3 pending)
        $debts = $user->debts()->where('status', 'pending')->take(3)->get();

        // 8. Chart Data for Dashboard
        // Pie Chart: Pengeluaran per Kategori Bulan Ini
        $categoryExpenses = $user->transactions()
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->where('type', 'expense')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->groupBy('category_id')
            ->with('category')
            ->get();

        // Line Chart: Pengeluaran 7 hari terakhir
        $last7Days = [];
        $last7DaysExpenseData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $last7Days[] = $date->translatedFormat('d M');
            $last7DaysExpenseData[] = $user->transactions()
                ->where('type', 'expense')
                ->whereDate('date', $date)
                ->sum('amount');
        }

        return view('dashboard', compact(
            'wallets',
            'totalBalance',
            'monthlyIncome',
            'monthlyExpense',
            'todayExpense',
            'daysRemaining',
            'nextPayday',
            'safeDailyLimit',
            'isOverlimit',
            'recentTransactions',
            'savings',
            'debts',
            'categoryExpenses',
            'last7Days',
            'last7DaysExpenseData'
        ));
    }

    public function laporan(Request $request)
    {
        $user = Auth::user();
        $month = $request->input('month', Carbon::today()->month);
        $year = $request->input('year', Carbon::today()->year);

        $dateString = Carbon::create($year, $month, 1)->translatedFormat('F Y');

        // Stats
        $monthlyIncome = $user->transactions()
            ->where('type', 'income')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');

        $monthlyExpense = $user->transactions()
            ->where('type', 'expense')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');

        // Rata-rata harian
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        // Jika bulan ini adalah bulan sekarang, rata-rata dihitung berdasarkan hari yang sudah berlalu
        if ($month == Carbon::today()->month && $year == Carbon::today()->year) {
            $daysToCount = Carbon::today()->day;
        } else {
            $daysToCount = $daysInMonth;
        }
        $averageDaily = $monthlyExpense / ($daysToCount > 0 ? $daysToCount : 1);

        // Kategori Terboros
        $topCategory = $user->transactions()
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->where('type', 'expense')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->groupBy('category_id')
            ->with('category')
            ->orderBy('total', 'desc')
            ->first();

        // Hari Paling Boros
        $topExpenseDay = $user->transactions()
            ->select('date', DB::raw('SUM(amount) as total'))
            ->where('type', 'expense')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->groupBy('date')
            ->orderBy('total', 'desc')
            ->first();

        // Data Grafik Kategori
        $categoryExpenses = $user->transactions()
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->where('type', 'expense')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->groupBy('category_id')
            ->with('category')
            ->get();

        // Data Grafik Pemasukan vs Pengeluaran 6 Bulan Terakhir
        $sixMonthsLabels = [];
        $sixMonthsIncome = [];
        $sixMonthsExpense = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::today()->subMonths($i);
            $sixMonthsLabels[] = $date->translatedFormat('M Y');
            $sixMonthsIncome[] = $user->transactions()
                ->where('type', 'income')
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->sum('amount');
            $sixMonthsExpense[] = $user->transactions()
                ->where('type', 'expense')
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->sum('amount');
        }

        // List semua transaksi untuk detail laporan bulan ini
        $transactions = $user->transactions()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->with(['wallet', 'category'])
            ->get();

        return view('laporan', compact(
            'month',
            'year',
            'dateString',
            'monthlyIncome',
            'monthlyExpense',
            'averageDaily',
            'topCategory',
            'topExpenseDay',
            'categoryExpenses',
            'sixMonthsLabels',
            'sixMonthsIncome',
            'sixMonthsExpense',
            'transactions'
        ));
    }
}
