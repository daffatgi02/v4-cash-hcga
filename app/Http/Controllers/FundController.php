<?php
namespace App\Http\Controllers;

use App\Models\Fund;
use Illuminate\Http\Request;

class FundController extends Controller
{
    public function index()
    {
        $funds = Fund::all();
        return view('funds.index', compact('funds'));
    }

    public function create()
    {
        return view('funds.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:rekening_utama,kas_kecil,dana_operasional,dana_rkb',
            'initial_balance' => 'required|numeric|min:0',
        ]);

        Fund::create($validatedData);
        return redirect()->route('funds.index')->with('success', 'Dana berhasil dibuat.');
    }

    public function show(Fund $fund)
    {
        $transactions = $fund->transactions()->with(['category', 'rkb'])->orderBy('transaction_date', 'desc')->get();
        $currentBalance = $fund->getCurrentBalance();

        return view('funds.show', compact('fund', 'transactions', 'currentBalance'));
    }

    public function edit(Fund $fund)
    {
        return view('funds.edit', compact('fund'));
    }

    public function update(Request $request, Fund $fund)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:rekening_utama,kas_kecil,dana_operasional,dana_rkb',
            'initial_balance' => 'required|numeric|min:0',
        ]);

        $fund->update($validatedData);
        return redirect()->route('funds.index')->with('success', 'Dana berhasil diperbarui.');
    }

    public function destroy(Fund $fund)
    {
        // Cek apakah dana memiliki transaksi
        if ($fund->transactions()->count() > 0) {
            return redirect()->route('funds.index')->with('error', 'Dana tidak dapat dihapus karena masih memiliki transaksi.');
        }

        $fund->delete();
        return redirect()->route('funds.index')->with('success', 'Dana berhasil dihapus.');
    }
}
