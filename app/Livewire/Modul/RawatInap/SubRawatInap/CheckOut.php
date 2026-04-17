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
            
            // SIMRS Khanza Standard: Calculate days
            // If the difference is less than 24h, but spans across days, it usually counts as one full day.
            // We'll use ceil on the float difference to ensure partial days count as full days.
            $diffInDays = $start->diffInMinutes($end) / (60 * 24);
            $this->lama = (int) ceil($diffInDays);
            
            // Minimum 1 day logic
            if ($this->lama <= 0) $this->lama = 1;

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
            // SOP #4: Use direct query builder update to avoid 'id' column requirement on legacy composite tables
            $updated = KamarInap::where([
                'no_rawat' => $this->currentKamarInapArray['no_rawat'],
                'kd_kamar' => $this->currentKamarInapArray['kd_kamar'],
                'tgl_masuk' => $this->currentKamarInapArray['tgl_masuk'],
                'jam_masuk' => $this->currentKamarInapArray['jam_masuk'],
            ])->update([
                'tgl_keluar' => $this->tgl_keluar,
                'jam_keluar' => $this->jam_keluar,
                'lama' => $this->lama,
                'ttl_biaya' => $this->total_biaya,
                'stts_pulang' => $this->stts_pulang,
                'diagnosa_akhir' => $this->kd_penyakit_akhir ?: '-',
            ]);

            if (!$updated) throw new \Exception("Gagal memperbarui data inap aktif. Data mungkin sudah berubah.");

            // 3. Update Kamar Status to KOSONG
            Kamar::where('kd_kamar', $this->currentKamarInapArray['kd_kamar'])->update(['status' => 'KOSONG']);

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
