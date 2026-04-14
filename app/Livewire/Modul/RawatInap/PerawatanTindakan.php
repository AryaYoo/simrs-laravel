<?php

namespace App\Livewire\Modul\RawatInap;

use App\Models\PemeriksaanRanap;
use App\Models\RegPeriksa;
use App\Livewire\Concerns\WithOptimisticLocking;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Perawatan/Tindakan Pasien Rawat Inap', 'hideSidebar' => true])]
class PerawatanTindakan extends Component
{
    use WithOptimisticLocking;

    public string $no_rawat;
    public $regPeriksa;
    public string $activeTab = 'pemeriksaan';

    // Form State for Pemeriksaan
    public bool $createModalOpen = false;
    public bool $isEditMode = false;
    public $tgl_perawatan, $jam_rawat, $suhu_tubuh, $tensi, $nadi, $respirasi;
    public $tinggi, $berat, $spo2, $gcs, $kesadaran;
    public $keluhan, $pemeriksaan, $alergi, $penilaian, $rtl, $instruksi, $evaluasi, $nip;
    
    public $currentJabatan = '-';
    
    // Reference State
    public $lastPemeriksaan;
    public string $pegawaiSearch = '';

    // Penanganan Dokter & Petugas State
    public bool $tindakanCreateModalOpen = false;
    public bool $tindakanDetailModalOpen = false;
    public $kd_dokter_tindakan, $nm_dokter_tindakan;
    public $nip_tindakan, $nm_petugas_tindakan;
    public string $dokterSearch = '', $petugasSearch = '', $tindakanSearch = '';
    
    public bool $tindakanLookupOpen = false;
    public string $lookupType = 'dr'; // 'dr' or 'pr'
    public $kd_jenis_prw_selected, $nm_perawatan_selected;
    public $isEditTindakanMode = false;
    public $original_tindakan_type, $original_tgl_perawatan, $original_jam_rawat, $original_kd_jenis_prw;

    public function mount(string $no_rawat): void
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with([
            'pasien',
            'penjab',
            'dokter',
            'kamarInap.kamar',
            'permintaanRanap.kamar',
        ])
        ->where('no_rawat', $this->no_rawat)
        ->firstOrFail();

        $this->tgl_perawatan = now()->format('Y-m-d');
        $this->jam_rawat     = now()->format('H:i:s');

