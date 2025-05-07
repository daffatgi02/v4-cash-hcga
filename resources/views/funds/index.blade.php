@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dana</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('funds.create') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Dana
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
                            <th>#</th>
                            <th>Nama</th>
                            <th>Tipe</th>
                            <th>Saldo Awal</th>
                            <th>Saldo Saat Ini</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($funds as $fund)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $fund->name }}</td>
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
                            <td>Rp {{ number_format($fund->initial_balance, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($fund->getCurrentBalance(), 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('funds.show', $fund) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('funds.edit', $fund) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('funds.destroy', $fund) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus dana ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data dana.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
