<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fund;
use App\Models\StaffDebt;
use App\Models\Transaction;
use App\Models\Rkb;
use App\Models\TemporaryFund;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Mendapatkan saldo rekening HCGA
        $rekeningUtama = Fund::where('type', 'rekening_utama')->first();
        $rekeningBalance = $rekeningUtama ? $rekeningUtama->getCurrentBalance() : 0;

        // Mendapatkan saldo kas kecil
        $kasKecil = Fund::where('type', 'kas_kecil')->first();
        $kasKecilBalance = $kasKecil ? $kasKecil->getCurrentBalance() : 0;

        // Mendapatkan total piutang staff
        $totalStaffDebt = StaffDebt::whereIn('status', ['outstanding', 'partial'])
            ->sum(DB::raw('given_amount - used_amount - returned_amount'));

        // Total dana aktif
        $totalActiveFund = $rekeningBalance + $kasKecilBalance - $totalStaffDebt;

        // Tabel piutang staff
        $staffDebts = StaffDebt::with('staff')
            ->whereIn('status', ['outstanding', 'partial'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Riwayat transaksi terbaru
        $recentTransactions = Transaction::with(['fund', 'category', 'rkb'])
            ->orderBy('transaction_date', 'desc')
            ->take(10)
            ->get();

        // Daftar RKB dan sisa dana
        $rkbs = Rkb::orderBy('request_date', 'desc')->get();

        // Daftar talangan
        $temporaryFunds = TemporaryFund::with(['sourceFund', 'targetFund', 'rkb'])
            ->where('status', 'outstanding')
            ->orderBy('loan_date', 'desc')
            ->get();

        return view('dashboard', compact(
            'rekeningBalance',
            'kasKecilBalance',
            'totalStaffDebt',
            'totalActiveFund',
            'staffDebts',
            'recentTransactions',
            'rkbs',
            'temporaryFunds'
        ));
    }
}
