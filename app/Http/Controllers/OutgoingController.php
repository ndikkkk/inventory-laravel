<?php

namespace App\Http\Controllers;

use App\Models\OutgoingTransaction;
use App\Models\Item;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OutgoingExport;

class OutgoingController extends Controller
{
    // 1. Tampilkan Form Pengajuan
    public function create()
    {
        $items = Item::where('stok_saat_ini', '>', 0)->get();
        $user = Auth::user();

        // LOGIKA DIVISI
        if ($user->role == 'bidang') {
            // Cek apakah user sudah punya divisi?
            if (!$user->division_id) {
                return redirect()->route('dashboard')->with('error', 'Akun Anda belum di-setting masuk ke Bidang mana. Hubungi Admin.');
            }
            // Jika ada, ambil divisi sesuai ID user
            $divisions = Division::where('id', $user->division_id)->get();
        } else {
            // Jika Admin, ambil semua
            $divisions = Division::all();
        }

        return view('transactions.outgoing.create', compact('items', 'divisions'));
    }

    // 2. Simpan Pengajuan (Pending / Approved)
    public function store(Request $request)
    {
        $request->validate([
            'item_id'     => 'required',
            'division_id' => 'required',
            'tanggal'     => 'required|date',
            'jumlah'      => 'required|numeric|min:1',
        ]);

        $item = Item::find($request->item_id);

        // Cek Stok Awal (Validasi Saja)
        if ($item->stok_saat_ini < $request->jumlah) {
            return back()->with('error', 'Stok gudang tidak cukup untuk permintaan ini.');
        }

        // TENTUKAN STATUS:
        // Jika Admin yg input -> Langsung Approved.
        // Jika Bidang yg input -> Pending (Menunggu ACC).
        $status = Auth::user()->role == 'admin' ? 'approved' : 'pending';

        // Simpan Transaksi
        OutgoingTransaction::create([
            'item_id'     => $request->item_id,
            'division_id' => $request->division_id,
            'tanggal'     => $request->tanggal,
            'jumlah'      => $request->jumlah,
            'status'      => $status, // <--- Simpan status
        ]);

        // LOGIC STOK:
        // Hanya kurangi stok JIKA status langsung approved (Admin yg input)
        if ($status == 'approved') {
            $item->decrement('stok_saat_ini', $request->jumlah);
            return redirect()->route('outgoing.index')->with('success', 'Barang berhasil dikeluarkan (Langsung ACC)!');
        } else {
            // Jika Pending
            return redirect()->route('outgoing.index')->with('success', 'Pengajuan berhasil dikirim! Menunggu persetujuan Admin.');
        }
    }

    // 3. (BARU) Fungsi untuk Admin Menyetujui Pengajuan
    // Pasang ini di Route nantinya: Route::post('/outgoing/{id}/approve', [OutgoingController::class, 'approve']);
    public function approve($id)
    {
        // Pastikan yg akses admin
        if (Auth::user()->role != 'admin') {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $transaksi = OutgoingTransaction::findOrFail($id);
        $item = Item::findOrFail($transaksi->item_id);

        // Cek lagi stoknya sebelum diapprove (takutnya stok udah diambil orang lain)
        if ($item->stok_saat_ini < $transaksi->jumlah) {
            return back()->with('error', 'Gagal ACC! Stok barang saat ini sudah habis/kurang.');
        }

        // Ubah Status jadi Approved
        $transaksi->update(['status' => 'approved']);

        // POTONG STOK SEKARANG
        $item->decrement('stok_saat_ini', $transaksi->jumlah);

        return back()->with('success', 'Pengajuan disetujui. Stok telah dikurangi.');
    }

    // [MENU 1] BARANG KELUAR (Hanya yang sudah ACC, Tampilan Bersih)
    public function index()
    {
        $user = Auth::user();

        // Query Dasar
        $query = OutgoingTransaction::with(['item', 'division'])->orderBy('created_at', 'desc');

        // LOGIC: HANYA AMBIL YANG STATUSNYA 'APPROVED'
        if ($user->role == 'admin') {
            $transactions = $query->where('status', 'approved')->get();
        } else {
            // Kalau user bidang, lihat history approved mereka
            $transactions = $query->where('division_id', $user->division_id)
                                  ->get();
        }

        // Return ke View yang bersih (tanpa tombol aksi)
        return view('transactions.outgoing.index', compact('transactions'));
    }

    // [MENU 2] PERSETUJUAN BARANG (Semua Status, Ada Tombol Aksi)
    public function approvalPage()
    {
        // Pastikan hanya admin yang bisa akses
        if (Auth::user()->role != 'admin') {
            abort(403);
        }

        // Ambil SEMUA data (Pending, Approved, Rejected)
        // Urutkan: Pending paling atas, sisanya berdasarkan tanggal terbaru
        $transactions = OutgoingTransaction::with(['item', 'division'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc')
            ->get();

        // Return ke View khusus Approval (ada tombolnya)
        return view('transactions.outgoing.approval', compact('transactions'));
    }

    // Tambahan Function Reject (Jika mau tombol tolak)
    public function reject($id)
    {
         if (Auth::user()->role != 'admin') abort(403);

         $transaksi = OutgoingTransaction::findOrFail($id);
         $transaksi->update(['status' => 'rejected']);

         return back()->with('success', 'Pengajuan telah ditolak.');
    }

    // --- TAMBAHAN BARU: CETAK KHUSUS HALAMAN APPROVAL (SEMUA STATUS) ---
    public function printApproval()
    {
        // Ambil SEMUA data (tanpa filter status approved)
        $data = OutgoingTransaction::with(['item', 'division'])
                ->latest()
                ->get();

        // Load view khusus approval
        $pdf = Pdf::loadView('transactions.outgoing.pdf_approval', compact('data'));
        
        // Stream dengan judul yang benar
        return $pdf->stream('Laporan-Pengajuan-Barang.pdf');
    }
    
    
    // --- FITUR BARU: CETAK PDF BARANG KELUAR (Approved Only) ---
    public function exportPdf()
    {
        // Hanya ambil yang approved
        $data = OutgoingTransaction::with(['item', 'division'])
                ->where('status', 'approved')
                ->latest()
                ->get();

        $pdf = PDF::loadView('transactions.outgoing.pdf', compact('data'));
        return $pdf->stream('Laporan-Barang-Keluar.pdf');
    }

    // --- FITUR BARU: CETAK EXCEL BARANG KELUAR ---
    public function exportExcel()
    {
        return Excel::download(new OutgoingExport, 'Laporan-Barang-Keluar.xlsx');
    }
}