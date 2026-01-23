<!DOCTYPE html>
<html>
<head>
    <title>Laporan Mutasi Persediaan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9pt; }
        
        /* HEADER */
        .header { text-align: center; border-bottom: 3px double #000; margin-bottom: 20px; padding-bottom: 10px; }
        .header h3 { margin: 0; text-transform: uppercase; }
        
        /* TABEL */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 4px; font-size: 8pt; vertical-align: middle; }
        
        /* HEADER KOLOM UTAMA */
        th { background-color: #e0e0e0; text-align: center; font-weight: bold; }
        
        /* UTILS */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        
        /* === PEWARNAAN KATEGORI (SESUAI GAMBAR) === */
        
        /* Level 1: Hijau Muda (Soft Green) */
        .bg-level1 { background-color: #d1e7dd; font-weight: bold; } 
        
        /* Level 2: Kuning Cream (Soft Yellow) */
        .bg-level2 { background-color: #fff3cd; font-weight: bold; font-style: italic; } 
        
        /* Level 3: Abu-abu Samar (Very Light Gray) */
        .bg-level3 { background-color: #f8f9fa; font-weight: bold; color: #333; }

        /* Total per Kelompok (Level 2) - Abu agak gelap dikit biar beda */
        .bg-total-l2 { background-color: #eaebec; font-weight: bold; } 
        
        /* Grand Total (Orange/Emas - Standar Laporan) */
        .bg-grand-total { background-color: #ffc107; font-weight: bold; } 
        
        /* TANDA TANGAN */
        .signature { margin-top: 30px; width: 100%; border: none; }
        .signature td { border: none; text-align: center; vertical-align: top; }
    </style>
</head>
<body>
    <div class="header">
        <h3 style="margin:0">PEMERINTAH KABUPATEN BOYOLALI</h3>
        <h3 style="margin:0">BADAN KEPEGAWAIAN DAN PENGEMBANGAN SDM</h3>
        <p style="margin: 5px 0; text-decoration: underline; font-weight: bold;">LAPORAN MUTASI BARANG PERSEDIAAN</p>
        <p style="font-size: 9pt; margin: 0;">Periode: {{ \Carbon\Carbon::parse($start)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($end)->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" width="4%">No</th>
                <th rowspan="2">Nama Barang</th>
                <th rowspan="2" width="5%">Sat</th>
                <th colspan="2">Saldo Awal</th>
                <th colspan="2">Pengadaan (Masuk)</th>
                <th colspan="2">Penggunaan (Keluar)</th>
                <th colspan="2">Saldo Akhir</th>
            </tr>
            <tr>
                <th>Qty</th>
                <th>Rupiah</th>
                <th>Qty</th>
                <th>Rupiah</th>
                <th>Qty</th>
                <th>Rupiah</th>
                <th>Qty</th>
                <th>Rupiah</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $no = 1; 
                // INISIALISASI GRAND TOTAL
                $gt_sa_qty=0; $gt_sa_rp=0; 
                $gt_in_qty=0; $gt_in_rp=0; 
                $gt_out_qty=0; $gt_out_rp=0; 
                $gt_end_qty=0; $gt_end_rp=0;
            @endphp

            @foreach($groupedData as $level1 => $groupL1)
                {{-- JUDUL LEVEL 1 (HIJAU MUDA) --}}
                <tr class="bg-level1"><td colspan="11">{{ strtoupper($level1) }}</td></tr>
                
                @php
                    // Tampungan Level 1
                    $l1_sa_qty=0; $l1_sa_rp=0; $l1_in_qty=0; $l1_in_rp=0; $l1_out_qty=0; $l1_out_rp=0; $l1_end_qty=0; $l1_end_rp=0;
                @endphp

                @foreach($groupL1 as $level2 => $groupL2)
                    {{-- JUDUL LEVEL 2 (KUNING CREAM) --}}
                    <tr class="bg-level2"><td colspan="11" style="padding-left: 15px;">{{ $level2 }}</td></tr>
                    
                    @php
                        // INISIALISASI TOTAL LEVEL 2
                        $l2_sa_qty=0; $l2_sa_rp=0; 
                        $l2_in_qty=0; $l2_in_rp=0; 
                        $l2_out_qty=0; $l2_out_rp=0; 
                        $l2_end_qty=0; $l2_end_rp=0;
                    @endphp

                    @foreach($groupL2 as $level3 => $items)
                        {{-- JUDUL LEVEL 3 (ABU SAMAR) --}}
                        <tr class="bg-level3"><td colspan="11" style="padding-left: 30px;">{{ $level3 }}</td></tr>
                        
                        @foreach($items as $item)
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td style="padding-left: 40px;">{{ $item->nama_barang }}</td>
                                <td class="text-center">{{ $item->satuan }}</td>
                                
                                {{-- DATA --}}
                                <td class="text-center">{{ $item->saldo_awal }}</td>
                                <td class="text-right">{{ number_format($item->nilai_saldo_awal, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $item->masuk }}</td>
                                <td class="text-right">{{ number_format($item->nilai_masuk, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $item->keluar }}</td>
                                <td class="text-right">{{ number_format($item->nilai_keluar, 0, ',', '.') }}</td>
                                
                                {{-- SALDO AKHIR (HITAM TEBAL) --}}
                                <td class="text-center text-bold">{{ $item->saldo_akhir }}</td>
                                <td class="text-right text-bold">{{ number_format($item->nilai_saldo_akhir, 0, ',', '.') }}</td>
                            </tr>

                            @php
                                // Hitung Total Level 2
                                $l2_sa_qty += $item->saldo_awal; $l2_sa_rp += $item->nilai_saldo_awal;
                                $l2_in_qty += $item->masuk; $l2_in_rp += $item->nilai_masuk;
                                $l2_out_qty += $item->keluar; $l2_out_rp += $item->nilai_keluar;
                                $l2_end_qty += $item->saldo_akhir; $l2_end_rp += $item->nilai_saldo_akhir;
                            @endphp
                        @endforeach
                    @endforeach

                    {{-- BARIS TOTAL LEVEL 2 (SUB-TOTAL KELOMPOK) --}}
                    <tr class="bg-total-l2">
                        <td colspan="3" class="text-right">Total {{ $level2 }}</td>
                        <td class="text-center">{{ $l2_sa_qty }}</td>
                        <td class="text-right">{{ number_format($l2_sa_rp, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $l2_in_qty }}</td>
                        <td class="text-right">{{ number_format($l2_in_rp, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $l2_out_qty }}</td>
                        <td class="text-right">{{ number_format($l2_out_rp, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $l2_end_qty }}</td>
                        <td class="text-right">{{ number_format($l2_end_rp, 0, ',', '.') }}</td>
                    </tr>

                    @php
                        // Akumulasi ke Level 1
                        $l1_sa_qty += $l2_sa_qty; $l1_sa_rp += $l2_sa_rp;
                        $l1_in_qty += $l2_in_qty; $l1_in_rp += $l2_in_rp;
                        $l1_out_qty += $l2_out_qty; $l1_out_rp += $l2_out_rp;
                        $l1_end_qty += $l2_end_qty; $l1_end_rp += $l2_end_rp;
                    @endphp
                @endforeach

                @php
                    // Akumulasi ke Grand Total
                    $gt_sa_qty += $l1_sa_qty; $gt_sa_rp += $l1_sa_rp;
                    $gt_in_qty += $l1_in_qty; $gt_in_rp += $l1_in_rp;
                    $gt_out_qty += $l1_out_qty; $gt_out_rp += $l1_out_rp;
                    $gt_end_qty += $l1_end_qty; $gt_end_rp += $l1_end_rp;
                @endphp
            @endforeach
        </tbody>
        
        {{-- FOOTER GRAND TOTAL --}}
        <tfoot>
            <tr class="bg-grand-total">
                <td colspan="3" class="text-center text-uppercase">TOTAL PERSEDIAAN AKHIR (31 DESEMBER 2026)</td>
                <td class="text-center">{{ $gt_sa_qty }}</td>
                <td class="text-right">{{ number_format($gt_sa_rp, 0, ',', '.') }}</td>
                <td class="text-center">{{ $gt_in_qty }}</td>
                <td class="text-right">{{ number_format($gt_in_rp, 0, ',', '.') }}</td>
                <td class="text-center">{{ $gt_out_qty }}</td>
                <td class="text-right">{{ number_format($gt_out_rp, 0, ',', '.') }}</td>
                
                {{-- GRAND TOTAL (HITAM TEBAL) --}}
                <td class="text-center text-bold">{{ $gt_end_qty }}</td>
                <td class="text-right text-bold">{{ number_format($gt_end_rp, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- TANDA TANGAN --}}
    <table class="signature">
        <tr>
            <td width="40%">Mengetahui,<br>Kepala BKPSDM Boyolali<br><br><br><br><br>( ..................................... )<br>NIP. ...........................</td>
            <td width="20%"></td>
            <td width="40%">Boyolali, {{ date('d F Y') }}<br>Pengurus Barang<br><br><br><br><br><b>{{ Auth::user()->name }}</b><br>NIP. {{ Auth::user()->nip ?? '...........................' }}</td>
        </tr>
    </table>
</body>
</html>