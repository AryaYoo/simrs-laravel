<?php

namespace App\Livewire\Modul\RawatInap\PerawatanTindakan;

use App\Models\RegPeriksa;
use App\Models\PemeriksaanRanap;
use App\Repositories\RawatInap\PerawatanTindakanRepository;
use App\Livewire\Concerns\WithOptimisticLocking;
use Livewire\Attributes\Layout;
use Livewire\Component;
#[Layout('layouts.app', ['title' => 'Perawatan/Tindakan Pasien Rawat Inap', 'hideSidebar' => true])]
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
    public $keluhan, $pemeriksaan, $alergi, $penilaian, $rtl, $instruksi, $evaluasi, $nip;
    
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
    public $isEditTindakanMode = false;
    public $original_tindakan_type, $original_tgl_perawatan, $original_jam_rawat, $original_kd_jenis_prw;
    
    public string $tindakanMode = 'spesifik';
    public $tgl_tindakan, $jam_tindakan;
    public array $selectedTindakan = [];

    public function mount(string $no_rawat): void
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with([
            'pasien',
            'penjab',
            'dokter',
            'kamarInap.kamar',
            'permintaanRanap.kamar',
        ])
        ->where('no_rawat', $this->no_rawat)
        ->firstOrFail();

        $this->tgl_perawatan = now()->format('Y-m-d');
        $this->jam_rawat     = now()->format('H:i:s');

        // SOP #1: Initialize concurrency lock
        $this->initializeLock($this->regPeriksa);
    }

    public function openCreateModal()
    {
        $this->reset(['suhu_tubuh', 'tensi', 'nadi', 'respirasi', 'tinggi', 'berat', 'spo2', 'gcs', 'kesadaran', 'keluhan', 'pemeriksaan', 'alergi', 'penilaian', 'rtl', 'instruksi', 'evaluasi', 'nip', 'isEditMode']);
        
        $this->tgl_perawatan = now()->format('Y-m-d');
        $this->jam_rawat     = now()->format('H:i:s');
        $this->isEditMode    = false;

        // Fetch last data as reference and store as Array to prevent Livewire model hydration bug with composite keys
        $lastRecord = \App\Models\PemeriksaanRanap::where('no_rawat', $this->no_rawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->first();

        if ($lastRecord) {
            $this->lastPemeriksaan = $lastRecord->toArray();
            $this->suhu_tubuh  = $lastRecord->suhu_tubuh;
            $this->tensi       = $lastRecord->tensi;
            $this->nadi        = $lastRecord->nadi;
            $this->respirasi   = $lastRecord->respirasi;
            $this->tinggi      = $lastRecord->tinggi;
            $this->berat       = $lastRecord->berat;
            $this->spo2        = $lastRecord->spo2;
            $this->gcs         = $lastRecord->gcs;
            $this->kesadaran   = $lastRecord->kesadaran;
            $this->alergi      = $lastRecord->alergi;
        } else {
            $this->lastPemeriksaan = null;
        }

        $this->createModalOpen = true;
    }

    public function fillAutoData()
    {
        // Auto-fill SOAPIE text fields if reference exists (from Array)
        if ($this->lastPemeriksaan) {
            $this->suhu_tubuh  = $this->lastPemeriksaan['suhu_tubuh'] ?? '-';
            $this->tensi       = $this->lastPemeriksaan['tensi'] ?? '-';
            $this->nadi        = $this->lastPemeriksaan['nadi'] ?? '-';
            $this->respirasi   = $this->lastPemeriksaan['respirasi'] ?? '-';
            $this->tinggi      = $this->lastPemeriksaan['tinggi'] ?? '-';
            $this->berat       = $this->lastPemeriksaan['berat'] ?? '-';
            $this->spo2        = $this->lastPemeriksaan['spo2'] ?? '-';
            $this->gcs         = $this->lastPemeriksaan['gcs'] ?? '-';
            $this->kesadaran   = $this->lastPemeriksaan['kesadaran'] ?? '-';
            $this->alergi      = $this->lastPemeriksaan['alergi'] ?? '-';
            $this->keluhan     = $this->lastPemeriksaan['keluhan'] ?? '-';
            $this->pemeriksaan = $this->lastPemeriksaan['pemeriksaan'] ?? '-';
            $this->penilaian   = $this->lastPemeriksaan['penilaian'] ?? '-';
            $this->rtl         = $this->lastPemeriksaan['rtl'] ?? '-';
            $this->instruksi   = $this->lastPemeriksaan['instruksi'] ?? '-';
            $this->evaluasi    = $this->lastPemeriksaan['evaluasi'] ?? '-';
        }

        // Auto-fill petugas from logged-in user
        $loggedInUsername = auth()->user()->username ?? null;
        if ($loggedInUsername) {
            $pegawai = \App\Models\Pegawai::find($loggedInUsername);
            if ($pegawai) {
                $this->nip = $pegawai->nik;
                $this->currentJabatan = $pegawai->jbtn ?? '-';
            }
        }
    }

    public function editPemeriksaan($data)
    {
        // $data is an array from the JSON in the view
        $this->tgl_perawatan = $data['tgl_perawatan'];
        $this->jam_rawat     = $data['jam_rawat'];
        $this->suhu_tubuh    = $data['suhu_tubuh'];
        $this->tensi         = $data['tensi'];
        $this->nadi          = $data['nadi'];
        $this->respirasi     = $data['respirasi'];
        $this->tinggi        = $data['tinggi'];
        $this->berat         = $data['berat'];
        $this->spo2          = str_replace('%', '', $data['spo2']);
        $this->gcs           = $data['gcs'];
        $this->kesadaran     = $data['kesadaran'];
        $this->keluhan       = $data['keluhan'];
        $this->pemeriksaan   = $data['pemeriksaan'];
        $this->alergi        = $data['alergi'];
        $this->penilaian     = $data['penilaian'];
        $this->rtl           = $data['rtl'];
        $this->instruksi     = $data['instruksi'];
        $this->evaluasi      = $data['evaluasi'];
        $this->nip           = $data['nip'];
        
        $this->currentJabatan = $data['jbtn_pegawai'];
        $this->isEditMode     = true;

        // Initialize lock by fetching the model first
        $model = \App\Models\PemeriksaanRanap::where('no_rawat', $this->no_rawat)
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

    // Penanganan Dokter & Petugas Methods
    public function selectDokter($kd, $nama)
    {
        $this->kd_dokter_tindakan = $kd;
        $this->nm_dokter_tindakan = $nama;
        $this->dokterSearch = '';
    }

    public function selectPetugas($nip, $nama)
    {
        $this->nip_tindakan = $nip;
        $this->nm_petugas_tindakan = $nama;
        $this->petugasSearch = '';
    }

    public function openTindakanCreateModal($mode = 'spesifik')
    {
        $this->reset(['kd_dokter_tindakan', 'nm_dokter_tindakan', 'nip_tindakan', 'nm_petugas_tindakan', 'isEditTindakanMode', 'original_tindakan_type', 'original_tgl_perawatan', 'original_jam_rawat', 'original_kd_jenis_prw', 'selectedTindakan']);
        $this->tindakanMode = $mode;
        $this->tgl_tindakan = now()->format('Y-m-d');
        $this->jam_tindakan = now()->format('H:i:s');
        $this->tindakanCreateModalOpen = true;
    }

    public function openTindakanLookup($type)
    {
        $this->lookupType = $type;
        $this->tindakanSearch = '';
        $this->tindakanLookupOpen = true;
    }

    public function editTindakan($data)
    {
        // Reset and populate
        $this->reset(['kd_dokter_tindakan', 'nm_dokter_tindakan', 'nip_tindakan', 'nm_petugas_tindakan', 'selectedTindakan']);
        
        $this->isEditTindakanMode = true;
        $this->tindakanMode = 'spesifik';
        $this->original_tindakan_type = $data['type']; // drpr, dr, pr
        $this->original_tgl_perawatan = $data['tgl_perawatan'];
        $this->original_jam_rawat = $data['jam_rawat'];
        $this->original_kd_jenis_prw = $data['kd_jenis_prw'];

        $this->tgl_tindakan = $data['tgl_perawatan'];
        $this->jam_tindakan = $data['jam_rawat'];

        $this->selectedTindakan[$data['kd_jenis_prw']] = [
            'nama' => $data['nm_perawatan'],
            'checked' => true
        ];
        
        $this->kd_dokter_tindakan = $data['kd_staff_dr'] != '-' ? $data['kd_staff_dr'] : null;
        $this->nm_dokter_tindakan = $data['staff_dr'] != '-' ? $data['staff_dr'] : null;
        $this->nip_tindakan = $data['kd_staff_pr'] != '-' ? $data['kd_staff_pr'] : null;
        $this->nm_petugas_tindakan = $data['staff_pr'] != '-' ? $data['staff_pr'] : null;
        
        $this->lookupType = ($data['type'] == 'pr') ? 'pr' : 'dr';
        
        $this->tindakanCreateModalOpen = true;
    }

    public function toggleTindakanSelection($kd_jenis_prw, $nm_perawatan, $shift = null)
    {
        if ($this->tindakanMode == 'spesifik') {
            if (isset($this->selectedTindakan[$kd_jenis_prw])) {
                unset($this->selectedTindakan[$kd_jenis_prw]);
            } else {
                $this->selectedTindakan[$kd_jenis_prw] = [
                    'nama' => $nm_perawatan,
                    'checked' => true
                ];
            }
        } else {
            // Mode rutin
            if (!isset($this->selectedTindakan[$kd_jenis_prw])) {
                $this->selectedTindakan[$kd_jenis_prw] = [
                    'nama' => $nm_perawatan,
                    'pagi' => false,
                    'siang' => false,
                    'sore' => false,
                    'malam' => false
                ];
            }
            if ($shift) {
                $this->selectedTindakan[$kd_jenis_prw][$shift] = !$this->selectedTindakan[$kd_jenis_prw][$shift];
            }
            
            // Cleanup if none selected
            if (!$this->selectedTindakan[$kd_jenis_prw]['pagi'] && !$this->selectedTindakan[$kd_jenis_prw]['siang'] && !$this->selectedTindakan[$kd_jenis_prw]['sore'] && !$this->selectedTindakan[$kd_jenis_prw]['malam']) {
                unset($this->selectedTindakan[$kd_jenis_prw]);
            }
        }
    }

    public function applyTindakanSelection()
    {
        $this->tindakanLookupOpen = false;
    }

    public function saveTindakan()
    {
        // SOP Rekam Medis: Validate if admission is already completed or canceled.
        if ($this->regPeriksa->stts === 'Sudah' || $this->regPeriksa->stts === 'Batal') {
            $this->dispatch('swal', ['title' => 'Terkunci', 'text' => 'Pelayanan pasien sudah selesai/dibatalkan. Modifikasi data tindakan ditolak (SOP).', 'icon' => 'error']);
            return;
        }

        if (empty($this->selectedTindakan)) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Tindakan belum dipilih.', 'icon' => 'error']);
            return;
        }

        if ($this->lookupType == 'dr' && !$this->kd_dokter_tindakan) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Dokter belum dipilih.', 'icon' => 'error']);
            return;
        }

        if ($this->lookupType == 'pr' && !$this->nip_tindakan) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Petugas belum dipilih.', 'icon' => 'error']);
            return;
        }

        if ($this->tindakanMode == 'spesifik' && (!$this->tgl_tindakan || !$this->jam_tindakan)) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Tanggal dan jam tindakan harus diisi.', 'icon' => 'error']);
            return;
        }

        if ($this->tindakanMode == 'rutin' && !$this->tgl_tindakan) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Tanggal tindakan harus diisi.', 'icon' => 'error']);
            return;
        }

        // SOP #1: Validate concurrency lock (Force fresh fetch from DB for accurate identification)
        $freshRegPeriksa = $this->regPeriksa->fresh();
        $this->validateLock($freshRegPeriksa);

        try {
            $data = [
                'isEditTindakanMode' => $this->isEditTindakanMode,
                'no_rawat' => $this->no_rawat,
                'tindakanMode' => $this->tindakanMode,
                'tgl_tindakan' => $this->tgl_tindakan,
                'jam_tindakan' => $this->jam_tindakan,
                'selectedTindakan' => $this->selectedTindakan,
                'kd_dokter_tindakan' => $this->kd_dokter_tindakan,
                'nip_tindakan' => $this->nip_tindakan,
                'original_tindakan_type' => $this->original_tindakan_type,
                'original_kd_jenis_prw' => $this->original_kd_jenis_prw,
                'original_tgl_perawatan' => $this->original_tgl_perawatan,
                'original_jam_rawat' => $this->original_jam_rawat,
            ];

            PerawatanTindakanRepository::saveMultipleTindakan($data);

            $this->tindakanCreateModalOpen = false;
            $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => 'Tindakan berhasil disimpan.', 'icon' => 'success']);

        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal!', 'text' => 'Kesalahan: ' . substr($e->getMessage(), 0, 100), 'icon' => 'error']);
        }
    }

    public function deleteTindakan($type, $kd_jenis_prw, $tgl, $jam, $kd_staff)
    {
        // SOP Rekam Medis: Validate if admission is already completed or canceled.
        if ($this->regPeriksa->stts === 'Sudah' || $this->regPeriksa->stts === 'Batal') {
            $this->dispatch('swal', ['title' => 'Terkunci', 'text' => 'Pelayanan pasien sudah selesai/dibatalkan. Penghapusan data ditolak (SOP).', 'icon' => 'error']);
            return;
        }

        try {
            PerawatanTindakanRepository::deleteTindakan($type, $this->no_rawat, $kd_jenis_prw, $tgl, $jam);
            $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => 'Tindakan dihapus.', 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal!', 'text' => 'Gagal menghapus.', 'icon' => 'error']);
        }
    }

    public function deletePemeriksaan($tgl, $jam)
    {
        // SOP Rekam Medis: Validate if admission is already completed or canceled.
        if ($this->regPeriksa->stts === 'Sudah' || $this->regPeriksa->stts === 'Batal') {
            $this->dispatch('swal', ['title' => 'Terkunci', 'text' => 'Pelayanan pasien sudah selesai/dibatalkan. Penghapusan data ditolak (SOP).', 'icon' => 'error']);
            return;
        }

        try {
            PerawatanTindakanRepository::deletePemeriksaan($this->no_rawat, $tgl, $jam);
            $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => 'Pemeriksaan dihapus.', 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal!', 'text' => 'Gagal menghapus.', 'icon' => 'error']);
        }
    }

    public function save()
    {
        $this->validate([
            'tgl_perawatan' => 'required|date',
            'jam_rawat'     => 'required',
            'nip'           => 'required',
        ], [
            'nip.required' => 'Petugas (Dokter/Perawat) harus dipilih.',
        ]);

        if ($this->isEditMode) {
            $this->updatePemeriksaan();
        } else {
            $this->storePemeriksaan();
        }
    }

    public function storePemeriksaan()
    {
        try {
            $data = [
                'no_rawat'      => $this->no_rawat,
                'tgl_perawatan' => $this->tgl_perawatan,
                'jam_rawat'     => $this->jam_rawat,
                'suhu_tubuh'    => $this->suhu_tubuh,
                'tensi'         => $this->tensi,
                'nadi'          => $this->nadi,
                'respirasi'     => $this->respirasi,
                'tinggi'        => $this->tinggi,
                'berat'         => $this->berat,
                'spo2'          => $this->spo2,
                'gcs'           => $this->gcs,
                'kesadaran'     => $this->kesadaran,
                'keluhan'       => $this->keluhan,
                'pemeriksaan'   => $this->pemeriksaan,
                'alergi'        => $this->alergi,
                'penilaian'     => $this->penilaian,
                'rtl'           => $this->rtl,
                'instruksi'     => $this->instruksi,
                'evaluasi'      => $this->evaluasi,
                'nip'           => $this->nip,
            ];

            PerawatanTindakanRepository::insertPemeriksaan($data);

            $this->createModalOpen = false;
            $this->dispatch('swal', ['title' => 'Berhasil!', 'text'  => 'Data pemeriksaan berhasil disimpan.', 'icon'  => 'success']);

        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal Menyimpan!', 'text'  => 'Terjadi kesalahan sistem: ' . substr($e->getMessage(), 0, 150), 'icon'  => 'error']);
        }
    }

    public function updatePemeriksaan()
    {
        try {
            $model = PemeriksaanRanap::where('no_rawat', $this->no_rawat)
                ->where('tgl_perawatan', $this->tgl_perawatan)
                ->where('jam_rawat', $this->jam_rawat)
                ->first();

            if (!$model) {
                throw new \Exception("Data pemeriksaan tidak ditemukan di database. Mungkin sudah dihapus.");
            }

            // Standard SOP: Validate lock before saving to handle concurrency
            // We pass the $model directly because it was just fetched using composite keys ($no_rawat, $tgl, $jam)
            $this->validateLock($model);

            $data = [
                'suhu_tubuh'    => $this->suhu_tubuh,
                'tensi'         => $this->tensi,
                'nadi'          => $this->nadi,
                'respirasi'     => $this->respirasi,
                'tinggi'        => $this->tinggi,
                'berat'         => $this->berat,
                'spo2'          => $this->spo2,
                'gcs'           => $this->gcs,
                'kesadaran'     => $this->kesadaran,
                'keluhan'       => $this->keluhan,
                'pemeriksaan'   => $this->pemeriksaan,
                'alergi'        => $this->alergi,
                'penilaian'     => $this->penilaian,
                'rtl'           => $this->rtl,
                'instruksi'     => $this->instruksi,
                'evaluasi'      => $this->evaluasi,
                'nip'           => $this->nip,
            ];

            PerawatanTindakanRepository::updatePemeriksaan($model, $data);

            $this->createModalOpen = false;
            $this->dispatch('swal', ['title' => 'Berhasil!', 'text'  => 'Data pemeriksaan berhasil diperbarui.', 'icon'  => 'success']);

        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'CONCURRENCY_ERROR') === false) {
                $this->dispatch('swal', ['title' => 'Gagal Memperbarui!', 'text'  => 'Terjadi kesalahan: ' . substr($e->getMessage(), 0, 150), 'icon'  => 'error']);
            }
        }
    }

    public function render()
    {
        $allTindakan = PerawatanTindakanRepository::getAllTindakanHistory($this->no_rawat);
        $pemeriksaanRanap = PerawatanTindakanRepository::getPemeriksaanHistory($this->no_rawat);

        $pegawaiList = PerawatanTindakanRepository::searchPegawai($this->pegawaiSearch);
        $dokterList = PerawatanTindakanRepository::searchDokter($this->dokterSearch);
        $petugasList = PerawatanTindakanRepository::searchPetugas($this->petugasSearch);

        $kelasStr = $this->regPeriksa->kamarInap->last()->kamar->kelas ?? '-';
        $tindakanList = PerawatanTindakanRepository::searchTarif($this->tindakanSearch, $kelasStr, $this->lookupType);

        return view('livewire.modul.rawat-inap.perawatan-tindakan.index', [
            'allTindakan'      => $allTindakan,
            'pemeriksaanRanap' => $pemeriksaanRanap,
            'pegawaiList'      => $pegawaiList,
            'dokterList'       => $dokterList,
            'petugasList'      => $petugasList,
            'tindakanList'     => $tindakanList,
        ]);
    }
}
