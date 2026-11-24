<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Komunikasi SBAR - {{ $regPeriksa->pasien->nm_pasien ?? $regPeriksa->no_rkm_medis }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* ================================================
           BASE (Screen & Print)
        ================================================ */
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
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
            margin-top: 65px;
            margin-bottom: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .document-page {
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            position: relative;
            box-sizing: border-box;
            width: 210mm;
            min-height: 296mm;
            padding: 10mm 15mm;
        }

        /* ================================================
           PRINT STYLES (@media print)
        ================================================ */
        @media print {
            body {
                background-color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .preview-toolbar {
                display: none !important;
            }

            .preview-container {
                margin: 0 !important;
                padding: 0 !important;
                gap: 0 !important;
            }

            .document-page {
                box-shadow: none !important;
                width: 100% !important;
                height: auto !important;
                min-height: auto !important;
                max-height: none !important;
                padding: 6mm 10mm !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
                overflow: visible !important;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }
        }

        /* Table styles */
        table.sbar-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
        }

        table.sbar-table td, table.sbar-table th {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }

        .bordered-box {
            border: 1.5px solid #000;
        }
    </style>
</head>

<body>

    {{-- Screen Viewer Toolbar --}}
    <div class="preview-toolbar">
        <div class="toolbar-group">
            <span class="font-semibold text-sm tracking-wide">Pratinjau Cetak: Lembar Komunikasi SBAR</span>
        </div>
        <div class="toolbar-group">
            <button type="button" onclick="window.print()" class="btn-action">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Cetak Dokumen
            </button>
            <button type="button" onclick="window.close()" class="btn-close">
                Tutup
            </button>
        </div>
    </div>

    {{-- Paper Container --}}
    <div class="preview-container">
        <div class="document-page">

            {{-- 1. TOP METADATA ROW: Tanggal & Jam --}}
            <div class="flex items-center gap-12 mb-2 text-[12px]">
                <div><span class="font-semibold">Tanggal :</span> {{ \Carbon\Carbon::parse($catatan->tanggal)->format('d-m-Y') }}</div>
                <div><span class="font-semibold">Jam :</span> {{ \Carbon\Carbon::parse($catatan->tanggal)->format('H:i') }}</div>
            </div>

            {{-- 2. HEADER BOX: LOGO & JUDUL LEMBAR KOMUNIKASI SBAR --}}
            <div class="bordered-box flex items-center justify-between p-2 mb-2 bg-neutral-50/40">
                <div class="w-14 flex items-center justify-center flex-shrink-0">
                    @if(!empty($setting['logo']))
                        <img src="data:image/jpeg;base64,{{ base64_encode($setting['logo']) }}" class="w-12 h-12 object-contain" alt="Logo" />
                    @else
                        <div class="w-10 h-10 rounded-full border border-gray-400 flex items-center justify-center font-bold text-xs">RS</div>
                    @endif
                </div>
                <div class="flex-1 text-center pr-14">
                    <h1 class="text-lg font-bold uppercase tracking-wider text-black">Lembar Komunikasi SBAR</h1>
                </div>
            </div>

            {{-- 3. TABEL UTAMA SBAR --}}
            <table class="sbar-table text-[11.5px] leading-snug">
                <tbody>
                    {{-- ================= ROW 1: SITUATION (S) ================= --}}
                    <tr>
                        <td class="w-[26%] bg-neutral-100/60 font-semibold text-center p-2">
                            <div class="font-bold text-sm">S</div>
                            <div class="font-bold text-xs tracking-wider">SITUATION</div>
                            <div class="text-[10.5px] font-normal text-left text-neutral-800 mt-2 leading-tight">
                                Apa yang terjadi pada saat ini ?<br>
                                Apa problemnya, kapan terjadinya dan bagaimana parahnya.
                            </div>
                        </td>
                        <td class="p-0">
                            {{-- Sub-row 1: Pelapor & Penerima Laporan --}}
                            <div class="grid grid-cols-2 border-b border-black">
                                <div class="p-1.5 border-r border-black">
                                    <span class="text-neutral-600 text-[11px]">Pelapor (nama & jabatan) :</span><br>
                                    <span class="font-bold text-[12px]">{{ $namaPelapor }} / {{ $jabatanPelapor }}</span>
                                </div>
                                <div class="p-1.5">
                                    <span class="text-neutral-600 text-[11px]">Penerima Laporan :</span><br>
                                    <span class="font-bold text-[12px]">{{ $catatan->dokter->nm_dokter ?? '-' }}</span>
                                </div>
                            </div>
                            {{-- Sub-row 2: Nama Pasien, Umur, Pav/Kamar --}}
                            <div class="grid grid-cols-12 border-b border-black text-[11.5px]">
                                <div class="col-span-5 p-1.5 border-r border-black">
                                    <span class="text-neutral-600">Nama Pasien :</span>
                                    <span class="font-bold uppercase">{{ $regPeriksa->pasien->nm_pasien ?? '-' }}</span>
                                </div>
                                <div class="col-span-3 p-1.5 border-r border-black">
                                    <span class="text-neutral-600">Umur :</span>
                                    <span class="font-bold">{{ $regPeriksa->umurdaftar }} {{ $regPeriksa->sttsumur }}</span>
                                </div>
                                <div class="col-span-4 p-1.5">
                                    <span class="text-neutral-600">Tahun Pav/kamar :</span>
                                    <span class="font-bold">
                                        @if($kamarInap)
                                            {{ $kamarInap->kamar->bangsal->nm_bangsal ?? '-' }} / {{ $kamarInap->kd_kamar }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                            </div>
                            {{-- Sub-row 3: Problem --}}
                            <div class="p-1.5 min-h-[45px]">
                                <span class="text-neutral-600 font-semibold">Problem :</span>
                                <div class="font-medium text-[12px] mt-0.5 whitespace-pre-wrap pl-1 leading-relaxed">{{ trim($catatan->situation) }}</div>
                            </div>
                        </td>
                    </tr>

                    {{-- ================= ROW 2: BACKGROUND (B) ================= --}}
                    <tr>
                        <td class="w-[26%] bg-neutral-100/60 font-semibold text-center p-2">
                            <div class="font-bold text-sm">B</div>
                            <div class="font-bold text-xs tracking-wider">BACKGROUND</div>
                            <div class="text-[10.5px] font-normal text-left text-neutral-800 mt-2 leading-tight">
                                Informasi yang berkaitan / mungkin berkaitan dengan problemnya<br><br>
                                (Ringkas dan penting untuk menerangkan problem yang terjadi atau untuk menentukan tindakan selanjutnya)
                            </div>
                        </td>
                        <td class="p-0">
                            {{-- 1. MRS/dirawat dengan --}}
                            <div class="p-1.5 border-b border-black">
                                <span class="text-neutral-600">MRS / dirawat dengan :</span>
                                <span class="font-medium pl-1 text-[12px]">{{ $catatan->bg_riwayat_penyakit_dahulu ?: '-' }}</span>
                            </div>
                            {{-- 2. Riwayat Penyakit --}}
                            <div class="p-1.5 border-b border-black">
                                <span class="text-neutral-600">Riwayat Penyakit :</span>
                                <span class="font-medium pl-1 text-[12px]">{{ $catatan->bg_riwayat_penyakit ?: '-' }}</span>
                            </div>
                            {{-- 3. Informasi Klinis --}}
                            <div class="p-1.5 border-b border-black">
                                <span class="text-neutral-600">Informasi Klinis :</span>
                                <span class="font-medium pl-1 text-[12px]">{{ $catatan->bg_informasi_klinis ?: ($catatan->background ?: '-') }}</span>
                            </div>
                            {{-- 4. Lab / Pemeriksaan Penunjang lain --}}
                            <div class="p-1.5 border-b border-black">
                                <span class="text-neutral-600">Lab / Pemeriksaan Penunjang lain :</span>
                                <span class="font-medium pl-1 text-[12px]">{{ $catatan->bg_penunjang ?: '-' }}</span>
                            </div>
                            {{-- 5. Riwayat Alergi --}}
                            <div class="p-1.5 border-b border-black">
                                <span class="text-neutral-600">Riwayat Alergi :</span>
                                <span class="font-medium pl-1 text-[12px]">{{ $catatan->bg_alergi ?: '-' }}</span>
                            </div>
                            {{-- 6. Tanda Vital saat ini --}}
                            <div class="p-1.5 border-b border-black">
                                <div class="text-neutral-600 font-semibold mb-0.5">Tanda Vital saat ini :</div>
                                <div class="grid grid-cols-2 gap-x-2 gap-y-0.5 pl-2 text-[11.5px]">
                                    <div>
                                        <span class="text-neutral-600">Kesadaran :</span>
                                        <span class="font-bold">{{ $catatan->bg_kesadaran ?: '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-neutral-600">TD :</span>
                                        <span class="font-bold">{{ $catatan->bg_tensi ? $catatan->bg_tensi . ' mmHg' : '-' }}</span>
                                    </div>
                                    <div class="col-span-2 flex flex-wrap gap-x-4 gap-y-0.5 mt-0.5">
                                        <div><span class="text-neutral-600">Nadi :</span> <span class="font-bold">{{ $catatan->bg_nadi ? $catatan->bg_nadi . ' x/mnt' : '-' }}</span></div>
                                        <div><span class="text-neutral-600">Resp :</span> <span class="font-bold">{{ $catatan->bg_respirasi ? $catatan->bg_respirasi . ' /menit' : '-' }}</span></div>
                                        <div><span class="text-neutral-600">Temp :</span> <span class="font-bold">{{ $catatan->bg_suhu ? $catatan->bg_suhu . ' °C' : '-' }}</span></div>
                                        <div><span class="text-neutral-600">SpO2 :</span> <span class="font-bold">{{ $catatan->bg_spo2 ? $catatan->bg_spo2 . ' %' : '-' }}</span></div>
                                    </div>
                                </div>
                            </div>
                            {{-- 7. Terapi saat ini --}}
                            <div class="p-1.5">
                                <span class="text-neutral-600">Terapi saat ini :</span>
                                <span class="font-medium pl-1 text-[12px]">{{ $catatan->bg_terapi ?: '-' }}</span>
                            </div>
                        </td>
                    </tr>

                    {{-- ================= ROW 3: ASSESSMENT (A) ================= --}}
                    <tr>
                        <td class="w-[26%] bg-neutral-100/60 font-semibold text-center p-2">
                            <div class="font-bold text-sm">A</div>
                            <div class="font-bold text-xs tracking-wider">Assessment</div>
                        </td>
                        <td class="p-1.5 min-h-[45px]">
                            <span class="text-neutral-600 font-semibold">Problem ini menurut Anda disebabkan :</span>
                            <div class="font-medium text-[12px] mt-1 whitespace-pre-wrap pl-1 leading-relaxed">{{ trim($catatan->assessment) }}</div>
                        </td>
                    </tr>

                    {{-- ================= ROW 4: RECOMMENDATION (R) ================= --}}
                    <tr>
                        <td class="w-[26%] bg-neutral-100/60 font-semibold text-center p-2">
                            <div class="font-bold text-sm">R</div>
                            <div class="font-bold text-xs tracking-wider">Recommendation</div>
                            <div class="text-[10.5px] font-normal text-left text-neutral-800 mt-2 leading-tight">
                                Apa yang dapat dilakukan untuk mengatasi problem?
                            </div>
                        </td>
                        <td class="p-1.5 min-h-[55px]">
                            <span class="text-neutral-600 font-semibold">Usulan & mohon petunjuk :</span>
                            <div class="text-[10.5px] text-neutral-500 italic">Pemeriksaan / tindakan lebih lanjut / konsul / pindah rawat (missal : ke ICU)</div>
                            <div class="font-medium text-[12px] mt-1 whitespace-pre-wrap pl-1 leading-relaxed">{{ trim($catatan->recommendation) }}</div>
                        </td>
                    </tr>

                    {{-- ================= ROW 5: INSTRUKSI / ANJURAN (ADVICE) & PARAF ================= --}}
                    <tr>
                        <td colspan="2" class="p-0">
                            <div class="grid grid-cols-12 min-h-[110px]">
                                {{-- Kiri: Instruksi / Anjuran Dokter (Advice) --}}
                                <div class="col-span-7 p-2 border-r border-black flex flex-col justify-between">
                                    <div>
                                        <div class="font-bold text-[11.5px] mb-1">Instruksi / Anjuran dari yang menerima laporan :**</div>
                                        <div class="text-[12px] whitespace-pre-wrap leading-relaxed pl-1 text-neutral-900">
                                            {{ trim($catatan->advice) ?: '-' }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Kanan: Paraf Dokter & Paraf Pelapor --}}
                                <div class="col-span-5 p-2 flex flex-col justify-between bg-neutral-50/20">
                                    <div class="grid grid-cols-2 gap-2 text-center text-[11px]">
                                        {{-- Paraf Dokter --}}
                                        <div>
                                            <div class="font-semibold text-neutral-800 mb-1">Paraf Dokter,</div>
                                            <div class="my-1 flex items-center justify-center">
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=65x65&data=Diverifikasi%20oleh%20DPJP:%20{{ urlencode($catatan->dokter->nm_dokter ?? '-') }}%20Tgl:%20{{ urlencode($catatan->tanggal) }}" class="w-14 h-14" alt="QR Dokter" />
                                            </div>
                                            <div class="font-bold text-[11px] underline mt-1 truncate" title="{{ $catatan->dokter->nm_dokter ?? '-' }}">
                                                {{ $catatan->dokter->nm_dokter ?? '-' }}
                                            </div>
                                        </div>

                                        {{-- Paraf Pelapor --}}
                                        <div>
                                            <div class="font-semibold text-neutral-800 mb-1">Paraf Pelapor,</div>
                                            <div class="my-1 flex items-center justify-center">
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=65x65&data=Dilaporkan%20oleh:%20{{ urlencode($namaPelapor) }}%20Tgl:%20{{ urlencode($catatan->tanggal) }}" class="w-14 h-14" alt="QR Pelapor" />
                                            </div>
                                            <div class="font-bold text-[11px] underline mt-1 truncate" title="{{ $namaPelapor }}">
                                                {{ $namaPelapor }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- 4. FOOTER NOTE --}}
            <div class="text-[10px] text-neutral-800 italic mt-1.5 leading-tight">
                **Catatan : ditulis (writing down) ; dibaca kembali (read back) ; diulang kembali (repeat back) ; dikonfirmasi oleh pemberi order (check back)
            </div>

        </div>
    </div>

</body>

</html>
