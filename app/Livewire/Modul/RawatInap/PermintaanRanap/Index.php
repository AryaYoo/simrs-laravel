<?php

namespace App\Livewire\Modul\RawatInap\PermintaanRanap;

use App\Models\Penjab;
use App\Repositories\RawatInap\PermintaanRanapRepository;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Permintaan Rawat Inap'])]
class Index extends Component
{
    use WithPagination;

    // Tabs
    public $activeTab = 'antrian';

    // Search antrian
    public $search = '';

    // Riwayat Filters
    public $filterTanggalMulai;
    public $filterTanggalSelesai;
    public $filterCaraBayar = '';
    public $searchRiwayat = '';

    // Modals
    public $detailModalOpen = false;
    public $detailData = [];

    // Check In form state
    public $isCheckInOpen = false;
    public $checkInData = [];
    public $tanggal_masuk;
    public $jam_masuk;
    public $kd_kamar;
    public $nm_bangsal;
    public $kd_bangsal;
    public $tarif_kamar = 0;
    public $kelas_kamar = '';
    public $stts_kamar = '';
    public $diagnosa_awal;
    public $kd_penyakit;
    public $nm_penyakit;
    public $lama_inap = 1;

    // Kamar Lookup
    public $isKamarModalOpen = false;
    public $searchKamar = '';

    // Diagnosa Lookup
    public $isDiagnosaModalOpen = false;
    public $searchDiagnosa = '';

    public function updatedSearch() { $this->resetPage(); }
    public function updatedActiveTab() { $this->resetPage(); }
    public function updatedFilterTanggalMulai() { $this->resetPage(); }
    public function updatedFilterTanggalSelesai() { $this->resetPage(); }
    public function updatedFilterCaraBayar() { $this->resetPage(); }
    public function updatedSearchRiwayat() { $this->resetPage(); }

    public function mount()
    {
        $this->tanggal_masuk = date('Y-m-d');
        $this->jam_masuk = date('H:i:s');
        $this->filterTanggalMulai = date('Y-m-01');
        $this->filterTanggalSelesai = date('Y-m-d');
    }

    public function showDetail($id, PermintaanRanapRepository $repo)
    {
        $decodedId = str_replace('-', '/', $id);
        $ranap = $repo->getDetail($decodedId);
        if ($ranap) {
            $this->detailData = $ranap->toArray();
            $this->detailModalOpen = true;
        }
    }

    public function closeDetail()
    {
        $this->detailModalOpen = false;
        $this->detailData = [];
    }

    public function openCheckIn($id, PermintaanRanapRepository $repo)
    {
        $decodedId = str_replace('-', '/', $id);
        $ranap = $repo->getForCheckIn($decodedId);

        if (!$ranap) return;

        $this->checkInData = $ranap->toArray();
        $this->tanggal_masuk = date('Y-m-d');
        $this->jam_masuk = date('H:i:s');

        if ($ranap->kamar) {
            $this->kd_kamar   = $ranap->kamar->kd_kamar;
            $this->kd_bangsal = $ranap->kamar->kd_bangsal;
            $this->nm_bangsal = $ranap->kamar->bangsal->nm_bangsal ?? '';
            $this->tarif_kamar = $ranap->kamar->trf_kamar;
            $this->stts_kamar = $ranap->kamar->status;
        } else {
            $this->kd_kamar   = '';
            $this->nm_bangsal = '';
            $this->tarif_kamar = 0;
            $this->stts_kamar = '';
        }

        $this->diagnosa_awal = $ranap->diagnosa;
        $this->kd_penyakit = '';
        $this->nm_penyakit = '';
        $this->lama_inap = 1;
        $this->isCheckInOpen = true;
    }

    public function closeCheckIn()
    {
        $this->isCheckInOpen = false;
        $this->checkInData = [];
    }

    public function openKamarModal() { $this->isKamarModalOpen = true; }

    public function selectKamar($kd_kamar, $kd_bangsal, $nm_bangsal, $trf_kamar, $kelas, $status)
    {
        $this->kd_kamar    = $kd_kamar;
        $this->kd_bangsal  = $kd_bangsal;
        $this->nm_bangsal  = $nm_bangsal;
        $this->tarif_kamar = $trf_kamar;
        $this->kelas_kamar = $kelas;
        $this->stts_kamar  = $status;
        $this->isKamarModalOpen = false;
    }

    public function openDiagnosaModal() { $this->isDiagnosaModalOpen = true; }

    public function selectDiagnosa($kd_penyakit, $nm_penyakit)
    {
        $this->kd_penyakit  = $kd_penyakit;
        $this->nm_penyakit  = $nm_penyakit;
        $this->diagnosa_awal = $kd_penyakit . ' - ' . $nm_penyakit;
        $this->isDiagnosaModalOpen = false;
    }

    public function saveCheckIn(PermintaanRanapRepository $repo)
    {
        $this->validate([
            'tanggal_masuk' => 'required|date',
            'jam_masuk'     => 'required',
            'kd_kamar'      => 'required',
            'lama_inap'     => 'required|numeric|min:1',
        ]);

        try {
            $data = [
                'no_rawat'      => $this->checkInData['no_rawat'],
                'kd_kamar'      => $this->kd_kamar,
                'tarif_kamar'   => $this->tarif_kamar,
                'diagnosa_awal' => $this->diagnosa_awal,
                'tanggal_masuk' => $this->tanggal_masuk,
                'jam_masuk'     => $this->jam_masuk,
                'lama_inap'     => $this->lama_inap,
            ];

            $repo->processCheckIn($data);

            $this->dispatch('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Pasien berhasil di check in ke rawat inap!']);
            $this->closeCheckIn();
        } catch (\Exception $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function render(PermintaanRanapRepository $repo)
    {
        // Count pending for badge
        $pendingCount = $repo->getPendingCount();

        // --- TAB ANTRIAN ---
        $listPermintaan = collect();
        if ($this->activeTab === 'antrian') {
            $listPermintaan = $repo->getAntrianPending($this->search);
        }

        // --- TAB RIWAYAT ---
        $riwayatList = collect();
        if ($this->activeTab === 'riwayat') {
            $riwayatList = $repo->getRiwayat($this->filterTanggalMulai, $this->filterTanggalSelesai, $this->filterCaraBayar, $this->searchRiwayat);
        }

        // Penjab list for filter dropdown
        $listPenjab = Penjab::orderBy('png_jawab')->get();

        // Lookup lists
        $listKamar = [];
        $listDiagnosa = [];

        if ($this->isKamarModalOpen) {
            $listKamar = $repo->searchKamar($this->searchKamar);
        }

        if ($this->isDiagnosaModalOpen) {
            $listDiagnosa = $repo->searchDiagnosa($this->searchDiagnosa);
        }

        return view('livewire.modul.rawat-inap.permintaan-ranap.index', [
            'listPermintaan' => $listPermintaan,
            'riwayatList'    => $riwayatList,
            'listKamar'      => $listKamar,
            'listDiagnosa'   => $listDiagnosa,
            'listPenjab'     => $listPenjab,
            'pendingCount'   => $pendingCount,
        ]);
    }
}

