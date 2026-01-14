<!DOCTYPE html>
<html>
<head>
    <title>Laporan Seluruh Transaksi</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid black; padding: 5px; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-green { color: green; font-weight: bold; }
        .text-red { color: red; font-weight: bold; }
        .bg-grey { background-color: #f9f9f9; }
        .total-row { background-color: #e0e0e0; font-weight: bold; }

        .summary-box {
            margin-top: 20px;
            font-size: 11px;
            font-weight: bold;
            border: 1px solid #000;
            padding: 10px;
            width: 50%;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0;">Laporan Riwayat Transaksi Lengkap</h2>
        <p>Dicetak Tanggal: {{ date('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Tanggal</th>
                <th width="20%">Nama Barang</th>
                <th width="15%">Keterangan</th>
                <th width="8%">Masuk</th>
                <th width="8%">Keluar</th>
                <th width="10%">Sisa Stok<br>(History)</th>
                <th width="12%">Harga Satuan</th>
                <th width="12%">Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gabungan as $key => $row)
            <tr class="{{ $row->jenis_transaksi == 'masuk' ? '' : 'bg-grey' }}">
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $row->item->nama_barang ?? '[Terhapus]' }}</td>
                <td>
                    @if($row->jenis_transaksi == 'masuk') Restock @else Ke: {{ $row->division->nama_bidang ?? '-' }} @endif
                </td>
                <td class="text-center">
                    @if($row->jenis_transaksi == 'masuk') <span class="text-green">{{ $row->jumlah }}</span> @else - @endif
                </td>
                <td class="text-center">
                    @if($row->jenis_transaksi == 'keluar') <span class="text-red">{{ $row->jumlah }}</span> @else - @endif
                </td>
                <td class="text-center">{{ $row->sisa_stok ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($row->harga_satuan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($row->total_harga, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-center">TOTAL MUTASI</td>
                <td class="text-center">{{ $totalQtyMasuk }}</td>
                <td class="text-center">{{ $totalQtyKeluar }}</td>
                <td colspan="2"></td>
                {{-- Kita tampilkan Total Nilai Transaksi Disini (Opsional, tapi biar gak kosong) --}}
                <td class="text-right">Rp {{ number_format($grandTotalNilai, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- KOTAK PERHITUNGAN ASET --}}
    <div class="summary-box">
        <div style="margin-bottom: 5px; text-decoration: underline;">Perhitungan Nilai Aset:</div>

        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; padding: 2px;">Total Aset Awal (Data Awal)</td>
                <td style="border: none; padding: 2px;">:</td>
                <td style="border: none; padding: 2px;" class="text-right">Rp {{ number_format($totalAsetAwal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px;">(+) Total Barang Masuk</td>
                <td style="border: none; padding: 2px;">:</td>
                <td style="border: none; padding: 2px;" class="text-right">Rp {{ number_format($totalRupiahMasuk, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px;">(-) Total Barang Keluar</td>
                <td style="border: none; padding: 2px;">:</td>
                <td style="border: none; padding: 2px;" class="text-right">Rp {{ number_format($totalRupiahKeluar, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3" style="border: none; border-bottom: 1px dashed black; padding: 5px 0;"></td>
            </tr>
            <tr>
                <td style="border: none; padding: 5px 2px; font-size: 13px;">Banyak Aset Akhir</td>
                <td style="border: none; padding: 5px 2px; font-size: 13px;">=</td>
                <td style="border: none; padding: 5px 2px; font-size: 13px;" class="text-right text-green">
                    Rp {{ number_format($totalAsetAkhir, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
