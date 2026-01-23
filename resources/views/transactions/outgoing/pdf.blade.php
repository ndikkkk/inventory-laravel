<!DOCTYPE html>
<html>
<head>
    <title>Laporan Barang Keluar</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        
        /* HEADER RESMI (KOP SURAT) */
        .header { 
            text-align: center; 
            border-bottom: 3px double #000; 
            margin-bottom: 20px; 
            padding-bottom: 10px; 
        }
        .header h3 { 
            margin: 0; 
            font-size: 14pt; 
            text-transform: uppercase; 
        }
        .header p { 
            margin: 2px 0; 
        }
        .title-report {
            margin-top: 10px;
            font-weight: bold;
            text-decoration: underline;
            font-size: 11pt;
        }

        /* TABEL */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; font-size: 9pt; }
        th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        
        /* UTILS */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bg-group { background-color: #e9ecef; font-weight: bold; }
        .mutasi { font-family: monospace; font-size: 9pt; }
        .total-row { background-color: #e0e0e0; font-weight: bold; }
    </style>
</head>
<body>

    {{-- HEADER KOP SURAT RESMI --}}
    <div class="header">
        <h3>PEMERINTAH KABUPATEN BOYOLALI</h3>
        <h3>BADAN KEPEGAWAIAN DAN PENGEMBANGAN SDM</h3>
        <p class="title-report">LAPORAN BARANG KELUAR</p>
        
        @if(isset($tglAwal) && isset($tglAkhir))
            <p style="font-size: 10pt;">Periode: {{ \Carbon\Carbon::parse($tglAwal)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($tglAkhir)->format('d M Y') }}</p>
        @elseif(isset($start) && isset($end))
            <p style="font-size: 10pt;">Periode: {{ \Carbon\Carbon::parse($start)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($end)->format('d M Y') }}</p>
        @else
            <p style="font-size: 10pt;">Periode: Semua Waktu</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Tanggal</th>
                <th width="20%">Nama Barang</th>
                <th width="15%">Divisi / Bidang</th>
                <th width="8%">Jumlah</th>
                <th width="16%">Mutasi Stok</th>
                <th width="13%">Harga Satuan</th>
                <th width="13%">Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $no = 1; 
                $totalQty = 0;
                $totalHarga = 0;
            @endphp

            {{-- MODE 1: FITUR BARU --}}
            @if(isset($groupedData))
                @foreach($groupedData as $level1 => $groupL1)
                    @foreach($groupL1 as $level2 => $groupL2)
                        @foreach($groupL2 as $level3 => $items)
                            <tr class="bg-group">
                                <td colspan="8" style="text-align: left; padding-left: 10px;">
                                    {{ $level3 }} <span style="font-weight: normal; font-size: 8px;">({{ $level1 }})</span>
                                </td>
                            </tr>
                            @foreach($items as $row)
                                @php
                                    $stokAkhir = $row->sisa_stok;
                                    $jumlahKeluar = $row->jumlah;
                                    $stokAwal = $stokAkhir + $jumlahKeluar;
                                    $totalQty += $row->jumlah;
                                    $totalHarga += $row->total_harga;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                                    <td>{{ $row->item->nama_barang ?? $row->deskripsi }} @if (!$row->item_id) (Servis) @endif</td>
                                    <td>{{ $row->division->nama_bidang ?? '-' }}</td>
                                    <td class="text-center">{{ $row->jumlah }}</td>
                                    <td class="text-center mutasi">
                                        @if(isset($stokAkhir)) {{ $stokAwal }} - {{ $jumlahKeluar }} = <b>{{ $stokAkhir }}</b> @else - @endif
                                    </td>
                                    <td class="text-right">Rp {{ number_format($row->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="text-right">Rp {{ number_format($row->total_harga, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                @endforeach

            {{-- MODE 2: FITUR LAMA --}}
            @else
                @php
                    $totalQty = $data->sum('jumlah');
                    $totalHarga = $data->sum('total_harga');
                @endphp
                @foreach ($data as $row)
                    @php
                        $stokAkhir = $row->sisa_stok;
                        $jumlahKeluar = $row->jumlah;
                        $stokAwal = $stokAkhir + $jumlahKeluar;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $row->item->nama_barang ?? $row->deskripsi }} @if (!$row->item_id) (Servis) @endif</td>
                        <td>{{ $row->division->nama_bidang ?? '-' }}</td>
                        <td class="text-center">{{ $row->jumlah }}</td>
                        <td class="text-center mutasi">
                            @if (isset($stokAkhir)) {{ $stokAwal }} - {{ $jumlahKeluar }} = <b>{{ $stokAkhir }}</b> @else - @endif
                        </td>
                        <td class="text-right">Rp {{ number_format($row->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($row->total_harga, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-center">TOTAL PERIODE INI</td>
                <td class="text-center">{{ $totalQty }}</td>
                <td></td>
                <td></td>
                <td class="text-right">Rp {{ number_format($totalHarga, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>