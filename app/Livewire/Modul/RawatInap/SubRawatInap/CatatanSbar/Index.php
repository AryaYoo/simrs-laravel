<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\CatatanSbar;

use Livewire\Component;
use App\Models\RegPeriksa;
use App\Livewire\Concerns\WithOptimisticLocking;
use App\Repositories\RawatInap\CatatanSbarRepository;

class Index extends Component
{
    use WithOptimisticLocking;

    public $noRawat;
    public $regPeriksa;
    public $catatans = [];
    public bool $isEmbedded = false;
    public ?array $detailSbar = null;

    // Form State
    public $tanggal, $tanggal_date, $tanggal_time;
    public $nip, $nmPetugas;
    public $kd_dokter, $nmDokter;
    public $situation, $background, $assessment, $recommendation, $advice;
    public $status_baca = 'Belum';
    public $status_konfirmasi = 'Belum';
    public $status_verifikasi = 'Belum';
    
    public $petugasSearch = '';
    public $dokterSearch = '';
    
    public $isPanelOpen = false;
    public $isEditMode = false;
    public $autoTime = true;
    public $old_tanggal;

    public function mount($no_rawat)
    {
        $this->noRawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'poliklinik'])
            ->where('no_rawat', $this->noRawat)
            ->firstOrFail();

        $this->initializeLock($this->regPeriksa);
        $this->loadCatatans();

        $this->tanggal_date = now()->format('Y-m-d');
        $this->tanggal_time = now()->format('H:i');
        
        // Auto-fill DPJP dari regPeriksa sebagai default
        $this->kd_dokter = $this->regPeriksa->kd_dokter;
        $this->nmDokter = $this->regPeriksa->dokter->nm_dokter ?? '';
    }

    public function loadCatatans()
    {
        $repo = new CatatanSbarRepository();
        $this->catatans = $repo->getByNoRawat($this->noRawat);
    }

    public function openPanel()
    {
        $this->reset([
            'nip', 'nmPetugas', 'petugasSearch',
            'situation', 'background', 'assessment', 'recommendation', 'advice',
            'status_baca', 'status_konfirmasi', 'old_tanggal'
        ]);
        // Kembalikan default DPJP
        $this->kd_dokter = $this->regPeriksa->kd_dokter;
        $this->nmDokter = $this->regPeriksa->dokter->nm_dokter ?? '';
        
        $this->isEditMode = false;
        $this->autoTime = true;
        $this->tanggal_date = now()->format('Y-m-d');
        $this->tanggal_time = now()->format('H:i');
        $this->status_baca = 'Belum';
        $this->status_konfirmasi = 'Belum';
        $this->status_verifikasi = 'Belum';
        $this->isPanelOpen = true;
        $this->dispatch('set-autotime', ['status' => true]);
    }

    public function edit(string $tanggal)
    {
        $catatan = collect($this->catatans)->first(function ($item) use ($tanggal) {
            return $item['tanggal'] === $tanggal;
        });

        if ($catatan) {
            $this->old_tanggal = $catatan['tanggal'];
            $dt = \Carbon\Carbon::parse($catatan['tanggal']);
            $this->tanggal_date = $dt->format('Y-m-d');
            $this->tanggal_time = $dt->format('H:i');

            $this->nip               = $catatan['nip'];
            $this->nmPetugas         = $catatan['petugas']['nama'] ?? '';
            $this->kd_dokter         = $catatan['kd_dokter'];
            $this->nmDokter          = $catatan['dokter']['nama'] ?? '';
            $this->situation         = $catatan['situation'];
            $this->background        = $catatan['background'];
            $this->assessment        = $catatan['assessment'];
            $this->recommendation    = $catatan['recommendation'];
            $this->advice            = $catatan['advice'];
            $this->status_baca       = $catatan['status_baca'];
            $this->status_konfirmasi = $catatan['status_konfirmasi'];
            $this->status_verifikasi = $catatan['status_verifikasi'];

            $this->isEditMode = true;
            $this->autoTime   = false;
            $this->isPanelOpen = true;
            $this->dispatch('set-autotime', ['status' => false]);
        }
    }

    public function selectPetugas($nip, $nama)
    {
        $this->nip = $nip;
        $this->nmPetugas = $nama;
        $this->petugasSearch = '';
    }

    public function selectDokter($kd_dokter, $nm_dokter)
    {
        $this->kd_dokter = $kd_dokter;
        $this->nmDokter = $nm_dokter;
        $this->dokterSearch = '';
    }

    public function save(CatatanSbarRepository $repository)
    {
        try {
            $this->validate([
                'tanggal_date'      => 'required|date',
                'tanggal_time'      => 'required',
                'nip'               => 'required',
                'kd_dokter'         => 'required',
                'situation'         => 'required|string',
                'background'        => 'required|string',
                'assessment'        => 'required|string',
                'recommendation'    => 'required|string',
                'advice'            => 'nullable|string',
                'status_baca'       => 'required|in:Belum,Sudah',
                'status_konfirmasi' => 'required|in:Belum,Sudah',
            ], [
                'tanggal_date.required'   => 'Tanggal wajib diisi.',
                'tanggal_time.required'   => 'Jam wajib diisi.',
                'nip.required'            => 'Petugas wajib dipilih.',
                'kd_dokter.required'      => 'DPJP wajib dipilih.',
                'situation.required'      => 'Situation (S) wajib diisi.',
                'background.required'     => 'Background (B) wajib diisi.',
                'assessment.required'     => 'Assessment (A) wajib diisi.',
                'recommendation.required' => 'Recommendation (R) wajib diisi.',
            ]);

            $this->validateLock($this->regPeriksa->fresh());

            $tanggalFull = $this->tanggal_date . ' ' . $this->tanggal_time . ':00';

            $data = [
                'no_rawat'          => $this->noRawat,
                'tanggal'           => $tanggalFull,
                'nip'               => $this->nip,
                'kd_dokter'         => $this->kd_dokter,
                'situation'         => $this->situation,
                'background'        => $this->background,
                'assessment'        => $this->assessment,
                'recommendation'    => $this->recommendation,
                'advice'            => $this->advice ?? '',
                'status_baca'       => $this->status_baca,
                'status_konfirmasi' => $this->status_konfirmasi,
                'status_verifikasi' => $this->status_verifikasi,
            ];

            if ($this->isEditMode) {
                $repository->update($this->noRawat, $this->old_tanggal, $data);
                $msg = 'Catatan SBAR berhasil diperbarui.';
            } else {
                $repository->store($data);
                $msg = 'Catatan SBAR berhasil disimpan.';
            }

            $this->isPanelOpen = false;
            $this->loadCatatans();
            $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => $msg, 'icon' => 'success']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('swal', ['title' => 'Validasi Gagal!', 'text' => 'Pastikan semua isian mandatory (*) sudah terisi dengan benar.', 'icon' => 'warning']);
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal Menyimpan!', 'text' => 'Terjadi kesalahan: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function delete(string $tanggal, CatatanSbarRepository $repository)
    {
        try {
            $repository->delete($this->noRawat, $tanggal);
            $this->loadCatatans();
            $this->dispatch('swal', ['title' => 'Terhapus!', 'text' => 'Catatan SBAR berhasil dihapus.', 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal Menghapus!', 'text' => 'Terjadi kesalahan: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function showDetail(string $tanggal)
    {
        $this->detailSbar = collect($this->catatans)->first(fn($item) => $item['tanggal'] === $tanggal);
    }

    public function verifikasi(CatatanSbarRepository $repository)
    {
        if (!$this->detailSbar) return;
        try {
            $repository->update($this->noRawat, $this->detailSbar['tanggal'], [
                'status_verifikasi' => 'Sudah',
            ]);
            $this->loadCatatans();
            $this->detailSbar = collect($this->catatans)->first(fn($item) => $item['tanggal'] === $this->detailSbar['tanggal']);
            $this->dispatch('swal', ['title' => 'Terverifikasi!', 'text' => 'SBAR berhasil diverifikasi oleh DPJP.', 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal!', 'text' => $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function render()
    {
        $petugasList = [];
        if (strlen($this->petugasSearch) >= 3) {
            $petugasList = \App\Models\Petugas::where('status', '1')
                ->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->petugasSearch . '%')
                      ->orWhere('nip', 'like', '%' . $this->petugasSearch . '%');
                })
                ->limit(10)
                ->get();
        }

        $dokterList = [];
        if (strlen($this->dokterSearch) >= 3) {
            $dokterList = \App\Models\Dokter::where('status', '1')
                ->where(function ($q) {
                    $q->where('nm_dokter', 'like', '%' . $this->dokterSearch . '%')
                      ->orWhere('kd_dokter', 'like', '%' . $this->dokterSearch . '%');
                })
                ->limit(10)
                ->get();
        }

        return view('livewire.modul.rawat-inap.sub-rawat-inap.catatan-sbar.index', [
            'petugasList' => $petugasList,
            'dokterList'  => $dokterList,
        ])->layout('layouts.app', ['title' => 'Catatan SBAR']);
    }
}
