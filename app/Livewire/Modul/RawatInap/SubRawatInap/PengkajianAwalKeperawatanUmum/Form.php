<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\PengkajianAwalKeperawatanUmum;

use Livewire\Component;
use App\Models\RegPeriksa;
use App\Livewire\Concerns\WithOptimisticLocking;
use App\Repositories\RawatInap\PengkajianAwalKeperawatanUmumRepository;

class Form extends Component
{
    use WithOptimisticLocking;

    public $noRawat;
    public $regPeriksa;
    public $isEditMode = false;

    // Petugas & Dokter
    public $nip1, $nmPetugas1, $petugas1Search = '';
    public $nip2, $nmPetugas2, $petugas2Search = '';
    public $kd_dokter, $nmDokter, $dokterSearch = '';

    // Section 1: Identitas
    public $tanggal, $informasi = 'Autoanamnesis', $ket_informasi = '';
    public $tiba_diruang_rawat = 'Jalan Tanpa Bantuan', $kasus_trauma = 'Non Trauma', $cara_masuk = 'Poli';

    // Section 2: Riwayat Kesehatan
    public $rps = '', $rpd = '', $rpk = '', $rpo = '';
    public $riwayat_pembedahan = '', $riwayat_dirawat_dirs = '', $alat_bantu_dipakai = 'Kacamata';
    public $riwayat_kehamilan = 'Tidak', $riwayat_kehamilan_perkiraan = '';
    public $riwayat_tranfusi = '', $riwayat_alergi = '';
    public $riwayat_merokok = 'Tidak', $riwayat_merokok_jumlah = '';
    public $riwayat_alkohol = 'Tidak', $riwayat_alkohol_jumlah = '';
    public $riwayat_narkoba = 'Tidak', $riwayat_olahraga = 'Tidak';

    // Section 3: Pemeriksaan Fisik
    public $pemeriksaan_mental = '', $pemeriksaan_keadaan_umum = 'Baik', $pemeriksaan_gcs = '';
    public $pemeriksaan_td = '', $pemeriksaan_nadi = '', $pemeriksaan_rr = '', $pemeriksaan_suhu = '';
    public $pemeriksaan_spo2 = '', $pemeriksaan_bb = '', $pemeriksaan_tb = '';
    public $pemeriksaan_susunan_kepala = 'TAK', $pemeriksaan_susunan_kepala_keterangan = '';
    public $pemeriksaan_susunan_wajah = 'TAK', $pemeriksaan_susunan_wajah_keterangan = '';
    public $pemeriksaan_susunan_leher = 'TAK', $pemeriksaan_susunan_kejang = 'TAK', $pemeriksaan_susunan_kejang_keterangan = '', $pemeriksaan_susunan_sensorik = 'TAK';
    public $pemeriksaan_kardiovaskuler_denyut_nadi = 'Teratur', $pemeriksaan_kardiovaskuler_sirkulasi = 'Akral Hangat', $pemeriksaan_kardiovaskuler_sirkulasi_keterangan = '', $pemeriksaan_kardiovaskuler_pulsasi = 'Kuat';
    public $pemeriksaan_respirasi_pola_nafas = 'Normal', $pemeriksaan_respirasi_retraksi = 'Tidak Ada', $pemeriksaan_respirasi_suara_nafas = 'Vesikuler', $pemeriksaan_respirasi_volume_pernafasan = 'Normal', $pemeriksaan_respirasi_jenis_pernafasan = 'Pernafasan Dada', $pemeriksaan_respirasi_jenis_pernafasan_keterangan = '', $pemeriksaan_respirasi_irama_nafas = 'Teratur', $pemeriksaan_respirasi_batuk = 'Tidak';
    public $pemeriksaan_gastrointestinal_mulut = 'TAK', $pemeriksaan_gastrointestinal_mulut_keterangan = '', $pemeriksaan_gastrointestinal_gigi = 'TAK', $pemeriksaan_gastrointestinal_gigi_keterangan = '', $pemeriksaan_gastrointestinal_lidah = 'TAK', $pemeriksaan_gastrointestinal_lidah_keterangan = '', $pemeriksaan_gastrointestinal_tenggorokan = 'TAK', $pemeriksaan_gastrointestinal_tenggorokan_keterangan = '', $pemeriksaan_gastrointestinal_abdomen = 'Supel', $pemeriksaan_gastrointestinal_abdomen_keterangan = '', $pemeriksaan_gastrointestinal_peistatik_usus = 'TAK', $pemeriksaan_gastrointestinal_anus = 'TAK';
    public $pemeriksaan_neurologi_pengelihatan = 'TAK', $pemeriksaan_neurologi_pengelihatan_keterangan = '', $pemeriksaan_neurologi_alat_bantu_penglihatan = 'Tidak', $pemeriksaan_neurologi_pendengaran = 'TAK', $pemeriksaan_neurologi_bicara = 'Jelas', $pemeriksaan_neurologi_bicara_keterangan = '', $pemeriksaan_neurologi_sensorik = 'TAK', $pemeriksaan_neurologi_motorik = 'TAK', $pemeriksaan_neurologi_kekuatan_otot = 'Kuat';
    public $pemeriksaan_integument_warnakulit = 'Normal', $pemeriksaan_integument_turgor = 'Baik', $pemeriksaan_integument_kulit = 'Normal', $pemeriksaan_integument_dekubitas = 'Tidak Ada';
    public $pemeriksaan_muskuloskletal_pergerakan_sendi = 'Bebas', $pemeriksaan_muskuloskletal_kekauatan_otot = 'Baik', $pemeriksaan_muskuloskletal_nyeri_sendi = 'Tidak Ada', $pemeriksaan_muskuloskletal_nyeri_sendi_keterangan = '', $pemeriksaan_muskuloskletal_oedema = 'Tidak Ada', $pemeriksaan_muskuloskletal_oedema_keterangan = '', $pemeriksaan_muskuloskletal_fraktur = 'Tidak Ada', $pemeriksaan_muskuloskletal_fraktur_keterangan = '';
    public $pemeriksaan_eliminasi_bab_frekuensi_jumlah = '', $pemeriksaan_eliminasi_bab_frekuensi_durasi = '', $pemeriksaan_eliminasi_bab_konsistensi = '', $pemeriksaan_eliminasi_bab_warna = '';
    public $pemeriksaan_eliminasi_bak_frekuensi_jumlah = '', $pemeriksaan_eliminasi_bak_frekuensi_durasi = '', $pemeriksaan_eliminasi_bak_warna = '', $pemeriksaan_eliminasi_bak_lainlain = '';

