@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-heading', 'Statistik Inventory')

@section('content')

{{-- BARIS 1: GRAFIK STOK (DI-COMMENT AGAR TIDAK BERAT) --}}
{{--
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Sisa Stok Barang Saat Ini</h4>
            </div>
            <div class="card-body">
                <div style="overflow-x: auto; width: 100%; padding-bottom: 15px;">
                    <div id="chart-stok-barang" style="min-width: 100px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
--}}

{{-- BARIS 2: GRAFIK PEMASUKAN & PENGELUARAN TERPISAH (Khusus Admin) --}}
@if(Auth::user()->role == 'admin')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Grafik Pemasukan (Tahun Ini)</h4>
            </div>
            <div class="card-body">
                <div id="chart-pemasukan"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Grafik Pengeluaran (Tahun Ini)</h4>
            </div>
            <div class="card-body">
                <div id="chart-pengeluaran"></div>
            </div>
        </div>
    </div>
</div>

{{-- BARIS 3: GRAFIK PERBANDINGAN GABUNGAN (Line Chart) --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Perbandingan Masuk dan Keluar</h4>
            </div>
            <div class="card-body">
                <div id="chart-gabungan"></div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- BARIS 4: PIE CHART + TOP 5 KELUAR + TOP 5 MASUK --}}
<div class="row">
    {{-- KOLOM 1: PIE CHART --}}
    <div class="col-12 col-xl-4">
        <div class="card h-100">
            <div class="card-header">
                <h4>Penggunaan per Bidang</h4>
            </div>
            <div class="card-body">
                <div id="chart-divisi"></div>
            </div>
        </div>
    </div>

    {{-- KOLOM 2: TOP 5 BARANG KELUAR --}}
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card h-100">
            <div class="card-header bg-light-danger">
                <h4 class="text-danger fs-6"><i class="bi bi-arrow-up-circle"></i> Sering Keluar</h4>
            </div>
            <div class="card-content pb-4">
                @foreach($top5Items as $top)
                <div class="recent-message d-flex px-4 py-3 border-bottom">
                    <div class="name">
                        <h6 class="mb-1 text-dark">{{ $top->item->nama_barang ?? '[Terhapus]' }}</h6>
                        <small class="text-muted">Total: <b class="text-danger">{{ $top->total }}</b> {{ $top->item->satuan ?? '' }}</small>
                    </div>
                </div>
                @endforeach
                @if($top5Items->isEmpty())
                    <div class="p-4 text-center text-muted">Belum ada data.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- KOLOM 3: TOP 5 BARANG MASUK --}}
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card h-100">
            <div class="card-header bg-light-success">
                <h4 class="text-success fs-6"><i class="bi bi-arrow-down-circle"></i> Sering Restock</h4>
            </div>
            <div class="card-content pb-4">
                @foreach($top5Incoming as $in)
                <div class="recent-message d-flex px-4 py-3 border-bottom">
                    <div class="name">
                        <h6 class="mb-1 text-dark">{{ $in->item->nama_barang ?? '[Terhapus]' }}</h6>
                        <small class="text-muted">Total: <b class="text-success">{{ $in->total }}</b> {{ $in->item->satuan ?? '' }}</small>
                    </div>
                </div>
                @endforeach
                @if($top5Incoming->isEmpty())
                    <div class="p-4 text-center text-muted">Belum ada data.</div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/extensions/apexcharts/apexcharts.min.js') }}"></script>

<script>
    // =========================================================
    // 1. GRAFIK STOK (DI-COMMENT AGAR TIDAK DIRENDER)
    // =========================================================
    /*
    var dataStok = @json($dataStok);
    var labelBarang = @json($labelBarang);
    var minWidthPerBar = 40;
    var calculatedWidth = labelBarang.length * minWidthPerBar;
    var containerWidth = document.querySelector("#chart-stok-barang").parentElement.offsetWidth;
    var chartWidth = calculatedWidth < containerWidth ? '100%' : calculatedWidth;

    var optionsStok = {
        series: [{ name: 'Sisa Stok', data: dataStok }],
        chart: { type: 'bar', height: 350, width: chartWidth, toolbar: { show: false } },
        plotOptions: { bar: { borderRadius: 4, columnWidth: '40px', dataLabels: { position: 'top' } } },
        colors: ['#435ebe'],
        xaxis: { categories: labelBarang, labels: { style: { fontSize: '11px' } } },
        grid: { show: false }
    };
    new ApexCharts(document.querySelector("#chart-stok-barang"), optionsStok).render();
    */

    @if(Auth::user()->role == 'admin')
        // 2. GRAFIK PEMASUKAN (Area)
        var optionsMasuk = {
            series: [{ name: 'Masuk', data: Object.values(@json($monthlyIncoming)) }],
            chart: { type: 'area', height: 250, toolbar: { show: false } },
            colors: ['#57caeb'],
            xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] },
            stroke: { curve: 'smooth', width: 2 },
            dataLabels: { enabled: false }
        };
        new ApexCharts(document.querySelector("#chart-pemasukan"), optionsMasuk).render();

        // 3. GRAFIK PENGELUARAN (Area)
        var optionsKeluar = {
            series: [{ name: 'Keluar', data: Object.values(@json($monthlyOutgoing)) }],
            chart: { type: 'area', height: 250, toolbar: { show: false } },
            colors: ['#ff7976'],
            xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] },
            stroke: { curve: 'smooth', width: 2 },
            dataLabels: { enabled: false }
        };
        new ApexCharts(document.querySelector("#chart-pengeluaran"), optionsKeluar).render();

        // 4. GRAFIK GABUNGAN (Line Chart)
        var optionsGabungan = {
            series: [
                { name: 'Barang Masuk', data: Object.values(@json($monthlyIncoming)) },
                { name: 'Barang Keluar', data: Object.values(@json($monthlyOutgoing)) }
            ],
            chart: { type: 'line', height: 300 },
            colors: ['#57caeb', '#ff7976'], // Biru Muda & Merah
            stroke: { width: [3, 3], curve: 'smooth' },
            xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] },
            markers: { size: 5, hover: { size: 7 } },
            grid: { borderColor: '#f1f1f1' }
        };
        new ApexCharts(document.querySelector("#chart-gabungan"), optionsGabungan).render();
    @endif

    // 5. PIE CHART DIVISI
    var optionsDivisi = {
        series: @json($dataDivisi),
        chart: { type: 'pie', height: 320 },
        labels: @json($labelDivisi),
        colors: ['#435ebe', '#55c6e8', '#f1b44c', '#ff7976', '#9694ff'],
        legend: { position: 'bottom' }
    };
    new ApexCharts(document.querySelector("#chart-divisi"), optionsDivisi).render();
</script>
@endpush
