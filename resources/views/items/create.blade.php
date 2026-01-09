@extends('layouts.app')

@section('title', 'Tambah Barang')
@section('page-heading', 'Tambah Data Barang Baru')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('items.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Kertas HVS A4" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Satuan</label>
                    <select name="satuan" class="form-select" required>
                        <option value="Pcs">Pcs</option>
                        <option value="Rim">Rim</option>
                        <option value="Box">Box</option>
                        <option value="Lusin">Lusin</option>
                        <option value="Unit">Unit</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Stok Awal (Per Hari Ini)</label>
                    <input type="number" name="stok_awal_2026" class="form-control" value="0" min="0" required>
                    <small class="text-muted">Stok ini akan menjadi stok awal sistem.</small>
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
