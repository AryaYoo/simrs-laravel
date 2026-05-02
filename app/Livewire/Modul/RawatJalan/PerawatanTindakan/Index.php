<?php

namespace App\Livewire\Modul\RawatJalan\PerawatanTindakan;

use App\Models\RegPeriksa;
use App\Models\PemeriksaanRalan;
use App\Repositories\RawatJalan\PemeriksaanRalanRepository;
use App\Repositories\RawatJalan\PerawatanTindakanRalanRepository;
use App\Repositories\RawatJalan\CatatanDokterRepository;
use App\Repositories\RawatJalan\DiagnosaProsedurRepository;
use App\Livewire\Concerns\WithOptimisticLocking;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Perawatan/Tindakan Pasien Rawat Jalan', 'hideSidebar' => true])]
class Index extends Component
{
    use WithOptimisticLocking;

    public string $no_rawat;
    public $regPeriksa;
    public string $activeTab = 'pemeriksaan';

    // Form State for Pemeriksaan
    public bool $createModalOpen = false;
    public bool $isEditMode = false;
    public $tgl_perawatan, $jam_rawat, $suhu_tubuh, $tensi, $nadi, $respirasi;
    public $tinggi, $berat, $spo2, $gcs, $kesadaran;
    public $keluhan, $pemeriksaan, $alergi, $lingkar_perut, $penilaian, $rtl, $instruksi, $evaluasi, $nip;
    
    public $currentJabatan = '-';
    
    // Reference State
    public $lastPemeriksaan;
    public string $pegawaiSearch = '';

    // Penanganan Dokter & Petugas State
    public bool $tindakanCreateModalOpen = false;
    public bool $tindakanDetailModalOpen = false;
    public $kd_dokter_tindakan, $nm_dokter_tindakan;
    public $nip_tindakan, $nm_petugas_tindakan;
    public string $dokterSearch = '', $petugasSearch = '', $tindakanSearch = '';
    
    public bool $tindakanLookupOpen = false;
    public string $lookupType = 'dr'; // 'dr' or 'pr'
    public $kd_jenis_prw_selected, $nm_perawatan_selected;
    public $isEditTindakanMode = false;
    public $original_tindakan_type, $original_tgl_perawatan, $original_jam_rawat, $original_kd_jenis_prw;

    // Catatan Dokter State
    public bool $catatanDokterModalOpen = false;
    public bool $isEditCatatanMode = false;
    public $tanggal_catatan, $jam_catatan, $kd_dokter_catatan, $nm_dokter_catatan, $isi_catatan;
    public $original_tanggal_catatan, $original_jam_catatan, $original_kd_dokter_catatan;

    // Diagnosa & Prosedur State
    public string $diagnosaSubTab = 'diagnosa'; // 'diagnosa' or 'prosedur'
    
    // Form Diagnosa
    public bool $diagnosaModalOpen = false;
    public $kd_penyakit, $nm_penyakit, $status_penyakit = 'Baru', $prioritas_diagnosa = 1;
    public string $diagnosaSearch = '';
    public array $selectedDiagnosa = [];

    // Form Prosedur
    public bool $prosedurModalOpen = false;
    public $kode_prosedur, $nm_prosedur, $prioritas_prosedur = 1, $jumlah_prosedur = 1;
    public string $prosedurSearch = '';
    public array $selectedProsedur = [];

    public function mount(string $no_rawat): void
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with([
            'pasien',
            'penjab',
            'dokter',
            'poliklinik'
        ])
        ->where('no_rawat', $this->no_rawat)
        ->firstOrFail();

        $this->tgl_perawatan = now()->format('Y-m-d');
        $this->jam_rawat     = now()->format('H:i:s');

