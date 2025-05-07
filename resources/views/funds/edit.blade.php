@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Dana</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('funds.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('funds.update', $fund) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Dana</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $fund->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Tipe Dana</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="" disabled>Pilih Tipe Dana</option>
                                <option value="rekening_utama" {{ old('type', $fund->type) == 'rekening_utama' ? 'selected' : '' }}>Rekening Utama</option>
                                <option value="kas_kecil" {{ old('type', $fund->type) == 'kas_kecil' ? 'selected' : '' }}>Kas Kecil</option>
                                <option value="dana_operasional" {{ old('type', $fund->type) == 'dana_operasional' ? 'selected' : '' }}>Dana Operasional</option>
                                <option value="dana_rkb" {{ old('type', $fund->type) == 'dana_rkb' ? 'selected' : '' }}>Dana RKB</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="initial_balance" class="form-label">Saldo Awal</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control rupiah @error('initial_balance') is-invalid @enderror" id="initial_balance" name="initial_balance" value="{{ old('initial_balance', number_format($fund->initial_balance, 0, ',', '.')) }}" required>
                                @error('initial_balance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Keterangan</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $fund->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="{{ route('funds.index') }}" class="btn btn-secondary">Batal</a>
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
                    <p><strong>Rekening Utama</strong>: Rekening bank utama divisi HCGA.</p>
                    <p><strong>Kas Kecil</strong>: Dana tunai untuk keperluan sehari-hari.</p>
                    <p><strong>Dana Operasional</strong>: Dana rutin bulanan dari Finance.</p>
                    <p><strong>Dana RKB</strong>: Dana khusus untuk kegiatan (RKB).</p>
                    <div class="alert alert-warning mt-3">
                        <strong>Perhatian!</strong> Mengubah saldo awal akan memengaruhi perhitungan saldo saat ini.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Format input saldo awal
    document.addEventListener('DOMContentLoaded', function() {
        var initialBalanceInput = document.getElementById('initial_balance');
        initialBalanceInput.addEventListener('keyup', function(e) {
            var value = this.value.replace(/\D/g, '');
            this.value = formatRupiah(value);
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            var value = initialBalanceInput.value.replace(/\D/g, '');
            initialBalanceInput.value = value;
        });
    });
</script>
@endpush
