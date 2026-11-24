<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check List Pre Operasi - {{ $regPeriksa->pasien->nm_pasien ?? $regPeriksa->no_rkm_medis }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
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
                padding: 8mm 12mm !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
                overflow: visible !important;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }
        }

        table.bordered-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
        }

        table.bordered-table th, table.bordered-table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }
    </style>
</head>

<body>

    {{-- Screen Toolbar --}}
    <div class="preview-toolbar">
        <div class="toolbar-group">
            <span class="font-semibold text-sm tracking-wide">Pratinjau Cetak: Check List Pre Operasi</span>
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

            {{-- 1. KOP SURAT INSTANSI --}}
            <div class="flex items-center gap-4 pb-3 border-b-2 border-black mb-3">
                @if(!empty($setting['logo']))
                    <img src="data:image/jpeg;base64,{{ base64_encode($setting['logo']) }}" class="w-16 h-16 object-contain" alt="Logo" />
                @else
                    <div class="w-14 h-14 rounded-full border border-gray-400 flex items-center justify-center font-bold text-xs">RS</div>
                @endif
                <div class="flex-1 text-center">
                    <h2 class="text-base font-bold uppercase tracking-wider text-black">{{ $setting['nama_instansi'] ?? 'RUMAH SAKIT' }}</h2>
                    <p class="text-[11px] text-neutral-800 leading-snug">{{ $setting['alamat_instansi'] ?? '' }}</p>
                    <p class="text-[10.5px] text-neutral-700">Telp: {{ $setting['kontak'] ?? '-' }} | Email: {{ $setting['email'] ?? '-' }}</p>
                </div>
            </div>

            {{-- 2. JUDUL DOKUMEN --}}
            <div class="text-center mb-4">
                <h1 class="text-base font-bold uppercase underline tracking-wider text-black">CHECK LIST PRE OPERASI</h1>
                <p class="text-[11px] text-neutral-600">Keselamatan dan Verifikasi Pasien Bedah Rawat Inap</p>
            </div>

            {{-- 3. IDENTITAS PASIEN & TINDAKAN --}}
            <div class="border border-black p-2.5 mb-3 text-[11.5px] leading-relaxed">
                <div class="grid grid-cols-2 gap-x-4">
                    <table class="w-full">
                        <tr>
                            <td class="font-semibold w-24">No. Rawat</td>
                            <td>: {{ $regPeriksa->no_rawat }}</td>
                        </tr>
                        <tr>
                            <td class="font-semibold">No. RM</td>
                            <td>: {{ $regPeriksa->no_rkm_medis }}</td>
                        </tr>
                        <tr>
                            <td class="font-semibold">Nama Pasien</td>
                            <td class="font-bold uppercase">: {{ $regPeriksa->pasien->nm_pasien ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-semibold">Tgl Lahir / Umur</td>
                            <td>: {{ $regPeriksa->pasien->tgl_lahir ?? '-' }} ({{ $regPeriksa->umurdaftar }} {{ $regPeriksa->sttsumur }})</td>
                        </tr>
                        <tr>
                            <td class="font-semibold">Ruang / Kamar</td>
                            <td>: {{ $kamarInap ? ($kamarInap->kamar->bangsal->nm_bangsal ?? '-') . ' / ' . $kamarInap->kd_kamar : '-' }}</td>
                        </tr>
                    </table>

                    <table class="w-full">
                        <tr>
                            <td class="font-semibold w-28">Waktu Tindakan</td>
                            <td>: {{ \Carbon\Carbon::parse($checklist->tanggal)->format('d-m-Y H:i:s') }} WIB</td>
                        </tr>
                        <tr>
                            <td class="font-semibold">SN / CN</td>
                            <td>: {{ $checklist->sncn ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-semibold">Tindakan Operasi</td>
                            <td class="font-bold uppercase">: {{ $checklist->tindakan }}</td>
                        </tr>
                        <tr>
                            <td class="font-semibold">Dokter Bedah</td>
                            <td class="font-bold">: {{ $checklist->dokterBedah->nm_dokter ?? $checklist->kd_dokter_bedah }}</td>
                        </tr>
                        <tr>
                            <td class="font-semibold">Dokter Anestesi</td>
                            <td class="font-bold">: {{ $checklist->dokterAnestesi->nm_dokter ?? $checklist->kd_dokter_anestesi }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- 4. TABEL KONFIRMASI PERAWAT --}}
            <div class="mb-3">
                <div class="font-bold text-[12px] uppercase mb-1 bg-neutral-200/60 p-1 border border-black border-b-0">
                    I. Perawat Melakukan Konfirmasi
                </div>
                <table class="bordered-table text-[11.5px]">
                    <thead class="bg-neutral-100">
                        <tr>
                            <th class="w-10 text-center">No</th>
                            <th class="text-left">Parameter Verifikasi</th>
                            <th class="w-36 text-center">Status</th>
                            <th class="text-left">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center font-semibold">1</td>
                            <td>Identitas Pasien</td>
                            <td class="text-center font-bold">{{ $checklist->identitas }}</td>
                            <td>Kesesuaian gelang identitas, nama, dan tanggal lahir</td>
                        </tr>
                        <tr>
                            <td class="text-center font-semibold">2</td>
                            <td>Keadaan Umum Pasien</td>
                            <td class="text-center font-bold">{{ $checklist->keadaan_umum }}</td>
                            <td>Kondisi klinis pasien saat serah terima</td>
                        </tr>
                        <tr>
                            <td class="text-center font-semibold">3</td>
                            <td>Penandaan Area Operasi (Site Marking)</td>
                            <td class="text-center font-bold">{{ $checklist->penandaan_area_operasi }}</td>
                            <td>Tanda area insisi bedah yang jelas</td>
                        </tr>
                        <tr>
                            <td class="text-center font-semibold">4</td>
                            <td>Surat Ijin Bedah (Informed Consent)</td>
                            <td class="text-center font-bold">{{ $checklist->surat_ijin_bedah }}</td>
                            <td>Persetujuan tindakan medis pembedahan</td>
                        </tr>
                        <tr>
                            <td class="text-center font-semibold">5</td>
                            <td>Surat Ijin Anestesi</td>
                            <td class="text-center font-bold">{{ $checklist->surat_ijin_anestesi }}</td>
                            <td>Persetujuan tindakan pembiusan</td>
                        </tr>
                        <tr>
                            <td class="text-center font-semibold">6</td>
                            <td>Surat Ijin Tranfusi</td>
                            <td class="text-center font-bold">{{ $checklist->surat_ijin_transfusi }}</td>
                            <td>Persetujuan pemberian produk darah</td>
                        </tr>
                        <tr>
                            <td class="text-center font-semibold">7</td>
                            <td>Persiapan Darah</td>
                            <td class="text-center font-bold">{{ $checklist->persiapan_darah }}</td>
                            <td>{{ $checklist->keterangan_persiapan_darah ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-center font-semibold">8</td>
                            <td>Perlengkapan Khusus, Alat / Implan</td>
                            <td class="text-center font-bold">{{ $checklist->perlengkapan_khusus }}</td>
                            <td>Kesiapan alat khusus / prostesis / implan steril</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 5. TABEL PEMERIKSAAN PENUNJANG --}}
            <div class="mb-4">
                <div class="font-bold text-[12px] uppercase mb-1 bg-neutral-200/60 p-1 border border-black border-b-0">
                    II. Hasil Pemeriksaan Penunjang
                </div>
                <table class="bordered-table text-[11.5px]">
                    <thead class="bg-neutral-100">
                        <tr>
                            <th class="w-10 text-center">No</th>
                            <th class="text-left w-52">Jenis Pemeriksaan</th>
                            <th class="w-36 text-center">Status</th>
                            <th class="text-left">Keterangan Hasil</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center font-semibold">1</td>
                            <td>Radiologi / Rontgen</td>
                            <td class="text-center font-bold">{{ $checklist->pemeriksaan_penunjang_rontgen }}</td>
                            <td>{{ $checklist->keterangan_pemeriksaan_penunjang_rontgen ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-center font-semibold">2</td>
                            <td>EKG (Elektrokardiografi)</td>
                            <td class="text-center font-bold">{{ $checklist->pemeriksaan_penunjang_ekg }}</td>
                            <td>{{ $checklist->keterangan_pemeriksaan_penunjang_ekg ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-center font-semibold">3</td>
                            <td>USG (Ultrasonografi)</td>
                            <td class="text-center font-bold">{{ $checklist->pemeriksaan_penunjang_usg }}</td>
                            <td>{{ $checklist->keterangan_pemeriksaan_penunjang_usg ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-center font-semibold">4</td>
                            <td>CT Scan</td>
                            <td class="text-center font-bold">{{ $checklist->pemeriksaan_penunjang_ctscan }}</td>
                            <td>{{ $checklist->keterangan_pemeriksaan_penunjang_ctscan ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-center font-semibold">5</td>
                            <td>MRI</td>
                            <td class="text-center font-bold">{{ $checklist->pemeriksaan_penunjang_mri }}</td>
                            <td>{{ $checklist->keterangan_pemeriksaan_penunjang_mri ?: '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 6. LEMBAR PENGESAHAN / TANDA TANGAN --}}
            <div class="border border-black p-3 bg-neutral-50/30">
                <div class="grid grid-cols-4 gap-2 text-center text-[11px]">
                    {{-- Petugas Ruangan --}}
                    <div class="flex flex-col justify-between h-36">
                        <div>
                            <span class="font-semibold block">Petugas Ruangan</span>
                            <span class="text-[10px] text-neutral-500">Yang Menyerahkan</span>
                        </div>
                        <div class="flex justify-center my-1">
                            @php
                                $namaPetugasRuangan = $checklist->petugasRuangan->nama ?? $checklist->nip_petugas_ruangan;
                            @endphp
                            @if(!empty($namaPetugasRuangan))
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=Petugas%20Ruangan:%20{{ urlencode($namaPetugasRuangan) }}%20Tgl:%20{{ urlencode($checklist->tanggal) }}" class="w-14 h-14" alt="QR" />
                            @else
                                <div class="w-14 h-14"></div>
                            @endif
                        </div>
                        <div class="font-bold underline text-[11px] truncate" title="{{ $namaPetugasRuangan ?? '-' }}">
                            {{ $namaPetugasRuangan ?: '(................................)' }}
                        </div>
                    </div>

                    {{-- Petugas OK --}}
                    <div class="flex flex-col justify-between h-36">
                        <div>
                            <span class="font-semibold block">Petugas OK</span>
                            <span class="text-[10px] text-neutral-500">Yang Menerima</span>
                        </div>
                        <div class="flex justify-center my-1">
                            @php
                                $namaPerawatOk = $checklist->perawatOk->nama ?? $checklist->nip_perawat_ok;
                            @endphp
                            @if(!empty($namaPerawatOk))
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=Petugas%20OK:%20{{ urlencode($namaPerawatOk) }}%20Tgl:%20{{ urlencode($checklist->tanggal) }}" class="w-14 h-14" alt="QR" />
                            @else
                                <div class="w-14 h-14"></div>
                            @endif
                        </div>
                        <div class="font-bold underline text-[11px] truncate" title="{{ $namaPerawatOk ?? '-' }}">
                            {{ $namaPerawatOk ?: '(................................)' }}
                        </div>
                    </div>

                    {{-- Dokter Bedah --}}
                    <div class="flex flex-col justify-between h-36">
                        <div>
                            <span class="font-semibold block">Dokter Bedah / Operator</span>
                            <span class="text-[10px] text-neutral-500">Penanggung Jawab</span>
                        </div>
                        <div class="flex justify-center my-1">
                            @php
                                $namaDokterBedah = $checklist->dokterBedah->nm_dokter ?? $checklist->kd_dokter_bedah;
                            @endphp
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=Dokter%20Bedah:%20{{ urlencode($namaDokterBedah) }}%20Tgl:%20{{ urlencode($checklist->tanggal) }}" class="w-14 h-14" alt="QR" />
                        </div>
                        <div class="font-bold underline text-[11px] truncate" title="{{ $namaDokterBedah }}">
                            {{ $namaDokterBedah }}
                        </div>
                    </div>

                    {{-- Dokter Anestesi --}}
                    <div class="flex flex-col justify-between h-36">
                        <div>
                            <span class="font-semibold block">Dokter Anestesi</span>
                            <span class="text-[10px] text-neutral-500">Penanggung Jawab</span>
                        </div>
                        <div class="flex justify-center my-1">
                            @php
                                $namaDokterAnestesi = $checklist->dokterAnestesi->nm_dokter ?? $checklist->kd_dokter_anestesi;
                            @endphp
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=Dokter%20Anestesi:%20{{ urlencode($namaDokterAnestesi) }}%20Tgl:%20{{ urlencode($checklist->tanggal) }}" class="w-14 h-14" alt="QR" />
                        </div>
                        <div class="font-bold underline text-[11px] truncate" title="{{ $namaDokterAnestesi }}">
                            {{ $namaDokterAnestesi }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- 7. FOOTER NOTE --}}
            <div class="text-[10px] text-neutral-500 mt-2 flex justify-between">
                <span>* Form Checklist Pre Operasi wajib dilengkapi sebelum pasien dipindahkan ke meja operasi</span>
                <span>Dicetak: {{ now()->format('d-m-Y H:i:s') }}</span>
            </div>

        </div>
    </div>

</body>

</html>
