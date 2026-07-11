<?php

namespace App\Livewire\Modul\RawatInap\KelahiranBayi;

use App\Models\AppSetting;
use App\Models\PasienBayi;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'Pengaturan SKL - SIMRS LaraLite'])]
class SklSettings extends Component
{
    public string $last_skl_number = '';

    public function mount()
    {
        $setting = AppSetting::where('setting_key', 'LAST_SKL_NUMBER')->first();
        if (!$setting) {
            // Auto-detect maximum number from database if not set yet
            $maxSklRecord = PasienBayi::whereNotNull('no_skl')
                ->where('no_skl', 'like', '%/%')
                ->get()
                ->filter(function($b) {
                    $firstPart = explode('/', $b->no_skl)[0];
                    return is_numeric($firstPart);
                })
                ->sortByDesc(function($b) {
                    return intval(explode('/', $b->no_skl)[0]);
                })
                ->first();
            
            $lastVal = 0;
            if ($maxSklRecord) {
                $lastVal = intval(explode('/', $maxSklRecord->no_skl)[0]);
            }

            $setting = AppSetting::create([
                'setting_key' => 'LAST_SKL_NUMBER',
                'setting_value' => strval($lastVal),
                'description' => 'Nomor urutan terakhir untuk Surat Keterangan Lahir (SKL)'
            ]);
        }

        $this->last_skl_number = $setting->setting_value;
    }

    public function save()
    {
        $this->validate([
            'last_skl_number' => 'required|integer|min:0',
        ], [
            'last_skl_number.required' => 'Nomor terakhir SKL wajib diisi.',
            'last_skl_number.integer' => 'Nomor terakhir SKL harus berupa angka.',
            'last_skl_number.min' => 'Nomor terakhir SKL minimal 0.',
        ]);

        AppSetting::updateOrCreate(
            ['setting_key' => 'LAST_SKL_NUMBER'],
            [
                'setting_value' => $this->last_skl_number,
                'description' => 'Nomor urutan terakhir untuk Surat Keterangan Lahir (SKL)'
            ]
        );

        $this->dispatch('swal', [
            'title' => 'Tersimpan!',
            'text' => 'Nomor terakhir SKL berhasil disimpan.',
            'icon' => 'success',
        ]);
    }

    public function render()
    {
        // Preview generation using current year and roman month
        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $romanMonth = $romans[intval(date('m'))] ?? 'I';
        $currentYear = date('Y');
        $nextSeq = intval($this->last_skl_number ?: 0) + 1;
        $previewNoSkl = "{$nextSeq}/KL.RSIAIBI/{$romanMonth}/{$currentYear}";

        return view('livewire.modul.rawat-inap.kelahiran-bayi.skl-settings', compact('previewNoSkl'));
    }
}
