<!DOCTYPE html>
<html>
<head>
    <title>Laporan Barang Masuk</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid black; padding: 4px; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .total-row { background-color: #e0e0e0; font-weight: bold; }
        .mutasi { font-family: monospace; font-size: 9px; } /* Font khusus angka mutasi */
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0;">Laporan Barang Masuk</h2>
        @if(isset($tglAwal) && isset($tglAkhir))
            <p style="margin: 5px 0;">Periode: {{ \Carbon\Carbon::parse($tglAwal)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($tglAkhir)->format('d M Y') }}</p>
        @else
            <p style="margin: 5px 0;">Periode: Semua Waktu</p>
        @endif
        <p style="margin: 0; font-size: 10px;">Dicetak Tanggal: {{ date('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="25%">Nama Barang</th>
                <th width="8%">Jumlah</th>
                <th width="18%">Mutasi Stok</th> {{-- KOLOM DIKEMBALIKAN --}}
                <th width="16%">Harga Satuan</th>
                <th width="16%">Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            @php
                // Hitung mundur: Sisa Stok - Jumlah Masuk = Stok Awal
                $stokAkhir = $row->sisa_stok;
                $jumlahMasuk = $row->jumlah;
                $stokAwal = $stokAkhir - $jumlahMasuk;
            @endphp
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $row->item->nama_barang ?? '[Terhapus]' }}</td>
                <td class="text-center">{{ $row->jumlah }}</td>

                {{-- ISI KOLOM MUTASI --}}
                <td class="text-center mutasi">
                    @if(isset($stokAkhir))
                        {{ $stokAwal }} + {{ $jumlahMasuk }} = <b>{{ $stokAkhir }}</b>
                    @else
                        -
                    @endif
                </td>

                <td class="text-right">Rp {{ number_format($row->harga_satuan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($row->total_harga, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr class="total-row">
                {{-- Colspan 3: No, Tanggal, Nama Barang --}}
                <td colspan="3" class="text-center">TOTAL PERIODE INI</td>

                <td class="text-center">{{ $data->sum('jumlah') }}</td>

                <td></td> {{-- Kosongkan kolom Mutasi --}}
                <td></td> {{-- Kosongkan kolom Harga Satuan --}}

                <td class="text-right">Rp {{ number_format($data->sum('total_harga'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
