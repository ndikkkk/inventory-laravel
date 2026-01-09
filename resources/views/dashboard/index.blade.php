@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-heading', 'Statistik Inventory')

@section('content')

{{-- 1. GRAFIK STOK (Dilihat Semua Role) --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Sisa Stok Barang Saat Ini</h4>
            </div>
            <div class="card-body">
                {{-- [MODIFIKASI HTML] Bungkus dengan div scrollable --}}
                <div style="overflow-x: auto; width: 100%; padding-bottom: 15px;">
                    <div id="chart-stok-barang" style="min-width: 100px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. GRAFIK PEMASUKAN & PENGELUARAN (HANYA ADMIN) --}}
@if(Auth::user()->role == 'admin')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Pemasukan Barang (Tahun Ini)</h4>
            </div>
            <div class="card-body">
                <div id="chart-pemasukan"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Pengeluaran Barang (Tahun Ini)</h4>
            </div>
            <div class="card-body">
                <div id="chart-pengeluaran"></div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- 3. PIE CHART & TOP 5 (Dilihat Semua Role) --}}
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Persentase Penggunaan per Bidang</h4>
            </div>
            <div class="card-body">
                <div id="chart-divisi"></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>5 Barang Paling Sering Keluar</h4>
            </div>
            <div class="card-content pb-4">
                @foreach($top5Items as $top)
                <div class="recent-message d-flex px-4 py-3">
                    <div class="avatar avatar-lg">
                        <div class="d-flex align-items-center justify-content-center bg-light-primary text-primary" style="width: 40px; height: 40px; border-radius: 50%;">
                            <i class="bi bi-box-seam fs-4"></i>
                        </div>
                    </div>
                    <div class="name ms-4">
                        <h5 class="mb-1">{{ $top->item->nama_barang }}</h5>
                        <h6 class="text-muted mb-0">Total Keluar: <b class="text-danger">{{ $top->total }}</b> {{ $top->item->satuan }}</h6>
                    </div>
                </div>
                @endforeach

                @if($top5Items->isEmpty())
                    <div class="px-4 py-3 text-muted">Belum ada transaksi keluar.</div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/extensions/apexcharts/apexcharts.min.js') }}"></script>

<script>
    // === 1. GRAFIK STOK BARANG (BAR CHART - MODIFIKASI SCROLL) ===
    
    // Ambil data dari Controller
    var dataStok = @json($dataStok);
    var labelBarang = @json($labelBarang);

    // [LOGIKA BARU] Hitung Lebar Grafik
    // Kita beri jatah 60px untuk setiap batang barang.
    // Jika barang ada 10, lebar = 600px. Jika ada 50, lebar = 3000px (scroll muncul).
    var minWidthPerBar = 80;
    var calculatedWidth = labelBarang.length * minWidthPerBar;
    
    // Cek lebar layar (container), pilih mana yang lebih besar
    // Agar kalau datanya sedikit, grafik tetap full width (100%)
    var containerWidth = document.querySelector("#chart-stok-barang").parentElement.offsetWidth;
    var chartWidth = calculatedWidth < containerWidth ? '100%' : calculatedWidth;

    var optionsStok = {
        series: [{
            name: 'Sisa Stok',
            data: dataStok
        }],
        chart: { 
            type: 'bar', 
            height: 400, 
            width: chartWidth, // Terapkan lebar dinamis di sini
            toolbar: { show: false } // Sembunyikan menu zoom agar tidak mengganggu scroll
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '40px', // [REQ] Lebar batang stabil (40px)
                dataLabels: { position: 'top' }
            }
        },
        colors: ['#435ebe'],
        xaxis: { 
            categories: labelBarang,
            labels: {
                rotate: 0, // [REQ] Tulisan Lurus Horizontal
                style: { fontSize: '11px' },
                trim: false, // Tampilkan full text tanpa dipotong

                formatter: function(val){
                    if (typeof val === "string") {
                        if (val.length > 10){
                            return val.split(" ");
                        }
                    }
                    return val;
                }
            }
        },
        tooltip: { theme: 'dark' },
        grid: { show: false }
    };
    new ApexCharts(document.querySelector("#chart-stok-barang"), optionsStok).render();

    // === GRAFIK INI HANYA DIRENDER JIKA USER ADALAH ADMIN ===
    @if(Auth::user()->role == 'admin')
        // === 2. GRAFIK PEMASUKAN BULANAN (AREA CHART) ===
        var optionsMasuk = {
            series: [{
                name: 'Barang Masuk',
                data: Object.values(@json($monthlyIncoming))
            }],
            chart: { type: 'area', height: 300 },
            colors: ['#57caeb'],
            xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] },
            stroke: { curve: 'smooth' },
            tooltip: { theme: 'dark' }
        };
        new ApexCharts(document.querySelector("#chart-pemasukan"), optionsMasuk).render();

        // === 3. GRAFIK PENGELUARAN BULANAN (AREA CHART) ===
        var optionsKeluar = {
            series: [{
                name: 'Barang Keluar',
                data: Object.values(@json($monthlyOutgoing))
            }],
            chart: { type: 'area', height: 300 },
            colors: ['#ff7976'],
            xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] },
            stroke: { curve: 'smooth' },
            tooltip: { theme: 'dark' }
        };
        new ApexCharts(document.querySelector("#chart-pengeluaran"), optionsKeluar).render();
    @endif

    // === 4. PIE CHART DIVISI ===
    var optionsDivisi = {
        series: @json($dataDivisi),
        chart: { type: 'pie', height: 350 },
        labels: @json($labelDivisi),
        colors: ['#435ebe', '#55c6e8', '#f1b44c', '#ff7976', '#9694ff'],
        legend: { position: 'bottom' },
        tooltip: { theme: 'dark' }
    };
    new ApexCharts(document.querySelector("#chart-divisi"), optionsDivisi).render();
</script>
@endpush
