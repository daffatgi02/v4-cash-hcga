@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detail Dana</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('funds.edit', $fund) }}" class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="{{ route('funds.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Informasi Dana</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th width="30%">Nama Dana</th>
                            <td width="70%">{{ $fund->name }}</td>
                        </tr>
                        <tr>
                            <th>Tipe Dana</th>
                            <td>
                                @if($fund->type == 'rekening_utama')
                                    <span class="badge bg-primary">Rekening Utama</span>
                                @elseif($fund->type == 'kas_kecil')
                                    <span class="badge bg-info">Kas Kecil</span>
                                @elseif($fund->type == 'dana_operasional')
                                    <span class="badge bg-success">Dana Operasional</span>
                                @elseif($fund->type == 'dana_rkb')
                                    <span class="badge bg-warning text-dark">Dana RKB</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Keterangan</th>
                            <td>{{ $fund->description ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Saldo Awal</th>
                            <td>Rp {{ number_format($fund->initial_balance, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td>{{ $fund->created_at->format('d F Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Diubah</th>
                            <td>{{ $fund->updated_at->format('d F Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Ringkasan Keuangan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card card-dashboard">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">Saldo Awal</h6>
                                    <h4 class="card-text">Rp {{ number_format($fund->initial_balance, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card card-dashboard card-success">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">Saldo Saat Ini</h6>
                                    <h4 class="card-text">Rp {{ number_format($currentBalance, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card card-dashboard card-success">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">Total Pemasukan</h6>
                                    <h4 class="card-text">Rp {{ number_format($transactions->where('type', 'pemasukan')->sum('amount'), 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card card-dashboard card-danger">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">Total Pengeluaran</h6>
                                    <h4 class="card-text">Rp {{ number_format($transactions->where('type', 'pengeluaran')->sum('amount'), 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('transactions.create') }}?fund_id={{ $fund->id }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Buat Transaksi Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th>RKB</th>
                            <th>Tipe</th>
                            <th>Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</td>
                            <td>{{ $transaction->category->name }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($transaction->description, 50) }}</td>
                            <td>{{ $transaction->rkb ? $transaction->rkb->title : '-' }}</td>
                            <td>
                                @if($transaction->type == 'pemasukan')
                                    <span class="badge bg-success">Masuk</span>
                                @else
                                    <span class="badge bg-danger">Keluar</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-sm btn-info">
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
