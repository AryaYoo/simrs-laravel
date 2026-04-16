<?php

namespace App\Livewire\Modul\Pasien;

use App\Models\Pasien;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\AppSetting;

#[Layout('layouts.app', ['title' => 'Edit Data Pasien'])]
class Edit extends Component
{
    use WithFileUploads, \App\Livewire\Concerns\WithOptimisticLocking;

    public $ktp_image;
    
    public $no_rkm_medis;
    public $nm_pasien;
    public $no_ktp;
    public $jk;
    public $tmp_lahir;
    public $tgl_lahir;
    public $umur;
    public $pnd;
    public $nm_ibu;
    public $no_tlp;
    public $email;
    public $pekerjaan;
    public $alamat;
    public $gol_darah;
    public $stts_nikah;
    public $agama;
    public $tgl_daftar;

    // Data Penanggung Jawab
    public $keluarga;
    public $namakeluarga;
    public $pekerjaanpj;
    public $alamatpj;
    public $kelurahanpj;
    public $kecamatanpj;
    public $kabupatenpj;
    public $propinsipj;

    // Data Master IDs
    public $kd_pj;
    public $no_peserta;
    public $kd_kel;
    public $kd_kec;
    public $kd_kab;
    public $kd_prop;
    public $suku_bangsa;
    public $bahasa_pasien;
    public $cacat_fisik;
    public $perusahaan_pasien;
    public $nip;

