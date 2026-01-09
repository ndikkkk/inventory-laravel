@extends('layouts.app')
@section('title', 'Laporan Transaksi')
@section('page-heading', 'Laporan Riwayat Inventory')

@section('content')
<style>
    /* 1. WARNA SAAT MODE TERANG (LIGHT) */
    .nav-tabs .nav-link {
        color: #6c757d; /* Abu-abu saat tidak aktif */
        font-weight: 600;
    }
    .nav-tabs .nav-link.active {
        color: #435ebe !important; /* Biru Mazer saat aktif */
    }

    /* 2. WARNA SAAT MODE GELAP (DARK) */
    /* Kita paksa jadi putih/terang agar kontras dengan background hitam */
    [data-bs-theme="dark"] .nav-tabs .nav-link {
        color: #b0b0b0 !important; /* Abu terang saat tidak aktif */
    }
    [data-bs-theme="dark"] .nav-tabs .nav-link:hover {
        color: #ffffff !important; /* Putih saat di-hover */
        border-color: #555;
    }
    [data-bs-theme="dark"] .nav-tabs .nav-link.active {
        color: #ffffff !important; /* Putih bersih saat tab aktif */
        background-color: #1e1e2d !important; /* Samakan dengan warna kartu */
        border-color: #555 #555 #1e1e2d; /* Border halus */
    }
</style>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Data Keluar Masuk Barang</h5>
    </div>
    <div class="card-body">
        
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#masuk" role="tab" aria-controls="home" aria-selected="true">
                    <i class="bi bi-arrow-down-circle text-success"></i> Riwayat Barang Masuk
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#keluar" role="tab" aria-controls="profile" aria-selected="false">
                    <i class="bi bi-arrow-up-circle text-danger"></i> Riwayat Barang Keluar
                </a>
            </li>
        </ul>

        <div class="tab-content pt-4" id="myTabContent">
            
            <div class="tab-pane fade show active" id="masuk" role="tabpanel" aria-labelledby="home-tab">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('incoming.excel') }}" class="btn btn-sm btn-success me-1">
                        <i class="bi bi-file-earmark-excel"></i> Excel Masuk
                    </a>
                    <a href="{{ route('incoming.pdf') }}" target="_blank" class="btn btn-sm btn-danger">
                        <i class="bi bi-file-earmark-pdf"></i> PDF Masuk
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="table-masuk">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Supplier</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($masuk as $m)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($m->tanggal)->format('d M Y') }}</td>
                                <td>{{ $m->item->nama_barang }}</td>
                                <td><span class="badge bg-success">+ {{ $m->jumlah }} {{ $m->item->satuan }}</span></td>
                                <td>{{ $m->supplier ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="keluar" role="tabpanel" aria-labelledby="profile-tab">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('outgoing.excel') }}" class="btn btn-sm btn-success me-1">
                        <i class="bi bi-file-earmark-excel"></i> Excel Keluar
                    </a>
                    <a href="{{ route('outgoing.pdf') }}" target="_blank" class="btn btn-sm btn-danger">
                        <i class="bi bi-file-earmark-pdf"></i> PDF Keluar
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="table-keluar">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Barang</th>
                                <th>Bidang / Divisi</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($keluar as $k)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($k->tanggal)->format('d M Y') }}</td>
                                <td>{{ $k->item->nama_barang }}</td>
                                <td><span class="badge bg-secondary">{{ $k->division->nama_bidang }}</span></td>
                                <td><span class="badge bg-danger">- {{ $k->jumlah }} {{ $k->item->satuan }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Opsional: Jika ingin tabelnya bisa di-search/pagination pakai Simple Datatables bawaan Mazer
    // Pastikan script simple-datatables sudah diload di layout jika ingin mengaktifkan ini
</script>
@endpush