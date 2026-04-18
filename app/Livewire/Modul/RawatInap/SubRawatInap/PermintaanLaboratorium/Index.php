<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\PermintaanLaboratorium;

use App\Models\RegPeriksa;
use App\Models\TemplateLaboratorium;
use App\Models\Dokter;
use App\Repositories\RawatInap\PermintaanLabRepository;
use App\Livewire\Concerns\WithOptimisticLocking;

use Livewire\Component;

use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination, WithOptimisticLocking;


    public $no_rawat;
    public $regPeriksa;
    
    // Perujuk
    public $kd_dokter_perujuk;
    public $nm_dokter_perujuk;
    public $searchDokterModal = '';
    public $listDokter = [];
    public $isDokterModalOpen = false;

    // Time Control
    public $tgl_permintaan;
    public $jam_permintaan_jam;
    public $jam_permintaan_menit;
    public $jam_permintaan_detik;
    public $auto_waktu = true;

    // Tab & Categories
    public $kategori = 'PK'; // PK, PA, MB
    
    // Left Table (Pemeriksaan Master)
    public $searchPemeriksaan = '';
    public $selectedTests = []; // Array of kd_jenis_prw
    
    // Right Table (Detail Parameters)
    public $searchDetail = '';
    public $selectedDetails = []; // Array of id_template
    
    public $diagnosa_klinis = '-';
    public $informasi_tambahan = '-';
    public $predictedOrderNo = '';

    protected $listeners = ['refresh' => '$refresh'];

    // --- PA Form Fields ---
    public $pa_pengambilan_bahan, $pa_diperoleh_dengan, $pa_lokasi_jaringan, $pa_diawetkan_dengan;
    public $pa_pernah_dilakukan_di, $pa_tanggal_sebelumnya, $pa_nomor_sebelumnya, $pa_diagnosa_sebelumnya;

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'kamarInap.kamar.bangsal'])->where('no_rawat', $this->no_rawat)->first();

        if (!$this->regPeriksa) {
            abort(404, 'Data pasien tidak ditemukan.');
        }

        $this->kd_dokter_perujuk = $this->regPeriksa->kd_dokter;
        $this->tgl_permintaan = date('Y-m-d');
        $this->pa_pengambilan_bahan = date('Y-m-d');
        $this->pa_tanggal_sebelumnya = date('Y-m-d');
        $this->syncWaktu();
        $this->updatePredictedOrderNo();

        // SOP: Initialize Lock
        $this->initializeLock($this->regPeriksa);
    }

    public function updatedKategori()
    {
        $this->selectedTests = [];
        $this->selectedDetails = [];
        $this->searchPemeriksaan = '';
        $this->searchDetail = '';
        $this->resetPage('masterPage');
        $this->updatePredictedOrderNo();
    }

    public function updatePredictedOrderNo()
    {
        $this->predictedOrderNo = PermintaanLabRepository::getPredictedOrderNo($this->kategori);
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
        if ($value) {
            $this->syncWaktu();
        }
    }

    public function updatedTglPermintaan()
    {
        $this->updatePredictedOrderNo();
    }

    public function openDokterModal()
    {
        $this->searchDokterModal = '';
        $this->loadListDokter();
        $this->isDokterModalOpen = true;
    }

    public function loadListDokter()
    {
        $this->listDokter = PermintaanLabRepository::getListDokter($this->searchDokterModal);
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
        $this->resetPage('masterPage');
    }

    public function updatedSelectedTests($values)
    {
        $allTemplateIds = \App\Models\TemplateLaboratorium::whereIn('kd_jenis_prw', $this->selectedTests)
            ->whereHas('pemeriksaanHeader', function($q) {
                $q->where('kategori', $this->kategori);
            })
            ->pluck('id_template')
            ->map(fn($id) => (string)$id)
            ->toArray();
            
        $this->selectedDetails = $allTemplateIds;
    }

    public function toggleGroup($kd_jenis_prw)
    {
        $groupIds = \App\Models\TemplateLaboratorium::where('kd_jenis_prw', $kd_jenis_prw)
            ->pluck('id_template')
            ->map(fn($id) => (string)$id)
            ->toArray();

        $isAllSelected = collect($groupIds)->every(fn($id) => in_array($id, $this->selectedDetails));

        if ($isAllSelected) {
            $this->selectedDetails = array_diff($this->selectedDetails, $groupIds);
        } else {
            $this->selectedDetails = array_unique(array_merge($this->selectedDetails, $groupIds));
        }
    }

    public function getPemeriksaanListProperty()
    {
        return PermintaanLabRepository::getPemeriksaanList($this->kategori, $this->searchPemeriksaan);
    }

    public function getDetailParametersProperty()
    {
        return PermintaanLabRepository::getDetailParameters($this->kategori, $this->selectedTests, $this->searchDetail);
    }

    public function toggleAllDetails($checked = true)
    {
        if ($checked) {
            $this->selectedDetails = $this->detailParameters->pluck('id_template')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedDetails = [];
        }
    }

    public function save()
    {
        if (empty($this->selectedTests) && empty($this->selectedDetails) && $this->kategori !== 'PA') {
            $this->dispatch('swal', ['title' => 'Peringatan', 'text' => 'Pilih setidaknya satu pemeriksaan.', 'icon' => 'warning']);
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
                'kategori' => $this->kategori,
                'diagnosa_klinis' => $this->diagnosa_klinis,
                'informasi_tambahan' => $this->informasi_tambahan,
                'selectedTests' => $this->selectedTests,
                'selectedDetails' => $this->selectedDetails,
                'pa' => [
                    'pengambilan_bahan' => $this->pa_pengambilan_bahan,
                    'diperoleh_dengan' => $this->pa_diperoleh_dengan,
                    'lokasi_jaringan' => $this->pa_lokasi_jaringan,
                    'diawetkan_dengan' => $this->pa_diawetkan_dengan,
                    'pernah_dilakukan_di' => $this->pa_pernah_dilakukan_di,
                    'tanggal_sebelumnya' => $this->pa_tanggal_sebelumnya,
                    'nomor_sebelumnya' => $this->pa_nomor_sebelumnya,
                    'diagnosa_sebelumnya' => $this->pa_diagnosa_sebelumnya,
                ]
            ];

            PermintaanLabRepository::savePermintaan($data);

            $this->dispatch('swal', ['title' => 'Berhasil', 'text' => 'Permintaan lab berhasil dikirim.', 'icon' => 'success']);
            $this->selectedTests = [];
            $this->selectedDetails = [];
            $this->diagnosa_klinis = '-';
            $this->informasi_tambahan = '-';
            $this->reset(['pa_diperoleh_dengan', 'pa_lokasi_jaringan', 'pa_diawetkan_dengan', 'pa_pernah_dilakukan_di', 'pa_nomor_sebelumnya', 'pa_diagnosa_sebelumnya']);
            $this->syncWaktu();
            $this->updatePredictedOrderNo();

        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Kesalahan: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function getPemeriksaanHistoryProperty()
    {
        return PermintaanLabRepository::getHistory($this->no_rawat);
    }

    public function batalPermintaan($noorder)
    {
        try {
            // SOP: Validate Lock
            $this->validateLock($this->regPeriksa);

            PermintaanLabRepository::batalPermintaan($noorder);

            $this->dispatch('swal', ['title' => 'Dibatalkan', 'text' => 'Permintaan lab berhasil dibatalkan.', 'icon' => 'success']);
            $this->dispatch('refresh');

        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function render()
    {
        return view('livewire.modul.rawat-inap.sub-rawat-inap.permintaan-laboratorium.index', [
            'pemeriksaanList' => $this->pemeriksaanList,
            'detailParameters' => $this->detailParameters
        ])->layout('layouts.app', ['title' => 'Permintaan Laboratorium']);
    }

}
