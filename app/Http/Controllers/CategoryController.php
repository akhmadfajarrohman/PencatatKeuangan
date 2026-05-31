<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        // Get global system categories and the user's custom ones
        $categories = Category::whereNull('user_id')
            ->orWhere('user_id', Auth::id())
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:income,expense'],
            'color' => ['required', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        Auth::user()->categories()->create([
            'name' => $request->name,
            'type' => $request->type,
            'color' => $request->color,
            'icon' => $request->icon ?? 'tags',
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori kustom berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            return redirect()->route('categories.index')->with('error', 'Anda tidak diizinkan mengubah kategori bawaan sistem.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:income,expense'],
            'color' => ['required', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        $category->update([
            'name' => $request->name,
            'type' => $request->type,
            'color' => $request->color,
            'icon' => $request->icon ?? 'tags',
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            return redirect()->route('categories.index')->with('error', 'Kategori bawaan sistem tidak dapat dihapus.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