        // SOP #1: Initialize concurrency lock
        $this->initializeLock($this->regPeriksa);
    }

    public function getPemeriksaanRalanProperty()
    {
        return PemeriksaanRalan::with(['pegawai'])
            ->where('no_rawat', $this->no_rawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->get();
    }

    public function getAllTindakanProperty()
    {
        return PerawatanTindakanRalanRepository::getAllTindakanHistory($this->no_rawat);
    }

    public function getDokterListProperty()
    {
        return PerawatanTindakanRalanRepository::searchDokter($this->dokterSearch);
    }

    public function getPetugasListProperty()
    {
        return PerawatanTindakanRalanRepository::searchPetugas($this->petugasSearch);
    }

    public function getTindakanListProperty()
    {
        return PerawatanTindakanRalanRepository::searchTarif($this->tindakanSearch, $this->lookupType);
    }

    public function openCreateModal()
    {
        $this->reset(['suhu_tubuh', 'tensi', 'nadi', 'respirasi', 'tinggi', 'berat', 'spo2', 'gcs', 'kesadaran', 'keluhan', 'pemeriksaan', 'alergi', 'lingkar_perut', 'penilaian', 'rtl', 'instruksi', 'evaluasi', 'nip', 'isEditMode']);
        
        $this->tgl_perawatan = now()->format('Y-m-d');
        $this->jam_rawat     = now()->format('H:i:s');
        $this->isEditMode    = false;

        // Fetch last data as reference
        $this->lastPemeriksaan = PemeriksaanRalan::where('no_rawat', $this->no_rawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->first();

        if ($this->lastPemeriksaan) {
            $this->suhu_tubuh  = $this->lastPemeriksaan->suhu_tubuh;
            $this->tensi       = $this->lastPemeriksaan->tensi;
            $this->nadi        = $this->lastPemeriksaan->nadi;
            $this->respirasi   = $this->lastPemeriksaan->respirasi;
            $this->tinggi      = $this->lastPemeriksaan->tinggi;
            $this->berat       = $this->lastPemeriksaan->berat;
            $this->spo2        = $this->lastPemeriksaan->spo2;
            $this->gcs         = $this->lastPemeriksaan->gcs;
            $this->kesadaran   = $this->lastPemeriksaan->kesadaran;
            $this->alergi      = $this->lastPemeriksaan->alergi;
            $this->lingkar_perut = $this->lastPemeriksaan->lingkar_perut;
        }

        $this->createModalOpen = true;
    }

    public function editPemeriksaan($data)
    {
        $this->tgl_perawatan = $data['tgl_perawatan'];
        $this->jam_rawat     = $data['jam_rawat'];
        $this->suhu_tubuh    = $data['suhu_tubuh'];
        $this->tensi         = $data['tensi'];
        $this->nadi          = $data['nadi'];
        $this->respirasi     = $data['respirasi'];
        $this->tinggi        = $data['tinggi'];
        $this->berat         = $data['berat'];
        $this->spo2          = str_replace('%', '', $data['spo2'] ?? '');
        $this->gcs           = $data['gcs'];
        $this->kesadaran     = $data['kesadaran'];
        $this->keluhan       = $data['keluhan'];
        $this->pemeriksaan   = $data['pemeriksaan'];
        $this->alergi        = $data['alergi'];
        $this->lingkar_perut = $data['lingkar_perut'] ?? '';
        $this->penilaian     = $data['penilaian'];
        $this->rtl           = $data['rtl'];
        $this->instruksi     = $data['instruksi'];
        $this->evaluasi      = $data['evaluasi'];
        $this->nip           = $data['nip'];
        
        $this->currentJabatan = $data['jbtn_pegawai'];
        $this->isEditMode     = true;

        // Initialize lock by fetching the model first
        $model = PemeriksaanRalan::where('no_rawat', $this->no_rawat)
            ->where('tgl_perawatan', $this->tgl_perawatan)
            ->where('jam_rawat', $this->jam_rawat)
            ->first();

        if ($model) {
            $this->initializeLock($model);
        }

        $this->createModalOpen = true;
    }

    public function updatedNip($value)
    {
        if ($value) {
            $staff = \App\Models\Pegawai::find($value);
            $this->currentJabatan = $staff->jbtn ?? '-';
            $this->pegawaiSearch = '';
        } else {
            $this->currentJabatan = '-';
        }
    }

    public function getPegawaiListProperty()
    {
        if (strlen($this->pegawaiSearch) >= 2) {
            return \App\Models\Pegawai::where('stts_aktif', 'AKTIF')
                ->where(function ($query) {
                    $query->where('nama', 'like', '%' . $this->pegawaiSearch . '%')
                          ->orWhere('nik', 'like', '%' . $this->pegawaiSearch . '%');
                })
                ->take(10)
                ->get();
        }
        return [];
    }

    public function save()
    {
        // SOP Rekam Medis: Validate if admission is already completed or canceled.
        if ($this->regPeriksa->stts === 'Sudah' || $this->regPeriksa->stts === 'Batal') {
            $this->dispatch('swal', ['title' => 'Terkunci', 'text' => 'Pelayanan pasien sudah selesai/dibatalkan. Modifikasi data tindakan ditolak (SOP).', 'icon' => 'error']);
            return;
        }

        $this->validate([
            'tgl_perawatan' => 'required|date',
            'jam_rawat'     => 'required',
            'nip'           => 'required',
            'kesadaran'     => 'required'
        ], [
            'nip.required'       => 'Petugas yang memeriksa harus diisi.',
            'kesadaran.required' => 'Kesadaran wajib dipilih.'
        ]);

        // SOP #1: Validate concurrency lock 
        if ($this->isEditMode) {
            $model = PemeriksaanRalan::where('no_rawat', $this->no_rawat)
                ->where('tgl_perawatan', $this->tgl_perawatan)
                ->where('jam_rawat', $this->jam_rawat)
                ->first();
            if ($model) {
                $this->validateLock($model);
            }
        }

        try {
            $data = [
                'isEditMode'    => $this->isEditMode,
                'no_rawat'      => $this->no_rawat,
                'tgl_perawatan' => $this->tgl_perawatan,
                'jam_rawat'     => $this->jam_rawat,
                'suhu_tubuh'    => $this->suhu_tubuh ?: '-',
                'tensi'         => $this->tensi ?: '-',
                'nadi'          => $this->nadi ?: '-',
                'respirasi'     => $this->respirasi ?: '-',
                'tinggi'        => $this->tinggi ?: '-',
                'berat'         => $this->berat ?: '-',
                'spo2'          => $this->spo2 ?: '-',
                'gcs'           => $this->gcs ?: '-',
                'kesadaran'     => $this->kesadaran,
                'keluhan'       => $this->keluhan ?: '-',
                'pemeriksaan'   => $this->pemeriksaan ?: '-',
                'alergi'        => $this->alergi ?: '-',
                'lingkar_perut' => $this->lingkar_perut ?: '-',
                'penilaian'     => $this->penilaian ?: '-',
                'rtl'           => $this->rtl ?: '-',
                'instruksi'     => $this->instruksi ?: '-',
                'evaluasi'      => $this->evaluasi ?: '-',
                'nip'           => $this->nip,
            ];

            PemeriksaanRalanRepository::save($data);

            $this->createModalOpen = false;
            $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => 'Data pemeriksaan rawat jalan berhasil disimpan.', 'icon' => 'success']);

        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Terjadi Kesalahan', 'text' => $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function deletePemeriksaan($tgl_perawatan, $jam_rawat)
    {
        if ($this->regPeriksa->stts === 'Sudah' || $this->regPeriksa->stts === 'Batal') {
            $this->dispatch('swal', ['title' => 'Terkunci', 'text' => 'Pelayanan pasien sudah selesai/dibatalkan. Penghapusan data ditolak (SOP).', 'icon' => 'error']);
            return;
        }

        try {
            PemeriksaanRalanRepository::delete($this->no_rawat, $tgl_perawatan, $jam_rawat);
            $this->dispatch('swal', ['title' => 'Berhasil', 'text' => 'Data pemeriksaan berhasil dihapus.', 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Terjadi Kesalahan', 'text' => $e->getMessage(), 'icon' => 'error']);
        }
    }

    /**
     * Penanganan Dokter & Petugas Methods
     */
    public function openTindakanCreateModal()
    {
        $this->reset(['kd_dokter_tindakan', 'nm_dokter_tindakan', 'nip_tindakan', 'nm_petugas_tindakan', 'kd_jenis_prw_selected', 'nm_perawatan_selected', 'isEditTindakanMode', 'original_tindakan_type', 'original_tgl_perawatan', 'original_jam_rawat', 'original_kd_jenis_prw']);
        $this->tindakanCreateModalOpen = true;
    }

    public function selectDokter($kd, $nm)
    {
        $this->kd_dokter_tindakan = $kd;
        $this->nm_dokter_tindakan = $nm;
    }

    public function selectPetugas($nip, $nm)
    {
        $this->nip_tindakan = $nip;
        $this->nm_petugas_tindakan = $nm;
    }

    public function openTindakanLookup($type)
    {
        $this->lookupType = $type;
        $this->tindakanLookupOpen = true;
    }

    public function previewTindakan($kd, $nm)
    {
        $this->kd_jenis_prw_selected = $kd;
        $this->nm_perawatan_selected = $nm;
        $this->tindakanLookupOpen = false;
    }

    public function saveTindakan()
    {
        if ($this->regPeriksa->stts === 'Sudah' || $this->regPeriksa->stts === 'Batal') {
            $this->dispatch('swal', ['title' => 'Terkunci', 'text' => 'Pelayanan pasien sudah selesai/dibatalkan. Modifikasi data ditolak (SOP).', 'icon' => 'error']);
            return;
        }

        if (!$this->kd_jenis_prw_selected) {
            $this->dispatch('swal', ['title' => 'Peringatan', 'text' => 'Pilih tindakan terlebih dahulu.', 'icon' => 'warning']);
            return;
        }

        if (!$this->kd_dokter_tindakan && !$this->nip_tindakan) {
            $this->dispatch('swal', ['title' => 'Peringatan', 'text' => 'Pilih Dokter atau Petugas (atau keduanya) sebagai pelaksana tindakan.', 'icon' => 'warning']);
            return;
        }

        try {
            $data = [
                'no_rawat'               => $this->no_rawat,
                'isEditTindakanMode'     => $this->isEditTindakanMode,
                'kd_jenis_prw_selected'  => $this->kd_jenis_prw_selected,
                'kd_dokter_tindakan'     => $this->kd_dokter_tindakan,
                'nip_tindakan'           => $this->nip_tindakan,
                'original_tindakan_type' => $this->original_tindakan_type,
                'original_tgl_perawatan' => $this->original_tgl_perawatan,
                'original_jam_rawat'     => $this->original_jam_rawat,
                'original_kd_jenis_prw'  => $this->original_kd_jenis_prw,
            ];

            PerawatanTindakanRalanRepository::saveTindakan($data);

            $this->tindakanCreateModalOpen = false;
            $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => 'Data penanganan/tindakan berhasil disimpan.', 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Terjadi Kesalahan', 'text' => $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function editTindakan($data)
    {
        $this->isEditTindakanMode = true;
        $this->original_tindakan_type = $data['type'];
        $this->original_tgl_perawatan = $data['tgl_perawatan'];
        $this->original_jam_rawat = $data['jam_rawat'];
        $this->original_kd_jenis_prw = $data['kd_jenis_prw'];

        $this->kd_jenis_prw_selected = $data['kd_jenis_prw'];
        $this->nm_perawatan_selected = $data['nm_perawatan'];
        
        $this->kd_dokter_tindakan = $data['kd_staff_dr'] != '-' ? $data['kd_staff_dr'] : null;
        $this->nm_dokter_tindakan = $data['staff_dr'] != '-' ? $data['staff_dr'] : null;
        
        $this->nip_tindakan = $data['kd_staff_pr'] != '-' ? $data['kd_staff_pr'] : null;
        $this->nm_petugas_tindakan = $data['staff_pr'] != '-' ? $data['staff_pr'] : null;

        $this->tindakanCreateModalOpen = true;
    }

    public function deleteTindakan($type, $kd_jenis_prw, $tgl_perawatan, $jam_rawat)
    {
        if ($this->regPeriksa->stts === 'Sudah' || $this->regPeriksa->stts === 'Batal') {
            $this->dispatch('swal', ['title' => 'Terkunci', 'text' => 'Pelayanan pasien sudah selesai/dibatalkan. Penghapusan data ditolak (SOP).', 'icon' => 'error']);
            return;
        }

        try {
            PerawatanTindakanRalanRepository::deleteTindakan($type, $this->no_rawat, $kd_jenis_prw, $tgl_perawatan, $jam_rawat);
            $this->dispatch('swal', ['title' => 'Berhasil', 'text' => 'Data tindakan berhasil dihapus.', 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Terjadi Kesalahan', 'text' => $e->getMessage(), 'icon' => 'error']);
        }
    }

    // --- Methods untuk Catatan Dokter ---

    #[Computed]
    public function allCatatan()
    {
        return CatatanDokterRepository::getRiwayatCatatan($this->no_rawat);
    }

    public function openCatatanModal()
    {
        $this->reset(['kd_dokter_catatan', 'nm_dokter_catatan', 'isi_catatan', 'isEditCatatanMode', 'original_tanggal_catatan', 'original_jam_catatan', 'original_kd_dokter_catatan']);
        $this->tanggal_catatan = now()->format('Y-m-d');
        $this->jam_catatan = now()->format('H:i:s');
        $this->catatanDokterModalOpen = true;
    }

    public function selectDokterCatatan($kd, $nama)
    {
        $this->kd_dokter_catatan = $kd;
        $this->nm_dokter_catatan = $nama;
        $this->dokterSearch = '';
    }

    public function saveCatatan()
    {
        if ($this->regPeriksa->stts === 'Sudah' || $this->regPeriksa->stts === 'Batal') {
            $this->dispatch('swal', ['title' => 'Ditolak', 'text' => 'Pasien sudah dilayani atau dibatalkan. Tidak dapat memodifikasi catatan.', 'icon' => 'error']);
            return;
        }

        if (!$this->kd_dokter_catatan || !$this->isi_catatan) {
            $this->dispatch('swal', ['title' => 'Peringatan', 'text' => 'Dokter dan Catatan harus diisi.', 'icon' => 'warning']);
            return;
        }

        try {
            if ($this->isEditCatatanMode) {
                CatatanDokterRepository::updateCatatan([
                    'no_rawat' => $this->no_rawat,
                    'tanggal' => $this->original_tanggal_catatan,
                    'jam' => $this->original_jam_catatan,
                    'kd_dokter' => $this->original_kd_dokter_catatan,
                ], [
                    'no_rawat' => $this->no_rawat,
                    'tanggal' => $this->original_tanggal_catatan, // Keep original timestamp
                    'jam' => $this->original_jam_catatan,
                    'kd_dokter' => $this->kd_dokter_catatan,
                    'catatan' => $this->isi_catatan,
                ]);
            } else {
                CatatanDokterRepository::saveCatatan([
                    'no_rawat' => $this->no_rawat,
                    'tanggal' => $this->tanggal_catatan,
                    'jam' => $this->jam_catatan,
                    'kd_dokter' => $this->kd_dokter_catatan,
                    'catatan' => $this->isi_catatan,
                ]);
            }

            $this->catatanDokterModalOpen = false;
            $this->dispatch('swal', ['title' => 'Berhasil', 'text' => 'Catatan berhasil disimpan.', 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Gagal menyimpan catatan: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function editCatatan($data)
    {
        if ($this->regPeriksa->stts === 'Sudah' || $this->regPeriksa->stts === 'Batal') {
            $this->dispatch('swal', ['title' => 'Ditolak', 'text' => 'Pasien sudah dilayani atau dibatalkan. Tidak dapat memodifikasi catatan.', 'icon' => 'error']);
            return;
        }

        $this->isEditCatatanMode = true;
        $this->original_tanggal_catatan = $data['tanggal'];
        $this->original_jam_catatan = $data['jam'];
        $this->original_kd_dokter_catatan = $data['kd_dokter'];

        $this->tanggal_catatan = $data['tanggal'];
        $this->jam_catatan = $data['jam'];
        $this->kd_dokter_catatan = $data['kd_dokter'];
        $this->nm_dokter_catatan = $data['nm_dokter'];
        $this->isi_catatan = $data['catatan'];

        $this->catatanDokterModalOpen = true;
    }

    public function deleteCatatan($tanggal, $jam, $kd_dokter)
    {
        if ($this->regPeriksa->stts === 'Sudah' || $this->regPeriksa->stts === 'Batal') {
            $this->dispatch('swal', ['title' => 'Ditolak', 'text' => 'Pasien sudah dilayani atau dibatalkan. Tidak dapat memodifikasi catatan.', 'icon' => 'error']);
            return;
        }

        try {
            CatatanDokterRepository::deleteCatatan($this->no_rawat, $tanggal, $jam, $kd_dokter);
            $this->dispatch('swal', ['title' => 'Berhasil', 'text' => 'Catatan berhasil dihapus.', 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Gagal menghapus catatan: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    // --- Methods untuk Diagnosa & Prosedur ---
    
    #[Computed]
    public function listDiagnosa()
    {
        return DiagnosaProsedurRepository::getDiagnosaPasien($this->no_rawat);
    }

    #[Computed]
    public function listProsedur()
    {
        return DiagnosaProsedurRepository::getProsedurPasien($this->no_rawat);
    }

    #[Computed]
    public function listPenyakitMaster()
    {
        if (strlen($this->diagnosaSearch) < 2) {
            return DiagnosaProsedurRepository::getTopIcd10();
        }
        return DiagnosaProsedurRepository::searchIcd10($this->diagnosaSearch);
    }

    #[Computed]
    public function listIcd9Master()
    {
        if (strlen($this->prosedurSearch) < 2) {
            return DiagnosaProsedurRepository::getTopIcd9();
        }
        return DiagnosaProsedurRepository::searchIcd9($this->prosedurSearch);
    }

    public function openDiagnosaModal()
    {
        $this->reset(['kd_penyakit', 'nm_penyakit', 'diagnosaSearch', 'selectedDiagnosa']);
        $this->status_penyakit = 'Baru';
        $this->prioritas_diagnosa = count($this->listDiagnosa) + 1;
        $this->diagnosaModalOpen = true;
    }

    public function toggleDiagnosa($kd, $nama)
    {
        $index = collect($this->selectedDiagnosa)->search(fn($item) => $item['kd_penyakit'] === $kd);

        if ($index !== false) {
            array_splice($this->selectedDiagnosa, $index, 1);
        } else {
            $this->selectedDiagnosa[] = [
                'kd_penyakit' => $kd,
                'nm_penyakit' => $nama,
                'status_penyakit' => $this->status_penyakit,
            ];
        }

        // Re-calculate priorities based on current list count
        $basePrioritas = count($this->listDiagnosa);
        foreach ($this->selectedDiagnosa as $i => $item) {
            $this->selectedDiagnosa[$i]['prioritas'] = $basePrioritas + $i + 1;
        }
    }

    public function saveDiagnosa()
    {
        $this->validateLock($this->regPeriksa->fresh());

        if ($this->regPeriksa->stts === 'Sudah' || $this->regPeriksa->stts === 'Batal') {
            $this->dispatch('swal', ['title' => 'Ditolak', 'text' => 'Pasien sudah dilayani atau dibatalkan.', 'icon' => 'error']);
            return;
        }

        if (empty($this->selectedDiagnosa)) {
            $this->dispatch('swal', ['title' => 'Peringatan', 'text' => 'Pilih diagnosa terlebih dahulu.', 'icon' => 'warning']);
            return;
        }

        try {
            DiagnosaProsedurRepository::saveMultipleDiagnosa($this->no_rawat, $this->selectedDiagnosa);
            $this->diagnosaModalOpen = false;
            $this->dispatch('swal', ['title' => 'Berhasil', 'text' => 'Diagnosa berhasil disimpan.', 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Gagal menyimpan diagnosa: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function deleteDiagnosa($kd_penyakit)
    {
        if ($this->regPeriksa->stts === 'Sudah' || $this->regPeriksa->stts === 'Batal') {
            $this->dispatch('swal', ['title' => 'Ditolak', 'text' => 'Pasien sudah dilayani atau dibatalkan.', 'icon' => 'error']);
            return;
        }

        try {
            DiagnosaProsedurRepository::deleteDiagnosa($this->no_rawat, $kd_penyakit);
            $this->dispatch('swal', ['title' => 'Berhasil', 'text' => 'Diagnosa berhasil dihapus.', 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Gagal menghapus diagnosa: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function openProsedurModal()
    {
        $this->reset(['kode_prosedur', 'nm_prosedur', 'prosedurSearch', 'selectedProsedur']);
        $this->prioritas_prosedur = count($this->listProsedur) + 1;
        $this->jumlah_prosedur = 1;
        $this->prosedurModalOpen = true;
    }

    public function toggleProsedur($kode, $nama)
    {
        $index = collect($this->selectedProsedur)->search(fn($item) => $item['kode'] === $kode);

        if ($index !== false) {
            array_splice($this->selectedProsedur, $index, 1);
        } else {
            $this->selectedProsedur[] = [
                'kode' => $kode,
                'nm_prosedur' => $nama,
                'jumlah' => 1,
            ];
        }

        // Re-calculate priorities
        $basePrioritas = count($this->listProsedur);
        foreach ($this->selectedProsedur as $i => $item) {
            $this->selectedProsedur[$i]['prioritas'] = $basePrioritas + $i + 1;
        }
    }

    public function saveProsedur()
    {
        $this->validateLock($this->regPeriksa->fresh());

        if ($this->regPeriksa->stts === 'Sudah' || $this->regPeriksa->stts === 'Batal') {
            $this->dispatch('swal', ['title' => 'Ditolak', 'text' => 'Pasien sudah dilayani atau dibatalkan.', 'icon' => 'error']);
            return;
        }

        if (empty($this->selectedProsedur)) {
            $this->dispatch('swal', ['title' => 'Peringatan', 'text' => 'Pilih prosedur terlebih dahulu.', 'icon' => 'warning']);
            return;
        }

        try {
            DiagnosaProsedurRepository::saveMultipleProsedur($this->no_rawat, $this->selectedProsedur);
            $this->prosedurModalOpen = false;
            $this->dispatch('swal', ['title' => 'Berhasil', 'text' => 'Prosedur berhasil disimpan.', 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Gagal menyimpan prosedur: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function deleteProsedur($kode)
    {
        if ($this->regPeriksa->stts === 'Sudah' || $this->regPeriksa->stts === 'Batal') {
            $this->dispatch('swal', ['title' => 'Ditolak', 'text' => 'Pasien sudah dilayani atau dibatalkan.', 'icon' => 'error']);
            return;
        }

        try {
            DiagnosaProsedurRepository::deleteProsedur($this->no_rawat, $kode);
            $this->dispatch('swal', ['title' => 'Berhasil', 'text' => 'Prosedur berhasil dihapus.', 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Gagal menghapus prosedur: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function render()
    {
        return view('livewire.modul.rawat-jalan.perawatan-tindakan.index');
    }
}

