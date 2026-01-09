<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Imports\ItemImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ItemsExport; // Nanti kita buat file ini
use Barryvdh\DomPDF\Facade\Pdf;

class ItemController extends Controller
{
    // 1. TAMPILKAN SEMUA BARANG
    public function index()
    {
        $items = Item::with('category')->latest()->get(); // Ambil data + nama kategorinya
        return view('items.index', compact('items'));
    }

    // 2. FORM TAMBAH BARANG
    public function create()
    {
        $categories = Category::all(); // Kirim data kategori untuk dropdown
        return view('items.create', compact('categories'));
    }

    // 3. SIMPAN BARANG BARU
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required',
            'category_id' => 'required',
            'satuan' => 'required',
            'stok_awal_2026' => 'required|numeric|min:0',
        ]);

        // Stok saat ini otomatis sama dengan stok awal saat pertama dibuat
        Item::create([
            'nama_barang' => $request->nama_barang,
            'category_id' => $request->category_id,
            'satuan' => $request->satuan,
            'stok_awal_2026' => $request->stok_awal_2026,
            'stok_saat_ini' => $request->stok_awal_2026,
        ]);

        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan!');
    }

    // 4. FORM EDIT
    public function edit(Item $item)
    {
        $categories = Category::all();
        return view('items.edit', compact('item', 'categories'));
    }

    // 5. UPDATE DATA
    public function update(Request $request, Item $item)
    {
        $request->validate([
            'nama_barang' => 'required',
            'category_id' => 'required',
            'satuan' => 'required',
        ]);

        // Note: Stok tidak diupdate manual di sini, tapi lewat transaksi nanti.
        // Di sini hanya update info barang.
        $item->update([
            'nama_barang' => $request->nama_barang,
            'category_id' => $request->category_id,
            'satuan' => $request->satuan,
        ]);

        return redirect()->route('items.index')->with('success', 'Data barang diperbarui!');
    }

    // 6. HAPUS BARANG
    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Barang dihapus!');
    }

    // IMPORT CSV/EXCEL
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        // Proses Import
        Excel::import(new ItemImport, $request->file('file'));

        return redirect()->route('items.index')->with('success', 'Data barang berhasil diimpor dari Excel!');
    }

    // --- FITUR BARU: CETAK EXCEL ---
    public function exportExcel()
    {
        // Kita butuh buat class Export dulu (nanti saya kasih kodenya di bawah)
        return Excel::download(new ItemsExport, 'Data-Stok-Barang.xlsx');
    }

    // CETAK LAPORAN STOK (PDF VIEW)
    public function printStock()
    {
        // Ambil semua data barang, urutkan berdasarkan kategori biar rapi ceknya
        $items = Item::with('category')->orderBy('category_id')->get();

        // Load view khusus PDF (pastikan view 'items.pdf_stock' dibuat nanti)
        $pdf = PDF::loadView('items.print_stock', compact('items'));

        // Stream / Download
        return $pdf->stream('Laporan-Stok-Barang.pdf');
    }
}