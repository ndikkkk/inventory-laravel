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
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Bidang / Divisi',
            'Nama Barang',
            'Jumlah Keluar',
            'Status',
        ];
    }

    public function map($row): array
    {
        return [
            $row->tanggal,
            $row->division->nama_bidang ?? 'Admin/Pusat',
            $row->item->nama_barang ?? '-',
            $row->jumlah,
            $row->status,
        ];
    }
}