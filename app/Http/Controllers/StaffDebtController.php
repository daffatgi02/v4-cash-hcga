<?php
namespace App\Http\Controllers;

use App\Models\StaffDebt;
use App\Models\Transaction;
use App\Models\Fund;
use App\Models\Category;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StaffDebtController extends Controller
{
    public function index()
    {
        $staffDebts = StaffDebt::with('staff')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('staff_debts.index', compact('staffDebts'));
    }

    public function show(StaffDebt $staffDebt)
    {
        $staffDebt->load(['staff', 'transaction', 'files']);
        return view('staff_debts.show', compact('staffDebt'));
    }

    public function edit(StaffDebt $staffDebt)
    {
        $funds = Fund::all();
        $categories = Category::where('type', 'pengeluaran')->get();

        return view('staff_debts.edit', compact('staffDebt', 'funds', 'categories'));
    }

    // Metode untuk mencatat realisasi pembelian dari piutang staff
    public function recordPurchase(Request $request, StaffDebt $staffDebt)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'rkb_id' => 'nullable|exists:rkbs,id',
            'transaction_date' => 'required|date',
            'description' => 'required|string',
            'recipient_name' => 'nullable|string',
            'recipient_type' => 'nullable|in:vendor,staff,finance',
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Validasi tambahan: jumlah realisasi tidak boleh melebihi sisa dana piutang
        $remainingDebt = $staffDebt->given_amount - $staffDebt->used_amount - $staffDebt->returned_amount;
        if ($validatedData['amount'] > $remainingDebt) {
            return redirect()->back()->with('error', 'Jumlah realisasi tidak boleh melebihi sisa piutang ('.$remainingDebt.')')->withInput();
        }

        DB::beginTransaction();

        try {
            // Ambil transaksi awal
            $originalTransaction = $staffDebt->transaction;

            // Catat transaksi realisasi
            $transaction = Transaction::create([
                'fund_id' => $originalTransaction->fund_id,
                'category_id' => $validatedData['category_id'],
                'rkb_id' => $validatedData['rkb_id'],
                'staff_id' => $staffDebt->staff_id,
                'type' => 'pengeluaran',
                'amount' => $validatedData['amount'],
                'transaction_date' => $validatedData['transaction_date'],
                'description' => $validatedData['description'],
                'recipient_name' => $validatedData['recipient_name'],
                'recipient_type' => $validatedData['recipient_type'],
            ]);

            // Upload files if any
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('uploads/transactions', $filename, 'public');

                    File::create([
                        'fileable_id' => $transaction->id,
                        'fileable_type' => Transaction::class,
                        'filename' => $filename,
                        'original_filename' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => 'bukti_pembelian',
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            // Jika pengeluaran terkait RKB, update used_amount di RKB
            if ($transaction->rkb_id) {
                $rkb = Rkb::find($transaction->rkb_id);
                $rkb->used_amount += $transaction->amount;
                $rkb->save();
            }

            // Update piutang staff
            $staffDebt->used_amount += $validatedData['amount'];
            $staffDebt->updateStatus();

            DB::commit();

            return redirect()->route('staff_debts.show', $staffDebt)->with('success', 'Realisasi pembelian berhasil dicatat.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    // Metode untuk mencatat pengembalian sisa piutang
    public function recordReturn(Request $request, StaffDebt $staffDebt)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'required|string',
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Validasi tambahan: jumlah pengembalian tidak boleh melebihi sisa dana piutang
        $remainingDebt = $staffDebt->given_amount - $staffDebt->used_amount - $staffDebt->returned_amount;
        if ($validatedData['amount'] > $remainingDebt) {
            return redirect()->back()->with('error', 'Jumlah pengembalian tidak boleh melebihi sisa piutang ('.$remainingDebt.')')->withInput();
        }

        DB::beginTransaction();

        try {
            // Ambil transaksi awal
            $originalTransaction = $staffDebt->transaction;

            // Catat transaksi pengembalian (sebagai pemasukan)
            $transaction = Transaction::create([
                'fund_id' => $originalTransaction->fund_id,
                'category_id' => Category::where('name', 'Pengembalian Piutang Staff')->first()->id,
                'staff_id' => $staffDebt->staff_id,
                'type' => 'pemasukan',
                'amount' => $validatedData['amount'],
                'transaction_date' => $validatedData['transaction_date'],
                'description' => $validatedData['description'],
                'recipient_type' => 'staff',
                'recipient_name' => $staffDebt->staff->name,
            ]);

            // Upload files if any
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('uploads/transactions', $filename, 'public');

                    File::create([
                        'fileable_id' => $transaction->id,
                        'fileable_type' => Transaction::class,
                        'filename' => $filename,
                        'original_filename' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => 'bukti_pengembalian',
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            // Update piutang staff
            $staffDebt->returned_amount += $validatedData['amount'];
            $staffDebt->updateStatus();

            DB::commit();

            return redirect()->route('staff_debts.show', $staffDebt)->with('success', 'Pengembalian piutang berhasil dicatat.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    // Metode untuk menghapus file bukti
    public function removeFile(Request $request, File $file)
    {
        if ($file->fileable_type == StaffDebt::class) {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();

            return redirect()->back()->with('success', 'File berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'File tidak dapat dihapus.');
    }
}
