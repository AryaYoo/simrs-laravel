<?php

namespace App\Livewire\Modul\RawatInap\PermintaanRanap;

use App\Models\Kamar;
use App\Models\KamarInap;
use App\Models\Penjab;
use App\Models\Penyakit;
use App\Models\PermintaanRanap;
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

    public function showDetail($id)
    {
        $decodedId = str_replace('-', '/', $id);
        $this->detailData = PermintaanRanap::with([
                'kamar', 'kamar.bangsal',
                'regPeriksa', 'regPeriksa.pasien',
                'regPeriksa.dokter', 'regPeriksa.poliklinik', 'regPeriksa.penjab',
                'kamarInap'
            ])
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

    public function openCheckIn($id)
    {
        $decodedId = str_replace('-', '/', $id);
        $ranap = PermintaanRanap::with(['kamar', 'kamar.bangsal', 'regPeriksa', 'regPeriksa.pasien'])
            ->where('no_rawat', $decodedId)->first();

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

    public function saveCheckIn()
    {
        $this->validate([
            'tanggal_masuk' => 'required|date',
            'jam_masuk'     => 'required',
            'kd_kamar'      => 'required',
            'lama_inap'     => 'required|numeric|min:1',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $no_rawat = $this->checkInData['no_rawat'];

            $kamar = Kamar::where('kd_kamar', $this->kd_kamar)->first();
            if ($kamar && $kamar->status == 'ISI') {
                \Illuminate\Support\Facades\DB::rollBack();
                $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Kamar sudah terisi, silakan pilih kamar lain.']);
                return;
            }

            KamarInap::create([
                'no_rawat'      => $no_rawat,
                'kd_kamar'      => $this->kd_kamar,
                'trf_kamar'     => $this->tarif_kamar,
                'diagnosa_awal' => $this->diagnosa_awal ?: '-',
                'diagnosa_akhir'=> '-',
                'tgl_masuk'     => $this->tanggal_masuk,
                'jam_masuk'     => $this->jam_masuk,
                'tgl_keluar'    => '0000-00-00',
                'jam_keluar'    => '00:00:00',
                'lama'          => $this->lama_inap,
                'ttl_biaya'     => $this->tarif_kamar * $this->lama_inap,
                'stts_pulang'   => '-',
            ]);

            Kamar::where('kd_kamar', $this->kd_kamar)->update(['status' => 'ISI']);

            \Illuminate\Support\Facades\DB::commit();

            $this->dispatch('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Pasien berhasil di check in ke rawat inap!']);
            $this->closeCheckIn();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        // Count pending for badge
        $pendingCount = PermintaanRanap::doesntHave('kamarInap')->count();

        // --- TAB ANTRIAN ---
        $listPermintaan = collect();
        if ($this->activeTab === 'antrian') {
            $antrianQuery = PermintaanRanap::with(['kamar', 'kamar.bangsal', 'regPeriksa', 'regPeriksa.pasien'])
                ->doesntHave('kamarInap');

            if ($this->search) {
                $antrianQuery->where(function ($q) {
                    $q->whereHas('regPeriksa.pasien', function ($q2) {
                        $q2->where('nm_pasien', 'like', '%' . $this->search . '%')
                            ->orWhere('no_rkm_medis', 'like', '%' . $this->search . '%');
                    })->orWhere('no_rawat', 'like', '%' . $this->search . '%');
                });
            }

            $listPermintaan = $antrianQuery->paginate(10);
        }

        // --- TAB RIWAYAT ---
        $riwayatList = collect();
        if ($this->activeTab === 'riwayat') {
            $riwayatQuery = PermintaanRanap::with([
                    'kamar', 'kamar.bangsal',
                    'regPeriksa', 'regPeriksa.pasien', 'regPeriksa.penjab',
                    'kamarInap'
                ])
                ->whereBetween('tanggal', [$this->filterTanggalMulai, $this->filterTanggalSelesai]);

            if ($this->filterCaraBayar) {
                $riwayatQuery->whereHas('regPeriksa', function ($q) {
                    $q->where('kd_pj', $this->filterCaraBayar);
                });
            }

            if ($this->searchRiwayat) {
                $riwayatQuery->where(function ($q) {
                    $q->whereHas('regPeriksa.pasien', function ($q2) {
                        $q2->where('nm_pasien', 'like', '%' . $this->searchRiwayat . '%')
                            ->orWhere('no_rkm_medis', 'like', '%' . $this->searchRiwayat . '%');
                    })->orWhere('no_rawat', 'like', '%' . $this->searchRiwayat . '%');
                });
            }

            $riwayatList = $riwayatQuery->orderBy('tanggal', 'desc')->paginate(10);
        }

        // Penjab list for filter dropdown
        $listPenjab = Penjab::orderBy('png_jawab')->get();

        // Lookup lists
        $listKamar = [];
        $listDiagnosa = [];

        if ($this->isKamarModalOpen) {
            $kamarQuery = Kamar::with('bangsal')->where('status', '!=', 'ISI');
            if (strlen($this->searchKamar) >= 2) {
                $search = $this->searchKamar;
                $kamarQuery->where(function ($q) use ($search) {
                    $q->where('kd_kamar', 'like', '%' . $search . '%')
                        ->orWhereHas('bangsal', function ($q2) use ($search) {
                            $q2->where('nm_bangsal', 'like', '%' . $search . '%');
                        });
                });
            }
            $listKamar = $kamarQuery->limit(50)->get();
        }

        if ($this->isDiagnosaModalOpen) {
            $diagQuery = Penyakit::query();
            if (strlen($this->searchDiagnosa) >= 2) {
                $search = $this->searchDiagnosa;
                $diagQuery->where('kd_penyakit', 'like', '%' . $search . '%')
                    ->orWhere('nm_penyakit', 'like', '%' . $search . '%');
            }
            $listDiagnosa = $diagQuery->limit(50)->get();
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
