<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\SuratKeteranganRawatInap;

use Livewire\Component;
use App\Models\RegPeriksa;
use App\Models\SuratKeteranganRawatInap;
use App\Models\AppSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
#[Title('Surat Keterangan Rawat Inap - SIMRS LaraLite')]
class Index extends Component
{
    public $noRawat;
    public $regPeriksa;

    // Create modal state
    public bool $showCreateModal = false;
    public string $tanggal_awal = '';
    public string $tanggal_akhir = '';

    public function mount($no_rawat)
    {
        $this->noRawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter'])
            ->where('no_rawat', $this->noRawat)
            ->firstOrFail();

        $this->tanggal_awal = date('Y-m-d');
        $this->tanggal_akhir = date('Y-m-d');
    }

    public function openCreateModal(): void
    {
        $this->tanggal_awal = date('Y-m-d');
        $this->tanggal_akhir = date('Y-m-d');
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->reset(['tanggal_awal', 'tanggal_akhir']);
    }

    private function getRomanMonth($month)
    {
        $map = [
            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV', 
            '05' => 'V', '06' => 'VI', '07' => 'VII', '08' => 'VIII', 
            '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
        ];
        return $map[$month] ?? 'I';
    }

    /**
     * Generate a unique no_surat format: 001/VK/RSIA/IBI/VI/2026
     */
    private function generateNoSurat(): string
    {
        $year = date('Y');
        $month = date('m');
        $romanMonth = $this->getRomanMonth($month);

        // Get or create number tracking in AppSetting
        $settingYear = AppSetting::where('setting_key', 'LAST_SKRI_YEAR')->first();
        $settingNum  = AppSetting::where('setting_key', 'LAST_SKRI_NUMBER')->first();

        if (!$settingYear) {
            $settingYear = AppSetting::create([
                'setting_key'   => 'LAST_SKRI_YEAR',
                'setting_value' => $year,
                'description'   => 'Tahun terakhir generate Surat Keterangan Rawat Inap (SKRI)',
            ]);
        }

        if (!$settingNum) {
            $settingNum = AppSetting::create([
                'setting_key'   => 'LAST_SKRI_NUMBER',
                'setting_value' => '0',
                'description'   => 'Nomor urutan terakhir untuk Surat Keterangan Rawat Inap (SKRI)',
            ]);
        }

        // Reset counter if year changed
        if ($settingYear->setting_value !== $year) {
            $settingYear->update(['setting_value' => $year]);
            $settingNum->update(['setting_value' => '0']);
            $settingNum->refresh();
        }

        $nextSeq    = intval($settingNum->setting_value) + 1;
        $urut       = str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
        
        $noSurat    = sprintf('%s/VK/RSIA/IBI/%s/%s', $urut, $romanMonth, $year);

        // Persist the new sequence
        $settingNum->update(['setting_value' => strval($nextSeq)]);

        return $noSurat;
    }

    public function store(): void
    {
        $this->validate([
            'tanggal_awal'  => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ], [
            'tanggal_awal.required'                => 'Tanggal awal wajib diisi.',
            'tanggal_akhir.required'               => 'Tanggal akhir wajib diisi.',
            'tanggal_akhir.after_or_equal'         => 'Tanggal akhir harus sama atau setelah tanggal awal.',
        ]);

        $noSurat = $this->generateNoSurat();

        SuratKeteranganRawatInap::create([
            'no_surat'     => $noSurat,
            'no_rawat'     => $this->noRawat,
            'tanggalawal'  => $this->tanggal_awal,
            'tanggalakhir' => $this->tanggal_akhir,
        ]);

        $this->showCreateModal = false;
        $this->reset(['tanggal_awal', 'tanggal_akhir']);

        $this->dispatch('swal', [
            'title' => 'Berhasil!',
            'text'  => 'Surat Keterangan Rawat Inap berhasil dibuat.',
            'icon'  => 'success',
        ]);
    }

    public function delete(string $noSurat): void
    {
        SuratKeteranganRawatInap::where('no_surat', $noSurat)
            ->where('no_rawat', $this->noRawat)
            ->delete();

        $this->dispatch('swal', [
            'title' => 'Dihapus!',
            'text'  => 'Surat berhasil dihapus.',
            'icon'  => 'success',
        ]);
    }

    public function getPreviewNoSurat(): string
    {
        $year = date('Y');
        $month = date('m');
        $romanMonth = $this->getRomanMonth($month);

        $settingYear = AppSetting::where('setting_key', 'LAST_SKRI_YEAR')->first();
        $settingNum  = AppSetting::where('setting_key', 'LAST_SKRI_NUMBER')->first();

        $currentYear = $settingYear ? $settingYear->setting_value : $year;
        $currentNum = $settingNum ? intval($settingNum->setting_value) : 0;

        if ($currentYear !== $year) {
            $currentNum = 0;
        }

        $nextSeq = $currentNum + 1;
        $urut = str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
        
        return sprintf('%s/VK/RSIA/IBI/%s/%s', $urut, $romanMonth, $year);
    }

    public function render()
    {
        return view('livewire.modul.rawat-inap.sub-rawat-inap.surat-keterangan-rawat-inap.index', [
            'suratList' => SuratKeteranganRawatInap::where('no_rawat', $this->noRawat)->latest('no_surat')->get(),
            'previewNoSurat' => $this->getPreviewNoSurat()
        ]);
    }
}
