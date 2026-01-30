<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun</title>
    <link rel="shortcut icon" href="{{ asset('images/logo_boyolali.png') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f2f7ff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Nunito', sans-serif;
        }
        .login-card {
            background: #ffffff;
            border-radius: 15px;
            padding: 2rem;
            width: 100%;
            max-width: 550px; /* Diperlebar sedikit */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            max-height: 90vh; /* Agar tidak kepotong jika layar kecil */
            overflow-y: auto;
        }
        .btn-primary {
            background-color: #435ebe; border: none; padding: 10px; width: 100%;
        }
        .form-control, .form-select { background-color: #f8f9fa; border: 1px solid #dfe3e7; }

        .footer-text {
        margin-top: 2rem;
        font-size: 0.85rem;
        color: #888;
        text-align: center;
    }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <h4 class="fw-bold">Pendaftaran Akun Baru</h4>
            <p class="text-muted small">Isi data diri Anda dengan lengkap</p>
        </div>

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label small fw-bold">NIP</label>
                <input type="text" name="nip" class="form-control" placeholder="199xxxxxxxxxxxxxxx" value="{{ old('nip') }}" required>
                @error('nip') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" placeholder="Nama Pegawai" value="{{ old('name') }}" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="email@boyolali.go.id" value="{{ old('email') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold">Jenis Kelamin</label>
                    <select name="gender" class="form-select" required>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>

            {{-- BAGIAN BARU: ROLE & DIVISI --}}
            <div class="mb-3 bg-light p-3 rounded border">
                <label class="form-label small fw-bold text-primary">Asal Bidang / Unit Kerja</label>
                <select name="division_id" class="form-select border-primary" required>
                    <option value="">-- Pilih Bidang --</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}">{{ $div->nama_bidang }}</option>
                    @endforeach
                </select>
                {{-- <div class="form-text text-muted" style="font-size: 0.75rem;">
                    *Jika memilih <b>Sekretariat</b>, akun otomatis menjadi <b>Admin</b>.
                </div> --}}
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold">Ulangi Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-2">DAFTAR SEKARANG</button>
        </form>

        <div class="text-center mt-3">
            <span class="small text-muted">Sudah punya akun?</span>
            <a href="{{ route('login') }}" class="small fw-bold text-decoration-none">Login disini</a>
        </div>

        <div class="footer-text">
            &copy; 2026 BKPSDM Kabupaten Boyolali<br>
            <span class="small">Dikembangkan oleh Mahasiswa Magang UNS</span>
        </div>
    </div>
</body>
</html>
