<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\CatatanAdimeGizi;

use Livewire\Component;
use App\Models\RegPeriksa;
use App\Livewire\Concerns\WithOptimisticLocking;
use App\Repositories\RawatInap\CatatanAdimeGiziRepository;

class Index extends Component
{
    use WithOptimisticLocking;

    public $noRawat;
    public $regPeriksa;
    public $catatans = [];
    public bool $isEmbedded = false;

    // Form State
    public $tanggal, $tanggal_date, $tanggal_time;
    public $asesmen, $diagnosis, $intervensi;
    public $monitoring, $evaluasi, $instruksi;
    public $nip, $nmPetugas;
    public $petugasSearch = '';
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
    }

    public function loadCatatans()
    {
        $repo = new CatatanAdimeGiziRepository();
        $this->catatans = $repo->getByNoRawat($this->noRawat);
    }

    public function openPanel()
    {
        $this->reset([
            'asesmen', 'diagnosis', 'intervensi',
            'monitoring', 'evaluasi', 'instruksi',
            'nip', 'nmPetugas', 'petugasSearch', 'old_tanggal'
        ]);
        $this->isEditMode = false;
        $this->autoTime = true;
        $this->tanggal_date = now()->format('Y-m-d');
        $this->tanggal_time = now()->format('H:i');
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

            $this->asesmen    = $catatan['asesmen'];
            $this->diagnosis  = $catatan['diagnosis'];
            $this->intervensi = $catatan['intervensi'];
            $this->monitoring = $catatan['monitoring'];
            $this->evaluasi   = $catatan['evaluasi'];
            $this->instruksi  = $catatan['instruksi'];
            $this->nip        = $catatan['nip'];
            $this->nmPetugas  = $catatan['petugas']['nama'] ?? '';

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

    public function save(CatatanAdimeGiziRepository $repository)
    {
        try {
            $this->validate([
                'tanggal_date' => 'required|date',
                'tanggal_time' => 'required',
                'asesmen'      => 'nullable|string|max:1000',
                'diagnosis'    => 'nullable|string|max:1000',
                'intervensi'   => 'nullable|string|max:1000',
                'monitoring'   => 'nullable|string|max:1000',
                'evaluasi'     => 'nullable|string|max:1000',
                'instruksi'    => 'nullable|string|max:1000',
                'nip'          => 'required',
            ], [
                'tanggal_date.required' => 'Tanggal wajib diisi.',
                'tanggal_time.required' => 'Jam wajib diisi.',
                'nip.required'          => 'Petugas wajib dipilih.',
            ]);

            $this->validateLock($this->regPeriksa->fresh());

            $tanggalFull = $this->tanggal_date . ' ' . $this->tanggal_time . ':00';

            $data = [
                'no_rawat'   => $this->noRawat,
                'tanggal'    => $tanggalFull,
                'asesmen'    => $this->asesmen ?? null,
                'diagnosis'  => $this->diagnosis ?? null,
                'intervensi' => $this->intervensi ?? null,
                'monitoring' => $this->monitoring ?? null,
                'evaluasi'   => $this->evaluasi ?? null,
                'instruksi'  => $this->instruksi ?? null,
                'nip'        => $this->nip,
            ];

            if ($this->isEditMode) {
                $repository->update($this->noRawat, $this->old_tanggal, $data);
                $msg = 'Catatan ADIME Gizi berhasil diperbarui.';
            } else {
                $repository->store($data);
                $msg = 'Catatan ADIME Gizi berhasil disimpan.';
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

    public function delete(string $tanggal, CatatanAdimeGiziRepository $repository)
    {
        try {
            $repository->delete($this->noRawat, $tanggal);
            $this->loadCatatans();
            $this->dispatch('swal', ['title' => 'Terhapus!', 'text' => 'Catatan ADIME Gizi berhasil dihapus.', 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal Menghapus!', 'text' => 'Terjadi kesalahan: ' . $e->getMessage(), 'icon' => 'error']);
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

        return view('livewire.modul.rawat-inap.sub-rawat-inap.catatan-adime-gizi.index', [
            'petugasList' => $petugasList,
        ])->layout('layouts.app', ['title' => 'Catatan ADIME Gizi']);
    }
}
