<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // 1. WAJIB IMPORT INI
use App\Models\IncomingTransaction;
use App\Models\OutgoingTransaction;
use App\Models\Item;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // 1. Data Masuk
        $masuk = IncomingTransaction::with('item')
                    ->orderBy('tanggal', 'asc')
                    ->orderBy('created_at', 'asc')
                    ->get()
                    ->map(function($item){
                        $item->jenis = 'masuk';
                        return $item;
                    });

        // 2. Data Keluar (Approved)
        $keluar = OutgoingTransaction::with(['item', 'division'])
                    ->where('status', 'approved')
                    ->orderBy('tanggal', 'asc')
                    ->orderBy('created_at', 'asc')
                    ->get()
                    ->map(function($item){
                        $item->jenis = 'keluar';
                        return $item;
                    });

        // 3. Gabungan
        $gabungan = $masuk->concat($keluar)->sortBy(function($item) {
            return $item->tanggal . $item->created_at;
        });

        return view('reports.index', compact('masuk', 'keluar', 'gabungan'));
    }

    // EXPORT PDF GABUNGAN (REVISI PEMISAHAN)
    public function exportAllPdf(Request $request)
    {
        $tglAwal  = $request->input('tgl_awal');
        $tglAkhir = $request->input('tgl_akhir');

        // ==========================================
        // 1. QUERY BARANG MASUK
        // ==========================================
        $queryMasuk = IncomingTransaction::with('item');
        if (!empty($tglAwal) && !empty($tglAkhir)) {
            $queryMasuk->whereDate('tanggal', '>=', $tglAwal)
                       ->whereDate('tanggal', '<=', $tglAkhir);
        }
        $masuk = $queryMasuk->get()->map(function($item){
            $item->jenis_transaksi = 'masuk';
            return $item;
        });

        // ==========================================
        // 2. QUERY BARANG KELUAR (HANYA ITEM FISIK)
        // ==========================================
        $queryKeluarBarang = OutgoingTransaction::with(['item', 'division'])
                        ->where('status', 'approved')
                        ->whereNotNull('item_id'); // <--- Filter Barang Only

        if (!empty($tglAwal) && !empty($tglAkhir)) {
            $queryKeluarBarang->whereDate('tanggal', '>=', $tglAwal)
                              ->whereDate('tanggal', '<=', $tglAkhir);
        }
        $keluarBarang = $queryKeluarBarang->get()->map(function($item){
            $item->jenis_transaksi = 'keluar';
            return $item;
        });

        // ==========================================
        // 3. QUERY PEMELIHARAAN (JASA/SERVIS)
        // ==========================================
        $queryMaintenance = OutgoingTransaction::with('division')
                        ->where('status', 'approved')
                        ->whereNull('item_id'); // <--- Filter Jasa Only (Item ID Null)

        if (!empty($tglAwal) && !empty($tglAkhir)) {
            $queryMaintenance->whereDate('tanggal', '>=', $tglAwal)
                             ->whereDate('tanggal', '<=', $tglAkhir);
        }
        $maintenance = $queryMaintenance->orderBy('tanggal', 'asc')->get();

        // ==========================================
        // 4. DATA TABEL 1 (STOK GABUNGAN)
        // ==========================================
        $gabunganStok = $masuk->concat($keluarBarang)->sortBy(function($item) {
            return $item->tanggal . $item->created_at;
        });

        // ==========================================
        // 5. PERHITUNGAN NILAI
        // ==========================================

        // -- Hitungan Stok --
        $totalQtyMasuk      = $masuk->sum('jumlah');
        $totalQtyKeluar     = $keluarBarang->sum('jumlah');
        $totalRupiahMasuk   = $masuk->sum('total_harga');
        $totalRupiahKeluar  = $keluarBarang->sum('total_harga');
        
        // -- Hitungan Maintenance (Terpisah) --
        $totalBiayaServis   = $maintenance->sum('total_harga');

        // -- Hitungan Aset Akhir (TIDAK DIPENGARUHI SERVIS) --
        $totalAsetAwal = Item::sum(DB::raw('stok_awal_2026 * harga_satuan'));
        $totalAsetAkhir = $totalAsetAwal + $totalRupiahMasuk - $totalRupiahKeluar;

        // Cetak PDF
        $pdf = Pdf::loadView('reports.combined_pdf', compact(
            'gabunganStok',    // Tabel 1
            'maintenance',     // Tabel 2
            'totalQtyMasuk',
            'totalQtyKeluar',
            'totalRupiahMasuk',
            'totalRupiahKeluar',
            'totalBiayaServis', // Kirim Total Jasa
            'totalAsetAwal',
            'totalAsetAkhir',
            'tglAwal',
            'tglAkhir'
        ));

        $pdf->setPaper('a4', 'landscape');
        return $pdf->stream('Laporan-Lengkap-Terpisah.pdf');
    }
}