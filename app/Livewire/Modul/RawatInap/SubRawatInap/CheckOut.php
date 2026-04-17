<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap;

use App\Models\Kamar;
use App\Models\KamarInap;
use App\Models\RegPeriksa;
use App\Models\Penyakit;
use App\Livewire\Concerns\WithOptimisticLocking;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Check Out Pasien'])]
class CheckOut extends Component
{
    use WithOptimisticLocking;

    public string $no_rawat;
    public $regPeriksa;
    public $currentKamarInapArray;

    // Form Fields
    public $tgl_keluar;
    public $jam_keluar;
    public $kd_penyakit_akhir;
    public $nm_penyakit_akhir;
    public $stts_pulang = 'Membaik';
    public $lama = 0;
    public $total_biaya = 0;

    // Diagnostic Modal State
    public bool $isIcdModalOpen = false;
    public string $searchIcd = '';

    public function mount(string $no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'kamarInap' => function($q) {
            $q->where('tgl_keluar', '0000-00-00')->orderBy('tgl_masuk', 'desc')->orderBy('jam_masuk', 'desc');
        }, 'kamarInap.kamar.bangsal'])
            ->where('no_rawat', $this->no_rawat)
            ->firstOrFail();

        $activeStay = $this->regPeriksa->kamarInap->first();
        if (!$activeStay) {
            return $this->redirect(route('modul.rawat-inap.show', $no_rawat), navigate: true);
        }

        $this->currentKamarInapArray = $activeStay->toArray();
        $this->tgl_keluar = now()->format('Y-m-d');
        $this->jam_keluar = now()->format('H:i:s');

        // SOP #1: Initialize lock
        $this->initializeLock($this->regPeriksa);

        $this->calculateBilling();
    }

    public function calculateBilling()
    {
        if ($this->currentKamarInapArray) {
            $start = Carbon::parse($this->currentKamarInapArray['tgl_masuk'] . ' ' . $this->currentKamarInapArray['jam_masuk']);
            $end = Carbon::parse($this->tgl_keluar . ' ' . $this->jam_keluar);
            
            // SIMRS Khanza Standard DATEDIFF
            $days = $start->diffInDays($end);
            
            // Min 1 day logic for billing if standard practice
            $this->lama = ($days == 0) ? 1 : $days;
            $this->total_biaya = $this->lama * $this->currentKamarInapArray['trf_kamar'];
        }
    }

    public function updatedTglKeluar() { $this->calculateBilling(); }
    public function updatedJamKeluar() { $this->calculateBilling(); }

    public function openIcdModal()
    {
        $this->isIcdModalOpen = true;
    }

    public function selectIcd($code, $name)
    {
        $this->kd_penyakit_akhir = $code;
        $this->nm_penyakit_akhir = $name;
        $this->isIcdModalOpen = false;
    }

    public function save()
    {
        $this->validate([
            'stts_pulang' => 'required',
            'tgl_keluar' => 'required|date',
            'jam_keluar' => 'required',
        ]);

        // SOP #1: Validate lock
        $this->validateLock($this->regPeriksa);

        DB::beginTransaction();
        try {
            // 1. Re-fetch the active model using composite key
            $activeModel = KamarInap::where([
                'no_rawat' => $this->currentKamarInapArray['no_rawat'],
                'kd_kamar' => $this->currentKamarInapArray['kd_kamar'],
                'tgl_masuk' => $this->currentKamarInapArray['tgl_masuk'],
                'jam_masuk' => $this->currentKamarInapArray['jam_masuk'],
            ])->first();

            if (!$activeModel) throw new \Exception("Data inap aktif tidak ditemukan untuk sinkronisasi.");

            // 2. Update KamarInap
            $activeModel->update([
                'tgl_keluar' => $this->tgl_keluar,
                'jam_keluar' => $this->jam_keluar,
                'lama' => $this->lama,
                'ttl_biaya' => $this->total_biaya,
                'stts_pulang' => $this->stts_pulang,
                'diagnosa_akhir' => $this->kd_penyakit_akhir ?: '-',
            ]);

            // 3. Update Kamar Status to KOSONG
            Kamar::where('kd_kamar', $activeModel->kd_kamar)->update(['status' => 'KOSONG']);

            // 4. Update RegPeriksa (Optional but standard in some Khanza variants)
            // Some hospitals update stts_daftar here, some wait for billing.
            // We'll leave it for now to avoid side effects on billing modules.

            DB::commit();
            $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => 'Proses Check Out berhasil dilakukan.', 'icon' => 'success']);
            
            return $this->redirect(route('modul.rawat-inap.show', str_replace('/', '-', $this->no_rawat)), navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', ['title' => 'Gagal!', 'text' => 'Terjadi kesalahan: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function render()
    {
        $listIcd = [];
        if ($this->isIcdModalOpen && strlen($this->searchIcd) >= 3) {
            $listIcd = Penyakit::where('kd_penyakit', 'like', '%' . $this->searchIcd . '%')
                ->orWhere('nm_penyakit', 'like', '%' . $this->searchIcd . '%')
                ->limit(20)
                ->get();
        }

        return view('livewire.modul.rawat-inap.sub-rawat-inap.check-out', [
            'listIcd' => $listIcd,
            'statusOptions' => [
                'Membaik', 'Sembuh', 'Belum Sembuh', 'Rujuk', 'Meninggal', 'APS', 'Pindah RS', 'Lain-lain'
            ]
        ]);
    }
}
