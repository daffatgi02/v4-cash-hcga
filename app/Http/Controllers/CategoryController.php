<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:pemasukan,pengeluaran',
        ]);

        Category::create($validatedData);
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dibuat.');
    }

    public function show(Category $category)
    {
        $transactions = $category->transactions()->with(['fund', 'rkb'])->orderBy('transaction_date', 'desc')->get();
        return view('categories.show', compact('category', 'transactions'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:pemasukan,pengeluaran',
        ]);

        $category->update($validatedData);
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        // Cek apakah kategori memiliki transaksi
        if ($category->transactions()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Kategori tidak dapat dihapus karena masih memiliki transaksi.');
        }

        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
