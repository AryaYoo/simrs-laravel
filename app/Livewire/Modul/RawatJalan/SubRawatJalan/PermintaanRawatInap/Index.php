<?php

namespace App\Livewire\Modul\RawatJalan\SubRawatJalan\PermintaanRawatInap;

use App\Models\Kamar;
use App\Models\Penyakit;
use App\Models\PermintaanRanap;
use App\Models\RegPeriksa;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Permintaan Rawat Inap'])]
class Index extends Component
{
    use WithPagination;

    public $no_rawat;
    public $no_rawat_slug;
    public $regPeriksa;

    // State untuk Modal Detail
    public $detailModalOpen = false;
    public $detailData = [];

    // State untuk Tabs dan Filter
    public $activeTab = 'pasien'; // 'pasien' atau 'semua'
    public $filterTanggalMulai;
    public $filterTanggalSelesai;
    public $filterStatus = 'semua'; // 'semua', 'menunggu', 'sudah_masuk'

    // State untuk Buat Permintaan
    public $isCreating = false;
    public $tanggal;
    public $catatan = '';

    // State untuk Kamar Lookup
    public $isKamarModalOpen = false;
    public $searchKamar = '';
    public $kd_kamar = '';
    public $kd_bangsal = '';
    public $nm_bangsal = '';
    public $trf_kamar = 0;
    public $kelas_kamar = '';

    // State untuk Diagnosa Lookup
    public $isDiagnosaModalOpen = false;
    public $searchDiagnosa = '';
    public $kd_penyakit = '';
    public $nm_penyakit = '';

