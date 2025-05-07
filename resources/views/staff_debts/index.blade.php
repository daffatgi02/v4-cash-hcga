@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Piutang Staff</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('transactions.create') }}?create_staff_debt=1" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus-circle"></i> Buat Transaksi Piutang
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Staff</th>
                            <th>Tanggal</th>
                            <th>Jenis Dana</th>
                            <th>Jumlah Diberikan</th>
                            <th>Jumlah Terpakai</th>
                            <th>Jumlah Dikembalikan</th>
                            <th>Sisa Piutang</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffDebts as $debt)
                        <tr>
                            <td>{{ $debt->staff->name }}</td>
                            <td>{{ $debt->created_at->format('d M Y') }}</td>
                            <td>
                                @if($debt->type == 'transfer')
                                    <span class="badge bg-primary">Transfer</span>
                                @else
                                    <span class="badge bg-info">Tunai</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($debt->given_amount, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($debt->used_amount, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($debt->returned_amount, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($debt->getCurrentDebt(), 0, ',', '.') }}</td>
                            <td>
                                @if($debt->status == 'outstanding')
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
                            <td colspan="9" class="text-center">Tidak ada data piutang staff.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
