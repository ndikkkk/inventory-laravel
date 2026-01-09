<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Inventory ATK</title>

    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/iconly.css') }}">

    <style>
        /* LOGIKA WARNA TULISAN */
        .text-bkpsdm {
            color: #25396f !important;
        }

        [data-bs-theme="dark"] .text-bkpsdm {
            color: #ffffff !important;
        }
    </style>
</head>

<body>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="logo">
                            <a href="{{ route('dashboard') }}"
                                class="d-flex align-items-center gap-2 text-decoration-none">
                                <img src="{{ asset('images/logo_boyolali.png') }}" alt="Logo"
                                    style="height: 45px; width: auto;">
                                <div class="d-flex flex-column justify-content-center text-start">
                                    <h5 class="m-0 fw-bolder text-bkpsdm"
                                        style="font-size: 1.4rem; letter-spacing: 1px;">
                                        BKPSDM
                                    </h5>
                                    <span class="text-muted"
                                        style="font-size: 0.55rem; line-height: 1.1; max-width: 140px; text-transform: uppercase;">
                                        Badan Kepegawaian dan Pengembangan Sumber Daya Manusia
                                    </span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        <li class="sidebar-item {{ Route::is('dashboard') ? 'active' : '' }}">
                            <a href="{{ route('dashboard') }}" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        {{-- KHUSUS ADMIN: DATA BARANG --}}
                        @if (Auth::user()->role == 'admin')
                            <li class="sidebar-item {{ Request::is('items*') ? 'active' : '' }}">
                                <a href="{{ route('items.index') }}" class='sidebar-link'>
                                    <i class="bi bi-box-seam"></i>
                                    <span>Data Barang</span>
                                </a>
                            </li>
                        @endif

                        <li class="sidebar-title">Transaksi</li>

                        {{-- KHUSUS ADMIN: BARANG MASUK & APPROVAL --}}
                        @if (Auth::user()->role == 'admin')
                            <li class="sidebar-item {{ Route::is('incoming.create') ? 'active' : '' }}">
                                <a href="{{ route('incoming.create') }}" class='sidebar-link'>
                                    <i class="bi bi-arrow-down-circle-fill text-success"></i>
                                    <span>Barang Masuk</span>
                                </a>
                            </li>

                            {{-- Menu Approval Page --}}
                            <li class="sidebar-item {{ Route::is('outgoing.approval') ? 'active' : '' }}">
                                <a href="{{ route('outgoing.approval') }}" class='sidebar-link'>
                                    <i class="bi bi-check-square-fill text-primary"></i>
                                    <span>Persetujuan Barang</span>
                                </a>
                            </li>

                            {{-- Menu History Barang Keluar (Bersih) --}}
                            <li class="sidebar-item {{ Route::is('outgoing.index') ? 'active' : '' }}">
                                <a href="{{ route('outgoing.index') }}" class='sidebar-link'>
                                    <i class="bi bi-arrow-up-circle-fill text-danger"></i>
                                    <span>Barang Keluar</span>
                                </a>
                            </li>
                        @endif

                        {{-- KHUSUS BIDANG: AJUKAN & RIWAYAT --}}
                        @if (Auth::user()->role == 'bidang')
                            <li class="sidebar-item {{ Route::is('outgoing.create') ? 'active' : '' }}">
                                <a href="{{ route('outgoing.create') }}" class='sidebar-link'>
                                    <i class="bi bi-arrow-up-circle-fill text-danger"></i>
                                    <span>Ajukan Barang</span>
                                </a>
                            </li>

                            <li class="sidebar-item {{ Route::is('outgoing.index') ? 'active' : '' }}">
                                <a href="{{ route('outgoing.index') }}" class='sidebar-link'>
                                    <i class="bi bi-clock-history"></i>
                                    <span>Riwayat Pengajuan</span>
                                </a>
                            </li>
                        @endif

                        {{-- KHUSUS ADMIN: LAPORAN --}}
                        {{-- (Menu ini disembunyikan dari Bidang sesuai permintaan) --}}
                        @if (Auth::user()->role == 'admin')
                            <li class="sidebar-item {{ Route::is('reports.index') ? 'active' : '' }}">
                                <a href="{{ route('reports.index') }}" class='sidebar-link'>
                                    <i class="bi bi-file-earmark-text"></i>
                                    <span>Laporan</span>
                                </a>
                            </li>
                        @endif

                        <li class="sidebar-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="sidebar-link btn btn-link text-start text-danger border-0 w-100">
                                    <i class="bi bi-box-arrow-left text-danger"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="main" class='layout-navbar'>
            <header class='mb-3'>
                <nav class="navbar navbar-expand navbar-light navbar-top">
                    <div class="container-fluid">
                        <a href="#" class="burger-btn d-block d-xl-none">
                            <i class="bi bi-justify fs-3"></i>
                        </a>

                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ms-auto mb-2 mb-lg-0"></ul>

                            <div class="theme-toggle d-flex gap-2 align-items-center mt-2 me-4">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    aria-hidden="true" role="img" class="iconify iconify--system-uicons"
                                    width="20" height="20" preserveAspectRatio="xMidYMid meet"
                                    viewBox="0 0 21 21">
                                    <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path
                                            d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2"
                                            opacity=".3"></path>
                                        <g transform="translate(-210 -1)">
                                            <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                                            <circle cx="220.5" cy="11.5" r="4"></circle>
                                            <path
                                                d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2">
                                            </path>
                                        </g>
                                    </g>
                                </svg>
                                <div class="form-check form-switch fs-6">
                                    <input class="form-check-input me-0" type="checkbox" id="toggle-dark"
                                        style="cursor: pointer">
                                    <label class="form-check-label"></label>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    aria-hidden="true" role="img" class="iconify iconify--mdi" width="20"
                                    height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z">
                                    </path>
                                </svg>
                            </div>

                            @php
                                $hour = date('H');
                                if ($hour >= 3 && $hour < 11) {
                                    $greeting = 'Selamat Pagi';
                                } elseif ($hour >= 11 && $hour < 15) {
                                    $greeting = 'Selamat Siang';
                                } elseif ($hour >= 15 && $hour < 18) {
                                    $greeting = 'Selamat Sore';
                                } else {
                                    $greeting = 'Selamat Malam';
                                }
                            @endphp

                            <div class="dropdown">
                                <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="user-menu d-flex">
                                        <div class="user-name text-end me-3">
                                            <h6 class="mb-0 text-gray-600">
                                                {{ $greeting }}, {{ Auth::user()->sapaan }}
                                                {{ Auth::user()->name }}
                                            </h6>
                                            <p class="mb-0 text-sm text-gray-600">
                                                @if (Auth::user()->role == 'admin')
                                                    Administrator
                                                @else
                                                    Bidang {{ Auth::user()->division->nama_bidang ?? 'Staff' }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="user-img d-flex align-items-center">
                                            <div class="avatar avatar-md">
                                                <img
                                                    src="{{ asset('assets/compiled/jpg/' . (Auth::user()->gender == 'L' ? '2.jpg' : '3.jpg')) }}">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton"
                                    style="min-width: 11rem;">
                                    <li>
                                        <h6 class="dropdown-header">Halo, {{ Auth::user()->name }}!</h6>
                                    </li>
                                    <li><a class="dropdown-item" href="{{ route('profile') }}"><i
                                                class="icon-mid bi bi-person me-2"></i> My Profile</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="icon-mid bi bi-box-arrow-left me-2"></i> Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </header>
            <div id="main-content">
                <div class="page-heading">
                    <h3>@yield('page-heading')</h3>
                </div>

                <div class="page-content">
                    @yield('content')
                </div>

                <footer>
                    <div class="footer clearfix mb-0 text-muted">
                        <div class="float-start">
                            <p>2026 &copy; BKPSDM Boyolali</p>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/static/js/components/dark.js') }}"></script>
    <script src="{{ asset('assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/compiled/js/app.js') }}"></script>

    @stack('scripts')
</body>

</html>
