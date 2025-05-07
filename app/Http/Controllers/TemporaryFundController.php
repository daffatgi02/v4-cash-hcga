<?php

namespace App\Http\Controllers;

use App\Models\TemporaryFund;
use App\Models\Fund;
use App\Models\Rkb;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TemporaryFundController extends Controller
{
    public function index()
    {
        $temporaryFunds = TemporaryFund::with(['sourceFund', 'targetFund', 'rkb'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('temporary_funds.index', compact('temporaryFunds'));
    }

    public function create()
    {
        $funds = Fund::all();
        $rkbs = Rkb::whereIn('status', ['pending', 'partial'])->get();

        return view('temporary_funds.create', compact('funds', 'rkbs'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'source_fund_id' => 'required|exists:funds,id',
            'target_fund_id' => 'required|exists:funds,id',
            'rkb_id' => 'nullable|exists:rkbs,id',
            'amount' => 'required|numeric|min:0',
            'loan_date' => 'required|date',
            'description' => 'required|string',
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Validasi tambahan: sumber dan target dana tidak boleh sama
        if ($validatedData['source_fund_id'] == $validatedData['target_fund_id']) {
            return redirect()->back()->with('error', 'Sumber dan target dana tidak boleh sama.')->withInput();
        }

        // Validasi saldo sumber dana cukup
        $sourceFund = Fund::find($validatedData['source_fund_id']);
        if ($sourceFund->getCurrentBalance() < $validatedData['amount']) {
            return redirect()->back()->with('error', 'Saldo sumber dana tidak mencukupi.')->withInput();
        }

        DB::beginTransaction();

        try {
            // Buat data talangan
            $temporaryFund = TemporaryFund::create($validatedData);

            // Buat transaksi pengeluaran dari sumber dana
            $transactionOut = Transaction::create([
                'fund_id' => $validatedData['source_fund_id'],
                'category_id' => Category::where('name', 'Dana Talangan')->first()->id,
                'rkb_id' => $validatedData['rkb_id'],
                'type' => 'pengeluaran',
                'amount' => $validatedData['amount'],
                'transaction_date' => $validatedData['loan_date'],
                'description' => 'Talangan untuk ' . ($temporaryFund->rkb ? $temporaryFund->rkb->title : $temporaryFund->targetFund->name),
                'temporary_fund_id' => $temporaryFund->id,
            ]);

            // Buat transaksi pemasukan ke target dana
            $transactionIn = Transaction::create([
                'fund_id' => $validatedData['target_fund_id'],
                'category_id' => Category::where('name', 'Dana Talangan')->first()->id,
                'rkb_id' => $validatedData['rkb_id'],
                'type' => 'pemasukan',
                'amount' => $validatedData['amount'],
                'transaction_date' => $validatedData['loan_date'],
                'description' => 'Talangan dari ' . $temporaryFund->sourceFund->name,
                'temporary_fund_id' => $temporaryFund->id,
            ]);

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('uploads/temporary_funds', $filename, 'public');

                    File::create([
                        'fileable_id' => $temporaryFund->id,
                        'fileable_type' => TemporaryFund::class,
                        'filename' => $filename,
                        'original_filename' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => 'document',
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('temporary_funds.index')->with('success', 'Dana talangan berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(TemporaryFund $temporaryFund)
    {
        $temporaryFund->load(['sourceFund', 'targetFund', 'rkb', 'files']);
        $transactions = Transaction::where('temporary_fund_id', $temporaryFund->id)->get();

        return view('temporary_funds.show', compact('temporaryFund', 'transactions'));
    }

    public function settle(Request $request, TemporaryFund $temporaryFund)
    {
        $validatedData = $request->validate([
            'settlement_date' => 'required|date',
            'description' => 'required|string',
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Validasi: talangan belum lunas
        if ($temporaryFund->status == 'settled') {
            return redirect()->back()->with('error', 'Dana talangan sudah lunas.');
        }

        DB::beginTransaction();

        try {
            // Buat transaksi pengeluaran dari target dana
            $transactionOut = Transaction::create([
                'fund_id' => $temporaryFund->target_fund_id,
                'category_id' => Category::where('name', 'Pengembalian Dana Talangan')->first()->id,
                'rkb_id' => $temporaryFund->rkb_id,
                'type' => 'pengeluaran',
                'amount' => $temporaryFund->amount,
                'transaction_date' => $validatedData['settlement_date'],
                'description' => 'Pengembalian talangan ke ' . $temporaryFund->sourceFund->name,
                'temporary_fund_id' => $temporaryFund->id,
                'is_settlement' => true,
            ]);

            // Buat transaksi pemasukan ke sumber dana
            $transactionIn = Transaction::create([
                'fund_id' => $temporaryFund->source_fund_id,
                'category_id' => Category::where('name', 'Pengembalian Dana Talangan')->first()->id,
                'rkb_id' => $temporaryFund->rkb_id,
                'type' => 'pemasukan',
                'amount' => $temporaryFund->amount,
                'transaction_date' => $validatedData['settlement_date'],
                'description' => 'Pengembalian talangan dari ' . $temporaryFund->targetFund->name,
                'temporary_fund_id' => $temporaryFund->id,
                'is_settlement' => true,
            ]);

            // Upload files if any
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('uploads/transactions', $filename, 'public');

                    File::create([
                        'fileable_id' => $transactionIn->id,
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

            // Update status talangan
            $temporaryFund->status = 'settled';
            $temporaryFund->settlement_date = $validatedData['settlement_date'];
            $temporaryFund->save();

            DB::commit();

            return redirect()->route('temporary_funds.show', $temporaryFund)->with('success', 'Dana talangan berhasil dilunasi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(TemporaryFund $temporaryFund)
    {
        // Validasi: hanya talangan yang belum ada transaksi yang bisa dihapus
        $transactions = Transaction::where('temporary_fund_id', $temporaryFund->id)->get();
        if ($transactions->count() > 0) {
            return redirect()->back()->with('error', 'Talangan tidak dapat dihapus karena sudah memiliki transaksi terkait.');
        }

        // Hapus file-file terkait
        foreach ($temporaryFund->files as $file) {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
        }

        $temporaryFund->delete();

        return redirect()->route('temporary_funds.index')->with('success', 'Dana talangan berhasil dihapus.');
    }
}
