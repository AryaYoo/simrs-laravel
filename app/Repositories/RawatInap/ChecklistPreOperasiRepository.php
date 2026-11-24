<?php

namespace App\Repositories\RawatInap;

use App\Models\ChecklistPreOperasi;
use Illuminate\Support\Facades\DB;

class ChecklistPreOperasiRepository
{
    /**
     * Ambil semua data checklist pre operasi berdasarkan no_rawat
     */
    public function getByNoRawat(string $no_rawat): array
    {
        return ChecklistPreOperasi::with([
                'dokterBedah',
                'dokterAnestesi',
                'petugasRuangan',
                'perawatOk'
            ])
            ->where('no_rawat', $no_rawat)
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'no_rawat'                                 => $item->no_rawat,
                    'tanggal'                                  => $item->tanggal ? $item->tanggal->format('Y-m-d H:i:s') : null,
                    'sncn'                                     => $item->sncn,
                    'tindakan'                                 => $item->tindakan,
                    'kd_dokter_bedah'                          => $item->kd_dokter_bedah,
                    'dokter_bedah'                             => [
                        'kd_dokter' => $item->kd_dokter_bedah,
                        'nama'      => $item->dokterBedah->nm_dokter ?? $item->kd_dokter_bedah,
                    ],
                    'kd_dokter_anestesi'                       => $item->kd_dokter_anestesi,
                    'dokter_anestesi'                          => [
                        'kd_dokter' => $item->kd_dokter_anestesi,
                        'nama'      => $item->dokterAnestesi->nm_dokter ?? $item->kd_dokter_anestesi,
                    ],
                    'identitas'                                => $item->identitas,
                    'surat_ijin_bedah'                         => $item->surat_ijin_bedah,
                    'surat_ijin_anestesi'                      => $item->surat_ijin_anestesi,
                    'surat_ijin_transfusi'                     => $item->surat_ijin_transfusi,
                    'penandaan_area_operasi'                   => $item->penandaan_area_operasi,
                    'keadaan_umum'                             => $item->keadaan_umum,
                    'pemeriksaan_penunjang_rontgen'            => $item->pemeriksaan_penunjang_rontgen,
                    'keterangan_pemeriksaan_penunjang_rontgen' => $item->keterangan_pemeriksaan_penunjang_rontgen,
                    'pemeriksaan_penunjang_ekg'                => $item->pemeriksaan_penunjang_ekg,
                    'keterangan_pemeriksaan_penunjang_ekg'     => $item->keterangan_pemeriksaan_penunjang_ekg,
                    'pemeriksaan_penunjang_usg'                => $item->pemeriksaan_penunjang_usg,
                    'keterangan_pemeriksaan_penunjang_usg'     => $item->keterangan_pemeriksaan_penunjang_usg,
                    'pemeriksaan_penunjang_ctscan'             => $item->pemeriksaan_penunjang_ctscan,
                    'keterangan_pemeriksaan_penunjang_ctscan'  => $item->keterangan_pemeriksaan_penunjang_ctscan,
                    'pemeriksaan_penunjang_mri'                => $item->pemeriksaan_penunjang_mri,
                    'keterangan_pemeriksaan_penunjang_mri'     => $item->keterangan_pemeriksaan_penunjang_mri,
                    'persiapan_darah'                          => $item->persiapan_darah,
                    'keterangan_persiapan_darah'               => $item->keterangan_persiapan_darah,
                    'perlengkapan_khusus'                      => $item->perlengkapan_khusus,
                    'nip_petugas_ruangan'                      => $item->nip_petugas_ruangan,
                    'petugas_ruangan'                          => [
                        'nip'  => $item->nip_petugas_ruangan,
                        'nama' => $item->petugasRuangan->nama ?? $item->nip_petugas_ruangan,
                    ],
                    'nip_perawat_ok'                           => $item->nip_perawat_ok,
                    'perawat_ok'                               => [
                        'nip'  => $item->nip_perawat_ok,
                        'nama' => $item->perawatOk->nama ?? $item->nip_perawat_ok,
                    ],
                ];
            })
            ->toArray();
    }

    /**
     * Cari 1 data checklist berdasarkan composite key (no_rawat + tanggal)
     */
    public function findByKey(string $no_rawat, string $tanggal): ?ChecklistPreOperasi
    {
        return ChecklistPreOperasi::with([
                'dokterBedah',
                'dokterAnestesi',
                'petugasRuangan',
                'perawatOk',
                'regPeriksa.pasien'
            ])
            ->where('no_rawat', $no_rawat)
            ->where('tanggal', $tanggal)
            ->first();
    }

    /**
     * Simpan data checklist baru
     */
    public function create(array $data): bool
    {
        return DB::table('checklist_pre_operasi')->insert($data);
    }

    /**
     * Update data checklist berdasarkan composite key
     */
    public function update(string $no_rawat, string $old_tanggal, array $data): bool
    {
        return DB::table('checklist_pre_operasi')
            ->where('no_rawat', $no_rawat)
            ->where('tanggal', $old_tanggal)
            ->update($data) >= 0;
    }

    /**
     * Hapus data checklist berdasarkan composite key
     */
    public function delete(string $no_rawat, string $tanggal): bool
    {
        return DB::table('checklist_pre_operasi')
            ->where('no_rawat', $no_rawat)
            ->where('tanggal', $tanggal)
            ->delete() > 0;
    }
}
