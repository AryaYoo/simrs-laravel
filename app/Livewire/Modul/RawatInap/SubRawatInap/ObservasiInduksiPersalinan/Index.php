<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\ObservasiInduksiPersalinan;

use Livewire\Component;
use App\Models\RegPeriksa;
use App\Livewire\Concerns\WithOptimisticLocking;
use App\Repositories\RawatInap\CatatanObservasiInduksiPersalinanRepository;

class Index extends Component
{
    use WithOptimisticLocking;

    public $noRawat;
    public $regPeriksa;
    public $observasis = [];

    // Form State
    public $tgl_perawatan, $jam_rawat;
    public $obat, $cairan, $dosis, $his, $djj, $keterangan;
    public $nip, $nmPetugas;
    public $petugasSearch = '';
    public $isModalOpen = false;
    public $isEditMode = false;
    public $autoTime = true;
    public $old_tgl_perawatan, $old_jam_rawat;

    public function mount($no_rawat)
    {
        $this->noRawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'poliklinik'])
            ->where('no_rawat', $this->noRawat)
            ->firstOrFail();

        $this->initializeLock($this->regPeriksa);
        $this->loadObservasis();

        $this->tgl_perawatan = now()->format('Y-m-d');
        $this->jam_rawat = now()->format('H:i:s');
    }

    public function loadObservasis()
    {
        $repo = new CatatanObservasiInduksiPersalinanRepository();
        $this->observasis = $repo->getByNoRawat($this->noRawat);
    }

    public function openModal()
    {
        $this->reset([
            'obat', 'cairan', 'dosis', 'his', 'djj', 'keterangan',
            'nip', 'nmPetugas', 'petugasSearch', 'old_tgl_perawatan', 'old_jam_rawat'
        ]);
        $this->isEditMode = false;
        $this->autoTime = true;
        $this->tgl_perawatan = now()->format('Y-m-d');
        $this->jam_rawat = now()->format('H:i:s');
        $this->isModalOpen = true;
        $this->dispatch('set-autotime', ['status' => true]);
    }

    public function edit(string $tglPerawatan, string $jamRawat)
    {
        $obs = collect($this->observasis)->first(function ($item) use ($tglPerawatan, $jamRawat) {
            return $item['tgl_perawatan'] === $tglPerawatan && $item['jam_rawat'] === $jamRawat;
        });

        if ($obs) {
            $this->old_tgl_perawatan = $obs['tgl_perawatan'];
            $this->old_jam_rawat = $obs['jam_rawat'];
            $this->tgl_perawatan = $obs['tgl_perawatan'];
            $this->jam_rawat = $obs['jam_rawat'];
            
            $this->obat = $obs['obat'];
            $this->cairan = $obs['cairan'];
            $this->dosis = $obs['dosis'];
            $this->his = $obs['his'];
            $this->djj = $obs['djj'];
            $this->keterangan = $obs['keterangan'];
            
            $this->nip = $obs['nip'];
            $this->nmPetugas = $obs['petugas']['nama'] ?? '';

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

    public function save(CatatanObservasiInduksiPersalinanRepository $repository)
    {
        try {
            $this->validate([
                'tgl_perawatan' => 'required|date',
                'jam_rawat'     => 'required',
                'obat'          => 'nullable|string|max:50',
                'cairan'        => 'required|string|max:50',
                'dosis'         => 'nullable|string|max:10',
                'his'           => 'nullable|string|max:50',
                'djj'           => 'nullable|string|max:5',
                'keterangan'    => 'nullable|string|max:50',
                'nip'           => 'required',
            ], [
                'tgl_perawatan.required' => 'Tanggal Observasi wajib diisi.',
                'jam_rawat.required'     => 'Jam Observasi wajib diisi.',
                'nip.required'           => 'Bidan wajib dipilih.',
                'cairan.required'        => 'Cairan wajib diisi.',
                'cairan.max'             => 'Cairan maksimal 50 karakter.',
                'obat.max'               => 'Obat maksimal 50 karakter.',
                'dosis.max'              => 'Dosis maksimal 10 karakter.',
                'his.max'                => 'HIS maksimal 50 karakter.',
                'djj.max'                => 'DJJ maksimal 5 karakter.',
                'keterangan.max'         => 'Keterangan maksimal 50 karakter.',
            ]);

            $this->validateLock($this->regPeriksa->fresh());

            $data = [
                'tgl_perawatan' => $this->tgl_perawatan,
                'jam_rawat'     => $this->jam_rawat,
                'no_rawat'      => $this->noRawat,
                'obat'          => $this->obat ?? null,
                'cairan'        => $this->cairan,
                'dosis'         => $this->dosis ?? null,
                'his'           => $this->his ?? null,
                'djj'           => $this->djj ?? null,
                'keterangan'    => $this->keterangan ?? null,
                'nip'           => $this->nip,
            ];

            if ($this->isEditMode) {
                $repository->update($this->noRawat, $this->old_tgl_perawatan, $this->old_jam_rawat, $data);
                $msg = 'Observasi Induksi Persalinan berhasil diperbarui.';
            } else {
                $repository->store($data);
                $msg = 'Observasi Induksi Persalinan berhasil disimpan.';
            }

            $this->isModalOpen = false;
            $this->loadObservasis();
            $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => $msg, 'icon' => 'success']);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('swal', ['title' => 'Validasi Gagal!', 'text' => 'Pastikan semua isian mandatory (*) sudah terisi dengan benar.', 'icon' => 'warning']);
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal Menyimpan!', 'text' => 'Terjadi kesalahan: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function delete(string $tglPerawatan, string $jamRawat, CatatanObservasiInduksiPersalinanRepository $repository)
    {
        try {
            $repository->delete($this->noRawat, $tglPerawatan, $jamRawat);
            $this->loadObservasis();
            $this->dispatch('swal', ['title' => 'Terhapus!', 'text' => 'Observasi Induksi Persalinan berhasil dihapus.', 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal Menghapus!', 'text' => 'Terjadi kesalahan sistem: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function render()
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

        return view('livewire.modul.rawat-inap.sub-rawat-inap.observasi-induksi-persalinan.index', [
            'petugasList' => $petugasList,
        ])->layout('layouts.app', ['title' => 'Observasi Induksi Persalinan']);
    }
}
