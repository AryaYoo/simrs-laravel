<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\PermintaanRadiologi;

use App\Models\RegPeriksa;
use App\Models\Dokter;
use App\Repositories\RawatInap\PermintaanRadiologiRepository;
use App\Livewire\Concerns\WithOptimisticLocking;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, WithOptimisticLocking;

    public $no_rawat;
    public $regPeriksa;
    
    // Perujuk / DPJP
    public $kd_dokter_perujuk;
    public $nm_dokter_perujuk;
    public $searchDokterModal = '';
    public $listDokter = [];
    public $isDokterModalOpen = false;

    // Time & Order
    public $tgl_permintaan;
    public $jam_permintaan_jam;
    public $jam_permintaan_menit;
    public $jam_permintaan_detik;
    public $auto_waktu = true;
    public $predictedOrderNo = '';

    // Form
    public $diagnosa_klinis = '-';
    public $informasi_tambahan = '-';
    public $searchPemeriksaan = '';
    public $selectedTests = []; // Array of kd_jenis_prw
    public int $perPageRadiologi = 25;

    protected $listeners = ['refresh' => '$refresh'];

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'kamarInap.kamar.bangsal'])
            ->where('no_rawat', $this->no_rawat)
            ->first();

        if (!$this->regPeriksa) {
            abort(404, 'Data pasien tidak ditemukan.');
        }

        $this->kd_dokter_perujuk = $this->regPeriksa->kd_dokter;
        $this->nm_dokter_perujuk = $this->regPeriksa->dokter->nm_dokter ?? '-';
        $this->tgl_permintaan = date('Y-m-d');
        $this->syncWaktu();
        $this->updatePredictedOrderNo();

        // SOP: Initialize Lock
        $this->initializeLock($this->regPeriksa);
    }

    public function syncWaktu()
    {
        if ($this->auto_waktu) {
            $this->jam_permintaan_jam = date('H');
            $this->jam_permintaan_menit = date('i');
            $this->jam_permintaan_detik = date('s');
        }
    }

    public function updatedAutoWaktu($value)
    {
        if ($value) $this->syncWaktu();
    }

    public function updatedTglPermintaan()
    {
        $this->updatePredictedOrderNo();
    }

    public function updatePredictedOrderNo()
    {
        $this->predictedOrderNo = PermintaanRadiologiRepository::getPredictedOrderNo();
    }

    public function openDokterModal()
    {
        $this->searchDokterModal = '';
        $this->loadListDokter();
        $this->isDokterModalOpen = true;
    }

    public function loadListDokter()
    {
        $this->listDokter = PermintaanRadiologiRepository::getListDokter($this->searchDokterModal);
    }

    public function updatedSearchDokterModal()
    {
        $this->loadListDokter();
    }

    public function selectDokter($kd_dokter, $nm_dokter)
    {
        $this->kd_dokter_perujuk = $kd_dokter;
        $this->nm_dokter_perujuk = $nm_dokter;
        $this->isDokterModalOpen = false;
    }

    public function updatedSearchPemeriksaan()
    {
        $this->resetPage('pemeriksaanPage');
    }

    public function updatedPerPageRadiologi()
    {
        $this->resetPage('pemeriksaanPage');
    }

    public function getPemeriksaanListProperty()
    {
        return PermintaanRadiologiRepository::getPemeriksaanList($this->searchPemeriksaan, $this->perPageRadiologi);
    }

    public function save()
    {
        if (empty($this->selectedTests)) {
            $this->dispatch('swal', ['title' => 'Peringatan', 'text' => 'Pilih setidaknya satu pemeriksaan radiologi.', 'icon' => 'warning']);
            return;
        }

        try {
            // SOP: Validate Lock
            $this->validateLock($this->regPeriksa);

            $data = [
                'no_rawat' => $this->no_rawat,
                'kd_dokter_perujuk' => $this->kd_dokter_perujuk,
                'tgl_permintaan' => $this->tgl_permintaan,
                'jam_jam' => $this->jam_permintaan_jam,
                'jam_menit' => $this->jam_permintaan_menit,
                'jam_detik' => $this->jam_permintaan_detik,
                'auto_waktu' => $this->auto_waktu,
                'diagnosa_klinis' => $this->diagnosa_klinis,
                'informasi_tambahan' => $this->informasi_tambahan,
                'selectedTests' => $this->selectedTests,
            ];

            PermintaanRadiologiRepository::savePermintaan($data);

            $this->dispatch('swal', ['title' => 'Berhasil', 'text' => 'Permintaan radiologi berhasil dikirim.', 'icon' => 'success']);
            $this->selectedTests = [];
            $this->diagnosa_klinis = '-';
            $this->informasi_tambahan = '-';
            $this->syncWaktu();
            $this->updatePredictedOrderNo();

        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Kesalahan: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function getHistoryProperty()
    {
        return PermintaanRadiologiRepository::getHistory($this->no_rawat);
    }

    public function batalPermintaan($noorder)
    {
        try {
            $this->validateLock($this->regPeriksa);

            PermintaanRadiologiRepository::batalPermintaan($noorder);

            $this->dispatch('swal', ['title' => 'Dibatalkan', 'text' => 'Permintaan radiologi berhasil dibatalkan.', 'icon' => 'success']);
            $this->dispatch('refresh');

        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function render()
    {
        return view('livewire.modul.rawat-inap.sub-rawat-inap.permintaan-radiologi.index', [
            'pemeriksaanList' => $this->pemeriksaanList,
            'history' => $this->history
        ])->layout('layouts.app', ['title' => 'Permintaan Radiologi']);
    }
}
