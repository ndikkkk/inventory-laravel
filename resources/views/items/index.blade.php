@extends('layouts.app')

@section('title', 'Master Barang')
@section('page-heading', 'Daftar Barang')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Data Stok Barang</h5>

            <div class="d-flex gap-2 flex-wrap">
                <form action="{{ route('items.index') }}" method="GET" class="d-flex align-items-center gap-2">
                    {{-- Simpan status sorting biar gak ilang saat ganti limit/filter --}}
                    <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                    <input type="hidden" name="direction" value="{{ request('direction') }}">

                    {{-- 1. Limit Baris --}}
                    <div class="d-flex align-items-center">
                        <label class="me-2 text-nowrap">Tampilkan</label>
                        <select name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>

                    {{-- 2. Filter Kategori --}}
                    <div class="d-flex align-items-center">
                        <select name="category_id" class="form-select form-select-sm w-auto" onchange="this.form.submit()"
                            style="min-width: 150px;">
                            <option value="">-- Semua Kategori --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nama_akun }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tombol Reset --}}
                    @if (request('category_id') || request('sort_by'))
                        <a href="{{ route('items.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </form>

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

                {{-- Group 2: Aksi Berisiko --}}
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

                            {{-- SORT: NAMA BARANG --}}
                            <th>
                                <a href="{{ route('items.index', array_merge(request()->all(), ['sort_by' => 'nama_barang', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="text-decoration-none text-dark d-flex justify-content-between align-items-center">
                                    Nama Barang
                                    @if (request('sort_by') == 'nama_barang')
                                        <i
                                            class="bi bi-sort-{{ request('direction') == 'asc' ? 'alpha-down' : 'alpha-down-alt' }} text-primary"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted"
                                            style="font-size: 10px; opacity: 0.5;"></i>
                                    @endif
                                </a>
                            </th>

                            {{-- SORT: KATEGORI --}}
                            <th>
                                <a href="{{ route('items.index', array_merge(request()->all(), ['sort_by' => 'kategori', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="text-decoration-none text-dark d-flex justify-content-between align-items-center">
                                    Kategori
                                    @if (request('sort_by') == 'kategori')
                                        <i
                                            class="bi bi-sort-{{ request('direction') == 'asc' ? 'down' : 'up' }} text-primary"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted"
                                            style="font-size: 10px; opacity: 0.5;"></i>
                                    @endif
                                </a>
                            </th>

                            <th>Satuan</th>

                            {{-- SORT: STOK --}}
                            <th class="text-center">
                                <a href="{{ route('items.index', array_merge(request()->all(), ['sort_by' => 'stok', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="text-decoration-none text-dark d-flex justify-content-center align-items-center gap-1">
                                    Stok
                                    @if (request('sort_by') == 'stok')
                                        <i
                                            class="bi bi-sort-numeric-{{ request('direction') == 'asc' ? 'down' : 'up-alt' }} text-primary"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted"
                                            style="font-size: 10px; opacity: 0.5;"></i>
                                    @endif
                                </a>
                            </th>

                            {{-- SORT: HARGA --}}
                            <th class="text-end">
                                <a href="{{ route('items.index', array_merge(request()->all(), ['sort_by' => 'harga', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="text-decoration-none text-dark d-flex justify-content-end align-items-center gap-1">
                                    Harga Satuan
                                    @if (request('sort_by') == 'harga')
                                        <i
                                            class="bi bi-sort-numeric-{{ request('direction') == 'asc' ? 'down' : 'up-alt' }} text-primary"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted"
                                            style="font-size: 10px; opacity: 0.5;"></i>
                                    @endif
                                </a>
                            </th>

                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            {{-- FIX: Style dipisah ke attribute 'style', bukan 'class' --}}
                            <tr class="align-middle"
                                style="{{ $item->stok_saat_ini <= $item->min_stok ? 'background-color: #fff5f5;' : '' }}">

                                {{-- RUMUS NOMOR HALAMAN --}}
                                <td>{{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}</td>

                                <td>
                                    {{ $item->nama_barang }}
                                    {{-- Tanda Peringatan Stok --}}
                                    @if ($item->stok_saat_ini <= $item->min_stok)
                                        <span class="text-danger fw-bold ms-1"
                                            style="cursor: help; font-size: 14px; vertical-align: top;"
                                            title="Peringatan: Stok Menipis! Sisa stok sudah mencapai batas minimum ({{ $item->min_stok }}).">
                                            *
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->account)
                                        <div class="d-flex flex-column">
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                {{ $item->account->parent->parent->nama_akun ?? ($item->account->parent->nama_akun ?? '') }}
                                            </small>
                                            <span class="fw-bold text-dark">
                                                {{ $item->account->nama_akun }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-danger">-</span>
                                    @endif
                                </td>
                                <td>{{ $item->satuan }}</td>

                                <td class="text-center">
                                    <span
                                        class="fw-bold {{ $item->stok_saat_ini <= $item->min_stok ? 'text-danger' : '' }}">
                                        {{ $item->stok_saat_ini }}
                                    </span>
                                    <br>
                                    <small class="text-muted" style="font-size: 9px; opacity: 0.7;">(Min:
                                        {{ $item->min_stok }})</small>
                                </td>
                                <td class="text-end">
                                    Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                </td>

                                <td>
                                    <div class="d-flex gap-1">
                                        {{-- Tombol Edit --}}
                                        <a href="{{ route('items.edit', $item->id) }}" class="btn btn-sm btn-warning"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        {{-- Tombol Hapus --}}
                                        <form id="delete-form-{{ $item->id }}"
                                            action="{{ route('items.destroy', $item->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" title="Hapus"
                                                onclick="confirmDelete('delete-form-{{ $item->id }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">
                        Menampilkan {{ $items->firstItem() }} s/d {{ $items->lastItem() }} dari total
                        {{ $items->total() }} data
                    </small>

                    <div>
                        {{ $items->links() }}
                    </div>
                </div>
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
                                <i class="bi bi-info-circle"></i> <b>Format Baru Import Excel:</b><br>
                                Pastikan header kolom di baris pertama adalah:<br>
                                <code class="fw-bold text-dark">nama_barang, kategori_level_3, satuan, stok_awal,
                                    harga_satuan</code>
                                <br><br>
                                Contoh isi <b>kategori_level_3</b>: "Alat Tulis Kantor", "Bahan Kimia", "Kertas dan Cover".
                                <br>
                                <i>(Sistem otomatis mendeteksi kategori induknya)</i>
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

            // === 2. Fungsi Hapus SEMUA Barang ===
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
