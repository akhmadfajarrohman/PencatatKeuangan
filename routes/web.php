<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\SavingsController;
use Illuminate\Support\Facades\Route;

// Redirect welcome page to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Guest routes (Authentication)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard & Reports
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/laporan', [DashboardController::class, 'laporan'])->name('laporan');

    // Profile Edit
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile.edit');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Wallets CRUD
    Route::get('/wallets', [WalletController::class, 'index'])->name('wallets.index');
    Route::post('/wallets', [WalletController::class, 'store'])->name('wallets.store');
    Route::put('/wallets/{wallet}', [WalletController::class, 'update'])->name('wallets.update');
    Route::delete('/wallets/{wallet}', [WalletController::class, 'destroy'])->name('wallets.destroy');

    // Categories CRUD
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Transactions CRUD
    Route::resource('transactions', TransactionController::class)->except(['show']);

    // Budgets Limit
    Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
    Route::post('/budgets', [BudgetController::class, 'storeOrUpdate'])->name('budgets.store');

    // Debts & Cicilan
    Route::get('/debts', [DebtController::class, 'index'])->name('debts.index');
    Route::post('/debts', [DebtController::class, 'store'])->name('debts.store');
    Route::post('/debts/{debt}/payment', [DebtController::class, 'storePayment'])->name('debts.payment');
    Route::delete('/debts/{debt}', [DebtController::class, 'destroy'])->name('debts.destroy');

    // Savings Target
    Route::get('/savings', [SavingsController::class, 'index'])->name('savings.index');
    Route::post('/savings', [SavingsController::class, 'store'])->name('savings.store');
    Route::post('/savings/{target}/transaction', [SavingsController::class, 'storeTransaction'])->name('savings.transaction');
    Route::delete('/savings/{target}', [SavingsController::class, 'destroy'])->name('savings.destroy');
});
