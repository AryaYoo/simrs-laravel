<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap;

use App\Models\Kamar;
use App\Models\KamarInap;
use App\Models\RegPeriksa;
use App\Livewire\Concerns\WithOptimisticLocking;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Pindah Kamar Inap Pasien'])]
class PindahKamar extends Component
{
    use WithOptimisticLocking;
    public string $no_rawat;
    public $regPeriksa;
    public $currentKamarInapArray;

    // Form Fields
    public $kd_kamar;
    public $nm_bangsal;
    public $trf_kamar;
    public $status_kamar;
    public $kelas_kamar;
    public $tgl_pindah;
    public $jam_pindah;
    public $pilihan = 3; // Default option 3

    // Computed / Preview Fields
    public $lama = 0;
    public $total = 0;

    // Modal State
    public bool $isKamarModalOpen = false;
    public string $searchKamar = '';

    public function mount(string $no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'kamarInap' => function($q) {
            $q->where('tgl_keluar', '0000-00-00')->orderBy('tgl_masuk', 'desc')->orderBy('jam_masuk', 'desc');
        }, 'kamarInap.kamar.bangsal'])
            ->where('no_rawat', $this->no_rawat)
            ->firstOrFail();

        $activeStay = $this->regPeriksa->kamarInap->first();
        $this->currentKamarInapArray = $activeStay ? $activeStay->toArray() : null;

        $this->tgl_pindah = now()->format('Y-m-d');
        $this->jam_pindah = now()->format('H:i:s');

        // SOP #1: Initialize concurrency lock
        $this->initializeLock($this->regPeriksa);

        $this->calculatePreview();
    }

    public function calculatePreview()
    {
        if ($this->currentKamarInapArray) {
            $start = Carbon::parse($this->currentKamarInapArray['tgl_masuk'] . ' ' . $this->currentKamarInapArray['jam_masuk']);
            $end = Carbon::parse($this->tgl_pindah . ' ' . $this->jam_pindah);
            
            // Standard SIMRS Khanza DATEDIFF logic:
            $days = $start->diffInDays($end);
            
            // If pindah on same day, count as 1 if it's Option 3/4
            if ($days == 0 && ($this->pilihan == 3 || $this->pilihan == 4)) {
                $this->lama = 1;
            } else {
                $this->lama = $days;
            }

            $currentRate = $this->currentKamarInapArray['trf_kamar'];
            
            // Option 4: Use highest rate
            if ($this->pilihan == 4 && $this->trf_kamar > $currentRate) {
                $currentRate = $this->trf_kamar;
            }

            $this->total = $this->lama * $currentRate;
        }
    }

    public function updatedPilihan() { $this->calculatePreview(); }
    public function updatedTglPindah() { $this->calculatePreview(); }
    public function updatedJamPindah() { $this->calculatePreview(); }

    public function openKamarModal()
    {
        $this->isKamarModalOpen = true;
    }

    public function selectKamar($kd_kamar, $nm_bangsal, $trf_kamar, $status, $kelas)
    {
        $this->kd_kamar = $kd_kamar;
        $this->nm_bangsal = $nm_bangsal;
        $this->trf_kamar = $trf_kamar;
        $this->status_kamar = $status;
        $this->kelas_kamar = $kelas;
        
        $this->isKamarModalOpen = false;
        $this->calculatePreview();
    }

    public function save()
    {
        if (!$this->kd_kamar) {
            $this->dispatch('swal', ['title' => 'Peringatan', 'text' => 'Pilih kamar baru terlebih dahulu.', 'icon' => 'warning']);
            return;
        }

        if (!$this->currentKamarInapArray) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Data inap aktif tidak ditemukan.', 'icon' => 'error']);
            return;
        }

        // SOP #1: Validate concurrency lock
        $this->validateLock($this->regPeriksa);

        DB::beginTransaction();
        try {
            $tgl_now = $this->tgl_pindah;
            $jam_now = $this->jam_pindah;

            // Re-fetch the model for updates/deletions (using composite key)
            $activeModel = KamarInap::where([
                'no_rawat' => $this->currentKamarInapArray['no_rawat'],
                'kd_kamar' => $this->currentKamarInapArray['kd_kamar'],
                'tgl_masuk' => $this->currentKamarInapArray['tgl_masuk'],
                'jam_masuk' => $this->currentKamarInapArray['jam_masuk'],
            ])->first();

            if (!$activeModel) throw new \Exception("Model kamar_inap tidak ditemukan untuk sinkronisasi.");

            switch ($this->pilihan) {
                case 1:
                    // Option 1: Hapus kamar lama, buat baru mulai dari tgl masuk asal
                    $tgl_asal = $activeModel->tgl_masuk;
                    $jam_asal = $activeModel->jam_masuk;
                    
                    // Set old kamar to KOSONG (if it's the only one using it)
                    Kamar::where('kd_kamar', $activeModel->kd_kamar)->update(['status' => 'KOSONG']);
                    
                    $activeModel->delete();

                    KamarInap::create([
                        'no_rawat' => $this->no_rawat,
                        'kd_kamar' => $this->kd_kamar,
                        'trf_kamar' => $this->trf_kamar,
                        'diagnosa_awal' => $this->currentKamarInapArray['diagnosa_awal'] ?? '-',
                        'diagnosa_akhir' => '-',
                        'tgl_masuk' => $tgl_asal,
                        'jam_masuk' => $jam_asal,
                        'tgl_keluar' => '0000-00-00',
                        'jam_keluar' => '00:00:00',
                        'lama' => 0,
                        'ttl_biaya' => 0,
                        'stts_pulang' => '-',
                    ]);
                    break;

                case 2:
                    // Option 2: Ganti kamar di record yang sama
                    Kamar::where('kd_kamar', $activeModel->kd_kamar)->update(['status' => 'KOSONG']);
                    
                    $activeModel->update([
                        'kd_kamar' => $this->kd_kamar,
                        'trf_kamar' => $this->trf_kamar,
                    ]);
                    break;

                case 3:
                case 4:
                    // Option 3/4: Tutup yang lama, buka yang baru
                    $finalRate = $activeModel->trf_kamar;
                    if ($this->pilihan == 4 && $this->trf_kamar > $finalRate) {
                        $finalRate = $this->trf_kamar;
                    }

                    $this->calculatePreview(); // Refresh lama/total

                    $activeModel->update([
                        'tgl_keluar' => $tgl_now,
                        'jam_keluar' => $jam_now,
                        'lama' => $this->lama,
                        'trf_kamar' => $finalRate,
                        'ttl_biaya' => $this->total,
                        'stts_pulang' => 'Pindah',
                    ]);

                    Kamar::where('kd_kamar', $activeModel->kd_kamar)->update(['status' => 'KOSONG']);

                    KamarInap::create([
                        'no_rawat' => $this->no_rawat,
                        'kd_kamar' => $this->kd_kamar,
                        'trf_kamar' => $this->trf_kamar,
                        'diagnosa_awal' => $this->currentKamarInapArray['diagnosa_awal'] ?? '-',
                        'diagnosa_akhir' => '-',
                        'tgl_masuk' => $tgl_now,
                        'jam_masuk' => $jam_now,
                        'tgl_keluar' => '0000-00-00',
                        'jam_keluar' => '00:00:00',
                        'lama' => 0,
                        'ttl_biaya' => 0,
                        'stts_pulang' => '-',
                    ]);
                    break;
            }

            // Update new room status to ISI
            Kamar::where('kd_kamar', $this->kd_kamar)->update(['status' => 'ISI']);

            DB::commit();
            $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => 'Proses pindah kamar berhasil dilakukan.', 'icon' => 'success']);
            
            return $this->redirect(route('modul.rawat-inap.show', str_replace('/', '-', $this->no_rawat)), navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', ['title' => 'Gagal!', 'text' => 'Terjadi kesalahan: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function render()
    {
        $listKamar = [];
        if ($this->isKamarModalOpen) {
            $query = Kamar::with('bangsal');
            if (strlen($this->searchKamar) >= 2) {
                $query->where('kd_kamar', 'like', '%' . $this->searchKamar . '%')
                      ->orWhereHas('bangsal', function($q) {
                          $q->where('nm_bangsal', 'like', '%' . $this->searchKamar . '%');
                      });
            }
            $listKamar = $query->limit(50)->get();
        }

        return view('livewire.modul.rawat-inap.sub-rawat-inap.pindah-kamar', [
            'listKamar' => $listKamar
        ]);
    }
}
