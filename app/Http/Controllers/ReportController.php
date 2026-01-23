<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncomingTransaction;
use App\Models\OutgoingTransaction;
use App\Models\Item;
use App\Models\Account;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // --- INDEX TIDAK BERUBAH ---
    public function index()
    {
        // 1. Logic Data Tabel (Punya Mas)
        $masuk = IncomingTransaction::with('item')->orderBy('tanggal', 'asc')->get()->map(function($item){ $item->jenis = 'masuk'; return $item; });
        $keluar = OutgoingTransaction::with(['item', 'division'])->where('status', 'approved')->orderBy('tanggal', 'asc')->get()->map(function($item){ $item->jenis = 'keluar'; return $item; });
        $gabungan = $masuk->concat($keluar)->sortBy(function($item) { return $item->tanggal . $item->created_at; });

        // 2. Logic Dropdown (Tambahan)
        $level1 = Account::where('level', 1)->get();
        $level2 = Account::where('level', 2)->get();
        $level3 = Account::where('level', 3)->get();

        return view('reports.index', compact('masuk', 'keluar', 'gabungan', 'level1', 'level2', 'level3'));
    }

    // --- EXPORT ALL (LOGIC MAS) ---
    public function exportAllPdf(Request $request)
    {
        $tglAwal  = $request->input('tgl_awal');
        $tglAkhir = $request->input('tgl_akhir');

        // Logic Mas: Filter tanggal HANYA JIKA diisi
        $queryMasuk = IncomingTransaction::with('item');
        if (!empty($tglAwal) && !empty($tglAkhir)) {
            $queryMasuk->whereDate('tanggal', '>=', $tglAwal)->whereDate('tanggal', '<=', $tglAkhir);
        }
        $masuk = $queryMasuk->get()->map(function($item){ $item->jenis_transaksi = 'masuk'; return $item; });

        $queryKeluar = OutgoingTransaction::with(['item', 'division'])->where('status', 'approved')->whereNotNull('item_id');
        if (!empty($tglAwal) && !empty($tglAkhir)) {
            $queryKeluar->whereDate('tanggal', '>=', $tglAwal)->whereDate('tanggal', '<=', $tglAkhir);
        }
        $keluarBarang = $queryKeluar->get()->map(function($item){ $item->jenis_transaksi = 'keluar'; return $item; });

        $queryMaint = OutgoingTransaction::with('division')->where('status', 'approved')->whereNull('item_id');
        if (!empty($tglAwal) && !empty($tglAkhir)) {
            $queryMaint->whereDate('tanggal', '>=', $tglAwal)->whereDate('tanggal', '<=', $tglAkhir);
        }
        $maintenance = $queryMaint->orderBy('tanggal', 'asc')->get();

        $gabunganStok = $masuk->concat($keluarBarang)->sortBy(function($item) { return $item->tanggal . $item->created_at; });

        // Hitungan
        $totalQtyMasuk = $masuk->sum('jumlah'); $totalQtyKeluar = $keluarBarang->sum('jumlah');
        $totalRupiahMasuk = $masuk->sum('total_harga'); $totalRupiahKeluar = $keluarBarang->sum('total_harga');
        $totalBiayaServis = $maintenance->sum('total_harga');
        $totalAsetAwal = Item::sum(DB::raw('stok_awal_2026 * harga_satuan'));
        $totalAsetAkhir = $totalAsetAwal + $totalRupiahMasuk - $totalRupiahKeluar;

        $pdf = Pdf::loadView('reports.combined_pdf', compact('gabunganStok', 'maintenance', 'totalQtyMasuk', 'totalQtyKeluar', 'totalRupiahMasuk', 'totalRupiahKeluar', 'totalBiayaServis', 'totalAsetAwal', 'totalAsetAkhir', 'tglAwal', 'tglAkhir'));
        $pdf->setPaper('a4', 'landscape');
        return $pdf->stream('Laporan-Lengkap.pdf');
    }

    // =========================================================================
    // FITUR BARU (YANG KEMAREN ERROR KARENA REQUIRED) -> SUDAH DIPERBAIKI
    // =========================================================================

    // 1. CETAK MUTASI (SALDO)
    // 1. CETAK MUTASI (SALDO) - REVISI HITUNGAN RUPIAH
    public function printMutasi(Request $request) {
        $scope = $request->scope;
        $id = $request->account_id;

        // Default Tanggal
        $start = $request->tgl_awal ?: '2026-01-01';
        $end   = $request->tgl_akhir ?: date('Y-m-d');

        $query = Item::with(['account.parent.parent']);
        $this->applyHierarchyFilter($query, $scope, $id);
        $items = $query->orderBy('account_id')->get();

        $laporan = $items->map(function ($item) use ($start, $end) {
            // 1. HITUNG HISTORI MASA LALU (Untuk Saldo Awal)
            // Qty
            $masuk_lalu_qty = IncomingTransaction::where('item_id', $item->id)->where('tanggal', '<', $start)->sum('jumlah');
            $keluar_lalu_qty = OutgoingTransaction::where('item_id', $item->id)->where('status', 'approved')->where('tanggal', '<', $start)->sum('jumlah');
            // Rupiah (PENTING: Ambil Total Harga Asli Transaksi, Jangan dikali harga master)
            $masuk_lalu_rp = IncomingTransaction::where('item_id', $item->id)->where('tanggal', '<', $start)->sum('total_harga');
            $keluar_lalu_rp = OutgoingTransaction::where('item_id', $item->id)->where('status', 'approved')->where('tanggal', '<', $start)->sum('total_harga');

            // Set Saldo Awal
            $saldo_awal_qty = $item->stok_awal_2026 + $masuk_lalu_qty - $keluar_lalu_qty;
            // Rumus Rupiah Awal: (Stok Master * Harga Master) + Riwayat Masuk - Riwayat Keluar
            $saldo_awal_rp  = ($item->stok_awal_2026 * $item->harga_satuan) + $masuk_lalu_rp - $keluar_lalu_rp;

            // 2. HITUNG PERIODE INI (Mutasi)
            // Qty
            $masuk_kini_qty = IncomingTransaction::where('item_id', $item->id)->whereBetween('tanggal', [$start, $end])->sum('jumlah');
            $keluar_kini_qty = OutgoingTransaction::where('item_id', $item->id)->where('status', 'approved')->whereBetween('tanggal', [$start, $end])->sum('jumlah');
            // Rupiah (Ambil Total Harga Asli)
            $masuk_kini_rp = IncomingTransaction::where('item_id', $item->id)->whereBetween('tanggal', [$start, $end])->sum('total_harga');
            $keluar_kini_rp = OutgoingTransaction::where('item_id', $item->id)->where('status', 'approved')->whereBetween('tanggal', [$start, $end])->sum('total_harga');

            // 3. HITUNG SALDO AKHIR
            $saldo_akhir_qty = $saldo_awal_qty + $masuk_kini_qty - $keluar_kini_qty;
            $saldo_akhir_rp  = $saldo_awal_rp + $masuk_kini_rp - $keluar_kini_rp;

            // 4. MASUKKAN KE OBJECT ITEM
            $item->saldo_awal = $saldo_awal_qty;
            $item->nilai_saldo_awal = $saldo_awal_rp;

            $item->masuk = $masuk_kini_qty;
            $item->nilai_masuk = $masuk_kini_rp;

            $item->keluar = $keluar_kini_qty;
            $item->nilai_keluar = $keluar_kini_rp;

            $item->saldo_akhir = $saldo_akhir_qty;
            $item->nilai_saldo_akhir = $saldo_akhir_rp;

            return $item;
        });

        $groupedData = $this->groupDataForPdf($laporan);
        $pdf = Pdf::loadView('reports.pdf_mutasi', compact('groupedData', 'start', 'end'));
        $pdf->setPaper('f4', 'landscape');
        return $pdf->stream('Laporan_Mutasi.pdf');
    }

    // 2. CETAK MASUK (HIERARKI)
    public function printMasuk(Request $request) {
        $start = $request->tgl_awal; $end = $request->tgl_akhir; $scope = $request->scope; $id = $request->account_id;

        $query = IncomingTransaction::with(['item.account.parent.parent']);

        // LOGIKA FIX: Hanya filter tanggal jika diisi
        if (!empty($start) && !empty($end)) {
            $query->whereBetween('tanggal', [$start, $end]);
        }

        if ($scope == 'level_3') $query->whereHas('item', fn($q) => $q->where('account_id', $id));
        elseif ($scope == 'level_2') $query->whereHas('item.account', fn($q) => $q->where('parent_id', $id));
        elseif ($scope == 'level_1') $query->whereHas('item.account.parent', fn($q) => $q->where('parent_id', $id));

        $groupedData = $this->groupDataForPdf($query->get(), true);
        $pdf = Pdf::loadView('transactions.incoming.pdf', compact('groupedData', 'start', 'end'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Laporan_Masuk.pdf');
    }

    // 3. CETAK KELUAR (HIERARKI)
    public function printKeluar(Request $request) {
        $start = $request->tgl_awal; $end = $request->tgl_akhir; $scope = $request->scope; $id = $request->account_id;

        $query = OutgoingTransaction::with(['item.account.parent.parent', 'division'])
                    ->where('status', 'approved')->whereNotNull('item_id');

        // LOGIKA FIX: Hanya filter tanggal jika diisi
        if (!empty($start) && !empty($end)) {
            $query->whereBetween('tanggal', [$start, $end]);
        }

        if ($scope == 'level_3') $query->whereHas('item', fn($q) => $q->where('account_id', $id));
        elseif ($scope == 'level_2') $query->whereHas('item.account', fn($q) => $q->where('parent_id', $id));
        elseif ($scope == 'level_1') $query->whereHas('item.account.parent', fn($q) => $q->where('parent_id', $id));

        $groupedData = $this->groupDataForPdf($query->get(), true);
        $pdf = Pdf::loadView('transactions.outgoing.pdf', compact('groupedData', 'start', 'end'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Laporan_Keluar.pdf');
    }

    // Helper
    private function applyHierarchyFilter($query, $scope, $id) {
        if ($scope == 'level_3') $query->where('account_id', $id);
        elseif ($scope == 'level_2') $query->whereHas('account', fn($q) => $q->where('parent_id', $id));
        elseif ($scope == 'level_1') $query->whereHas('account.parent', fn($q) => $q->where('parent_id', $id));
    }
    private function groupDataForPdf($collection, $isTransaction = false) {
        return $collection->groupBy(function($obj) use ($isTransaction) { $item = $isTransaction ? $obj->item : $obj; return $item->account->parent->parent->nama_akun ?? 'Lainnya'; })
        ->map(function($l1) use ($isTransaction) { return $l1->groupBy(function($obj) use ($isTransaction) { $item = $isTransaction ? $obj->item : $obj; return $item->account->parent->nama_akun ?? '-'; })
        ->map(function($l2) use ($isTransaction) { return $l2->groupBy(function($obj) use ($isTransaction) { $item = $isTransaction ? $obj->item : $obj; return $item->account->nama_akun ?? '-'; }); }); });
    }
}
