@extends('layouts.app')

@section('title', 'Input Barang Masuk')
@section('page-heading', 'Form Barang Masuk')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Barang Masuk / Restock</h4>
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
                {{-- 1. TANGGAL --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Tanggal Masuk</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                {{-- 2. PILIH BARANG --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Pilih Barang</label>
                    <select name="item_id" id="pilih-barang" class="form-select select2" required>
                        <option value="" data-harga="0">-- Pilih Barang --</option>
                        @foreach($items as $item)
                            {{-- Simpan harga master di data-harga buat auto-fill --}}
                            <option value="{{ $item->id }}" data-harga="{{ $item->harga_satuan }}">
                                {{ $item->nama_barang }} (Sisa Stok: {{ $item->stok_saat_ini }})
                            </option>
                        @endforeach
                    </select>
                    {{-- Info Harga Master (Hanya Referensi) --}}
                    <small id="info-harga-master" class="text-muted fst-italic mt-1 d-block"></small>
                </div>
            </div>

            <div class="row">
                {{-- 3. INPUT JUMLAH --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Jumlah Masuk</label>
                    <input type="number" name="jumlah" id="input-jumlah" class="form-control" min="1" placeholder="0" required>
                </div>

                {{-- 4. INPUT HARGA BELI (WAJIB ADA UNTUK RUMUS RATA-RATA) --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-success">Harga Beli Satuan (Sesuai Nota)</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="harga_satuan" id="input-harga" class="form-control border-success" placeholder="0" required>
                    </div>
                    <small class="text-muted" style="font-size: 11px;">
                        *Jika harga beda dengan master, sistem otomatis menghitung rata-rata.
                    </small>
                </div>
            </div>

            {{-- 5. KETERANGAN (Opsional) --}}
            <div class="mb-3">
                <label class="form-label">Keterangan / Asal Toko</label>
                <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: Belanja di Toko Merah, Nota No. 123"></textarea>
            </div>

            {{-- BOX TOTAL ESTIMASI --}}
            <div id="box-total" class="alert alert-success d-none mt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-calculator-fill me-2"></i> Total Nominal Belanja:</span>
                    <span class="fw-bold fs-4" id="text-total-rupiah">Rp 0</span>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('incoming.index') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-success px-4">Simpan Barang Masuk</button>
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
        const inputHarga   = document.getElementById('input-harga'); // Input Harga Baru
        const infoMaster   = document.getElementById('info-harga-master');
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

        // 3. Saat Barang Dipilih -> Auto Isi Harga
        selectBarang.addEventListener('change', function() {
            const selectedOption = selectBarang.options[selectBarang.selectedIndex];
            const hargaMaster    = parseFloat(selectedOption.getAttribute('data-harga')) || 0;

            if (hargaMaster > 0) {
                // Tampilkan Info Harga Master
                infoMaster.innerText = "Harga di Master saat ini: " + formatRupiah(hargaMaster);

                // AUTO FILL: Masukkan harga master ke input harga beli (biar user gak ngetik ulang kalau sama)
                inputHarga.value = Math.round(hargaMaster);
            } else {
                infoMaster.innerText = "";
                inputHarga.value = "";
            }
            hitungTotal(); // Hitung ulang total
        });

        // 4. Fungsi Hitung Total (Jumlah * Harga Inputan)
        function hitungTotal() {
            const jumlah = parseFloat(inputJumlah.value) || 0;
            const harga  = parseFloat(inputHarga.value) || 0; // Ambil dari input manual
            const total  = jumlah * harga;

            if (total > 0) {
                boxTotal.classList.remove('d-none');
                textTotal.innerText = formatRupiah(total);
            } else {
                boxTotal.classList.add('d-none');
            }
        }

        // 5. Event Listener untuk Hitung Realtime
        inputJumlah.addEventListener('input', hitungTotal);
        inputHarga.addEventListener('input', hitungTotal);

        // ============================================================
        // === 6. FITUR BARU: AUTO-SELECT DARI URL (LOGIKA TAMBAHAN) ===
        // ============================================================
        // Cek apakah ada parameter ?item_id=... di URL (dari link Suggestion)
        const urlParams = new URLSearchParams(window.location.search);
        const preSelectedId = urlParams.get('item_id');

        if (preSelectedId) {
            // 1. Set nilai dropdown ke ID barang tersebut
            selectBarang.value = preSelectedId;

            // 2. Trigger event 'change' secara manual
            // Ini PENTING: Supaya logika nomor 3 (Auto Isi Harga) di atas langsung jalan
            selectBarang.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush
