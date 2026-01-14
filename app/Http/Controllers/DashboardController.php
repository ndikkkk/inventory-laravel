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
        // 1. DATA GRAFIK STOK
        $items = Item::all();
        $labelBarang = $items->pluck('nama_barang')->toArray();
        $dataStok = $items->pluck('stok_saat_ini')->toArray();

        // 2. DATA PIE CHART (Barang Keluar per Divisi)
        $pieData = OutgoingTransaction::select('division_id', DB::raw('sum(jumlah) as total'))
            ->where('status', 'approved')
            ->with('division')
            ->groupBy('division_id')
            ->get();

        $grandTotal = $pieData->sum('total');
        $labelDivisi = $pieData->pluck('division.nama_bidang')->toArray();

        // Hitung Persentase
        $dataDivisi = $pieData->map(function($item) use ($grandTotal) {
            if ($grandTotal == 0) return 0;
            return round(($item->total / $grandTotal) * 100, 2);
        })->toArray();

        // 3. DATA GRAFIK BULANAN (Tahun Ini)
        $monthlyIncoming = array_fill(1, 12, 0);
        $monthlyOutgoing = array_fill(1, 12, 0);

        // Data Masuk
        $incoming = IncomingTransaction::select(
            DB::raw('MONTH(tanggal) as month'),
            DB::raw('SUM(jumlah) as total')
        )
        ->whereYear('tanggal', date('Y'))
        ->groupBy('month')
        ->pluck('total', 'month');

        // Data Keluar (Approved)
        $outgoing = OutgoingTransaction::select(
            DB::raw('MONTH(tanggal) as month'),
            DB::raw('SUM(jumlah) as total')
        )
        ->where('status', 'approved')
        ->whereYear('tanggal', date('Y'))
        ->groupBy('month')
        ->pluck('total', 'month');

        foreach ($incoming as $month => $total) {
            $monthlyIncoming[$month] = $total;
        }
        foreach ($outgoing as $month => $total) {
            $monthlyOutgoing[$month] = $total;
        }

        // 4. TOP 5 BARANG SERING KELUAR
        $top5Items = OutgoingTransaction::select('item_id', DB::raw('sum(jumlah) as total'))
            ->where('status', 'approved')
            ->with('item')
            ->groupBy('item_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 5. [BARU] TOP 5 BARANG SERING MASUK
        $top5Incoming = IncomingTransaction::select('item_id', DB::raw('sum(jumlah) as total'))
            ->with('item')
            ->groupBy('item_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'labelBarang', 'dataStok',
            'labelDivisi', 'dataDivisi',
            'monthlyIncoming', 'monthlyOutgoing',
            'top5Items', 'top5Incoming' // Tambahkan ini
        ));
    }
}