@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">RKB (Rencana Kegiatan dan Budget)</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('rkbs.create') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus-circle"></i> Tambah RKB
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
                            <th>Judul RKB</th>
                            <th>Diajukan</th>
                            <th>Disetujui</th>
                            <th>Diterima</th>
                            <th>Digunakan</th>
                            <th>Sisa</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rkbs as $rkb)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $rkb->title }}</td>
                            <td>Rp {{ number_format($rkb->requested_amount, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($rkb->approved_amount, 0, ',', '.') }}</td>
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
                            <td>
                                <a href="{{ route('rkbs.show', $rkb) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('rkbs.edit', $rkb) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('rkbs.destroy', $rkb) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus RKB ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data RKB.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
