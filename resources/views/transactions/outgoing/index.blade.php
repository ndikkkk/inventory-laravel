@extends('layouts.app')
@section('title', 'Riwayat Barang Keluar')
@section('page-heading', 'Data Barang Keluar')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title my-auto">
                {{ Auth::user()->role == 'admin' ? 'Riwayat Barang Keluar (ACC)' : 'Riwayat Pengajuan Saya' }}
            </h5>

            <div class="d-flex gap-2">
                {{-- 1. TOMBOL TAMBAH / AJUKAN --}}
                <a href="{{ route('outgoing.create') }}" class="btn btn-primary ms-2">
                    <i class="bi bi-plus"></i>
                    {{ Auth::user()->role == 'admin' ? 'Input Baru' : 'Ajukan Permintaan' }}
                </a>
                {{-- TOMBOL PEMELIHARAAN (HANYA ADMIN) --}}
                @if (Auth::user()->role == 'admin')
                    <a href="{{ route('outgoing.maintenance') }}" class="btn btn-warning ms-2">
                        <i class="bi bi-wrench-adjustable"></i> Input Pemeliharaan
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="table-responsive">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Bidang / Divisi</th>
                            <th>Barang</th>
                            {{-- Header Kolom Sudah Ada --}}
                            <th>Jumlah</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $t)
                            <tr>
                                {{-- 1. Tanggal --}}
                                <td>{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}</td>

                                {{-- 2. Nama Bidang --}}
                                <td>{{ $t->division->nama_bidang ?? 'Admin/Pusat' }}</td>

                                {{-- 3. Nama Barang / Deskripsi --}}
                                <td>
                                    @if ($t->item_id)
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $t->item->nama_barang ?? 'Barang Dihapus' }}</span>
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                <i class="bi bi-tag"></i> {{ $t->item->account->nama_akun ?? '-' }}
                                            </small>
                                        </div>
                                    @else
                                        {{-- Khusus Pemeliharaan --}}
                                        <span class="text-primary fw-bold">{{ $t->deskripsi }}</span>
                                        <div class="d-flex align-items-center gap-1">
                                            <small class="badge bg-light text-secondary border">Pemeliharaan</small>
                                            @if ($t->km_saat_ini)
                                                <small class="text-muted" style="font-size: 0.75rem;">(KM: {{ $t->km_saat_ini }})</small>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                {{-- 4. KOLOM JUMLAH (INI YANG HILANG TADI) --}}
                                <td>
                                    <span class="fw-bold">{{ $t->jumlah }}</span>
                                    {{-- Tampilkan Satuan (Pcs/Rim/Paket) --}}
                                    <small class="text-muted">{{ $t->item->satuan ?? 'Pkt' }}</small>
                                </td>

                                {{-- 5. KOLOM STATUS (INI JUGA HILANG TADI) --}}
                                <td class="text-center">
                                    @if ($t->status == 'pending')
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock"></i> Menunggu
                                        </span>
                                    @elseif($t->status == 'approved')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Disetujui
                                        </span>
                                    @elseif($t->status == 'rejected')
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> Ditolak
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">
                                    Belum ada riwayat barang keluar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection