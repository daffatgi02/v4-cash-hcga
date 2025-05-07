<?php
namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::all();
        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'bank_account' => 'nullable|string',
        ]);

        Staff::create($validatedData);
        return redirect()->route('staff.index')->with('success', 'Staff berhasil dibuat.');
    }

    public function show(Staff $staff)
    {
        $debts = $staff->debts()->with('transaction')->orderBy('created_at', 'desc')->get();
        $transactions = $staff->transactions()->with(['fund', 'category', 'rkb'])->orderBy('transaction_date', 'desc')->get();
        $totalDebt = $staff->getTotalDebt();

        return view('staff.show', compact('staff', 'debts', 'transactions', 'totalDebt'));
    }

    public function edit(Staff $staff)
    {
        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'bank_account' => 'nullable|string',
        ]);

        $staff->update($validatedData);
        return redirect()->route('staff.index')->with('success', 'Staff berhasil diperbarui.');
    }

    public function destroy(Staff $staff)
    {
        // Cek apakah staff memiliki transaksi atau piutang
        if ($staff->transactions()->count() > 0 || $staff->debts()->count() > 0) {
            return redirect()->route('staff.index')->with('error', 'Staff tidak dapat dihapus karena masih memiliki transaksi atau piutang.');
        }

        $staff->delete();
        return redirect()->route('staff.index')->with('success', 'Staff berhasil dihapus.');
    }
}
