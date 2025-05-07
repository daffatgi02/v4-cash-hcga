<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Fund;
use App\Models\Category;
use App\Models\Rkb;
use App\Models\Staff;
use App\Models\StaffDebt;
use App\Models\TemporaryFund;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['fund', 'category', 'rkb', 'staff'])
            ->orderBy('transaction_date', 'desc')
            ->paginate(20);

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $funds = Fund::all();
        $categories = Category::all();
        $rkbs = Rkb::whereIn('status', ['partial', 'full'])->get();
        $staff = Staff::all();

        return view('transactions.create', compact('funds', 'categories', 'rkbs', 'staff'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'fund_id' => 'required|exists:funds,id',
            'category_id' => 'required|exists:categories,id',
            'rkb_id' => 'nullable|exists:rkbs,id',
            'staff_id' => 'nullable|exists:staff,id',
            'type' => 'required|in:pemasukan,pengeluaran',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'required|string',
            'recipient_name' => 'nullable|string',
            'recipient_type' => 'nullable|in:vendor,staff,finance',
            'is_settlement' => 'nullable|boolean',
            'temporary_fund_id' => 'nullable|exists:temporary_funds,id',
            'create_staff_debt' => 'nullable|boolean',
            'purchase_details' => 'nullable|string',
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // Buat transaksi
            $transaction = Transaction::create($validatedData);

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
                        'file_type' => 'bukti_transaksi',
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            // Jika pengeluaran terkait RKB, update used_amount di RKB
            if ($transaction->type == 'pengeluaran' && $transaction->rkb_id) {
                $rkb = Rkb::find($transaction->rkb_id);
                $rkb->used_amount += $transaction->amount;
                $rkb->save();
            }

            // Jika transaksi untuk pengembalian dana talangan
            if ($transaction->is_settlement && $transaction->temporary_fund_id) {
                $tempFund = TemporaryFund::find($transaction->temporary_fund_id);
                $tempFund->status = 'settled';
                $tempFund->settlement_date = $transaction->transaction_date;
                $tempFund->save();
            }

            // Jika transaksi membuat piutang staff
            if ($request->has('create_staff_debt') && $request->create_staff_debt && $transaction->staff_id) {
                StaffDebt::create([
                    'staff_id' => $transaction->staff_id,
                    'transaction_id' => $transaction->id,
                    'given_amount' => $transaction->amount,
                    'purchase_details' => $request->purchase_details,
                    'status' => 'outstanding',
                    'type' => $transaction->fund->type == 'kas_kecil' ? 'cash' : 'transfer',
                ]);
            }

            DB::commit();

            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['fund', 'category', 'rkb', 'staff', 'temporaryFund', 'staffDebt', 'files']);
        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $funds = Fund::all();
        $categories = Category::all();
        $rkbs = Rkb::whereIn('status', ['partial', 'full'])->get();
        $staff = Staff::all();

        return view('transactions.edit', compact('transaction', 'funds', 'categories', 'rkbs', 'staff'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validatedData = $request->validate([
            'fund_id' => 'required|exists:funds,id',
            'category_id' => 'required|exists:categories,id',
            'rkb_id' => 'nullable|exists:rkbs,id',
            'staff_id' => 'nullable|exists:staff,id',
            'type' => 'required|in:pemasukan,pengeluaran',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'required|string',
            'recipient_name' => 'nullable|string',
            'recipient_type' => 'nullable|in:vendor,staff,finance',
            'is_settlement' => 'nullable|boolean',
            'temporary_fund_id' => 'nullable|exists:temporary_funds,id',
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // Simpan nilai lama untuk penghitungan kembali
            $oldAmount = $transaction->amount;
            $oldRkbId = $transaction->rkb_id;

            // Update transaksi
            $transaction->update($validatedData);

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
                        'file_type' => 'bukti_transaksi',
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            // Jika transaksi pengeluaran terkait RKB, update RKB
            if ($transaction->type == 'pengeluaran') {
                // Jika RKB berubah atau dihapus, kembalikan used_amount RKB lama
                if ($oldRkbId && $oldRkbId != $transaction->rkb_id) {
                    $oldRkb = Rkb::find($oldRkbId);
                    $oldRkb->used_amount -= $oldAmount;
                    $oldRkb->save();
                }

                // Update used_amount RKB baru
                if ($transaction->rkb_id) {
                    $rkb = Rkb::find($transaction->rkb_id);
                    if ($oldRkbId == $transaction->rkb_id) {
                        // RKB sama, sesuaikan selisih
                        $rkb->used_amount = $rkb->used_amount - $oldAmount + $transaction->amount;
                    } else {
                        // RKB baru
                        $rkb->used_amount += $transaction->amount;
                    }
                    $rkb->save();
                }
            }

            // Jika transaksi untuk pengembalian dana talangan
            if ($transaction->is_settlement && $transaction->temporary_fund_id) {
                $tempFund = TemporaryFund::find($transaction->temporary_fund_id);
                $tempFund->status = 'settled';
                $tempFund->settlement_date = $transaction->transaction_date;
                $tempFund->save();
            }

            // Jika terkait piutang staff, update
            if ($transaction->staffDebt) {
                $staffDebt = $transaction->staffDebt;
                $staffDebt->given_amount = $transaction->amount;
                $staffDebt->save();
                $staffDebt->updateStatus();
            }

            DB::commit();

            return redirect()->route('transactions.show', $transaction)->with('success', 'Transaksi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Transaction $transaction)
    {
        DB::beginTransaction();

        try {
            // Jika transaksi pengeluaran terkait RKB, kembalikan used_amount RKB
            if ($transaction->type == 'pengeluaran' && $transaction->rkb_id) {
                $rkb = Rkb::find($transaction->rkb_id);
                $rkb->used_amount -= $transaction->amount;
                $rkb->save();
            }

            // Jika terkait piutang staff, cek apakah bisa dihapus
            if ($transaction->staffDebt) {
                if ($transaction->staffDebt->used_amount > 0 || $transaction->staffDebt->returned_amount > 0) {
                    return redirect()->back()->with('error', 'Transaksi tidak dapat dihapus karena sudah ada realisasi atau pengembalian piutang staff.');
                }

                $transaction->staffDebt->delete();
            }

            // Hapus file-file terkait
            foreach ($transaction->files as $file) {
                Storage::disk('public')->delete($file->file_path);
                $file->delete();
            }

            $transaction->delete();

            DB::commit();

            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
