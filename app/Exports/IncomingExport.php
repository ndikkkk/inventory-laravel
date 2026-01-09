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
        return IncomingTransaction::with('item')->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Barang',
            'Jumlah Masuk',
            'Supplier',
            'Dibuat Pada'
        ];
    }

    public function map($row): array
    {
        return [
            $row->tanggal,
            $row->item->nama_barang ?? '-', // Ambil nama barang dari relasi
            $row->jumlah,
            $row->supplier ?? '-',
            $row->created_at->format('d-m-Y H:i'),
        ];
    }
}