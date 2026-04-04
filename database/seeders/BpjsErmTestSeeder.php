<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BpjsErmTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $nokaSakti = '0002035875352'; // Noka Sandbox BPJS
        
        // AMBIL PASIEN PERTAMA YANG ADA DI DATABASE BIAR AMAN DARI ERROR KOLOM
        $pasien = DB::table('pasien')->first();
        
        if (!$pasien) {
            echo "Error: Tidak ada data pasien sama sekali di database untuk diupdate.\n";
            return;
        }

        $noMr    = $pasien->no_rkm_medis;
        $noRawat = '2026/04/01/999111';
        $noSep   = '20260401999111';

        // 1. Update Noka Pasien Eksis (Biar BPJS ngenalin)
        DB::table('pasien')->where('no_rkm_medis', $noMr)->update([
            'no_peserta' => $nokaSakti
        ]);

        // 2. Insert/Update Reg Periksa
        DB::table('reg_periksa')->updateOrInsert(['no_rawat' => $noRawat], [
            'no_reg'        => '001',
            'no_rawat'      => $noRawat,
            'tgl_registrasi'=> now()->format('Y-m-d'),
            'jam_reg'       => now()->format('H:i:s'),
            'kd_dokter'     => (DB::table('dokter')->value('kd_dokter') ?? '-'),
            'no_rkm_medis'  => $noMr,
            'kd_poli'       => (DB::table('poliklinik')->value('kd_poli') ?? '-'),
            'p_jawab'       => 'Pindah Tangan',
            'almt_pj'       => '-',
            'hubunganpj'    => '-',
            'biaya_reg'     => 0,
            'stts'          => 'Belum',
            'stts_daftar'   => 'Lama',
            'status_lanjut' => 'Ralan',
            'kd_pj'         => 'BPJ',
            'status_bayar'  => 'Belum Bayar',
            'status_poli'   => 'Baru',
        ]);

        // 3. Insert/Update Bridging SEP
        $defaults = [
            'no_sep'           => $noSep,
            'no_rawat'         => $noRawat,
            'tglsep'           => now()->format('Y-m-d'),
            'jnspelayanan'     => '2',
            'nomr'             => $noMr,
            'nama_pasien'      => 'PASIEN SANDBOX BPJS',
            'diagawal'         => 'I10',
            'nmdiagnosaawal'   => 'Essential Hypertension',
            'tglkkl'           => '2000-01-01',
            'keterangankkl'    => '-',
            'suplesi'          => '0. Tidak',
            'no_sep_suplesi'   => '-',
            'kdprop'           => '-',
            'nmprop'           => '-',
            'kdkab'            => '-',
            'nmkab'            => '-',
            'kdkec'            => '-',
            'nmkec'            => '-',
            'noskdp'           => '-',
            'kddpjp'           => '-',
            'nmdpdjp'          => '-',
            'tujuankunjungan'  => '0',
            'flagprosedur'     => '',
            'klsnaik'          => '',
            'penunjang'        => '',
            'asesmenpelayanan' => '',
            'kddpjplayanan'    => '-',
            'nmdpjplayanan'    => '-',
            'tglrujukan'       => '2000-01-01',
            'pjnaikkelas'      => '-',
            'pembiayaan'       => '',
            'asal_rujukan'     => '1. Faskes 1',
            'eksekutif'        => '0. Tidak',
            'cob'              => '0. Tidak',
            'notelep'          => '-',
            'katarak'          => '0. Tidak',
        ];

        DB::table('bridging_sep')->updateOrInsert(['no_sep' => $noSep], $defaults);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        echo "Magic Seeder for Sandbox Patient ($nokaSakti) completed!\n";
    }
}
