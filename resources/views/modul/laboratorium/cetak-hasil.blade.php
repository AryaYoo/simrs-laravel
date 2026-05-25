<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Hasil Laboratorium - {{ $regPeriksa->no_rkm_medis ?? '' }}</title>
    <!-- Tambahkan library Tailwind CSS untuk style toolbar preview (tidak ikut dicetak) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Base styling (Screen & Print) */
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #525659; /* PDF Viewer Background */
        }
        
        /* Toolbar Style (Screen Only) */
        .preview-toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 50px;
            background-color: #323639;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
        }

        .toolbar-group {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .paper-select {
            background-color: #45494c;
            color: white;
            border: 1px solid #555;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
            outline: none;
        }
        
        .paper-select:focus {
            border-color: #8ab4f8;
        }

        .btn-action {
            background-color: #8ab4f8;
            color: #202124;
            border: none;
            padding: 6px 16px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background-color 0.2s;
        }

        .btn-action:hover {
            background-color: #aecbfa;
        }

        .btn-close {
            background-color: transparent;
            color: #e8eaed;
            border: 1px solid #5f6368;
            padding: 6px 16px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-close:hover {
            background-color: #3c4043;
        }

        .preview-container {
            margin-top: 70px;
            margin-bottom: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        /* Document Styling */
        .document-page {
            background-color: white;
            width: 210mm; /* A4 Width */
            /* Height is defined dynamically below depending on paper size */
            padding: 1.5cm;
            box-sizing: border-box;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
            position: relative;
        }
        .header {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .header-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-right: 15px;
        }
        .header-text h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            margin-bottom: 2px;
        }
        .header-text p {
            margin: 0;
            font-size: 11px;
            line-height: 1.3;
        }
        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .info-table td {
            vertical-align: top;
            padding: 2px 5px;
        }
        .info-label {
            width: 130px;
        }
        .info-colon {
            width: 10px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        .data-table th {
            text-align: center;
            font-weight: bold;
        }
        .footer {
            width: 100%;
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            text-align: center;
            width: 250px;
        }
        .signature-name {
            text-decoration: underline;
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* Print Specific Styles */
        @media print {
            .preview-toolbar {
                display: none !important;
            }
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                background-color: transparent !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .preview-container {
                margin: 0 !important;
                display: block !important;
            }
            .document-page {
                box-shadow: none !important;
                margin: 0 !important;
                /* Inherit all width/height/padding from screen mode so it prints EXACTLY as seen on screen */
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                page-break-after: always !important;
                break-after: page !important;
            }
            .document-page:last-child, .document-page:last-of-type {
                page-break-after: auto !important;
                break-after: auto !important;
            }
            /* Make printed pages exactly 1mm shorter than physical paper to prevent fractional spill-over blank pages */
            .size-a4 {
                height: 296mm !important;
                min-height: 296mm !important;
                max-height: 296mm !important;
                overflow: hidden !important;
            }
            .size-f4 {
                height: 329mm !important;
                min-height: 329mm !important;
                max-height: 329mm !important;
                overflow: hidden !important;
            }
        }
    </style>
    
    <!-- Dynamic Style for Print Page Size -->
    <style id="print-page-style">
        @media print {
            @page { size: A4 portrait; margin: 0; }
        }
    </style>
</head>
<body>

    <!-- NO-PRINT TOOLBAR -->
    <div class="preview-toolbar no-print">
        <div class="toolbar-group">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span style="font-size: 14px; font-weight: 500;">Pratinjau Hasil Lab ({{ $regPeriksa->no_rkm_medis ?? '' }})</span>
        </div>
        
        <div class="toolbar-group">
            <span style="font-size: 13px; color: #9aa0a6;">Ukuran Kertas:</span>
            <select id="paperSizeSelector" class="paper-select" onchange="changePaperSize()">
                <option value="a4">A4 (210 x 297 mm)</option>
                <option value="f4">F4 / Folio (215 x 330 mm)</option>
            </select>
            
            <div style="width: 1px; height: 24px; background: #5f6368; margin: 0 10px;"></div>
            
            <button class="btn-action" onclick="window.print()">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Dokumen
            </button>
            <button class="btn-close" onclick="window.close()">Tutup</button>
        </div>
    </div>

    <!-- DOCUMENT WRAPPER -->
    <div class="preview-container">
        @forelse($pages as $pageIndex => $pageData)
            <!-- VIRTUAL PAPER -->
            <div class="document-page size-a4" 
                @if(!empty($settingCetak->wallpaper)) 
                    style="background-image: url('data:image/jpeg;base64,{{ base64_encode($settingCetak->wallpaper) }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"
                @endif
            >
                
                <!-- --- ACTUAL PRINT CONTENT --- -->
                <!-- PAGE NUMBERING (Bottom Left) -->
                <div style="position: absolute; bottom: 1.5cm; left: 1.5cm; font-size: 10px; color: #999; opacity: 0.7; font-weight: bold;">
                    Halaman {{ $pageIndex + 1 }} / {{ count($pages) }}
                </div>

                @if($pageIndex == 0)
                    <div class="header">
                        @if(!empty($settingCetak->logo))
                            <img src="data:image/jpeg;base64,{{ base64_encode($settingCetak->logo) }}" class="header-logo" alt="Logo">
                        @else
                            <div class="header-logo" style="background:#eee; display:flex; align-items:center; justify-content:center; text-align:center; font-size:10px;">LOGO</div>
                        @endif
                        
                        <div class="header-text">
                            <h1>{{ $settingCetak->nama_instansi ?? 'RUMAH SAKIT' }}</h1>
                            <p>{{ $settingCetak->alamat_instansi ?? '' }}, {{ $settingCetak->kabupaten ?? '' }}, {{ $settingCetak->propinsi ?? '' }}</p>
                            <p>{{ $settingCetak->kontak ?? '' }}</p>
                            <p>E-mail : {{ $settingCetak->email ?? '' }}</p>
                        </div>
                    </div>
                @else
                    <!-- Spacer for pages without Kop Surat -->
                    <div style="height: 1cm;"></div>
                @endif

            <div class="title">
                HASIL PEMERIKSAAN LABORATORIUM
            </div>

            <table class="info-table">
                <tr>
                    <td class="info-label">No.RM</td>
                    <td class="info-colon">:</td>
                    <td style="width: 40%;">{{ $regPeriksa->no_rkm_medis ?? '-' }}</td>
                    <td class="info-label">No.Permintaan Lab</td>
                    <td class="info-colon">:</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td class="info-label">Nama Pasien</td>
                    <td class="info-colon">:</td>
                    <td>{{ $regPeriksa->nm_pasien ?? '-' }}</td>
                    <td class="info-label">Tgl.Permintaan</td>
                    <td class="info-colon">:</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td class="info-label">JK/Umur</td>
                    <td class="info-colon">:</td>
                    <td>
                        {{ $regPeriksa->jk ?? '-' }} / 
                        {{ $regPeriksa->umur ?? '-' }}
                    </td>
                    <td class="info-label">Jam Permintaan</td>
                    <td class="info-colon">:</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td class="info-label">Alamat</td>
                    <td class="info-colon">:</td>
                    <td>{{ $regPeriksa->alamat ?? '-' }}</td>
                    <td class="info-label">Tgl. Keluar Hasil</td>
                    <td class="info-colon">:</td>
                    <td>{{ date('d-m-Y', strtotime($masterPeriksa->tgl_periksa)) }}</td>
                </tr>
                <tr>
                    <td class="info-label">No.Periksa</td>
                    <td class="info-colon">:</td>
                    <td>{{ $masterPeriksa->no_periksa ?? '-' }}</td>
                    <td class="info-label">Jam Keluar Hasil</td>
                    <td class="info-colon">:</td>
                    <td>{{ $masterPeriksa->jam ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Dokter Pengirim</td>
                    <td class="info-colon">:</td>
                    <td>{{ $masterPeriksa->dokter_perujuk ?? '-' }}</td>
                    <td class="info-label">Kamar/Poli</td>
                    <td class="info-colon">:</td>
                    <td>{{ $regPeriksa->nm_poli ?? 'Rawat Inap' }}</td>
                </tr>
            </table>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Pemeriksaan</th>
                        <th style="width: 15%;">Hasil</th>
                        <th style="width: 15%;">Satuan</th>
                        <th style="width: 25%;">Nilai Rujukan</th>
                        <th style="width: 20%;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pageData as $row)
                        @if($row['type'] == 'header')
                            <tr>
                                <td colspan="5" style="font-weight: bold; background: rgba(0,0,0,0.02);">{{ $row['name'] }}</td>
                            </tr>
                        @else
                            <tr>
                                <td style="padding-left: 20px;">{{ $row['pemeriksaan'] ?? '' }}</td>
                                <td style="text-align: center; {{ !empty($row['keterangan']) ? 'color: red; font-weight: bold;' : '' }}">{{ $row['hasil'] ?? '' }}</td>
                                <td style="text-align: center;">{{ $row['satuan'] ?? '' }}</td>
                                <td style="text-align: center;">{{ $row['nilai_rujukan'] ?? '-' }}</td>
                                <td style="text-align: center; {{ !empty($row['keterangan']) ? 'color: red; font-weight: bold;' : '' }}">{{ $row['keterangan'] ?? '-' }}</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center;">Tidak ada detail pemeriksaan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($loop->last)
                <div style="font-size: 10px; margin-bottom: 20px; color: #555;">
                    <span style="font-weight: bold;">Catatan :</span> Jika ada keragu-raguan pemeriksaan, diharapkan segera menghubungi laboratorium.
                    @if(!empty($saranKesan))
                        <br><br>
                        <span style="font-weight: bold;">Kesan :</span> {{ $saranKesan->kesan ?? '-' }}<br>
                        <span style="font-weight: bold;">Saran :</span> {{ $saranKesan->saran ?? '-' }}
                    @endif
                </div>

                <div class="footer">
                    <div class="signature">
                        Penanggung Jawab
                        
                        @php
                            $qrTextPj = "Penanggung Jawab\n" .
                                      ($masterPeriksa->penanggung_jawab ?? '-') . "\n" .
                                      date('d-m-Y', strtotime($masterPeriksa->tgl_periksa));
                        @endphp
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($qrTextPj) }}" alt="QR Code" style="width: 80px; height: 80px; margin: 10px auto; display: block;">

                        <div class="signature-name">{{ $masterPeriksa->penanggung_jawab ?? '-' }}</div>
                    </div>
                    
                    <div class="signature">
                        Tgl.Cetak : {{ date('d-m-Y H:i:s') }}<br>
                        Petugas Laboratorium
                        
                        @php
                            $qrTextPetugas = "Petugas Laboratorium\n" .
                                      ($masterPeriksa->nm_petugas ?? '-') . "\n" .
                                      date('d-m-Y', strtotime($masterPeriksa->tgl_periksa));
                        @endphp
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($qrTextPetugas) }}" alt="QR Code" style="width: 80px; height: 80px; margin: 10px auto; display: block;">

                        <div class="signature-name">{{ $masterPeriksa->nm_petugas ?? '-' }}</div>
                    </div>
                </div>
            @else
                <div style="text-align: right; font-size: 10px; color: #999; margin-top: 20px;">Berlanjut ke halaman berikutnya...</div>
            @endif
            <!-- --- END ACTUAL PRINT CONTENT --- -->

        </div>
        @empty
            <div class="document-page size-a4" style="display:flex; justify-content:center; align-items:center; color:#999;">
                Tidak ada data.
            </div>
        @endforelse
    </div>

    <script>
        function changePaperSize() {
            const selector = document.getElementById('paperSizeSelector');
            const docElements = document.querySelectorAll('.document-page');
            const printStyle = document.getElementById('print-page-style');
            
            if (selector.value === 'f4') {
                docElements.forEach(el => {
                    el.classList.remove('size-a4');
                    el.classList.add('size-f4');
                });
                printStyle.innerHTML = '@media print { @page { size: 215mm 330mm portrait; margin: 0; } }';
            } else {
                docElements.forEach(el => {
                    el.classList.remove('size-f4');
                    el.classList.add('size-a4');
                });
                printStyle.innerHTML = '@media print { @page { size: A4 portrait; margin: 0; } }';
            }
        }
    </script>
</body>
</html>
