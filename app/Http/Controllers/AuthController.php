<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Division; // Pastikan Model Division di-import

class AuthController extends Controller
{
    // 1. Menampilkan Form Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 2. Memproses Login
    public function login(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nip' => ['required', 'numeric'],
            'password' => ['required'],
        ]);

        // 2. Cari User berdasarkan NIP
        $user = \App\Models\User::where('nip', $request->nip)->first();

        // 3. LOGIKA PENGECEKAN SPESIFIK

        // KASUS A: Jika User TIDAK DITEMUKAN (NIP belum terdaftar)
        if (!$user) {
            return back()
                ->withErrors(['nip' => 'NIP belum terdaftar di sistem.'])
                ->withInput(); // Biar inputan NIP gak ilang
        }

        // KASUS B: Jika User ADA, tapi Password SALAH
        if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['password' => 'Password yang Anda masukkan salah.'])
                ->withInput();
        }

        // KASUS C: Jika Semua Benar (Login Sukses)
        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();
        
        return redirect()->intended('dashboard');
    }

    // 3. Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // MENAMPILKAN FORM REGISTER
    public function showRegisterForm()
    {
        // [BARU] Ambil data divisi untuk dropdown
        $divisions = Division::all();
        return view('auth.register', compact('divisions'));
    }

    // PROSES REGISTER
    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'nip'         => 'required|numeric|unique:users,nip',
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|min:6|confirmed',
            'gender'      => 'required|in:L,P',
            'division_id' => 'required|exists:divisions,id', // Wajib pilih divisi
        ]);

        // [LOGIKA BARU] Cek Divisi yang dipilih
        $division = Division::find($request->division_id);

        // Default role
        $role = 'bidang';

        // Jika namanya mengandung kata 'Sekretariat', jadikan Admin
        // (Pakai stripos biar tidak sensitif huruf besar/kecil)
        if (stripos($division->nama_bidang, 'Sekretariat') !== false) {
            $role = 'admin';
        }

        // Buat User Baru
        User::create([
            'nip'         => $request->nip,
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => \Illuminate\Support\Facades\Hash::make($request->password),
            'gender'      => $request->gender,
            'role'        => $role,                // <-- Role otomatis
            'division_id' => $request->division_id // <-- Divisi tetap disimpan
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Role Anda: ' . ucfirst($role));
    }
}