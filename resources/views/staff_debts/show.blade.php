@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detail Piutang Staff</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('staff-debts.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header {{ $staffDebt->status == 'settled' ? 'bg-success' : 'bg-danger' }} text-white">
                    <h5 class="card-title mb-0">Informasi Piutang</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th width="30%">Nama Staff</th>
                            <td width="70%">{{ $staffDebt->staff->name }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>{{ $staffDebt->created_at->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Dana</th>
                            <td>
                                @if($staffDebt->type == 'transfer')
                                    <span class="badge bg-primary">Transfer</span>
                                @else
                                    <span class="badge bg-info">Tunai</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Jumlah Diberikan</th>
                            <td>Rp {{ number_format($staffDebt->given_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah Terpakai</th>
                            <td>Rp {{ number_format($staffDebt->used_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah Dikembalikan</th>
                            <td>Rp {{ number_format($staffDebt->returned_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Sisa Piutang</th>
                            <td>Rp {{ number_format($staffDebt->getCurrentDebt(), 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($staffDebt->status == 'outstanding')
                                    <span class="badge bg-danger">Belum Dibayar</span>
                                @elseif($staffDebt->status == 'partial')
                                    <span class="badge bg-warning text-dark">Sebagian</span>
                                @elseif($staffDebt->status == 'settled')
                                    <span class="badge bg-success">Lunas</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Detail Pembelian</th>
                            <td>{{ $staffDebt->purchase_details ?? '-' }}</td>
                        </tr>
                    </table>

                    <div class="transaction-detail mt-3">
                        <h5>Transaksi Awal</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Tanggal</th>
                                <td>{{ \Carbon\Carbon::parse($staffDebt->transaction->transaction_date)->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>Dana</th>
                                <td>{{ $staffDebt->transaction->fund->name }}</td>
                            </tr>
                            <tr>
                                <th>Kategori</th>
                                <td>{{ $staffDebt->transaction->category->name }}</td>
                            </tr>
                            <tr>
                                <th>Keterangan</th>
                                <td>{{ $staffDebt->transaction->description }}</td>
                            </tr>
                            <tr>
                                <th>Jumlah</th>
                                <td>Rp {{ number_format($staffDebt->transaction->amount, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                        <a href="{{ route('transactions.show', $staffDebt->transaction) }}" class="btn btn-sm btn-info">
                            <i class="bi bi-eye"></i> Lihat Detail Transaksi
                        </a>
                    </div>
                </div>
            </div>

            @if($staffDebt->files->count() > 0)
            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Dokumen Pendukung</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($staffDebt->files as $file)
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
                        <input type="hidden" name="fileable_id" value="{{ $staffDebt->id }}">
                        <input type="hidden" name="fileable_type" value="App\Models\StaffDebt">
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
                    @if($staffDebt->status != 'settled')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Catat Realisasi Pembelian</h5>
                                    <p class="card-text">Mencatat pembelian yang dilakukan oleh staff menggunakan dana yang telah diberikan.</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#recordPurchaseModal">
                                        <i class="bi bi-cart-plus"></i> Catat Pembelian
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Catat Pengembalian Dana</h5>
                                    <p class="card-text">Mencatat pengembalian dana oleh staff yang tidak digunakan.</p>
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#recordReturnModal">
                                        <i class="bi bi-cash-coin"></i> Catat Pengembalian
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-success">
                        <h5><i class="bi bi-check-circle"></i> Piutang Telah Lunas</h5>
                        <p>Piutang ini telah selesai dan tidak memerlukan tindakan lebih lanjut.</p>
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
                                    <th>Tipe</th>
                                    <th>Keterangan</th>
                                    <th>Jumlah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $relatedTransactions = App\Models\Transaction::where('staff_id', $staffDebt->staff_id)
                                        ->where(function($query) use ($staffDebt) {
                                            $query->where('id', $staffDebt->transaction_id)
                                                ->orWhere(function($q) use ($staffDebt) {
                                                    $q->where('transaction_date', '>=', $staffDebt->created_at->format('Y-m-d'))
                                                      ->where('description', 'like', '%' . $staffDebt->transaction->description . '%');
                                                });
                                        })
                                        ->orderBy('transaction_date', 'desc')
                                        ->get();
                                @endphp

                                @forelse($relatedTransactions as $transaction)
                                <tr class="{{ $transaction->id == $staffDebt->transaction_id ? 'table-primary' : '' }}">
                                    <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</td>
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
                                    <td colspan="5" class="text-center">Tidak ada data transaksi.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Catat Pembelian -->
    <div class="modal fade" id="recordPurchaseModal" tabindex="-1" aria-labelledby="recordPurchaseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('staff-debts.record-purchase', $staffDebt) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="recordPurchaseModalLabel">Catat Realisasi Pembelian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah Pembelian</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control rupiah" id="amount" name="amount" required>
                            </div>
                            <small class="form-text text-muted">Maksimal: Rp {{ number_format($staffDebt->getCurrentDebt(), 0, ',', '.') }}</small>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Kategori</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="" selected disabled>Pilih Kategori</option>
                                @foreach(App\Models\Category::where('type', 'pengeluaran')->get() as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="rkb_id" class="form-label">RKB (opsional)</label>
                            <select class="form-select" id="rkb_id" name="rkb_id">
                                <option value="">-- Tidak Ada --</option>
                                @foreach(App\Models\Rkb::whereIn('status', ['partial', 'full'])->get() as $rkb)
                                <option value="{{ $rkb->id }}">{{ $rkb->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="transaction_date" class="form-label">Tanggal Pembelian</label>
                            <input type="date" class="form-control" id="transaction_date" name="transaction_date" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required>Realisasi pembelian dari dana yang diberikan ke {{ $staffDebt->staff->name }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="recipient_name" class="form-label">Penerima/Vendor (opsional)</label>
                            <input type="text" class="form-control" id="recipient_name" name="recipient_name">
                        </div>

                        <div class="mb-3">
                            <label for="recipient_type" class="form-label">Tipe Penerima</label>
                            <select class="form-select" id="recipient_type" name="recipient_type">
                                <option value="">-- Tidak Ada --</option>
                                <option value="vendor">Vendor</option>
                                <option value="staff">Staff</option>
                                <option value="finance">Finance</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="files" class="form-label">Bukti Pembelian</label>
                            <input type="file" class="form-control" id="files" name="files[]" multiple>
                            <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Maksimal ukuran: 2MB per file.</small>
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

    <!-- Modal Catat Pengembalian -->
    <div class="modal fade" id="recordReturnModal" tabindex="-1" aria-labelledby="recordReturnModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('staff-debts.record-return', $staffDebt) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="recordReturnModalLabel">Catat Pengembalian Dana</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah Pengembalian</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control rupiah" id="amount" name="amount" required>
                            </div>
                            <small class="form-text text-muted">Maksimal: Rp {{ number_format($staffDebt->getCurrentDebt(), 0, ',', '.') }}</small>
                        </div>

                        <div class="mb-3">
                            <label for="transaction_date" class="form-label">Tanggal Pengembalian</label>
                            <input type="date" class="form-control" id="transaction_date" name="transaction_date" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required>Pengembalian dana dari {{ $staffDebt->staff->name }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="files" class="form-label">Bukti Pengembalian</label>
                            <input type="file" class="form-control" id="files" name="files[]" multiple>
                            <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Maksimal ukuran: 2MB per file.</small>
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
