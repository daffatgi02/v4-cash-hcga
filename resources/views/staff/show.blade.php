@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Detail Staff</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <a href="{{ route('staff.edit', $staff) }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="{{ route('staff.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Informasi Staff</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <th width="30%">Nama Staff</th>
                                <td width="70%">{{ $staff->name }}</td>
                            </tr>
                            <tr>
                                <th>Posisi / Jabatan</th>
                                <td>{{ $staff->position ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $staff->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>No. Telepon</th>
                                <td>{{ $staff->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Informasi Rekening</th>
                                <td>{{ $staff->bank_account ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Dibuat</th>
                                <td>{{ $staff->created_at->format('d F Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Terakhir Diubah</th>
                                <td>{{ $staff->updated_at->format('d F Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-{{ $totalDebt > 0 ? 'danger' : 'success' }} text-white">
                        <h5 class="card-title mb-0">Piutang Staff</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card card-dashboard">
                                    <div class="card-body">
                                        <h6 class="card-title text-muted">Jumlah Piutang</h6>
                                        <h4 class="card-text">{{ $debts->count() }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card card-dashboard {{ $totalDebt > 0 ? 'card-danger' : 'card-success' }}">
                                    <div class="card-body">
                                        <h6 class="card-title text-muted">Total Piutang</h6>
                                        <h4 class="card-text">Rp {{ number_format($totalDebt, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('transactions.create') }}?staff_id={{ $staff->id }}&create_staff_debt=1"
                                class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Transaksi Baru ke Staff
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Piutang Staff -->
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">Piutang Staff</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jumlah Diberikan</th>
                                <th>Jumlah Terpakai</th>
                                <th>Jumlah Dikembalikan</th>
                                <th>Sisa Piutang</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($debts as $debt)
                                <tr>
                                    <td>{{ $debt->created_at->format('d M Y') }}</td>
                                    <td>Rp {{ number_format($debt->given_amount, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($debt->used_amount, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($debt->returned_amount, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($debt->getCurrentDebt(), 0, ',', '.') }}</td>
                                    <td>
                                        @if ($debt->status == 'outstanding')
                                            <span class="badge bg-danger">Belum Dibayar</span>
                                        @elseif($debt->status == 'partial')
                                            <span class="badge bg-warning text-dark">Sebagian</span>
                                        @elseif($debt->status == 'settled')
                                            <span class="badge bg-success">Lunas</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('staff-debts.show', $debt) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data piutang staff.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
    </div>
@endsection
