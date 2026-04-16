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
    
    public $auto_rm = true;
    public $no_rkm_medis;
    public $nm_pasien;
    public $no_ktp;
    public $jk = 'L';
    public $tmp_lahir;
    public $tgl_lahir;
    public $umur;
    public $pnd = '-';
    public $nm_ibu;
    public $no_tlp;
    public $email = '-';
    public $pekerjaan = '-';
    public $alamat;
    public $gol_darah = '-';
    public $stts_nikah = 'BELUM MENIKAH';
    public $agama = 'ISLAM';
    public $tgl_daftar;

    // Data Penanggung Jawab
    public $keluarga = 'DIRI SENDIRI';
    public $namakeluarga = '-';
    public $pekerjaanpj = '-';
    public $alamatpj;
    public $kelurahanpj = '1';
    public $kecamatanpj = '1';
    public $kabupatenpj = '1';
    public $propinsipj = '1';

    // Data Master IDs
    public $kd_pj = '-';
    public $no_peserta = '-';
    public $kd_kel = 1;
    public $kd_kec = 1;
    public $kd_kab = 1;
    public $kd_prop = 1;
    public $suku_bangsa = 1;
    public $bahasa_pasien = 1;
    public $cacat_fisik = 1;
    public $perusahaan_pasien = '-';
    public $nip = '-';

    public function mount()
    {
        $this->generateNoRkmMedis();
        $this->tgl_lahir = Carbon::now()->format('Y-m-d');
        $this->tgl_daftar = Carbon::now()->format('Y-m-d');
        $this->hitungUmur();
    }

    public function updatedTglLahir()
    {
        $this->hitungUmur();
    }

    public function hitungUmur()
    {
        if ($this->tgl_lahir) {
            $birthDate = Carbon::parse($this->tgl_lahir);
            $now = Carbon::now();
            $age = $birthDate->diff($now);
            $this->umur = "{$age->y} Th {$age->m} Bl {$age->d} Hr";
        }
    }

    public function copyAddressToPj()
    {
        $this->alamatpj = $this->alamat;
        
        // Ambil nama berdasarkan kode yang dipilih
        $kel = \App\Models\Kelurahan::where('kd_kel', $this->kd_kel)->first();
        $kec = \App\Models\Kecamatan::where('kd_kec', $this->kd_kec)->first();
        $kab = \App\Models\Kabupaten::where('kd_kab', $this->kd_kab)->first();
        $prop = \App\Models\Propinsi::where('kd_prop', $this->kd_prop)->first();

        $this->kelurahanpj = $kel ? $kel->nm_kel : $this->kd_kel;
        $this->kecamatanpj = $kec ? $kec->nm_kec : $this->kd_kec;
        $this->kabupatenpj = $kab ? $kab->nm_kab : $this->kd_kab;
        $this->propinsipj = $prop ? $prop->nm_prop : $this->kd_prop;
        
        $this->dispatch('swal', [
            'title' => 'Berhasil!',
            'text'  => 'Alamat penanggung jawab disamakan dengan alamat pasien.',
            'icon'  => 'success',
        ]);
    }

    public function updatedAutoRm($value)
    {
        if ($value) {
            $this->generateNoRkmMedis();
        }
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

    public function updatedNoRkmMedis($value)
    {
        if (empty($value)) return;

        $exists = Pasien::where('no_rkm_medis', $value)->exists();
        if ($exists) {
            $this->dispatch('swal', [
                'title' => 'Nomor RM Sudah Ada!',
                'text'  => "Nomor Rekam Medis [{$value}] sudah digunakan oleh pasien lain. Silakan gunakan nomor lain.",
                'icon'  => 'warning',
            ]);
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
                    $this->hitungUmur(); // Pastikan umur terhitung setelah tgl_lahir diisi OCR

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

            // Alamat
            if (str_contains(strtolower($line), 'alamat')) {
                $alamat = trim(preg_replace('/alamat|\:|:/i', '', $line));
                if (empty($alamat) && isset($lines[$i+1])) {
                    $alamat = trim($lines[$i+1]);
                }
                if(strlen($alamat) > 5) $this->alamat = strtoupper($alamat);
            }

            // Pekerjaan
            if (str_contains(strtolower($line), 'pekerjaan')) {
                $pekerjaan = trim(preg_replace('/pekerjaan|\:|:/i', '', $line));
                if (empty($pekerjaan) && isset($lines[$i+1])) {
                    $pekerjaan = trim($lines[$i+1]);
                }
                if(strlen($pekerjaan) > 2) $this->pekerjaan = strtoupper($pekerjaan);
            }

            // Golongan Darah
            if (str_contains(strtolower($line), 'gol. darah') || str_contains(strtolower($line), 'gol darah')) {
                $goldarahText = strtoupper($line);
                if (preg_match('/\b(A|B|AB|O)\b/', $goldarahText, $matches)) {
                    $this->gol_darah = $matches[1];
                }
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
            'email' => 'nullable|email|max:50',
            'pekerjaan' => 'nullable|string|max:40',
            'pnd' => 'required|string',
            'umur' => 'required|string',
            'keluarga' => 'required|string',
            'namakeluarga' => 'required|string|max:50',
            'pekerjaanpj' => 'required|string|max:40',
            'alamatpj' => 'required|string|max:200',
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
                'pekerjaan' => $this->pekerjaan ?: '-',
                'stts_nikah' => $this->stts_nikah,
                'agama' => $this->agama,
                'tgl_daftar' => $this->tgl_daftar ?: date('Y-m-d'),
                'no_tlp' => $this->no_tlp ?: '-',
                'umur' => $this->umur,
                'pnd' => $this->pnd,
                'keluarga' => $this->keluarga,
                'namakeluarga' => $this->namakeluarga,
                'kd_pj' => $this->kd_pj ?: '-', 
                'no_peserta' => $this->no_peserta ?: '-',
                'kd_kel' => $this->kd_kel, 
                'kd_kec' => $this->kd_kec,
                'kd_kab' => $this->kd_kab,
                'pekerjaanpj' => $this->pekerjaanpj,
                'alamatpj' => $this->alamatpj,
                'kelurahanpj' => $this->kelurahanpj,
                'kecamatanpj' => $this->kecamatanpj,
                'kabupatenpj' => $this->kabupatenpj,
                'perusahaan_pasien' => $this->perusahaan_pasien ?: '-',
                'suku_bangsa' => $this->suku_bangsa,
                'bahasa_pasien' => $this->bahasa_pasien,
                'cacat_fisik' => $this->cacat_fisik,
                'email' => $this->email ?: '-',
                'nip' => $this->nip ?: '-',
                'kd_prop' => $this->kd_prop,
                'propinsipj' => $this->propinsipj,
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
        return view('livewire.modul.registrasi-pasien.new', [
            'penjabs' => \App\Models\Penjab::orderBy('png_jawab')->get(),
            'sukuBangsas' => \App\Models\SukuBangsa::all(),
            'bahasaPasiens' => \App\Models\BahasaPasien::all(),
            'cacatFisiks' => \App\Models\CacatFisik::all(),
            'perusahaans' => \App\Models\PerusahaanPasien::all(),
            'propinsis' => \App\Models\Propinsi::all(),
            'kabupatens' => \App\Models\Kabupaten::all(),
            'kecamatans' => \App\Models\Kecamatan::all(),
            'kelurahans' => \App\Models\Kelurahan::all(),
            'pendidikans' => ['TS', 'TK', 'SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3', '-'],
            'keluargas' => ['DIRI SENDIRI', 'AYAH', 'IBU', 'ISTRI', 'SUAMI', 'SAUDARA', 'ANAK', 'DLL'],
        ]);
    }
}
