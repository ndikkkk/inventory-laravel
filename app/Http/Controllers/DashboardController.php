<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\IncomingTransaction;
use App\Models\OutgoingTransaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ==========================================
        // A. DATA KARTU STATISTIK (RINGKASAN ATAS)
        // ==========================================
        $totalBarang = Item::count();
        // Stok Menipis (Misal kurang dari 5)
        $stokMenipis = Item::where('stok_saat_ini', '<', 5)->count();
        // Transaksi Hari Ini
        $transaksiMasukToday  = IncomingTransaction::whereDate('tanggal', date('Y-m-d'))->count();
        $transaksiKeluarToday = OutgoingTransaction::whereDate('tanggal', date('Y-m-d'))->where('status', 'approved')->count();

        // Total Aset (Untuk body dashboard, kalau mau ditampilkan lagi selain di navbar)
        $totalAset = Item::sum(DB::raw('stok_saat_ini * harga_satuan'));


        // ==========================================
        // B. DATA GRAFIK & TABEL
        // ==========================================

        // 1. DATA GRAFIK STOK BARANG
        $items = Item::all();
        $labelBarang = $items->pluck('nama_barang')->toArray();
        $dataStok    = $items->pluck('stok_saat_ini')->toArray();

        // 2. DATA PIE CHART (Penggunaan per Divisi)
        $pieData = OutgoingTransaction::select('division_id', DB::raw('sum(jumlah) as total'))
            ->where('status', 'approved')
            ->with('division')
            ->groupBy('division_id')
            ->get();

        $grandTotal = $pieData->sum('total');
        $labelDivisi = $pieData->pluck('division.nama_bidang')->toArray();

        // Hitung Persentase untuk Pie Chart
        $dataDivisi = $pieData->map(function($item) use ($grandTotal) {
            return $grandTotal > 0 ? round(($item->total / $grandTotal) * 100, 2) : 0;
        })->toArray();

        // 3. DATA GRAFIK BULANAN (Area Chart)
        $monthlyIncoming = array_fill(1, 12, 0);
        $monthlyOutgoing = array_fill(1, 12, 0);
        $currentYear = date('Y');

        // Query Masuk
        $incoming = IncomingTransaction::select(
            DB::raw('MONTH(tanggal) as month'),
            DB::raw('SUM(jumlah) as total')
        )
        ->whereYear('tanggal', $currentYear)
        ->groupBy('month')
        ->pluck('total', 'month');

        // Query Keluar
        $outgoing = OutgoingTransaction::select(
            DB::raw('MONTH(tanggal) as month'),
            DB::raw('SUM(jumlah) as total')
        )
        ->where('status', 'approved')
        ->whereYear('tanggal', $currentYear)
        ->groupBy('month')
        ->pluck('total', 'month');

        // Mapping ke array 1-12
        foreach ($incoming as $month => $total) {
            $monthlyIncoming[$month] = $total;
        }
        foreach ($outgoing as $month => $total) {
            $monthlyOutgoing[$month] = $total;
        }

        // 4. TOP 5 BARANG KELUAR
        $top5Items = OutgoingTransaction::select('item_id', DB::raw('sum(jumlah) as total'))
            ->where('status', 'approved')
            ->whereNotNull('item_id')
            ->with('item')
            ->groupBy('item_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 5. TOP 5 BARANG MASUK (RESTOCK)
        $top5Incoming = IncomingTransaction::select('item_id', DB::raw('sum(jumlah) as total'))
            ->with('item')
            ->groupBy('item_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Kirim semua data ke View
        return view('dashboard.index', compact(
            // Data Kartu
            'totalBarang', 'stokMenipis', 'transaksiMasukToday', 'transaksiKeluarToday', 'totalAset',
            // Data Grafik
            'labelBarang', 'dataStok',
            'labelDivisi', 'dataDivisi',
            'monthlyIncoming', 'monthlyOutgoing',
            // Data Tabel Top 5
            'top5Items', 'top5Incoming'
        ));
    }
}