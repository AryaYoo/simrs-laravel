<?php

namespace App\Repositories\RawatJalan;

use Illuminate\Support\Facades\DB;
use App\Models\DiagnosaPasien;
use App\Models\ProsedurPasien;
use App\Models\Penyakit;
use App\Models\Icd9;

class DiagnosaProsedurRepository
{
    /**
     * Get Riwayat Diagnosa Pasien
     */
    public static function getDiagnosaPasien(string $no_rawat)
    {
        return DiagnosaPasien::with(['penyakit:kd_penyakit,nm_penyakit'])
            ->where('no_rawat', $no_rawat)
            ->where('status', 'Ralan')
            ->orderBy('prioritas', 'asc')
            ->get()
            ->map(fn($item) => [
                'no_rawat' => $item->no_rawat,
                'kd_penyakit' => $item->kd_penyakit,
                'nm_penyakit' => $item->penyakit->nm_penyakit ?? '-',
                'status' => $item->status,
                'kasus' => $item->status_penyakit,
                'prioritas' => $item->prioritas,
            ]);
    }

    /**
     * Search ICD-10 Master (Penyakit)
     */
    public static function searchIcd10(string $keyword)
    {
        return Penyakit::where(function ($query) use ($keyword) {
                $query->where('kd_penyakit', 'like', "%{$keyword}%")
                      ->orWhere('nm_penyakit', 'like', "%{$keyword}%");
            })
            ->select('kd_penyakit', 'nm_penyakit', 'keterangan')
            ->limit(50)
            ->get();
    }

    /**
     * Get Top 10 ICD-10 Master
     */
    public static function getTopIcd10()
    {
        return Penyakit::select('kd_penyakit', 'nm_penyakit', 'keterangan')
            ->limit(10)
            ->get();
    }

    /**
     * Save Diagnosa Pasien
     */
    public static function saveDiagnosa(array $data)
    {
        return DB::transaction(function () use ($data) {
            return DiagnosaPasien::create([
                'no_rawat' => $data['no_rawat'],
                'kd_penyakit' => $data['kd_penyakit'],
                'status' => 'Ralan',
                'prioritas' => $data['prioritas'],
                'status_penyakit' => $data['status_penyakit'],
            ]);
        });
    }

    /**
     * Save Multiple Diagnosa Pasien
     */
    public static function saveMultipleDiagnosa(string $no_rawat, array $diagnosas)
    {
        return DB::transaction(function () use ($no_rawat, $diagnosas) {
            foreach ($diagnosas as $data) {
                // Check if exists to avoid duplicate
                $exists = DiagnosaPasien::where('no_rawat', $no_rawat)
                    ->where('kd_penyakit', $data['kd_penyakit'])
                    ->exists();

                if (!$exists) {
                    DiagnosaPasien::create([
                        'no_rawat' => $no_rawat,
                        'kd_penyakit' => $data['kd_penyakit'],
                        'status' => 'Ralan',
                        'prioritas' => $data['prioritas'],
                        'status_penyakit' => $data['status_penyakit'],
                    ]);
                }
            }
            return true;
        });
    }

    /**
     * Delete Diagnosa Pasien
     */
    public static function deleteDiagnosa(string $no_rawat, string $kd_penyakit)
    {
        return DB::transaction(function () use ($no_rawat, $kd_penyakit) {
            return DiagnosaPasien::where('no_rawat', $no_rawat)
                ->where('kd_penyakit', $kd_penyakit)
                ->where('status', 'Ralan')
                ->delete();
        });
    }

    /**
     * Get Riwayat Prosedur Pasien
     */
    public static function getProsedurPasien(string $no_rawat)
    {
        return ProsedurPasien::with(['icd9:kode,deskripsi_panjang'])
            ->where('no_rawat', $no_rawat)
            ->where('status', 'Ralan')
            ->orderBy('prioritas', 'asc')
            ->get()
            ->map(fn($item) => [
                'no_rawat' => $item->no_rawat,
                'kode' => $item->kode,
                'nm_prosedur' => $item->icd9->deskripsi_panjang ?? '-',
                'status' => $item->status,
                'prioritas' => $item->prioritas,
                'jumlah' => $item->jumlah,
            ]);
    }

    /**
     * Search ICD-9 Master (Prosedur)
     */
    public static function searchIcd9(string $keyword)
    {
        return Icd9::where('kode', 'like', "%{$keyword}%")
            ->orWhere('deskripsi_panjang', 'like', "%{$keyword}%")
            ->select('kode', 'deskripsi_panjang', 'deskripsi_pendek')
            ->limit(50)
            ->get();
    }

    /**
     * Get Top 10 ICD-9 Master
     */
    public static function getTopIcd9()
    {
        return Icd9::select('kode', 'deskripsi_panjang', 'deskripsi_pendek')
            ->limit(10)
            ->get();
    }

    /**
     * Save Prosedur Pasien
     */
    public static function saveProsedur(array $data)
    {
        return DB::transaction(function () use ($data) {
            return ProsedurPasien::create([
                'no_rawat' => $data['no_rawat'],
                'kode' => $data['kode'],
                'status' => 'Ralan',
                'prioritas' => $data['prioritas'],
                'jumlah' => $data['jumlah'] ?? 1,
            ]);
        });
    }

    /**
     * Save Multiple Prosedur Pasien
     */
    public static function saveMultipleProsedur(string $no_rawat, array $prosedurs)
    {
        return DB::transaction(function () use ($no_rawat, $prosedurs) {
            foreach ($prosedurs as $data) {
                $exists = ProsedurPasien::where('no_rawat', $no_rawat)
                    ->where('kode', $data['kode'])
                    ->exists();

                if (!$exists) {
                    ProsedurPasien::create([
                        'no_rawat' => $no_rawat,
                        'kode' => $data['kode'],
                        'status' => 'Ralan',
                        'prioritas' => $data['prioritas'],
                        'jumlah' => $data['jumlah'] ?? 1,
                    ]);
                }
            }
            return true;
        });
    }

    /**
     * Delete Prosedur Pasien
     */
    public static function deleteProsedur(string $no_rawat, string $kode)
    {
        return DB::transaction(function () use ($no_rawat, $kode) {
            return ProsedurPasien::where('no_rawat', $no_rawat)
                ->where('kode', $kode)
                ->where('status', 'Ralan')
                ->delete();
        });
    }
}
