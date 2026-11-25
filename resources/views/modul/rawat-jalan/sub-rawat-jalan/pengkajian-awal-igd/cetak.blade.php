<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian Awal Keperawatan IGD - {{ $regPeriksa->pasien->nm_pasien ?? $regPeriksa->no_rkm_medis }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* ================================================
           BASE STYLES
        ================================================ */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #525659;
            line-height: 1.25;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
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
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .toolbar-group {
            display: flex;
            align-items: center;
            gap: 12px;
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
           CONTAINER & PAPER
        ================================================ */
        .preview-container {
            margin-top: 60px;
            margin-bottom: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .document-page {
            background-color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            position: relative;
            box-sizing: border-box;
            width: 210mm;
            min-height: 297mm;
            padding: 6mm 10mm 8mm 10mm;
            color: #000;
        }

        /* ================================================
           TABLE & BORDER UTILITIES
        ================================================ */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        .border-box {
            border: 1px solid #000;
        }

        .border-b-black {
            border-bottom: 1px solid #000;
        }

        .border-t-black {
            border-top: 1px solid #000;
        }

        .border-r-black {
            border-right: 1px solid #000;
        }

        .border-l-black {
            border-left: 1px solid #000;
        }

        /* ================================================
           PRINT MEDIA
        ================================================ */
        @media print {
            body {
                background-color: white !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .preview-toolbar {
                display: none !important;
            }

            .preview-container {
                margin: 0 !important;
                padding: 0 !important;
            }

            .document-page {
                box-shadow: none !important;
                width: 100% !important;
                height: auto !important;
                min-height: auto !important;
                padding: 5mm 8mm !important;
                margin: 0 !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>

<body>

    {{-- Screen Preview Toolbar --}}
    <div class="preview-toolbar">
        <div class="toolbar-group">
            <span class="font-semibold text-sm tracking-wide">Pratinjau Cetak: Penilaian Awal Keperawatan Gawat Darurat</span>
        </div>
        <div class="toolbar-group">
            <button class="btn-action" onclick="window.print()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Cetak Dokumen</span>
            </button>
            <button class="btn-close" onclick="window.close()">Tutup</button>
        </div>
    </div>

    {{-- Document Container --}}
    <div class="preview-container">
        <div class="document-page">

            {{-- 1. KOP SURAT --}}
            <table style="width: 100%; margin-bottom: 2px;">
                <tr>
                    <td style="width: 70px; vertical-align: middle; text-align: left;">
                        @if (!empty($setting['logo']))
                            <img src="data:image/jpeg;base64,{{ base64_encode($setting['logo']) }}" style="width: 58px; height: 58px; object-fit: contain;" alt="Logo RS">
                        @else
                            <div style="width: 58px; height: 58px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; font-size: 8px;">LOGO</div>
                        @endif
                    </td>
                    <td style="text-align: center; vertical-align: middle; padding-right: 70px;">
                        <div style="font-size: 12pt; font-weight: bold; text-transform: none; letter-spacing: 0.2px;">
                            {{ $setting['nama_instansi'] ?? 'Rumah Sakit Ibu dan Anak IBI Surabaya' }}
                        </div>
                        <div style="font-size: 8pt; margin-top: 1px;">
                            {{ $setting['alamat_instansi'] ?? 'Jl. Dupak No. 15A, Kelurahan Gundih, Kecamatan Bubutan' }}{{ !empty($setting['kabupaten']) ? ', ' . $setting['kabupaten'] : '' }}{{ !empty($setting['propinsi']) ? ', ' . $setting['propinsi'] : '' }}
                        </div>
                        @if(!empty($setting['kontak']))
                            <div style="font-size: 8pt; margin-top: 1px;">
                                {{ $setting['kontak'] }}
                            </div>
                        @endif
                        @if(!empty($setting['email']))
                            <div style="font-size: 8pt; margin-top: 1px;">
                                E-mail : {{ $setting['email'] }}
                            </div>
                        @endif
                    </td>
                </tr>
            </table>

            {{-- Double line separator --}}
            <div style="border-bottom: 2px solid #000; margin-top: 2px; margin-bottom: 1px;"></div>
            <div style="border-bottom: 0.8px solid #000; margin-bottom: 4px;"></div>

            {{-- 2. TITLE BAR --}}
            <div style="background-color: #F0F0DC; border: 1px solid #000; text-align: center; font-weight: bold; font-size: 9pt; padding: 2.5px 0; letter-spacing: 0.3px;">
                PENILAIAN AWAL KEPERAWATAN GAWAT DARURAT
            </div>

            {{-- 3. DEMOGRAFI PASIEN --}}
            <table class="border-box" style="border-top: none; font-size: 8pt;">
                <tr class="border-b-black">
                    <td style="width: 36%; padding: 2px 5px;" class="border-r-black">
                        No. RM &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $regPeriksa->no_rkm_medis }}
                    </td>
                    <td style="width: 30%; padding: 2px 5px;" class="border-r-black">
                        Jenis Kelamin &nbsp;: {{ $regPeriksa->pasien->jk === 'L' ? 'Laki-Laki' : 'Perempuan' }}
                    </td>
                    <td style="width: 34%; padding: 2px 5px;">
                        Tanggal Kunjungan &nbsp;: {{ \Carbon\Carbon::parse($regPeriksa->tgl_registrasi)->format('d/m/Y') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 2px 5px;" class="border-r-black">
                        Nama Pasien &nbsp;: {{ $regPeriksa->pasien->nm_pasien }}
                    </td>
                    <td style="padding: 2px 5px;" class="border-r-black">
                        Tanggal Lahir &nbsp;&nbsp;: {{ $regPeriksa->pasien->tgl_lahir ? \Carbon\Carbon::parse($regPeriksa->pasien->tgl_lahir)->format('d/m/Y') : '-' }}
                    </td>
                    <td style="padding: 2px 5px;">
                        Informasi didapat dari &nbsp;: {{ $pengkajian->informasi ?: 'Autoanamnesis' }}
                    </td>
                </tr>
            </table>

            {{-- 4. SECTION I: RIWAYAT KESEHATAN PASIEN --}}
            <div class="border-box" style="border-top: none; font-size: 8pt;">
                <div style="padding: 2px 5px; font-weight: bold;">
                    I. RIWAYAT KESEHATAN PASIEN
                </div>
                <div style="padding: 1px 5px 3px 5px;">
                    Riwayat Penyakit Sekarang : {{ $pengkajian->keluhan_utama ?: '-' }}
                </div>
                <table class="border-t-black" style="font-size: 8pt;">
                    <tr>
                        <td style="width: 50%; padding: 2px 5px;" class="border-r-black">
                            Riwayat Penyakit Dahulu : {{ $pengkajian->rpd ?: '-' }}
                        </td>
                        <td style="width: 50%; padding: 2px 5px;">
                            Riwayat Penggunaan Obat : {{ $pengkajian->rpo ?: '-' }}
                        </td>
                    </tr>
                </table>
                <div class="border-t-black" style="padding: 2px 5px;">
                    Status Kehamilan : {{ $pengkajian->status_kehamilan ?: 'Tidak Hamil' }}, Gravida : {{ $pengkajian->gravida ?: '-' }}, Para : {{ $pengkajian->para ?: '-' }}, Abortus : {{ $pengkajian->abortus ?: '-' }}, HPHT : {{ $pengkajian->hpht ?: '-' }}
                </div>
            </div>

            {{-- 5. SECTION II: PEMERIKSAAN FISIK --}}
            <div class="border-box" style="border-top: none; font-size: 8pt;">
                <div style="padding: 2px 5px; font-weight: bold;">
                    II. PEMERIKSAAN FISIK
                </div>
                <table style="font-size: 8pt; width: 100%;">
                    <tr>
                        <td style="width: 32%; padding: 1px 5px; vertical-align: top;">
                            <div>Tekanan Intrakranial : {{ $pengkajian->tekanan ?: 'TAK' }}</div>
                            <div>Pupil &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $pengkajian->pupil ?: 'Normal' }}</div>
                            <div>Edema &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $pengkajian->edema ?: 'Tidak Ada' }}</div>
                        </td>
                        <td style="width: 32%; padding: 1px 5px; vertical-align: top;">
                            <div>Integumen &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $pengkajian->integumen ?: 'TAK' }}</div>
                            <div>Turgor Kulit &nbsp;: {{ $pengkajian->turgor ?: 'Baik' }}</div>
                            <div>Mukosa Mulut : {{ $pengkajian->mukosa ?: 'Lembab' }}</div>
                        </td>
                        <td style="width: 36%; padding: 1px 5px; vertical-align: top;">
                            <div>Perdarahan : {{ $pengkajian->perdarahan ?: 'Tidak Ada' }}, Jumlah : {{ (!empty($pengkajian->jumlah_perdarahan) && $pengkajian->jumlah_perdarahan !== '-') ? $pengkajian->jumlah_perdarahan . ' cc' : '-' }}, Warna : {{ $pengkajian->warna_perdarahan ?: '-' }}</div>
                            <div>Intoksikasi &nbsp;: {{ $pengkajian->intoksikasi ?: 'Tidak Ada' }}</div>
                            <div>Neurosensorik / Muskuloskeletal &nbsp;: {{ $pengkajian->neurosensorik ?: 'TAK' }}</div>
                        </td>
                    </tr>
                </table>
                <div style="padding: 2px 5px 3px 5px; line-height: 1.35;">
                    <div>Eliminasi &nbsp;: BAB : Frekuensi : {{ $pengkajian->bab ?: '-' }}x / {{ $pengkajian->xbab ?: '' }}, Konsistensi : {{ $pengkajian->kbab ?: '-' }}, Warna : {{ $pengkajian->wbab ?: '-' }}</div>
                    <div style="padding-left: 56px;">BAK : Frekuensi : {{ $pengkajian->bak ?: '-' }} x / {{ $pengkajian->xbak ?: '' }}, Warna : {{ $pengkajian->wbak ?: '-' }}, Lain-lain : {{ $pengkajian->lbak ?: '-' }}</div>
                </div>
            </div>

            {{-- 6. SECTION III: RIWAYAT PSIKOLOGIS – SOSIAL – EKONOMI – BUDAYA – SPIRITUAL --}}
            @php
                $bahasaVal = $regPeriksa->pasien->bahasa->nama_bahasa ?? null;
                $bahasaText = ($bahasaVal && !in_array(strtolower($bahasaVal), ['-', '1'])) ? $bahasaVal : 'INDONESIA';
            @endphp
            <div class="border-box" style="border-top: none; font-size: 8pt; padding: 2px 5px 4px 5px;">
                <div style="font-weight: bold; margin-bottom: 1px;">
                    III. RIWAYAT PSIKOLOGIS – SOSIAL – EKONOMI – BUDAYA – SPIRITUAL
                </div>
                <div style="text-align: justify; line-height: 1.3;">
                    <span>a. Kondisi psikologis : {{ $pengkajian->psikologis ?: 'Tidak Ada Masalah' }}</span>&nbsp;&nbsp;&nbsp;
                    <span>b. Gangguan Jiwa di masa lalu : {{ $pengkajian->jiwa ?: 'Tidak' }}</span>&nbsp;&nbsp;&nbsp;
                    <span>c. Adakah perilaku : {{ $pengkajian->perilaku ?: '-' }}, Dilaporkan Ke : {{ $pengkajian->dilaporkan ?: '-' }}, Sebutkan : {{ $pengkajian->sebutkan ?: '-' }}</span>&nbsp;&nbsp;&nbsp;
                    <span>d. Hubungan pasien dengan anggota keluarga : {{ $pengkajian->hubungan ?: 'Harmonis' }}</span>&nbsp;&nbsp;&nbsp;
                    <span>e. Status pernikahan : {{ (!empty($regPeriksa->pasien->stts_nikah) && $regPeriksa->pasien->stts_nikah !== '-') ? $regPeriksa->pasien->stts_nikah : 'BELUM MENIKAH' }}</span>&nbsp;&nbsp;&nbsp;
                    <span>f. Tinggal dengan : {{ $pengkajian->tinggal_dengan ?: 'Sendiri' }}, {{ $pengkajian->ket_tinggal ?: '-' }}</span>&nbsp;&nbsp;&nbsp;
                    <span>g. Pekerjaan : {{ (!empty($regPeriksa->pasien->pekerjaan) && $regPeriksa->pasien->pekerjaan !== '-') ? $regPeriksa->pasien->pekerjaan : 'BELUM BEKERJA' }}</span>&nbsp;&nbsp;&nbsp;
                    <span>h. Pembayaran : {{ $regPeriksa->penjab->png_jawab ?? 'Umum' }}</span>&nbsp;&nbsp;&nbsp;
                    <span>i. Bahasa sehari-hari : {{ $bahasaText }}</span>&nbsp;&nbsp;&nbsp;
                    <span>j. Agama : {{ (!empty($regPeriksa->pasien->agama) && $regPeriksa->pasien->agama !== '-') ? $regPeriksa->pasien->agama : 'ISLAM' }}</span>&nbsp;&nbsp;&nbsp;
                    <span>k. Nilai-nilai kepercayaan/budaya yang perlu diperhatikan : {{ $pengkajian->budaya ?: 'Tidak Ada' }}, {{ $pengkajian->ket_budaya ?: '-' }}</span>&nbsp;&nbsp;&nbsp;
                    <span>l. Pendidikan pasien : {{ $regPeriksa->pasien->pnd ?: '-' }}</span>&nbsp;&nbsp;&nbsp;
                    <span>m. Pendidikan penanggung jawab : {{ $pengkajian->pendidikan_pj ?: '-' }}, {{ $pengkajian->ket_pendidikan_pj ?: '-' }}</span>&nbsp;&nbsp;&nbsp;
                    <span>n. Edukasi diberikan kepada : {{ $pengkajian->edukasi ?: 'Pasien' }}, {{ $pengkajian->ket_edukasi ?: '-' }}</span>
                </div>
            </div>

            {{-- 7. SECTION IV: PENGKAJIAN FUNGSI --}}
            @php
                $cacatVal = $regPeriksa->pasien->cacatFisik->nama_cacat ?? null;
                $cacatText = ($cacatVal && !in_array(strtolower($cacatVal), ['-', 'tidak ada', '1'])) ? $cacatVal : 'TIDAK ADA';
            @endphp
            <div class="border-box" style="border-top: none; font-size: 8pt; padding: 2px 5px;">
                <div style="font-weight: bold; margin-bottom: 1px;">
                    IV. PENGKAJIAN FUNGSI
                </div>
                <div>
                    <span>a. Kemampuan aktifitas sehari-hari : {{ $pengkajian->kemampuan ?: 'Mandiri' }}</span>&nbsp;&nbsp;&nbsp;
                    <span>b. Aktifitas : {{ $pengkajian->aktifitas ?: 'Tirah Baring' }}</span>&nbsp;&nbsp;&nbsp;
                    <span>c. Alat bantu : {{ $pengkajian->alat_bantu ?: 'Tidak' }}, {{ $pengkajian->ket_bantu ?: '-' }}</span>&nbsp;&nbsp;&nbsp;
                    <span>d. Cacat tubuh : {{ $cacatText }}</span>
                </div>
            </div>

            {{-- 8. SECTION V: SKALA NYERI --}}
            @php
                $nyeriImgPath = public_path('img/skala-nyeri-print.png');
                $nyeriBase64 = file_exists($nyeriImgPath) ? base64_encode(file_get_contents($nyeriImgPath)) : '';
            @endphp
            <div class="border-box" style="border-top: none; font-size: 8pt;">
                <div style="padding: 2px 5px; font-weight: bold;">
                    V. SKALA NYERI
                </div>
                <table style="font-size: 8pt; width: 100%;">
                    <tr>
                        <td style="width: 44%; padding: 2px 6px; text-align: center; vertical-align: middle;">
                            @if(!empty($nyeriBase64))
                                <img src="data:image/png;base64,{{ $nyeriBase64 }}" alt="Wong Baker Faces Pain Scale" style="width: 100%; max-width: 250px; height: auto; display: block; margin: 0 auto;">
                            @else
                                <img src="{{ asset('img/skala-nyeri.png') }}" alt="Wong Baker Faces Pain Scale" style="width: 100%; max-width: 250px; height: auto; display: block; margin: 0 auto;">
                            @endif
                        </td>
                        <td style="width: 56%; padding: 3px 6px; vertical-align: top;" class="border-l-black">
                            <div style="margin-bottom: 2px;">{{ $pengkajian->nyeri ?: 'Tidak Ada Nyeri' }}, Penyebab : {{ $pengkajian->provokes ?: 'Proses Penyakit' }} {{ $pengkajian->ket_provokes ?: '-' }}</div>
                            <div style="margin-bottom: 2px;">Kualitas : {{ $pengkajian->quality ?: 'Seperti Tertusuk' }} {{ $pengkajian->ket_quality ?: '-' }}</div>
                            <div style="margin-bottom: 2px;">Wilayah : Lokasi {{ $pengkajian->lokasi ?: '-' }}, Menyebar : {{ $pengkajian->menyebar ?: 'Tidak' }}</div>
                            <div>Severity : Skala Nyeri {{ $pengkajian->skala_nyeri ?? '0' }}, Waktu / Durasi : {{ $pengkajian->durasi ?: '-' }} Menit</div>
                        </td>
                    </tr>
                </table>
                <table class="border-t-black" style="font-size: 8pt; width: 100%;">
                    <tr>
                        <td style="width: 55%; padding: 2px 5px;">
                            Nyeri hilang bila : {{ $pengkajian->nyeri_hilang ?: 'Istirahat' }} {{ $pengkajian->ket_nyeri ?: '-' }}
                        </td>
                        <td style="width: 45%; padding: 2px 5px;">
                            Diberitahukan pada dokter ? {{ $pengkajian->pada_dokter ?: 'Tidak' }} {{ $pengkajian->ket_dokter ?: '-' }}
                        </td>
                    </tr>
                </table>
            </div>

            {{-- 9. SECTION VI: PENILAIAN RESIKO JATUH (GET UP AND GO) --}}
            <div class="border-box" style="border-top: none; font-size: 8pt;">
                <div style="padding: 2px 5px; font-weight: bold;">
                    VI. PENILAIAN RESIKO JATUH (GET UP AND GO)
                </div>
                <table style="font-size: 8pt; width: 100%; border-collapse: collapse;">
                    <tr class="border-t-black">
                        <th style="width: 28px; text-align: center; font-weight: normal; padding: 2px 4px;" class="border-r-black">No</th>
                        <th style="text-align: left; padding: 2px 5px; font-weight: normal;" class="border-r-black">Hal-hal yang diobservasi</th>
                        <th style="width: 65px; text-align: center; font-weight: normal; padding: 2px 4px;" class="border-r-black">Ya/Tidak</th>
                        <td rowspan="3" style="width: 175px; vertical-align: top; padding: 4px 6px;">
                            <div style="font-weight: normal; line-height: 1.25;">
                                Hasil : {{ $pengkajian->hasil ?: 'Tidak beresiko (tidak ditemukan a dan b)' }}
                            </div>
                            <div style="margin-top: 10px;">
                                Dilaporkan ke dokter ? {{ $pengkajian->lapor ?: 'Tidak' }} {{ $pengkajian->ket_lapor ?: '-' }}
                            </div>
                        </td>
                    </tr>
                    <tr class="border-t-black">
                        <td style="text-align: center; vertical-align: top; padding: 2px 4px;" class="border-r-black">a.</td>
                        <td style="padding: 2px 5px; vertical-align: top;" class="border-r-black">
                            <div>Cara Berjalan :</div>
                            <div>1. Tidak seimbang/sempoyongan/ limbung</div>
                            <div>2. Jalan dengan menggunakan alat bantu (kruk, tripot, kursi roda, orang lain)</div>
                        </td>
                        <td style="text-align: center; vertical-align: middle; padding: 2px 4px;" class="border-r-black">
                            <div>{{ $pengkajian->berjalan_a ?: 'Tidak' }}</div>
                            <div style="margin-top: 2px;">{{ $pengkajian->berjalan_b ?: 'Tidak' }}</div>
                        </td>
                    </tr>
                    <tr class="border-t-black">
                        <td style="text-align: center; vertical-align: middle; padding: 2px 4px;" class="border-r-black">b.</td>
                        <td style="padding: 2px 5px; vertical-align: middle;" class="border-r-black">
                            Menopang saat akan duduk, tampak memegang pinggiran kursi atau meja/benda lain sebagai penopang
                        </td>
                        <td style="text-align: center; vertical-align: middle; padding: 2px 4px;" class="border-r-black">
                            {{ $pengkajian->berjalan_c ?: 'Tidak' }}
                        </td>
                    </tr>
                </table>
            </div>

            {{-- 10. MASALAH KEPERAWATAN & RENCANA KEPERAWATAN --}}
            <table class="border-box" style="border-top: none; font-size: 8pt; width: 100%;">
                <tr>
                    <td style="width: 50%; vertical-align: top; padding: 3px 5px; min-height: 110px; height: 110px;" class="border-r-black">
                        <div style="font-weight: bold; margin-bottom: 2px;">MASALAH KEPERAWATAN :</div>
                        <div style="line-height: 1.35;">{{ $masalahText }}</div>
                    </td>
                    <td style="width: 50%; vertical-align: top; padding: 3px 5px; min-height: 110px; height: 110px;">
                        <div style="font-weight: bold; margin-bottom: 2px;">RENCANA KEPERAWATAN :</div>
                        <div style="line-height: 1.35;">{{ $rencanaText }}</div>
                    </td>
                </tr>
            </table>

            {{-- 11. YANG MELAKUKAN PENGKAJIAN --}}
            <table class="border-box" style="border-top: none; font-size: 8pt; width: 100%;">
                <tr class="border-b-black">
                    <th colspan="2" style="text-align: center; font-weight: bold; padding: 2.5px 0;">
                        YANG MELAKUKAN PENGKAJIAN
                    </th>
                </tr>
                <tr class="border-b-black">
                    <th style="width: 50%; text-align: center; font-weight: normal; padding: 2px 0;" class="border-r-black">
                        Tanggal dan Jam
                    </th>
                    <th style="width: 50%; text-align: center; font-weight: normal; padding: 2px 0;">
                        Tanda Tangan dan Nama Perawat
                    </th>
                </tr>
                <tr>
                    <td style="width: 50%; text-align: center; vertical-align: middle; padding: 8px 5px;" class="border-r-black">
                        {{ \Carbon\Carbon::parse($pengkajian->tanggal)->format('M, d/m/Y H:i:s') }} WIB
                    </td>
                    <td style="width: 50%; text-align: center; vertical-align: middle; padding: 6px 5px;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode($qrData) }}" style="width: 52px; height: 52px; margin: 0 auto 3px auto; display: block;" alt="QR Signature">
                        <div style="font-weight: normal;">
                            {{ $pengkajian->petugas->nama ?? $pengkajian->nip }}
                        </div>
                    </td>
                </tr>
            </table>

        </div>
    </div>

</body>

</html>
