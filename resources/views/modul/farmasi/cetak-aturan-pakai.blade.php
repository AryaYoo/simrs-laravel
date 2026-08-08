<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aturan Pakai Obat {{ $penjualan->nota_jual }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #111;
            margin: 0;
            padding: 0;
            background-color: #fff;
            -webkit-print-color-adjust: exact !important;
        }
        .etiket-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .etiket-card {
            border: 2px solid #333;
            border-radius: 6px;
            padding: 8px 10px;
            page-break-inside: avoid;
            background: #fff;
        }
        .header-instansi {
            font-weight: bold;
            font-size: 11px;
            text-align: center;
            border-bottom: 1px solid #444;
            padding-bottom: 4px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .patient-row {
            display: flex;
            justify-content: space-between;
            font-size: 9.5px;
            margin-bottom: 4px;
        }
        .drug-name {
            font-weight: bold;
            font-size: 11px;
            margin: 6px 0 3px 0;
            color: #000;
        }
        .aturan-box {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            padding: 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11.5px;
            text-align: center;
            margin-top: 6px;
            color: #222;
        }
        .footer-date {
            text-align: right;
            font-size: 8.5px;
            color: #666;
            margin-top: 6px;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="position: fixed; top: 15px; right: 15px; z-index: 9999;">
        <button onclick="window.print()" style="background-color: #4C5C2D; color: white; border: none; padding: 8px 16px; font-weight: bold; border-radius: 6px; cursor: pointer; shadow: 0 2px 4px rgba(0,0,0,0.2);">
            Cetak Etiket Sekarang
        </button>
    </div>

    <div style="max-width: 190mm; margin: 0 auto;">
        <h3 class="no-print" style="text-align: center; margin-bottom: 15px;">Etiket Aturan Pakai Obat — Nota {{ $penjualan->nota_jual }}</h3>
        
        <div class="etiket-grid">
            @foreach($items as $item)
            <div class="etiket-card">
                <div class="header-instansi">
                    {{ $setting['nama_instansi'] ?? 'RSIA IBI SURABAYA' }}<br>
                    <span style="font-size: 8.5px; font-weight: normal; text-transform: none;">INSTALASI FARMASI</span>
                </div>
                <div class="patient-row">
                    <span><strong>No. RM:</strong> {{ $penjualan->no_rkm_medis ?: '-' }}</span>
                    <span><strong>Tgl:</strong> {{ $penjualan->tgl_jual ? $penjualan->tgl_jual->format('d/m/Y') : '-' }}</span>
                </div>
                <div class="patient-row">
                    <span><strong>Nama:</strong> {{ $penjualan->nm_pasien ?: 'PASIEN UMUM' }}</span>
                </div>
                
                <div class="drug-name">
                    {{ $item->barang->nama_brng ?? $item->kode_brng }} ({{ number_format($item->jumlah, 0, ',', '.') }} {{ $item->kode_sat }})
                </div>
                
                <div class="aturan-box">
                    ATURAN PAKAI:<br>
                    <span style="font-size: 12px; color: #1e3a8a;">{{ $item->aturan_pakai ?: 'Sesuai Petunjuk Dokter / Farmasi' }}</span>
                </div>
                
                <div class="footer-date">
                    Nota: {{ $penjualan->nota_jual }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

</body>
</html>
