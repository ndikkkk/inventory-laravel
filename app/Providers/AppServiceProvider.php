<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // Tambahkan ini
use App\Models\Item; // Tambahkan ini
use Illuminate\Support\Facades\DB; // Tambahkan ini

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Logic: Hitung Total Aset (Stok * Harga) untuk ditampilkan di Navbar
        // Gunakan View Composer (*) agar data ini ada di SEMUA halaman
        View::composer('*', function ($view) {
            $totalAsetGlobal = 0;
            
            // Cek tabel items dulu agar tidak error saat migrasi awal
            if (\Illuminate\Support\Facades\Schema::hasTable('items')) {
                $totalAsetGlobal = Item::sum(DB::raw('stok_saat_ini * harga_satuan'));
            }

            $view->with('totalAsetGlobal', $totalAsetGlobal);
        });
    }
}