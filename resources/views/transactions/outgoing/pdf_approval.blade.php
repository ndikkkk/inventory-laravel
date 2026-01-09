<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengajuan Barang</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid black; padding: 5px; }
        th { background-color: #f2f2f2; }
        .badge { padding: 2px 5px; border-radius: 3px; color: #fff; font-size: 10px; }
        .bg-warning { background-color: #ffc107; color: #000; } /* Menunggu */
        .bg-success { background-color: #198754; } /* Approved */
        .bg-danger { background-color: #dc3545; } /* Rejected */
    </style>
</head>
<body>
    <h2>Laporan Data Pengajuan Barang</h2>
    <p>Tanggal Cetak: {{ date('d M Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Divisi</th>
                <th>Barang</th>
                <th>Jml</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td style="text-align:center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $row->division->nama_bidang ?? '-' }}</td>
                <td>{{ $row->item->nama_barang ?? '-' }}</td>
                <td style="text-align:center">{{ $row->jumlah }}</td>
                <td style="text-align:center">
                    @if($row->status == 'pending')
                        <span class="badge bg-warning">Menunggu</span>
                    @elseif($row->status == 'approved')
                        <span class="badge bg-success">Disetujui</span>
                    @else
                        <span class="badge bg-danger">Ditolak</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>