    public function mount($no_rawat)
    {
        // Decode the slug to normal no_rawat format
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->no_rawat_slug = $no_rawat;
        
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'poliklinik', 'penjab'])
            ->where('no_rawat', $this->no_rawat)
            ->firstOrFail();

        $this->filterTanggalMulai = date('Y-m-d');
        $this->filterTanggalSelesai = date('Y-m-d');
        $this->tanggal = date('Y-m-d');
    }

    public function updatedActiveTab()
    {
        $this->resetPage();
    }

    public function updatedFilterTanggalMulai()
    {
        $this->resetPage();
    }

    public function updatedFilterTanggalSelesai()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function showDetail($id)
    {
        $decodedId = str_replace('-', '/', $id);
        $this->detailData = PermintaanRanap::with(['kamar', 'kamar.bangsal', 'regPeriksa', 'regPeriksa.pasien', 'regPeriksa.dokter', 'regPeriksa.poliklinik', 'regPeriksa.penjab'])
            ->where('no_rawat', $decodedId)
            ->first()
            ->toArray();
        $this->detailModalOpen = true;
    }

    public function closeDetail()
    {
        $this->detailModalOpen = false;
        $this->detailData = [];
    }

    // Aksi Kamar Lookup
    public function openKamarModal()
    {
        $this->isKamarModalOpen = true;
    }

    public function selectKamar($kd_kamar, $kd_bangsal, $nm_bangsal, $trf_kamar, $kelas)
    {
        $this->kd_kamar = $kd_kamar;
        $this->kd_bangsal = $kd_bangsal;
        $this->nm_bangsal = $nm_bangsal;
        $this->trf_kamar = $trf_kamar;
        $this->kelas_kamar = $kelas;
        $this->isKamarModalOpen = false;
    }

    // Aksi Diagnosa Lookup
    public function openDiagnosaModal()
    {
        $this->isDiagnosaModalOpen = true;
    }

    public function selectDiagnosa($kd_penyakit, $nm_penyakit)
    {
        $this->kd_penyakit = $kd_penyakit;
        $this->nm_penyakit = $nm_penyakit;
        $this->isDiagnosaModalOpen = false;
    }

    public function showCreateForm()
    {
        $this->resetForm();
        $this->isCreating = true;
    }

    public function cancelCreate()
    {
        $this->isCreating = false;
    }

    public function savePermintaan()
    {
        $this->validate([
            'tanggal' => 'required|date',
            'kd_kamar' => 'required',
        ]);

        $exists = PermintaanRanap::where('no_rawat', $this->no_rawat)->exists();
        if ($exists) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Permintaan rawat inap untuk nomor rawat ini sudah terdaftar.']);
            return;
        }

        try {
            PermintaanRanap::create([
                'no_rawat' => $this->no_rawat,
                'tanggal' => $this->tanggal,
                'kd_kamar' => $this->kd_kamar,
                'diagnosa' => $this->kd_penyakit ? $this->kd_penyakit . ' - ' . $this->nm_penyakit : '-',
                'catatan' => $this->catatan ?: '-',
            ]);

            $this->dispatch('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Permintaan rawat inap berhasil disimpan!']);
            $this->isCreating = false;
        } catch (\Exception $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function deletePermintaan($id)
    {
        $decodedId = str_replace('-', '/', $id);
        $permintaan = PermintaanRanap::where('no_rawat', $decodedId)->first();
        
        if ($permintaan) {
            $hasRanap = \App\Models\KamarInap::where('no_rawat', $decodedId)->exists();
            if ($hasRanap) {
                $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Pasien sudah masuk Rawat Inap, permintaan tidak dapat dihapus.']);
                return;
            }
            $permintaan->delete();
            $this->dispatch('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Permintaan rawat inap berhasil dihapus!']);
        }
    }

    private function resetForm()
    {
        $this->tanggal = date('Y-m-d');
        $this->catatan = '';
        $this->kd_kamar = '';
        $this->kd_bangsal = '';
        $this->nm_bangsal = '';
        $this->trf_kamar = 0;
        $this->kelas_kamar = '';
        $this->kd_penyakit = '';
        $this->nm_penyakit = '';
        $this->searchKamar = '';
        $this->searchDiagnosa = '';
    }

    public function render()
    {
        $permintaanRanaps = collect();
        $semuaPermintaan = collect();
        $listKamar = [];
        $listDiagnosa = [];

        if ($this->isKamarModalOpen) {
            $query = Kamar::with('bangsal');
            if (strlen($this->searchKamar) >= 2) {
                $search = $this->searchKamar;
                $query->where('kd_kamar', 'like', '%' . $search . '%')
                      ->orWhereHas('bangsal', function($q) use ($search) {
                          $q->where('nm_bangsal', 'like', '%' . $search . '%');
                      });
            }
            $listKamar = $query->limit(50)->get();
        }

        if ($this->isDiagnosaModalOpen) {
            $query = Penyakit::query();
            if (strlen($this->searchDiagnosa) >= 2) {
                $search = $this->searchDiagnosa;
                $query->where('kd_penyakit', 'like', '%' . $search . '%')
                      ->orWhere('nm_penyakit', 'like', '%' . $search . '%');
            }
            $listDiagnosa = $query->limit(50)->get();
        }

        if ($this->activeTab === 'pasien') {
            $permintaanRanaps = PermintaanRanap::with(['kamar', 'kamar.bangsal', 'regPeriksa', 'regPeriksa.pasien', 'kamarInap'])
                ->where('no_rawat', $this->no_rawat)
                ->get();
        } else {
            $query = PermintaanRanap::with(['kamar', 'kamar.bangsal', 'regPeriksa', 'regPeriksa.pasien', 'regPeriksa.dokter', 'regPeriksa.poliklinik', 'regPeriksa.penjab', 'kamarInap'])
                ->whereBetween('tanggal', [$this->filterTanggalMulai, $this->filterTanggalSelesai]);

            if ($this->filterStatus === 'menunggu') {
                $query->doesntHave('kamarInap');
            } elseif ($this->filterStatus === 'sudah_masuk') {
                $query->has('kamarInap');
            }

            $semuaPermintaan = $query->paginate(10);
        }

        return view('livewire.modul.rawat-jalan.sub-rawat-jalan.permintaan-rawat-inap.index', [
            'permintaanRanaps' => $permintaanRanaps,
            'semuaPermintaan' => $semuaPermintaan,
            'listKamar' => $listKamar,
            'listDiagnosa' => $listDiagnosa,
        ]);
    }
}
