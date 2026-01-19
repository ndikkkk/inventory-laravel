<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\IncomingController;
use App\Http\Controllers\OutgoingController; // Pastikan ini ada
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
    Route::delete('/items/delete-all', [ItemController::class, 'deleteAll'])->name('items.deleteAll');
    Route::post('items/import', [ItemController::class, 'import'])->name('items.import');
    Route::get('items/print-stock', [ItemController::class, 'printStock'])->name('items.print');
    Route::get('items/export/excel', [ItemController::class, 'exportExcel'])->name('items.excel');
    
    Route::resource('items', ItemController::class);

    // C. Barang Masuk (Incoming)
    Route::get('incoming/export/pdf', [IncomingController::class, 'exportPdf'])->name('incoming.pdf');
    Route::get('incoming/export/excel', [IncomingController::class, 'exportExcel'])->name('incoming.excel');
    Route::get('incoming/create', [IncomingController::class, 'create'])->name('incoming.create');
    Route::post('incoming', [IncomingController::class, 'store'])->name('incoming.store');

    // D. Barang Keluar (Outgoing) & Approval
    // --- 1. Export & Print (Letakkan Paling Atas) ---
    Route::get('outgoing/export/pdf', [OutgoingController::class, 'exportPdf'])->name('outgoing.pdf');
    Route::get('outgoing/export/excel', [OutgoingController::class, 'exportExcel'])->name('outgoing.excel');
    Route::get('outgoing/approval/print', [OutgoingController::class, 'printApproval'])->name('outgoing.print_approval');

    // --- 2. Halaman Khusus (Maintenance & Create & Approval) ---
    // PENTING: Maintenance ditaruh DISINI (sebelum route yang pakai ID)
    Route::get('outgoing/maintenance', [OutgoingController::class, 'createMaintenance'])->name('outgoing.maintenance');
    Route::post('outgoing/maintenance', [OutgoingController::class, 'storeMaintenance'])->name('outgoing.store_maintenance');
    
    Route::get('outgoing/approval', [OutgoingController::class, 'approvalPage'])->name('outgoing.approval');
    Route::get('outgoing/create', [OutgoingController::class, 'create'])->name('outgoing.create');

    // --- 3. Logic dengan ID (Wildcard) ---
    Route::post('outgoing/{id}/approve', [OutgoingController::class, 'approve'])->name('outgoing.approve');
    Route::post('outgoing/{id}/reject', [OutgoingController::class, 'reject'])->name('outgoing.reject');

    // --- 4. Index & Store Standar ---
    Route::post('outgoing', [OutgoingController::class, 'store'])->name('outgoing.store');
    Route::get('outgoing', [OutgoingController::class, 'index'])->name('outgoing.index');

    // E. Laporan (Reports)
    Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::get('reports/export/all', [ReportController::class, 'exportAllPdf'])->name('reports.export_all'); // Rapikan namespace
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // F. Profil User
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});