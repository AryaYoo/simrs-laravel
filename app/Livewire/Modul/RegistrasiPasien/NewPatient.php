<?php

namespace App\Livewire\Modul\RegistrasiPasien;

use App\Models\Pasien;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\AppSetting;

#[Layout('layouts.app', ['title' => 'Tambah Pasien Baru'])]
class NewPatient extends Component
{
    use WithFileUploads;

    public $ktp_image;
    
    public $no_rkm_medis;
    public $nm_pasien;
    public $no_ktp;
    public $jk = 'L';
    public $tmp_lahir;
    public $tgl_lahir;
    public $nm_ibu;
    public $alamat;
    public $gol_darah = '-';
    public $stts_nikah = 'BELUM MENIKAH';
    public $agama = 'ISLAM';
    public $no_tlp;

    public function mount()
    {
        $this->generateNoRkmMedis();
        $this->tgl_lahir = Carbon::now()->format('Y-m-d');
    }

    public function generateNoRkmMedis()
    {
        $lastPasien = Pasien::select('no_rkm_medis')->orderBy('no_rkm_medis', 'desc')->first();
        
        if ($lastPasien) {
            $lastNumber = intval($lastPasien->no_rkm_medis);
            $newNumber = $lastNumber + 1;
            $this->no_rkm_medis = str_pad($newNumber, 6, "0", STR_PAD_LEFT);
        } else {
            $this->no_rkm_medis = '000001';
        }
    }

    public function updatedKtpImage()
    {
        $this->validate([
            'ktp_image' => 'image|max:2048',
        ]);

        $this->processKtpOcr();
    }

    public function processKtpFromBase64($base64Image)
    {
        // Hapus header data URL jika ada (e.g. "data:image/png;base64,")
        $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);

        $apiKeySetting = AppSetting::where('setting_key', 'GOOGLE_VISION_API_KEY')->first();
        if (!$apiKeySetting || empty($apiKeySetting->setting_value)) {
            $this->dispatch('swal', [
                'title' => 'API Key Belum Diset',
                'text'  => 'Silakan atur Google Vision API Key di menu Pengaturan Aplikasi.',
                'icon'  => 'warning',
            ]);
            return;
        }

        $apiKey = $apiKeySetting->setting_value;

