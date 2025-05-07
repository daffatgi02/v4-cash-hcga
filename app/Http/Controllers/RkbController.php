<?php
namespace App\Http\Controllers;

use App\Models\Rkb;
use App\Models\Fund;
use App\Models\TemporaryFund;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class RkbController extends Controller
{
    public function index()
    {
        $rkbs = Rkb::orderBy('request_date', 'desc')->get();
        return view('rkbs.index', compact('rkbs'));
    }

    public function create()
    {
        return view('rkbs.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requested_amount' => 'required|numeric|min:0',
            'approved_amount' => 'nullable|numeric|min:0',
            'request_date' => 'required|date',
            'approval_date' => 'nullable|date',
            'status' => 'required|in:pending,partial,full,completed,cancelled',
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $rkb = Rkb::create($validatedData);

            // Upload files if any
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('uploads/rkbs', $filename, 'public');

                    File::create([
                        'fileable_id' => $rkb->id,
                        'fileable_type' => Rkb::class,
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

            return redirect()->route('rkbs.index')->with('success', 'RKB berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Rkb $rkb)
    {
        $temporaryFunds = TemporaryFund::where('rkb_id', $rkb->id)->with(['sourceFund', 'targetFund'])->get();
        $transactions = $rkb->transactions()->with(['fund', 'category', 'staff'])->orderBy('transaction_date', 'desc')->get();

        return view('rkbs.show', compact('rkb', 'temporaryFunds', 'transactions'));
    }

    public function edit(Rkb $rkb)
    {
        return view('rkbs.edit', compact('rkb'));
    }

    public function update(Request $request, Rkb $rkb)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requested_amount' => 'required|numeric|min:0',
            'approved_amount' => 'nullable|numeric|min:0',
            'received_amount' => 'nullable|numeric|min:0',
            'used_amount' => 'nullable|numeric|min:0',
            'request_date' => 'required|date',
            'approval_date' => 'nullable|date',
            'status' => 'required|in:pending,partial,full,completed,cancelled',
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $rkb->update($validatedData);

            // Upload files if any
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('uploads/rkbs', $filename, 'public');

                    File::create([
                        'fileable_id' => $rkb->id,
                        'fileable_type' => Rkb::class,
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

            return redirect()->route('rkbs.show', $rkb)->with('success', 'RKB berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Rkb $rkb)
    {
        // Cek apakah RKB memiliki transaksi atau dana talangan
        if ($rkb->transactions()->count() > 0 || $rkb->temporaryFunds()->count() > 0) {
            return redirect()->route('rkbs.index')->with('error', 'RKB tidak dapat dihapus karena masih memiliki transaksi atau dana talangan.');
        }

        DB::beginTransaction();

        try {
            // Hapus file-file terkait
            foreach ($rkb->files as $file) {
                Storage::disk('public')->delete($file->file_path);
                $file->delete();
            }

            $rkb->delete();

            DB::commit();

            return redirect()->route('rkbs.index')->with('success', 'RKB berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Metode untuk mencatat penerimaan dana dari Finance
    public function receiveFunds(Request $request, Rkb $rkb)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric|min:0',
            'fund_id' => 'required|exists:funds,id',
            'transaction_date' => 'required|date',
            'description' => 'required|string',
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $fund = Fund::find($validatedData['fund_id']);

            // Buat transaksi pemasukan
            $transaction = Transaction::create([
                'fund_id' => $validatedData['fund_id'],
                'category_id' => Category::where('name', 'Pencairan Dana RKB')->first()->id,
                'rkb_id' => $rkb->id,
                'type' => 'pemasukan',
                'amount' => $validatedData['amount'],
                'transaction_date' => $validatedData['transaction_date'],
                'description' => $validatedData['description'],
                'recipient_type' => 'finance',
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
                        'file_type' => 'bukti_transfer',
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            // Update RKB received_amount
            $rkb->received_amount += $validatedData['amount'];

            // Update status RKB
            if ($rkb->received_amount >= $rkb->approved_amount) {
                $rkb->status = 'full';
            } elseif ($rkb->received_amount > 0) {
                $rkb->status = 'partial';
            }

            $rkb->save();

            DB::commit();

            return redirect()->route('rkbs.show', $rkb)->with('success', 'Dana RKB berhasil diterima.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}
