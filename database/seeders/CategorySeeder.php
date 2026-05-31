<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Expenses (Pengeluaran)
            [
                'user_id' => null,
                'name' => 'Makan & Minum',
                'type' => 'expense',
                'icon' => 'utensils',
                'color' => '#f97316', // orange
            ],
            [
                'user_id' => null,
                'name' => 'Transportasi & Bensin',
                'type' => 'expense',
                'icon' => 'car',
                'color' => '#3b82f6', // blue
            ],
            [
                'user_id' => null,
                'name' => 'Jajan & Hiburan',
                'type' => 'expense',
                'icon' => 'gamepad',
                'color' => '#8b5cf6', // purple
            ],
            [
                'user_id' => null,
                'name' => 'Tagihan & Bulanan',
                'type' => 'expense',
                'icon' => 'file-invoice-dollar',
                'color' => '#ef4444', // red
            ],
            [
                'user_id' => null,
                'name' => 'Belanja & Groceries',
                'type' => 'expense',
                'icon' => 'shopping-cart',
                'color' => '#ec4899', // pink
            ],
            [
                'user_id' => null,
                'name' => 'Kesehatan',
                'type' => 'expense',
                'icon' => 'heartbeat',
                'color' => '#10b981', // green/emerald
            ],
            [
                'user_id' => null,
                'name' => 'Pendidikan',
                'type' => 'expense',
                'icon' => 'graduation-cap',
                'color' => '#06b6d4', // cyan
            ],
            [
                'user_id' => null,
                'name' => 'Lain-lain',
                'type' => 'expense',
                'icon' => 'ellipsis-h',
                'color' => '#6b7280', // gray
            ],

            // Incomes (Pemasukan)
            [
                'user_id' => null,
                'name' => 'Gaji Bulanan',
                'type' => 'income',
                'icon' => 'money-bill-wave',
                'color' => '#059669', // dark green
            ],
            [
                'user_id' => null,
                'name' => 'Bonus & THR',
                'type' => 'income',
                'icon' => 'gift',
                'color' => '#0d9488', // dark teal
            ],
            [
                'user_id' => null,
                'name' => 'Investasi & Dividen',
                'type' => 'income',
                'icon' => 'chart-line',
                'color' => '#4f46e5', // indigo
            ],
            [
                'user_id' => null,
                'name' => 'Sampingan / Freelance',
                'type' => 'income',
                'icon' => 'laptop-code',
                'color' => '#0891b2', // cyan
            ],
            [
                'user_id' => null,
                'name' => 'Uang Saku / Pemberian',
                'type' => 'income',
                'icon' => 'hand-holding-usd',
                'color' => '#b45309', // amber
            ],
            [
                'user_id' => null,
                'name' => 'Lain-lain',
                'type' => 'income',
                'icon' => 'ellipsis-h',
                'color' => '#6b7280', // gray
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
