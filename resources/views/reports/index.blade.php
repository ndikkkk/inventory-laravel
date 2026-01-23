@extends('layouts.app')
@section('title', 'Laporan Transaksi')
@section('page-heading', 'Laporan Riwayat Inventory')

@section('content')
    <style>
        .nav-tabs .nav-link { color: #6c757d; font-weight: 600; }
        .nav-tabs .nav-link.active { color: #435ebe !important; }
        [data-bs-theme="dark"] .nav-tabs .nav-link.active { color: #ffffff !important; background-color: #1e1e2d !important; border-color: #555 #555 #1e1e2d; }
    </style>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">Data Keluar Masuk Barang & Nilai Aset</h5>

            <div class="d-flex gap-2">
                {{-- TOMBOL CETAK SEMUA --}}
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFilterCetak">
                    <i class="bi bi-printer-fill"></i> Cetak Semua Transaksi
                </button>

                {{-- TOMBOL MENU LAPORAN LENGKAP --}}
                <button type="button" class="btn btn-warning text-dark" data-bs-toggle="modal" data-bs-target="#modalCetakLengkap">
                    <i class="bi bi-file-earmark-bar-graph"></i> Menu Laporan Lengkap
                </button>
            </div>
        </div>
        
        <div class="card-body">

            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#masuk" role="tab"><i class="bi bi-arrow-down-circle text-success"></i> Riwayat Barang Masuk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#keluar" role="tab"><i class="bi bi-arrow-up-circle text-danger"></i> Riwayat Barang Keluar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="gabungan-tab" data-bs-toggle="tab" href="#gabungan" role="tab"><i class="bi bi-collection text-primary"></i> Semua Transaksi</a>
                </li>
            </ul>

            <div class="tab-content pt-4" id="myTabContent">
                {{-- TAB MASUK --}}
                <div class="tab-pane fade show active" id="masuk" role="tabpanel">
                    {{--}}
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('incoming.excel') }}" class="btn btn-sm btn-success me-1"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalFilterMasuk"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
                    </div>
                    {{--}}
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="table-masuk">
                            <thead><tr><th>Tanggal</th><th>Nama Barang</th><th>Jumlah Masuk</th><th class="text-center">Mutasi Stok</th><th class="text-end">Harga Satuan</th><th class="text-end">Total Harga</th></tr></thead>
                            <tbody>
                                @foreach ($masuk as $m)
                                    @php $stokAwal = $m->sisa_stok - $m->jumlah; @endphp
                                    <tr class="align-middle">
                                        <td>{{ \Carbon\Carbon::parse($m->tanggal)->format('d M Y') }}</td>
                                        <td>{{ $m->item->nama_barang ?? '[Terhapus]' }}</td>
                                        <td><span class="badge bg-success">+ {{ $m->jumlah }}</span></td>
                                        <td class="text-center font-monospace">
                                            @if (isset($m->sisa_stok)) <span class="text-muted">{{ $stokAwal }}</span> <span class="text-success fw-bold mx-1">+</span> {{ $m->jumlah }} <span class="fw-bold mx-1">=</span> <span class="badge bg-info text-dark">{{ $m->sisa_stok }}</span> @else - @endif
                                        </td>
                                        <td class="text-end">Rp {{ number_format($m->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($m->total_harga, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB KELUAR --}}
                <div class="tab-pane fade" id="keluar" role="tabpanel">
                    {{--}}
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('outgoing.excel') }}" class="btn btn-sm btn-success me-1"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalFilterKeluar"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
                    </div>
                    {{--}}
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="table-keluar">
                            <thead><tr><th>Tanggal</th><th>Nama Barang</th><th>Divisi</th><th>Jumlah Keluar</th><th class="text-center">Mutasi Stok</th><th class="text-end">Harga Satuan</th><th class="text-end">Total Nilai (Rp)</th></tr></thead>
                            <tbody>
                                @foreach ($keluar as $k)
                                    @php $stokAwal = $k->sisa_stok + $k->jumlah; @endphp
                                    <tr class="align-middle">
                                        <td>{{ \Carbon\Carbon::parse($k->tanggal)->format('d M Y') }}</td>
                                        <td>{{ $k->item->nama_barang ?? $k->deskripsi }} @if (!$k->item_id) (Servis) @endif</td>
                                        <td><span class="badge bg-secondary">{{ $k->division->nama_bidang ?? '-' }}</span></td>
                                        <td><span class="badge bg-danger">- {{ $k->jumlah }}</span></td>
                                        <td class="text-center font-monospace">
                                            @if (isset($k->sisa_stok)) <span class="text-muted">{{ $stokAwal }}</span> <span class="text-danger fw-bold mx-1">-</span> {{ $k->jumlah }} <span class="fw-bold mx-1">=</span> <span class="badge bg-info text-dark">{{ $k->sisa_stok }}</span> @else - @endif
                                        </td>
                                        <td class="text-end">Rp {{ number_format($k->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($k->total_harga, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB GABUNGAN --}}
                <div class="tab-pane fade" id="gabungan" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead><tr><th>Tanggal</th><th>Nama Barang</th><th>Status</th><th class="text-center">Jumlah</th><th class="text-center">Sisa Stok</th></tr></thead>
                            <tbody>
                                @foreach ($gabungan as $row)
                                    <tr class="align-middle">
                                        <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                                        <td>{{ $row->item->nama_barang ?? $row->deskripsi }} @if (!$row->item_id) (Servis) @endif</td>
                                        <td>@if ($row->jenis == 'masuk') <span class="badge bg-success">Masuk</span> @else <span class="badge bg-danger">Ke: {{ $row->division->nama_bidang ?? '-' }}</span> @endif</td>
                                        <td class="text-center">@if ($row->jenis == 'masuk') <span class="text-success fw-bold">+ {{ $row->jumlah }}</span> @else <span class="text-danger fw-bold">- {{ $row->jumlah }}</span> @endif</td>
                                        <td class="text-center bg-light font-monospace fw-bold">{{ $row->sisa_stok ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL FILTER SEDERHANA (YANG LAMA) --}}
    <div class="modal fade" id="modalFilterCetak" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Cetak Semua Transaksi</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form action="{{ route('reports.export_all') }}" method="GET" target="_blank">
                    <div class="modal-body">
                        <div class="row"><div class="col-6"><label class="form-label">Tanggal Awal</label><input type="date" name="tgl_awal" class="form-control"></div><div class="col-6"><label class="form-label">Tanggal Akhir</label><input type="date" name="tgl_akhir" class="form-control"></div></div>
                        <small class="text-muted d-block mt-2">*Kosongkan untuk cetak semua.</small>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary"><i class="bi bi-file-pdf"></i> Cetak PDF</button></div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL FILTER MASUK --}}
    <div class="modal fade" id="modalFilterMasuk" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title text-success">Cetak Laporan Masuk</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form action="{{ route('incoming.pdf') }}" method="GET" target="_blank">
                    <div class="modal-body">
                        <div class="row"><div class="col-6"><label class="form-label">Tanggal Awal</label><input type="date" name="tgl_awal" class="form-control"></div><div class="col-6"><label class="form-label">Tanggal Akhir</label><input type="date" name="tgl_akhir" class="form-control"></div></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-success">Cetak</button></div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL FILTER KELUAR --}}
    <div class="modal fade" id="modalFilterKeluar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title text-danger">Cetak Laporan Keluar</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form action="{{ route('outgoing.pdf') }}" method="GET" target="_blank">
                    <div class="modal-body">
                        <div class="row"><div class="col-6"><label class="form-label">Tanggal Awal</label><input type="date" name="tgl_awal" class="form-control"></div><div class="col-6"><label class="form-label">Tanggal Akhir</label><input type="date" name="tgl_akhir" class="form-control"></div></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger">Cetak</button></div>
                </form>
            </div>
        </div>
    </div>
 

    {{-- MODAL LENGKAP 4 TOMBOL (YANG SAYA PERBAIKI: HAPUS REQUIRED) --}}
    <div class="modal fade" id="modalCetakLengkap" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-dark"><i class="bi bi-printer"></i> Pusat Pencetakan Laporan Lengkap</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light">
                    <ul class="nav nav-pills mb-3 justify-content-center" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pills-mutasi">1. Laporan Mutasi (Stok)</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-masuk">2. Barang Masuk</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-keluar">3. Barang Keluar</button></li>
                    </ul>

                    <div class="tab-content">
                        @foreach(['mutasi' => ['primary', 'reports.mutasi'], 'masuk' => ['success', 'reports.masuk'], 'keluar' => ['danger', 'reports.keluar']] as $key => $val)
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="pills-{{ $key }}">
                                <div class="row g-4">
                                    {{-- ITEM 1-3: PER LEVEL (TETAP PERLU SELECT AKUN, TAPI TANGGAL TIDAK WAJIB) --}}
                                    @foreach([['3. Per Rincian (L3)', 'level_3', $level3], ['2. Per Kelompok (L2)', 'level_2', $level2], ['1. Per Jenis (L1)', 'level_1', $level1]] as $idx => $lvl)
                                    <div class="col-md-3">
                                        <div class="card h-100 border-{{ $val[0] }}">
                                            <div class="card-body p-3">
                                                <h6 class="text-{{ $val[0] }}">{{ $lvl[0] }}</h6>
                                                <form action="{{ route($val[1]) }}" method="GET" target="_blank">
                                                    <input type="hidden" name="scope" value="{{ $lvl[1] }}">
                                                    <select name="account_id" class="form-select form-select-sm mb-2" required>
                                                        <option value="">-- Pilih --</option>
                                                        @foreach($lvl[2] as $acc) <option value="{{ $acc->id }}">{{ $acc->nama_akun }}</option> @endforeach
                                                    </select>
                                                    {{-- DISINI SAYA HAPUS 'REQUIRED' --}}
                                                    <input type="date" name="tgl_awal" class="form-control form-control-sm mb-1">
                                                    <input type="date" name="tgl_akhir" class="form-control form-control-sm mb-2">
                                                    <button class="btn btn-sm btn-{{ $val[0] }} w-100">Cetak</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach

                                    {{-- ITEM 4: CETAK SEMUA --}}
                                    <div class="col-md-3">
                                        <div class="card h-100 bg-{{ $val[0] }} text-white">
                                            <div class="card-body p-3 text-center">
                                                <h6>4. Cetak Semua</h6>
                                                <p style="font-size: 10px;">Format beranak-pinak lengkap.</p>
                                                <form action="{{ route($val[1]) }}" method="GET" target="_blank">
                                                    <input type="hidden" name="scope" value="all">
                                                    {{-- DISINI JUGA SAYA HAPUS 'REQUIRED' --}}
                                                    <input type="date" name="tgl_awal" class="form-control form-control-sm mb-1">
                                                    <input type="date" name="tgl_akhir" class="form-control form-control-sm mb-2">
                                                    <button class="btn btn-sm btn-light text-{{ $val[0] }} w-100 fw-bold">Cetak</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection