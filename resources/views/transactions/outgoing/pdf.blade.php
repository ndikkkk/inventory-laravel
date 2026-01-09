<!DOCTYPE html>
<html>
<head>
    <title>Laporan Barang Keluar</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; padding: 5px; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <div class="header">
        <h3>Laporan Barang Keluar</h3>
        <p>Tanggal Cetak: {{ date('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Barang</th>
                <th>Divisi</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $row)
            <tr>
                <td style="text-align:center">{{ $key + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                <td>{{ $row->item->nama_barang ?? '-' }}</td>
                <td>{{ $row->division->nama_bidang ?? '-' }}</td>
                <td>{{ $row->jumlah }} {{ $row->item->satuan ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>