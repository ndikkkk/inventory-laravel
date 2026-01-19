@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-heading', 'Profil Pengguna')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">

        {{-- Pesan Sukses / Error --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body text-center">
                <div class="avatar avatar-xl mb-3">
                    <img src="{{ asset('assets/compiled/jpg/' . (Auth::user()->gender == 'L' ? '2.jpg' : '3.jpg')) }}"
                    alt="Avatar" style="width: 100px; height: 100px;">
                </div>

                <h3 class="mb-0">{{ $user->name }}</h3>
                <p class="text-muted">{{ $user->nip }}</p>

                {{-- Tampilkan Role --}}
                <span class="badge {{ $user->role == 'admin' ? 'bg-primary' : 'bg-success' }} mb-3">
                    {{ $user->role == 'admin' ? 'Admin' : 'Bidang ' . ($user->division->nama_bidang ?? '-') }}
                </span>

                <hr>

                <div class="text-start mt-4">
                    <div class="form-group mb-3">
                        <label class="fw-bold text-muted small">Nama Lengkap</label>
                        <p class="form-control-static fs-5">{{ $user->name }}</p>
                    </div>
                    <div class="form-group mb-3">
                        <label class="fw-bold text-muted small">NIP</label>
                        <p class="form-control-static fs-5">{{ $user->nip }}</p>
                    </div>
                    <div class="form-group mb-3">
                        <label class="fw-bold text-muted small">Email</label>
                        <p class="form-control-static fs-5">{{ $user->email }}</p>
                    </div>
                    <div class="form-group mb-3">
                        <label class="fw-bold text-muted small">Jenis Kelamin</label>
                        <p class="form-control-static fs-5">
                            {{ $user->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2 justify-content-center">
                    <a href="{{ route('dashboard') }}" class="btn btn-light-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    {{-- TOMBOL PEMICU MODAL EDIT --}}
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="bi bi-pencil-square"></i> Edit Profil
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT PROFIL --}}
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileLabel">Edit Profil Saya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    {{-- NIP (Read Only - Tidak boleh diganti sembarangan) --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">NIP</label>
                        <input type="text" class="form-control bg-light" value="{{ $user->nip }}" readonly disabled>
                        <small class="text-muted">NIP tidak dapat diubah.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis Kelamin</label>
                        <select name="gender" class="form-select" required>
                            <option value="L" {{ $user->gender == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ $user->gender == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <hr class="my-4">
                    <h6 class="text-danger mb-3"><i class="bi bi-lock"></i> Ganti Password (Opsional)</h6>
                    <div class="alert alert-light-warning small">
                        Kosongkan jika tidak ingin mengganti password.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
