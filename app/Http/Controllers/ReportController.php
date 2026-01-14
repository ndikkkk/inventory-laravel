<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncomingTransaction;
use App\Models\OutgoingTransaction;
use App\Models\Item; // Jangan lupa import model Item
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class ReportController extends Controller
{
    public function index()
    {
        // 1. Data Masuk
        $masuk = IncomingTransaction::with('item')
                    ->orderBy('tanggal', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($item){
                        $item->jenis = 'masuk';
                        return $item;
                    });

        // 2. Data Keluar (Approved)
        $keluar = OutgoingTransaction::with(['item', 'division'])
                    ->where('status', 'approved')
                    ->orderBy('tanggal', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($item){
                        $item->jenis = 'keluar';
                        return $item;
                    });

        // 3. Gabungan
        $gabungan = $masuk->concat($keluar)->sortByDesc(function($item) {
            return $item->tanggal . $item->created_at;
        });

        return view('reports.index', compact('masuk', 'keluar', 'gabungan'));
    }

    // EXPORT PDF GABUNGAN (FIXED ERROR Undefined Variable)
    public function exportAllPdf()
    {
        // 1. Ambil Data Masuk
        $masuk = IncomingTransaction::with('item')->get()->map(function($item){
            $item->jenis_transaksi = 'masuk';
            return $item;
        });

        // 2. Ambil Data Keluar
        $keluar = OutgoingTransaction::with(['item', 'division'])
            ->where('status', 'approved')
            ->get()
            ->map(function($item){
                $item->jenis_transaksi = 'keluar';
                return $item;
            });

        // 3. Gabungan
        $gabungan = $masuk->concat($keluar)->sortBy(function($item) {
            return $item->tanggal . $item->created_at;
        });

        // ==========================================
        // PERHITUNGAN LENGKAP (AGAR TIDAK ERROR)
        // ==========================================

        // A. Total Qty
        $totalQtyMasuk  = $masuk->sum('jumlah');
        $totalQtyKeluar = $keluar->sum('jumlah');

        // B. Total Nilai (Rupiah) Transaksi
        $totalRupiahMasuk  = $masuk->sum('total_harga');
        $totalRupiahKeluar = $keluar->sum('total_harga');

        // C. Grand Total Nilai (Total kolom paling kanan tabel) -> INI YANG BIKIN ERROR TADI
        $grandTotalNilai = $gabungan->sum('total_harga');

        // D. Total Aset Awal (Saldo Awal Master Barang)
        $totalAsetAwal = Item::get()->sum(function($item){
            return $item->stok_awal_2026 * $item->harga_satuan;
        });

        // E. Total Aset Akhir (Stok Saat Ini)
        $totalAsetAkhir = Item::get()->sum(function($item){
            return $item->stok_saat_ini * $item->harga_satuan;
        });

        // 4. Cetak PDF (Kirim SEMUA variabel)
        $pdf = Pdf::loadView('reports.combined_pdf', compact(
            'gabungan',
            'totalQtyMasuk',
            'totalQtyKeluar',
            'totalRupiahMasuk',
            'totalRupiahKeluar',
            'grandTotalNilai', // <-- Sudah ditambahkan kembali
            'totalAsetAwal',
            'totalAsetAkhir'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan-Seluruh-Transaksi.pdf');
    }
}
