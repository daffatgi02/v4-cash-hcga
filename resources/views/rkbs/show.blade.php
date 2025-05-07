@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Detail RKB</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <a href="{{ route('rkbs.edit', $rkb) }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="{{ route('rkbs.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Informasi RKB</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <th width="30%">Judul RKB</th>
                                <td width="70%">{{ $rkb->title }}</td>
                            </tr>
                            <tr>
                                <th>Deskripsi</th>
                                <td>{{ $rkb->description ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Pengajuan</th>
                                <td>{{ \Carbon\Carbon::parse($rkb->request_date)->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Persetujuan</th>
                                <td>{{ $rkb->approval_date ? \Carbon\Carbon::parse($rkb->approval_date)->format('d M Y') : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if ($rkb->status == 'pending')
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    @elseif($rkb->status == 'partial')
                                        <span class="badge bg-info">Sebagian</span>
                                    @elseif($rkb->status == 'full')
                                        <span class="badge bg-success">Penuh</span>
                                    @elseif($rkb->status == 'completed')
                                        <span class="badge bg-primary">Selesai</span>
                                    @else
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if ($rkb->files->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">Dokumen Pendukung</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                @foreach ($rkb->files as $file)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ $file->original_filename }}</span>
                                        <div>
                                            <a href="{{ route('files.download', $file) }}" class="btn btn-sm btn-success"
                                                title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            <form action="{{ route('files.destroy', $file) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus file ini?')"
                                                    title="Hapus">
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
                            <input type="hidden" name="fileable_id" value="{{ $rkb->id }}">
                            <input type="hidden" name="fileable_type" value="App\Models\Rkb">
                            <input type="hidden" name="file_type" value="document">

                            <div class="mb-3">
                                <label for="file" class="form-label">Pilih File</label>
                                <input type="file" class="form-control @error('file') is-invalid @enderror"
                                    id="file" name="file" required>
                                <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Maksimal ukuran:
                                    2MB.</small>
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
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">Dana RKB</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card card-dashboard">
                                    <div class="card-body">
                                        <h6 class="card-title text-muted">Jumlah Diajukan</h6>
                                        <h4 class="card-text">Rp {{ number_format($rkb->requested_amount, 0, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card card-dashboard">
                                    <div class="card-body">
                                        <h6 class="card-title text-muted">Jumlah Disetujui</h6>
                                        <h4 class="card-text">Rp {{ number_format($rkb->approved_amount, 0, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card card-dashboard card-success">
                                    <div class="card-body">
                                        <h6 class="card-title text-muted">Jumlah Diterima</h6>
                                        <h4 class="card-text">Rp {{ number_format($rkb->received_amount, 0, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card card-dashboard card-danger">
                                    <div class="card-body">
                                        <h6 class="card-title text-muted">Jumlah Digunakan</h6>
                                        <h4 class="card-text">Rp {{ number_format($rkb->used_amount, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="card card-dashboard card-warning">
                                    <div class="card-body">
                                        <h6 class="card-title text-muted">Sisa Dana</h6>
                                        <h4 class="card-text">Rp
                                            {{ number_format($rkb->getRemainingBalance(), 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#receiveFundsModal">
                                <i class="bi bi-cash-coin"></i> Catat Penerimaan Dana
                            </button>
                            <a href="{{ route('transactions.create') }}?rkb_id={{ $rkb->id }}"
                                class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Buat Transaksi
                            </a>
                            <a href="{{ route('temporary-funds.create') }}?rkb_id={{ $rkb->id }}"
                                class="btn btn-warning">
                                <i class="bi bi-shuffle"></i> Buat Talangan
                            </a>
                        </div>
                    </div>
                </div>

                @if ($temporaryFunds->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="card-title mb-0">Dana Talangan</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Dari</th>
                                            <th>Ke</th>
                                            <th>Jumlah</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($temporaryFunds as $fund)
                                            <tr>
                                                <td>{{ $fund->sourceFund->name }}</td>
                                                <td>{{ $fund->targetFund->name }}</td>
                                                <td>Rp {{ number_format($fund->amount, 0, ',', '.') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($fund->loan_date)->format('d M Y') }}</td>
                                                <td>
                                                    @if ($fund->status == 'outstanding')
                                                        <span class="badge bg-danger">Belum Dikembalikan</span>
                                                    @else
                                                        <span class="badge bg-success">Lunas</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('temporary-funds.show', $fund) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Riwayat Transaksi -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Riwayat Transaksi</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Dana</th>
                                <th>Kategori</th>
                                <th>Keterangan</th>
                                <th>Tipe</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</td>
                                    <td>{{ $transaction->fund->name }}</td>
                                    <td>{{ $transaction->category->name }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($transaction->description, 50) }}</td>
                                    <td>
                                        @if ($transaction->type == 'pemasukan')
                                            <span class="badge bg-success">Masuk</span>
                                        @else
                                            <span class="badge bg-danger">Keluar</span>
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td>
                                        <a href="{{ route('transactions.show', $transaction) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Penerimaan Dana -->
        <div class="modal fade" id="receiveFundsModal" tabindex="-1" aria-labelledby="receiveFundsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('rkbs.receive-funds', $rkb) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="receiveFundsModalLabel">Catat Penerimaan Dana</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Jumlah Diterima</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control rupiah" id="amount" name="amount"
                                        required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="fund_id" class="form-label">Dana Tujuan</label>
                                <select class="form-select" id="fund_id" name="fund_id" required>
                                    <option value="" selected disabled>Pilih Dana</option>
                                    @foreach (App\Models\Fund::all() as $fund)
                                        <option value="{{ $fund->id }}">{{ $fund->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="transaction_date" class="form-label">Tanggal Penerimaan</label>
                                <input type="date" class="form-control" id="transaction_date" name="transaction_date"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Keterangan</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required>Penerimaan dana RKB: {{ $rkb->title }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="files" class="form-label">Bukti Penerimaan</label>
                                <input type="file" class="form-control" id="files" name="files[]" multiple>
                                <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Maksimal ukuran: 2MB per
                                    file.</small>
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

@push('scripts')
    <script>
        // Format input rupiah
        document.addEventListener('DOMContentLoaded', function() {
            const rupiahInputs = document.querySelectorAll('.rupiah');
            rupiahInputs.forEach(function(input) {
                input.addEventListener('keyup', function(e) {
                    const value = this.value.replace(/\D/g, '');
                    this.value = formatRupiah(value);
                });
            });

            document.querySelectorAll('form').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    const rupiahInputs = this.querySelectorAll('.rupiah');
                    rupiahInputs.forEach(function(input) {
                        const value = input.value.replace(/\D/g, '');
                        input.value = value;
                    });
                });
            });
        });
    </script>
@endpush
