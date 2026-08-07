<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Operasi - {{ $laporan->regPeriksa->pasien->nm_pasien ?? $laporan->no_rawat }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #525659;
        }

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
        }

        .btn-action:hover {
            background-color: #aecbfa;
        }

        .document-container {
            padding-top: 70px;
            padding-bottom: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .document-page {
            width: 210mm;
            min-height: 297mm;
            background-color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            padding: 1.2cm 1.5cm;
            box-sizing: border-box;
            position: relative;
        }

        .page-break-avoid {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        @media print {
            @page {
                size: portrait;
                margin: 10mm 12mm;
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
            <span class="text-xs font-semibold">Laporan Operasi (CASEMIX)</span>
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
            <div class="flex items-center gap-4 pb-3 border-b-2 border-neutral-800 mb-4">
                @if(!empty($setting['logo']))
                    <img src="data:image/jpeg;base64,{{ base64_encode($setting['logo']) }}" class="w-16 h-16 object-contain" alt="Logo">
                @elseif(!empty($setting['logo_base64']))
                    <img src="{{ $setting['logo_base64'] }}" class="w-16 h-16 object-contain" alt="Logo">
                @endif
                <div class="flex-1">
                    <h2 class="text-lg font-bold uppercase tracking-wide leading-tight">{{ $setting['nama_instansi'] ?? 'RUMAH SAKIT' }}</h2>
                    <p class="text-xs text-neutral-600 leading-snug">{{ $setting['alamat_instansi'] ?? '' }}, {{ $setting['kabupaten'] ?? '' }}</p>
                    <p class="text-xs text-neutral-600 leading-snug">Kontak: {{ $setting['kontak'] ?? '-' }} | Email: {{ $setting['email'] ?? '-' }}</p>
                </div>
            </div>

            {{-- Title --}}
            <div class="text-center mb-4">
                <h1 class="text-base font-bold underline uppercase">LAPORAN OPERASI</h1>
                <p class="text-xs text-neutral-500 font-mono">No. Rawat: {{ $laporan->no_rawat }}</p>
            </div>

            {{-- Identitas Pasien --}}
            <div class="border border-neutral-400 rounded p-3 mb-4 bg-neutral-50 text-xs">
                <div class="grid grid-cols-2 gap-x-6 gap-y-1">
                    <div class="flex"><span class="w-28 text-neutral-500">No. Rekam Medis</span><span class="font-bold">: {{ $laporan->regPeriksa->pasien->no_rkm_medis ?? '-' }}</span></div>
                    <div class="flex"><span class="w-28 text-neutral-500">Nama Pasien</span><span class="font-bold">: {{ $laporan->regPeriksa->pasien->nm_pasien ?? '-' }}</span></div>
                    <div class="flex"><span class="w-28 text-neutral-500">Jenis Kelamin</span><span>: {{ ($laporan->regPeriksa->pasien->jk ?? '') == 'L' ? 'Laki-Laki' : 'Perempuan' }}</span></div>
                    <div class="flex"><span class="w-28 text-neutral-500">Tanggal Lahir / Umur</span><span>: {{ isset($laporan->regPeriksa->pasien->tgl_lahir) ? date('d-m-Y', strtotime($laporan->regPeriksa->pasien->tgl_lahir)) : '-' }} ({{ $laporan->regPeriksa->umurdaftar ?? '-' }} {{ $laporan->regPeriksa->sttsumur ?? '' }})</span></div>
                    <div class="flex"><span class="w-28 text-neutral-500">Poliklinik / Ruang</span><span>: {{ $laporan->regPeriksa->poliklinik->nm_poli ?? '-' }}</span></div>
                    <div class="flex"><span class="w-28 text-neutral-500">Penjamin</span><span>: {{ $laporan->regPeriksa->penjab->png_jawab ?? '-' }}</span></div>
                </div>
            </div>
            {{-- PRE SURGICAL ASSESMENT --}}
            @if($preSurgical)
            <div class="mb-3 page-break-avoid">
                <div class="bg-neutral-700 text-white text-center text-xs font-bold py-1 mb-0" style="background-color:#4a4a4a;">
                    PRE SURGICAL ASSESMENT
                </div>
                <table class="w-full border-collapse border border-neutral-400 text-xs">
                    <tbody>
                        <tr>
                            <td class="border border-neutral-400 p-1.5 w-1/4">
                                <span class="text-neutral-500">Tanggal</span> :
                                <span class="font-semibold">{{ isset($preSurgical->tgl_perawatan) ? date('d-m-Y', strtotime($preSurgical->tgl_perawatan)) : '-' }}</span>
                            </td>
                            <td class="border border-neutral-400 p-1.5 w-1/4">
                                <span class="text-neutral-500">Waktu</span> :
                                <span class="font-semibold">{{ $preSurgical->jam_rawat ?? '-' }}</span>
                            </td>
                            <td class="border border-neutral-400 p-1.5 w-1/2">
                                <span class="text-neutral-500">Alergi</span> :
                                <span class="font-semibold">{{ $preSurgical->alergi ?: '-' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="border border-neutral-400 p-1.5">
                                <span class="text-neutral-500">Dokter Bedah</span> :
                                <span class="font-semibold">{{ $preSurgical->nm_dokter ?? $preSurgical->nama_petugas ?? '-' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border border-neutral-400 p-1.5 align-top">
                                <div class="mb-1"><span class="font-bold">Keluhan :</span></div>
                                <div class="italic text-neutral-700 leading-relaxed">{{ $preSurgical->keluhan ?: '-' }}</div>
                            </td>
                            <td class="border border-neutral-400 p-1.5 align-top">
                                <div class="mb-1"><span class="font-bold">Penilaian :</span></div>
                                <div class="italic text-neutral-700 leading-relaxed">{{ $preSurgical->penilaian ?: '-' }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="border border-neutral-400 p-1.5">
                                <div class="font-bold mb-1">Pemeriksaan :</div>
                                <div class="grid grid-cols-2 gap-x-4 text-xs">
                                    <div class="leading-relaxed italic text-neutral-700">{{ $preSurgical->pemeriksaan ?: '-' }}</div>
                                    <div class="border-l border-neutral-300 pl-3">
                                        <div class="font-bold mb-1">Tindak Lanjut :</div>
                                        <div class="italic text-neutral-700 leading-relaxed">{{ $preSurgical->rtl ?: '-' }}</div>
                                    </div>
                                </div>
                                <div class="mt-2 grid grid-cols-3 gap-x-2 text-xs">
                                    <div class="flex items-center gap-1">
                                        <span class="text-neutral-500 w-28">Tensi (mmHg)</span>
                                        <span class="font-bold">{{ $preSurgical->tensi ?: '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="text-neutral-500 w-28">Nadi (/Mnt)</span>
                                        <span class="font-bold">{{ $preSurgical->nadi ?: '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="text-neutral-500 w-16">SPO2</span>
                                        <span class="font-bold">{{ $preSurgical->spo2 ? $preSurgical->spo2.'%' : '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="text-neutral-500 w-28">Suhu Tubuh (°C)</span>
                                        <span class="font-bold">{{ $preSurgical->suhu_tubuh ?: '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="text-neutral-500 w-28">Respirasi (/Mnt)</span>
                                        <span class="font-bold">{{ $preSurgical->respirasi ?: '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="text-neutral-500 w-16">GCS (E,V,M)</span>
                                        <span class="font-bold">{{ $preSurgical->gcs ?: '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="text-neutral-500 w-28">Tinggi (Cm)</span>
                                        <span class="font-bold">{{ $preSurgical->tinggi ?: '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="text-neutral-500 w-28">Berat (Kg)</span>
                                        <span class="font-bold">{{ $preSurgical->berat ?: '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="text-neutral-500 w-16">Kesadaran</span>
                                        <span class="font-bold">{{ $preSurgical->kesadaran ?: '-' }}</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif

            {{-- POST SURGICAL REPORT --}}
            <div class="mb-3 page-break-avoid">
                <div class="bg-neutral-700 text-white text-center text-xs font-bold py-1 mb-0" style="background-color:#4a4a4a;">
                    POST SURGICAL REPORT
                </div>
                <table class="w-full border-collapse border border-neutral-400 text-xs">
                    <tbody>
                        <tr class="border-b border-neutral-300">
                            <td class="w-40 font-semibold p-2 bg-neutral-100 border-r border-neutral-300">Tanggal & Jam Operasi</td>
                            <td class="p-2">
                                <span class="font-bold">{{ date('d-m-Y H:i', strtotime($laporan->tanggal)) }}</span>
                                <span class="text-neutral-500 ml-2">s/d</span>
                                <span class="font-bold ml-2">{{ date('d-m-Y H:i', strtotime($laporan->selesaioperasi)) }}</span>
                            </td>
                        </tr>
                        <tr class="border-b border-neutral-300">
                            <td class="font-semibold p-2 bg-neutral-100 border-r border-neutral-300">Diagnosa Pre Operatif</td>
                            <td class="p-2 font-semibold text-neutral-800">{{ $laporan->diagnosa_preop ?: '-' }}</td>
                        </tr>
                        <tr class="border-b border-neutral-300">
                            <td class="font-semibold p-2 bg-neutral-100 border-r border-neutral-300">Diagnosa Post Operatif</td>
                            <td class="p-2 font-semibold text-neutral-800">{{ $laporan->diagnosa_postop ?: '-' }}</td>
                        </tr>
                        <tr class="border-b border-neutral-300">
                            <td class="font-semibold p-2 bg-neutral-100 border-r border-neutral-300">Jaringan Dieksekusi</td>
                            <td class="p-2">{{ $laporan->jaringan_dieksekusi ?: '-' }}</td>
                        </tr>
                        <tr class="border-b border-neutral-300">
                            <td class="font-semibold p-2 bg-neutral-100 border-r border-neutral-300">Permintaan PA</td>
                            <td class="p-2 font-bold">{{ $laporan->permintaan_pa }}</td>
                        </tr>
                        @if(!empty($laporan->nomor_implan))
                        <tr class="border-b border-neutral-300">
                            <td class="font-semibold p-2 bg-neutral-100 border-r border-neutral-300">Nomor Implan</td>
                            <td class="p-2">{{ $laporan->nomor_implan }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Tim Medis (jika ada data operasi) --}}
            @if($operasi)
            <div class="mb-3 page-break-avoid">
                <h3 class="font-bold text-xs uppercase mb-1 border-b border-neutral-300 pb-0.5">Tim Medis Operasi</h3>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div><span class="text-neutral-500">Operator Utama:</span> {{ $operasi->dokterOperator1->nm_dokter ?? '-' }}</div>
                    <div><span class="text-neutral-500">Dokter Anestesi:</span> {{ $operasi->dokterAnestesi->nm_dokter ?? '-' }}</div>
                </div>
            </div>
            @endif

            {{-- Laporan Operasi Content --}}
            <div class="mb-4 page-break-avoid">
                <h3 class="font-bold text-xs uppercase mb-1 border-b border-neutral-300 pb-0.5">Laporan Rincian Operasi</h3>
                <div class="p-3 border border-neutral-300 rounded text-xs whitespace-pre-line leading-relaxed bg-white">
                    {{ $laporan->laporan_operasi ?: 'Tidak ada rincian catatan operasi.' }}
                </div>
            </div>

            {{-- Tanda Tangan Block --}}
            <div class="flex justify-end mt-6 page-break-avoid">
                <div class="text-center w-64">
                    <p class="text-xs text-neutral-600 mb-1">{{ $setting['kabupaten'] ?? 'Tempat' }}, {{ date('d-m-Y') }}</p>
                    <p class="text-xs font-bold mb-2">Dokter / Operator</p>

                    @php
                        $dokterNama = $operasi->dokterOperator1->nm_dokter ?? ($laporan->regPeriksa->dokter->nm_dokter ?? 'Dokter Penanggung Jawab');
                        $qrData = "Dokumen Valid Laporan Operasi RSIA IBI | No Rawat: {$laporan->no_rawat} | Dokter: {$dokterNama}";
                    @endphp
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode($qrData) }}" class="w-20 h-20 mx-auto my-2 border p-1 rounded" alt="QR Signature">

                    <p class="text-xs font-bold underline mt-1">{{ $dokterNama }}</p>
                </div>
            </div>

        </div>
    </div>

</body>
</html>