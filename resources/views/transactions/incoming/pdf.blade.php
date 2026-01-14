<!DOCTYPE html>
<html>
<head>
    <title>Laporan Barang Masuk</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid black; padding: 6px; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        /* Font khusus angka agar rumus rapi */
        .mono { font-family: 'Courier New', monospace; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h3 style="margin: 0;">Laporan Barang Masuk</h3>
        <p style="margin: 5px 0;">Tanggal Cetak: {{ date('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th>Nama Barang</th>
                <th width="10%">Jml Masuk</th>
                {{-- KOLOM BARU --}}
                <th width="25%">Mutasi Stok</th>
                <th width="15%">Total Harga (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $row)
            @php
                // LOGIKA HITUNG MUNDUR
                // Stok Awal = Sisa Stok Sekarang - Jumlah Masuk
                $sisa = $row->sisa_stok;
                $jumlah = $row->jumlah;
                $stok_awal = isset($sisa) ? ($sisa - $jumlah) : null;
            @endphp
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                <td>{{ $row->item->nama_barang ?? '[Terhapus]' }}</td>

                <td class="text-center">
                    <span style="color: green;">+ {{ $jumlah }}</span>
                </td>

                {{-- KOLOM MUTASI STOK --}}
                <td class="text-center mono">
                    @if(isset($sisa))
                        {{-- Contoh: 90 + 10 = 100 --}}
                        {{ $stok_awal }} + {{ $jumlah }} = <b>{{ $sisa }}</b>
                    @else
                        -
                    @endif
                </td>

                {{-- KOLOM TOTAL HARGA --}}
                <td class="text-right">
                    Rp {{ number_format($row->total_harga, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