        // SOP #1: Initialize concurrency lock
        $this->initializeLock($this->regPeriksa);
    }

    public function openCreateModal()
    {
        $this->reset(['suhu_tubuh', 'tensi', 'nadi', 'respirasi', 'tinggi', 'berat', 'spo2', 'gcs', 'kesadaran', 'keluhan', 'pemeriksaan', 'alergi', 'penilaian', 'rtl', 'instruksi', 'evaluasi', 'nip', 'isEditMode']);
        
        $this->tgl_perawatan = now()->format('Y-m-d');
        $this->jam_rawat     = now()->format('H:i:s');
        $this->isEditMode    = false;

        // Fetch last data as reference
        $this->lastPemeriksaan = \App\Models\PemeriksaanRanap::where('no_rawat', $this->no_rawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->first();

        if ($this->lastPemeriksaan) {
            $this->suhu_tubuh  = $this->lastPemeriksaan->suhu_tubuh;
            $this->tensi       = $this->lastPemeriksaan->tensi;
            $this->nadi        = $this->lastPemeriksaan->nadi;
            $this->respirasi   = $this->lastPemeriksaan->respirasi;
            $this->tinggi      = $this->lastPemeriksaan->tinggi;
            $this->berat       = $this->lastPemeriksaan->berat;
            $this->spo2        = $this->lastPemeriksaan->spo2;
            $this->gcs         = $this->lastPemeriksaan->gcs;
            $this->kesadaran   = $this->lastPemeriksaan->kesadaran;
            $this->alergi      = $this->lastPemeriksaan->alergi;
        }

        $this->createModalOpen = true;
    }

    public function editPemeriksaan($data)
    {
        // $data is an array from the JSON in the view
        $this->tgl_perawatan = $data['tgl_perawatan'];
        $this->jam_rawat     = $data['jam_rawat'];
        $this->suhu_tubuh    = $data['suhu_tubuh'];
        $this->tensi         = $data['tensi'];
        $this->nadi          = $data['nadi'];
        $this->respirasi     = $data['respirasi'];
        $this->tinggi        = $data['tinggi'];
        $this->berat         = $data['berat'];
        $this->spo2          = str_replace('%', '', $data['spo2']);
        $this->gcs           = $data['gcs'];
        $this->kesadaran     = $data['kesadaran'];
        $this->keluhan       = $data['keluhan'];
        $this->pemeriksaan   = $data['pemeriksaan'];
        $this->alergi        = $data['alergi'];
        $this->penilaian     = $data['penilaian'];
        $this->rtl           = $data['rtl'];
        $this->instruksi     = $data['instruksi'];
        $this->evaluasi      = $data['evaluasi'];
        $this->nip           = $data['nip'];
        
        $this->currentJabatan = $data['jbtn_pegawai'];
        $this->isEditMode     = true;

        // Initialize lock by fetching the model first
        $model = \App\Models\PemeriksaanRanap::where('no_rawat', $this->no_rawat)
            ->where('tgl_perawatan', $this->tgl_perawatan)
            ->where('jam_rawat', $this->jam_rawat)
            ->first();

        if ($model) {
            $this->initializeLock($model);
        }

        $this->createModalOpen = true;
    }

    public function updatedNip($value)
    {
        if ($value) {
            $staff = \App\Models\Pegawai::find($value);
            $this->currentJabatan = $staff->jbtn ?? '-';
            $this->pegawaiSearch = '';
        } else {
            $this->currentJabatan = '-';
        }
    }

    // Penanganan Dokter & Petugas Methods
    public function selectDokter($kd, $nama)
    {
        $this->kd_dokter_tindakan = $kd;
        $this->nm_dokter_tindakan = $nama;
        $this->dokterSearch = '';
    }

    public function selectPetugas($nip, $nama)
    {
        $this->nip_tindakan = $nip;
        $this->nm_petugas_tindakan = $nama;
        $this->petugasSearch = '';
    }

    public function openTindakanCreateModal()
    {
        $this->reset(['kd_dokter_tindakan', 'nm_dokter_tindakan', 'nip_tindakan', 'nm_petugas_tindakan', 'kd_jenis_prw_selected', 'nm_perawatan_selected', 'isEditTindakanMode', 'original_tindakan_type', 'original_tgl_perawatan', 'original_jam_rawat', 'original_kd_jenis_prw']);
        $this->tindakanCreateModalOpen = true;
    }

    public function openTindakanLookup($type)
    {
        $this->lookupType = $type;
        $this->tindakanSearch = '';
        $this->tindakanLookupOpen = true;
    }

    public function editTindakan($data)
    {
        // Reset and populate
        $this->reset(['kd_dokter_tindakan', 'nm_dokter_tindakan', 'nip_tindakan', 'nm_petugas_tindakan', 'kd_jenis_prw_selected', 'nm_perawatan_selected']);
        
        $this->isEditTindakanMode = true;
        $this->original_tindakan_type = $data['type']; // drpr, dr, pr
        $this->original_tgl_perawatan = $data['tgl_perawatan'];
        $this->original_jam_rawat = $data['jam_rawat'];
        $this->original_kd_jenis_prw = $data['kd_jenis_prw'];

        $this->kd_jenis_prw_selected = $data['kd_jenis_prw'];
        $this->nm_perawatan_selected = $data['nm_perawatan'];
        
        $this->kd_dokter_tindakan = $data['kd_staff_dr'] != '-' ? $data['kd_staff_dr'] : null;
        $this->nm_dokter_tindakan = $data['staff_dr'] != '-' ? $data['staff_dr'] : null;
        $this->nip_tindakan = $data['kd_staff_pr'] != '-' ? $data['kd_staff_pr'] : null;
        $this->nm_petugas_tindakan = $data['staff_pr'] != '-' ? $data['staff_pr'] : null;
        
        $this->lookupType = ($data['type'] == 'pr') ? 'pr' : 'dr';
        
        $this->tindakanCreateModalOpen = true;
    }

    public function previewTindakan($kd, $nm)
    {
        $this->kd_jenis_prw_selected = $kd;
        $this->nm_perawatan_selected = $nm;
        $this->tindakanLookupOpen = false;
    }

    public function saveTindakan()
    {
        // SOP Rekam Medis: Validate if admission is already completed or canceled.
        if ($this->regPeriksa->stts === 'Sudah' || $this->regPeriksa->stts === 'Batal') {
            $this->dispatch('swal', ['title' => 'Terkunci', 'text' => 'Pelayanan pasien sudah selesai/dibatalkan. Modifikasi data tindakan ditolak (SOP).', 'icon' => 'error']);
            return;
        }

        if (!$this->kd_jenis_prw_selected) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Tindakan belum dipilih.', 'icon' => 'error']);
            return;
        }

        if ($this->lookupType == 'dr' && !$this->kd_dokter_tindakan) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Dokter belum dipilih.', 'icon' => 'error']);
            return;
        }

        if ($this->lookupType == 'pr' && !$this->nip_tindakan) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Petugas belum dipilih.', 'icon' => 'error']);
            return;
        }

        // SOP #1: Validate concurrency lock
        $this->validateLock($this->regPeriksa);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $tarif = \App\Models\JnsPerawatanInap::find($this->kd_jenis_prw_selected);
            if (!$tarif) throw new \Exception("Tarif tidak ditemukan.");

            if ($this->isEditTindakanMode) {
                // Hapus tindakan lama sebelum memasukkan yang baru
                if ($this->original_tindakan_type == 'drpr') {
                    \App\Models\RawatInapDrpr::where(['no_rawat' => $this->no_rawat, 'kd_jenis_prw' => $this->original_kd_jenis_prw, 'tgl_perawatan' => $this->original_tgl_perawatan, 'jam_rawat' => $this->original_jam_rawat])->delete();
                } elseif ($this->original_tindakan_type == 'dr') {
                    \App\Models\RawatInapDr::where(['no_rawat' => $this->no_rawat, 'kd_jenis_prw' => $this->original_kd_jenis_prw, 'tgl_perawatan' => $this->original_tgl_perawatan, 'jam_rawat' => $this->original_jam_rawat])->delete();
                } else {
                    \App\Models\RawatInapPr::where(['no_rawat' => $this->no_rawat, 'kd_jenis_prw' => $this->original_kd_jenis_prw, 'tgl_perawatan' => $this->original_tgl_perawatan, 'jam_rawat' => $this->original_jam_rawat])->delete();
                }

                $tgl = $this->original_tgl_perawatan;
                $jam = $this->original_jam_rawat;
            } else {
                $tgl = now()->format('Y-m-d');
                $jam = now()->format('H:i:s');
            }

            if ($this->kd_dokter_tindakan && $this->nip_tindakan) {
                \App\Models\RawatInapDrpr::create([
                    'no_rawat' => $this->no_rawat, 'kd_jenis_prw' => $this->kd_jenis_prw_selected, 'kd_dokter' => $this->kd_dokter_tindakan, 'nip' => $this->nip_tindakan, 'tgl_perawatan' => $tgl, 'jam_rawat' => $jam,
                    'material' => $tarif->material, 'bhp' => $tarif->bhp, 'tarif_tindakandr' => $tarif->tarif_tindakandr, 'tarif_tindakanpr' => $tarif->tarif_tindakanpr, 'kso' => $tarif->kso, 'menejemen' => $tarif->menejemen, 'biaya_rawat' => $tarif->total_byrdrpr
                ]);
            } elseif ($this->kd_dokter_tindakan) {
                \App\Models\RawatInapDr::create([
                    'no_rawat' => $this->no_rawat, 'kd_jenis_prw' => $this->kd_jenis_prw_selected, 'kd_dokter' => $this->kd_dokter_tindakan, 'tgl_perawatan' => $tgl, 'jam_rawat' => $jam,
                    'material' => $tarif->material, 'bhp' => $tarif->bhp, 'tarif_tindakandr' => $tarif->tarif_tindakandr, 'kso' => $tarif->kso, 'menejemen' => $tarif->menejemen, 'biaya_rawat' => $tarif->total_byrdr
                ]);
            } else {
                \App\Models\RawatInapPr::create([
                    'no_rawat' => $this->no_rawat, 'kd_jenis_prw' => $this->kd_jenis_prw_selected, 'nip' => $this->nip_tindakan, 'tgl_perawatan' => $tgl, 'jam_rawat' => $jam,
                    'material' => $tarif->material, 'bhp' => $tarif->bhp, 'tarif_tindakanpr' => $tarif->tarif_tindakanpr, 'kso' => $tarif->kso, 'menejemen' => $tarif->menejemen, 'biaya_rawat' => $tarif->total_byrpr
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();
            $this->tindakanCreateModalOpen = false;
            $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => 'Tindakan berhasil disimpan.', 'icon' => 'success']);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            $this->dispatch('swal', ['title' => 'Gagal!', 'text' => 'Kesalahan: ' . substr($e->getMessage(), 0, 100), 'icon' => 'error']);
        }
    }

    public function deleteTindakan($type, $kd_jenis_prw, $tgl, $jam, $kd_staff)
    {
        // SOP Rekam Medis: Validate if admission is already completed or canceled.
        if ($this->regPeriksa->stts === 'Sudah' || $this->regPeriksa->stts === 'Batal') {
            $this->dispatch('swal', ['title' => 'Terkunci', 'text' => 'Pelayanan pasien sudah selesai/dibatalkan. Penghapusan data ditolak (SOP).', 'icon' => 'error']);
            return;
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            if ($type == 'drpr') {
                \App\Models\RawatInapDrpr::where([
                    'no_rawat' => $this->no_rawat,
                    'kd_jenis_prw' => $kd_jenis_prw,
                    'tgl_perawatan' => $tgl,
                    'jam_rawat' => $jam,
                ])->delete();
            } elseif ($type == 'dr') {
                \App\Models\RawatInapDr::where([
                    'no_rawat' => $this->no_rawat,
                    'kd_jenis_prw' => $kd_jenis_prw,
                    'tgl_perawatan' => $tgl,
                    'jam_rawat' => $jam,
                ])->delete();
            } else {
                \App\Models\RawatInapPr::where([
                    'no_rawat' => $this->no_rawat,
                    'kd_jenis_prw' => $kd_jenis_prw,
                    'tgl_perawatan' => $tgl,
                    'jam_rawat' => $jam,
                ])->delete();
            }
            \Illuminate\Support\Facades\DB::commit();
            $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => 'Tindakan dihapus.', 'icon' => 'success']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            $this->dispatch('swal', ['title' => 'Gagal!', 'text' => 'Gagal menghapus.', 'icon' => 'error']);
        }
    }

    public function deletePemeriksaan($tgl, $jam)
    {
        // SOP Rekam Medis: Validate if admission is already completed or canceled.
        if ($this->regPeriksa->stts === 'Sudah' || $this->regPeriksa->stts === 'Batal') {
            $this->dispatch('swal', ['title' => 'Terkunci', 'text' => 'Pelayanan pasien sudah selesai/dibatalkan. Penghapusan data ditolak (SOP).', 'icon' => 'error']);
            return;
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            \App\Models\PemeriksaanRanap::where([
                'no_rawat'      => $this->no_rawat,
                'tgl_perawatan' => $tgl,
                'jam_rawat'     => $jam,
            ])->delete();
            
            \Illuminate\Support\Facades\DB::commit();
            $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => 'Pemeriksaan dihapus.', 'icon' => 'success']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            $this->dispatch('swal', ['title' => 'Gagal!', 'text' => 'Gagal menghapus.', 'icon' => 'error']);
        }
    }

    public function save()
    {
        $this->validate([
            'tgl_perawatan' => 'required|date',
            'jam_rawat'     => 'required',
            'nip'           => 'required',
        ], [
            'nip.required' => 'Petugas (Dokter/Perawat) harus dipilih.',
        ]);

        if ($this->isEditMode) {
            $this->updatePemeriksaan();
        } else {
            $this->storePemeriksaan();
        }
    }

    public function storePemeriksaan()
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            \App\Models\PemeriksaanRanap::create([
                'no_rawat'      => $this->no_rawat,
                'tgl_perawatan' => $this->tgl_perawatan,
                'jam_rawat'     => $this->jam_rawat,
                'suhu_tubuh'    => $this->suhu_tubuh ?: '-',
                'tensi'         => $this->tensi ?: '-',
                'nadi'          => $this->nadi ?: '-',
                'respirasi'     => $this->respirasi ?: '-',
                'tinggi'        => $this->tinggi ?: '-',
                'berat'         => $this->berat ?: '-',
                'spo2'          => $this->spo2 ?: '-',
                'gcs'           => $this->gcs ?: '-',
                'kesadaran'     => $this->kesadaran ?: 'Compos Mentis',
                'keluhan'       => $this->keluhan ?: '-',
                'pemeriksaan'   => $this->pemeriksaan ?: '-',
                'alergi'        => $this->alergi ?: '-',
                'penilaian'     => $this->penilaian ?: '-',
                'rtl'           => $this->rtl ?: '-',
                'instruksi'     => $this->instruksi ?: '-',
                'evaluasi'      => $this->evaluasi ?: '-',
                'nip'           => $this->nip,
            ]);

            \Illuminate\Support\Facades\DB::commit();

            $this->createModalOpen = false;
            
            $this->dispatch('swal', [
                'title' => 'Berhasil!',
                'text'  => 'Data pemeriksaan berhasil disimpan.',
                'icon'  => 'success',
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            $this->dispatch('swal', [
                'title' => 'Gagal Menyimpan!',
                'text'  => 'Terjadi kesalahan sistem: ' . substr($e->getMessage(), 0, 150),
                'icon'  => 'error',
            ]);
        }
    }

    public function updatePemeriksaan()
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Find the record using composite keys
            $model = \App\Models\PemeriksaanRanap::where('no_rawat', $this->no_rawat)
                ->where('tgl_perawatan', $this->tgl_perawatan)
                ->where('jam_rawat', $this->jam_rawat)
                ->first();

            if (!$model) {
                throw new \Exception("Data pemeriksaan tidak ditemukan di database. Mungkin sudah dihapus.");
            }

            // Standard SOP: Validate lock before saving to handle concurrency
            $this->validateLock($model);

            $model->update([
                'suhu_tubuh'    => $this->suhu_tubuh ?: '-',
                'tensi'         => $this->tensi ?: '-',
                'nadi'          => $this->nadi ?: '-',
                'respirasi'     => $this->respirasi ?: '-',
                'tinggi'        => $this->tinggi ?: '-',
                'berat'         => $this->berat ?: '-',
                'spo2'          => $this->spo2 ?: '-',
                'gcs'           => $this->gcs ?: '-',
                'kesadaran'     => $this->kesadaran ?: 'Compos Mentis',
                'keluhan'       => $this->keluhan ?: '-',
                'pemeriksaan'   => $this->pemeriksaan ?: '-',
                'alergi'        => $this->alergi ?: '-',
                'penilaian'     => $this->penilaian ?: '-',
                'rtl'           => $this->rtl ?: '-',
                'instruksi'     => $this->instruksi ?: '-',
                'evaluasi'      => $this->evaluasi ?: '-',
                'nip'           => $this->nip,
            ]);

            \Illuminate\Support\Facades\DB::commit();

            $this->createModalOpen = false;
            
            $this->dispatch('swal', [
                'title' => 'Berhasil!',
                'text'  => 'Data pemeriksaan berhasil diperbarui.',
                'icon'  => 'success',
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            
            // Only dispatch generic error if it's NOT a concurrency error (already handled by trait)
            if (strpos($e->getMessage(), 'CONCURRENCY_ERROR') === false) {
                $this->dispatch('swal', [
                    'title' => 'Gagal Memperbarui!',
                    'text'  => 'Terjadi kesalahan: ' . substr($e->getMessage(), 0, 150),
                    'icon'  => 'error',
                ]);
            }
        }
    }

    public function render()
    {
        $rawatInapDrpr = \App\Models\RawatInapDrpr::with(['regPeriksa.pasien:no_rkm_medis,nm_pasien', 'jnsPerawatan:kd_jenis_prw,nm_perawatan', 'dokter:kd_dokter,nm_dokter', 'petugas:nip,nama'])
            ->select('no_rawat', 'kd_jenis_prw', 'kd_dokter', 'nip', 'tgl_perawatan', 'jam_rawat', 'material', 'bhp', 'tarif_tindakandr', 'tarif_tindakanpr', 'kSO', 'kso', 'menejemen', 'biaya_rawat')
            ->where('no_rawat', $this->no_rawat)
            ->get()->map(fn(\App\Models\RawatInapDrpr $i) => [ 
                ...$i->toArray(), 
                'type' => 'drpr', 
                'staff_dr' => $i->dokter->nm_dokter ?? '-', 
                'staff_pr' => $i->petugas->nama ?? '-', 
                'nm_perawatan' => $i->jnsPerawatan->nm_perawatan ?? '-',
                'no_r_m' => $i->regPeriksa->pasien->no_rkm_medis ?? '-',
                'nm_pasien' => $i->regPeriksa->pasien->nm_pasien ?? '-',
                'kd_staff_dr' => $i->kd_dokter,
                'kd_staff_pr' => $i->nip,
                'biaya_material' => $i->material,
                'biaya_bhp' => $i->bhp,
                'biaya_dr' => $i->tarif_tindakandr,
                'biaya_pr' => $i->tarif_tindakanpr,
                'biaya_kso' => $i->kSO ?? $i->kso ?? 0,
                'biaya_menejemen' => $i->menejemen ?? 0,
            ]);

        $rawatInapDr = \App\Models\RawatInapDr::with(['regPeriksa.pasien:no_rkm_medis,nm_pasien', 'jnsPerawatan:kd_jenis_prw,nm_perawatan', 'dokter:kd_dokter,nm_dokter'])
            ->select('no_rawat', 'kd_jenis_prw', 'kd_dokter', 'tgl_perawatan', 'jam_rawat', 'material', 'bhp', 'tarif_tindakandr', 'kSO', 'kso', 'menejemen', 'biaya_rawat')
            ->where('no_rawat', $this->no_rawat)
            ->get()->map(fn(\App\Models\RawatInapDr $i) => [ 
                ...$i->toArray(), 
                'type' => 'dr', 
                'staff_dr' => $i->dokter->nm_dokter ?? '-', 
                'staff_pr' => '-', 
                'nm_perawatan' => $i->jnsPerawatan->nm_perawatan ?? '-',
                'no_r_m' => $i->regPeriksa->pasien->no_rkm_medis ?? '-',
                'nm_pasien' => $i->regPeriksa->pasien->nm_pasien ?? '-',
                'kd_staff_dr' => $i->kd_dokter,
                'kd_staff_pr' => '-',
                'biaya_material' => $i->material ?? 0,
                'biaya_bhp' => $i->bhp ?? 0,
                'biaya_dr' => $i->tarif_tindakandr ?? 0,
                'biaya_pr' => 0,
                'biaya_kso' => $i->kSO ?? $i->kso ?? 0,
                'biaya_menejemen' => $i->menejemen ?? 0,
            ]);

        $rawatInapPr = \App\Models\RawatInapPr::with(['regPeriksa.pasien:no_rkm_medis,nm_pasien', 'jnsPerawatan:kd_jenis_prw,nm_perawatan', 'petugas:nip,nama'])
            ->select('no_rawat', 'kd_jenis_prw', 'nip', 'tgl_perawatan', 'jam_rawat', 'material', 'bhp', 'tarif_tindakanpr', 'kSO', 'kso', 'menejemen', 'biaya_rawat')
            ->where('no_rawat', $this->no_rawat)
            ->get()->map(fn(\App\Models\RawatInapPr $i) => [ 
                ...$i->toArray(), 
                'type' => 'pr', 
                'staff_dr' => '-', 
                'staff_pr' => $i->petugas->nama ?? '-', 
                'nm_perawatan' => $i->jnsPerawatan->nm_perawatan ?? '-',
                'no_r_m' => $i->regPeriksa->pasien->no_rkm_medis ?? '-',
                'nm_pasien' => $i->regPeriksa->pasien->nm_pasien ?? '-',
                'kd_staff_dr' => '-',
                'kd_staff_pr' => $i->nip,
                'biaya_material' => $i->material ?? 0,
                'biaya_bhp' => $i->bhp ?? 0,
                'biaya_dr' => 0,
                'biaya_pr' => $i->tarif_tindakanpr ?? 0,
                'biaya_kso' => $i->kSO ?? $i->kso ?? 0,
                'biaya_menejemen' => $i->menejemen ?? 0,
            ]);

        $allTindakan = collect($rawatInapDrpr)->concat($rawatInapDr)->concat($rawatInapPr)->sortByDesc(fn($i) => $i['tgl_perawatan'] . $i['jam_rawat']);

        $pemeriksaanRanap = \App\Models\PemeriksaanRanap::with(['regPeriksa.pasien', 'pegawai'])
            ->where('no_rawat', $this->no_rawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->get();

        $pegawaiList = [];
        if (strlen($this->pegawaiSearch) >= 3) {
            $pegawaiList = \App\Models\Pegawai::where('nama', 'like', '%' . $this->pegawaiSearch . '%')
                ->orWhere('nik', 'like', '%' . $this->pegawaiSearch . '%')
                ->limit(10)
                ->get();
        }

        $dokterList = [];
        if (strlen($this->dokterSearch) >= 3) {
            $dokterList = \App\Models\Dokter::where('nm_dokter', 'like', '%' . $this->dokterSearch . '%')->orWhere('kd_dokter', 'like', '%' . $this->dokterSearch . '%')->limit(5)->get();
        }

        $petugasList = [];
        if (strlen($this->petugasSearch) >= 3) {
            $petugasList = \App\Models\Petugas::where('nama', 'like', '%' . $this->petugasSearch . '%')->orWhere('nip', 'like', '%' . $this->petugasSearch . '%')->limit(5)->get();
        }

        $tindakanFilter = \App\Models\JnsPerawatanInap::query();
        if (strlen($this->tindakanSearch) >= 3) {
            $tindakanFilter->where('nm_perawatan', 'like', '%' . $this->tindakanSearch . '%');
        }
        
        // Filter based on patient class if available
        $kelas = $this->regPeriksa->kamarInap->last()->kamar->kelas ?? '-';
        if ($kelas != '-') {
            $tindakanFilter->where(function($q) use ($kelas) {
                $q->where('kelas', $kelas)->orWhere('kelas', '-');
            });
        }

        if ($this->lookupType == 'dr') {
            $tindakanFilter->where('total_byrdr', '>', 0)->orWhere('total_byrdrpr', '>', 0);
        } else {
            $tindakanFilter->where('total_byrpr', '>', 0)->orWhere('total_byrdrpr', '>', 0);
        }

        return view('livewire.modul.rawat-inap.perawatan-tindakan', [
            'allTindakan'      => $allTindakan,
            'pemeriksaanRanap' => $pemeriksaanRanap,
            'pegawaiList'      => $pegawaiList,
            'dokterList'       => $dokterList,
            'petugasList'      => $petugasList,
            'tindakanList'     => $tindakanFilter->limit(50)->get(),
        ]);
    }
}
