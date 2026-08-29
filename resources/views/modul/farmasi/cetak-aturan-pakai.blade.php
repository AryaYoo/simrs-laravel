<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aturan Pakai Obat {{ $penjualan->nota_jual }}</title>
    <style id="page-style">
        @page {
            size: 60mm 40mm;
            margin: 1mm;
        }
    </style>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            background-color: #f3f4f6;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Toolbar Navigasi */
        .no-print {
            background-color: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 9999;
        }
        .toolbar-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .toolbar-label {
            font-size: 12px;
            font-weight: bold;
            color: #374151;
        }
        .toolbar-select {
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            background-color: #fff;
            color: #1f2937;
            cursor: pointer;
            outline: none;
        }
        .toolbar-btn {
            background-color: #4C5C2D;
            color: #fff;
            border: none;
            padding: 7px 16px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.15s;
        }
        .toolbar-btn:hover {
            background-color: #3d4b24;
        }

        /* Container Preview */
        .preview-wrapper {
            padding: 25px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }

        /* Base Etiket Card */
        .etiket-card {
            background: #fff;
            border: 1.5px solid #000;
            border-radius: 4px;
            page-break-inside: avoid;
            page-break-after: always;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            margin: 0 auto;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .etiket-card:last-child {
            page-break-after: auto;
        }

        .header-instansi {
            width: 100%;
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid #000;
            text-transform: uppercase;
            line-height: 1.15;
        }
        .patient-info {
            width: 100%;
            display: flex;
            flex-direction: column;
            border-bottom: 1px dashed #777;
        }
        .patient-row {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            line-height: 1.2;
        }
        .drug-section {
            width: 100%;
            display: flex;
            flex-direction: column;
        }
        .drug-name {
            width: 100%;
            font-weight: bold;
            color: #000;
            line-height: 1.2;
            word-break: break-word;
        }
        .aturan-box {
            width: 100%;
            background-color: #fafafa;
            border: 1px dashed #333;
            border-radius: 3px;
            font-weight: bold;
            text-align: center;
            color: #000;
            line-height: 1.2;
        }
        .footer-info {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #444;
            line-height: 1.1;
        }

        /* ========================================================
           SIZE VARIANTS
           ======================================================== */

        /* 1. Size 50x35 mm (Thermal Mini) */
        .size-50x35 .etiket-card {
            width: 48mm;
            height: 33mm;
            padding: 1.5mm 2mm;
        }
        .size-50x35 .header-instansi { font-size: 7px; padding-bottom: 1px; }
        .size-50x35 .header-sub { font-size: 5.5px; }
        .size-50x35 .patient-info { padding: 1px 0; }
        .size-50x35 .patient-row { font-size: 6.5px; }
        .size-50x35 .drug-name { font-size: 7px; margin: 1px 0; }
        .size-50x35 .aturan-box { font-size: 7px; padding: 1.5px 1px; }
        .size-50x35 .aturan-text { font-size: 8px; color: #1e3a8a; }
        .size-50x35 .footer-info { font-size: 5.5px; margin-top: 1px; }

        /* 2. Size 60x40 mm (Thermal Standar Farmasi - Default) */
        .size-60x40 .etiket-card {
            width: 57mm;
            height: 37mm;
            padding: 2mm 2.5mm;
        }
        .size-60x40 .header-instansi { font-size: 8px; padding-bottom: 1.5px; }
        .size-60x40 .header-sub { font-size: 6.5px; }
        .size-60x40 .patient-info { padding: 1.5px 0; }
        .size-60x40 .patient-row { font-size: 7.5px; }
        .size-60x40 .drug-name { font-size: 8px; margin: 1.5px 0; }
        .size-60x40 .aturan-box { font-size: 8px; padding: 2px; }
        .size-60x40 .aturan-text { font-size: 9.5px; color: #1e3a8a; }
        .size-60x40 .footer-info { font-size: 6.5px; margin-top: 1.5px; }

        /* 3. Size 70x50 mm (Thermal Sedang) */
        .size-70x50 .etiket-card {
            width: 66mm;
            height: 46mm;
            padding: 2.5mm 3.5mm;
        }
        .size-70x50 .header-instansi { font-size: 9.5px; padding-bottom: 2px; }
        .size-70x50 .header-sub { font-size: 7.5px; }
        .size-70x50 .patient-info { padding: 2px 0; }
        .size-70x50 .patient-row { font-size: 8.5px; }
        .size-70x50 .drug-name { font-size: 9.5px; margin: 2.5px 0; }
        .size-70x50 .aturan-box { font-size: 9.5px; padding: 3px; }
        .size-70x50 .aturan-text { font-size: 11px; color: #1e3a8a; }
        .size-70x50 .footer-info { font-size: 7.5px; margin-top: 2px; }

        /* 4. Size 80x50 mm (Thermal Lebar) */
        .size-80x50 .etiket-card {
            width: 76mm;
            height: 46mm;
            padding: 2.5mm 4mm;
        }
        .size-80x50 .header-instansi { font-size: 10px; padding-bottom: 2px; }
        .size-80x50 .header-sub { font-size: 8px; }
        .size-80x50 .patient-info { padding: 2px 0; }
        .size-80x50 .patient-row { font-size: 9px; }
        .size-80x50 .drug-name { font-size: 10.5px; margin: 2.5px 0; }
        .size-80x50 .aturan-box { font-size: 10px; padding: 3.5px; }
        .size-80x50 .aturan-text { font-size: 12px; color: #1e3a8a; }
        .size-80x50 .footer-info { font-size: 8px; margin-top: 2px; }

        /* 5. Size A4 (Kertas HVS / Lembaran) */
        .size-a4 .preview-wrapper {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            max-width: 190mm;
            margin: 0 auto;
            padding: 10px;
        }
        .size-a4 .etiket-card {
            width: 100%;
            min-height: 52mm;
            padding: 8px 12px;
            border: 2px solid #000;
            page-break-after: auto;
        }
        .size-a4 .header-instansi { font-size: 11px; padding-bottom: 3px; }
        .size-a4 .header-sub { font-size: 8.5px; }
        .size-a4 .patient-info { padding: 3px 0; }
        .size-a4 .patient-row { font-size: 9.5px; }
        .size-a4 .drug-name { font-size: 11px; margin: 4px 0; }
        .size-a4 .aturan-box { font-size: 11px; padding: 5px; }
        .size-a4 .aturan-text { font-size: 13px; color: #1e3a8a; }
        .size-a4 .footer-info { font-size: 8.5px; margin-top: 3px; }

        /* Print Media Override */
        @media print {
            body {
                background-color: #fff;
            }
            .no-print {
                display: none !important;
            }
            .preview-wrapper {
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
            }
            .size-a4 .preview-wrapper {
                display: grid !important;
            }
            .etiket-card {
                box-shadow: none !important;
                margin: 0 auto !important;
            }
        }
    </style>
</head>
<body class="size-60x40" id="body-container">

    {{-- Toolbar Navigasi & Ukuran Kertas --}}
    <div class="no-print">
        <div class="toolbar-group">
            <span class="toolbar-label">Ukuran Kertas Stiker:</span>
            <select id="size-selector" class="toolbar-select" onchange="changeSize(this.value)">
                <option value="50x35">50 x 35 mm (Stiker Thermal Kecil)</option>
                <option value="60x40" selected>60 x 40 mm (Stiker Standar Farmasi)</option>
                <option value="70x50">70 x 50 mm (Stiker Thermal Sedang)</option>
                <option value="80x50">80 x 50 mm (Stiker Thermal Besar)</option>
                <option value="a4">A4 (Lembaran Kertas HVS)</option>
            </select>
        </div>

        <div class="toolbar-group">
            <button type="button" onclick="window.print()" class="toolbar-btn">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Cetak Etiket Sekarang
            </button>
            <button type="button" onclick="window.close()" class="toolbar-btn" style="background-color: #6b7280;">
                Tutup
            </button>
        </div>
    </div>

    {{-- Tampilan Kartu Etiket --}}
    <div class="preview-wrapper">
        @foreach($items as $item)
        <div class="etiket-card">
            {{-- Header Instansi --}}
            <div class="header-instansi">
                {{ $setting['nama_instansi'] ?? 'RSIA IBI SURABAYA' }}<br>
                <span class="header-sub" style="font-weight: normal; text-transform: none;">INSTALASI FARMASI</span>
            </div>

            {{-- Info Pasien --}}
            <div class="patient-info">
                <div class="patient-row">
                    <span><strong>No. RM:</strong> {{ $penjualan->no_rkm_medis ?: '-' }}</span>
                    <span><strong>Tgl:</strong> {{ $penjualan->tgl_jual ? $penjualan->tgl_jual->format('d/m/Y') : '-' }}</span>
                </div>
                <div class="patient-row">
                    <span><strong>Nama:</strong> {{ $penjualan->nm_pasien ?: 'PASIEN UMUM' }}</span>
                    <span><strong>Nota:</strong> {{ $penjualan->nota_jual }}</span>
                </div>
            </div>
            
            {{-- Nama Obat --}}
            <div class="drug-section">
                <div class="drug-name">
                    {{ $item->barang->nama_brng ?? $item->kode_brng }} ({{ number_format($item->jumlah, 0, ',', '.') }} {{ $item->kode_sat }})
                </div>
            </div>
            
            {{-- Aturan Pakai / Dosis --}}
            <div class="aturan-box">
                ATURAN PAKAI:<br>
                <span class="aturan-text">{{ $item->aturan_pakai ?: 'Sesuai Petunjuk Dokter / Farmasi' }}</span>
            </div>
            
            {{-- Footer Waktu & Lokasi --}}
            <div class="footer-info">
                <span>Petugas: {{ $penjualan->petugas->nama ?? ($penjualan->nip ?? '-') }}</span>
            </div>
        </div>
        @endforeach
    </div>

    <script>
        const sizeMap = {
            '50x35': { page: '50mm 35mm', margin: '1mm', cls: 'size-50x35' },
            '60x40': { page: '60mm 40mm', margin: '1mm', cls: 'size-60x40' },
            '70x50': { page: '70mm 50mm', margin: '1.5mm', cls: 'size-70x50' },
            '80x50': { page: '80mm 50mm', margin: '1.5mm', cls: 'size-80x50' },
            'a4':    { page: 'A4 portrait', margin: '10mm', cls: 'size-a4' }
        };

        function changeSize(sizeKey) {
            const config = sizeMap[sizeKey] || sizeMap['60x40'];
            
            // Update Page CSS
            document.getElementById('page-style').innerHTML = `@page { size: ${config.page}; margin: ${config.margin}; }`;
            
            // Update Body Class
            const body = document.getElementById('body-container');
            body.className = config.cls;

            // Save to localStorage
            localStorage.setItem('etiket_paper_size', sizeKey);
        }

        // Init preferred size from localStorage or query param
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const initialSize = urlParams.get('size') || localStorage.getItem('etiket_paper_size') || '60x40';
            
            const selector = document.getElementById('size-selector');
            if (selector && sizeMap[initialSize]) {
                selector.value = initialSize;
                changeSize(initialSize);
            }
        });
    </script>
</body>
</html>
