<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Import Model
use App\Models\Item;
use App\Models\IncomingTransaction;
use App\Models\OutgoingTransaction;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }


    public function boot(): void
    {
        // === 1. PAKSA HTTPS (WAJIB BUAT NGROK) ===
        // Ini biar CSS & Gambar gak hilang saat dibuka via Ngrok
        // URL::forceScheme('https');

        // === 2. SETTING PAGINATION ===
        // Paginator::useBootstrapFive();

        // === 3. LOGIKA HITUNG ASET HEADER (GLOBAL) ===
        View::composer('layouts.app', function ($view) {
            $totalAsetGlobal = 0;

            if (Schema::hasTable('items') && Schema::hasTable('incoming_transactions') && Schema::hasTable('outgoing_transactions')) {

                // A. Aset Awal (Modal)
                $asetAwal = Item::sum(DB::raw('stok_awal_2026 * harga_satuan'));

                // B. Aset Masuk (Belanja Barang)
                $asetMasuk = IncomingTransaction::sum('total_harga');

                // C. Aset Keluar (HANYA BARANG, JASA TIDAK DIHITUNG)
                $asetKeluar = OutgoingTransaction::where('status', 'approved')
                                ->whereNotNull('item_id') // Filter hanya yang punya ID Barang
                                ->sum('total_harga');

                // D. RUMUS ASET MURNI
                $totalAsetGlobal = $asetAwal + $asetMasuk - $asetKeluar;
            }

            $view->with('totalAsetGlobal', $totalAsetGlobal);
        });
    }
}