<?php

namespace App\Livewire\Modul\RawatJalan\SubRawatJalan\TriaseIgd;

use App\Repositories\RawatJalan\TriaseIgdRepository;
use App\Livewire\Concerns\WithOptimisticLocking;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Data Triase IGD', 'hideSidebar' => true])]
class Index extends Component
{
    use WithOptimisticLocking;

    public string $no_rawat;
    public $regPeriksa;
    public string $activeTab = 'input'; // 'input' or 'data'
    public string $activeSubTab = 'primer'; // 'primer' or 'sekunder'
    public string $activeSkalaTab = 'skala1'; // 'skala1' or 'skala2'

    // Form Fields - Data Utama
    public $tgl_kunjungan, $cara_masuk, $alat_transportasi, $alasan_kedatangan, $keterangan_kedatangan;
    public $kode_kasus;
    public $tekanan_darah, $nadi, $pernapasan, $suhu, $saturasi_o2, $nyeri;

    // Form Fields - Triase Primer
    public $keluhan_utama, $kebutuhan_khusus, $catatan_primer, $plan_primer;
    
    // Form Fields - Triase Sekunder
    public $anamnesa_singkat, $catatan_sekunder, $plan_sekunder;
    
    public $selectedPemeriksaan;
    public array $selectedSkala1 = [];
    public array $selectedSkala2 = [];
    public array $selectedSkala3 = [];
    public array $selectedSkala4 = [];
    public array $selectedSkala5 = [];

    public function mount(string $no_rawat): void
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = TriaseIgdRepository::getRegPeriksa($this->no_rawat);

        if (!$this->regPeriksa) {
            abort(404, 'Data registrasi tidak ditemukan.');
        }

