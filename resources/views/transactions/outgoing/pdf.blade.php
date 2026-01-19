<!DOCTYPE html>
<html>
<head>
    <title>Laporan Barang Keluar</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid black; padding: 4px; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .total-row { background-color: #e0e0e0; font-weight: bold; }
        .mutasi { font-family: monospace; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0;">Laporan Barang Keluar</h2>
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
                <th width="10%">Tanggal</th>
                <th width="20%">Nama Barang</th>
                <th width="15%">Divisi / Bidang</th>
                <th width="8%">Jumlah</th>
                <th width="16%">Mutasi Stok</th> {{-- KOLOM DIKEMBALIKAN --}}
                <th width="13%">Harga Satuan</th>
                <th width="13%">Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            @php
                // Hitung mundur: Sisa Stok + Jumlah Keluar = Stok Awal
                $stokAkhir = $row->sisa_stok;
                $jumlahKeluar = $row->jumlah;
                $stokAwal = $stokAkhir + $jumlahKeluar;
            @endphp
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                <td>
    {{ $row->item->nama_barang ?? $row->deskripsi }} 
    @if(!$row->item_id) (Servis) @endif
</td>
                <td>{{ $row->division->nama_bidang ?? '-' }}</td>
                <td class="text-center">{{ $row->jumlah }}</td>

                {{-- ISI KOLOM MUTASI --}}
                <td class="text-center mutasi">
                    @if(isset($stokAkhir))
                        {{ $stokAwal }} - {{ $jumlahKeluar }} = <b>{{ $stokAkhir }}</b>
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
                {{-- Colspan 4: No, Tanggal, Nama Barang, Divisi --}}
                <td colspan="4" class="text-center">TOTAL PERIODE INI</td>

                <td class="text-center">{{ $data->sum('jumlah') }}</td>

                <td></td> {{-- Kosongkan Mutasi --}}
                <td></td> {{-- Kosongkan Harga Satuan --}}

                <td class="text-right">Rp {{ number_format($data->sum('total_harga'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
