@extends('layouts.app')
@section('title', 'Barang Masuk')
@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Catat Barang Masuk (Restock)</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('incoming.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Tanggal Masuk</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Pilih Barang</label>
                    <select name="item_id" class="form-select" required>
                        <option value="">-- Pilih Barang --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">{{ $item->nama_barang }} (Stok: {{ $item->stok_saat_ini }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Jumlah Masuk</label>
                    <input type="number" name="jumlah" class="form-control" min="1" placeholder="0" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Supplier / Toko (Opsional)</label>
                    <input type="text" name="supplier" class="form-control" placeholder="Nama Toko">
                </div>
            </div>
            <button type="submit" class="btn btn-success">Simpan Stok Masuk</button>
        </form>
    </div>
</div>
@endsection