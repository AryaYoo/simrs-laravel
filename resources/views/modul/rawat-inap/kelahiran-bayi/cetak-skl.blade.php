<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak SKL - {{ $bayi->pasien->nm_pasien ?? $bayi->no_rkm_medis }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* ================================================
           BASE (Screen & Print)
        ================================================ */
        body {
            font-family: Arial, sans-serif;
            font-size: 14.5px;
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
            padding: 1.5cm 1.8cm;
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
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .kop-logo {
            width: 75px;
            height: 75px;
            object-fit: contain;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .kop-logo-placeholder {
            width: 75px;
            height: 75px;
            border: 1px solid #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 9px;
            color: #999;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .kop-text {
            flex-grow: 1;
            text-align: center;
        }

        .kop-text h1 {
            margin: 0 0 2px 0;
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .kop-text h2 {
            margin: 0 0 3px 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-text p {
            margin: 1px 0;
            font-size: 12px;
        }

        .doc-title-block {
            text-align: center;
            margin: 16px 0 8px 0;
        }

        .doc-title {
            font-size: 17px;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .doc-nomor {
            font-size: 13px;
            margin-top: 3px;
        }

        .opening-paragraph {
            text-align: justify;
            margin: 16px 0 12px 0;
            line-height: 1.7;
            font-size: 14.5px;
        }

        /* Data table - label:colon:value style */
        .data-list {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0;
        }

        .data-list td {
            padding: 2.5px 4px;
            vertical-align: top;
        }

        .data-list .label {
            width: 160px;
            font-size: 14.5px;
        }

        .data-list .colon {
            width: 12px;
            text-align: center;
        }

        .data-list .value {
            font-size: 14.5px;
        }

        .section-label {
            font-weight: bold;
            margin: 10px 0 4px 0;
            font-size: 14.5px;
        }

        .signature-block {
            margin-top: 30px;
            overflow: hidden;
            /* clearfix */
        }

        .signature-inner {
            float: right;
            text-align: center;
            width: 220px;
        }

        .signature-city-date {
            font-size: 14.5px;
            margin-bottom: 2px;
        }

        .signature-title {
            font-size: 14.5px;
            margin-bottom: 4px;
        }

        .signature-qr img {
            display: block;
            margin: 6px auto;
            width: 90px;
            height: 90px;
        }

        .signature-name {
            font-size: 14.5px;
            font-weight: bold;
            margin-top: 2px;
        }

        /* Page number watermark */
        .page-number {
            position: absolute;
            bottom: 1cm;
            left: 1.8cm;
            font-size: 9px;
            color: #999;
            opacity: 0.7;
        }

        /* ================================================
           PRINT STYLES (SOP #7)
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
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                page-break-after: always !important;
                break-after: page !important;
            }

            .document-page:last-child,
            .document-page:last-of-type {
                page-break-after: auto !important;
                break-after: auto !important;
            }

            /* SOP: shrink 1mm to prevent fractional spill-over blank page */
            .size-a4 {
                height: 296mm !important;
                min-height: 296mm !important;
                max-height: 296mm !important;
                overflow: hidden !important;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }
        }

        /* Hide background option styling */
        .no-bg {
            background-image: none !important;
        }

        /* Toggle Switch styling */
        .toggle-container {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
            color: #e8eaed;
            font-size: 13px;
        }

        .toggle-container:hover {
            color: white;
        }

        .toggle-container input {
            display: none;
        }

        .toggle-switch {
            position: relative;
            width: 34px;
            height: 18px;
            background-color: #5f6368;
            border-radius: 9px;
            transition: background-color 0.2s;
        }

        .toggle-switch::after {
            content: "";
            position: absolute;
            top: 2px;
            left: 2px;
            width: 14px;
            height: 14px;
            background-color: white;
            border-radius: 50%;
            transition: transform 0.2s;
        }

        .toggle-container input:checked+.toggle-switch {
            background-color: #8ab4f8;
        }

        .toggle-container input:checked+.toggle-switch::after {
            transform: translateX(16px);
        }

        .btn-secondary {
            background-color: transparent;
            color: #8ab4f8;
            border: 1px solid #8ab4f8;
            padding: 6px 16px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background-color: rgba(138, 180, 248, 0.1);
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
                Pratinjau SKL &mdash; {{ $bayi->pasien->nm_pasien ?? $bayi->no_rkm_medis }}
            </span>
        </div>
        <div class="toolbar-group">
            @if(!empty($setting['wallpaper']))
                <label class="toggle-container">
                    <input type="checkbox" id="toggle-bg" checked onchange="toggleBg(this.checked)">
                    <div class="toggle-switch"></div>
                    <span>Sembunyikan Background</span>
                </label>
            @endif
            <button class="btn-action" onclick="window.print()">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak SKL
            </button>
            <button class="btn-close" onclick="window.close()">Tutup</button>
        </div>
    </div>

    {{-- PAPER WRAPPER --}}
    <div class="preview-container">
        <div class="document-page size-a4 no-bg" id="documentPage" @if(!empty($setting['wallpaper']))
            style="background-image: url('data:image/jpeg;base64,{{ base64_encode($setting['wallpaper']) }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"
        @endif>
            {{-- Page Number (SOP #7 item 4) --}}
            <div class="page-number">Tgl. Cetak: {{ date('d/m/Y H.i') }}</div>

            {{-- ===================== KOP SURAT ===================== --}}
            <div class="kop-header">
                @if(!empty($setting['logo']))
                    <img src="data:image/jpeg;base64,{{ base64_encode($setting['logo']) }}" class="kop-logo" alt="Logo RS">
                @else
                    <div class="kop-logo-placeholder">LOGO</div>
                @endif

                <div class="kop-text">
                    <h1>{{ $setting['nama_instansi'] ?? 'RUMAH SAKIT' }}</h1>
                    @if(!empty($setting['nama_instansi2'] ?? $setting['sub_judul'] ?? null))
                        <h2>{{ $setting['nama_instansi2'] ?? $setting['sub_judul'] }}</h2>
                    @endif
                    <p>Sekretariat : {{ $setting['alamat_instansi'] ?? '-' }}</p>
                    <p>
                        Phone : {{ $setting['kontak'] ?? '-' }}
                        @if(!empty($setting['email']))
                            &nbsp; Email: {{ $setting['email'] }}
                        @endif
                    </p>
                </div>
            </div>

            {{-- ===================== JUDUL ===================== --}}
            <div class="doc-title-block">
                <div class="doc-title">Surat Keterangan Lahir</div>
                <div class="doc-nomor">Nomor : {{ $bayi->no_skl ?? '-' }}</div>
            </div>

            {{-- ===================== PARAGRAF PEMBUKA ===================== --}}
            @php
                $penolongNama = $bayi->pegawai->nama ?? '-';
                $penolongJabatan = $bayi->pegawai->jbtn ?? '';
                $jkBayi = $bayi->pasien->jk ?? 'L';
                $jkLabel = $jkBayi === 'P' ? 'PEREMPUAN' : 'LAKI-LAKI';
                $tglLahir = $bayi->pasien?->tgl_lahir
                    ? \Carbon\Carbon::parse($bayi->pasien->tgl_lahir)
                    : null;
                $hariLahir = $tglLahir
                    ? ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$tglLahir->dayOfWeek]
                    : '-';
                $tglFormatted = $tglLahir
                    ? $tglLahir->isoFormat('D MMMM YYYY')
                    : '-';
                $kotaCetak = $setting['kabupaten'] ?? ($setting['kota'] ?? 'Surabaya');
            @endphp

            <p class="opening-paragraph">
                Yang bertanda tangan di bawah ini,
                <strong>{{ $penolongNama }}{{ $penolongJabatan ? ', ' . $penolongJabatan : '' }}</strong>,
                menerangkan dengan sesungguhnya, bahwa telah lahir seorang bayi dengan jenis kelamin
                <strong>{{ $jkLabel }}</strong>:
            </p>

            {{-- ===================== DATA BAYI ===================== --}}
            <table class="data-list">
                <tr>
                    <td class="label">No. Rekam Medik</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $bayi->no_rkm_medis }}</td>
                </tr>
                <tr>
                    <td class="label">Nama Bayi</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $bayi->pasien->nm_pasien ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Nama Ibu</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $bayi->nm_ibu ?? $pasienRaw->nm_ibu ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Nama Ayah</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $bayi->nama_ayah ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">No. KTP Ibu</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $pasienRaw->no_ktp ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Pekerjaan Ibu</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $pasienRaw->pekerjaan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Pada Tanggal</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $tglFormatted }}</td>
                </tr>
                <tr>
                    <td class="label">Hari</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $hariLahir }}</td>
                </tr>
                <tr>
                    <td class="label">Pukul</td>
                    <td class="colon">:</td>
                    <td class="value">
                        @if($bayi->jam_lahir)
                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $bayi->jam_lahir)->format('H.i') }} WIB
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Alamat</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $bayi->alamat ?? $pasienRaw->alamat ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Anak Ke -</td>
                    <td class="colon">:</td>
                    <td class="value">
                        @php
                            $anakke = intval($bayi->anakke ?? 0);
                            $terbilang = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh'];
                            $labelAnakke = $anakke >= 1 && $anakke <= 10 ? $anakke . '(' . $terbilang[$anakke] . ')' : ($bayi->anakke ?? '-');
                        @endphp
                        {{ $labelAnakke }}
                    </td>
                </tr>

            </table>

            {{-- ===================== KETERANGAN FISIK ===================== --}}
            <div class="section-label">Keterangan Fisik</div>
            <table class="data-list">
                <tr>
                    <td class="label">Berat Badan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $bayi->berat_badan ? $bayi->berat_badan . ' gram' : '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Panjang Badan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $bayi->panjang_badan ? $bayi->panjang_badan . ' cm' : '-' }}</td>
                </tr>
            </table>

            {{-- ===================== TANDA TANGAN (SOP #7 item 5) ===================== --}}
            @php
                $qrData = "SKL - " . ($setting['nama_instansi'] ?? 'Rumah Sakit') . "\n" .
                    "Nomor: " . ($bayi->no_skl ?? '-') . "\n" .
                    "Bayi: " . ($bayi->pasien->nm_pasien ?? '-') . " | RM: " . $bayi->no_rkm_medis . "\n" .
                    "Penolong: " . $penolongNama . "\n" .
                    "Tanggal: " . $tglFormatted;
            @endphp

            <div class="signature-block">
                <div class="signature-inner">
                    <div class="signature-city-date">{{ $kotaCetak }}, {{ $tglFormatted }}</div>
                    <div class="signature-title">Dokter Yang Menangani,</div>
                    <div class="signature-qr">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($qrData) }}"
                            alt="QR Code Verifikasi">
                    </div>
                    <div class="signature-name">{{ $penolongNama }}</div>
                </div>
            </div>

        </div>{{-- .document-page --}}
    </div>{{-- .preview-container --}}

    <script>
        function toggleBg(checked) {
            const page = document.getElementById('documentPage');
            if (page) {
                if (checked) {
                    page.classList.add('no-bg');
                } else {
                    page.classList.remove('no-bg');
                }
            }
        }

        function printWithoutBg() {
            const page = document.getElementById('documentPage');
            if (page) {
                page.classList.add('no-bg');
            }
            window.print();
        }

        window.onafterprint = function () {
            const checkbox = document.getElementById('toggle-bg');
            if (checkbox && !checkbox.checked) {
                const page = document.getElementById('documentPage');
                if (page) {
                    page.classList.remove('no-bg');
                }
            }
        };
    </script>
</body>

</html>