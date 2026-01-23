@extends('layouts.app')

@section('title', 'Tambah Barang')
@section('page-heading', 'Tambah Data Barang Baru')

@section('content')
    <div class="card">
        <div class="card-body">
            {{-- ERROR HANDLING --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('items.store') }}" method="POST">
                @csrf

                {{-- === BAGIAN 1: INFORMASI DASAR === --}}
                <h6 class="text-muted mb-3">A. Informasi Barang</h6>
                <div class="row">
                    {{-- 1. NAMA BARANG --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control"
                            placeholder="Contoh: Kertas HVS A4 70gr" required>
                    </div>
                </div>

                {{-- === BAGIAN 2: HIERARKI KATEGORI (AJAX) === --}}
                <h6 class="text-muted mb-3 mt-2">B. Kategori (Kode Rekening)</h6>
                <div class="card bg-light border-0 mb-4">
                    <div class="card-body">
                        <div class="row">
                            {{-- LEVEL 1: JENIS BARANG --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label small fw-bold">1. Jenis Barang</label>
                                <select class="form-select" id="level1">
                                    <option value="" selected disabled>Pilih Jenis...</option>
                                    @foreach ($level1 as $l1)
                                        <option value="{{ $l1->id }}">{{ $l1->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- LEVEL 2: KELOMPOK --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label small fw-bold">2. Kelompok</label>
                                <select class="form-select" id="level2" disabled>
                                    <option value="" selected disabled>Pilih Jenis Dulu...</option>
                                </select>
                            </div>

                            {{-- LEVEL 3: RINCIAN OBJEK (YANG DISIMPAN KE DB) --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label small fw-bold text-primary">3. Rincian Objek</label>
                                <select class="form-select @error('account_id') is-invalid @enderror" id="level3"
                                    name="account_id" disabled required>
                                    <option value="" selected disabled>Pilih Kelompok Dulu...</option>
                                </select>
                                @error('account_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- === BAGIAN 3: DETAIL STOK & HARGA === --}}
                <h6 class="text-muted mb-3">C. Detail Stok & Harga</h6>
                <div class="row">
                    {{-- 3. SATUAN --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Satuan</label>
                        <select name="satuan" class="form-select" required>
                            <option value="Pcs">Pcs</option>
                            <option value="Rim">Rim</option>
                            <option value="Box">Box</option>
                            <option value="Lusin">Lusin</option>
                            <option value="Unit">Unit</option>
                            <option value="Paket">Paket</option>
                            <option value="Botol">Botol</option>
                            <option value="Lembar">Lembar</option>
                            <option value="Roll">Roll</option>
                            <option value="Tube">Tube</option>
                            <option value="Kotak">Kotak</option>
                        </select>
                    </div>

                    {{-- 4. HARGA SATUAN --}}
                    <div class="col-md-6 mb-3">
                        <label for="harga">Harga Satuan (Rp)</label>
                        <input type="number" name="harga_satuan" class="form-control" required placeholder="Contoh: 50000">
                    </div>

                    {{-- 5. PILIHAN SUMBER DATA --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Jenis Input Barang</label>
                        <div class="d-flex mt-2">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="sumber_data" id="radio1"
                                    value="awal" checked>
                                <label class="form-check-label" for="radio1">
                                    Saldo Awal (Data Lama)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sumber_data" id="radio2"
                                    value="baru">
                                <label class="form-check-label" for="radio2">
                                    Belanja Baru (Masuk Laporan)
                                </label>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-1" id="help-text">
                            *Saldo Awal tidak akan tercatat sebagai Transaksi Masuk hari ini.
                        </small>
                    </div>

                    {{-- 6. JUMLAH STOK --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Jumlah Stok</label>
                        <input type="number" name="stok_awal" class="form-control" placeholder="Masukan angka..."
                            min="0" required>
                    </div>

                    {{-- 7. STOK MINIMUM (ALERT) - FITUR BARU --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label text-danger fw-bold">Stok Minimum (Alert)</label>
                        <input type="number" name="min_stok" class="form-control border-danger" value="5"
                            min="0" required>
                        <small class="text-muted" style="font-size: 10px;">
                            Jika sisa stok <= angka ini, baris tabel akan merah. </small>
                    </div>
                </div>

                <div class="mt-4 border-top pt-3 text-end">
                    <a href="{{ route('items.index') }}" class="btn btn-light me-2">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">Simpan Barang</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- PENTING: Load jQuery untuk AJAX --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- 1. SCRIPT DROPDOWN BERTINGKAT (AJAX) ---

            // Saat Level 1 (Jenis) dipilih
            $('#level1').change(function() {
                var id = $(this).val();

                // Reset Level 2 & 3
                $('#level2').empty().append('<option value="" selected disabled>Loading...</option>').prop(
                    'disabled', true);
                $('#level3').empty().append(
                    '<option value="" selected disabled>Menunggu Kelompok...</option>').prop('disabled',
                    true);

                // Request Ajax ke Server
                $.get('/get-accounts/' + id, function(data) {
                    $('#level2').empty().append(
                        '<option value="" selected disabled>Pilih Kelompok</option>');
                    $.each(data, function(key, value) {
                        $('#level2').append('<option value="' + value.id + '">' + value
                            .nama_akun + '</option>');
                    });
                    $('#level2').prop('disabled', false);
                });
            });

            // Saat Level 2 (Kelompok) dipilih
            $('#level2').change(function() {
                var id = $(this).val();

                // Reset Level 3
                $('#level3').empty().append('<option value="" selected disabled>Loading...</option>').prop(
                    'disabled', true);

                // Request Ajax ke Server
                $.get('/get-accounts/' + id, function(data) {
                    $('#level3').empty().append(
                        '<option value="" selected disabled>Pilih Rincian Objek</option>');
                    $.each(data, function(key, value) {
                        $('#level3').append('<option value="' + value.id + '">' + value
                            .nama_akun + '</option>');
                    });
                    $('#level3').prop('disabled', false);
                });
            });


            // --- 2. SCRIPT RADIO BUTTON TEXT ---
            const radio1 = document.getElementById('radio1');
            const radio2 = document.getElementById('radio2');
            const helpText = document.getElementById('help-text');

            radio1.addEventListener('change', () => {
                helpText.innerText = "*Saldo Awal tidak akan tercatat sebagai Transaksi Masuk hari ini.";
                helpText.className = "text-muted d-block mt-1";
            });

            radio2.addEventListener('change', () => {
                helpText.innerText = "*Barang ini akan otomatis dicatat ke Riwayat Barang Masuk hari ini.";
                helpText.className = "text-success fw-bold d-block mt-1";
            });
        });
    </script>
@endpush
