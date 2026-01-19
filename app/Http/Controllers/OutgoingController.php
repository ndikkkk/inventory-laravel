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
            if (!$user->division_id) {
                return redirect()->route('dashboard')->with('error', 'Akun Anda belum di-setting masuk ke Bidang mana. Hubungi Admin.');
            }
            $divisions = Division::where('id', $user->division_id)->get();
        } else {
            $divisions = Division::all();
        }

        return view('transactions.outgoing.create', compact('items', 'divisions'));
    }

    // 2. Simpan Pengajuan (Pending / Approved) + LOGIC SISA STOK
    public function store(Request $request)
    {
        $request->validate([
            'item_id'     => 'required',
            'division_id' => 'required',
            'tanggal'     => 'required|date',
            'jumlah'      => 'required|numeric|min:1',
        ]);

        $item = Item::findOrFail($request->item_id);

        // Cek Stok Awal
        if ($item->stok_saat_ini < $request->jumlah) {
            return back()->with('error', 'Stok gudang tidak cukup untuk permintaan ini.');
        }

        // Ambil harga dari Master Barang
        $harga_saat_ini = $item->harga_satuan;
        $total_rupiah   = $request->jumlah * $harga_saat_ini;

        // TENTUKAN STATUS
        $status = Auth::user()->role == 'admin' ? 'approved' : 'pending';

        // --- LOGIKA SISA STOK (Khusus Admin Input Langsung) ---
        $sisaStok = null; // Default null jika pending
        if ($status == 'approved') {
            // Jika admin input, stok langsung berkurang sekarang
            $sisaStok = $item->stok_saat_ini - $request->jumlah;
        }

        // Simpan Transaksi
        OutgoingTransaction::create([
            'item_id'      => $request->item_id,
            'division_id'  => $request->division_id,
            'tanggal'      => $request->tanggal,
            'jumlah'       => $request->jumlah,
            'status'       => $status,
            'harga_satuan' => $harga_saat_ini,
            'total_harga'  => $total_rupiah,
            'sisa_stok'    => $sisaStok // Simpan Sisa Stok (Jika Approved)
        ]);

        // LOGIC STOK DI MASTER BARANG
        if ($status == 'approved') {
            $item->decrement('stok_saat_ini', $request->jumlah);

            return redirect()->route('outgoing.index')
                ->with('success', 'Barang keluar! Nilai aset berkurang Rp ' . number_format($total_rupiah));
        } else {
            return redirect()->route('outgoing.index')
                ->with('success', 'Pengajuan berhasil dikirim! Menunggu persetujuan Admin.');
        }
    }

    // 3. Fungsi Admin Menyetujui (Approve) + CATAT SISA STOK
    public function approve($id)
    {
        if (Auth::user()->role != 'admin') {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $transaksi = OutgoingTransaction::findOrFail($id);
        $item = Item::findOrFail($transaksi->item_id);

        // Cek Stok lagi sebelum diapprove
        if ($item->stok_saat_ini < $transaksi->jumlah) {
            return back()->with('error', 'Gagal ACC! Stok barang saat ini sudah habis/kurang.');
        }

        // --- UPDATE HARGA & SISA STOK ---
        $harga_terbaru = $item->harga_satuan;
        $total_terbaru = $transaksi->jumlah * $harga_terbaru;

        // HITUNG SISA STOK SETELAH DIKURANGI
        $stokBaru = $item->stok_saat_ini - $transaksi->jumlah;

        // Update Transaksi
        $transaksi->update([
            'status'       => 'approved',
            'harga_satuan' => $harga_terbaru,
            'total_harga'  => $total_terbaru,
            'sisa_stok'    => $stokBaru // <--- PENTING: Catat Sisa Stok di sini
        ]);

        // POTONG STOK DI MASTER BARANG
        $item->stok_saat_ini = $stokBaru; // Pakai nilai yang sudah dihitung agar sinkron
        $item->save();

        return back()->with('success', 'Pengajuan disetujui. Stok berkurang & Sisa Stok tercatat.');
    }

    // [MENU 1] BARANG KELUAR (Hanya yang sudah ACC)
    public function index()
    {
        $user = Auth::user();
        $query = OutgoingTransaction::with(['item', 'division'])->orderBy('tanggal', 'asc')->orderBy('created_at', 'asc');

        if ($user->role == 'admin') {
            $transactions = $query->where('status', 'approved')->get();
        } else {
            $transactions = $query->where('division_id', $user->division_id)->get();
        }

        return view('transactions.outgoing.index', compact('transactions'));
    }

    // [MENU 2] PERSETUJUAN BARANG
    public function approvalPage()
    {
        if (Auth::user()->role != 'admin') abort(403);

        $transactions = OutgoingTransaction::with(['item', 'division'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('tanggal', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('transactions.outgoing.approval', compact('transactions'));
    }

    // Fungsi Reject
    public function reject($id)
    {
         if (Auth::user()->role != 'admin') abort(403);

         $transaksi = OutgoingTransaction::findOrFail($id);
         $transaksi->update(['status' => 'rejected']);

         return back()->with('success', 'Pengajuan telah ditolak.');
    }
    // Tambahkan Method Baru

// 1. Tampilkan Form Khusus Pemeliharaan
public function createMaintenance()
{
    $divisions = \App\Models\Division::all();
    // Kita tidak butuh list item ATK, karena user akan input nama sparepart/jasa manual
    return view('transactions.outgoing.maintenance_create', compact('divisions'));
}

// 2. Simpan Data Pemeliharaan
public function storeMaintenance(Request $request)
{
    $request->validate([
        'tanggal'        => 'required|date',
        'nama_item'      => 'required|string', // Nama Jasa/Sparepart
        'harga'          => 'required|numeric',
        'division_id'    => 'required',
        'km_saat_ini'    => 'nullable|integer',
        'km_berikutnya'  => 'nullable|integer',
    ]);

    // A. Cari/Buat Kategori "Pemeliharaan Mesin"
    $kategori = \App\Models\Category::firstOrCreate(['nama_kategori' => 'Pemeliharaan Mesin']);

    // B. Buat "Barang Dummy/Jasa" secara otomatis
    // Karena ini maintenance, kita anggap ini barang habis pakai langsung (Jasa/Oli)
    // Kita cek dulu apakah barang dengan nama ini sudah ada di kategori pemeliharaan?
    $item = \App\Models\Item::firstOrCreate(
        [
            'nama_barang' => $request->nama_item,
            'category_id' => $kategori->id
        ],
        [
            'satuan'         => 'Paket/Pcs',
            'harga_satuan'   => $request->harga,
            'stok_awal_2026' => 0,
            'stok_saat_ini'  => 0 // Stok 0 karena ini jasa/langsung beli langsung pakai
        ]
    );

    // C. Jika harga berubah, update harga master barangnya
    $item->update(['harga_satuan' => $request->harga]);

    // D. Simpan Transaksi Keluar
    \App\Models\OutgoingTransaction::create([
        'item_id'       => null,
        'deskripsi'     => $request->nama_item,        'division_id'   => $request->division_id,
        'tanggal'       => $request->tanggal,
        'jumlah'        => 1, // Anggap 1 paket maintenance
        'status'        => 'approved', // Langsung approved kalau admin yang input
        'km_saat_ini'   => $request->km_saat_ini,
        'km_berikutnya' => $request->km_berikutnya,
        // Hitung total harga
        'harga_satuan'  => $request->harga,
        'total_harga'   => $request->harga, // 1 * harga
        'sisa_stok'     => 0 // Tidak mempengaruhi stok gudang ATK
    ]);

    return redirect()->route('outgoing.index')->with('success', 'Data Pemeliharaan berhasil dicatat!');
}

    // Cetak PDF Approval
    public function printApproval()
    {
        $data = OutgoingTransaction::with(['item', 'division'])->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")->orderBy('tanggal', 'asc')->orderBy('created_at', 'asc')->get();
        $pdf = Pdf::loadView('transactions.outgoing.pdf_approval', compact('data'));
        return $pdf->stream('Laporan-Pengajuan-Barang.pdf');
    }

    // Cetak PDF Barang Keluar (Approved Only)
    public function exportPdf(Request $request)
{
    // 1. Ambil Filter Tanggal
    $tglAwal  = $request->input('tgl_awal');
    $tglAkhir = $request->input('tgl_akhir');

    // 2. Query Data (Hanya yang Approved)
    $query = OutgoingTransaction::with(['item', 'division'])
                ->where('status', 'approved');

    // 3. Terapkan Filter Jika Ada
    if (!empty($tglAwal) && !empty($tglAkhir)) {
        $query->whereDate('tanggal', '>=', $tglAwal)
              ->whereDate('tanggal', '<=', $tglAkhir);
    }

    $data = $query->orderBy('tanggal', 'asc')->get();

    // 4. Load View PDF
    // Pastikan view-nya sesuai dengan nama file kamu
    $pdf = Pdf::loadView('transactions.outgoing.pdf', compact('data', 'tglAwal', 'tglAkhir'));

    $pdf->setPaper('a4', 'portrait');

    return $pdf->stream('Laporan-Barang-Keluar.pdf');
}

    // Cetak Excel
    public function exportExcel()
    {
        return Excel::download(new OutgoingExport, 'Laporan-Barang-Keluar.xlsx');
    }
}