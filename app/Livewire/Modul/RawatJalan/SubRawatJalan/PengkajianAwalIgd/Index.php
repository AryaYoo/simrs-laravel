<?php

namespace App\Livewire\Modul\RawatJalan\SubRawatJalan\PengkajianAwalIgd;

use Livewire\Component;
use App\Models\RegPeriksa;
use App\Models\PenilaianAwalKeperawatanIgd;
use App\Repositories\RawatJalan\PengkajianAwalIgdRepository;
use Illuminate\Support\Facades\DB;

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
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'poliklinik'])
            ->where('no_rawat', $this->noRawat)
            ->firstOrFail();

        $this->loadData();
    }

    public function loadData()
    {
        $this->pengkajianList = PenilaianAwalKeperawatanIgd::with('petugas')
            ->where('no_rawat', $this->noRawat)
            ->orderBy('tanggal', 'desc')
            ->get()
            ->toArray();
    }

    public function viewData($noRawat)
    {
        $repo = new PengkajianAwalIgdRepository();
        $data = $repo->getByNoRawat($noRawat);
        if ($data) {
            $this->detailData = $data->toArray();
            $this->isDetailModalOpen = true;
        }
    }

    public function delete($noRawat)
    {
        try {
            $repo = new PengkajianAwalIgdRepository();
            $repo->delete($noRawat);

            $this->loadData();
            $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => 'Data pengkajian berhasil dihapus.', 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal!', 'text' => 'Data gagal dihapus: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function render()
    {
        return view('livewire.modul.rawat-jalan.sub-rawat-jalan.pengkajian-awal-igd.index')
            ->layout('layouts.app', ['title' => 'Pengkajian Awal Keperawatan IGD']);
    }
}
