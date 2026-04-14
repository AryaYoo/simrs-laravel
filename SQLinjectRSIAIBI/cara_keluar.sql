ALTER TABLE `resume_pasien_ranap` 
MODIFY COLUMN `cara_keluar` enum(
    'Atas Izin Dokter',
    'Pindah RS',
    'Pulang Atas Permintaan Sendiri',
    'Dirujuk',
    'Lainnya'
) NOT NULL;