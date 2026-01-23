<!DOCTYPE html>
<html>
<head>
    <title>Laporan Mutasi Barang</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; color: #000; }
        
        /* KOP SURAT */
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px double #000; padding-bottom: 10px; }
        .header h3 { margin: 0; font-size: 14pt; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 11pt; }
        .header .small { font-size: 9pt; font-style: italic; }

        /* JUDUL LAPORAN */
        .title-section { text-align: center; margin-bottom: 20px; }
        .title-section h4 { margin: 5px 0; text-decoration: underline; text-transform: uppercase; }

        /* TABEL */
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #000; padding: 5px; font-size: 9pt; vertical-align: middle; }
        th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        
        /* UTILS */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .bg-light { background-color: #f9f9f9; }

        /* TANDA TANGAN */
        .signature-section { margin-top: 40px; page-break-inside: avoid; }
        .signature-table { border: none; width: 100%; }
        .signature-table td { border: none; text-align: center; vertical-align: top; }
    </style>
</head>
<body>

    {{-- 1. KOP SURAT RESMI --}}
    <div class="header">
        <h3>PEMERINTAH KABUPATEN BOYOLALI</h3>
        <h3>BADAN KEPEGAWAIAN DAN PENGEMBANGAN SDM</h3>
        <p>Komplek Perkantoran Terpadu Kabupaten Boyolali</p>
        <p class="small">Laporan digenerate otomatis oleh Sistem Informasi Logistik (SIM-LOG)</p>
    </div>

    {{-- 2. JUDUL LAPORAN --}}
    <div class="title-section">
        <h4>LAPORAN MUTASI & POSISI PERSEDIAAN BARANG</h4>
        <p style="font-size: 10pt;">
            Periode: 
            @if(isset($tglAwal) && isset($tglAkhir))
                {{ \Carbon\Carbon::parse($tglAwal)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($tglAkhir)->format('d M Y') }}
            @else
                Semua Waktu
            @endif
        </p>
    </div>

    {{-- 3. TABEL MUTASI (GABUNGAN) --}}
    <h5 style="margin-bottom: 5px;">A. RINCIAN MUTASI BARANG</h5>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Tanggal</th>
                <th width="25%">Uraian Barang</th>
                <th width="15%">Masuk / Debet<br>(Rp)</th>
                <th width="15%">Keluar / Kredit<br>(Rp)</th>
                <th width="10%">Saldo<br>(Qty)</th>
                <th width="20%">Keterangan / Divisi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gabunganStok as $row)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                
                {{-- Nama Barang --}}
                <td>
                    <span class="text-bold">{{ $row->item->nama_barang ?? '[Barang Dihapus]' }}</span>
                </td>

                {{-- Kolom Masuk (Rupiah) --}}
                <td class="text-right">
                    @if($row->jenis_transaksi == 'masuk') 
                        Rp {{ number_format($row->total_harga, 0, ',', '.') }}<br>
                        <small>({{ $row->jumlah }} {{ $row->item->satuan ?? 'Unit' }})</small>
                    @else - @endif
                </td>

                {{-- Kolom Keluar (Rupiah) --}}
                <td class="text-right">
                    @if($row->jenis_transaksi == 'keluar') 
                        Rp {{ number_format($row->total_harga, 0, ',', '.') }}<br>
                        <small>({{ $row->jumlah }} {{ $row->item->satuan ?? 'Unit' }})</small>
                    @else - @endif
                </td>

                {{-- Saldo Fisik --}}
                <td class="text-center">{{ $row->sisa_stok ?? '-' }}</td>

                {{-- Keterangan --}}
                <td style="font-size: 8pt;">
                    @if($row->jenis_transaksi == 'masuk') 
                        Pengadaan / Restock
                    @else 
                        {{ $row->division->nama_bidang ?? 'Umum' }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-light">
                <td colspan="3" class="text-center text-bold">TOTAL MUTASI PERIODE INI</td>
                <td class="text-right text-bold">Rp {{ number_format($totalRupiahMasuk, 0, ',', '.') }}</td>
                <td class="text-right text-bold">Rp {{ number_format($totalRupiahKeluar, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    {{-- 4. TABEL PEMELIHARAAN --}}
    <h5 style="margin-bottom: 5px; margin-top: 20px;">B. BIAYA PEMELIHARAAN & JASA</h5>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th>Uraian Jasa / Servis</th>
                <th>Divisi Pengguna</th>
                <th width="20%">Biaya (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($maintenance as $m)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($m->tanggal)->format('d/m/Y') }}</td>
                <td>
                    {{ $m->deskripsi }}
                    @if($m->km_saat_ini)<br><small>(KM: {{ $m->km_saat_ini }})</small>@endif
                </td>
                <td>{{ $m->division->nama_bidang ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($m->total_harga, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center" style="font-style: italic;">Tidak ada data pemeliharaan.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="bg-light">
                <td colspan="4" class="text-center text-bold">TOTAL BIAYA JASA</td>
                <td class="text-right text-bold">Rp {{ number_format($totalBiayaServis, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- 5. RINGKASAN REKONSILIASI --}}
    <div style="width: 60%; margin-top: 20px; border: 1px solid #000; padding: 10px;">
        <h5 style="margin: 0 0 10px 0; text-decoration: underline;">RINGKASAN POSISI NILAI ASET:</h5>
        <table style="border: none; margin: 0;">
            <tr>
                <td style="border: none; padding: 2px;">Nilai Awal (Stok 2026)</td>
                <td style="border: none; padding: 2px;">:</td>
                <td style="border: none; padding: 2px;" class="text-right">Rp {{ number_format($totalAsetAwal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px;">Total Belanja Barang</td>
                <td style="border: none; padding: 2px;">:</td>
                <td style="border: none; padding: 2px;" class="text-right">Rp {{ number_format($totalRupiahMasuk, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px;">Total Pemakaian (Beban)</td>
                <td style="border: none; padding: 2px;">:</td>
                <td style="border: none; padding: 2px;" class="text-right">(Rp {{ number_format($totalRupiahKeluar, 0, ',', '.') }})</td>
            </tr>
            <tr>
                <td style="border: none; border-top: 1px solid #000; font-weight: bold;">NILAI PERSEDIAAN AKHIR</td>
                <td style="border: none; border-top: 1px solid #000;">:</td>
                <td style="border: none; border-top: 1px solid #000; font-weight: bold;" class="text-right">Rp {{ number_format($totalAsetAkhir, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    {{-- 6. TANDA TANGAN PEJABAT (WAJIB DI PEMERINTAHAN) --}}
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td width="40%">
                    Mengetahui,<br>
                    Kepala BKPSDM Boyolali<br>
                    <br><br><br><br><br>
                    <span style="text-decoration: underline; font-weight: bold;">( ..................................... )</span><br>
                    NIP. .....................................
                </td>
                <td width="20%"></td> {{-- Spacer --}}
                <td width="40%">
                    Boyolali, {{ date('d F Y') }}<br>
                    Pengurus Barang<br>
                    <br><br><br><br><br>
                    <span style="text-decoration: underline; font-weight: bold;">{{ Auth::user()->name }}</span><br>
                    NIP. {{ Auth::user()->nip ?? '.....................................' }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>