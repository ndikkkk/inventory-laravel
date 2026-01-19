<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi & Pemeliharaan</title>
    <style>
        body { font-family: sans-serif; font-size: 9px; }
        .header { text-align: center; margin-bottom: 20px; }
        .section-title { font-size: 11px; font-weight: bold; margin-top: 15px; margin-bottom: 5px; text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table, th, td { border: 1px solid black; padding: 4px; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-green { color: green; font-weight: bold; }
        .text-red { color: red; font-weight: bold; }
        .bg-grey { background-color: #f9f9f9; }
        .total-row { background-color: #e0e0e0; font-weight: bold; }

        .summary-container { display: table; width: 100%; margin-top: 10px; }
        .summary-box { 
            display: table-cell; width: 48%; 
            border: 1px solid #000; padding: 10px; 
            vertical-align: top;
        }
        .spacer { display: table-cell; width: 4%; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0;">Laporan Mutasi & Pemeliharaan</h2>
        @if(isset($tglAwal) && isset($tglAkhir))
            <p style="margin: 5px 0;">Periode: {{ \Carbon\Carbon::parse($tglAwal)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($tglAkhir)->format('d M Y') }}</p>
        @else
            <p style="margin: 5px 0;">Periode: Semua Waktu</p>
        @endif
        <p style="margin: 0; font-size: 10px;">Dicetak Tanggal: {{ date('d M Y') }}</p>
    </div>

    {{-- ================= TABEL 1: MUTASI BARANG (STOK) ================= --}}
    <div class="section-title">A. Riwayat Mutasi Barang (Stok Fisik)</div>
    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="8%">Tanggal</th>
                <th width="20%">Nama Barang</th>
                <th width="5%">Msk</th>
                <th width="5%">Klr</th>
                <th width="7%">Sisa</th>
                <th width="12%">Nilai Masuk (Rp)</th>
                <th width="12%">Nilai Keluar (Rp)</th>
                <th width="15%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gabunganStok as $row)
            <tr class="{{ $row->jenis_transaksi == 'masuk' ? '' : 'bg-grey' }}">
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $row->item->nama_barang ?? '[Terhapus]' }}</td>
                
                <td class="text-center">@if($row->jenis_transaksi == 'masuk') <span class="text-green">{{ $row->jumlah }}</span> @else - @endif</td>
                <td class="text-center">@if($row->jenis_transaksi == 'keluar') <span class="text-red">{{ $row->jumlah }}</span> @else - @endif</td>
                
                <td class="text-center">{{ $row->sisa_stok ?? '-' }}</td>
                
                <td class="text-right">
                    @if($row->jenis_transaksi == 'masuk') Rp {{ number_format($row->total_harga, 0, ',', '.') }} @else - @endif
                </td>
                <td class="text-right">
                    @if($row->jenis_transaksi == 'keluar') Rp {{ number_format($row->total_harga, 0, ',', '.') }} @else - @endif
                </td>

                <td style="font-size: 8px;">
                    @if($row->jenis_transaksi == 'masuk') Restock @else Divisi: {{ $row->division->nama_bidang ?? '-' }} @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-center">TOTAL MUTASI BARANG</td>
                <td class="text-center">{{ $totalQtyMasuk }}</td>
                <td class="text-center">{{ $totalQtyKeluar }}</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($totalRupiahMasuk, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalRupiahKeluar, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    {{-- ================= TABEL 2: PEMELIHARAAN (JASA) ================= --}}
    <div class="section-title">B. Riwayat Pemeliharaan & Jasa (Expense)</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Tanggal</th>
                <th width="35%">Deskripsi Jasa / Servis</th>
                <th width="20%">Divisi Pengguna</th>
                <th width="15%">Kilometer (KM)</th>
                <th width="15%">Biaya (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($maintenance as $m)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($m->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $m->deskripsi }}</td>
                <td>{{ $m->division->nama_bidang ?? '-' }}</td>
                <td class="text-center">
                    @if($m->km_saat_ini) {{ $m->km_saat_ini }} KM @else - @endif
                </td>
                <td class="text-right">Rp {{ number_format($m->total_harga, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">Tidak ada data pemeliharaan pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-center">TOTAL BIAYA PEMELIHARAAN</td>
                <td class="text-right">Rp {{ number_format($totalBiayaServis, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- ================= RINGKASAN DATA ================= --}}
    <div class="summary-container">
        {{-- KIRI: PERHITUNGAN ASET --}}
        <div class="summary-box">
            <div style="margin-bottom: 5px; text-decoration: underline;">Perhitungan Nilai Gudang:</div>
            <table style="width: 100%; border: none; margin: 0;">
                <tr>
                    <td style="border: none; padding: 1px;">Persediaan Awal</td>
                    <td style="border: none; padding: 1px; width: 10px;">:</td>
                    <td style="border: none; padding: 1px;" class="text-right">Rp {{ number_format($totalAsetAwal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 1px;">Pembelian Barang</td>
                    <td style="border: none; padding: 1px;">:</td>
                    <td style="border: none; padding: 1px;" class="text-right">Rp {{ number_format($totalRupiahMasuk, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 1px;">Pemakaian Barang</td>
                    <td style="border: none; padding: 1px;">:</td>
                    <td style="border: none; padding: 1px;" class="text-right">Rp {{ number_format($totalRupiahKeluar, 0, ',', '.') }}</td>
                </tr>
                <tr><td colspan="3" style="border: none; border-bottom: 1px dashed black; padding: 2px;"></td></tr>
                <tr>
                    <td style="border: none; padding: 3px; font-weight: bold;">Nilai Akhir</td>
                    <td style="border: none; padding: 3px;">=</td>
                    <td style="border: none; padding: 3px;" class="text-right text-green">
                        Rp {{ number_format($totalAsetAkhir, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
            <div style="font-size: 8px; margin-top: 5px; color: #555;">
                *Nilai Akhir adalah nilai stok fisik barang yang tersisa di gudang. Biaya jasa tidak mengurangi nilai ini.
            </div>
        </div>

        <div class="spacer"></div>

        {{-- KANAN: RINGKASAN PENGELUARAN --}}
        <div class="summary-box">
            <div style="margin-bottom: 5px; text-decoration: underline;">Ringkasan Pengeluaran (Expense):</div>
            <table style="width: 100%; border: none; margin: 0;">
                <tr>
                    <td style="border: none; padding: 3px;">Biaya Pemakaian Barang</td>
                    <td style="border: none; padding: 3px;">:</td>
                    <td style="border: none; padding: 3px;" class="text-right">Rp {{ number_format($totalRupiahKeluar, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 3px;">Biaya Jasa / Servis</td>
                    <td style="border: none; padding: 3px;">:</td>
                    <td style="border: none; padding: 3px;" class="text-right">Rp {{ number_format($totalBiayaServis, 0, ',', '.') }}</td>
                </tr>
                <tr><td colspan="3" style="border: none; border-bottom: 1px dashed black; padding: 2px;"></td></tr>
                <tr>
                    <td style="border: none; padding: 3px; font-weight: bold;">Total Anggaran Terpakai</td>
                    <td style="border: none; padding: 3px;">=</td>
                    <td style="border: none; padding: 3px;" class="text-right text-red">
                        Rp {{ number_format($totalRupiahKeluar + $totalBiayaServis, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>