@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus-circle"></i> Transaksi Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Saldo & Dana Aktif -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card card-dashboard">
                <div class="card-body">
                    <h6 class="card-title text-muted">Saldo Rekening HCGA</h6>
                    <h4 class="card-text">Rp {{ number_format($rekeningBalance, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-dashboard">
                <div class="card-body">
                    <h6 class="card-title text-muted">Saldo Kas Kecil HCGA</h6>
                    <h4 class="card-text">Rp {{ number_format($kasKecilBalance, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-dashboard card-danger">
                <div class="card-body">
                    <h6 class="card-title text-muted">Piutang Staff</h6>
                    <h4 class="card-text">Rp {{ number_format($totalStaffDebt, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-dashboard card-success">
                <div class="card-body">
                    <h6 class="card-title text-muted">Total Dana Aktif</h6>
                    <h4 class="card-text">Rp {{ number_format($totalActiveFund, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Piutang Staff -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">Uang Belum Kembali Dari Staff</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Staff</th>
                                    <th>Tanggal</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staffDebts as $debt)
                                <tr>
                                    <td>{{ $debt->staff->name }}</td>
                                    <td>{{ $debt->created_at->format('d M Y') }}</td>
                                    <td>Rp {{ number_format($debt->getCurrentDebt(), 0, ',', '.') }}</td>
                                    <td>
                                        @if($debt->status == 'outstanding')
                                            <span class="badge bg-danger">Belum Dibayar</span>
                                        @elseif($debt->status == 'partial')
                                            <span class="badge bg-warning text-dark">Sebagian</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('staff-debts.show', $debt) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data piutang staff.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Transaksi Terbaru -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Riwayat Transaksi Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Dana</th>
                                    <th>Kategori</th>
                                    <th>Jumlah</th>
                                    <th>Tipe</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</td>
                                    <td>{{ $transaction->fund->name }}</td>
                                    <td>{{ $transaction->category->name }}</td>
                                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if($transaction->type == 'pemasukan')
                                            <span class="badge bg-success">Masuk</span>
                                        @else
                                            <span class="badge bg-danger">Keluar</span>
                                        @endif
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
                    <div class="text-end mt-2">
                        <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- RKB & Sisa Dana -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Status RKB</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>RKB</th>
                                    <th>Diajukan</th>
                                    <th>Diterima</th>
                                    <th>Digunakan</th>
                                    <th>Sisa</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rkbs as $rkb)
                                <tr>
                                    <td>{{ $rkb->title }}</td>
                                    <td>Rp {{ number_format($rkb->requested_amount, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($rkb->received_amount, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($rkb->used_amount, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($rkb->getRemainingBalance(), 0, ',', '.') }}</td>
                                    <td>
                                        @if($rkb->status == 'pending')
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
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data RKB.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-2">
                        <a href="{{ route('rkbs.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dana Talangan -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">Dana Talangan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Dari</th>
                                    <th>Ke</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($temporaryFunds as $fund)
                                <tr>
                                    <td>{{ $fund->sourceFund->name }}</td>
                                    <td>{{ $fund->targetFund->name }}</td>
                                    <td>Rp {{ number_format($fund->amount, 0, ',', '.') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($fund->loan_date)->format('d M Y') }}</td>
                                    <td>
                                        @if($fund->status == 'outstanding')
                                            <span class="badge bg-danger">Belum Dikembalikan</span>
                                        @else
                                            <span class="badge bg-success">Lunas</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('temporary-funds.show', $fund) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data dana talangan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-2">
                        <a href="{{ route('temporary-funds.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
