<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Imports\ItemImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ItemsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\IncomingTransaction; // PENTING: Untuk catat riwayat masuk
use App\Models\OutgoingTransaction;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon; // PENTING: Untuk tanggal otomatis

class ItemController extends Controller
{
    // 1. TAMPILKAN SEMUA BARANG
    public function index()
    {
        // Urutkan berdasarkan updated_at desc agar barang yang baru diedit/ditambah muncul paling atas
        $items = Item::with('category')->orderBy('updated_at', 'desc')->get(); 
        return view('items.index', compact('items'));
    }

    // 2. FORM TAMBAH BARANG
    public function create()
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    // 3. SIMPAN BARANG BARU (DIPERBARUI REVISI NO. 4)
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang'  => 'required',
            'category_id'  => 'required',
            'satuan'       => 'required',
            'harga_satuan' => 'required|numeric|min:0',
            // Validasi Tambahan
            'stok_awal'    => 'required|integer|min:0',
            'sumber_data'  => 'required|in:awal,baru', 
        ]);

        // LOGIKA PENENTUAN STOK AWAL 2026
        // Jika pilih 'awal': Masuk ke stok_awal_2026 (Modal Awal)
        // Jika pilih 'baru': stok_awal_2026 tetap 0 (Dianggap pembelian tahun berjalan)
        $stokAwalMaster = ($request->sumber_data == 'awal') ? $request->stok_awal : 0;

        // 1. Simpan ke Master Barang
        $item = Item::create([
            'nama_barang'    => $request->nama_barang,
            'category_id'    => $request->category_id,
            'satuan'         => $request->satuan,
            'harga_satuan'   => $request->harga_satuan,
            'stok_awal_2026' => $stokAwalMaster, // Simpan sesuai logika di atas
            'stok_saat_ini'  => $request->stok_awal, // Stok gudang pasti terisi sejumlah input
        ]);

        // 2. LOGIKA KHUSUS: Jika "Belanja Baru", catat ke IncomingTransaction
        // Agar masuk ke Laporan Barang Masuk & Grafik
        if ($request->sumber_data == 'baru' && $request->stok_awal > 0) {
            IncomingTransaction::create([
                'item_id'      => $item->id,
                'tanggal'      => Carbon::now(), // Tanggal hari ini
                'jumlah'       => $request->stok_awal,
                'harga_satuan' => $request->harga_satuan,
                'total_harga'  => $request->stok_awal * $request->harga_satuan,
                'sisa_stok'    => $request->stok_awal // Karena barang baru, sisanya = jumlah masuk
            ]);
        }

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
            'satuan'      => 'required',
            'harga_satuan'=> 'required|numeric', // Tambahkan validasi harga
        ]);

        $item->update([
            'nama_barang'  => $request->nama_barang,
            'category_id'  => $request->category_id,
            'satuan'       => $request->satuan,
            'harga_satuan' => $request->harga_satuan, // Update harga master
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

        try {
            Excel::import(new ItemImport, $request->file('file'));
            return redirect()->route('items.index')->with('success', 'Data barang BERHASIL diimport!');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $pesanError = 'Gagal Import! Ada data yang salah: ';
            foreach ($failures as $failure) {
                $baris = $failure->row();
                $error = $failure->errors()[0];
                $pesanError .= " (Baris $baris: $error)";
            }
            return back()->with('error', $pesanError);

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // CETAK EXCEL MASTER
    public function exportExcel()
    {
        return Excel::download(new ItemsExport, 'Data-Stok-Barang.xlsx');
    }

    // CETAK PDF STOK MASTER
    public function printStock()
    {
        $items = Item::with('category')->orderBy('category_id')->get();
        $pdf = PDF::loadView('items.print_stock', compact('items'));
        return $pdf->stream('Laporan-Stok-Barang.pdf');
    }

    // RESET SYSTEM
    public function deleteAll()
    {
        Schema::disableForeignKeyConstraints();
        IncomingTransaction::truncate(); 
        OutgoingTransaction::truncate(); 
        Item::truncate(); 
        Schema::enableForeignKeyConstraints();

        return redirect()->route('items.index')
            ->with('success', 'SYSTEM RESET: Semua Barang & Laporan BERHASIL DIHAPUS TOTAL (0)!');
    }
}