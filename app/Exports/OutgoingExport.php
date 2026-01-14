<?php

namespace App\Exports;

use App\Models\OutgoingTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OutgoingExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // Hanya export yang sudah APPROVED
        return OutgoingTransaction::with(['item', 'division'])
            ->where('status', 'approved')
            ->orderBy('tanggal', 'asc') // Tambahkan ini
        ->orderBy('created_at', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Bidang / Divisi',
            'Nama Barang',
            'Jumlah Keluar',
            'Mutasi Stok', // Header Baru
            'Sisa Stok Akhir',
            'Status',
            'Total Nilai (Rp)',
        ];
    }

    public function map($row): array
    {
        // Hitung Stok Awal
        $stokAwal = isset($row->sisa_stok) ? ($row->sisa_stok + $row->jumlah) : 0;
        $sisa = $row->sisa_stok ?? 0;
        return [
            $row->tanggal,
            $row->division->nama_bidang ?? 'Admin/Pusat',
            $row->item->nama_barang ?? '-',
            $row->jumlah,
            "{$stokAwal} - {$row->jumlah} = {$sisa}",
            $sisa,
            $row->total_harga,
            $row->status,
        ];
    }
}