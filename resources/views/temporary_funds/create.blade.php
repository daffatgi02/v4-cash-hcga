@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Tambah Dana Talangan</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('temporary-funds.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('temporary-funds.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="source_fund_id" class="form-label">Dana Asal</label>
                                    <select class="form-select @error('source_fund_id') is-invalid @enderror" id="source_fund_id" name="source_fund_id" required>
                                        <option value="" selected disabled>Pilih Dana Asal</option>
                                        @foreach($funds as $fund)
                                        <option value="{{ $fund->id }}" {{ old('source_fund_id') == $fund->id ? 'selected' : '' }}>{{ $fund->name }} (Rp {{ number_format($fund->getCurrentBalance(), 0, ',', '.') }})</option>
                                        @endforeach
                                    </select>
                                    @error('source_fund_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="target_fund_id" class="form-label">Dana Tujuan</label>
                                    <select class="form-select @error('target_fund_id') is-invalid @enderror" id="target_fund_id" name="target_fund_id" required>
                                        <option value="" selected disabled>Pilih Dana Tujuan</option>
                                        @foreach($funds as $fund)
                                        <option value="{{ $fund->id }}" {{ old('target_fund_id') == $fund->id ? 'selected' : '' }}>{{ $fund->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('target_fund_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="rkb_id" class="form-label">RKB (opsional)</label>
                            <select class="form-select @error('rkb_id') is-invalid @enderror" id="rkb_id" name="rkb_id">
                                <option value="">-- Tidak Ada --</option>
                                @foreach($rkbs as $rkb)
                                <option value="{{ $rkb->id }}" {{ old('rkb_id') == $rkb->id || request('rkb_id') == $rkb->id ? 'selected' : '' }}>{{ $rkb->title }}</option>
                                @endforeach
                            </select>
                            @error('rkb_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control rupiah @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', '0') }}" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="loan_date" class="form-label">Tanggal Talangan</label>
                            <input type="date" class="form-control @error('loan_date') is-invalid @enderror" id="loan_date" name="loan_date" value="{{ old('loan_date', date('Y-m-d')) }}" required>
                            @error('loan_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Keterangan</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                            @error('description')
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
                            <a href="{{ route('temporary-funds.index') }}" class="btn btn-secondary">Batal</a>
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
                    <p><strong>Dana Talangan</strong> adalah penggunaan dana dari satu sumber untuk menutupi kebutuhan dana pada sumber lain secara sementara.</p>
                    <p>Misalnya:</p>
                    <ul>
                        <li>Menggunakan dana operasional untuk RKB yang belum cair</li>
                        <li>Menggunakan kas kecil untuk keperluan mendesak</li>
                    </ul>
                    <p>Dana talangan harus dikembalikan ke sumber asalnya setelah kebutuhan terpenuhi.</p>
                    <div class="alert alert-warning">
                        <strong>Perhatian!</strong> Pastikan dana asal dan tujuan tidak sama.
                    </div>
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
        const rupiahInput = document.getElementById('amount');
        rupiahInput.addEventListener('keyup', function(e) {
            const value = this.value.replace(/\D/g, '');
            this.value = formatRupiah(value);
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            const value = rupiahInput.value.replace(/\D/g, '');
            rupiahInput.value = value;
        });

        // Validasi dana asal dan tujuan tidak boleh sama
        const sourceFundSelect = document.getElementById('source_fund_id');
        const targetFundSelect = document.getElementById('target_fund_id');

        targetFundSelect.addEventListener('change', function() {
            if (this.value === sourceFundSelect.value) {
                alert('Dana asal dan tujuan tidak boleh sama!');
                this.value = '';
            }
        });

        sourceFundSelect.addEventListener('change', function() {
            if (this.value === targetFundSelect.value) {
                alert('Dana asal dan tujuan tidak boleh sama!');
                targetFundSelect.value = '';
            }
        });
    });
</script>
@endpush
