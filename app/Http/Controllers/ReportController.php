<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncomingTransaction;
use App\Models\OutgoingTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FullReportExport; // Nanti dibuat

class ReportController extends Controller
{
    public function index()
    {
        // Ambil data barang masuk, urutkan dari yang terbaru
        $masuk = IncomingTransaction::with('item')->latest()->get();

        // Ambil data barang keluar, lengkap dengan info barang & divisi
        $keluar = OutgoingTransaction::with(['item', 'division'])->latest()->get();

        return view('reports.index', compact('masuk', 'keluar'));
    }

    // --- CETAK LAPORAN LENGKAP (PDF) ---
    public function exportIncomingPdf()
    {
        // Ambil data
        $data = IncomingTransaction::with('item')->latest()->get();

        // Load View (Pastikan nama filenya nanti kita buat sama dengan ini)
        $pdf = Pdf::loadView('reports.incoming_pdf', compact('data'));
        
        // Stream (Tampilkan di browser)
        return $pdf->stream('Laporan-Barang-Masuk.pdf');
    }

    // --- CETAK PDF KHUSUS BARANG KELUAR (Opsional, buat jaga-jaga) ---
    public function exportOutgoingPdf()
    {
        $data = OutgoingTransaction::with(['item', 'division'])->latest()->get();
        $pdf = Pdf::loadView('reports.outgoing_pdf', compact('data'));
        return $pdf->stream('Laporan-Barang-Keluar.pdf');
    }
}