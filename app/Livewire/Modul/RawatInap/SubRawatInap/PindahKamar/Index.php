<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\PindahKamar;

use App\Models\Kamar;
use App\Models\KamarInap;
use App\Models\RegPeriksa;
use App\Repositories\RawatInap\PindahKamarRepository;
use App\Livewire\Concerns\WithOptimisticLocking;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Pindah Kamar Inap Pasien'])]
class Index extends Component
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
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'kamarInap' => function($q) {
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
        // SOP #1: Validate lock using fresh model state
        $this->validateLock($this->regPeriksa->fresh());

        try {
            $data = [
                'no_rawat' => $this->no_rawat,
                'kd_kamar' => $this->kd_kamar,
                'currentKamarInapArray' => $this->currentKamarInapArray,
                'tgl_pindah' => $this->tgl_pindah,
                'jam_pindah' => $this->jam_pindah,
                'pilihan' => $this->pilihan,
                'trf_kamar' => $this->trf_kamar,
                'lama' => $this->lama,
                'total' => $this->total,
            ];

            PindahKamarRepository::savePindahKamar($data);

            $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => 'Proses pindah kamar berhasil dilakukan.', 'icon' => 'success']);
            
            return $this->redirect(route('modul.rawat-inap.show', str_replace('/', '-', $this->no_rawat)), navigate: true);

        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal!', 'text' => 'Terjadi kesalahan: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function render()
    {
        $listKamar = [];
        if ($this->isKamarModalOpen) {
            $listKamar = PindahKamarRepository::searchKamar($this->searchKamar);
        }

        return view('livewire.modul.rawat-inap.sub-rawat-inap.pindah-kamar.index', [
            'listKamar' => $listKamar
        ]);
    }
}