    // Section 4: Pola Kehidupan Sehari-hari
    public $pola_aktifitas_makanminum = 'Mandiri', $pola_aktifitas_mandi = 'Mandiri', $pola_aktifitas_eliminasi = 'Mandiri', $pola_aktifitas_berpakaian = 'Mandiri', $pola_aktifitas_berpindah = 'Mandiri';
    public $pola_nutrisi_frekuesi_makan = '', $pola_nutrisi_jenis_makanan = '', $pola_nutrisi_porsi_makan = '';
    public $pola_tidur_lama_tidur = '', $pola_tidur_gangguan = 'Tidak Ada Gangguan';

    // Section 5: Pengkajian Fungsi
    public $pengkajian_fungsi_kemampuan_sehari = 'Mandiri', $pengkajian_fungsi_aktifitas = 'Tirah Baring', $pengkajian_fungsi_berjalan = 'TAK', $pengkajian_fungsi_berjalan_keterangan = '', $pengkajian_fungsi_ambulasi = 'Tidak Menggunakan', $pengkajian_fungsi_ekstrimitas_atas = 'TAK', $pengkajian_fungsi_ekstrimitas_atas_keterangan = '', $pengkajian_fungsi_ekstrimitas_bawah = 'TAK', $pengkajian_fungsi_ekstrimitas_bawah_keterangan = '', $pengkajian_fungsi_menggenggam = 'Tidak Ada Kesulitan', $pengkajian_fungsi_menggenggam_keterangan = '', $pengkajian_fungsi_koordinasi = 'Tidak Ada Kesulitan', $pengkajian_fungsi_koordinasi_keterangan = '', $pengkajian_fungsi_kesimpulan = 'Tidak (Tidak Perlu Co DPJP)';

    // Section 6: Riwayat Psikososial
    public $riwayat_psiko_kondisi_psiko = 'Tidak Ada Masalah', $riwayat_psiko_perilaku = 'Tidak Ada Masalah', $riwayat_psiko_perilaku_keterangan = '', $riwayat_psiko_gangguan_jiwa = 'Tidak', $riwayat_psiko_hubungan_keluarga = 'Harmonis', $riwayat_psiko_agama = 'ISLAM', $riwayat_psiko_tinggal = 'Sendiri', $riwayat_psiko_tinggal_keterangan = '', $riwayat_psiko_pekerjaan = '', $riwayat_psiko_pembayaran = 'BPJS', $riwayat_psiko_nilai_kepercayaan = 'Tidak Ada', $riwayat_psiko_nilai_kepercayaan_keterangan = '', $riwayat_psiko_bahasa = 'INDONESIA', $riwayat_psiko_pendidikan = '-', $riwayat_psiko_pendidikan_pj = '-', $riwayat_psiko_edukasi_diberikan = 'Pasien', $riwayat_psiko_edukasi_diberikan_keterangan = '';

