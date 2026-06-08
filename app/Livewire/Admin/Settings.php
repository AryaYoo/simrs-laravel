<?php

namespace App\Livewire\Admin;

use App\Models\AppSetting;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\SettingCetakWeb;

#[Layout('layouts.app', ['title' => 'Pengaturan Aplikasi'])]
class Settings extends Component
{
    use WithFileUploads;
    public $google_vision_api_key;
    public $nama_instansi;

    // Fields for Login Background
    public $login_background; // for upload
    public $login_background_preview; // for existing base64 preview

    // Fields for Cetak Web
    public $cetak_nama_instansi;
    public $cetak_alamat_instansi;
    public $cetak_kabupaten;
    public $cetak_propinsi;
    public $cetak_kontak;
    public $cetak_email;
    public $cetak_logo; // for upload
    public $cetak_logo_preview; // for existing base64 preview
    public $cetak_background; // for upload
    public $cetak_background_preview; // for existing base64 preview

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

        // Load Login Background
        $loginBg = AppSetting::where('setting_key', 'LOGIN_BACKGROUND_IMAGE')->first();
        if ($loginBg && !empty($loginBg->setting_value)) {
            $value = $loginBg->setting_value;
            // Support both new format (full data URI) and old format (raw base64)
            if (str_starts_with($value, 'data:')) {
                $this->login_background_preview = $value;
            } else {
                // Old format: raw base64 — wrap with default webp MIME for compat
                $this->login_background_preview = 'data:image/webp;base64,' . $value;
            }
        }

        // Load Cetak Web Settings
        $cetakSetting = SettingCetakWeb::first();
        if ($cetakSetting) {
            $this->cetak_nama_instansi = $cetakSetting->nama_instansi;
            $this->cetak_alamat_instansi = $cetakSetting->alamat_instansi;
            $this->cetak_kabupaten = $cetakSetting->kabupaten;
            $this->cetak_propinsi = $cetakSetting->propinsi;
            $this->cetak_kontak = $cetakSetting->kontak;
            $this->cetak_email = $cetakSetting->email;
            $this->cetak_logo_preview = $cetakSetting->logo;
            $this->cetak_background_preview = $cetakSetting->background;
        }
    }

    public function save()
    {
        $this->validate([
            'google_vision_api_key' => 'nullable|string',
            'nama_instansi' => 'nullable|string',
            'cetak_nama_instansi' => 'nullable|string',
            'cetak_alamat_instansi' => 'nullable|string',
            'cetak_kabupaten' => 'nullable|string',
            'cetak_propinsi' => 'nullable|string',
            'cetak_kontak' => 'nullable|string',
            'cetak_email' => 'nullable|email',
            'cetak_logo' => 'nullable|image|max:2048', // max 2MB
            'cetak_background' => 'nullable|image|max:2048', // max 2MB
            'login_background' => 'nullable|image|mimes:webp,jpg,jpeg,png|max:512', // max ~500KB
        ], [
            'login_background.max' => 'Ukuran gambar login maksimal 500KB.',
            'login_background.mimes' => 'Format gambar login harus WebP, JPG, atau PNG.',
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

        // Simpan Pengaturan Cetak
        $cetakSetting = SettingCetakWeb::first();
        if (!$cetakSetting) {
            $cetakSetting = new SettingCetakWeb();
        }

        $cetakSetting->nama_instansi = $this->cetak_nama_instansi ?? '';
        $cetakSetting->alamat_instansi = $this->cetak_alamat_instansi;
        $cetakSetting->kabupaten = $this->cetak_kabupaten;
        $cetakSetting->propinsi = $this->cetak_propinsi;
        $cetakSetting->kontak = $this->cetak_kontak;
        $cetakSetting->email = $this->cetak_email;

        // Proses Logo (Convert to Base64 Text)
        if ($this->cetak_logo) {
            $logoContent = file_get_contents($this->cetak_logo->getRealPath());
            $cetakSetting->logo = base64_encode($logoContent);
            $this->cetak_logo_preview = $cetakSetting->logo; // Update preview
            $this->cetak_logo = null; // Clear upload input
        }

        // Proses Background (Convert to Base64 Text)
        if ($this->cetak_background) {
            $bgContent = file_get_contents($this->cetak_background->getRealPath());
            $cetakSetting->background = base64_encode($bgContent);
            $this->cetak_background_preview = $cetakSetting->background; // Update preview
            $this->cetak_background = null; // Clear upload input
        }

        $cetakSetting->save();

        // Simpan Login Background
        if ($this->login_background) {
            $mime = $this->login_background->getMimeType() ?? 'image/webp';
            $bgContent = file_get_contents($this->login_background->getRealPath());
            $dataUri = 'data:' . $mime . ';base64,' . base64_encode($bgContent);
            AppSetting::updateOrCreate(
                ['setting_key' => 'LOGIN_BACKGROUND_IMAGE'],
                [
                    'setting_value' => $dataUri,
                    'description'   => 'Gambar latar halaman login (Data URI, format WebP/JPG/PNG, maks 500KB)'
                ]
            );
            $this->login_background_preview = $dataUri;
            $this->login_background = null;
        }

        $this->dispatch('swal', [
            'title' => 'Tersimpan!',
            'text'  => 'Pengaturan aplikasi berhasil disimpan.',
            'icon'  => 'success',
        ]);
    }

    public function removeLoginBackground()
    {
        AppSetting::updateOrCreate(
            ['setting_key' => 'LOGIN_BACKGROUND_IMAGE'],
            [
                'setting_value' => '',
                'description'   => 'Gambar latar halaman login (Base64 encoded, format WebP/JPG/PNG, maks 500KB)'
            ]
        );
        $this->login_background_preview = null;
        $this->login_background = null;

        $this->dispatch('swal', [
            'title' => 'Dihapus!',
            'text'  => 'Gambar login telah dihapus. Halaman login akan kembali ke tampilan default.',
            'icon'  => 'success',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.settings');
    }
}
