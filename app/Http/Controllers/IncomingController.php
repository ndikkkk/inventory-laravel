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

        // 1. Ambil Data Barang Lama
    $item = \App\Models\Item::find($request->item_id);
    $hargaDariMaster = $item->harga_satuan;

    // 2. Hitung Sisa Stok (Stok Lama + Masuk Baru)
    $stokBaru = $item->stok_saat_ini + $request->jumlah;

    // 3. Simpan Transaksi dengan Sisa Stok
$total = $request->jumlah * $hargaDariMaster;

        // Simpan Catatan Transaksi
        IncomingTransaction::create([
            'item_id'  => $request->item_id,
            'tanggal'  => $request->tanggal,
            'jumlah'   => $request->jumlah,
            'harga_satuan' => $hargaDariMaster, // Masuk DB
            'total_harga'  => $total,                 // Masuk DB
            'sisa_stok'    => $stokBaru,              // Masuk DB
        ]);

        // 4. Update Master Barang
    $item->stok_saat_ini = $stokBaru;
    $item->save();

        return redirect()->route('items.index')->with('success', 'Stok & Harga berhasil diperbarui!');
    }

    // --- FITUR BARU: CETAK PDF BARANG MASUK ---
    public function exportPdf()
    {
        $data = IncomingTransaction::with('item')->orderBy('tanggal', 'asc')->orderBy('created_at','asc')->get();
        $pdf = PDF::loadView('transactions.incoming.pdf', compact('data'));
        return $pdf->stream('Laporan-Barang-Masuk.pdf');
    }

    // --- FITUR BARU: CETAK EXCEL BARANG MASUK ---
    public function exportExcel()
    {
        return Excel::download(new IncomingExport, 'Laporan-Barang-Masuk.xlsx');
    }
}