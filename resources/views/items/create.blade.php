@extends('layouts.app')

@section('title', 'Tambah Barang')
@section('page-heading', 'Tambah Data Barang Baru')

@section('content')
    <div class="card">
        <div class="card-body">
            {{-- BAGIAN INI UNTUK MENAMPILKAN ERROR JIKA GAGAL SIMPAN --}}
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
                <div class="row">
                    {{-- 1. NAMA BARANG --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Kertas HVS A4" required>
                    </div>

                    {{-- 2. KATEGORI --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

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

                    {{-- 5. PILIHAN SUMBER DATA (REVISI NO. 4) --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Jenis Input Barang</label>
                        <div class="d-flex mt-2">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="sumber_data" id="radio1" value="awal" checked>
                                <label class="form-check-label" for="radio1">
                                    Saldo Awal (Data Lama)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sumber_data" id="radio2" value="baru">
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
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jumlah Stok</label>
                        <input type="number" name="stok_awal" class="form-control" value="0" min="0" required>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Simpan Barang</button>
                    <a href="{{ route('items.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Script sederhana untuk mengubah teks keterangan saat radio button diklik
    document.addEventListener('DOMContentLoaded', function() {
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