<?php

namespace App\Livewire\Concerns;

use App\Models\Kelurahan;
use App\Models\Kecamatan;
use App\Models\Kabupaten;
use App\Models\Propinsi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait WithKtpAddressMatching
{
    /**
     * Terapkan dan cocokkan data wilayah (Kelurahan, Kecamatan, Kab/Kota, Provinsi) & Alamat RT/RW
     * dari hasil ekstraksi Gemini KTP.
     */
    protected function matchAndApplyWilayah(array $data): void
    {
        // 1. Lengkapi Alamat dengan RT/RW jika ada dan belum tercantum di string alamat
        if (!empty($data['alamat'])) {
            $alamat = trim($data['alamat']);
            $rt = trim($data['rt'] ?? '');
            $rw = trim($data['rw'] ?? '');

            if (($rt !== '' || $rw !== '') && !preg_match('/\bRT\b/i', $alamat)) {
                $rtrw = '';
                if ($rt !== '' && $rw !== '') {
                    $rtrw = "RT {$rt} RW {$rw}";
                } elseif ($rt !== '') {
                    $rtrw = "RT {$rt}";
                } elseif ($rw !== '') {
                    $rtrw = "RW {$rw}";
                }

                if ($rtrw !== '') {
                    $alamat = trim("{$alamat} {$rtrw}");
                }
            }
            $this->alamat = $alamat;
        }

        // 2. Provinsi
        if (!empty($data['provinsi'])) {
            $propId = $this->matchProvinsi($data['provinsi']);
            if ($propId !== null) {
                $this->kd_prop = $propId;
            }
        }

        // 3. Kabupaten / Kota
        if (!empty($data['kabupaten'])) {
            $kabId = $this->matchKabupaten($data['kabupaten']);
            if ($kabId !== null) {
                $this->kd_kab = $kabId;
            }
        }

        // 4. Kecamatan
        if (!empty($data['kecamatan'])) {
            $kecId = $this->matchKecamatan($data['kecamatan']);
            if ($kecId !== null) {
                $this->kd_kec = $kecId;
            }
        }

        // 5. Kelurahan
        if (!empty($data['kelurahan'])) {
            $kelId = $this->matchKelurahan($data['kelurahan']);
            if ($kelId !== null) {
                $this->kd_kel = $kelId;
            }
        }

        // 6. Sinkronkan ke Data Penanggung Jawab jika hubungan adalah 'DIRI SENDIRI'
        if (isset($this->keluarga) && $this->keluarga === 'DIRI SENDIRI') {
            $this->syncPjFromPatientAddress();
        }
    }

    /**
     * Sinkronkan alamat & nama penanggung jawab jika keluarga adalah DIRI SENDIRI
     */
    protected function syncPjFromPatientAddress(): void
    {
        $this->alamatpj = $this->alamat;

        $kel = Kelurahan::where('kd_kel', $this->kd_kel)->first();
        $kec = Kecamatan::where('kd_kec', $this->kd_kec)->first();
        $kab = Kabupaten::where('kd_kab', $this->kd_kab)->first();
        $prop = Propinsi::where('kd_prop', $this->kd_prop)->first();

        $this->kelurahanpj = $kel ? trim($kel->nm_kel) : ($this->kelurahanpj ?: '-');
        $this->kecamatanpj = $kec ? trim($kec->nm_kec) : ($this->kecamatanpj ?: '-');
        $this->kabupatenpj = $kab ? trim($kab->nm_kab) : ($this->kabupatenpj ?: '-');
        $this->propinsipj  = $prop ? trim($prop->nm_prop) : ($this->propinsipj ?: '-');

        if (!empty($this->nm_pasien)) {
            $this->namakeluarga = $this->nm_pasien;
        }
        if (!empty($this->pekerjaan)) {
            $this->pekerjaanpj = $this->pekerjaan;
        }
    }

    /**
     * Cari atau cocokkan Provinsi dari teks KTP.
     */
    protected function matchProvinsi(?string $raw): ?int
    {
        if (empty($raw)) return null;
        $rawTrim = trim($raw);
        $clean   = trim(preg_replace('/^(PROVINSI|PROPINSI|PROP\.?|PROV\.?)\s+/i', '', $rawTrim));
        if (empty($clean)) return null;

        // 1. Exact match (case insensitive + trimmed)
        $found = Propinsi::whereRaw('TRIM(LOWER(nm_prop)) = ?', [strtolower($clean)])
            ->orWhereRaw('TRIM(LOWER(nm_prop)) = ?', [strtolower($rawTrim)])
            ->first();
        if ($found) return (int) $found->kd_prop;

        // 2. Like match
        $found = Propinsi::whereRaw('TRIM(LOWER(nm_prop)) LIKE ?', ['%' . strtolower($clean) . '%'])->first();
        if ($found) return (int) $found->kd_prop;

        // 3. Fallback: buat baru jika belum terdaftar
        try {
            DB::table('propinsi')->insert(['nm_prop' => strtoupper($clean)]);
            $new = Propinsi::whereRaw('TRIM(LOWER(nm_prop)) = ?', [strtolower($clean)])->first();
            return $new ? (int) $new->kd_prop : null;
        } catch (\Throwable $e) {
            Log::warning('Gagal auto-insert Propinsi: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cari atau cocokkan Kabupaten/Kota dari teks KTP.
     */
    protected function matchKabupaten(?string $raw): ?int
    {
        if (empty($raw)) return null;
        $rawTrim = trim($raw);
        $clean   = trim(preg_replace('/^(KABUPATEN|KAB\.?|KOTA)\s+/i', '', $rawTrim));
        if (empty($clean)) return null;

        // 1. Exact match
        $found = Kabupaten::whereRaw('TRIM(LOWER(nm_kab)) = ?', [strtolower($clean)])
            ->orWhereRaw('TRIM(LOWER(nm_kab)) = ?', [strtolower($rawTrim)])
            ->first();
        if ($found) return (int) $found->kd_kab;

        // 2. Like match
        $found = Kabupaten::whereRaw('TRIM(LOWER(nm_kab)) LIKE ?', ['%' . strtolower($clean) . '%'])->first();
        if ($found) return (int) $found->kd_kab;

        // 3. Fallback: buat baru jika belum terdaftar
        try {
            DB::table('kabupaten')->insert(['nm_kab' => strtoupper($clean)]);
            $new = Kabupaten::whereRaw('TRIM(LOWER(nm_kab)) = ?', [strtolower($clean)])->first();
            return $new ? (int) $new->kd_kab : null;
        } catch (\Throwable $e) {
            Log::warning('Gagal auto-insert Kabupaten: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cari atau cocokkan Kecamatan dari teks KTP.
     */
    protected function matchKecamatan(?string $raw): ?int
    {
        if (empty($raw)) return null;
        $rawTrim = trim($raw);
        $clean   = trim(preg_replace('/^(KECAMATAN|KEC\.?)\s+/i', '', $rawTrim));
        if (empty($clean)) return null;

        // 1. Exact match
        $found = Kecamatan::whereRaw('TRIM(LOWER(nm_kec)) = ?', [strtolower($clean)])
            ->orWhereRaw('TRIM(LOWER(nm_kec)) = ?', [strtolower($rawTrim)])
            ->first();
        if ($found) return (int) $found->kd_kec;

        // 2. Like match
        $found = Kecamatan::whereRaw('TRIM(LOWER(nm_kec)) LIKE ?', ['%' . strtolower($clean) . '%'])->first();
        if ($found) return (int) $found->kd_kec;

        // 3. Fallback: buat baru jika belum terdaftar
        try {
            DB::table('kecamatan')->insert(['nm_kec' => strtoupper($clean)]);
            $new = Kecamatan::whereRaw('TRIM(LOWER(nm_kec)) = ?', [strtolower($clean)])->first();
            return $new ? (int) $new->kd_kec : null;
        } catch (\Throwable $e) {
            Log::warning('Gagal auto-insert Kecamatan: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cari atau cocokkan Kelurahan dari teks KTP.
     */
    protected function matchKelurahan(?string $raw): ?int
    {
        if (empty($raw)) return null;
        $rawTrim = trim($raw);
        $clean   = trim(preg_replace('/^(KELURAHAN|KEL\.?|DESA)\s+/i', '', $rawTrim));
        if (empty($clean)) return null;

        // 1. Exact match
        $found = Kelurahan::whereRaw('TRIM(LOWER(nm_kel)) = ?', [strtolower($clean)])
            ->orWhereRaw('TRIM(LOWER(nm_kel)) = ?', [strtolower($rawTrim)])
            ->first();
        if ($found) return (int) $found->kd_kel;

        // 2. Like match
        $found = Kelurahan::whereRaw('TRIM(LOWER(nm_kel)) LIKE ?', ['%' . strtolower($clean) . '%'])->first();
        if ($found) return (int) $found->kd_kel;

        // 3. Fallback: buat baru jika belum terdaftar
        try {
            DB::table('kelurahan')->insert(['nm_kel' => strtoupper($clean)]);
            $new = Kelurahan::whereRaw('TRIM(LOWER(nm_kel)) = ?', [strtolower($clean)])->first();
            return $new ? (int) $new->kd_kel : null;
        } catch (\Throwable $e) {
            Log::warning('Gagal auto-insert Kelurahan: ' . $e->getMessage());
            return null;
        }
    }
}
