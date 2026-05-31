<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run Category Seeder
        $this->call(CategorySeeder::class);

        // Create Test User
        $user = User::create([
            'name' => 'Budi Santoso',
            'email' => 'user@dompet.com',
            'password' => Hash::make('password'),
            'payday_day' => 25,
        ]);

        // Create Wallets
        $cash = Wallet::create([
            'user_id' => $user->id,
            'name' => 'Uang Cash',
            'type' => 'cash',
            'balance' => 500000.00,
            'notes' => 'Uang fisik di dompet',
        ]);

        $mandiri = Wallet::create([
            'user_id' => $user->id,
            'name' => 'Bank Mandiri',
            'type' => 'bank',
            'balance' => 4500000.00,
            'notes' => 'Rekening gaji utama',
        ]);

        $gopay = Wallet::create([
            'user_id' => $user->id,
            'name' => 'GoPay',
            'type' => 'e-wallet',
            'balance' => 350000.00,
            'notes' => 'Dompet digital harian',
        ]);

        // Find Categories
        $catMakan = Category::where('name', 'Makan & Minum')->first();
        $catBensin = Category::where('name', 'Transportasi & Bensin')->first();
        $catJajan = Category::where('name', 'Jajan & Hiburan')->first();
        $catBelanja = Category::where('name', 'Belanja & Groceries')->first();
        $catGaji = Category::where('name', 'Gaji Bulanan')->first();
        $catFreelance = Category::where('name', 'Sampingan / Freelance')->first();

        // Seed Transactions
        // 1. Income (Gaji)
        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $mandiri->id,
            'category_id' => $catGaji->id,
            'date' => Carbon::now()->subDays(6)->toDateString(),
            'type' => 'income',
            'name' => 'Gaji Bulan Ini',
            'amount' => 5000000.00,
            'notes' => 'Alhamdulillah gajian',
        ]);

        // 2. Income (Freelance)
        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $gopay->id,
            'category_id' => $catFreelance->id,
            'date' => Carbon::now()->subDays(2)->toDateString(),
            'type' => 'income',
            'name' => 'Desain Logo Client',
            'amount' => 400000.00,
            'notes' => 'Project sampingan logo Cafe',
        ]);

        // 3. Expense (Makan Siang)
        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $cash->id,
            'category_id' => $catMakan->id,
            'date' => Carbon::now()->toDateString(),
            'type' => 'expense',
            'name' => 'Nasi Padang Ampera',
            'amount' => 25000.00,
            'notes' => 'Lauk ayam bakar + es teh manis',
        ]);

        // 4. Expense (Bensin)
        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $cash->id,
            'category_id' => $catBensin->id,
            'date' => Carbon::now()->toDateString(),
            'type' => 'expense',
            'name' => 'Pertamax Motor',
            'amount' => 30000.00,
        ]);

        // 5. Expense (Kopi Jajan)
        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $gopay->id,
            'category_id' => $catJajan->id,
            'date' => Carbon::now()->subDay()->toDateString(),
            'type' => 'expense',
            'name' => 'Kopi Susu Gula Aren',
            'amount' => 22000.00,
        ]);

        // 6. Expense (Belanja Bulanan)
        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $mandiri->id,
            'category_id' => $catBelanja->id,
            'date' => Carbon::now()->subDays(4)->toDateString(),
            'type' => 'expense',
            'name' => 'Belanja Supermarket',
            'amount' => 450000.00,
            'notes' => 'Sabun, sampo, beras, minyak, dll',
        ]);
    }
}
