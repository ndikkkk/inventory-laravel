@extends('layouts.app')

@section('title', 'Master Barang')
@section('page-heading', 'Daftar Barang')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="card-title">Data Stok Barang</h5>
    <div>
        {{-- TOMBOL EXCEL --}}
            <a href="{{ route('items.excel') }}" class="btn btn-success me-2">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </a>

            {{-- TOMBOL PDF --}}
            <a href="{{ route('items.print') }}" target="_blank" class="btn btn-danger me-2">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>

        <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-excel"></i> Import CSV
        </button>

        <a href="{{ route('items.create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> Tambah Manual
        </a>
    </div>
</div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped" id="table1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th class="text-center">Stok Saat Ini</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td><span class="badge bg-secondary">{{ $item->category->nama_kategori }}</span></td>
                        <td>{{ $item->satuan }}</td>
                        <td class="text-center fw-bold">{{ $item->stok_saat_ini }}</td>
                        <td>
                            <a href="{{ route('items.edit', $item->id) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Import Data Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('items.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <small>Pastikan format CSV/Excel memiliki header: <b>nama_barang, kategori, satuan, stok_awal</b></small>
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label">Pilih File CSV/Excel</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">Import Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
