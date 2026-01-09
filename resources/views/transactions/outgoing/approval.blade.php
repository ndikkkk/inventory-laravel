@extends('layouts.app')
@section('title', 'Persetujuan Barang')

@section('page-heading', 'Persetujuan Barang')

@section('content')
    <div class="card">
        {{-- HEADER: Flexbox agar Judul di Kiri, Tombol Download di Kanan --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title my-auto">Kelola Permintaan Barang</h5>

            <div class="d-flex gap-2">
                <a href="{{ route('outgoing.excel') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-excel"></i> Excel
                </a>
                <a href="{{ route('outgoing.print_approval') }}" target="_blank" class="btn btn-sm btn-danger">
                    <i class="bi bi-printer"></i> Cetak Laporan Pengajuan
                </a>
            </div>
        </div>

        <div class="card-body">
            {{-- Alert Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Bidang</th>
                            <th>Barang</th>
                            <th>Jml</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $t)
                            <tr>
                                {{-- Format Tanggal: 09 Jan 2026 --}}
                                <td>{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}</td>

                                <td>{{ $t->division->nama_bidang ?? '-' }}</td>
                                <td>{{ $t->item->nama_barang ?? '-' }}</td>

                                {{-- Jumlah + Satuan --}}
                                <td>{{ $t->jumlah }} <small class="text-muted">{{ $t->item->satuan ?? '' }}</small></td>

                                <td>
                                    @if ($t->status == 'pending')
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    @elseif($t->status == 'approved')
                                        <span class="badge bg-success">Disetujui</span>
                                    @else
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>

                                <td>
                                    {{-- TOMBOL HANYA MUNCUL JIKA STATUS PENDING --}}
                                    @if ($t->status == 'pending')
                                        <div class="d-flex gap-2">
                                            <form action="{{ route('outgoing.approve', $t->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menyetujui (ACC) permintaan ini?')">
                                                @csrf
                                                <button class="btn btn-sm btn-success" title="Terima">
                                                    <i class="bi bi-check-lg"></i> ACC
                                                </button>
                                            </form>

                                            <form action="{{ route('outgoing.reject', $t->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menolak permintaan ini?')">
                                                @csrf
                                                <button class="btn btn-sm btn-danger" title="Tolak">
                                                    <i class="bi bi-x-lg"></i> Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted small fst-italic">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Tidak ada permintaan barang yang menunggu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
