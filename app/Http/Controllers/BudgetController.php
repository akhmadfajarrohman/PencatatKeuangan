<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $month = $request->input('month', Carbon::today()->month);
        $year = $request->input('year', Carbon::today()->year);

        // Get expense categories
        $categories = Category::where('type', 'expense')
            ->where(function($query) use ($user) {
                $query->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->orderBy('name')
            ->get();

        // Get existing budgets for the selected month/year
        $budgets = $user->budgets()
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->keyBy('category_id');

        // Calculate actual spending for each category in that month/year
        $spending = $user->transactions()
            ->where('type', 'expense')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->groupBy('category_id')
            ->selectRaw('category_id, SUM(amount) as total')
            ->get()
            ->keyBy('category_id');

        $budgetData = [];
        foreach ($categories as $category) {
            $budgetLimit = isset($budgets[$category->id]) ? $budgets[$category->id]->amount : 0;
            $actualSpent = isset($spending[$category->id]) ? $spending[$category->id]->total : 0;
            
            $percentage = 0;
            if ($budgetLimit > 0) {
                $percentage = ($actualSpent / $budgetLimit) * 100;
            }

            $budgetData[] = (object) [
                'category' => $category,
                'limit' => $budgetLimit,
                'spent' => $actualSpent,
                'percentage' => $percentage,
                'budget_id' => isset($budgets[$category->id]) ? $budgets[$category->id]->id : null
            ];
        }

        return view('budgets.index', compact('budgetData', 'month', 'year'));
    }

    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $user = Auth::user();

        // Check if category belongs to user or is global
        $category = Category::where('id', $request->category_id)
            ->where(function($query) use ($user) {
                $query->whereNull('user_id')->orWhere('user_id', $user->id);
            })->firstOrFail();

        // Check if amount is 0, which means they want to remove the budget
        if ($request->amount == 0) {
            $user->budgets()
                ->where('category_id', $request->category_id)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->delete();

            return redirect()->back()->with('success', 'Anggaran berhasil dihapus.');
        }

        // Store or update
        $user->budgets()->updateOrCreate(
            [
                'category_id' => $request->category_id,
                'month' => $request->month,
                'year' => $request->year,
            ],
            [
                'amount' => $request->amount,
            ]
        );

        return redirect()->back()->with('success', 'Anggaran berhasil disimpan.');
    }
}
