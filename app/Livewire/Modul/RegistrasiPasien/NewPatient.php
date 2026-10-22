<?php

namespace App\Livewire\Modul\RegistrasiPasien;

use App\Models\Pasien;
use App\Services\GeminiKtpService;
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
    use WithFileUploads, \App\Livewire\Concerns\WithKtpAddressMatching;

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
    public $is_ktp_scanned = false;

    public function getKolomBelumTerisiProperty()
    {
        $list = [];
        if (empty($this->nm_ibu) || $this->nm_ibu === '-') {
            $list['nm_ibu'] = 'Nama Ibu Kandung';
        }
        if (empty($this->no_tlp) || $this->no_tlp === '-') {
            $list['no_tlp'] = 'No. Telepon / HP';
        }
        if (empty($this->namakeluarga) || $this->namakeluarga === '-') {
            $list['namakeluarga'] = 'Nama PJ';
        }
        if (empty($this->pekerjaanpj) || $this->pekerjaanpj === '-') {
            $list['pekerjaanpj'] = 'Pekerjaan PJ';
        }
        if (empty($this->alamatpj) || $this->alamatpj === '-') {
            $list['alamatpj'] = 'Alamat PJ';
        }
        if (empty($this->email)) {
            $list['email'] = 'Email';
        }
        return $list;
    }

    public function mount()
    {
        $this->generateNoRkmMedis();
        $this->tgl_lahir = Carbon::now()->format('Y-m-d');
        $this->tgl_daftar = Carbon::now()->format('Y-m-d');
        $this->hitungUmur();

        // Default bahasa pasien: INDONESIA
        $bahasaIndo = \App\Models\BahasaPasien::where('nama_bahasa', 'like', '%INDONESIA%')->first();
        if ($bahasaIndo) {
            $this->bahasa_pasien = $bahasaIndo->id;
        }
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
        try {
            $this->validate([
                'ktp_image' => 'image|max:10240', // Maksimal 10MB
            ], [
                'ktp_image.image' => 'File harus berupa gambar (JPG, PNG, WEBP).',
                'ktp_image.max'   => 'Ukuran foto KTP maksimal 10MB.',
            ]);

            $this->processKtpOcr();
        } catch (\Illuminate\Validation\ValidationException $ve) {
            $errorMsg = collect($ve->errors())->flatten()->first() ?? 'Validasi gambar gagal.';
            $this->dispatch('swal', [
                'title' => 'Foto KTP Tidak Valid',
                'text'  => $errorMsg,
                'icon'  => 'warning',
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('updatedKtpImage Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->dispatch('swal', [
                'title' => 'Gagal Memproses KTP',
                'text'  => 'Terjadi kesalahan: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }

    /**
     * Proses KTP dari base64 (mode webcam capture).
     * Menggunakan Gemini Vision API untuk auto-fill data form.
     */
    public function processKtpFromBase64($base64Image)
    {
        try {
            // Deteksi MIME type dari data URI header
            $mimeType = 'image/jpeg';
            if (preg_match('/^data:(image\/[\w]+);base64,/', $base64Image, $mimeMatch)) {
                $mimeType = $mimeMatch[1];
            }
            $base64Image = preg_replace('/^data:image\/[\w]+;base64,/', '', $base64Image);

            $gemini = new GeminiKtpService();

            if (!$gemini->isConfigured()) {
                $this->dispatch('swal', [
                    'title' => 'Gemini API Belum Diset',
                    'text'  => 'Isi GEMINI_API_KEY di file .env server untuk menggunakan fitur Scan KTP otomatis.',
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
            $this->dispatch('ktp-autofilled');

            $this->dispatch('swal', [
                'title' => 'Berhasil!',
                'text'  => 'Data KTP berhasil diekstrak oleh Gemini AI dan diisi ke dalam form.',
                'icon'  => 'success',
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('processKtpFromBase64 Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->dispatch('swal', [
                'title' => 'Gagal Memproses KTP',
                'text'  => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }

    /**
     * Proses KTP dari file upload.
     * Menggunakan Gemini Vision API untuk auto-fill data form.
     */
    public function processKtpOcr()
    {
        try {
            $gemini = new GeminiKtpService();

            if (!$gemini->isConfigured()) {
                $this->dispatch('swal', [
                    'title' => 'Gemini API Belum Diset',
                    'text'  => 'Isi GEMINI_API_KEY di file .env server untuk menggunakan fitur Scan KTP otomatis.',
                    'icon'  => 'warning',
                ]);
                return;
            }

            if (!$this->ktp_image) {
                return;
            }

            // Baca file bytes secara aman
            $fileBytes = null;
            if (method_exists($this->ktp_image, 'get')) {
                $fileBytes = $this->ktp_image->get();
            } elseif (method_exists($this->ktp_image, 'getRealPath') && file_exists($this->ktp_image->getRealPath())) {
                $fileBytes = file_get_contents($this->ktp_image->getRealPath());
            }

            if (empty($fileBytes)) {
                $this->dispatch('swal', [
                    'title' => 'File Tidak Terbaca',
                    'text'  => 'File foto KTP gagal dimuat ke memori server. Coba pilih file lagi.',
                    'icon'  => 'error',
                ]);
                return;
            }

            $imageData = base64_encode($fileBytes);
            $mimeType  = method_exists($this->ktp_image, 'getMimeType') ? ($this->ktp_image->getMimeType() ?: 'image/jpeg') : 'image/jpeg';

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
            $this->dispatch('ktp-autofilled');

            $this->dispatch('swal', [
                'title' => 'Berhasil!',
                'text'  => 'Data KTP berhasil diekstrak oleh Gemini AI dan diisi ke dalam form.',
                'icon'  => 'success',
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('processKtpOcr Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->dispatch('swal', [
                'title' => 'Gagal Memproses KTP',
                'text'  => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }

    /**
     * Terapkan data KTP hasil ekstraksi Gemini ke property Livewire.
     * Hanya mengisi field yang berhasil diekstrak (tidak null).
     */
    private function applyKtpData(array $data): void
    {
        if (!empty($data['nik']))             $this->no_ktp    = $data['nik'];
        if (!empty($data['nama']))            $this->nm_pasien = $data['nama'];
        if (!empty($data['tempat_lahir']))    $this->tmp_lahir = $data['tempat_lahir'];
        if (!empty($data['tanggal_lahir']))   $this->tgl_lahir = $data['tanggal_lahir'];
        if (!empty($data['jenis_kelamin']))   $this->jk        = $data['jenis_kelamin'];
        if (!empty($data['agama']))           $this->agama     = $data['agama'];
        if (!empty($data['status_perkawinan'])) $this->stts_nikah = $data['status_perkawinan'];
        if (!empty($data['pekerjaan']))       $this->pekerjaan = $data['pekerjaan'];
        if (!empty($data['golongan_darah']))  $this->gol_darah = $data['golongan_darah'];

        // Bahasa yang digunakan: jika tidak ada informasi di KTP, otomatis isi Bahasa Indonesia
        $bahasaIndo = \App\Models\BahasaPasien::where('nama_bahasa', 'like', '%INDONESIA%')->first();
        $idIndo = $bahasaIndo ? $bahasaIndo->id : 5;
        if (!empty($data['bahasa'])) {
            $match = \App\Models\BahasaPasien::where('nama_bahasa', 'like', '%' . $data['bahasa'] . '%')->first();
            $this->bahasa_pasien = $match ? $match->id : $idIndo;
        } else {
            $this->bahasa_pasien = $idIndo;
        }

        // Terapkan alamat lengkap beserta wilayah (Kelurahan, Kecamatan, Kab/Kota, Provinsi)
        $this->matchAndApplyWilayah($data);

        // Hitung ulang umur setelah tgl_lahir diisi
        if (!empty($data['tanggal_lahir'])) {
            try {
                $this->hitungUmur();
            } catch (\Throwable $e) {
                // Ignore umur parsing error
            }
        }

        // Tandai bahwa data baru saja diisi dari KTP untuk menampilkan banner kolom yang belum lengkap
        $this->is_ktp_scanned = true;
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
