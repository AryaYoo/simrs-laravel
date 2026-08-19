<?php

namespace App\Livewire\Modul\Farmasi;

use App\Models\ResepObat;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Penyerahan Resep Obat Rawat Jalan'])]
class PenyerahanResepJalan extends Component
{
    public ?string $no_resep = null;

    // Data Pasien
    public string $no_rawat     = '';
    public string $no_rkm_medis = '';
    public string $nm_pasien    = '';
    public string $jk           = '';
    public string $tgl_lahir    = '';
    public string $umur         = '';
    public string $alamat       = '';
    public string $no_hp        = '';

    // Data Obat (sudah divalidasi di detail_pemberian_obat)
    public array $listObat = [];

    // Evidence
    public string $capturedImage = ''; // base64 data URL dari JS
    public bool $hasCaptured = false;

    // ─────────────────────────────────────────────
    // LIFECYCLE
    // ─────────────────────────────────────────────

    public function mount(?string $no_resep = null): void
    {
        $this->no_resep = $no_resep;

        if (!$this->no_resep) {
            return;
        }

        // Load header resep + data pasien
        $resep = ResepObat::with(['regPeriksa.pasien'])->find($this->no_resep);

        if ($resep && $resep->regPeriksa && $resep->regPeriksa->pasien) {
            $reg    = $resep->regPeriksa;
            $pasien = $reg->pasien;

            $this->no_rawat     = $resep->no_rawat ?? '';
            $this->no_rkm_medis = $reg->no_rkm_medis ?? '';
            $this->nm_pasien    = $pasien->nm_pasien ?? '';
            $this->jk           = $pasien->jk ?? '';
            $this->tgl_lahir    = $pasien->tgl_lahir ?? '';
            $this->alamat       = $pasien->alamat ?? '';
            $this->no_hp        = $pasien->no_tlp ?? '';

            // Hitung umur dari tgl_lahir
            if (!empty($pasien->tgl_lahir)) {
                try {
                    $birth = \Carbon\Carbon::parse($pasien->tgl_lahir);
                    $this->umur = $birth->diff(now())->y . ' Tahun';
                } catch (\Throwable $e) {
                    $this->umur = $reg->umurdaftar ?? '';
                }
            } else {
                $this->umur = $reg->umurdaftar ?? '';
            }
        }

        // Load daftar obat yang sudah divalidasi (ada di detail_pemberian_obat)
        if (!empty($this->no_rawat)) {
            $rows = DB::table('detail_pemberian_obat')
                ->join('databarang', 'detail_pemberian_obat.kode_brng', '=', 'databarang.kode_brng')
                ->where('detail_pemberian_obat.no_rawat', $this->no_rawat)
                ->select(
                    'detail_pemberian_obat.kode_brng',
                    'databarang.nama_brng',
                    'detail_pemberian_obat.jml',
                )
                ->get();

            $noResep = $this->no_resep;
            $this->listObat = $rows->map(function ($row) use ($noResep) {
                $resepDokter = DB::table('resep_dokter')
                    ->where('no_resep', $noResep)
                    ->where('kode_brng', $row->kode_brng)
                    ->first();

                return [
                    'kode_brng'    => $row->kode_brng,
                    'nama_brng'    => $row->nama_brng,
                    'jml'          => $row->jml,
                    'aturan_pakai' => $resepDokter?->aturan_pakai ?? '-',
                ];
            })->toArray();
        }
    }

    public function render()
    {
        return view('livewire.modul.farmasi.penyerahan-resep-jalan');
    }

    // ─────────────────────────────────────────────
    // ACTIONS
    // ─────────────────────────────────────────────

    /**
     * Dipanggil dari Alpine.js setelah capture canvas → base64
     */
    public function setCapturedImage(string $dataUrl): void
    {
        $this->capturedImage = $dataUrl;
        $this->hasCaptured   = true;
    }

    /**
     * Simpan evidence foto ke file & DB
     */
    public function simpan(): void
    {
        if (empty($this->capturedImage)) {
            $this->dispatch('swal', [
                'title' => 'Peringatan',
                'text'  => 'Silakan ambil foto evidence terlebih dahulu.',
                'icon'  => 'warning',
            ]);
            return;
        }

        if (empty($this->no_resep)) {
            $this->dispatch('swal', [
                'title' => 'Peringatan',
                'text'  => 'No. Resep tidak valid.',
                'icon'  => 'warning',
            ]);
            return;
        }

        try {
            // Decode base64 → binary
            $dataUrl   = $this->capturedImage;
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $dataUrl);
            $imageData = base64_decode($imageData);

            if ($imageData === false) {
                throw new \RuntimeException('Gagal decode gambar.');
            }

            $filename  = $this->no_resep . '.jpeg';
            $dbPath    = 'pages/upload/' . $filename;

            // Direktori simpan: dari env KHANZA_PENYERAHAN_PATH
            // Produksi: /var/www/html/webapps/penyerahanresep/pages/upload
            // Dev fallback: public/pages/upload
            $uploadDir = env('KHANZA_PENYERAHAN_PATH', public_path('pages/upload'));
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fullPath = rtrim($uploadDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

            // Kompresi server-side via PHP GD (quality 75)
            // Client-side sudah dikompresi di canvas.toDataURL('image/jpeg', 0.75)
            if (function_exists('imagecreatefromstring')) {
                $gdImage = imagecreatefromstring($imageData);
                if ($gdImage !== false) {
                    imagejpeg($gdImage, $fullPath, 75);
                    imagedestroy($gdImage);
                } else {
                    // Fallback: simpan binary langsung
                    file_put_contents($fullPath, $imageData);
                }
            } else {
                // GD tidak tersedia, simpan binary langsung
                file_put_contents($fullPath, $imageData);
            }

            // Insert / update tabel bukti_penyerahan_resep_obat
            DB::table('bukti_penyerahan_resep_obat')->updateOrInsert(
                ['no_resep' => $this->no_resep],
                ['foto'     => $dbPath]
            );

            session()->flash('swal', [
                'title' => 'Berhasil!',
                'text'  => 'Evidence penyerahan resep berhasil disimpan.',
                'icon'  => 'success',
            ]);

            return $this->redirect(route('modul.farmasi.daftar-resep-dokter'), navigate: true);
        } catch (\Throwable $e) {
            $this->dispatch('swal', [
                'title' => 'Gagal Menyimpan',
                'text'  => 'Terjadi kesalahan: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }
}