        $this->loadData();
        $this->initializeLock($this->regPeriksa);
    }

    public function loadData()
    {
        $triase = TriaseIgdRepository::getTriaseData($this->no_rawat);

        if ($triase) {
            $this->tgl_kunjungan = $triase->tgl_kunjungan;
            $this->cara_masuk = $triase->cara_masuk;
            $this->alat_transportasi = $triase->alat_transportasi;
            $this->alasan_kedatangan = $triase->alasan_kedatangan;
            $this->keterangan_kedatangan = $triase->keterangan_kedatangan;
            $this->kode_kasus = $triase->kode_kasus;
            $this->tekanan_darah = $triase->tekanan_darah;
            $this->nadi = $triase->nadi;
            $this->pernapasan = $triase->pernapasan;
            $this->suhu = $triase->suhu;
            $this->saturasi_o2 = $triase->saturasi_o2;
            $this->nyeri = $triase->nyeri;
        } else {
            $this->tgl_kunjungan = now()->format('Y-m-d H:i:s');
            $this->cara_masuk = 'Jalan';
            $this->alat_transportasi = '-';
            $this->alasan_kedatangan = 'Datang Sendiri';
            $this->keterangan_kedatangan = '-';
            $this->kode_kasus = '';
            $this->tekanan_darah = '-';
            $this->nadi = '-';
            $this->pernapasan = '-';
            $this->suhu = '-';
            $this->saturasi_o2 = '-';
            $this->nyeri = '-';
        }

        // Load Primer Data
        $primer = TriaseIgdRepository::getTriasePrimer($this->no_rawat);
        if ($primer) {
            $this->keluhan_utama = $primer->keluhan_utama;
            $this->kebutuhan_khusus = $primer->kebutuhan_khusus;
            $this->catatan_primer = $primer->catatan;
            $this->plan_primer = $primer->plan;
        } else {
            $this->keluhan_utama = '-';
            $this->kebutuhan_khusus = '-';
            $this->catatan_primer = '-';
            $this->plan_primer = 'Ruang Resusitasi';
        }

        // Load Sekunder Data
        $sekunder = TriaseIgdRepository::getTriaseSekunder($this->no_rawat);
        if ($sekunder) {
            $this->anamnesa_singkat = $sekunder->anamnesa_singkat;
            $this->catatan_sekunder = $sekunder->catatan;
            $this->plan_sekunder = $sekunder->plan;
        } else {
            $this->anamnesa_singkat = '-';
            $this->catatan_sekunder = '-';
            $this->plan_sekunder = 'Zona Kuning';
        }

        $scales = TriaseIgdRepository::getSelectedScales($this->no_rawat);
        $this->selectedSkala1 = $scales['skala1'];
        $this->selectedSkala2 = $scales['skala2'];
        $this->selectedSkala3 = $scales['skala3'];
        $this->selectedSkala4 = $scales['skala4'];
        $this->selectedSkala5 = $scales['skala5'];

        // Set default pemeriksaan if not set
        if (!$this->selectedPemeriksaan) {
            $first = TriaseIgdRepository::getMasterPemeriksaan()->first();
            $this->selectedPemeriksaan = $first ? $first->kode_pemeriksaan : null;
        }
    }

    public function getMasterPemeriksaanProperty()
    {
        return TriaseIgdRepository::getMasterPemeriksaan();
    }

    public function getMacamKasusListProperty()
    {
        return TriaseIgdRepository::getMacamKasus();
    }

    public function getTriaseHistoryProperty()
    {
        return TriaseIgdRepository::getTriaseHistory($this->regPeriksa->no_rkm_medis);
    }

    public function save()
    {
        // SOP Concurrency: validate lock
        $this->validateLock($this->regPeriksa->fresh());

        $this->validate([
            'tgl_kunjungan' => 'required',
            'cara_masuk' => 'required',
            'alat_transportasi' => 'required',
            'alasan_kedatangan' => 'required',
            'kode_kasus' => 'required',
        ]);

        try {
            // 1. Save Main Triage Data
            $mainData = [
                'no_rawat' => $this->no_rawat,
                'tgl_kunjungan' => $this->tgl_kunjungan,
                'cara_masuk' => $this->cara_masuk,
                'alat_transportasi' => $this->alat_transportasi,
                'alasan_kedatangan' => $this->alasan_kedatangan,
                'keterangan_kedatangan' => $this->keterangan_kedatangan ?: '-',
                'kode_kasus' => $this->kode_kasus,
                'tekanan_darah' => $this->tekanan_darah ?: '-',
                'nadi' => $this->nadi ?: '-',
                'pernapasan' => $this->pernapasan ?: '-',
                'suhu' => $this->suhu ?: '-',
                'saturasi_o2' => $this->saturasi_o2 ?: '-',
                'nyeri' => $this->nyeri ?: '-',
            ];
            TriaseIgdRepository::saveTriase($mainData);

            // 2. Save Primer & Sekunder & Scales
            $primerData = [
                'keluhan_utama' => $this->keluhan_utama ?: '-',
                'kebutuhan_khusus' => $this->kebutuhan_khusus ?: '-',
                'catatan' => $this->catatan_primer ?: '-',
                'plan' => $this->plan_primer ?: 'Ruang Resusitasi',
                'tanggaltriase' => now(),
                'nik' => auth()->user()->nik ?? '-',
            ];

            $sekunderData = [
                'anamnesa_singkat' => $this->anamnesa_singkat ?: '-',
                'catatan' => $this->catatan_sekunder ?: '-',
                'plan' => $this->plan_sekunder ?: 'Zona Kuning',
                'tanggaltriase' => now(),
                'nik' => auth()->user()->nik ?? '-',
            ];

            $scales = [
                'skala1' => $this->selectedSkala1,
                'skala2' => $this->selectedSkala2,
                'skala3' => $this->selectedSkala3,
                'skala4' => $this->selectedSkala4,
                'skala5' => $this->selectedSkala5,
            ];

            TriaseIgdRepository::saveFullAssessment($this->no_rawat, $primerData, $sekunderData, $scales);

            $this->dispatch('swal', ['title' => 'Berhasil', 'text' => 'Seluruh data triase IGD berhasil disimpan.', 'icon' => 'success']);
            $this->loadData();
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Gagal menyimpan data: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function selectPemeriksaan($kode)
    {
        $this->selectedPemeriksaan = $kode;
    }

    public function toggleSkala($skalaNum, $kode)
    {
        $property = "selectedSkala{$skalaNum}";
        if (in_array($kode, $this->$property)) {
            $this->$property = array_values(array_diff($this->$property, [$kode]));
        } else {
            $this->{$property}[] = $kode;
        }
    }

    public function render()
    {
        return view('livewire.modul.rawat-jalan.sub-rawat-jalan.triase-igd.index');
    }
}