        try {
            $response = Http::post("https://vision.googleapis.com/v1/images:annotate?key={$apiKey}", [
                'requests' => [[
                    'image'    => ['content' => $base64Image],
                    'features' => [['type' => 'TEXT_DETECTION']],
                ]]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['responses'][0]['textAnnotations'][0]['description'])) {
                    $this->parseKtpText($data['responses'][0]['textAnnotations'][0]['description']);
                    $this->dispatch('swal', [
                        'title' => 'Berhasil!',
                        'text'  => 'Data KTP berhasil diekstrak dari webcam.',
                        'icon'  => 'success',
                    ]);
                } else {
                    $this->dispatch('swal', [
                        'title' => 'Gagal Membaca',
                        'text'  => 'KTP tidak terdeteksi. Coba dekatkan KTP ke kamera.',
                        'icon'  => 'warning',
                    ]);
                }
            }
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Gagal!',
                'text'  => substr($e->getMessage(), 0, 150),
                'icon'  => 'error',
            ]);
        }
    }

    public function processKtpOcr()
    {
        // Ambil Key dari Setting
        $apiKeySetting = AppSetting::where('setting_key', 'GOOGLE_VISION_API_KEY')->first();
        if (!$apiKeySetting || empty($apiKeySetting->setting_value)) {
            $this->dispatch('swal', [
                'title' => 'API Key Belum Diset',
                'text'  => 'Silakan atur Google Vision API Key di menu Pengaturan Aplikasi (Master Data).',
                'icon'  => 'warning',
            ]);
            return;
        }

        $apiKey = $apiKeySetting->setting_value;
        $imageData = base64_encode(file_get_contents($this->ktp_image->getRealPath()));

        try {
            $response = Http::post("https://vision.googleapis.com/v1/images:annotate?key={$apiKey}", [
                'requests' => [
                    [
                        'image' => [
                            'content' => $imageData
                        ],
                        'features' => [
                            [
                                'type' => 'TEXT_DETECTION'
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['responses'][0]['textAnnotations'][0]['description'])) {
                    $rawText = $data['responses'][0]['textAnnotations'][0]['description'];
                    $this->parseKtpText($rawText);

                    $this->dispatch('swal', [
                        'title' => 'Berhasil!',
                        'text'  => 'Data KTP berhasil diekstrak dan diisi ke dalam form.',
                        'icon'  => 'success',
                    ]);
                } else {
                    $this->dispatch('swal', [
                        'title' => 'Gagal Membaca KTP',
                        'text'  => 'Tidak ada teks yang terdeteksi di foto.',
                        'icon'  => 'error',
                    ]);
                }
            } else {
                $this->dispatch('swal', [
                    'title' => 'API Error',
                    'text'  => 'Terjadi kesalahan saat menghubungi API Google Vision.',
                    'icon'  => 'error',
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Gagal!',
                'text'  => 'Proses unggah gagal: ' . substr($e->getMessage(), 0, 150),
                'icon'  => 'error',
            ]);
        }
    }

    private function parseKtpText($text)
    {
        $lines = explode("\n", $text);
        
        $nik_found = false;

        foreach ($lines as $i => $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Cari NIK
            if (preg_match('/(?:^|\D)(\d{16})(?:\D|$)/', $line, $matches) || preg_match('/NIK\s*:\s*(\d{16})/i', $line, $matches)) {
                $this->no_ktp = $matches[1];
                $nik_found = true;
            }

            // Cari Nama
            if (str_contains(strtolower($line), 'nama')) {
                $nama = trim(preg_replace('/nama|\:|:/i', '', $line));
                if (empty($nama) && isset($lines[$i+1])) {
                    $nama = trim($lines[$i+1]);
                }
                if(strlen($nama) > 2) $this->nm_pasien = substr(strtoupper($nama), 0, 40);
            }
            
            // Cari Agama
            if (str_contains(strtolower($line), 'agama')) {
                $agamaText = strtoupper($line);
                if (str_contains($agamaText, 'ISLAM')) $this->agama = 'ISLAM';
                elseif (str_contains($agamaText, 'KRISTEN')) $this->agama = 'KRISTEN';
                elseif (str_contains($agamaText, 'KATHOLIK') || str_contains($agamaText, 'KATOLIK')) $this->agama = 'KATOLIK';
                elseif (str_contains($agamaText, 'HINDU')) $this->agama = 'HINDU';
                elseif (str_contains($agamaText, 'BUDHA') || str_contains($agamaText, 'BUDDHA')) $this->agama = 'BUDHA';
            }

            // Status Perkawinan
            if (str_contains(strtolower($line), 'status perkawinan') || str_contains(strtolower($line), 'perkawinan')) {
                $statusText = strtoupper(trim(preg_replace('/status\s*perkawinan|\:|:/i', '', strtolower($line))));
                if (empty($statusText) && isset($lines[$i+1])) {
                    $statusText = strtoupper(trim($lines[$i+1]));
                }
                
                if (str_contains($statusText, 'BELUM')) $this->stts_nikah = 'BELUM MENIKAH';
                elseif (str_contains($statusText, 'KAWIN')) $this->stts_nikah = 'MENIKAH';
                elseif (str_contains($statusText, 'CERAI HIDUP') || str_contains($statusText, 'JANDA') || str_contains($statusText, 'DUDHA')) $this->stts_nikah = 'JANDA';
            }
            
            // Tempat Tgl Lahir
            if (str_contains(strtolower($line), 'tempat/tgl') || str_contains(strtolower($line), 'lahir')) {
                $ttl = trim(preg_replace('/tempat\/tgl\s*lahir|\:|:/i', '', strtolower($line)));
                if (empty($ttl) && isset($lines[$i+1])) {
                    $ttl = trim($lines[$i+1]);
                }

                if (str_contains($ttl, ',')) {
                    $parts = explode(',', $ttl);
                    if(strlen(trim($parts[0])) > 2) {
                        $this->tmp_lahir = substr(strtoupper(trim($parts[0])), 0, 15);
                    }
                    $dateStr = trim($parts[1] ?? ''); 
                    if (preg_match('/(\d{2})[- \/]+(\d{2})[- \/]+(\d{4})/', $dateStr, $dMatch)) {
                        $this->tgl_lahir = $dMatch[3] . '-' . $dMatch[2] . '-' . $dMatch[1];
                    }
                }
            }

            // Jenis Kelamin
            if (str_contains(strtolower($line), 'jenis kelamin') || str_contains(strtolower($line), 'kelamin')) {
                $jkText = strtolower($line);
                if (str_contains($jkText, 'laki')) $this->jk = 'L';
                elseif (str_contains($jkText, 'perempuan')) $this->jk = 'P';
            }
        }
        
        if (!$nik_found) {
            if (preg_match('/(?:^|\D)(\d{16})(?:\D|$)/', $text, $matches)) {
                $this->no_ktp = $matches[1];
            }
        }
    }

    public function save()
    {
        $this->validate([
            'no_rkm_medis' => 'required|unique:pasien,no_rkm_medis',
            'nm_pasien' => 'required|string|max:40',
            'no_ktp' => 'nullable|string|max:20',
            'jk' => 'required|in:L,P',
            'tmp_lahir' => 'required|string|max:15',
            'tgl_lahir' => 'required|date',
            'nm_ibu' => 'required|string|max:40',
            'alamat' => 'required|string|max:200',
            'gol_darah' => 'required|string',
            'stts_nikah' => 'required|string',
            'agama' => 'required|string',
            'no_tlp' => 'nullable|string|max:40',
        ]);

        DB::beginTransaction();
        try {
            Pasien::create([
                'no_rkm_medis' => $this->no_rkm_medis,
                'nm_pasien' => $this->nm_pasien,
                'no_ktp' => $this->no_ktp ?? '-',
                'jk' => $this->jk,
                'tmp_lahir' => $this->tmp_lahir,
                'tgl_lahir' => $this->tgl_lahir,
                'nm_ibu' => $this->nm_ibu,
                'alamat' => $this->alamat,
                'gol_darah' => $this->gol_darah,
                'pekerjaan' => '-',
                'stts_nikah' => $this->stts_nikah,
                'agama' => $this->agama,
                'tgl_daftar' => date('Y-m-d'),
                'no_tlp' => $this->no_tlp ?? '-',
                'umur' => '-',
                'pnd' => '-',
                'keluarga' => 'DIRI SENDIRI',
                'namakeluarga' => '-',
                'kd_pj' => '-', 
                'no_peserta' => '-',
                'kd_kel' => 1, 
                'kd_kec' => 1,
                'kd_kab' => 1,
                'pekerjaanpj' => '-',
                'alamatpj' => $this->alamat,
                'kelurahanpj' => '1',
                'kecamatanpj' => '1',
                'kabupatenpj' => '1',
                'perusahaan_pasien' => '-',
                'suku_bangsa' => 1,
                'bahasa_pasien' => 1,
                'cacat_fisik' => 1,
                'email' => '-',
                'nip' => '-',
                'kd_prop' => 1,
                'propinsipj' => '1',
            ]);

            DB::commit();

            $this->dispatch('swal', [
                'title' => 'Berhasil!',
                'text'  => 'Data pasien baru berhasil disimpan.',
                'icon'  => 'success',
            ]);

            return $this->redirect(route('modul.pasien.index'), navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', [
                'title' => 'Gagal!',
                'text'  => 'Terjadi kesalahan: ' . substr($e->getMessage(), 0, 150),
                'icon'  => 'error',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.modul.registrasi-pasien.new');
    }
}