    // Section 7: Pengkajian Tingkat Nyeri
    public $penilaian_nyeri = 'Tidak Ada Nyeri', $penilaian_nyeri_penyebab = 'Proses Penyakit', $penilaian_nyeri_ket_penyebab = '', $penilaian_nyeri_kualitas = 'Seperti Tertusuk', $penilaian_nyeri_ket_kualitas = '', $penilaian_nyeri_lokasi = '', $penilaian_nyeri_menyebar = 'Tidak', $penilaian_nyeri_skala = '0', $penilaian_nyeri_waktu = '', $penilaian_nyeri_hilang = 'Istirahat', $penilaian_nyeri_ket_hilang = '', $penilaian_nyeri_diberitahukan_dokter = 'Tidak', $penilaian_nyeri_jam_diberitahukan_dokter = '';

    // Section 8: Pengkajian Risiko Jatuh
    public $penilaian_jatuhmorse_skala1 = 'Tidak', $penilaian_jatuhmorse_nilai1 = 0;
    public $penilaian_jatuhmorse_skala2 = 'Tidak', $penilaian_jatuhmorse_nilai2 = 0;
    public $penilaian_jatuhmorse_skala3 = 'Tidak Ada/Kursi Roda/Perawat/Tirah Baring', $penilaian_jatuhmorse_nilai3 = 0;
    public $penilaian_jatuhmorse_skala4 = 'Tidak', $penilaian_jatuhmorse_nilai4 = 0;
    public $penilaian_jatuhmorse_skala5 = 'Normal/Tirah Baring/Imobilisasi', $penilaian_jatuhmorse_nilai5 = 0;
    public $penilaian_jatuhmorse_skala6 = 'Sadar Akan Kemampuan Diri Sendiri', $penilaian_jatuhmorse_nilai6 = 0;
    public $penilaian_jatuhmorse_totalnilai = 0;

    public $penilaian_jatuhsydney_skala1 = 'Tidak', $penilaian_jatuhsydney_nilai1 = 0;
    public $penilaian_jatuhsydney_skala2 = 'Tidak', $penilaian_jatuhsydney_nilai2 = 0;
    public $penilaian_jatuhsydney_skala3 = 'Tidak', $penilaian_jatuhsydney_nilai3 = 0;
    public $penilaian_jatuhsydney_skala4 = 'Tidak', $penilaian_jatuhsydney_nilai4 = 0;
    public $penilaian_jatuhsydney_skala5 = 'Tidak', $penilaian_jatuhsydney_nilai5 = 0;
    public $penilaian_jatuhsydney_skala6 = 'Tidak', $penilaian_jatuhsydney_nilai6 = 0;
    public $penilaian_jatuhsydney_skala7 = 'Tidak', $penilaian_jatuhsydney_nilai7 = 0;
    public $penilaian_jatuhsydney_skala8 = 'Tidak', $penilaian_jatuhsydney_nilai8 = 0;
    public $penilaian_jatuhsydney_skala9 = 'Tidak', $penilaian_jatuhsydney_nilai9 = 0;
    public $penilaian_jatuhsydney_skala10 = 'Tidak', $penilaian_jatuhsydney_nilai10 = 0;
    public $penilaian_jatuhsydney_skala11 = 'Tidak', $penilaian_jatuhsydney_nilai11 = 0;
    public $penilaian_jatuhsydney_totalnilai = 0;

    // Section 9: Skrining Gizi
    public $skrining_gizi1 = 'Tidak ada penurunan berat badan', $nilai_gizi1 = 0, $skrining_gizi2 = 'Tidak', $nilai_gizi2 = 0, $nilai_total_gizi = 0;
    public $skrining_gizi_diagnosa_khusus = 'Tidak', $skrining_gizi_ket_diagnosa_khusus = '', $skrining_gizi_diketahui_dietisen = 'Tidak', $skrining_gizi_jam_diketahui_dietisen = '';

    // Section 10: Masalah & Rencana Keperawatan
    public $selectedMasalah = [];
    public $selectedRencana = [];
    public $rencana = ''; // Rencana Lainnya (free text)

    // Master data
    public $masterMasalah = [];
    public $availableRencana = [];

