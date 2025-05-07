@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Tambah RKB</h1>
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
                    <form action="{{ route('rkbs.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Judul RKB</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi Kegiatan</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="requested_amount" class="form-label">Jumlah Diajukan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control rupiah @error('requested_amount') is-invalid @enderror" id="requested_amount" name="requested_amount" value="{{ old('requested_amount', '0') }}" required>
                                @error('requested_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="approved_amount" class="form-label">Jumlah Disetujui</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control rupiah @error('approved_amount') is-invalid @enderror" id="approved_amount" name="approved_amount" value="{{ old('approved_amount', '0') }}">
                                @error('approved_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="request_date" class="form-label">Tanggal Pengajuan</label>
                                    <input type="date" class="form-control @error('request_date') is-invalid @enderror" id="request_date" name="request_date" value="{{ old('request_date', date('Y-m-d')) }}" required>
                                    @error('request_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="approval_date" class="form-label">Tanggal Persetujuan</label>
                                    <input type="date" class="form-control @error('approval_date') is-invalid @enderror" id="approval_date" name="approval_date" value="{{ old('approval_date') }}">
                                    @error('approval_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="" selected disabled>Pilih Status</option>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                <option value="partial" {{ old('status') == 'partial' ? 'selected' : '' }}>Sebagian</option>
                                <option value="full" {{ old('status') == 'full' ? 'selected' : '' }}>Penuh</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="files" class="form-label">Dokumen Pendukung</label>
                            <input type="file" class="form-control @error('files.*') is-invalid @enderror" id="files" name="files[]" multiple>
                            <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Maksimal ukuran: 2MB per file.</small>
                            @error('files.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Simpan</button>
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
                </div>
            </div>
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
