<?php

namespace App\Repositories\RawatJalan;

use App\Models\DataTriaseIgd;
use App\Models\DataTriaseIgdPrimer;
use App\Models\DataTriaseIgdSekunder;
use App\Models\DataTriaseIgdDetailSkala1;
use App\Models\DataTriaseIgdDetailSkala2;
use App\Models\DataTriaseIgdDetailSkala3;
use App\Models\DataTriaseIgdDetailSkala4;
use App\Models\DataTriaseIgdDetailSkala5;
use App\Models\MasterTriasePemeriksaan;
use App\Models\MasterTriaseMacamKasus;
use App\Models\RegPeriksa;
use Illuminate\Support\Facades\DB;

class TriaseIgdRepository
{
    /**
     * Get registration detail
     */
    public static function getRegPeriksa(string $no_rawat)
    {
        return RegPeriksa::with(['pasien', 'dokter', 'poliklinik', 'penjab'])
            ->where('no_rawat', $no_rawat)
            ->first();
    }

    /**
     * Get triage data for a specific registration
     */
    public static function getTriaseData(string $no_rawat)
    {
        return DataTriaseIgd::with('macamKasus')
            ->where('no_rawat', $no_rawat)
            ->first();
    }

    /**
     * Get all triage records for the patient (history)
     */
    public static function getTriaseHistory(string $no_rkm_medis)
    {
        return DataTriaseIgd::whereHas('regPeriksa', function($query) use ($no_rkm_medis) {
            $query->where('no_rkm_medis', $no_rkm_medis);
        })
        ->with('macamKasus')
        ->orderBy('tgl_kunjungan', 'desc')
        ->get();
    }

    /**
     * Get master data for Macam Kasus
     */
    public static function getMacamKasus()
    {
        return MasterTriaseMacamKasus::orderBy('macam_kasus', 'asc')->get();
    }

    /**
     * Save or update triage data
     */
    public static function saveTriase(array $data)
    {
        return DB::transaction(function () use ($data) {
            return DataTriaseIgd::updateOrCreate(
                ['no_rawat' => $data['no_rawat']],
                $data
            );
        });
    }

    /**
     * Delete triage data
     */
    public static function deleteTriase(string $no_rawat)
    {
        return DB::transaction(function () use ($no_rawat) {
            DataTriaseIgdPrimer::where('no_rawat', $no_rawat)->delete();
            DataTriaseIgdSekunder::where('no_rawat', $no_rawat)->delete();
            DataTriaseIgdDetailSkala1::where('no_rawat', $no_rawat)->delete();
            DataTriaseIgdDetailSkala2::where('no_rawat', $no_rawat)->delete();
            DataTriaseIgdDetailSkala3::where('no_rawat', $no_rawat)->delete();
            DataTriaseIgdDetailSkala4::where('no_rawat', $no_rawat)->delete();
            DataTriaseIgdDetailSkala5::where('no_rawat', $no_rawat)->delete();
            return DataTriaseIgd::where('no_rawat', $no_rawat)->delete();
        });
    }

    /**
     * Get Master Pemeriksaan with scales
     */
    public static function getMasterPemeriksaan()
    {
        return MasterTriasePemeriksaan::with(['skala1', 'skala2', 'skala3', 'skala4', 'skala5'])->get();
    }

    /**
     * Get Triase Primer data
     */
    public static function getTriasePrimer(string $no_rawat)
    {
        return DataTriaseIgdPrimer::where('no_rawat', $no_rawat)->first();
    }

    /**
     * Get Triase Sekunder data
     */
    public static function getTriaseSekunder(string $no_rawat)
    {
        return DataTriaseIgdSekunder::where('no_rawat', $no_rawat)->first();
    }

    /**
     * Get Selected Scales
     */
    public static function getSelectedScales(string $no_rawat)
    {
        return [
            'skala1' => DataTriaseIgdDetailSkala1::where('no_rawat', $no_rawat)->pluck('kode_skala1')->toArray(),
            'skala2' => DataTriaseIgdDetailSkala2::where('no_rawat', $no_rawat)->pluck('kode_skala2')->toArray(),
            'skala3' => DataTriaseIgdDetailSkala3::where('no_rawat', $no_rawat)->pluck('kode_skala3')->toArray(),
            'skala4' => DataTriaseIgdDetailSkala4::where('no_rawat', $no_rawat)->pluck('kode_skala4')->toArray(),
            'skala5' => DataTriaseIgdDetailSkala5::where('no_rawat', $no_rawat)->pluck('kode_skala5')->toArray(),
        ];
    }

    /**
     * Save All Triage Assessment Data (Primer & Sekunder)
     */
    public static function saveFullAssessment(string $no_rawat, array $primer, array $sekunder, array $scales)
    {
        return DB::transaction(function () use ($no_rawat, $primer, $sekunder, $scales) {
            // Save Primer
            DataTriaseIgdPrimer::updateOrCreate(['no_rawat' => $no_rawat], $primer);

            // Save Sekunder
            DataTriaseIgdSekunder::updateOrCreate(['no_rawat' => $no_rawat], $sekunder);

            // Sync Scales
            $scaleModels = [
                'skala1' => [DataTriaseIgdDetailSkala1::class, 'kode_skala1'],
                'skala2' => [DataTriaseIgdDetailSkala2::class, 'kode_skala2'],
                'skala3' => [DataTriaseIgdDetailSkala3::class, 'kode_skala3'],
                'skala4' => [DataTriaseIgdDetailSkala4::class, 'kode_skala4'],
                'skala5' => [DataTriaseIgdDetailSkala5::class, 'kode_skala5'],
            ];

            foreach ($scaleModels as $key => $config) {
                $model = $config[0];
                $field = $config[1];
                $model::where('no_rawat', $no_rawat)->delete();
                foreach ($scales[$key] ?? [] as $kode) {
                    $model::create(['no_rawat' => $no_rawat, $field => $kode]);
                }
            }
        });
    }
}
