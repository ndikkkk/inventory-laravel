@extends('layouts.app')

@section('title', 'Input Barang Masuk')
@section('page-heading', 'Form Barang Masuk')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Restock Barang</h4>
    </div>
    <div class="card-body">

        {{-- Tampilkan Error Validasi --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('incoming.store') }}" method="POST">
            @csrf
            <div class="row">
                {{-- TANGGAL --}}
                <div class="col-md-6 mb-3">
                    <label>Tanggal Masuk</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                {{-- PILIH BARANG (Dropdown menyimpan data harga) --}}
                <div class="col-md-6 mb-3">
                    <label>Pilih Barang</label>
                    <select name="item_id" id="pilih-barang" class="form-select" required>
                        <option value="" data-harga="0">-- Pilih Barang --</option>
                        @foreach($items as $item)
                            {{-- Kita simpan harga di atribut 'data-harga' --}}
                            <option value="{{ $item->id }}" data-harga="{{ $item->harga_satuan }}">
                                {{ $item->nama_barang }} (Sisa: {{ $item->stok_saat_ini }})
                            </option>
                        @endforeach
                    </select>
                    {{-- Info Harga Satuan (Muncul otomatis) --}}
                    <small id="info-harga-satuan" class="text-muted fst-italic mt-1 d-block"></small>
                </div>

                {{-- INPUT JUMLAH --}}
                <div class="col-12 mb-3">
                    <label>Jumlah Masuk</label>
                    <input type="number" name="jumlah" id="input-jumlah" class="form-control" min="1" placeholder="Masukkan jumlah barang..." required>

                    {{-- HASIL PERHITUNGAN TOTAL (Tampil disini) --}}
                    <div id="box-total" class="alert alert-light-primary color-primary mt-2 d-none">
                        <i class="bi bi-calculator-fill me-2"></i>
                        Estimasi Total Nilai: <span class="fw-bold fs-5" id="text-total-rupiah">Rp 0</span>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('items.index') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-success">Simpan Stok</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Definisi Elemen
        const selectBarang = document.getElementById('pilih-barang');
        const inputJumlah  = document.getElementById('input-jumlah');
        const infoHarga    = document.getElementById('info-harga-satuan');
        const boxTotal     = document.getElementById('box-total');
        const textTotal    = document.getElementById('text-total-rupiah');

        // 2. Fungsi Format Rupiah
        const formatRupiah = (angka) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(angka);
        };

        // 3. Fungsi Utama Hitung
        function hitungOtomatis() {
            // Ambil harga dari atribut 'data-harga' di <option> yang dipilih
            // Bukan dari input manual
            const selectedOption = selectBarang.options[selectBarang.selectedIndex];
            const hargaSatuan    = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
            const jumlah         = parseFloat(inputJumlah.value) || 0;

            // Update Info Harga Satuan Kecil
            if (hargaSatuan > 0) {
                infoHarga.innerText = "Harga Satuan: " + formatRupiah(hargaSatuan);
            } else {
                infoHarga.innerText = "";
            }

            // Hitung Total
            const total = hargaSatuan * jumlah;

            // Tampilkan Hasil Total di Bawah Input Jumlah
            if (total > 0) {
                boxTotal.classList.remove('d-none'); // Munculkan kotak
                textTotal.innerText = formatRupiah(total);
            } else {
                boxTotal.classList.add('d-none'); // Sembunyikan kotak jika 0
            }
        }

        // 4. Pasang Event Listener (Jalankan saat dropdown berubah ATAU jumlah diketik)
        selectBarang.addEventListener('change', hitungOtomatis);
        inputJumlah.addEventListener('input', hitungOtomatis);
    });
</script>
@endpush
