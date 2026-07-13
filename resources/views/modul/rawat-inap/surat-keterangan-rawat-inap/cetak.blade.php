<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Rawat Inap - {{ $regPeriksa->pasien->nm_pasien ?? $regPeriksa->no_rkm_medis }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* ================================================
           BASE (Screen & Print)
        ================================================ */
        body {
            font-family: Arial, sans-serif;
            font-size: 14.5px; /* Consistent font size with SKL */
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #525659;
        }

        /* ================================================
           TOOLBAR (Screen Only)
        ================================================ */
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            font-family: ui-sans-serif, system-ui, sans-serif;
        }

        .toolbar-group {
            display: flex;
            align-items: center;
            gap: 15px;
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
            border: 1px solid #757b80;
            padding: 6px 16px;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-close:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* ================================================
           PAPER PREVIEW (Screen Only)
        ================================================ */
        .preview-container {
            margin-top: 70px;
            margin-bottom: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }

        .document-page {
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            position: relative;
            box-sizing: border-box;
            padding: 1cm 2cm;
        }

        .size-a4 {
            width: 210mm;
            min-height: 297mm;
        }

        /* ================================================
           DOCUMENT CONTENT
        ================================================ */
        .kop-header {
            display: flex;
            align-items: center;
            border-bottom: 4px double #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .kop-logo {
            width: 85px;
            height: 85px;
            object-fit: contain;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .kop-text {
            flex-grow: 1;
            text-align: center;
        }

        .kop-text h1 {
            margin: 0 0 2px 0;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-text h2 {
            margin: 0 0 2px 0;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-text p {
            margin: 1px 0;
            font-size: 14px;
        }

        .doc-title-block {
            text-align: center;
            margin: 0 0 15px 0;
            line-height: 1.4;
        }

        .doc-title {
            font-size: 17px;
            font-weight: bold;
            text-decoration: underline;
        }

        .doc-nomor {
            font-size: 14.5px;
            font-weight: bold;
            margin-top: 3px;
        }

        .content-paragraph {
            text-align: justify;
            margin: 8px 0;
            line-height: 1.6;
            font-size: 14.5px;
        }

        /* Data table - label:colon:value style */
        .data-list {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0;
            font-size: 14.5px;
        }

        .data-list td {
            padding: 3px 0;
            vertical-align: top;
            line-height: 1.6;
        }

        .data-list .label {
            width: 160px;
        }

        .data-list .colon {
            width: 15px;
            text-align: center;
        }

        .data-list .value {
            font-weight: normal;
        }

        .signature-block {
            margin-top: 20px;
            overflow: hidden;
        }

        .signature-inner {
            float: right;
            text-align: center;
            width: 280px;
            font-size: 14.5px;
        }

        .signature-city-date {
            margin-bottom: 2px;
        }

        .signature-title {
            margin-bottom: 5px;
        }

        .signature-qr img {
            display: block;
            margin: 10px auto;
            width: 80px;
            height: 80px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 5px;
        }

        /* ================================================
           PRINT STYLES
        ================================================ */
        @media print {
            .preview-toolbar {
                display: none !important;
            }

            html,
            body {
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
                padding: 0 !important; /* Let @page handle margins */
                width: 100%;
                min-height: auto;
                page-break-after: always !important;
            }

            @page {
                size: A4 portrait;
                margin: 1.5cm;
            }
        }
    </style>
</head>

<body>
    {{-- TOOLBAR (no-print) --}}
    <div class="preview-toolbar">
        <div class="toolbar-group">
            <svg style="width:20px;height:20px;color:#9aa0a6;flex-shrink:0;" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span style="font-size:14px;font-weight:500;">
                Pratinjau Surat Keterangan Opname &mdash; {{ $regPeriksa->pasien->nm_pasien ?? $regPeriksa->no_rkm_medis }}
            </span>
        </div>
        <div class="toolbar-group">
            <button class="btn-action" onclick="window.print()">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Surat
            </button>
            <button class="btn-close" onclick="window.close()">Tutup</button>
        </div>
    </div>

    {{-- PAPER WRAPPER --}}
    <div class="preview-container">
        <div class="document-page size-a4">
            
            {{-- ===================== KOP SURAT ===================== --}}
            <div class="kop-header">
                @if(!empty($setting['logo']))
                    <img src="data:image/jpeg;base64,{{ base64_encode($setting['logo']) }}" class="kop-logo" alt="Logo RS" onerror="this.style.display='none'">
                @else
                    <div style="width: 85px; height: 85px; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center; font-size: 10px; margin-right: 20px;">LOGO</div>
                @endif

                <div class="kop-text">
                    <h1>{{ $setting['nama_instansi'] ?? 'RUMAH SAKIT IBU DAN ANAK' }}</h1>
                    @if(!empty($setting['nama_instansi2'] ?? $setting['sub_judul'] ?? null))
                        <h2>{{ $setting['nama_instansi2'] ?? $setting['sub_judul'] }}</h2>
                    @endif
                    <p>Sekretariat : {{ $setting['alamat_instansi'] ?? 'Jl. Dupak no.15 A Surabaya' }}</p>
                    <p>
                        Phone : {{ $setting['kontak'] ?? '62.31.5323837-5477277-5477534-5450187' }} 
                        Email: {{ $setting['email'] ?? 'rsiaibil5a@gmail.com' }}
                    </p>
                </div>
            </div>

            {{-- ===================== JUDUL ===================== --}}
            <div class="doc-title-block">
                <div class="doc-title">SURAT KETERANGAN OPNAME</div>
                <div class="doc-nomor">Nomor: {{ $surat->no_surat }}</div>
            </div>

            {{-- ===================== DATA DOKTER ===================== --}}
            <div class="content-paragraph" style="margin-bottom: 4px;">
                Yang bertandatangan dibawah ini:
            </div>
            
            <table class="data-list">
                <tr>
                    <td class="label">Nama</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $regPeriksa->dokter->nm_dokter ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Jabatan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $nm_sps }}</td>
                </tr>
                <tr>
                    <td class="label">Tempat</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $setting['nama_instansi'] ?? 'RSIA IBI' }}, {{ $setting['alamat_instansi'] ?? 'Surabaya' }}</td>
                </tr>
            </table>

            {{-- ===================== DATA PASIEN ===================== --}}
            <div class="content-paragraph" style="margin-top: 15px; margin-bottom: 4px;">
                Menerangkan bahwa :
            </div>
            
            <table class="data-list">
                <tr>
                    <td class="label">Nama</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $regPeriksa->pasien->nm_pasien ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Umur</td>
                    <td class="colon">:</td>
                    <td class="value">{{ isset($regPeriksa->pasien->tgl_lahir) ? \Carbon\Carbon::parse($regPeriksa->pasien->tgl_lahir)->age . ' Tahun' : '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Jenis Kelamin</td>
                    <td class="colon">:</td>
                    <td class="value">
                        {{ isset($regPeriksa->pasien->jk) ? ($regPeriksa->pasien->jk === 'L' ? 'Laki-laki' : 'Perempuan') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Tempat/Tanggal Lahir</td>
                    <td class="colon">:</td>
                    <td class="value">
                        {{ $regPeriksa->pasien->tmp_lahir ?? '-' }}, 
                        {{ isset($regPeriksa->pasien->tgl_lahir) ? \Carbon\Carbon::parse($regPeriksa->pasien->tgl_lahir)->translatedFormat('d F Y') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Pekerjaan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $regPeriksa->pasien->pekerjaan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">No SEP</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $sep->no_sep ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Alamat</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $regPeriksa->pasien->alamat ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Diagnosa</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $diagnosa_awal }}</td>
                </tr>
            </table>

            {{-- ===================== PARAGRAF PENUTUP ===================== --}}
            @php
                \Carbon\Carbon::setLocale('id');
            @endphp
            <div class="content-paragraph" style="margin-top: 15px;">
                Bahwa benar pasien tersebut diatas telah mendapatkan perawatan di {{ $setting['nama_instansi'] ?? 'Rumah Sakit' }} pada tanggal 
                {{ \Carbon\Carbon::parse($surat->tanggalawal)->translatedFormat('d F Y') }} sampai dengan 
                {{ \Carbon\Carbon::parse($surat->tanggalakhir)->translatedFormat('d F Y') }} dengan seluruh biaya tercover/ditanggung 
                {!! strtolower($regPeriksa->penjab->png_jawab ?? '') === 'umum' ? '<strong>Pribadi</strong>' : ($regPeriksa->penjab->png_jawab ?? 'Pasien/Keluarga') !!}.
            </div>
            
            <div class="content-paragraph">
                Demikian Surat Keterangan ini dibuat dengan sebenarnya dan dapat dipergunakan sebagaimana mestinya.
            </div>

            {{-- ===================== TANDA TANGAN ===================== --}}
            @php
                $kotaCetak = $setting['kabupaten'] ?? ($setting['kota'] ?? 'Surabaya');
                $tglFormatted = \Carbon\Carbon::now()->translatedFormat('d F Y');
                $penolongNama = $regPeriksa->dokter->nm_dokter ?? 'Dokter Jaga';
                
                $qrData = "Surat Keterangan Opname - " . ($setting['nama_instansi'] ?? 'Rumah Sakit') . "\n" .
                    "Nomor: " . ($surat->no_surat ?? '-') . "\n" .
                    "Pasien: " . ($regPeriksa->pasien->nm_pasien ?? '-') . "\n" .
                    "Dokter: " . $penolongNama;
            @endphp

            <div class="signature-block">
                <div class="signature-inner">
                    <div class="signature-city-date">{{ $kotaCetak }}, {{ \Carbon\Carbon::parse($surat->tanggalakhir)->translatedFormat('d F Y') }}</div>
                    <div class="signature-title">Dokter yang menerangkan,</div>
                    <div class="signature-qr">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($qrData) }}"
                            alt="QR Code Verifikasi">
                    </div>
                    <div class="signature-name">{{ $penolongNama }}</div>
                </div>
            </div>

        </div>{{-- .document-page --}}
    </div>{{-- .preview-container --}}

</body>
</html>
