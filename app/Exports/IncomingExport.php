<?php

namespace App\Exports;

use App\Models\IncomingTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class IncomingExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return IncomingTransaction::with('item')->orderBy('tanggal', 'asc') // Tambahkan ini
        ->orderBy('created_at', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Barang',
            'Jumlah Masuk',
            'Mutasi Stok',
            'Sisa Stok Akhir',
            'Total Harga (Rp)',
            'Dibuat Pada'
        ];
    }

    public function map($row): array
    {
    // Hitung Stok Awal
        $stokAwal = isset($row->sisa_stok) ? ($row->sisa_stok - $row->jumlah) : 0;
        $sisa = $row->sisa_stok ?? 0;

    return [
            $row->tanggal,
            $row->item->nama_barang ?? '-', // Ambil nama barang dari relasi
            $row->jumlah,
            "{$stokAwal} + {$row->jumlah} = {$sisa}",
            $sisa,
            $row->total_harga,
            $row->created_at->format('d-m-Y H:i'),
        ];
    }
}