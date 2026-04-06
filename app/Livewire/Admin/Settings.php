<?php

namespace App\Livewire\Admin;

use App\Models\AppSetting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Pengaturan Aplikasi'])]
class Settings extends Component
{
    public $google_vision_api_key;
    public $nama_instansi;

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        // Load API Key
        $visionKey = AppSetting::where('setting_key', 'GOOGLE_VISION_API_KEY')->first();
        if ($visionKey) {
            $this->google_vision_api_key = $visionKey->setting_value;
        }

        // Load Instansi Name
        $instansi = AppSetting::where('setting_key', 'NAMA_INSTANSI')->first();
        if ($instansi) {
            $this->nama_instansi = $instansi->setting_value;
        }
    }

    public function save()
    {
        $this->validate([
            'google_vision_api_key' => 'nullable|string',
            'nama_instansi' => 'nullable|string',
        ]);

        // Simpan API Key
        AppSetting::updateOrCreate(
            ['setting_key' => 'GOOGLE_VISION_API_KEY'],
            [
                'setting_value' => $this->google_vision_api_key, 
                'description'   => 'Kunci API (API Key) untuk layanan Google Cloud Vision OCR Foto KTP'
            ]
        );

        // Simpan Nama Instansi
        AppSetting::updateOrCreate(
            ['setting_key' => 'NAMA_INSTANSI'],
            [
                'setting_value' => $this->nama_instansi,
                'description'   => 'Nama Instansi / Rumah Sakit'
            ]
        );

        $this->dispatch('swal', [
            'title' => 'Tersimpan!',
            'text'  => 'Pengaturan aplikasi berhasil disimpan.',
            'icon'  => 'success',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.settings');
    }
}
