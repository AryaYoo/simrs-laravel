<?php

namespace App\Livewire\Modul\Pasien;

use App\Models\Pasien;
use App\Services\GeminiKtpService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
        $mimeType = 'image/jpeg';
        if (preg_match('/^data:(image\/[\w]+);base64,/', $base64Image, $mimeMatch)) {
            $mimeType = $mimeMatch[1];
        }
        $base64Image = preg_replace('/^data:image\/[\w]+;base64,/', '', $base64Image);

        $gemini = new GeminiKtpService();

        if (!$gemini->isConfigured()) {
            $this->dispatch('swal', [
                'title' => 'Gemini API Belum Diset',
                'text'  => 'Isi GEMINI_API_KEY di file .env untuk menggunakan fitur Scan KTP otomatis.',
                'icon'  => 'warning',
            ]);
            return;
        }

        $result = $gemini->extractFromBase64($base64Image, $mimeType);

        if (!$result['success']) {
            $this->dispatch('swal', [
                'title' => 'Gagal Membaca KTP',
                'text'  => $result['error'],
                'icon'  => 'warning',
            ]);
            return;
        }

        $this->applyKtpData($result['data']);

        $this->dispatch('swal', [
            'title' => 'Berhasil!',
            'text'  => 'Data KTP berhasil diekstrak oleh Gemini AI.',
            'icon'  => 'success',
        ]);
    }

    public function processKtpOcr()
    {
        $gemini = new GeminiKtpService();

        if (!$gemini->isConfigured()) {
            $this->dispatch('swal', [
                'title' => 'Gemini API Belum Diset',
                'text'  => 'Isi GEMINI_API_KEY di file .env untuk menggunakan fitur Scan KTP otomatis.',
                'icon'  => 'warning',
            ]);
            return;
        }

        $imageData = base64_encode(file_get_contents($this->ktp_image->getRealPath()));
        $mimeType  = $this->ktp_image->getMimeType() ?: 'image/jpeg';

        $result = $gemini->extractFromBase64($imageData, $mimeType);

        if (!$result['success']) {
            $this->dispatch('swal', [
                'title' => 'Gagal Membaca KTP',
                'text'  => $result['error'],
                'icon'  => 'error',
            ]);
            return;
        }

        $this->applyKtpData($result['data']);

        $this->dispatch('swal', [
            'title' => 'Berhasil!',
            'text'  => 'Data KTP berhasil diekstrak oleh Gemini AI.',
            'icon'  => 'success',
        ]);
    }

    /**
     * Terapkan data KTP hasil ekstraksi Gemini ke property Livewire.
     */
    private function applyKtpData(array $data): void
    {
        if (!empty($data['nik']))               $this->no_ktp    = $data['nik'];
        if (!empty($data['nama']))              $this->nm_pasien = $data['nama'];
        if (!empty($data['tempat_lahir']))      $this->tmp_lahir = $data['tempat_lahir'];
        if (!empty($data['tanggal_lahir']))     $this->tgl_lahir = $data['tanggal_lahir'];
        if (!empty($data['jenis_kelamin']))     $this->jk        = $data['jenis_kelamin'];
        if (!empty($data['alamat']))            $this->alamat    = $data['alamat'];
        if (!empty($data['agama']))             $this->agama     = $data['agama'];
        if (!empty($data['status_perkawinan'])) $this->stts_nikah = $data['status_perkawinan'];
        if (!empty($data['pekerjaan']))         $this->pekerjaan = $data['pekerjaan'];
        if (!empty($data['golongan_darah']))    $this->gol_darah = $data['golongan_darah'];

        if (!empty($data['tanggal_lahir'])) {
            $this->hitungUmur();
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
