@extends('layouts.app')
@section('title', 'Edit Barang')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Edit Data Barang</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('items.update', $item->id) }}" method="POST">
                @csrf @method('PUT')

                {{-- A. DATA UTAMA --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control" value="{{ $item->nama_barang }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Kategori / Rincian Objek</label>
                    <select name="account_id" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($accounts as $acc)
                            {{-- Kita kelompokkan berdasarkan Induknya biar rapi --}}
                            <optgroup label="{{ $acc->parent->nama_akun ?? 'Lainnya' }}">
                                <option value="{{ $acc->id }}" {{ $item->account_id == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->nama_akun }}
                                </option>
                            </optgroup>
                        @endforeach
                    </select>
                    <small class="text-muted">*Pastikan kategori sesuai dengan Rincian Objek.</small>
                </div>

                <hr class="my-4">

                {{-- B. DETAIL & SETTING (Satuan, Harga, Min Stok) --}}
                <h6 class="text-muted mb-3">Detail & Peringatan Stok</h6>

                <div class="row">
                    {{-- 1. SATUAN --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Satuan</label>
                        {{-- Pakai Select biar konsisten, tapi tetap support custom value lama --}}
                        <select name="satuan" class="form-select" required>
                            @php
                                $opsi = ['Pcs','Rim','Box','Lusin','Unit','Paket','Botol','Lembar','Roll','Tube','Kotak'];
                            @endphp
                            @foreach($opsi as $op)
                                <option value="{{ $op }}" {{ $item->satuan == $op ? 'selected' : '' }}>{{ $op }}</option>
                            @endforeach
                            {{-- Jika satuan lama tidak ada di list opsi, tambahkan manual --}}
                            @if(!in_array($item->satuan, $opsi))
                                <option value="{{ $item->satuan }}" selected>{{ $item->satuan }}</option>
                            @endif
                        </select>
                    </div>

                    {{-- 2. HARGA SATUAN (Wajib ada karena Controller memintanya) --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Harga Satuan (Rp)</label>
                        <input type="number" name="harga_satuan" class="form-control" value="{{ $item->harga_satuan }}" required>
                    </div>

                    {{-- 3. STOK MINIMUM (FITUR BARU) --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-danger fw-bold">Stok Minimum (Alert)</label>
                        <input type="number" name="min_stok" class="form-control border-danger" value="{{ $item->min_stok }}" required>
                        <small class="text-muted" style="font-size: 11px;">
                            Jika sisa stok <= angka ini, tabel akan merah.
                        </small>
                    </div>
                </div>

                <div class="mt-3 text-end">
                    <a href="{{ route('items.index') }}" class="btn btn-light me-2">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
