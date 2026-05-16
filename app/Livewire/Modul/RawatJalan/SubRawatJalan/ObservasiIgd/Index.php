<?php

namespace App\Livewire\Modul\RawatJalan\SubRawatJalan\ObservasiIgd;

use Livewire\Component;
use App\Models\RegPeriksa;
use App\Livewire\Concerns\WithOptimisticLocking;
use App\Repositories\RawatJalan\CatatanObservasiIgdRepository;

class Index extends Component
{
    use WithOptimisticLocking;

    public $noRawat;
    public $regPeriksa;
    public $observasis = [];

    // Form State
    public $tgl_perawatan, $jam_rawat, $gcs, $td, $hr, $rr, $suhu, $spo2, $nip, $nmPetugas;
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
        $repo = new CatatanObservasiIgdRepository();
        $this->observasis = $repo->getByNoRawat($this->noRawat);
    }

    public function openModal()
    {
        $this->reset(['gcs', 'td', 'hr', 'rr', 'suhu', 'spo2', 'nip', 'nmPetugas', 'petugasSearch', 'old_tgl_perawatan', 'old_jam_rawat']);
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
            $this->gcs = $obs['gcs'];
            $this->td = $obs['td'];
            $this->hr = $obs['hr'];
            $this->rr = $obs['rr'];
            $this->suhu = $obs['suhu'];
            $this->spo2 = $obs['spo2'];
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

    public function save(CatatanObservasiIgdRepository $repository)
    {
        $this->validate([
            'tgl_perawatan' => 'required|date',
            'jam_rawat' => 'required',
            'gcs' => 'nullable|string|max:10',
            'td' => 'required|string|max:8',
            'hr' => 'nullable|string|max:5',
            'rr' => 'nullable|string|max:5',
            'suhu' => 'nullable|string|max:5',
            'spo2' => 'required|string|max:3',
            'nip' => 'required',
        ], [
            'nip.required' => 'Petugas harus dipilih.',
            'td.required' => 'Tekanan Darah wajib diisi.',
            'td.max' => 'Tekanan Darah maksimal 8 karakter (contoh: 120/80).',
            'spo2.required' => 'SpO2 wajib diisi.',
            'spo2.max' => 'SpO2 maksimal 3 karakter (contoh: 98).',
            'gcs.max' => 'GCS maksimal 10 karakter.',
            'hr.max' => 'HR maksimal 5 karakter.',
            'rr.max' => 'RR maksimal 5 karakter.',
            'suhu.max' => 'Suhu maksimal 5 karakter.',
        ]);

        $this->validateLock($this->regPeriksa->fresh());

        try {
            $data = [
                'tgl_perawatan' => $this->tgl_perawatan,
                'jam_rawat' => $this->jam_rawat,
                'no_rawat' => $this->noRawat,
                'gcs' => $this->gcs ?? '-',
                'td' => $this->td,
                'hr' => $this->hr ?? '-',
                'rr' => $this->rr ?? '-',
                'suhu' => $this->suhu ?? '-',
                'spo2' => $this->spo2,
                'nip' => $this->nip,
            ];

            if ($this->isEditMode) {
                $repository->update($this->noRawat, $this->old_tgl_perawatan, $this->old_jam_rawat, $data);
                $msg = 'Observasi IGD berhasil diperbarui.';
            } else {
                $repository->store($data);
                $msg = 'Observasi IGD berhasil disimpan.';
            }

            $this->isModalOpen = false;
            $this->loadObservasis();
            $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => $msg, 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal!', 'text' => 'Data gagal disimpan. Pastikan semua isian sudah benar dan tidak melebihi batas karakter.', 'icon' => 'error']);
        }
    }

    public function delete(string $tglPerawatan, string $jamRawat, CatatanObservasiIgdRepository $repository)
    {
        try {
            $repository->delete($this->noRawat, $tglPerawatan, $jamRawat);
            $this->loadObservasis();
            $this->dispatch('swal', ['title' => 'Terhapus!', 'text' => 'Observasi IGD berhasil dihapus.', 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal!', 'text' => 'Data gagal dihapus. Silakan coba lagi.', 'icon' => 'error']);
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

        return view('livewire.modul.rawat-jalan.sub-rawat-jalan.observasi-igd.index', [
            'petugasList' => $petugasList,
        ])->layout('layouts.app', ['title' => 'Observasi IGD']);
    }
}
