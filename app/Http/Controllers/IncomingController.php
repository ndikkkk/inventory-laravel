<?php

namespace App\Http\Controllers;

use App\Models\IncomingTransaction;
use App\Models\Item;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IncomingExport; // Nanti dibuat

class IncomingController extends Controller
{
    // 1. Tampilkan Form Barang Masuk
    public function create()
    {
        $items = Item::all(); // Ambil data barang untuk dropdown
        return view('transactions.incoming.create', compact('items'));
    }

    // 2. Simpan Data & Tambah Stok
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'tanggal' => 'required|date',
            'jumlah'  => 'required|numeric|min:1',
        ]);

        // Simpan Catatan Transaksi
        IncomingTransaction::create([
            'item_id'  => $request->item_id,
            'tanggal'  => $request->tanggal,
            'jumlah'   => $request->jumlah,
            'supplier' => $request->supplier, // Opsional
        ]);

        // LOGIC PENTING: Otomatis Tambah Stok Barang
        $item = Item::find($request->item_id);
        $item->increment('stok_saat_ini', $request->jumlah);

        return redirect()->route('items.index')->with('success', 'Stok berhasil ditambahkan!');
    }

    // --- FITUR BARU: CETAK PDF BARANG MASUK ---
    public function exportPdf()
    {
        $data = IncomingTransaction::with('item')->latest()->get();
        $pdf = PDF::loadView('transactions.incoming.pdf', compact('data'));
        return $pdf->stream('Laporan-Barang-Masuk.pdf');
    }

    // --- FITUR BARU: CETAK EXCEL BARANG MASUK ---
    public function exportExcel()
    {
        return Excel::download(new IncomingExport, 'Laporan-Barang-Masuk.xlsx');
    }
}