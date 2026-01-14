<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\IncomingController;
use App\Http\Controllers\OutgoingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. HALAMAN UTAMA & AUTHENTICATION ---
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// --- 2. HALAMAN SETELAH LOGIN (DASHBOARD & FITUR) ---
Route::middleware('auth')->group(function () {

    // A. Dashboard & Logout
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // B. Master Barang (Items)
    // PENTING: Route 'delete-all', 'import', 'print', 'export' WAJIB di atas resource
    Route::delete('/items/delete-all', [ItemController::class, 'deleteAll'])->name('items.deleteAll');
    Route::post('items/import', [ItemController::class, 'import'])->name('items.import');
    Route::get('items/print-stock', [ItemController::class, 'printStock'])->name('items.print');
    Route::get('items/export/excel', [ItemController::class, 'exportExcel'])->name('items.excel');

    Route::resource('items', ItemController::class); // Resource ditaruh paling bawah di grup ini

    // C. Barang Masuk (Incoming)
    // Export
    Route::get('incoming/export/pdf', [IncomingController::class, 'exportPdf'])->name('incoming.pdf');
    Route::get('incoming/export/excel', [IncomingController::class, 'exportExcel'])->name('incoming.excel');
    // Form & Action
    Route::get('incoming/create', [IncomingController::class, 'create'])->name('incoming.create');
    Route::post('incoming', [IncomingController::class, 'store'])->name('incoming.store');

    // D. Barang Keluar (Outgoing) & Approval
    // 1. Export & Print (Letakkan di atas agar tidak dianggap ID)
    Route::get('outgoing/export/pdf', [OutgoingController::class, 'exportPdf'])->name('outgoing.pdf');
    Route::get('outgoing/export/excel', [OutgoingController::class, 'exportExcel'])->name('outgoing.excel');
    Route::get('outgoing/approval/print', [OutgoingController::class, 'printApproval'])->name('outgoing.print_approval');

    // 2. Halaman Approval & Logic ACC/Tolak (Admin Only)
    Route::get('outgoing/approval', [OutgoingController::class, 'approvalPage'])->name('outgoing.approval');
    Route::post('outgoing/{id}/approve', [OutgoingController::class, 'approve'])->name('outgoing.approve');
    Route::post('outgoing/{id}/reject', [OutgoingController::class, 'reject'])->name('outgoing.reject');

    // 3. Form Pengajuan & List Riwayat
    Route::get('outgoing/create', [OutgoingController::class, 'create'])->name('outgoing.create');
    Route::post('outgoing', [OutgoingController::class, 'store'])->name('outgoing.store');
    Route::get('outgoing', [OutgoingController::class, 'index'])->name('outgoing.index');

    // E. Laporan (Reports)
    Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    // CETAK LAPORAN GABUNGAN (ALL TRANSACTIONS)
    Route::get('reports/export/all', [App\Http\Controllers\ReportController::class, 'exportAllPdf'])->name('reports.export_all');

    // F. Profil User
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
