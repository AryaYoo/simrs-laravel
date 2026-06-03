<?php

namespace App\Livewire\Modul\RawatJalan\SubRawatJalan\CatatanKeperawatan;

use Livewire\Component;
use App\Models\RegPeriksa;
use App\Livewire\Concerns\WithOptimisticLocking;

class Index extends Component
{
    use WithOptimisticLocking;

    public $noRawat;
    public $regPeriksa;
    public $catatans;

    // Form State
    public $tanggal, $jam, $uraian, $nip, $nmPetugas;
    public $petugasSearch = '';
    public $isModalOpen = false;
    public $isEditMode = false;
    public $autoTime = true;
    public $old_tanggal, $old_jam;

    // Legacy form mapped fields (hidden in UI)
    public $no_rkm_medis, $nm_pasien, $tgl_lahir;

    public function mount($no_rawat, \App\Repositories\RawatJalan\CatatanKeperawatanRepository $repository)
    {
        $this->noRawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'poliklinik'])
            ->where('no_rawat', $this->noRawat)
            ->firstOrFail();
        
        $this->initializeLock($this->regPeriksa);
        $this->catatans = $repository->getByNoRawat($this->noRawat)->toArray();
        
        $this->no_rkm_medis = $this->regPeriksa->no_rkm_medis;
        $this->nm_pasien = $this->regPeriksa->pasien->nm_pasien ?? '';
        $this->tgl_lahir = $this->regPeriksa->pasien->tgl_lahir ?? '';

        $this->tanggal = now()->format('Y-m-d');
        $this->jam = now()->format('H:i:s');
    }

    public function openModal()
    {
        $this->reset(['uraian', 'nip', 'nmPetugas', 'petugasSearch', 'old_tanggal', 'old_jam']);
        $this->isEditMode = false;
        $this->autoTime = true;
        $this->tanggal = now()->format('Y-m-d');
        $this->jam = now()->format('H:i:s');

        // Auto-fill petugas from logged-in user
        $loggedInUsername = auth()->user()->username ?? null;
        if ($loggedInUsername) {
            $pegawai = \App\Models\Pegawai::find($loggedInUsername);
            if ($pegawai) {
                $this->nip = $pegawai->nik;
                $this->nmPetugas = $pegawai->nama;
            }
        }

        $this->isModalOpen = true;
        $this->dispatch('set-autotime', ['status' => true]);
    }

    public function edit(string $tanggal, string $jam)
    {
        $catatan = collect($this->catatans)->first(function ($item) use ($tanggal, $jam) {
            return $item['tanggal'] === $tanggal && $item['jam'] === $jam;
        });

        if ($catatan) {
            $this->old_tanggal = $catatan['tanggal'];
            $this->old_jam = $catatan['jam'];
            $this->tanggal = $catatan['tanggal'];
            $this->jam = $catatan['jam'];
            $this->uraian = $catatan['uraian'];
            $this->nip = $catatan['nip'];
            $this->nmPetugas = $catatan['petugas']['nama'] ?? '';
            
            $this->isEditMode = true;
            $this->autoTime = false;
            $this->isModalOpen = true;
            $this->dispatch('set-autotime', ['status' => false]);
        }
    }

    public function selectPetugas($nip, $nama)
    {
        $this->nip = $nip;
        $this->nmPetugas = $nama;
        $this->petugasSearch = '';
    }

    public function save(\App\Repositories\RawatJalan\CatatanKeperawatanRepository $repository)
    {
        $this->validate([
            'tanggal' => 'required|date',
            'jam' => 'required',
            'uraian' => 'required|string|max:1000',
            'nip' => 'required',
        ], [
            'nip.required' => 'Petugas harus dipilih.',
        ]);

        $this->validateLock($this->regPeriksa->fresh());

        try {
            $data = [
                'tanggal' => $this->tanggal,
                'jam' => $this->jam,
                'no_rawat' => $this->noRawat,
                'uraian' => $this->uraian,
                'nip' => $this->nip,
            ];

            if ($this->isEditMode) {
                $repository->update($this->noRawat, $this->old_tanggal, $this->old_jam, $data);
                $msg = 'Catatan keperawatan berhasil diperbarui.';
            } else {
                $repository->store($data);
                $msg = 'Catatan keperawatan berhasil disimpan.';
            }

            $this->isModalOpen = false;
            $this->catatans = $repository->getByNoRawat($this->noRawat)->toArray();
            $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => $msg, 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal!', 'text' => 'Terjadi kesalahan: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function delete(string $tanggal, string $jam, \App\Repositories\RawatJalan\CatatanKeperawatanRepository $repository)
    {
        try {
            $repository->delete($this->noRawat, $tanggal, $jam);
            
            $this->catatans = $repository->getByNoRawat($this->noRawat)->toArray();
            $this->dispatch('swal', ['title' => 'Terhapus!', 'text' => 'Catatan keperawatan berhasil dihapus.', 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal!', 'text' => 'Terjadi kesalahan: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function render(\App\Repositories\RawatJalan\CatatanKeperawatanRepository $repository)
    {
        $petugasList = [];
        if (strlen($this->petugasSearch) >= 3) {
            $petugasList = \App\Models\Petugas::where('status', '1')
                ->where(function($q) {
                    $q->where('nama', 'like', '%' . $this->petugasSearch . '%')
                      ->orWhere('nip', 'like', '%' . $this->petugasSearch . '%');
                })
                ->limit(10)
                ->get();
        }

        return view('livewire.modul.rawat-jalan.sub-rawat-jalan.catatan-keperawatan.index', [
            'petugasList' => $petugasList
        ])
            ->layout('layouts.app', ['title' => 'Catatan Keperawatan Rawat Jalan']);
    }
}
