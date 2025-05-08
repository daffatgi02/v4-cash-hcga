@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detail Dana Talangan</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('temporary-funds.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header {{ $temporaryFund->status == 'settled' ? 'bg-success' : 'bg-warning text-dark' }} text-white">
                    <h5 class="card-title mb-0">Informasi Dana Talangan</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th width="30%">Dana Asal</th>
                            <td width="70%">{{ $temporaryFund->sourceFund->name }}</td>
                        </tr>
                        <tr>
                            <th>Dana Tujuan</th>
                            <td>{{ $temporaryFund->targetFund->name }}</td>
                        </tr>
                        <tr>
                            <th>RKB</th>
                            <td>{{ $temporaryFund->rkb ? $temporaryFund->rkb->title : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td>Rp {{ number_format($temporaryFund->amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Talangan</th>
                            <td>{{ \Carbon\Carbon::parse($temporaryFund->loan_date)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Kembali</th>
                            <td>{{ $temporaryFund->settlement_date ? \Carbon\Carbon::parse($temporaryFund->settlement_date)->format('d F Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($temporaryFund->status == 'outstanding')
                                    <span class="badge bg-danger">Belum Dikembalikan</span>
                                @else
                                    <span class="badge bg-success">Lunas</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Keterangan</th>
                            <td>{{ $temporaryFund->description }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat Pada</th>
                            <td>{{ $temporaryFund->created_at->format('d F Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Diubah</th>
                            <td>{{ $temporaryFund->updated_at->format('d F Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($temporaryFund->files->count() > 0)
            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Dokumen Pendukung</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($temporaryFund->files as $file)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $file->original_filename }}</span>
                            <div>
                                <a href="{{ route('files.download', $file) }}" class="btn btn-sm btn-success" title="Download">
                                    <i class="bi bi-download"></i>
                                </a>
                                <form action="{{ route('files.destroy', $file) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus file ini?')" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <div class="card mt-3">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Upload Dokumen Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="fileable_id" value="{{ $temporaryFund->id }}">
                        <input type="hidden" name="fileable_type" value="App\Models\TemporaryFund">
                        <input type="hidden" name="file_type" value="document">

                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" required>
                            <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Maksimal ukuran: 2MB.</small>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    @if($temporaryFund->status == 'outstanding')
                    <div class="alert alert-warning">
                        <h5><i class="bi bi-exclamation-triangle"></i> Dana Belum Dikembalikan</h5>
                        <p>Dana talangan ini belum dikembalikan ke sumber asalnya. Silakan lakukan pengembalian jika sudah tersedia.</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#settleFundModal">
                            <i class="bi bi-cash-coin"></i> Catat Pengembalian Dana
                        </button>
                    </div>
                    @else
                    <div class="alert alert-success">
                        <h5><i class="bi bi-check-circle"></i> Dana Telah Dikembalikan</h5>
                        <p>Dana talangan ini telah dikembalikan ke sumber asalnya pada {{ \Carbon\Carbon::parse($temporaryFund->settlement_date)->format('d F Y') }}.</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Riwayat Transaksi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Dana</th>
                                    <th>Tipe</th>
                                    <th>Keterangan</th>
                                    <th>Jumlah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</td>
                                    <td>{{ $transaction->fund->name }}</td>
                                    <td>
                                        @if($transaction->type == 'pemasukan')
                                            <span class="badge bg-success">Masuk</span>
                                        @else
                                            <span class="badge bg-danger">Keluar</span>
                                        @endif
                                    </td>
                                    <td>{{ \Illuminate\Support\Str::limit($transaction->description, 50) }}</td>
                                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td>
                                        <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data transaksi.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pengembalian Dana -->
    <div class="modal fade" id="settleFundModal" tabindex="-1" aria-labelledby="settleFundModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('temporary-funds.settle', $temporaryFund) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="settleFundModalLabel">Catat Pengembalian Dana</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="settlement_date" class="form-label">Tanggal Pengembalian</label>
                            <input type="date" class="form-control" id="settlement_date" name="settlement_date" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required>Pengembalian dana talangan dari {{ $temporaryFund->targetFund->name }} ke {{ $temporaryFund->sourceFund->name }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="files" class="form-label">Bukti Pengembalian</label>
                            <input type="file" class="form-control" id="files" name="files[]" multiple>
                            <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Maksimal ukuran: 2MB per file.</small>
                        </div>

                        <div class="alert alert-info">
                            <p>Sistem akan mencatat:</p>
                            <ul>
                                <li>Pengeluaran Rp {{ number_format($temporaryFund->amount, 0, ',', '.') }} dari {{ $temporaryFund->targetFund->name }}</li>
                                <li>Pemasukan Rp {{ number_format($temporaryFund->amount, 0, ',', '.') }} ke {{ $temporaryFund->sourceFund->name }}</li>
                                <li>Status dana talangan akan berubah menjadi Lunas</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
