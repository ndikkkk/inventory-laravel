@extends('layouts.app')
@section('title', 'Persetujuan Barang')
@section('page-heading', 'Persetujuan Barang')

@section('content')
    <div class="card">
        {{-- HEADER --}}
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
            {{-- Alert Messages (Session) --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle" id="table1">
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
                                {{-- Tanggal --}}
                                <td class="text-nowrap">{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}</td>

                                {{-- Bidang & Barang --}}
                                <td><span class="fw-bold">{{ $t->division->nama_bidang ?? '-' }}</span></td>
                                <td>{{ $t->item->nama_barang ?? '-' }}</td>

                                {{-- Jumlah --}}
                                <td>
                                    <span class="badge bg-secondary">{{ $t->jumlah }} {{ $t->item->satuan ?? '' }}</span>
                                </td>

                                {{-- Status Badge --}}
                                <td>
                                    @if ($t->status == 'pending')
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    @elseif($t->status == 'approved')
                                        <span class="badge bg-success">Disetujui</span>
                                    @else
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>

                                {{-- AKSI --}}
                                <td>
                                    @if ($t->status == 'pending')
                                        <div class="d-flex gap-2">

                                            {{-- 1. TOMBOL ACC (SWEETALERT) --}}
                                            {{-- Perhatikan parameter ke-5: Harga --}}
                                            <button type="button" class="btn btn-sm btn-success"
                                                onclick="confirmApprove(
                                                    '{{ $t->id }}',
                                                    '{{ $t->item->nama_barang }}',
                                                    '{{ $t->division->nama_bidang }}',
                                                    '{{ $t->jumlah }}',
                                                    'Rp {{ number_format($t->total_harga, 0, ',', '.') }}'
                                                )">
                                                <i class="bi bi-check-lg"></i> ACC
                                            </button>

                                            {{-- Form Tersembunyi untuk ACC --}}
                                            <form id="approve-form-{{ $t->id }}" action="{{ route('outgoing.approve', $t->id) }}" method="POST" style="display: none;">
                                                @csrf
                                            </form>

                                            {{-- 2. TOMBOL TOLAK (SWEETALERT) --}}
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="confirmReject('{{ $t->id }}', '{{ $t->division->nama_bidang }}')">
                                                <i class="bi bi-x-lg"></i> Tolak
                                            </button>

                                            {{-- Form Tersembunyi untuk TOLAK --}}
                                            <form id="reject-form-{{ $t->id }}" action="{{ route('outgoing.reject', $t->id) }}" method="POST" style="display: none;">
                                                @csrf
                                            </form>

                                        </div>
                                    @else
                                        <span class="text-muted small fst-italic text-nowrap">
                                            <i class="bi bi-check-all"></i> Selesai
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="Empty" width="80" class="mb-3 opacity-50">
                                    <br>
                                    Tidak ada permintaan barang yang perlu diproses.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- SCRIPT SWEETALERT --}}
    <script>
        // 1. Popup Konfirmasi ACC (Dengan Harga)
        function confirmApprove(id, barang, bidang, jumlah, harga) {
            Swal.fire({
                title: 'Setujui Permintaan?',
                html: `
                    <div class="text-start px-3">
                        <table class="table table-sm table-borderless mb-0 fs-6">
                            <tr>
                                <td width="30%">Bidang</td>
                                <td>: <strong>${bidang}</strong></td>
                            </tr>
                            <tr>
                                <td>Barang</td>
                                <td>: <strong>${barang}</strong></td>
                            </tr>
                            <tr>
                                <td>Jumlah</td>
                                <td>: <strong>${jumlah}</strong></td>
                            </tr>
                            <tr class="table-success rounded">
                                <td class="fw-bold">Total Nilai</td>
                                <td>: <strong class="text-success fs-5">${harga}</strong></td>
                            </tr>
                        </table>
                    </div>
                    <div class="mt-2 small text-muted">Stok barang akan berkurang otomatis.</div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754', // Hijau
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('approve-form-' + id).submit();
                }
            })
        }

        // 2. Popup Konfirmasi TOLAK
        function confirmReject(id, bidang) {
            Swal.fire({
                title: 'Tolak Permintaan?',
                text: "Permintaan dari " + bidang + " akan ditolak.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545', // Merah
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Tolak!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('reject-form-' + id).submit();
                }
            })
        }
    </script>
@endsection
