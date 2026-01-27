<?php

namespace App\Http\Controllers;

use App\Models\IncomingTransaction;
use App\Models\Item;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IncomingExport;

class IncomingController extends Controller
{
    // === 1. HALAMAN UTAMA (DAFTAR RIWAYAT) ===
    // (Ini yang tadi hilang dan bikin error)
    public function index()
    {
        // Ambil data transaksi, urutkan dari yang terbaru
        // Pakai 'with' biar loadingnya ngebut (Eager Loading)
        $transactions = IncomingTransaction::with('item')
                            ->orderBy('tanggal', 'desc')
                            ->paginate(10);

        return view('transactions.incoming.index', compact('transactions'));
    }

    // === 2. TAMPILKAN FORM INPUT ===
    public function create()
    {
        // Ambil data barang untuk dropdown, diurutkan A-Z
        $items = Item::orderBy('nama_barang', 'asc')->get();
        return view('transactions.incoming.create', compact('items'));
    }

    // === 3. PROSES SIMPAN & UPDATE HARGA RATA-RATA ===
    public function store(Request $request)
    {
        $request->validate([
            'item_id'       => 'required|exists:items,id',
            'tanggal'       => 'required|date',
            'jumlah'        => 'required|integer|min:1',
            'harga_satuan'  => 'required|numeric|min:0', // Ini Harga Beli (dari Nota)
            'keterangan'    => 'nullable|string',
        ]);

        // A. Ambil Data Barang Master
        $item = Item::findOrFail($request->item_id);

        // B. Hitung Matematika Harga Rata-Rata (Weighted Average)
        $nilai_lama = $item->stok_saat_ini * $item->harga_satuan; // Aset Lama
        $nilai_baru = $request->jumlah * $request->harga_satuan;  // Aset Baru

        $total_stok_baru = $item->stok_saat_ini + $request->jumlah;

        // Rumus Rata-rata: Total Rupiah / Total Pcs
        if ($total_stok_baru > 0) {
            $harga_rata_rata = ($nilai_lama + $nilai_baru) / $total_stok_baru;
        } else {
            $harga_rata_rata = $request->harga_satuan;
        }

        // C. Update Master Barang (Stok Nambah + Harga Jadi Rata2)
        $item->update([
            'stok_saat_ini' => $total_stok_baru,
            'harga_satuan'  => $harga_rata_rata, // Update harga master
        ]);

        // D. Simpan Transaksi (Catat Harga Beli Asli, BUKAN Rata-rata)
        IncomingTransaction::create([
            'item_id'       => $request->item_id,
            'user_id'       => auth()->id(), // Opsional jika ada login
            'tanggal'       => $request->tanggal,
            'jumlah'        => $request->jumlah,
            'harga_satuan'  => $request->harga_satuan, // Harga Nota
            'total_harga'   => $nilai_baru,
            'sisa_stok'     => $request->jumlah,
            'keterangan'    => $request->keterangan,
        ]);

        return redirect()->route('incoming.index')
            ->with('success', 'Stok berhasil ditambah! Harga Master otomatis disesuaikan.');
    }

    // --- FITUR BARU: CETAK PDF BARANG MASUK ---
    public function exportPdf(Request $request)
    {
        $tglAwal  = $request->input('tgl_awal');
        $tglAkhir = $request->input('tgl_akhir');

        $query = IncomingTransaction::with('item');

        if (!empty($tglAwal) && !empty($tglAkhir)) {
            $query->whereDate('tanggal', '>=', $tglAwal)
                  ->whereDate('tanggal', '<=', $tglAkhir);
        }

        $data = $query->orderBy('tanggal', 'asc')->get();

        $pdf = Pdf::loadView('transactions.incoming.pdf', compact('data', 'tglAwal', 'tglAkhir'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan-Barang-Masuk.pdf');
    }

    // --- FITUR BARU: CETAK EXCEL BARANG MASUK ---
    public function exportExcel()
    {
        // Pastikan file IncomingExport sudah dibuat ya Mas, kalau belum ini bakal error
        return Excel::download(new IncomingExport, 'Laporan-Barang-Masuk.xlsx');
    }
}