    public function mount($no_rkm_medis)
    {
        $pasien = Pasien::where('no_rkm_medis', $no_rkm_medis)->firstOrFail();
        
        // Capture initial state for concurrency check
        $this->initializeLock($pasien);

        $this->no_rkm_medis = $pasien->no_rkm_medis;
        $this->nm_pasien = $pasien->nm_pasien;
        $this->no_ktp = $pasien->no_ktp;
        $this->jk = $pasien->jk;
        $this->tmp_lahir = $pasien->tmp_lahir;
        $this->tgl_lahir = $pasien->tgl_lahir;
        $this-> umur = $pasien->umur;
        $this->pnd = $pasien->pnd;
        $this->nm_ibu = $pasien->nm_ibu;
        $this->no_tlp = $pasien->no_tlp;
        $this->email = $pasien->email;
        $this->pekerjaan = $pasien->pekerjaan;
        $this->alamat = $pasien->alamat;
        $this->gol_darah = $pasien->gol_darah;
        $this->stts_nikah = $pasien->stts_nikah;
        $this->agama = $pasien->agama;
        $this->tgl_daftar = $pasien->tgl_daftar;

        $this->keluarga = $pasien->keluarga;
        $this->namakeluarga = $pasien->namakeluarga;
        $this->pekerjaanpj = $pasien->pekerjaanpj;
        $this->alamatpj = $pasien->alamatpj;
        $this->kelurahanpj = $pasien->kelurahanpj;
        $this->kecamatanpj = $pasien->kecamatanpj;
        $this->kabupatenpj = $pasien->kabupatenpj;
        $this->propinsipj = $pasien->propinsipj;

        $this->kd_pj = $pasien->kd_pj;
        $this->no_peserta = $pasien->no_peserta;
        $this->kd_kel = $pasien->kd_kel;
        $this->kd_kec = $pasien->kd_kec;
        $this->kd_kab = $pasien->kd_kab;
        $this->kd_prop = $pasien->kd_prop;
        $this->suku_bangsa = $pasien->suku_bangsa;
        $this->bahasa_pasien = $pasien->bahasa_pasien;
        $this->cacat_fisik = $pasien->cacat_fisik;
        $this->perusahaan_pasien = $pasien->perusahaan_pasien;
        $this->nip = $pasien->nip;
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

    public function updatedKtpImage()
    {
        $this->validate([
            'ktp_image' => 'image|max:2048',
        ]);

        $this->processKtpOcr();
    }

    public function processKtpFromBase64($base64Image)
    {
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
                'requests' => [['image' => ['content' => $base64Image], 'features' => [['type' => 'TEXT_DETECTION']] ]]
            ]);
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['responses'][0]['textAnnotations'][0]['description'])) {
                    $this->parseKtpText($data['responses'][0]['textAnnotations'][0]['description']);
                    $this->hitungUmur();
                    $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => 'Data KTP berhasil diekstrak.', 'icon' => 'success']);
                }
            }
        } catch (\Exception $e) {}
    }

    public function processKtpOcr()
    {
        $apiKeySetting = AppSetting::where('setting_key', 'GOOGLE_VISION_API_KEY')->first();
        if (!$apiKeySetting || empty($apiKeySetting->setting_value)) return;
        $apiKey = $apiKeySetting->setting_value;
        $imageData = base64_encode(file_get_contents($this->ktp_image->getRealPath()));
        try {
            $response = Http::post("https://vision.googleapis.com/v1/images:annotate?key={$apiKey}", [
                'requests' => [['image' => ['content' => $imageData], 'features' => [['type' => 'TEXT_DETECTION']] ]]
            ]);
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['responses'][0]['textAnnotations'][0]['description'])) {
                    $this->parseKtpText($data['responses'][0]['textAnnotations'][0]['description']);
                    $this->hitungUmur();
                    $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => 'Data KTP berhasil diekstrak.', 'icon' => 'success']);
                }
            }
        } catch (\Exception $e) {}
    }

    private function parseKtpText($text)
    {
        $lines = explode("\n", $text);
        foreach ($lines as $i => $line) {
            $line = trim($line);
            if (empty($line)) continue;
            // Nama
            if (str_contains(strtolower($line), 'nama')) {
                $nama = trim(preg_replace('/nama|\:|:/i', '', $line));
                if (empty($nama) && isset($lines[$i+1])) $nama = trim($lines[$i+1]);
                if(strlen($nama) > 2) $this->nm_pasien = substr(strtoupper($nama), 0, 40);
            }
            // Agama
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
                if (empty($statusText) && isset($lines[$i+1])) $statusText = strtoupper(trim($lines[$i+1]));
                if (str_contains($statusText, 'BELUM')) $this->stts_nikah = 'BELUM MENIKAH';
                elseif (str_contains($statusText, 'KAWIN')) $this->stts_nikah = 'MENIKAH';
                elseif (str_contains($statusText, 'CERAI HIDUP') || str_contains($statusText, 'JANDA') || str_contains($statusText, 'DUDHA')) $this->stts_nikah = 'JANDA';
            }
            // TTL
            if (str_contains(strtolower($line), 'tempat/tgl') || str_contains(strtolower($line), 'lahir')) {
                $ttl = trim(preg_replace('/tempat\/tgl\s*lahir|\:|:/i', '', strtolower($line)));
                if (empty($ttl) && isset($lines[$i+1])) $ttl = trim($lines[$i+1]);
                if (str_contains($ttl, ',')) {
                    $parts = explode(',', $ttl);
                    if(strlen(trim($parts[0])) > 2) $this->tmp_lahir = substr(strtoupper(trim($parts[0])), 0, 15);
                    $dateStr = trim($parts[1] ?? ''); 
                    if (preg_match('/(\d{2})[- \/]+(\d{2})[- \/]+(\d{4})/', $dateStr, $dMatch)) $this->tgl_lahir = $dMatch[3] . '-' . $dMatch[2] . '-' . $dMatch[1];
                }
            }
            // JK
            if (str_contains(strtolower($line), 'jenis kelamin') || str_contains(strtolower($line), 'kelamin')) {
                $jkText = strtolower($line);
                if (str_contains($jkText, 'laki')) $this->jk = 'L';
                elseif (str_contains($jkText, 'perempuan')) $this->jk = 'P';
            }
            // Alamat
            if (str_contains(strtolower($line), 'alamat')) {
                $alamat = trim(preg_replace('/alamat|\:|:/i', '', $line));
                if (empty($alamat) && isset($lines[$i+1])) $alamat = trim($lines[$i+1]);
                if(strlen($alamat) > 5) $this->alamat = strtoupper($alamat);
            }
            // Pekerjaan
            if (str_contains(strtolower($line), 'pekerjaan')) {
                $pekerjaan = trim(preg_replace('/pekerjaan|\:|:/i', '', $line));
                if (empty($pekerjaan) && isset($lines[$i+1])) $pekerjaan = trim($lines[$i+1]);
                if(strlen($pekerjaan) > 2) $this->pekerjaan = strtoupper($pekerjaan);
            }
            // Gol Darah
            if (str_contains(strtolower($line), 'gol. darah') || str_contains(strtolower($line), 'gol darah')) {
                if (preg_match('/\b(A|B|AB|O)\b/i', $line, $matches)) $this->gol_darah = strtoupper($matches[1]);
            }
        }
    }

    public function save()
    {
        $pasien = Pasien::where('no_rkm_medis', $this->no_rkm_medis)->firstOrFail();

        // Perform concurrency check before proceeding
        try {
            $this->validateLock($pasien);
        } catch (\Exception $e) {
            // Error already handled by swal dispatch in trait
            return;
        }

        $this->validate([
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
            $pasien = Pasien::where('no_rkm_medis', $this->no_rkm_medis)->firstOrFail();
            $pasien->update([
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
                'text'  => 'Data pasien berhasil diperbarui.',
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
        return view('livewire.modul.pasien.edit', [
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
