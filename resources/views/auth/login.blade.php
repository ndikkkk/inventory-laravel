<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory ATK BKPSDM</title>

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            /* Ganti background gambar jadi warna solid soft (Mazer Theme Color) */
            background-color: #f2f7ff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Nunito', sans-serif;
        }

        .login-card {
            background: #ffffff;
            border: none;
            border-radius: 15px;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            /* Shadow yang halus agar card terlihat melayang (Elevation) */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .brand-logo {
            color: #435ebe;
            /* Warna Biru Utama Mazer */
            font-weight: 800;
            font-size: 1.5rem;
        }

        .form-control {
            background-color: #f8f9fa;
            border: 1px solid #dfe3e7;
            padding: 12px 15px;
            border-radius: 8px;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: #435ebe;
            box-shadow: 0 0 0 4px rgba(67, 94, 190, 0.1);
        }

        .btn-primary {
            background-color: #435ebe;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 700;
            width: 100%;
            margin-top: 1.5rem;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: #2d4596;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 94, 190, 0.3);
        }

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
        <div class="text-center mb-5">
            <div class="brand-logo mb-2">SI-ATK</div>
            <h5 class="fw-bold text-dark">Selamat Datang</h5>
            <p class="text-secondary small">Silakan login untuk mengelola inventory BKPSDM Boyolali</p>
        </div>

        {{-- PERBAIKAN DI SINI: MENAMPILKAN PESAN ERROR --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show small shadow-sm" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i><strong>Gagal Masuk!</strong>
                <ul class="mb-0 ps-3 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- MENAMPILKAN PESAN SUKSES (Posisi dipindah ke atas biar kelihatan) --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show small shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">NIP (Nomor Induk Pegawai)</label>
                <div class="form-control-icon-wrapper">
                    <input type="text" class="form-control" name="nip" placeholder="Contoh: 199001012025011001"
                        required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">PASSWORD</label>
                <input type="password" class="form-control" name="password" placeholder="********" required>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember">
                <label class="form-check-label small text-secondary" for="remember">
                    Ingat Saya
                </label>
            </div>

            <button type="submit" class="btn btn-primary">MASUK DASHBOARD</button>

            <div class="text-center mt-3">
                <span class="small text-muted">Belum punya akun?</span>
                <a href="{{ route('register') }}" class="small text-decoration-none"> Daftar disini</a>
            </div>
        </form>

        <div class="footer-text">
            &copy; 2026 BKPSDM Kabupaten Boyolali<br>
            <span class="small">Dikembangkan oleh Mahasiswa Magang UNS</span>
        </div>
    </div>
    {{-- Script Bootstrap untuk fungsi Close pada Alert --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
