<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\IncomingTransaction;
use App\Models\OutgoingTransaction;
use Illuminate\Support\Facades\DB; // WAJIB ADA: Untuk query database raw

class DashboardController extends Controller
{
    public function index()
    {
        // 1. DATA GRAFIK STOK (YANG SUDAH ADA)
        $items = Item::all();
        $labelBarang = $items->pluck('nama_barang')->toArray();
        $dataStok = $items->pluck('stok_saat_ini')->toArray();

        // 2. DATA PIE CHART (Barang Keluar per Divisi)
        // Group by division_id dan jumlahkan quantity-nya
        $pieData = OutgoingTransaction::select('division_id', DB::raw('sum(jumlah) as total'))
            ->with('division')
            ->groupBy('division_id')
            ->get();
        
        // --- [BARU] Hitung Total Semua Barang Keluar ---
        $grandTotal = $pieData->sum('total');

        $labelDivisi = $pieData->pluck('division.nama_bidang')->toArray();

        // --- [UBAH BAGIAN INI] ---
        // Jangan pakai pluck('total'), tapi kita hitung persennya di sini
        $dataDivisi = $pieData->map(function($item) use ($grandTotal) {
            // Cek biar tidak error division by zero
            if ($grandTotal == 0) return 0;
            
            // Rumus: (Jumlah Item / Total Semua) * 100
            $persen = ($item->total / $grandTotal) * 100;
            
            // Kita bulatkan 2 angka desimal biar rapi (opsional)
            return round($persen, 2); 
        })->toArray();

        // 3. DATA GRAFIK BULANAN (Tahun Ini)
        // Kita siapkan array kosong untuk bulan 1-12 agar grafik urut Jan-Des
        $monthlyIncoming = array_fill(1, 12, 0);
        $monthlyOutgoing = array_fill(1, 12, 0);

        // Ambil data masuk tahun ini
        $incoming = IncomingTransaction::select(
            DB::raw('MONTH(tanggal) as month'), 
            DB::raw('SUM(jumlah) as total')
        )
        ->whereYear('tanggal', date('Y'))
        ->groupBy('month')
        ->pluck('total', 'month');

        // Ambil data keluar tahun ini
        $outgoing = OutgoingTransaction::select(
            DB::raw('MONTH(tanggal) as month'), 
            DB::raw('SUM(jumlah) as total')
        )
        ->whereYear('tanggal', date('Y'))
        ->groupBy('month')
        ->pluck('total', 'month');

        // Gabungkan data asli ke array 1-12
        foreach ($incoming as $month => $total) {
            $monthlyIncoming[$month] = $total;
        }
        foreach ($outgoing as $month => $total) {
            $monthlyOutgoing[$month] = $total;
        }

        // 4. TOP 5 BARANG SERING KELUAR
        $top5Items = OutgoingTransaction::select('item_id', DB::raw('sum(jumlah) as total'))
            ->with('item')
            ->groupBy('item_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'labelBarang', 'dataStok',          // Data Stok
            'labelDivisi', 'dataDivisi',        // Data Pie Chart
            'monthlyIncoming', 'monthlyOutgoing', // Data Bulanan
            'top5Items'                         // Data Top 5
        ));
    }
}