    public function mount($no_rawat, PengkajianAwalKeperawatanUmumRepository $repository)
    {
        $this->noRawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'kamarInap.kamar'])
            ->where('no_rawat', $this->noRawat)
            ->firstOrFail();

        $this->initializeLock($this->regPeriksa);
        $this->tanggal = now()->format('Y-m-d H:i:s');
        
        $this->kd_dokter = $this->regPeriksa->kd_dokter;
        $this->nmDokter = $this->regPeriksa->dokter->nm_dokter ?? '';

        // Load master data
        $this->masterMasalah = $repository->getMasterMasalah()->toArray();

        // Default auto-fill petugas 1 from logged-in user
        $loggedInUsername = auth()->user()->username ?? null;
        if ($loggedInUsername) {
            $pegawai = \App\Models\Pegawai::find($loggedInUsername);
            if ($pegawai) {
                $this->nip1 = $pegawai->nik;
                $this->nmPetugas1 = $pegawai->nama;
            }
        }

        // Load existing data if exists
        $existing = $repository->getByNoRawat($this->noRawat);
        if ($existing) {
            $this->isEditMode = true;
            $this->fillFromExisting($existing);
        }
    }

    private function fillFromExisting($data)
    {
        $attributes = [
            'tanggal', 'informasi', 'ket_informasi', 'tiba_diruang_rawat', 'kasus_trauma', 'cara_masuk',
            'rps', 'rpd', 'rpk', 'rpo', 'riwayat_pembedahan', 'riwayat_dirawat_dirs', 'alat_bantu_dipakai',
            'riwayat_kehamilan', 'riwayat_kehamilan_perkiraan', 'riwayat_tranfusi', 'riwayat_alergi',
            'riwayat_merokok', 'riwayat_merokok_jumlah', 'riwayat_alkohol', 'riwayat_alkohol_jumlah',
            'riwayat_narkoba', 'riwayat_olahraga', 'pemeriksaan_mental', 'pemeriksaan_keadaan_umum',
            'pemeriksaan_gcs', 'pemeriksaan_td', 'pemeriksaan_nadi', 'pemeriksaan_rr', 'pemeriksaan_suhu',
            'pemeriksaan_spo2', 'pemeriksaan_bb', 'pemeriksaan_tb', 'pemeriksaan_susunan_kepala',
            'pemeriksaan_susunan_kepala_keterangan', 'pemeriksaan_susunan_wajah', 'pemeriksaan_susunan_wajah_keterangan',
            'pemeriksaan_susunan_leher', 'pemeriksaan_susunan_kejang', 'pemeriksaan_susunan_kejang_keterangan',
            'pemeriksaan_susunan_sensorik', 'pemeriksaan_kardiovaskuler_denyut_nadi', 'pemeriksaan_kardiovaskuler_sirkulasi',
            'pemeriksaan_kardiovaskuler_sirkulasi_keterangan', 'pemeriksaan_kardiovaskuler_pulsasi',
            'pemeriksaan_respirasi_pola_nafas', 'pemeriksaan_respirasi_retraksi', 'pemeriksaan_respirasi_suara_nafas',
            'pemeriksaan_respirasi_volume_pernafasan', 'pemeriksaan_respirasi_jenis_pernafasan',
            'pemeriksaan_respirasi_jenis_pernafasan_keterangan', 'pemeriksaan_respirasi_irama_nafas',
            'pemeriksaan_respirasi_batuk', 'pemeriksaan_gastrointestinal_mulut', 'pemeriksaan_gastrointestinal_mulut_keterangan',
            'pemeriksaan_gastrointestinal_gigi', 'pemeriksaan_gastrointestinal_gigi_keterangan', 'pemeriksaan_gastrointestinal_lidah',
            'pemeriksaan_gastrointestinal_lidah_keterangan', 'pemeriksaan_gastrointestinal_tenggorokan',
            'pemeriksaan_gastrointestinal_tenggorokan_keterangan', 'pemeriksaan_gastrointestinal_abdomen',
            'pemeriksaan_gastrointestinal_abdomen_keterangan', 'pemeriksaan_gastrointestinal_peistatik_usus',
            'pemeriksaan_gastrointestinal_anus', 'pemeriksaan_neurologi_pengelihatan', 'pemeriksaan_neurologi_pengelihatan_keterangan',
            'pemeriksaan_neurologi_alat_bantu_penglihatan', 'pemeriksaan_neurologi_pendengaran', 'pemeriksaan_neurologi_bicara',
            'pemeriksaan_neurologi_bicara_keterangan', 'pemeriksaan_neurologi_sensorik', 'pemeriksaan_neurologi_motorik',
            'pemeriksaan_neurologi_kekuatan_otot', 'pemeriksaan_integument_warnakulit', 'pemeriksaan_integument_turgor',
            'pemeriksaan_integument_kulit', 'pemeriksaan_integument_dekubitas', 'pemeriksaan_muskuloskletal_pergerakan_sendi',
            'pemeriksaan_muskuloskletal_kekauatan_otot', 'pemeriksaan_muskuloskletal_nyeri_sendi',
            'pemeriksaan_muskuloskletal_nyeri_sendi_keterangan', 'pemeriksaan_muskuloskletal_oedema',
            'pemeriksaan_muskuloskletal_oedema_keterangan', 'pemeriksaan_muskuloskletal_fraktur',
            'pemeriksaan_muskuloskletal_fraktur_keterangan', 'pemeriksaan_eliminasi_bab_frekuensi_jumlah',
            'pemeriksaan_eliminasi_bab_frekuensi_durasi', 'pemeriksaan_eliminasi_bab_konsistensi',
            'pemeriksaan_eliminasi_bab_warna', 'pemeriksaan_eliminasi_bak_frekuensi_jumlah',
            'pemeriksaan_eliminasi_bak_frekuensi_durasi', 'pemeriksaan_eliminasi_bak_warna',
            'pemeriksaan_eliminasi_bak_lainlain', 'pola_aktifitas_makanminum', 'pola_aktifitas_mandi',
            'pola_aktifitas_eliminasi', 'pola_aktifitas_berpakaian', 'pola_aktifitas_berpindah',
            'pola_nutrisi_frekuesi_makan', 'pola_nutrisi_jenis_makanan', 'pola_nutrisi_porsi_makan',
            'pola_tidur_lama_tidur', 'pola_tidur_gangguan', 'pengkajian_fungsi_kemampuan_sehari',
            'pengkajian_fungsi_aktifitas', 'pengkajian_fungsi_berjalan', 'pengkajian_fungsi_berjalan_keterangan',
            'pengkajian_fungsi_ambulasi', 'pengkajian_fungsi_ekstrimitas_atas', 'pengkajian_fungsi_ekstrimitas_atas_keterangan',
            'pengkajian_fungsi_ekstrimitas_bawah', 'pengkajian_fungsi_ekstrimitas_bawah_keterangan',
            'pengkajian_fungsi_menggenggam', 'pengkajian_fungsi_menggenggam_keterangan', 'pengkajian_fungsi_koordinasi',
            'pengkajian_fungsi_koordinasi_keterangan', 'pengkajian_fungsi_kesimpulan', 'riwayat_psiko_kondisi_psiko',
            'riwayat_psiko_perilaku', 'riwayat_psiko_perilaku_keterangan', 'riwayat_psiko_gangguan_jiwa',
            'riwayat_psiko_hubungan_keluarga', 'riwayat_psiko_tinggal', 'riwayat_psiko_tinggal_keterangan',
            'riwayat_psiko_nilai_kepercayaan', 'riwayat_psiko_nilai_kepercayaan_keterangan',
            'riwayat_psiko_pendidikan_pj', 'riwayat_psiko_edukasi_diberikan', 'riwayat_psiko_edukasi_diberikan_keterangan',
            'penilaian_nyeri', 'penilaian_nyeri_penyebab', 'penilaian_nyeri_ket_penyebab', 'penilaian_nyeri_kualitas',
            'penilaian_nyeri_ket_kualitas', 'penilaian_nyeri_lokasi', 'penilaian_nyeri_menyebar', 'penilaian_nyeri_skala',
            'penilaian_nyeri_waktu', 'penilaian_nyeri_hilang', 'penilaian_nyeri_ket_hilang', 'penilaian_nyeri_diberitahukan_dokter',
            'penilaian_nyeri_jam_diberitahukan_dokter', 'penilaian_jatuhmorse_skala1', 'penilaian_jatuhmorse_nilai1',
            'penilaian_jatuhmorse_skala2', 'penilaian_jatuhmorse_nilai2', 'penilaian_jatuhmorse_skala3',
            'penilaian_jatuhmorse_nilai3', 'penilaian_jatuhmorse_skala4', 'penilaian_jatuhmorse_nilai4',
            'penilaian_jatuhmorse_skala5', 'penilaian_jatuhmorse_nilai5', 'penilaian_jatuhmorse_skala6',
            'penilaian_jatuhmorse_nilai6', 'penilaian_jatuhmorse_totalnilai', 'penilaian_jatuhsydney_skala1',
            'penilaian_jatuhsydney_nilai1', 'penilaian_jatuhsydney_skala2', 'penilaian_jatuhsydney_nilai2',
            'penilaian_jatuhsydney_skala3', 'penilaian_jatuhsydney_nilai3', 'penilaian_jatuhsydney_skala4',
            'penilaian_jatuhsydney_nilai4', 'penilaian_jatuhsydney_skala5', 'penilaian_jatuhsydney_nilai5',
            'penilaian_jatuhsydney_skala6', 'penilaian_jatuhsydney_nilai6', 'penilaian_jatuhsydney_skala7',
            'penilaian_jatuhsydney_nilai7', 'penilaian_jatuhsydney_skala8', 'penilaian_jatuhsydney_nilai8',
            'penilaian_jatuhsydney_skala9', 'penilaian_jatuhsydney_nilai9', 'penilaian_jatuhsydney_skala10',
            'penilaian_jatuhsydney_nilai10', 'penilaian_jatuhsydney_skala11', 'penilaian_jatuhsydney_nilai11',
            'penilaian_jatuhsydney_totalnilai', 'skrining_gizi1', 'nilai_gizi1', 'skrining_gizi2', 'nilai_gizi2',
            'nilai_total_gizi', 'skrining_gizi_diagnosa_khusus', 'skrining_gizi_ket_diagnosa_khusus',
            'skrining_gizi_diketahui_dietisen', 'skrining_gizi_jam_diketahui_dietisen', 'rencana',
            'nip1', 'nip2', 'kd_dokter'
        ];

        foreach ($attributes as $attr) {
            $this->$attr = $data->$attr;
        }

        $this->nmPetugas1 = $data->petugas1->nama ?? '';
        $this->nmPetugas2 = $data->petugas2->nama ?? '';
        $this->nmDokter = $data->dokter->nm_dokter ?? '';

        // Load selected masalah
        $this->selectedMasalah = $data->masalah->pluck('kode_masalah')->toArray();

        // Load selected rencana
        $this->selectedRencana = $data->detailRencana->pluck('kode_rencana')->toArray();

        // Load available rencana based on selected masalah
        $this->loadAvailableRencana();

        // SOP #1: Initialize Optimistic Lock
        $this->initializeLock($data);
    }

    public function updatedSelectedMasalah()
    {
        $this->loadAvailableRencana();
    }

    private function loadAvailableRencana()
    {
        if (empty($this->selectedMasalah)) {
            $this->availableRencana = [];
            return;
        }

        $repo = new PengkajianAwalKeperawatanUmumRepository();
        $this->availableRencana = $repo->getRencanaByMasalah($this->selectedMasalah)->toArray();
    }

    public function selectPetugas1($nip, $nama)
    {
        $this->nip1 = $nip;
        $this->nmPetugas1 = $nama;
        $this->petugas1Search = '';
    }

    public function selectPetugas2($nip, $nama)
    {
        $this->nip2 = $nip;
        $this->nmPetugas2 = $nama;
        $this->petugas2Search = '';
    }
    
    public function selectDokter($kd_dokter, $nama)
    {
        $this->kd_dokter = $kd_dokter;
        $this->nmDokter = $nama;
        $this->dokterSearch = '';
    }

    public function save(PengkajianAwalKeperawatanUmumRepository $repository)
    {
        $this->validate([
            'tanggal' => 'required',
            'nip1' => 'required',
            'nip2' => 'required',
            'kd_dokter' => 'required',
        ], [
            'nip1.required' => 'Petugas 1 harus diisi.',
            'nip2.required' => 'Petugas 2 harus diisi.',
            'kd_dokter.required' => 'Dokter DPJP harus diisi.',
        ]);

        $this->validateLock($this->regPeriksa->fresh());

        try {
            $data = [];
            $attributes = [
                'tanggal', 'informasi', 'ket_informasi', 'tiba_diruang_rawat', 'kasus_trauma', 'cara_masuk',
                'rps', 'rpd', 'rpk', 'rpo', 'riwayat_pembedahan', 'riwayat_dirawat_dirs', 'alat_bantu_dipakai',
                'riwayat_kehamilan', 'riwayat_kehamilan_perkiraan', 'riwayat_tranfusi', 'riwayat_alergi',
                'riwayat_merokok', 'riwayat_merokok_jumlah', 'riwayat_alkohol', 'riwayat_alkohol_jumlah',
                'riwayat_narkoba', 'riwayat_olahraga', 'pemeriksaan_mental', 'pemeriksaan_keadaan_umum',
                'pemeriksaan_gcs', 'pemeriksaan_td', 'pemeriksaan_nadi', 'pemeriksaan_rr', 'pemeriksaan_suhu',
                'pemeriksaan_spo2', 'pemeriksaan_bb', 'pemeriksaan_tb', 'pemeriksaan_susunan_kepala',
                'pemeriksaan_susunan_kepala_keterangan', 'pemeriksaan_susunan_wajah', 'pemeriksaan_susunan_wajah_keterangan',
                'pemeriksaan_susunan_leher', 'pemeriksaan_susunan_kejang', 'pemeriksaan_susunan_kejang_keterangan',
                'pemeriksaan_susunan_sensorik', 'pemeriksaan_kardiovaskuler_denyut_nadi', 'pemeriksaan_kardiovaskuler_sirkulasi',
                'pemeriksaan_kardiovaskuler_sirkulasi_keterangan', 'pemeriksaan_kardiovaskuler_pulsasi',
                'pemeriksaan_respirasi_pola_nafas', 'pemeriksaan_respirasi_retraksi', 'pemeriksaan_respirasi_suara_nafas',
                'pemeriksaan_respirasi_volume_pernafasan', 'pemeriksaan_respirasi_jenis_pernafasan',
                'pemeriksaan_respirasi_jenis_pernafasan_keterangan', 'pemeriksaan_respirasi_irama_nafas',
                'pemeriksaan_respirasi_batuk', 'pemeriksaan_gastrointestinal_mulut', 'pemeriksaan_gastrointestinal_mulut_keterangan',
                'pemeriksaan_gastrointestinal_gigi', 'pemeriksaan_gastrointestinal_gigi_keterangan', 'pemeriksaan_gastrointestinal_lidah',
                'pemeriksaan_gastrointestinal_lidah_keterangan', 'pemeriksaan_gastrointestinal_tenggorokan',
                'pemeriksaan_gastrointestinal_tenggorokan_keterangan', 'pemeriksaan_gastrointestinal_abdomen',
                'pemeriksaan_gastrointestinal_abdomen_keterangan', 'pemeriksaan_gastrointestinal_peistatik_usus',
                'pemeriksaan_gastrointestinal_anus', 'pemeriksaan_neurologi_pengelihatan', 'pemeriksaan_neurologi_pengelihatan_keterangan',
                'pemeriksaan_neurologi_alat_bantu_penglihatan', 'pemeriksaan_neurologi_pendengaran', 'pemeriksaan_neurologi_bicara',
                'pemeriksaan_neurologi_bicara_keterangan', 'pemeriksaan_neurologi_sensorik', 'pemeriksaan_neurologi_motorik',
                'pemeriksaan_neurologi_kekuatan_otot', 'pemeriksaan_integument_warnakulit', 'pemeriksaan_integument_turgor',
                'pemeriksaan_integument_kulit', 'pemeriksaan_integument_dekubitas', 'pemeriksaan_muskuloskletal_pergerakan_sendi',
                'pemeriksaan_muskuloskletal_kekauatan_otot', 'pemeriksaan_muskuloskletal_nyeri_sendi',
                'pemeriksaan_muskuloskletal_nyeri_sendi_keterangan', 'pemeriksaan_muskuloskletal_oedema',
                'pemeriksaan_muskuloskletal_oedema_keterangan', 'pemeriksaan_muskuloskletal_fraktur',
                'pemeriksaan_muskuloskletal_fraktur_keterangan', 'pemeriksaan_eliminasi_bab_frekuensi_jumlah',
                'pemeriksaan_eliminasi_bab_frekuensi_durasi', 'pemeriksaan_eliminasi_bab_konsistensi',
                'pemeriksaan_eliminasi_bab_warna', 'pemeriksaan_eliminasi_bak_frekuensi_jumlah',
                'pemeriksaan_eliminasi_bak_frekuensi_durasi', 'pemeriksaan_eliminasi_bak_warna',
                'pemeriksaan_eliminasi_bak_lainlain', 'pola_aktifitas_makanminum', 'pola_aktifitas_mandi',
                'pola_aktifitas_eliminasi', 'pola_aktifitas_berpakaian', 'pola_aktifitas_berpindah',
                'pola_nutrisi_frekuesi_makan', 'pola_nutrisi_jenis_makanan', 'pola_nutrisi_porsi_makan',
                'pola_tidur_lama_tidur', 'pola_tidur_gangguan', 'pengkajian_fungsi_kemampuan_sehari',
                'pengkajian_fungsi_aktifitas', 'pengkajian_fungsi_berjalan', 'pengkajian_fungsi_berjalan_keterangan',
                'pengkajian_fungsi_ambulasi', 'pengkajian_fungsi_ekstrimitas_atas', 'pengkajian_fungsi_ekstrimitas_atas_keterangan',
                'pengkajian_fungsi_ekstrimitas_bawah', 'pengkajian_fungsi_ekstrimitas_bawah_keterangan',
                'pengkajian_fungsi_menggenggam', 'pengkajian_fungsi_menggenggam_keterangan', 'pengkajian_fungsi_koordinasi',
                'pengkajian_fungsi_koordinasi_keterangan', 'pengkajian_fungsi_kesimpulan', 'riwayat_psiko_kondisi_psiko',
                'riwayat_psiko_perilaku', 'riwayat_psiko_perilaku_keterangan', 'riwayat_psiko_gangguan_jiwa',
                'riwayat_psiko_hubungan_keluarga', 'riwayat_psiko_tinggal', 'riwayat_psiko_tinggal_keterangan',
                'riwayat_psiko_nilai_kepercayaan', 'riwayat_psiko_nilai_kepercayaan_keterangan',
                'riwayat_psiko_pendidikan_pj', 'riwayat_psiko_edukasi_diberikan', 'riwayat_psiko_edukasi_diberikan_keterangan',
                'penilaian_nyeri', 'penilaian_nyeri_penyebab', 'penilaian_nyeri_ket_penyebab', 'penilaian_nyeri_kualitas',
                'penilaian_nyeri_ket_kualitas', 'penilaian_nyeri_lokasi', 'penilaian_nyeri_menyebar', 'penilaian_nyeri_skala',
                'penilaian_nyeri_waktu', 'penilaian_nyeri_hilang', 'penilaian_nyeri_ket_hilang', 'penilaian_nyeri_diberitahukan_dokter',
                'penilaian_nyeri_jam_diberitahukan_dokter', 'penilaian_jatuhmorse_skala1', 'penilaian_jatuhmorse_nilai1',
                'penilaian_jatuhmorse_skala2', 'penilaian_jatuhmorse_nilai2', 'penilaian_jatuhmorse_skala3',
                'penilaian_jatuhmorse_nilai3', 'penilaian_jatuhmorse_skala4', 'penilaian_jatuhmorse_nilai4',
                'penilaian_jatuhmorse_skala5', 'penilaian_jatuhmorse_nilai5', 'penilaian_jatuhmorse_skala6',
                'penilaian_jatuhmorse_nilai6', 'penilaian_jatuhmorse_totalnilai', 'penilaian_jatuhsydney_skala1',
                'penilaian_jatuhsydney_nilai1', 'penilaian_jatuhsydney_skala2', 'penilaian_jatuhsydney_nilai2',
                'penilaian_jatuhsydney_skala3', 'penilaian_jatuhsydney_nilai3', 'penilaian_jatuhsydney_skala4',
                'penilaian_jatuhsydney_nilai4', 'penilaian_jatuhsydney_skala5', 'penilaian_jatuhsydney_nilai5',
                'penilaian_jatuhsydney_skala6', 'penilaian_jatuhsydney_nilai6', 'penilaian_jatuhsydney_skala7',
                'penilaian_jatuhsydney_nilai7', 'penilaian_jatuhsydney_skala8', 'penilaian_jatuhsydney_nilai8',
                'penilaian_jatuhsydney_skala9', 'penilaian_jatuhsydney_nilai9', 'penilaian_jatuhsydney_skala10',
                'penilaian_jatuhsydney_nilai10', 'penilaian_jatuhsydney_skala11', 'penilaian_jatuhsydney_nilai11',
                'penilaian_jatuhsydney_totalnilai', 'skrining_gizi1', 'nilai_gizi1', 'skrining_gizi2', 'nilai_gizi2',
                'nilai_total_gizi', 'skrining_gizi_diagnosa_khusus', 'skrining_gizi_ket_diagnosa_khusus',
                'skrining_gizi_diketahui_dietisen', 'skrining_gizi_jam_diketahui_dietisen', 'rencana',
                'nip1', 'nip2', 'kd_dokter'
            ];

            $data['no_rawat'] = $this->noRawat;
            foreach ($attributes as $attr) {
                // Handle empty values
                $val = $this->$attr;
                if ($val === '') {
                    // Provide default dash for text fields
                    $val = '-';
                }
                $data[$attr] = $val;
            }

            if ($this->isEditMode) {
                // SOP #1: Validate Optimistic Lock before updating
                $existingData = $repository->getByNoRawat($this->noRawat);
                if ($existingData) {
                    $this->validateLock($existingData->fresh());
                }

                $repository->update($this->noRawat, $data, $this->selectedMasalah, $this->selectedRencana);
                $msg = 'Pengkajian Awal Keperawatan Umum berhasil diperbarui.';
            } else {
                $repository->store($data, $this->selectedMasalah, $this->selectedRencana);
                $this->isEditMode = true;
                $msg = 'Pengkajian Awal Keperawatan Umum berhasil disimpan.';
            }

            $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => $msg, 'icon' => 'success']);
            return $this->redirectRoute('modul.rawat-inap.sub-rawat-inap.pengkajian-awal-keperawatan-umum', str_replace('/', '-', $this->noRawat), navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal!', 'text' => 'Data gagal disimpan. Pastikan semua isian sudah benar. ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function render()
    {
        $petugas1List = [];
        if (strlen($this->petugas1Search) >= 3) {
            $petugas1List = \App\Models\Petugas::where('status', '1')
                ->where(function($q) {
                    $q->where('nama', 'like', '%' . $this->petugas1Search . '%')
                      ->orWhere('nip', 'like', '%' . $this->petugas1Search . '%');
                })
                ->limit(10)
                ->get();
        }

        $petugas2List = [];
        if (strlen($this->petugas2Search) >= 3) {
            $petugas2List = \App\Models\Petugas::where('status', '1')
                ->where(function($q) {
                    $q->where('nama', 'like', '%' . $this->petugas2Search . '%')
                      ->orWhere('nip', 'like', '%' . $this->petugas2Search . '%');
                })
                ->limit(10)
                ->get();
        }
        
        $dokterList = [];
        if (strlen($this->dokterSearch) >= 3) {
            $dokterList = \App\Models\Dokter::where('status', '1')
                ->where(function($q) {
                    $q->where('nm_dokter', 'like', '%' . $this->dokterSearch . '%')
                      ->orWhere('kd_dokter', 'like', '%' . $this->dokterSearch . '%');
                })
                ->limit(10)
                ->get();
        }

        return view('livewire.modul.rawat-inap.sub-rawat-inap.pengkajian-awal-keperawatan-umum.form', [
            'petugas1List' => $petugas1List,
            'petugas2List' => $petugas2List,
            'dokterList' => $dokterList,
        ])->layout('layouts.app', ['title' => 'Form Pengkajian Awal Keperawatan Umum']);
    }
}
