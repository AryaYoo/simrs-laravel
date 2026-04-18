<?php

namespace App\Repositories\RawatInap;

use App\Models\PemeriksaanRanap;
use App\Models\PemeriksaanRalan;
use App\Models\DetailPeriksaLab;
use App\Models\PeriksaRadiologi;
use App\Models\RawatInapDr;
use App\Models\RawatInapPr;
use App\Models\RawatInapDrpr;
use App\Models\DetailPemberianObat;
use App\Models\DetailBeriDiet;
use App\Models\PermintaanLab;
use App\Models\ResepPulang;
use App\Models\ResumePasienRanap;
use Illuminate\Support\Collection;

class ResumePasienRepository
{
    /**
     * Mengambil auto-fill data untuk resume baru.
     */
    public static function getAutoFillData(string $no_rawat, $regPeriksa): array
    {
        $data = [
            'keluhan_utama' => '',
            'pemeriksaan_fisik' => '',
            'alergi' => '',
            'diagnosa_awal' => '',
            'diagnosa_utama' => '', 'kd_diagnosa_utama' => '',
            'diagnosa_sekunder' => '', 'kd_diagnosa_sekunder' => '',
            'diagnosa_sekunder2' => '', 'kd_diagnosa_sekunder2' => '',
            'diagnosa_sekunder3' => '', 'kd_diagnosa_sekunder3' => '',
            'diagnosa_sekunder4' => '', 'kd_diagnosa_sekunder4' => '',
        ];

        // 1. Fetch Latest Clinical Record (Pemeriksaan Ranap)
        $latestPemeriksaan = PemeriksaanRanap::where('no_rawat', $no_rawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->first();

        if ($latestPemeriksaan) {
            $data['keluhan_utama'] = $latestPemeriksaan->keluhan;
            $data['pemeriksaan_fisik'] = $latestPemeriksaan->pemeriksaan;
            $data['alergi'] = $latestPemeriksaan->alergi;
        }

        // 2. Fetch Latest Ward Info for Diagnosa Awal
        $rooms = $regPeriksa->kamarInap ?? collect();
        if ($rooms->count() > 0) {
            $data['diagnosa_awal'] = $rooms->first()->diagnosa_awal;
        }

        // 3. Fetch Existing Diagnoses
        $diagnoses = $regPeriksa->diagnosaPasien ?? collect();
        
        // Primary
        $primary = $diagnoses->where('prioritas', 1)->first();
        if ($primary) {
            $data['kd_diagnosa_utama'] = $primary->kd_penyakit;
            $data['diagnosa_utama'] = $primary->penyakit->nm_penyakit ?? '';
        }

        // Secondaries
        $secondaries = $diagnoses->where('prioritas', '>', 1)->sortBy('prioritas')->values();
        for ($i = 0; $i < 4; $i++) {
            if (isset($secondaries[$i])) {
                $fieldKd = 'kd_diagnosa_sekunder' . ($i === 0 ? '' : ($i + 1));
                $fieldName = 'diagnosa_sekunder' . ($i === 0 ? '' : ($i + 1));
                $data[$fieldKd] = $secondaries[$i]->kd_penyakit;
                $data[$fieldName] = $secondaries[$i]->penyakit->nm_penyakit ?? '';
            }
        }

        return $data;
    }

    /**
     * Memanggil riwayat medis pasien beserta relasinya.
     */
    public static function getHistoryItems(string $type, string $no_rawat): array
    {
        $items = [];

        if ($type === 'KELUHAN') {
            $items = PemeriksaanRalan::where('no_rawat', $no_rawat)
                ->orderBy('tgl_perawatan', 'desc')
                ->orderBy('jam_rawat', 'desc')
                ->get()
                ->map(fn($item) => [
                    'id' => 'ralan_kel_' . $item->tgl_perawatan . '_' . $item->jam_rawat,
                    'keluhan' => $item->keluhan,
                    'tgl_perawatan' => $item->tgl_perawatan,
                    'jam_rawat' => $item->jam_rawat,
                ])->values()->toArray();
        } elseif ($type === 'PEMERIKSAAN') {
            $items = PemeriksaanRalan::where('no_rawat', $no_rawat)
                ->orderBy('tgl_perawatan', 'desc')
                ->orderBy('jam_rawat', 'desc')
                ->get()
                ->map(fn($item) => [
                    'id' => 'ralan_' . $item->tgl_perawatan . '_' . $item->jam_rawat,
                    'pemeriksaan' => $item->pemeriksaan,
                    'tgl_perawatan' => $item->tgl_perawatan,
                    'jam_rawat' => $item->jam_rawat,
                ])->values()->toArray();
        } elseif ($type === 'SOAP') {
            $items = PemeriksaanRanap::where('no_rawat', $no_rawat)
                ->orderBy('tgl_perawatan', 'desc')
                ->orderBy('jam_rawat', 'desc')
                ->get()
                ->map(fn($item) => [
                    'id' => 'ranap_soap_' . $item->tgl_perawatan . '_' . $item->jam_rawat,
                    'keluhan' => $item->keluhan,
                    'pemeriksaan' => $item->pemeriksaan,
                    'tgl_perawatan' => $item->tgl_perawatan,
                    'jam_rawat' => $item->jam_rawat,
                ])->values()->toArray();
        } elseif ($type === 'LAB') {
            $items = DetailPeriksaLab::with('template')
                ->where('no_rawat', $no_rawat)
                ->get()
                ->map(function($item) {
                    $ref = [];
                    if ($item->template?->nilai_rujukan_ld) $ref[] = "L:" . $item->template->nilai_rujukan_ld;
                    if ($item->template?->nilai_rujukan_pd) $ref[] = "P:" . $item->template->nilai_rujukan_pd;
                    $nilaiNormal = !empty($ref) ? implode("; ", $ref) : null;
                    return [
                        'id' => $item->id_template . '_' . $item->tgl_periksa . '_' . $item->jam,
                        'name' => $item->template?->Pemeriksaan ?? '-',
                        'nilai' => $item->nilai,
                        'satuan' => $item->template?->satuan ?? '',
                        'label' => ($item->template?->Pemeriksaan ?? '-') . ': ' . $item->nilai . ' ' . ($item->template?->satuan ?? ''),
                        'nilai_normal' => $nilaiNormal,
                        'date' => $item->tgl_periksa . ' ' . $item->jam
                    ];
                })->values()->toArray();
        } elseif ($type === 'RAD') {
            $items = PeriksaRadiologi::with('jnsPerawatan')
                ->where('no_rawat', $no_rawat)
                ->get()
                ->map(fn($item) => [
                    'id' => $item->kd_jenis_prw . '_' . $item->tgl_periksa . '_' . $item->jam,
                    'label' => ($item->jnsPerawatan?->nm_perawatan ?? '-') . ' (Hasil: ' . $item->hasil . ')',
                    'text' => $item->hasil,
                    'date' => $item->tgl_periksa . ' ' . $item->jam
                ])->values()->toArray();
        } elseif ($type === 'TINDAKAN') {
            $dr = RawatInapDr::with('jnsPerawatan')->where('no_rawat', $no_rawat)->get();
            $pr = RawatInapPr::with('jnsPerawatan')->where('no_rawat', $no_rawat)->get();
            $drpr = RawatInapDrpr::with('jnsPerawatan')->where('no_rawat', $no_rawat)->get();

            $items = $dr->concat($pr)->concat($drpr)
                ->map(fn($item) => [
                    'id' => $item->kd_jenis_prw . '_' . $item->tgl_perawatan . '_' . ($item->jam_rawat ?? ''),
                    'label' => $item->jnsPerawatan?->nm_perawatan ?? '-',
                    'date' => $item->tgl_perawatan . ' ' . ($item->jam_rawat ?? '')
                ])->values()->toArray();
        } elseif ($type === 'OBAT') {
            $items = DetailPemberianObat::with('barang')
                ->where('no_rawat', $no_rawat)
                ->get()
                ->groupBy('kode_brng')
                ->map(fn(\Illuminate\Support\Collection $groups, $kode) => [
                    'id' => $kode,
                    'label' => ($groups->first()->barang?->nama_brng ?? '-') . ' (Total: ' . $groups->sum('jml') . ' ' . ($groups->first()->barang?->kode_sat ?? '') . ')',
                    'name' => $groups->first()->barang?->nama_brng ?? '-'
                ])->values()->toArray();
        } elseif ($type === 'DIET') {
            $items = DetailBeriDiet::with('diet')
                ->where('no_rawat', $no_rawat)
                ->orderBy('tanggal', 'desc')
                ->get()
                ->map(fn($item) => [
                    'id' => $item->kd_diet . '_' . $item->tanggal . '_' . $item->waktu,
                    'label' => ($item->diet?->nama_diet ?? '-') . ' (' . $item->tanggal . ' ' . $item->waktu . ')',
                    'name' => $item->diet?->nama_diet ?? '-',
                    'date' => $item->tanggal . ' ' . $item->waktu
                ])->values()->toArray();
        } elseif ($type === 'LAB_PENDING') {
            $items = PermintaanLab::with('detailPemeriksaan.jnsPemeriksaan')
                ->where('no_rawat', $no_rawat)
                ->orderBy('tgl_permintaan', 'desc')
                ->get()
                ->map(fn($item) => [
                    'id' => $item->noorder,
                    'label' => 'Order: ' . $item->noorder . ' (' . $item->detailPemeriksaan->map(fn($d) => $d->jnsPemeriksaan->nm_perawatan ?? '-')->implode(', ') . ')',
                    'text' => $item->detailPemeriksaan->map(fn($d) => $d->jnsPemeriksaan->nm_perawatan ?? '-')->implode(', '),
                    'date' => $item->tgl_permintaan . ' ' . $item->jam_permintaan
                ])->values()->toArray();
        } elseif ($type === 'OBAT_PULANG') {
            $items = ResepPulang::with('barang')
                ->where('no_rawat', $no_rawat)
                ->orderBy('tanggal', 'desc')
                ->get()
                ->map(fn($item) => [
                    'id' => $item->kode_brng . '_' . $item->tanggal . '_' . $item->jam,
                    'label' => ($item->barang?->nama_brng ?? '-') . ' (' . $item->dosis . ')',
                    'text' => ($item->barang?->nama_brng ?? '-') . ' (' . $item->dosis . ')',
                    'date' => $item->tanggal . ' ' . $item->jam
                ])->values()->toArray();
        }

        return $items;
    }

    /**
     * Get paginated list of resumes for a patient.
     */
    public static function getResumesPagination(string $no_rkm_medis, int $perPage = 10)
    {
        return ResumePasienRanap::with([
            'regPeriksa.pasien',
            'regPeriksa.dokter',
            'regPeriksa.rujukMasuk',
            'regPeriksa.kamarInap' => function($query) {
                $query->orderBy('tgl_masuk', 'desc')->orderBy('jam_masuk', 'desc');
            }
        ])
        ->whereHas('regPeriksa', function($query) use ($no_rkm_medis) {
            $query->where('no_rkm_medis', $no_rkm_medis);
        })
        ->orderByDesc('no_rawat')
        ->paginate($perPage);
    }
}
