<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\ObservasiRanapKebidanan;

use Livewire\Component;
use App\Models\RegPeriksa;
use App\Livewire\Concerns\WithOptimisticLocking;
use App\Repositories\RawatInap\CatatanObservasiRanapKebidananRepository;

class Index extends Component
{
    use WithOptimisticLocking;

    public $noRawat;
    public $regPeriksa;
    public $observasis = [];

    // Form State
    public $tgl_perawatan, $jam_rawat, $gcs, $td, $hr, $rr, $suhu, $spo2;
    public $kontraksi, $bjj, $ppv, $vt;
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
        $repo = new CatatanObservasiRanapKebidananRepository();
        $this->observasis = $repo->getByNoRawat($this->noRawat);
    }

    public function openModal()
    {
        $this->reset([
            'gcs', 'td', 'hr', 'rr', 'suhu', 'spo2',
            'kontraksi', 'bjj', 'ppv', 'vt',
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
            
            $this->gcs = $obs['gcs'];
            $this->td = $obs['td'];
            $this->hr = $obs['hr'];
            $this->rr = $obs['rr'];
            $this->suhu = $obs['suhu'];
            $this->spo2 = $obs['spo2'];
            
            $this->kontraksi = $obs['kontraksi'];
            $this->bjj = $obs['bjj'];
            $this->ppv = $obs['ppv'];
            $this->vt = $obs['vt'];
            
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

    public function save(CatatanObservasiRanapKebidananRepository $repository)
    {
        try {
            $this->validate([
                'tgl_perawatan' => 'required|date',
                'jam_rawat' => 'required',
                'gcs' => 'nullable|string|max:10',
                'td' => 'required|string|max:8',
                'hr' => 'nullable|string|max:5',
                'rr' => 'nullable|string|max:5',
                'suhu' => 'nullable|string|max:5',
                'spo2' => 'required|string|max:3',
                'kontraksi' => 'required|string|max:15',
                'bjj' => 'required|string|max:5',
                'ppv' => 'required|string|max:15',
                'vt' => 'required|string|max:30',
                'nip' => 'required',
            ], [
                'tgl_perawatan.required' => 'Tanggal Observasi wajib diisi.',
                'jam_rawat.required' => 'Jam Observasi wajib diisi.',
                'nip.required' => 'Bidan wajib dipilih.',
                'td.required' => 'Tekanan Darah wajib diisi.',
                'td.max' => 'Tekanan Darah maksimal 8 karakter (contoh: 120/80).',
                'spo2.required' => 'SpO2 wajib diisi.',
                'spo2.max' => 'SpO2 maksimal 3 karakter (contoh: 98).',
                'gcs.max' => 'GCS maksimal 10 karakter.',
                'hr.max' => 'HR maksimal 5 karakter.',
                'rr.max' => 'RR maksimal 5 karakter.',
                'suhu.max' => 'Suhu maksimal 5 karakter.',
                'kontraksi.required' => 'Kontraksi wajib diisi.',
                'kontraksi.max' => 'Kontraksi maksimal 15 karakter.',
                'bjj.required' => 'BJJ wajib diisi.',
                'bjj.max' => 'BJJ maksimal 5 karakter.',
                'ppv.required' => 'PPV wajib diisi.',
                'ppv.max' => 'PPV maksimal 15 karakter.',
                'vt.required' => 'VT wajib diisi.',
                'vt.max' => 'VT maksimal 30 karakter.',
            ]);

            $this->validateLock($this->regPeriksa->fresh());

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
                'kontraksi' => $this->kontraksi,
                'bjj' => $this->bjj,
                'ppv' => $this->ppv,
                'vt' => $this->vt,
                'nip' => $this->nip,
            ];

            if ($this->isEditMode) {
                $repository->update($this->noRawat, $this->old_tgl_perawatan, $this->old_jam_rawat, $data);
                $msg = 'Observasi Rawat Inap Kebidanan berhasil diperbarui.';
            } else {
                $repository->store($data);
                $msg = 'Observasi Rawat Inap Kebidanan berhasil disimpan.';
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

    public function delete(string $tglPerawatan, string $jamRawat, CatatanObservasiRanapKebidananRepository $repository)
    {
        try {
            $repository->delete($this->noRawat, $tglPerawatan, $jamRawat);
            $this->loadObservasis();
            $this->dispatch('swal', ['title' => 'Terhapus!', 'text' => 'Observasi Rawat Inap Kebidanan berhasil dihapus.', 'icon' => 'success']);
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

        return view('livewire.modul.rawat-inap.sub-rawat-inap.observasi-ranap-kebidanan.index', [
            'petugasList' => $petugasList,
        ])->layout('layouts.app', ['title' => 'Observasi Rawat Inap Kebidanan']);
    }
}
