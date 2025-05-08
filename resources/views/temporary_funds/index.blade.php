@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dana Talangan</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('temporary-funds.create') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Dana Talangan
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
                            <th>Dana Asal</th>
                            <th>Dana Tujuan</th>
                            <th>RKB</th>
                            <th>Jumlah</th>
                            <th>Tanggal Talangan</th>
                            <th>Tanggal Kembali</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($temporaryFunds as $fund)
                        <tr>
                            <td>{{ $fund->sourceFund->name }}</td>
                            <td>{{ $fund->targetFund->name }}</td>
                            <td>{{ $fund->rkb ? $fund->rkb->title : '-' }}</td>
                            <td>Rp {{ number_format($fund->amount, 0, ',', '.') }}</td>
                            <td>{{ \Carbon\Carbon::parse($fund->loan_date)->format('d M Y') }}</td>
                            <td>{{ $fund->settlement_date ? \Carbon\Carbon::parse($fund->settlement_date)->format('d M Y') : '-' }}</td>
                            <td>
                                @if($fund->status == 'outstanding')
                                    <span class="badge bg-danger">Belum Dikembalikan</span>
                                @else
                                    <span class="badge bg-success">Lunas</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('temporary-funds.show', $fund) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($fund->status == 'outstanding')
                                <form action="{{ route('temporary-funds.destroy', $fund) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus dana talangan ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data dana talangan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
