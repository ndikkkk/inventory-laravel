@extends('layouts.app')
@section('title', 'Laporan Transaksi')
@section('page-heading', 'Laporan Riwayat Inventory')

@section('content')
    <style>
        .nav-tabs .nav-link {
            color: ##6c757d;
            font-weight: 600;
        }

        .nav-tabs .nav-link.active {
            color: #435ebe !important;
        }

        [data-bs-theme="dark"] .nav-tabs .nav-link.active {
            color: #ffffff !important;
            background-color: #1e1e2d !important;
            border-color: #555 #555 #1e1e2d;
        }
    </style>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">Data Keluar Masuk Barang & Nilai Aset</h5>

            {{-- TOMBOL CETAK SEMUA (Gabungan) --}}
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFilterCetak">
                <i class="bi bi-printer-fill"></i> Cetak Semua Transaksi
            </button>
        </div>
        <div class="card-body">

            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#masuk" role="tab">
                        <i class="bi bi-arrow-down-circle text-success"></i> Riwayat Barang Masuk
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#keluar" role="tab">
                        <i class="bi bi-arrow-up-circle text-danger"></i> Riwayat Barang Keluar
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="gabungan-tab" data-bs-toggle="tab" href="#gabungan" role="tab">
                        <i class="bi bi-collection text-primary"></i> Semua Transaksi
                    </a>
                </li>
            </ul>

            <div class="tab-content pt-4" id="myTabContent">

                {{-- TAB MASUK --}}
                <div class="tab-pane fade show active" id="masuk" role="tabpanel">
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('incoming.excel') }}" class="btn btn-sm btn-success me-1">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </a>
                        {{-- TOMBOL PDF MASUK (PAKAI MODAL BARU) --}}
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                            data-bs-target="#modalFilterMasuk">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="table-masuk">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah Masuk</th>
                                    <th class="text-center">Mutasi Stok</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-end">Total Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($masuk as $m)
                                    @php
                                        $stokAkhir = $m->sisa_stok;
                                        $jumlahMasuk = $m->jumlah;
                                        $stokAwal = $stokAkhir - $jumlahMasuk;
                                    @endphp
                                    <tr class="align-middle">
                                        <td>{{ \Carbon\Carbon::parse($m->tanggal)->format('d M Y') }}</td>
                                        <td>{{ $m->item->nama_barang ?? '[Terhapus]' }}</td>
                                        <td><span class="badge bg-success">+ {{ $m->jumlah }}</span></td>
                                        <td class="text-center font-monospace">
                                            @if (isset($stokAkhir))
                                                <span class="text-muted">{{ $stokAwal }}</span>
                                                <span class="text-success fw-bold mx-1">+</span>
                                                {{ $jumlahMasuk }}
                                                <span class="fw-bold mx-1">=</span>
                                                <span class="badge bg-info text-dark">{{ $stokAkhir }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-end">Rp {{ number_format($m->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($m->total_harga, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB KELUAR --}}
                <div class="tab-pane fade" id="keluar" role="tabpanel">
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('outgoing.excel') }}" class="btn btn-sm btn-success me-1">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </a>
                        {{-- TOMBOL PDF KELUAR (PAKAI MODAL BARU) --}}
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                            data-bs-target="#modalFilterKeluar">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="table-keluar">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nama Barang</th>
                                    <th>Bidang / Divisi</th>
                                    <th>Jumlah Keluar</th>
                                    <th class="text-center">Mutasi Stok</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-end">Total Nilai (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($keluar as $k)
                                    @php
                                        $stokAkhir = $k->sisa_stok;
                                        $jumlahKeluar = $k->jumlah;
                                        $stokAwal = $stokAkhir + $jumlahKeluar;
                                    @endphp
                                    <tr class="align-middle">
                                        <td>{{ \Carbon\Carbon::parse($k->tanggal)->format('d M Y') }}</td>
                                        <td>{{ $k->item->nama_barang ?? $k->deskripsi }}
                                            @if (!$k->item_id)
                                                (Servis)
                                            @endif
                                        </td>
                                        <td><span class="badge bg-secondary">{{ $k->division->nama_bidang ?? '-' }}</span>
                                        </td>
                                        <td><span class="badge bg-danger">- {{ $jumlahKeluar }}</span></td>
                                        <td class="text-center font-monospace">
                                            @if (isset($stokAkhir))
                                                <span class="text-muted">{{ $stokAwal }}</span>
                                                <span class="text-danger fw-bold mx-1">-</span>
                                                {{ $jumlahKeluar }}
                                                <span class="fw-bold mx-1">=</span>
                                                <span class="badge bg-info text-dark">{{ $stokAkhir }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-end">Rp {{ number_format($k->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($k->total_harga, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB GABUNGAN (ALL) --}}
                <div class="tab-pane fade" id="gabungan" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nama Barang</th>
                                    <th>Status</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Sisa Stok (Saldo)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gabungan as $row)
                                    <tr class="align-middle">
                                        <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                                        <td>{{ $row->item->nama_barang ?? $row->deskripsi }}
                                            @if (!$row->item_id)
                                                (Servis)
                                            @endif
                                        </td>
                                        <td>
                                            @if ($row->jenis == 'masuk')
                                                <span class="badge bg-success">Barang Masuk</span>
                                            @else
                                                <span class="badge bg-danger">Ke:
                                                    {{ $row->division->nama_bidang ?? '-' }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($row->jenis == 'masuk')
                                                <span class="text-success fw-bold">+ {{ $row->jumlah }}</span>
                                            @else
                                                <span class="text-danger fw-bold">- {{ $row->jumlah }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center bg-light font-monospace fw-bold">
                                            {{ $row->sisa_stok ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================= --}}
    {{-- MODAL 1: FILTER CETAK GABUNGAN (SEMUA) --}}
    {{-- ================================================= --}}
    <div class="modal fade" id="modalFilterCetak" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak Semua Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('reports.export_all') }}" method="GET" target="_blank">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label">Tanggal Awal</label>
                                <input type="date" name="tgl_awal" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="tgl_akhir" class="form-control">
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">*Kosongkan untuk cetak semua.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-file-pdf"></i> Cetak PDF</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================================================= --}}
    {{-- MODAL 2: FILTER CETAK MASUK (BARU) --}}
    {{-- ================================================= --}}
    <div class="modal fade" id="modalFilterMasuk" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-success">Cetak Laporan Barang Masuk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                {{-- PERHATIKAN ROUTE-NYA KE INCOMING.PDF --}}
                <form action="{{ route('incoming.pdf') }}" method="GET" target="_blank">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label">Tanggal Awal</label>
                                <input type="date" name="tgl_awal" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="tgl_akhir" class="form-control">
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">*Kosongkan untuk cetak semua.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success"><i class="bi bi-file-pdf"></i> Cetak PDF
                            Masuk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================================================= --}}
    {{-- MODAL 3: FILTER CETAK KELUAR (BARU) --}}
    {{-- ================================================= --}}
    <div class="modal fade" id="modalFilterKeluar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Cetak Laporan Barang Keluar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                {{-- PERHATIKAN ROUTE-NYA KE OUTGOING.PDF --}}
                <form action="{{ route('outgoing.pdf') }}" method="GET" target="_blank">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label">Tanggal Awal</label>
                                <input type="date" name="tgl_awal" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="tgl_akhir" class="form-control">
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">*Kosongkan untuk cetak semua.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger"><i class="bi bi-file-pdf"></i> Cetak PDF
                            Keluar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
