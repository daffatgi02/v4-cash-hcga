@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Detail Transaksi</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div
                        class="card-header {{ $transaction->type == 'pemasukan' ? 'bg-success' : 'bg-danger' }} text-white">
                        <h5 class="card-title mb-0">Informasi Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <th width="30%">ID Transaksi</th>
                                <td width="70%">#{{ $transaction->id }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Transaksi</th>
                                <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th>Tipe</th>
                                <td>
                                    @if ($transaction->type == 'pemasukan')
                                        <span class="badge bg-success">Pemasukan</span>
                                    @else
                                        <span class="badge bg-danger">Pengeluaran</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Dana</th>
                                <td>{{ $transaction->fund->name }}</td>
                            </tr>
                            <tr>
                                <th>Kategori</th>
                                <td>{{ $transaction->category->name }}</td>
                            </tr>
                            <tr>
                                <th>Jumlah</th>
                                <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Keterangan</th>
                                <td>{{ $transaction->description }}</td>
                            </tr>
                            <tr>
                                <th>RKB</th>
                                <td>{{ $transaction->rkb ? $transaction->rkb->title : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Dibuat Pada</th>
                                <td>{{ $transaction->created_at->format('d F Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Terakhir Diubah</th>
                                <td>{{ $transaction->updated_at->format('d F Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if ($transaction->files->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">Bukti Transaksi</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                @foreach ($transaction->files as $file)
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
                        <h5 class="card-title mb-0">Upload Bukti Baru</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="fileable_id" value="{{ $transaction->id }}">
                            <input type="hidden" name="fileable_type" value="App\Models\Transaction">
                            <input type="hidden" name="file_type" value="bukti_transaksi">

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
                @if ($transaction->staff)
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">Informasi Staff</h5>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th width="30%">Nama Staff</th>
                                    <td width="70%">{{ $transaction->staff->name }}</td>
                                </tr>
                                <tr>
                                    <th>Posisi</th>
                                    <td>{{ $transaction->staff->position ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Kontak</th>
                                    <td>{{ $transaction->staff->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Rekening</th>
                                    <td>{{ $transaction->staff->bank_account ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Penerima</th>
                                    <td>{{ $transaction->recipient_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tipe Penerima</th>
                                    <td>
                                        @if ($transaction->recipient_type == 'vendor')
                                            <span class="badge bg-info">Vendor</span>
                                        @elseif($transaction->recipient_type == 'staff')
                                            <span class="badge bg-primary">Staff</span>
                                        @elseif($transaction->recipient_type == 'finance')
                                            <span class="badge bg-success">Finance</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            @if ($transaction->staffDebt)
                                <div
                                    class="alert {{ $transaction->staffDebt->status == 'settled' ? 'alert-success' : 'alert-warning' }} mt-3">
                                    <h5>Piutang Staff</h5>
                                    <p>Status:
                                        @if ($transaction->staffDebt->status == 'outstanding')
                                            <span class="badge bg-danger">Belum Dibayar</span>
                                        @elseif($transaction->staffDebt->status == 'partial')
                                            <span class="badge bg-warning text-dark">Sebagian</span>
                                        @elseif($transaction->staffDebt->status == 'settled')
                                            <span class="badge bg-success">Lunas</span>
                                        @endif
                                    </p>
                                    <p>Jumlah Diberikan: Rp
                                        {{ number_format($transaction->staffDebt->given_amount, 0, ',', '.') }}</p>
                                    <p>Jumlah Terpakai: Rp
                                        {{ number_format($transaction->staffDebt->used_amount, 0, ',', '.') }}</p>
                                    <p>Jumlah Dikembalikan: Rp
                                        {{ number_format($transaction->staffDebt->returned_amount, 0, ',', '.') }}</p>
                                    <p>Sisa Piutang: Rp
                                        {{ number_format($transaction->staffDebt->getCurrentDebt(), 0, ',', '.') }}</p>

                                    @if ($transaction->staffDebt->status != 'settled')
                                        <div class="mt-3">
                                            <a href="{{ route('staff-debts.show', $transaction->staffDebt) }}"
                                                class="btn btn-primary">Lihat Detail Piutang</a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($transaction->temporaryFund)
                    <div class="card mb-3">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="card-title mb-0">Informasi Dana Talangan</h5>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th width="30%">Dana Asal</th>
                                    <td width="70%">{{ $transaction->temporaryFund->sourceFund->name }}</td>
                                </tr>
                                <tr>
                                    <th>Dana Tujuan</th>
                                    <td>{{ $transaction->temporaryFund->targetFund->name }}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah</th>
                                    <td>Rp {{ number_format($transaction->temporaryFund->amount, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Talangan</th>
                                    <td>{{ \Carbon\Carbon::parse($transaction->temporaryFund->loan_date)->format('d F Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if ($transaction->temporaryFund->status == 'outstanding')
                                            <span class="badge bg-danger">Belum Dikembalikan</span>
                                        @else
                                            <span class="badge bg-success">Lunas</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tanggal Pengembalian</th>
                                    <td>{{ $transaction->temporaryFund->settlement_date ? \Carbon\Carbon::parse($transaction->temporaryFund->settlement_date)->format('d F Y') : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Keterangan</th>
                                    <td>{{ $transaction->temporaryFund->description }}</td>
                                </tr>
                            </table>

                            <div class="mt-3">
                                <a href="{{ route('temporary-funds.show', $transaction->temporaryFund) }}"
                                    class="btn btn-primary">Lihat Detail Talangan</a>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($transaction->rkb)
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0">Informasi RKB</h5>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th width="30%">Judul RKB</th>
                                    <td width="70%">{{ $transaction->rkb->title }}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah Diajukan</th>
                                    <td>Rp {{ number_format($transaction->rkb->requested_amount, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah Disetujui</th>
                                    <td>Rp {{ number_format($transaction->rkb->approved_amount, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah Diterima</th>
                                    <td>Rp {{ number_format($transaction->rkb->received_amount, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah Digunakan</th>
                                    <td>Rp {{ number_format($transaction->rkb->used_amount, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Sisa Dana</th>
                                    <td>Rp {{ number_format($transaction->rkb->getRemainingBalance(), 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if ($transaction->rkb->status == 'pending')
                                            <span class="badge bg-warning text-dark">Menunggu</span>
                                        @elseif($transaction->rkb->status == 'partial')
                                            <span class="badge bg-info">Sebagian</span>
                                        @elseif($transaction->rkb->status == 'full')
                                            <span class="badge bg-success">Penuh</span>
                                        @elseif($transaction->rkb->status == 'completed')
                                            <span class="badge bg-primary">Selesai</span>
                                        @else
                                            <span class="badge bg-danger">Dibatalkan</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <div class="mt-3">
                                <a href="{{ route('rkbs.show', $transaction->rkb) }}" class="btn btn-primary">Lihat
                                    Detail RKB</a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
