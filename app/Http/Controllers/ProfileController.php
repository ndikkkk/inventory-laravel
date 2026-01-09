<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Penting untuk password
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    // FUNGSI BARU: UPDATE PROFIL
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'  => 'required|string|max:255',
            // Email harus unik, TAPI abaikan validasi unik untuk email milik user ini sendiri
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'gender'=> 'required|in:L,P',
            // Password bersifat opsional (nullable). Jika diisi, minimal 6 karakter & harus dikonfirmasi
            'password' => 'nullable|min:6|confirmed',
        ]);

        // Update Data Dasar
        $user->name = $request->name;
        $user->email = $request->email;
        $user->gender = $request->gender;

        // Update Password HANYA JIKA kolom password diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}