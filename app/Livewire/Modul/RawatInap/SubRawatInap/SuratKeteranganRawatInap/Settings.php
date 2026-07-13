<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\SuratKeteranganRawatInap;

use App\Models\AppSetting;
use App\Models\RegPeriksa;
use App\Models\SuratKeteranganRawatInap;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Pengaturan SKRI - SIMRS LaraLite')]
class Settings extends Component
{
    public $noRawat;
    public $regPeriksa;
    public string $last_skri_number = '';

    private function getRomanMonth($month)
    {
        $map = [
            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV', 
            '05' => 'V', '06' => 'VI', '07' => 'VII', '08' => 'VIII', 
            '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
        ];
        return $map[$month] ?? 'I';
    }

    public function mount($no_rawat)
    {
        $this->noRawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien'])
            ->where('no_rawat', $this->noRawat)
            ->firstOrFail();

        $year = date('Y');
        
        // Ensure year setting exists
        $settingYear = AppSetting::where('setting_key', 'LAST_SKRI_YEAR')->first();
        if (!$settingYear) {
            $settingYear = AppSetting::create([
                'setting_key' => 'LAST_SKRI_YEAR',
                'setting_value' => $year,
                'description' => 'Tahun terakhir generate Surat Keterangan Rawat Inap (SKRI)'
            ]);
        }

        // Ensure number setting exists
        $settingNum = AppSetting::where('setting_key', 'LAST_SKRI_NUMBER')->first();
        if (!$settingNum) {
            $yearSuffix = '/' . $year;
            $maxYear = SuratKeteranganRawatInap::where('no_surat', 'like', '%/VK/RSIA/IBI/%' . $yearSuffix)
                ->orderBy('no_surat', 'desc')
                ->first();
            
            $lastVal = 0;
            if ($maxYear) {
                // Format: 001/VK/RSIA/IBI/VII/2026 -> Extract first 3 chars
                $lastVal = intval(substr($maxYear->no_surat, 0, 3));
            }

            $settingNum = AppSetting::create([
                'setting_key' => 'LAST_SKRI_NUMBER',
                'setting_value' => strval($lastVal),
                'description' => 'Nomor urutan terakhir untuk Surat Keterangan Rawat Inap (SKRI)'
            ]);
        }

        // Reset if year changed
        if ($settingYear->setting_value !== $year) {
            $settingYear->update(['setting_value' => $year]);
            $settingNum->update(['setting_value' => '0']);
            $settingNum->refresh();
        }

        $this->last_skri_number = $settingNum->setting_value;
    }

    public function save()
    {
        $this->validate([
            'last_skri_number' => 'required|integer|min:0',
        ], [
            'last_skri_number.required' => 'Nomor terakhir SKRI wajib diisi.',
            'last_skri_number.integer' => 'Nomor terakhir SKRI harus berupa angka.',
            'last_skri_number.min' => 'Nomor terakhir SKRI minimal 0.',
        ]);

        AppSetting::updateOrCreate(
            ['setting_key' => 'LAST_SKRI_NUMBER'],
            [
                'setting_value' => $this->last_skri_number,
                'description' => 'Nomor urutan terakhir untuk Surat Keterangan Rawat Inap (SKRI)'
            ]
        );

        $this->dispatch('swal', [
            'title' => 'Tersimpan!',
            'text' => 'Nomor terakhir SKRI berhasil disimpan.',
            'icon' => 'success',
        ]);
    }

    public function render()
    {
        $year = date('Y');
        $month = date('m');
        $romanMonth = $this->getRomanMonth($month);

        $nextSeq = intval($this->last_skri_number ?: 0) + 1;
        $urut = str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
        
        $previewNoSkri = sprintf('%s/VK/RSIA/IBI/%s/%s', $urut, $romanMonth, $year);

        return view('livewire.modul.rawat-inap.sub-rawat-inap.surat-keterangan-rawat-inap.settings', compact('previewNoSkri'));
    }
}
