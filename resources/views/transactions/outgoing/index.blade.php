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
            <a href="{{ route('outgoing.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus"></i> 
                {{ Auth::user()->role == 'admin' ? 'Input Baru' : 'Ajukan Permintaan' }}
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="table1">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Bidang / Divisi</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th class="text-center">Status</th>
                        {{-- Status dihapus sesuai request Anda --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr>
                        {{-- Format Tanggal: 09 Jan 2026 --}}
                        <td>{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}</td>
                        
                        {{-- Nama Bidang --}}
                        <td>{{ $t->division->nama_bidang ?? 'Admin/Pusat' }}</td>
                        
                        {{-- Nama Barang --}}
                        <td>{{ $t->item->nama_barang ?? '-' }}</td>
                        
                        {{-- Jumlah + Satuan (Misal: 5 Rim) --}}
                        <td>
                            <span class="fw-bold">{{ $t->jumlah }}</span> 
                            <small class="text-muted">{{ $t->item->satuan ?? '' }}</small>
                        </td>

                        <td class="text-center">
                            @if($t->status == 'pending')
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-clock-history"></i> Menunggu ACC
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
                        <td colspan="4" class="text-center py-3 text-muted">
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