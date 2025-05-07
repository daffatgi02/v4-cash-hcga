@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit RKB</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('rkbs.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('rkbs.update', $rkb) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Judul RKB</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $rkb->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi Kegiatan</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $rkb->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="requested_amount" class="form-label">Jumlah Diajukan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control rupiah @error('requested_amount') is-invalid @enderror" id="requested_amount" name="requested_amount" value="{{ old('requested_amount', number_format($rkb->requested_amount, 0, ',', '.')) }}" required>
                                @error('requested_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="approved_amount" class="form-label">Jumlah Disetujui</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control rupiah @error('approved_amount') is-invalid @enderror" id="approved_amount" name="approved_amount" value="{{ old('approved_amount', number_format($rkb->approved_amount, 0, ',', '.')) }}">
                                @error('approved_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="received_amount" class="form-label">Jumlah Diterima</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control rupiah @error('received_amount') is-invalid @enderror" id="received_amount" name="received_amount" value="{{ old('received_amount', number_format($rkb->received_amount, 0, ',', '.')) }}">
                                @error('received_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="used_amount" class="form-label">Jumlah Digunakan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control rupiah @error('used_amount') is-invalid @enderror" id="used_amount" name="used_amount" value="{{ old('used_amount', number_format($rkb->used_amount, 0, ',', '.')) }}">
                                @error('used_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="request_date" class="form-label">Tanggal Pengajuan</label>
                                    <input type="date" class="form-control @error('request_date') is-invalid @enderror" id="request_date" name="request_date" value="{{ old('request_date', $rkb->request_date) }}" required>
                                    @error('request_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="approval_date" class="form-label">Tanggal Persetujuan</label>
                                    <input type="date" class="form-control @error('approval_date') is-invalid @enderror" id="approval_date" name="approval_date" value="{{ old('approval_date', $rkb->approval_date) }}">
                                    @error('approval_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="" disabled>Pilih Status</option>
                                <option value="pending" {{ old('status', $rkb->status) == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                <option value="partial" {{ old('status', $rkb->status) == 'partial' ? 'selected' : '' }}>Sebagian</option>
                                <option value="full" {{ old('status', $rkb->status) == 'full' ? 'selected' : '' }}>Penuh</option>
                                <option value="completed" {{ old('status', $rkb->status) == 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="cancelled" {{ old('status', $rkb->status) == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="files" class="form-label">Dokumen Pendukung Baru</label>
                            <input type="file" class="form-control @error('files.*') is-invalid @enderror" id="files" name="files[]" multiple>
                            <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Maksimal ukuran: 2MB per file.</small>
                            @error('files.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="{{ route('rkbs.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Informasi</h5>
                </div>
                <div class="card-body">
                    <p><strong>RKB (Rencana Kegiatan dan Budget)</strong> adalah pengajuan dana untuk kegiatan tertentu.</p>
                    <p>Alur RKB:</p>
                    <ol>
                        <li>Pengajuan RKB (Status: Menunggu)</li>
                        <li>Persetujuan Finance (Status: Sebagian/Penuh)</li>
                        <li>Pencairan Dana (Input di halaman Detail RKB)</li>
                        <li>Realisasi Kegiatan (Transaksi dengan RKB)</li>
                        <li>Penutupan RKB (Status: Selesai)</li>
                    </ol>

                    <div class="alert alert-warning mt-3">
                        <strong>Perhatian!</strong> Perubahan jumlah diterima dan digunakan sebaiknya melalui transaksi, bukan edit manual.
                    </div>
                </div>
            </div>

            @if($rkb->files->count() > 0)
            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Dokumen Pendukung</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($rkb->files as $file)
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

        document.querySelector('form').addEventListener('submit', function(e) {
            rupiahInputs.forEach(function(input) {
                const value = input.value.replace(/\D/g, '');
                input.value = value;
            });
        });
    });
</script>
@endpush
