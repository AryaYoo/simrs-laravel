<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\PengkajianAwalKeperawatanUmum;

use Livewire\Component;
use App\Models\RegPeriksa;
use App\Models\PenilaianAwalKeperawatanRanap;
use App\Repositories\RawatInap\PengkajianAwalKeperawatanUmumRepository;

class Index extends Component
{
    public $noRawat;
    public $regPeriksa;
    public $pengkajianList = [];

    // For Detail Modal
    public $isDetailModalOpen = false;
    public $detailData = null;

    public function mount($no_rawat)
    {
        $this->noRawat = str_replace('-', '/', $no_rawat);
        
        // Memuat data pasien untuk header banner
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'kamarInap.kamar'])
            ->where('no_rawat', $this->noRawat)
            ->firstOrFail();

        $this->loadData();
    }

    public function loadData()
    {
        $this->pengkajianList = PenilaianAwalKeperawatanRanap::with(['petugas1', 'petugas2'])
            ->where('no_rawat', $this->noRawat)
            ->orderBy('tanggal', 'desc')
            ->get()
            ->toArray();
    }

    public function viewData($noRawat)
    {
        $repo = new PengkajianAwalKeperawatanUmumRepository();
        $data = $repo->getByNoRawat($noRawat);
        if ($data) {
            $this->detailData = $data->toArray();
            $this->isDetailModalOpen = true;
        }
    }

    public function delete($noRawat)
    {
        try {
            $repo = new PengkajianAwalKeperawatanUmumRepository();
            $repo->delete($noRawat);

            $this->loadData();
            $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => 'Data pengkajian berhasil dihapus.', 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal!', 'text' => 'Data gagal dihapus: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function render()
    {
        return view('livewire.modul.rawat-inap.sub-rawat-inap.pengkajian-awal-keperawatan-umum.index')
            ->layout('layouts.app', ['title' => 'Pengkajian Awal Keperawatan Umum']);
    }
}
