@extends('layouts.app')
@section('title', 'Barang Keluar')
@section('content')
<div class="card border-danger">
    <div class="card-header">
        <h4 class="card-title text-danger">
            {{-- Ubah judul sesuai Role --}}
            {{ Auth::user()->role == 'admin' ? 'Catat Pengambilan Barang' : 'Form Pengajuan Barang' }}
        </h4>
    </div>
    <div class="card-body">

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('outgoing.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Tanggal Ambil / Request</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                {{-- PILIH BARANG (DENGAN GROUPING) --}}
                <div class="col-md-6 mb-3">
                    <label>Barang yang Diminta</label>
                    <select name="item_id" id="pilih-barang" class="form-select" required>
                        <option value="" data-satuan="-">-- Pilih Barang --</option>
                        
                        {{-- Grouping Barang Berdasarkan Akun (Kategori) --}}
                        @php
                            $groupedItems = $items->groupBy(function($item) {
                                return $item->account->nama_akun ?? 'Lainnya';
                            });
                        @endphp

                        @foreach($groupedItems as $kategori => $listBarang)
                            <optgroup label="{{ $kategori }}">
                                @foreach($listBarang as $item)
                                    <option value="{{ $item->id }}" data-satuan="{{ $item->satuan }}">
                                        {{ $item->nama_barang }} (Sisa: {{ $item->stok_saat_ini }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach

                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Bidang / Divisi Pengambil</label>
                    <select name="division_id" class="form-select" required>
                        {{-- Jika Admin, muncul pilihan default. Jika Bidang, langsung ke pilihannya --}}
                        @if(Auth::user()->role == 'admin')
                            <option value="">-- Siapa yang mengambil? --</option>
                        @endif

                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ count($divisions) == 1 ? 'selected' : '' }}>
                                {{ $div->nama_bidang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Jumlah</label>
                    {{-- UBAH JADI INPUT GROUP (TERBAGI 2) --}}
                    <div class="input-group">
                        {{-- Bagian Kiri: Input Angka --}}
                        <input type="number" name="jumlah" class="form-control" min="1" placeholder="0" required>

                        {{-- Bagian Kanan: Teks Satuan (Readonly) --}}
                        <span class="input-group-text bg-light fw-bold" id="tampil-satuan">
                            -
                        </span>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-danger">
                    {{-- Ubah Teks Tombol sesuai Role --}}
                    @if(Auth::user()->role == 'admin')
                        <i class="bi bi-check-circle"></i> Simpan & Kurangi Stok (ACC)
                    @else
                        <i class="bi bi-send"></i> Kirim Pengajuan (Menunggu ACC)
                    @endif
                </button>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPT KHUSUS UNTUK GANTI SATUAN --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Ambil elemen dropdown dan span satuan
        const dropdown = document.getElementById('pilih-barang');
        const spanSatuan = document.getElementById('tampil-satuan');

        // Saat pilihan barang berubah
        dropdown.addEventListener('change', function() {
            // Ambil option yang sedang dipilih
            const selectedOption = this.options[this.selectedIndex];

            // Ambil data-satuan dari option tersebut
            const satuan = selectedOption.getAttribute('data-satuan');

            // Update teks di kotak kanan
            spanSatuan.textContent = satuan ? satuan : '-';
        });
    });
</script>
@endsection