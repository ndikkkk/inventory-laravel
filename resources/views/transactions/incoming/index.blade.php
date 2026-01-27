@extends('layouts.app')

@section('title', 'Riwayat Barang Masuk')
@section('page-heading', 'Riwayat Barang Masuk')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Daftar Restock / Barang Masuk</h5>

        {{-- Tombol Tambah Data --}}
        <a href="{{ route('incoming.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Input Stok Baru
        </a>
    </div>

    <div class="card-body">
        {{-- Alert Sukses --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Barang</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-end">Harga Beli</th>
                        <th class="text-end">Total Nilai</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                        <tr>
                            <td>{{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}</td>
                            <td>{{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d F Y') }}</td>
                            <td>
                                <span class="fw-bold">{{ $trx->item->nama_barang ?? '-' }}</span>
                                <br>
                                <small class="text-muted" style="font-size: 11px;">
                                    {{ $trx->item->account->nama_akun ?? '' }}
                                </small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">+{{ $trx->jumlah }}</span>
                            </td>
                            <td class="text-end">
                                Rp {{ number_format($trx->harga_satuan, 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-bold text-dark">
                                Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                            </td>
                            <td>{{ $trx->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada riwayat barang masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
