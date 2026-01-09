<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ItemsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Item::with('category')->get();
    }

    public function headings(): array
    {
        return [
            'Nama Barang',
            'Kategori',
            'Satuan',
            'Stok Saat Ini',
        ];
    }

    public function map($item): array
    {
        return [
            $item->nama_barang,
            $item->category->nama_kategori ?? '-',
            $item->satuan,
            $item->stok_saat_ini,
        ];
    }
}