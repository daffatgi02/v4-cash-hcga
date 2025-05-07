@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Tambah Transaksi</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Tipe Transaksi</label>
                                        <select class="form-select @error('type') is-invalid @enderror" id="type"
                                            name="type" required>
                                            <option value="" selected disabled>Pilih Tipe</option>
                                            <option value="pemasukan" {{ old('type') == 'pemasukan' ? 'selected' : '' }}>
                                                Pemasukan</option>
                                            <option value="pengeluaran"
                                                {{ old('type') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="transaction_date" class="form-label">Tanggal Transaksi</label>
                                        <input type="date"
                                            class="form-control @error('transaction_date') is-invalid @enderror"
                                            id="transaction_date" name="transaction_date"
                                            value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                                        @error('transaction_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fund_id" class="form-label">Dana</label>
                                        <select class="form-select @error('fund_id') is-invalid @enderror" id="fund_id"
                                            name="fund_id" required>
                                            <option value="" selected disabled>Pilih Dana</option>
                                            @foreach ($funds as $fund)
                                                <option value="{{ $fund->id }}"
                                                    {{ old('fund_id') == $fund->id || request('fund_id') == $fund->id ? 'selected' : '' }}>
                                                    {{ $fund->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('fund_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Kategori</label>
                                        <select class="form-select @error('category_id') is-invalid @enderror"
                                            id="category_id" name="category_id" required>
                                            <option value="" selected disabled>Pilih Kategori</option>
                                            <optgroup label="Pemasukan">
                                                @foreach ($categories->where('type', 'pemasukan') as $category)
                                                    <option value="{{ $category->id }}" data-type="pemasukan"
                                                        {{ old('category_id') == $category->id || request('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}</option>
                                                @endforeach
                                            </optgroup>
                                            <optgroup label="Pengeluaran">
                                                @foreach ($categories->where('type', 'pengeluaran') as $category)
                                                    <option value="{{ $category->id }}" data-type="pengeluaran"
                                                        {{ old('category_id') == $category->id || request('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        @error('category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="amount" class="form-label">Jumlah</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control rupiah @error('amount') is-invalid @enderror"
                                        id="amount" name="amount" value="{{ old('amount', '0') }}" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Keterangan</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="rkb_id" class="form-label">RKB (opsional)</label>
                                        <select class="form-select @error('rkb_id') is-invalid @enderror" id="rkb_id"
                                            name="rkb_id">
                                            <option value="">-- Tidak Ada --</option>
                                            @foreach ($rkbs as $rkb)
                                                <option value="{{ $rkb->id }}"
                                                    {{ old('rkb_id') == $rkb->id || request('rkb_id') == $rkb->id ? 'selected' : '' }}>
                                                    {{ $rkb->title }}</option>
                                            @endforeach
                                        </select>
                                        @error('rkb_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="temporary_fund_id" class="form-label">Dana Talangan (opsional)</label>
                                        <select class="form-select @error('temporary_fund_id') is-invalid @enderror"
                                            id="temporary_fund_id" name="temporary_fund_id">
                                            <option value="">-- Tidak Ada --</option>
                                            @foreach (App\Models\TemporaryFund::where('status', 'outstanding')->get() as $tempFund)
                                                <option value="{{ $tempFund->id }}"
                                                    {{ old('temporary_fund_id') == $tempFund->id ? 'selected' : '' }}>
                                                    {{ $tempFund->sourceFund->name }} &rarr;
                                                    {{ $tempFund->targetFund->name }} (Rp
                                                    {{ number_format($tempFund->amount, 0, ',', '.') }})</option>
                                            @endforeach
                                        </select>
                                        @error('temporary_fund_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" value="1" id="is_settlement"
                                    name="is_settlement" {{ old('is_settlement') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_settlement">
                                    Pengembalian Dana Talangan
                                </label>
                            </div>

                            <div id="staffSection" class="border p-3 mb-3 rounded">
                                <h5>Informasi Staff</h5>

                                <div class="mb-3">
                                    <label for="staff_id" class="form-label">Staff</label>
                                    <select class="form-select @error('staff_id') is-invalid @enderror" id="staff_id"
                                        name="staff_id">
                                        <option value="">-- Pilih Staff --</option>
                                        @foreach ($staff as $person)
                                            <option value="{{ $person->id }}"
                                                {{ old('staff_id') == $person->id || request('staff_id') == $person->id ? 'selected' : '' }}>
                                                {{ $person->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('staff_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="recipient_name" class="form-label">Nama Penerima</label>
                                    <input type="text"
                                        class="form-control @error('recipient_name') is-invalid @enderror"
                                        id="recipient_name" name="recipient_name" value="{{ old('recipient_name') }}">
                                    @error('recipient_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="recipient_type" class="form-label">Tipe Penerima</label>
                                    <select class="form-select @error('recipient_type') is-invalid @enderror"
                                        id="recipient_type" name="recipient_type">
                                        <option value="">-- Tidak Ada --</option>
                                        <option value="vendor" {{ old('recipient_type') == 'vendor' ? 'selected' : '' }}>
                                            Vendor</option>
                                        <option value="staff" {{ old('recipient_type') == 'staff' ? 'selected' : '' }}>
                                            Staff</option>
                                        <option value="finance"
                                            {{ old('recipient_type') == 'finance' ? 'selected' : '' }}>Finance</option>
                                    </select>
                                    @error('recipient_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-check mb-3" id="staffDebtSection">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="create_staff_debt" name="create_staff_debt"
                                        {{ old('create_staff_debt') || request('create_staff_debt') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="create_staff_debt">
                                        Catat sebagai Piutang Staff
                                    </label>
                                </div>

                                <div id="purchaseDetailsSection" style="display: none;">
                                    <div class="mb-3">
                                        <label for="purchase_details" class="form-label">Detail Pembelian</label>
                                        <textarea class="form-control @error('purchase_details') is-invalid @enderror" id="purchase_details"
                                            name="purchase_details" rows="2">{{ old('purchase_details') }}</textarea>
                                        @error('purchase_details')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="files" class="form-label">Bukti Transaksi</label>
                                <input type="file" class="form-control @error('files.*') is-invalid @enderror"
                                    id="files" name="files[]" multiple>
                                <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Maksimal ukuran: 2MB per
                                    file.</small>
                                @error('files.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Batal</a>
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
                        <p><strong>Transaksi</strong> adalah aktivitas pemasukan atau pengeluaran dana.</p>
                        <p><strong>Pemasukan</strong>: Dana yang masuk ke rekening atau kas HCGA.</p>
                        <p><strong>Pengeluaran</strong>: Dana yang keluar dari rekening atau kas HCGA.</p>
                        <p>Setiap transaksi wajib memiliki:</p>
                        <ul>
                            <li>Tipe (Pemasukan/Pengeluaran)</li>
                            <li>Dana (asal/tujuan dana)</li>
                            <li>Kategori (jenis pemasukan/pengeluaran)</li>
                            <li>Jumlah</li>
                            <li>Tanggal transaksi</li>
                            <li>Keterangan</li>
                        </ul>
                        <p>Pastikan kategori sesuai dengan tipe transaksi.</p>
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

            // Kategori filter berdasarkan tipe transaksi
            const typeSelect = document.getElementById('type');
            const categorySelect = document.getElementById('category_id');

            typeSelect.addEventListener('change', function() {
                const type = this.value;
                const options = categorySelect.querySelectorAll('option');

                options.forEach(function(option) {
                    if (option.value === '') return;
                    const optionType = option.getAttribute('data-type');

                    if (optionType === type) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                });

                // Reset kategori jika tipe tidak sesuai
                const selectedOption = categorySelect.selectedOptions[0];
                if (selectedOption && selectedOption.getAttribute('data-type') !== type) {
                    categorySelect.value = '';
                }
            });

            // Tampilkan field piutang staff jika checkbox dicentang
            const createStaffDebt = document.getElementById('create_staff_debt');
            const purchaseDetailsSection = document.getElementById('purchaseDetailsSection');

            createStaffDebt.addEventListener('change', function() {
                if (this.checked) {
                    purchaseDetailsSection.style.display = 'block';
                } else {
                    purchaseDetailsSection.style.display = 'none';
                }
            });

            // Jalankan sekali saat load
            if (createStaffDebt.checked) {
                purchaseDetailsSection.style.display = 'block';
            }

            // Trigger change event pada load jika sudah ada value
            if (typeSelect.value) {
                typeSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endpush
