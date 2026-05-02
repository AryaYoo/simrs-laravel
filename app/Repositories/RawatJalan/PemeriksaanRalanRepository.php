<?php

namespace App\Repositories\RawatJalan;

use App\Models\PemeriksaanRalan;

class PemeriksaanRalanRepository
{
    /**
     * Save or update pemeriksaan ralan
     */
    public static function save(array $data)
    {
        $isEditMode = $data['isEditMode'] ?? false;
        
        // Remove helper fields
        unset($data['isEditMode']);
        
        if ($isEditMode) {
            $pemeriksaan = PemeriksaanRalan::where('no_rawat', $data['no_rawat'])
                ->where('tgl_perawatan', $data['tgl_perawatan'])
                ->where('jam_rawat', $data['jam_rawat'])
                ->first();
                
            if ($pemeriksaan) {
                $pemeriksaan->update($data);
            } else {
                throw new \Exception("Data pemeriksaan tidak ditemukan untuk diupdate.");
            }
        } else {
            PemeriksaanRalan::create($data);
        }
    }

    /**
     * Delete pemeriksaan ralan
     */
    public static function delete(string $no_rawat, string $tgl_perawatan, string $jam_rawat)
    {
        $pemeriksaan = PemeriksaanRalan::where('no_rawat', $no_rawat)
            ->where('tgl_perawatan', $tgl_perawatan)
            ->where('jam_rawat', $jam_rawat)
            ->first();

        if ($pemeriksaan) {
            $pemeriksaan->delete();
        } else {
            throw new \Exception("Data pemeriksaan tidak ditemukan.");
        }
    }
}
