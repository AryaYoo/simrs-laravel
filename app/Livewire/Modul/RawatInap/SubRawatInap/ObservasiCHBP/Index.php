<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\ObservasiCHBP;

use Livewire\Component;
use App\Models\RegPeriksa;
use App\Livewire\Concerns\WithOptimisticLocking;
use App\Repositories\RawatInap\CatatanObservasiCHBPRepository;

class Index extends Component
{
    use WithOptimisticLocking;

    public $noRawat;
    public $regPeriksa;
    public $observasis = [];

    // Form State
    public $tgl_perawatan, $jam_rawat;
    public $td, $hr, $suhu, $djj, $his, $ppv, $keterangan;
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
        $repo = new CatatanObservasiCHBPRepository();
        $this->observasis = $repo->getByNoRawat($this->noRawat);
    }

    public function openModal()
    {
        $this->reset([
            'td', 'hr', 'suhu', 'djj', 'his', 'ppv', 'keterangan',
            'nip', 'nmPetugas', 'petugasSearch', 'old_tgl_perawatan', 'old_jam_rawat'
        ]);
        $this->isEditMode = false;
        $this->autoTime = true;
        $this->tgl_perawatan = now()->format('Y-m-d');
        $this->jam_rawat = now()->format('H:i:s');

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
            
            $this->td = $obs['td'];
            $this->hr = $obs['hr'];
            $this->suhu = $obs['suhu'];
            $this->djj = $obs['djj'];
            $this->his = $obs['his'];
            $this->ppv = $obs['ppv'];
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

    public function save(CatatanObservasiCHBPRepository $repository)
    {
        try {
            $this->validate([
                'tgl_perawatan' => 'required|date',
                'jam_rawat' => 'required',
                'td' => 'required|string|max:8',
                'hr' => 'nullable|string|max:5',
                'suhu' => 'nullable|string|max:5',
                'djj' => 'required|string|max:5',
                'his' => 'required|string|max:20',
                'ppv' => 'required|string|max:10',
                'keterangan' => 'required|string|max:50',
                'nip' => 'required',
            ], [
                'tgl_perawatan.required' => 'Tanggal Observasi wajib diisi.',
                'jam_rawat.required' => 'Jam Observasi wajib diisi.',
                'nip.required' => 'Petugas wajib dipilih.',
                'td.required' => 'Tekanan Darah wajib diisi.',
                'td.max' => 'Tekanan Darah maksimal 8 karakter.',
                'hr.max' => 'Nadi maksimal 5 karakter.',
                'suhu.max' => 'Suhu Rectal maksimal 5 karakter.',
                'djj.required' => 'DJJ wajib diisi.',
                'djj.max' => 'DJJ maksimal 5 karakter.',
                'his.required' => 'HIS wajib diisi.',
                'his.max' => 'HIS maksimal 20 karakter.',
                'ppv.required' => 'PPV wajib diisi.',
                'ppv.max' => 'PPV maksimal 10 karakter.',
                'keterangan.required' => 'Keterangan wajib diisi.',
                'keterangan.max' => 'Keterangan maksimal 50 karakter.',
            ]);

            $this->validateLock($this->regPeriksa->fresh());

            $data = [
                'tgl_perawatan' => $this->tgl_perawatan,
                'jam_rawat' => $this->jam_rawat,
                'no_rawat' => $this->noRawat,
                'td' => $this->td,
                'hr' => $this->hr ?? '-',
                'suhu' => $this->suhu ?? '-',
                'djj' => $this->djj,
                'his' => $this->his,
                'ppv' => $this->ppv,
                'keterangan' => $this->keterangan,
                'nip' => $this->nip,
            ];

            if ($this->isEditMode) {
                $repository->update($this->noRawat, $this->old_tgl_perawatan, $this->old_jam_rawat, $data);
                $msg = 'Observasi CHBP berhasil diperbarui.';
            } else {
                $repository->store($data);
                $msg = 'Observasi CHBP berhasil disimpan.';
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

    public function delete(string $tglPerawatan, string $jamRawat, CatatanObservasiCHBPRepository $repository)
    {
        try {
            $repository->delete($this->noRawat, $tglPerawatan, $jamRawat);
            $this->loadObservasis();
            $this->dispatch('swal', ['title' => 'Terhapus!', 'text' => 'Observasi CHBP berhasil dihapus.', 'icon' => 'success']);
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

        return view('livewire.modul.rawat-inap.sub-rawat-inap.observasi-chbp.index', [
            'petugasList' => $petugasList,
        ])->layout('layouts.app', ['title' => 'Observasi CHBP']);
    }
}
