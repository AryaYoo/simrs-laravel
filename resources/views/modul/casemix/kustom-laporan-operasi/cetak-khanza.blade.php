<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Operasi (Persis Khanza) - {{ $laporan->regPeriksa->pasien->nm_pasien ?? $laporan->no_rawat }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 11.5px;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #525659;
            line-height: 1.35;
        }

        .preview-toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 45px;
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

        .btn-action {
            background-color: #8ab4f8;
            color: #202124;
            border: none;
            padding: 5px 14px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-action:hover {
            background-color: #aecbfa;
        }

        .document-container {
            padding-top: 55px;
            padding-bottom: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .document-page {
            width: 210mm;
            min-height: 297mm;
            background-color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            padding: 0.8cm 1.2cm;
            box-sizing: border-box;
            position: relative;
        }

        .banner-gray {
            background-color: #c0c0c0 !important;
            color: #000 !important;
            text-align: center;
            font-weight: bold;
            font-size: 11.5px;
            padding: 2.5px 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .banner-gray-left {
            background-color: #c0c0c0 !important;
            color: #000 !important;
            font-weight: bold;
            font-size: 11px;
            padding: 2.5px 6px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .page-break-avoid {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        @media print {
            @page {
                size: portrait;
                margin: 6mm 8mm;
            }
            body {
                background-color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .preview-toolbar {
                display: none !important;
            }
            .document-container {
                padding: 0 !important;
                display: block !important;
            }
            .document-page {
                width: 100% !important;
                min-height: auto !important;
                height: auto !important;
                box-shadow: none !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body>

    <div class="preview-toolbar">
        <div class="flex items-center gap-3">
            <button onclick="window.close()" class="text-white/80 hover:text-white flex items-center gap-1 text-xs">
                ✕ Tutup
            </button>
            <span class="text-white/30">|</span>
            <span class="text-xs font-semibold">Laporan Operasi (Persis Legacy Khanza)</span>
        </div>
        <div>
            <button onclick="window.print()" class="btn-action">
                🖨️ Cetak Dokumen
            </button>
        </div>
    </div>

    <div class="document-container">
        <div class="document-page">

            {{-- Kop Surat --}}
            <div class="flex items-center gap-3 pb-1.5 mb-2">
                @if(!empty($setting['logo']))
                    <img src="data:image/jpeg;base64,{{ base64_encode($setting['logo']) }}" class="w-14 h-14 object-contain" alt="Logo">
                @elseif(!empty($setting['logo_base64']))
                    <img src="{{ $setting['logo_base64'] }}" class="w-14 h-14 object-contain" alt="Logo">
                @endif
                <div>
                    <h2 class="text-base font-bold uppercase leading-tight">{{ $setting['nama_instansi'] ?? 'Rumah Sakit Ibu dan Anak IBI Surabaya' }}</h2>
                    <p class="text-[11px] leading-tight">{{ $setting['alamat_instansi'] ?? 'Jl. Dupak No. 15A, Kelurahan Gundih, Kecamatan Bubutan, Surabaya, Jawa Timur' }}</p>
                    <p class="text-[11px] leading-tight">{{ $setting['kontak'] ?? '+628152300730' }}</p>
                    <p class="text-[11px] leading-tight">E-mail : {{ $setting['email'] ?? 'rsiaibi15a@gmail.com' }}</p>
                </div>
            </div>

            {{-- Title --}}
            <div class="mb-2">
                <h1 class="text-sm font-bold italic uppercase">LAPORAN OPERASI</h1>
            </div>

            {{-- Identitas Pasien 2 Kolom --}}
            <div class="grid grid-cols-2 gap-x-12 text-[11px] mb-3">
                <div class="space-y-0.5">
                    <div class="flex"><span class="w-24">Nama Pasien</span><span>: <strong class="italic uppercase">{{ $laporan->regPeriksa->pasien->nm_pasien ?? '-' }}</strong></span></div>
                    <div class="flex"><span class="w-24">Umur</span><span>: <em class="italic">{{ $laporan->regPeriksa->umurdaftar ?? '-' }} {{ $laporan->regPeriksa->sttsumur ?? 'Th' }}</em></span></div>
                    <div class="flex"><span class="w-24">Tgl Lahir</span><span>: <em class="italic">{{ isset($laporan->regPeriksa->pasien->tgl_lahir) ? date('d-m-Y', strtotime($laporan->regPeriksa->pasien->tgl_lahir)) : '-' }}</em></span></div>
                    <div class="flex"><span class="w-24">Tindakan</span><span>: {{ $operasi->paketOperasi->nm_perawatan ?? ($laporan->jaringan_dieksekusi ?: 'Curetage Kelas 3') }}</span></div>
                </div>
                <div class="space-y-0.5">
                    <div class="flex"><span class="w-32">No. Rekam Medis</span><span>: <em class="italic">{{ $laporan->regPeriksa->pasien->no_rkm_medis ?? '-' }}</em></span></div>
                    <div class="flex"><span class="w-32">Ruang</span><span>: <em class="italic">{{ $laporan->regPeriksa->poliklinik->nm_poli ?? ($laporan->regPeriksa->kamarInap->kamar->bangsal->nm_bangsal ?? 'IGD') }}</em></span></div>
                    <div class="flex"><span class="w-32">Jenis Kelamin</span><span>: <em class="italic">{{ ($laporan->regPeriksa->pasien->jk ?? '') == 'L' ? 'Laki-Laki' : 'Perempuan' }}</em></span></div>
                </div>
            </div>

            {{-- SECTION 1: PRE SURGICAL ASSESMENT --}}
            @if($preSurgical)
            <div class="mb-2 page-break-avoid">
                <div class="banner-gray mb-1">
                    PRE SURGICAL ASSESMENT
                </div>

                <div class="text-[11px] space-y-0.5 px-0.5">
                    <div class="grid grid-cols-12 gap-2">
                        <div class="col-span-5 flex"><span class="w-24">Tanggal</span><span>: {{ isset($preSurgical->tgl_perawatan) ? date('d-m-Y', strtotime($preSurgical->tgl_perawatan)) : '-' }}</span></div>
                        <div class="col-span-4 flex"><span class="w-16">Waktu :</span><span>{{ $preSurgical->jam_rawat ?? '-' }}</span></div>
                        <div class="col-span-3 flex"><span class="w-14">Alergi :</span><span>{{ $preSurgical->alergi ?: '-' }}</span></div>
                    </div>

                    <div class="flex">
                        <span class="w-24">Dokter Bedah</span>
                        <span>: {{ $preSurgical->nm_dokter ?? ($operasi->dokterOperator1->nm_dokter ?? '-') }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-x-6">
                        <div>
                            <div class="font-bold">Keluhan :</div>
                            <div class="pl-4 italic underline leading-tight">{{ $preSurgical->keluhan ?: '-' }}</div>
                        </div>
                        <div>
                            <div class="font-bold">Penilaian :</div>
                            <div class="pl-4 italic underline leading-tight">{{ $preSurgical->penilaian ?: '-' }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-x-6">
                        <div>
                            <div class="font-bold">Pemeriksaan :</div>
                            <div class="pl-4 italic underline leading-tight">{{ $preSurgical->pemeriksaan ?: '-' }}</div>
                        </div>
                        <div>
                            <div class="font-bold">Tindak Lanjut :</div>
                            <div class="pl-4 italic underline leading-tight">{{ $preSurgical->rtl ?: '-' }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-x-12 text-[11px] pt-0.5">
                        <div class="space-y-0">
                            <div class="flex justify-between"><span class="w-32">Suhu Tubuh.(C)</span><span class="font-semibold italic underline">{{ $preSurgical->suhu_tubuh ?: '-' }}</span></div>
                            <div class="flex justify-between"><span class="w-32">Tensi.</span><span class="font-semibold italic underline">{{ $preSurgical->tensi ?: '-' }}</span></div>
                            <div class="flex justify-between"><span class="w-32">Tinggi (Cm).</span><span class="font-semibold italic underline">{{ $preSurgical->tinggi ?: '' }}</span></div>
                            <div class="flex justify-between"><span class="w-32">Berat (Kg).</span><span class="font-semibold italic underline">{{ $preSurgical->berat ?: '' }}</span></div>
                        </div>
                        <div class="space-y-0">
                            <div class="flex justify-between"><span class="w-32">Nadi (/Mnt).</span><span class="font-semibold italic underline">{{ $preSurgical->nadi ?: '-' }}</span></div>
                            <div class="flex justify-between"><span class="w-32">Respirasi (/Mnt).</span><span class="font-semibold italic underline">{{ $preSurgical->respirasi ?: '' }}</span></div>
                            <div class="flex justify-between"><span class="w-32">GCS (E,V,M).</span><span class="font-semibold italic underline">{{ $preSurgical->gcs ?: '-' }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif


            {{-- SECTION 2: POST SURGICAL REPORT --}}
            <div class="mb-3 page-break-avoid">
                <div class="banner-gray mb-1.5">
                    POST SURGICAL REPORT
                </div>

                {{-- 3 Kolom Tim Medis & Status Operasi --}}
                <div class="grid grid-cols-12 gap-2 text-[11px] mb-2 px-0.5">
                    {{-- Kolom 1 (4/12) --}}
                    <div class="col-span-5 space-y-1">
                        <div class="flex"><span class="w-32">Tanggal & Waktu</span><span>: {{ date('d/m/Y H:i:s', strtotime($laporan->tanggal)) }}</span></div>

                        <div>
                            <div class="flex"><span class="w-32">Dokter Bedah</span><span>:</span></div>
                            <div class="pl-4 italic font-medium">{{ $operasi->dokterOperator1->nm_dokter ?? ($laporan->regPeriksa->dokter->nm_dokter ?? '-') }}</div>
                        </div>

                        <div>
                            <div class="flex"><span class="w-32">Dokter Bedah 2</span><span>:</span></div>
                            <div class="pl-4">{{ $operasi->dokterOperator2->nm_dokter ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="flex"><span class="w-32">Perawat Resusitas</span><span>:</span></div>
                            <div class="pl-4">{{ $staff['perawaat_resusitas'] ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="flex"><span class="w-32">Instrumen</span><span>:</span></div>
                            <div class="pl-4">{{ $staff['instrumen'] ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="flex"><span class="w-32">Dokter Anak</span><span>:</span></div>
                            <div class="pl-4">{{ $operasi->dokterAnak->nm_dokter ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="flex"><span class="w-32">Dokter Umum</span><span>:</span></div>
                            <div class="pl-4">{{ $operasi->dokterUmum->nm_dokter ?? '-' }}</div>
                        </div>
                    </div>

                    {{-- Kolom 2 (4/12) --}}
                    <div class="col-span-4 space-y-1">
                        <div>
                            <div class="flex"><span class="w-28">Asisten Bedah</span><span>:</span></div>
                            <div class="pl-4">{{ $staff['asisten_operator1'] ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="flex"><span class="w-28">Asisten Bedah 2</span><span>:</span></div>
                            <div class="pl-4">{{ $staff['asisten_operator2'] ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="flex"><span class="w-28">Dokter Anastesi</span><span>:</span></div>
                            <div class="pl-4 italic font-medium">{{ $operasi->dokterAnestesi->nm_dokter ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="flex"><span class="w-28">Asisten Anastesi</span><span>:</span></div>
                            <div class="pl-4">{{ $staff['asisten_anestesi'] ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="flex"><span class="w-28">Bidan</span><span>:</span></div>
                            <div class="pl-4 italic font-medium">{{ $staff['bidan'] ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="flex"><span class="w-28">Onloop</span><span>:</span></div>
                            <div class="pl-4">{{ $staff['omloop'] ?? '-' }}</div>
                        </div>
                    </div>

                    {{-- Kolom 3 (3/12) --}}
                    <div class="col-span-3 space-y-2 text-center">
                        <div>
                            <div class="font-medium">Tipe/Jenis Anastesi</div>
                            <div class="italic font-semibold">{{ $operasi->jenis_anestesi ?? 'General' }}</div>
                        </div>

                        <div>
                            <div class="font-medium">Dikirim ke Pemeriksaaan PA</div>
                            <div class="italic font-semibold">{{ $laporan->permintaan_pa ?: 'Tidak' }}</div>
                        </div>

                        <div>
                            <div class="font-medium">Tipe/Kategori Operasi</div>
                            <div class="italic font-semibold">{{ $operasi->kategori ?? 'Kecil' }}</div>
                        </div>

                        <div>
                            <div class="font-medium">Selesai Operasi</div>
                            <div class="font-semibold">{{ date('d/m/Y H:i:s', strtotime($laporan->selesaioperasi)) }}</div>
                        </div>
                    </div>
                </div>

                {{-- Sub-Banners Diagnosa Kiri + Tanda Tangan Kanan (Persis Gambar 2) --}}
                <div class="grid grid-cols-12 gap-4 items-start pt-1">
                    {{-- Sisi Kiri (~75%): Sub Banners Diagnosa --}}
                    <div class="col-span-9 space-y-1">
                        <div class="banner-gray-left">
                            Diagnosa Pre-Op / Pre Operation Diagnosis
                        </div>
                        <div class="pl-3 italic text-[11px] mb-1">
                            {{ $laporan->diagnosa_preop ?: '-' }}
                        </div>

                        <div class="banner-gray-left">
                            Jaringan Yang di-Eksisi/-Insisi
                        </div>
                        <div class="pl-3 italic text-[11px] mb-1">
                            {{ $laporan->jaringan_dieksekusi ?: '-' }}
                        </div>

                        <div class="banner-gray-left">
                            Diagnosa Post-Op / Post Operation Diagnosis
                        </div>
                        <div class="pl-3 italic text-[11px]">
                            {{ $laporan->diagnosa_postop ?: '-' }}
                        </div>
                    </div>

                    {{-- Sisi Kanan (~25%): Signature Block --}}
                    <div class="col-span-3 text-center space-y-0.5">
                        <p class="text-[11px]">{{ date('d/m/Y') }}</p>
                        <p class="text-[11px] font-medium">Dokter Bedah</p>

                        @php
                            $dokterNama = $operasi->dokterOperator1->nm_dokter ?? ($laporan->regPeriksa->dokter->nm_dokter ?? 'Dokter Penanggung Jawab');
                            $qrData = "Dokumen Valid Laporan Operasi Khanza | No Rawat: {$laporan->no_rawat} | Dokter: {$dokterNama}";
                        @endphp
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=85x85&data={{ urlencode($qrData) }}" class="w-20 h-20 mx-auto border p-0.5" alt="QR Signature">

                        <p class="text-[11px] underline font-medium mt-1 leading-snug">{{ $dokterNama }}</p>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: REPORT ( PROCEDURES, SPECIFIC FINDINGS AND COMPLICATIONS ) --}}
            <div class="mb-2 page-break-avoid">
                <div class="banner-gray mb-1">
                    REPORT ( PROCEDURES, SPECIFIC FINDINGS AND COMPLICATIONS )
                </div>
                @php
                    $rawText = $laporan->laporan_operasi ?: "Jaringan yang di Insisi :\npersiapan                 : Inform Consent, Puasa, Infus, Antiobiotik profilaksis\nPosisi Pasien             : Lithotomy\nDesinfeksi                : Betadine + alcohol\nInsisI                    : -\nTemuan Operasi            : Sisa Jaringan\nTindakan                  : Curettage\nPendarahan                : -\nAdvice                    : RL 1000cc/24jam\n                            Oxytocin 10 IU dalam 500cc RL\nLama Anestesi             : 00:35:00";
                    // Hapus baris-baris enter kosong berlebih
                    $lines = array_values(array_filter(array_map('rtrim', explode("\n", str_replace("\r", "", $rawText))), fn($line) => trim($line) !== ''));
                    $cleanedText = implode("\n", $lines);
                @endphp
                <div class="p-1 text-[11px] italic whitespace-pre-line leading-tight">
                    {{ $cleanedText }}
                </div>
            </div>


        </div>
    </div>

</body>
</html>
