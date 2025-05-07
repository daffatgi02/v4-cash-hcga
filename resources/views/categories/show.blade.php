@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detail Kategori</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header {{ $category->type == 'pemasukan' ? 'bg-success' : 'bg-danger' }} text-white">
                    <h5 class="card-title mb-0">Informasi Kategori</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th width="30%">Nama Kategori</th>
                            <td width="70%">{{ $category->name }}</td>
                        </tr>
                        <tr>
                            <th>Tipe</th>
                            <td>
                                @if($category->type == 'pemasukan')
                                    <span class="badge bg-success">Pemasukan</span>
                                @else
                                    <span class="badge bg-danger">Pengeluaran</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Keterangan</th>
                            <td>{{ $category->description ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td>{{ $category->created_at->format('d F Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Diubah</th>
                            <td>{{ $category->updated_at->format('d F Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Ringkasan Transaksi</h5>
                </div>
                <div class="card-body">
                    @php
                        $totalAmount = $transactions->sum('amount');
                        $countTransactions = $transactions->count();
                    @endphp

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card card-dashboard">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">Jumlah Transaksi</h6>
                                    <h4 class="card-text">{{ $countTransactions }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card card-dashboard {{ $category->type == 'pemasukan' ? 'card-success' : 'card-danger' }}">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">Total {{ $category->type == 'pemasukan' ? 'Pemasukan' : 'Pengeluaran' }}</h6>
                                    <h4 class="card-text">Rp {{ number_format($totalAmount, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('transactions.create') }}?category_id={{ $category->id }}" class="btn btn-primary">
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
                            <th>Dana</th>
                            <th>Keterangan</th>
                            <th>RKB</th>
                            <th>Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</td>
                            <td>{{ $transaction->fund->name }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($transaction->description, 50) }}</td>
                            <td>{{ $transaction->rkb ? $transaction->rkb->title : '-' }}</td>
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
@endsection
