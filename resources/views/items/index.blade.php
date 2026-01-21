@extends('layouts.app')

@section('title', 'Master Barang')
@section('page-heading', 'Daftar Barang')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Data Stok Barang</h5>

            <div class="d-flex gap-2 flex-wrap">
                {{-- Group 1: Export & Import --}}
                <a href="{{ route('items.excel') }}" class="btn btn-success btn-sm" title="Download Excel">
                    <i class="bi bi-file-earmark-excel"></i> Excel
                </a>

                <a href="{{ route('items.print') }}" target="_blank" class="btn btn-danger btn-sm" title="Cetak PDF">
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                </a>

                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="bi bi-file-arrow-up"></i> Import
                </button>

                {{-- Group 2: Aksi Berisiko (Hapus Semua) --}}
                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteAll()"
                    title="Hapus Seluruh Data">
                    <i class="bi bi-trash"></i> Hapus Semua
                </button>

                {{-- Group 3: Tambah Data --}}
                <a href="{{ route('items.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Tambah Baru
                </a>
            </div>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="table1">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Satuan</th>
                            <th class="text-center">Stok</th>
                            <th class="text-end">Harga Satuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr class="align-middle">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nama_barang }}</td>
                                <td><span class="badge bg-secondary">{{ $item->category->nama_kategori ?? '-' }}</span></td>
                                <td>{{ $item->satuan }}</td>

                                <td class="text-center fw-bold">{{ $item->stok_saat_ini }}</td>

                                <td class="text-end">
                                    Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                </td>

                                <td>
                                    <div class="d-flex gap-1">
                                        {{-- Tombol Edit --}}
                                        <a href="{{ route('items.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        {{-- === BAGIAN INI YANG DIUBAH (Form Hapus Satuan) === --}}
                                        <form id="delete-form-{{ $item->id }}"
                                              action="{{ route('items.destroy', $item->id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')

                                            {{-- Button type="button" dan onclick ke fungsi SweetAlert --}}
                                            <button type="button" class="btn btn-sm btn-danger" title="Hapus"
                                                    onclick="confirmDelete('delete-form-{{ $item->id }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        {{-- ================================================= --}}

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Form Hidden Hapus Semua --}}
    <form id="delete-all-form" action="{{ route('items.deleteAll') }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- Modal Import --}}
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
                            <small>
                                <i class="bi bi-info-circle"></i> Pastikan format CSV/Excel memiliki header:<br>
                                <b>nama_barang, kategori_id, satuan, stok_saat_ini, harga_satuan</b>
                            </small>
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

    {{-- SCRIPT JAVASCRIPT --}}
    {{-- Kita gunakan @push('scripts') agar script ini dilempar ke bawah body di layout utama --}}
    @push('scripts')
    <script>
        // === 1. Fungsi Hapus SATU Barang ===
        function confirmDelete(formId) {
            Swal.fire({
                title: 'Hapus barang ini?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

        // === 2. Fungsi Hapus SEMUA Barang (Yang sudah ada sebelumnya) ===
        function confirmDeleteAll() {
            Swal.fire({
                title: 'Hapus SEMUA Barang?',
                text: "Semua data barang akan hilang permanen! Stok akan jadi 0.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'YAKIN?',
                        text: "Data yang dihapus tidak bisa dikembalikan!",
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus Data!',
                        cancelButtonText: 'Kembali'
                    }).then((res2) => {
                        if (res2.isConfirmed) {
                            document.getElementById('delete-all-form').submit();
                        }
                    })
                }
            })
        }
    </script>
    @endpush

@endsection
