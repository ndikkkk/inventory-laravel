<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Sisa Stok ATK</title>
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    
    <style>
        /* CSS Khusus Cetak */
        body {
            background-color: white !important;
            font-family: 'Times New Roman', Times, serif;
            color: black !important;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 { margin: 0; font-weight: bold; text-transform: uppercase; }
        .header p { margin: 0; font-size: 14px; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        th { background-color: #f2f2f2 !important; -webkit-print-color-adjust: exact; }
        
        /* Sembunyikan tombol saat dicetak */
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="container mt-4">
        
        <div class="header">
            <h2>Pemerintah Kabupaten Boyolali</h2>
            <h3>Badan Kepegawaian dan Pengembangan Sumber Daya Manusia</h3>
            <p>Laporan Posisi Stok Alat Tulis Kantor (ATK)</p>
            <p>Per Tanggal: {{ date('d F Y') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th style="width: 15%; text-align: center;">Satuan</th>
                    <th style="width: 15%; text-align: center;">Sisa Stok</th>
                    <th style="width: 15%;">Keterangan</th> </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->category->nama_kategori }}</td>
                    <td style="text-align: center;">{{ $item->satuan }}</td>
                    <td style="text-align: center; font-weight: bold;">
                        {{ $item->stok_saat_ini }}
                    </td>
                    <td></td> </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-5 d-flex justify-content-end">
            <div class="text-center" style="width: 200px;">
                <p>Boyolali, {{ date('d F Y') }}</p>
                <br><br><br>
                <p><strong>Admin Pengelola</strong></p>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>