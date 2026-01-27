<?php

namespace App\Http\Controllers;

use App\Models\Item;
// use App\Models\Category; // HAPUS INI (Sudah tidak dipakai)
use App\Models\Account;     // PENTING: Pakai Model Account
use Illuminate\Http\Request;
use App\Imports\ItemImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ItemsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\IncomingTransaction;
use App\Models\OutgoingTransaction;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    // 1. TAMPILKAN SEMUA BARANG
    public function index(Request $request)
    {
        // 1. Ambil Parameter
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'created_at'); // Default: Tanggal Buat
        $direction = $request->input('direction', 'desc');  // Default: Terbaru
        $categoryId = $request->input('category_id');       // Filter Kategori

        // 2. Query Dasar (Pakai Join supaya bisa sort nama kategori)
        $query = Item::select('items.*')
                    ->leftJoin('accounts', 'items.account_id', '=', 'accounts.id');

        // 3. Logic Filter Kategori (Level 1)
        if ($categoryId) {
            $query->whereHas('account.parent.parent', function($q) use ($categoryId) {
                $q->where('id', $categoryId);
            });
        }

        // 4. Logic Sorting
        switch ($sortBy) {
            case 'nama_barang':
                $query->orderBy('items.nama_barang', $direction);
                break;
            case 'kategori':
                $query->orderBy('accounts.nama_akun', $direction);
                break;
            case 'stok':
                $query->orderBy('items.stok_saat_ini', $direction);
                break;
            case 'harga':
                $query->orderBy('items.harga_satuan', $direction);
                break;
            default:
                $query->orderBy('items.updated_at', 'desc'); // Default fallback
                break;
        }

        // 5. Eksekusi
        $items = $query->with('account.parent.parent') // Load relasi untuk tampilan
                       ->paginate($perPage)
                       ->appends($request->all()); // Biar filter gak ilang pas ganti halaman

        // 6. Data Dropdown Filter
        $categories = Account::where('level', 1)->get();

        return view('items.index', compact('items', 'categories'));
    }

    // 2. FORM TAMBAH BARANG (HIERARKI)
    public function create()
    {
        // Ambil hanya Akun Level 1 (Jenis) untuk dropdown pertama
        $level1 = Account::where('level', 1)->get();

        return view('items.create', compact('level1'));
    }

    // AJAX: AMBIL ANAK AKUN (Level 2 & 3)
    public function getAccounts($parentId)
    {
        $accounts = Account::where('parent_id', $parentId)->get();
        return response()->json($accounts);
    }

    // 3. SIMPAN BARANG BARU
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang'  => 'required',
            'account_id'   => 'required|exists:accounts,id', // Validasi ke tabel accounts
            'satuan'       => 'required',
            'harga_satuan' => 'required|numeric|min:0',
            'stok_awal'    => 'required|integer|min:0',
            'min_stok'     => 'required|integer|min:0',
            'sumber_data'  => 'required|in:awal,baru',
        ]);

        // Logika Stok Awal vs Belanja Baru
        $stokAwalMaster = ($request->sumber_data == 'awal') ? $request->stok_awal : 0;

        // 1. Simpan ke Master Barang
        $item = Item::create([
            'nama_barang'    => Str::title($request->nama_barang),
            'account_id'     => $request->account_id, // Simpan ID Akun (Level 3)
            'satuan'         => $request->satuan,
            'harga_satuan'   => $request->harga_satuan,
            'stok_awal_2026' => $stokAwalMaster,
            'stok_saat_ini'  => $request->stok_awal,
            'min_stok'       => $request->min_stok,
        ]);

        // 2. Catat Transaksi Masuk (Jika Belanja Baru)
        if ($request->sumber_data == 'baru' && $request->stok_awal > 0) {
            IncomingTransaction::create([
                'item_id'      => $item->id,
                'tanggal'      => Carbon::now(),
                'jumlah'       => $request->stok_awal,
                'harga_satuan' => $request->harga_satuan,
                'total_harga'  => $request->stok_awal * $request->harga_satuan,
                'sisa_stok'    => $request->stok_awal
            ]);
        }

        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan!');
    }

    // 4. FORM EDIT
    public function edit(Item $item)
    {
        // REVISI: Untuk Edit, kita tampilkan daftar Akun Level 3 saja agar simpel
        // (User tidak perlu klik hierarki dari awal jika cuma mau ganti nama barang)

        // Ambil akun Level 3 (Rincian Objek) beserta induknya agar jelas
        $accounts = Account::where('level', 3)->with('parent')->get();

        return view('items.edit', compact('item', 'accounts'));
    }

    // 5. UPDATE DATA
    public function update(Request $request, Item $item)
    {
        $request->validate([
            'nama_barang' => 'required',
            'account_id'  => 'required|exists:accounts,id', // REVISI: Ganti category_id
            'satuan'      => 'required',
            'harga_satuan'=> 'required|numeric',
            'min_stok'    => 'required|integer|min:0',
        ]);

        $item->update([
            'nama_barang'  => Str::title($request->nama_barang),
            'account_id'   => $request->account_id, // REVISI: Update ke account_id
            'satuan'       => $request->satuan,
            'harga_satuan' => $request->harga_satuan,
            'min_stok'     => $request->min_stok,
        ]);

        return redirect()->route('items.index')->with('success', 'Data barang diperbarui!');
    }

    // 6. HAPUS BARANG
    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Barang dihapus!');
    }

    // IMPORT CSV/EXCEL
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        try {
            Excel::import(new ItemImport, $request->file('file'));
            return redirect()->route('items.index')->with('success', 'Data barang BERHASIL diimport!');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $pesanError = 'Gagal Import! Ada data yang salah: ';
            foreach ($failures as $failure) {
                $baris = $failure->row();
                $error = $failure->errors()[0];
                $pesanError .= " (Baris $baris: $error)";
            }
            return back()->with('error', $pesanError);

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // CETAK EXCEL MASTER
    public function exportExcel()
    {
        return Excel::download(new ItemsExport, 'Data-Stok-Barang.xlsx');
    }

    // CETAK PDF STOK MASTER
    public function printStock()
    {
        // REVISI: Ganti with('category') jadi with('account')
        // Urutkan berdasarkan account_id agar di PDF rapi per kategori
        $items = Item::with('account')
                     ->orderBy('account_id')
                     ->get();

        $pdf = PDF::loadView('items.print_stock', compact('items'));
        return $pdf->stream('Laporan-Stok-Barang.pdf');
    }

    // RESET SYSTEM
    public function deleteAll()
    {
        Schema::disableForeignKeyConstraints();
        IncomingTransaction::truncate();
        OutgoingTransaction::truncate();
        Item::truncate();
        Schema::enableForeignKeyConstraints();

        return redirect()->route('items.index')
            ->with('success', 'SYSTEM RESET: Semua Barang & Laporan BERHASIL DIHAPUS TOTAL (0)!');
    }
}
