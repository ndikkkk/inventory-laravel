<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncomingController;
use App\Http\Controllers\OutgoingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Barang & Import
    Route::post('items/import', [ItemController::class, 'import'])->name('items.import');
    Route::get('items/print-stock', [ItemController::class, 'printStock'])->name('items.print');
    Route::resource('items', ItemController::class);

    // Barang Masuk
    Route::get('incoming/create', [IncomingController::class, 'create'])->name('incoming.create');
    Route::post('incoming', [IncomingController::class, 'store'])->name('incoming.store');

    // --- [UPDATE: BARANG KELUAR & APPROVAL] ---
    // 1. INPUT FORM (Tetap sama)
    Route::get('outgoing/create', [OutgoingController::class, 'create'])->name('outgoing.create');
    Route::post('outgoing', [OutgoingController::class, 'store'])->name('outgoing.store');

    // 2. HALAMAN KHUSUS APPROVAL (Menu: Persetujuan Barang) -> Admin only
    Route::get('outgoing/approval', [OutgoingController::class, 'approvalPage'])->name('outgoing.approval');

    // 3. HALAMAN RIWAYAT BERSIH (Menu: Barang Keluar) -> Hanya yang Approved
    Route::get('outgoing', [OutgoingController::class, 'index'])->name('outgoing.index');

    // 4. LOGIC AKSI ACC/TOLAK
    Route::post('outgoing/{id}/approve', [OutgoingController::class, 'approve'])->name('outgoing.approve');
    Route::post('outgoing/{id}/reject', [OutgoingController::class, 'reject'])->name('outgoing.reject');


    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    // Item
Route::get('items/export/excel', [ItemController::class, 'exportExcel'])->name('items.excel');

// Incoming
Route::get('incoming/export/pdf', [IncomingController::class, 'exportPdf'])->name('incoming.pdf');
Route::get('incoming/export/excel', [IncomingController::class, 'exportExcel'])->name('incoming.excel');

// Outgoing
Route::get('outgoing/export/pdf', [OutgoingController::class, 'exportPdf'])->name('outgoing.pdf');
Route::get('outgoing/export/excel', [OutgoingController::class, 'exportExcel'])->name('outgoing.excel');

// Reports
Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');

// Route Update Profil
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

// Update bagian Outgoing
// 1. Route Approval Page (Halaman Web)
Route::get('outgoing/approval', [OutgoingController::class, 'approvalPage'])->name('outgoing.approval');

// 2. Route PRINT Approval (PDF) -> Arahkan ke method 'printApproval'
Route::get('outgoing/approval/print', [OutgoingController::class, 'printApproval'])->name('outgoing.print_approval');

// 3. Route Print Barang Keluar (Hanya yang Approved/Bersih)
Route::get('outgoing/export/pdf', [OutgoingController::class, 'exportPdf'])->name('outgoing.pdf');
});