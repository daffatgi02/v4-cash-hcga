<?php
namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class FileController extends Controller
{
    public function download(File $file)
    {
        if (Storage::disk('public')->exists($file->file_path)) {
            return Response::download(Storage::disk('public')->path($file->file_path), $file->original_filename);
        }

        return redirect()->back()->with('error', 'File tidak ditemukan.');
    }

    public function destroy(File $file)
    {
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        return redirect()->back()->with('success', 'File berhasil dihapus.');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'fileable_id' => 'required|integer',
            'fileable_type' => 'required|string',
            'file_type' => 'required|string',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();

        // Tentukan folder berdasarkan fileable_type
        $fileableType = $validatedData['fileable_type'];
        $folder = '';

        switch ($fileableType) {
            case 'App\Models\Transaction':
                $folder = 'transactions';
                break;
            case 'App\Models\Rkb':
                $folder = 'rkbs';
                break;
            case 'App\Models\StaffDebt':
                $folder = 'staff_debts';
                break;
            case 'App\Models\TemporaryFund':
                $folder = 'temporary_funds';
                break;
            default:
                $folder = 'others';
        }

        $filePath = $file->storeAs('uploads/' . $folder, $filename, 'public');

        File::create([
            'fileable_id' => $validatedData['fileable_id'],
            'fileable_type' => $validatedData['fileable_type'],
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $validatedData['file_type'],
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return redirect()->back()->with('success', 'File berhasil diunggah.');
    }
}
