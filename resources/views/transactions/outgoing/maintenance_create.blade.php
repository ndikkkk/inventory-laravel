@extends('layouts.app')
@section('title', 'Input Pemeliharaan')
@section('page-heading', 'Form Pemeliharaan Kendaraan/Mesin')

@section('content')
{{-- CSS KHUSUS UNTUK MENGHILANGKAN PANAH (SPINNER) PADA INPUT NUMBER --}}
<style>
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    /* Firefox */
    input[type=number] {
      -moz-appearance: textfield;
    }
</style>
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Catat Servis & Pemeliharaan</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('outgoing.store_maintenance') }}" method="POST">
            @csrf
            <div class="row">
                {{-- Kolom Kiri --}}
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Tanggal Servis</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label>Nama Jasa / Sparepart</label>
                        <input type="text" name="nama_item" class="form-control" placeholder="Contoh: Ganti Oli Innova, Service AC" required>
                        <small class="text-muted">Item ini akan otomatis masuk kategori "Pemeliharaan Mesin"</small>
                    </div>

                    <div class="mb-3">
                        <label>Biaya (Rp)</label>
                        <input type="number" name="harga" class="form-control" placeholder="Masukkan nominal rupiah" required>
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="col-md-6">
                    <div class="mb-3">
                        <label>Bidang / Divisi Pengguna</label>
                        <select name="division_id" class="form-select" required>
                            <option value="">-- Pilih Bidang --</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div->id }}">{{ $div->nama_bidang }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kolom KM --}}
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label>KM Saat Ini <small class="text-muted">(Jika Kendaraan)</small></label>
                            {{-- HAPUS 'required' --}}
                            <input type="number" name="km_saat_ini" class="form-control" placeholder="Contoh: 50000">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="text-danger fw-bold">Ingatkan KM Berikutnya</label>
                            <input type="number" name="km_berikutnya" class="form-control" placeholder="Contoh: 55000">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('outgoing.index') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Data Pemeliharaan</button>
            </div>
        </form>
    </div>
</div>
@endsection