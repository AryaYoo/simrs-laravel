<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\ChecklistPreOperasi;

use Livewire\Component;
use App\Models\RegPeriksa;
use App\Models\Pegawai;
use App\Models\Dokter;
use App\Models\Petugas;
use App\Livewire\Concerns\WithOptimisticLocking;
use App\Repositories\RawatInap\ChecklistPreOperasiRepository;
use Carbon\Carbon;

class Index extends Component
{
    use WithOptimisticLocking;

    public $noRawat;
    public $regPeriksa;
    public $checklists = [];
    public ?array $detailChecklist = null;

    // Filter/Search list
    public $search = '';

    // Form State (matching Image 2)
    public $tanggal_date, $tanggal_time;
    public $autoTime = true;
    public $old_tanggal;
    public $sncn = '';
    public $tindakan = '';
    public $kd_dokter_bedah = '', $nm_dokter_bedah = '';
    public $kd_dokter_anestesi = '', $nm_dokter_anestesi = '';

    // Perawat Melakukan Konfirmasi
    public $identitas = 'Ya';
    public $keadaan_umum = 'Baik';
    public $penandaan_area_operasi = 'Ada';
    public $surat_ijin_bedah = 'Ada';
    public $surat_ijin_anestesi = 'Ada';
    public $surat_ijin_transfusi = 'Tidak Ada';
    public $persiapan_darah = 'Tidak Ada';
    public $keterangan_persiapan_darah = '';
    public $perlengkapan_khusus = 'Tidak Ada';

    // Hasil Pemeriksaan Penunjang
    public $pemeriksaan_penunjang_rontgen = 'Tidak Ada';
    public $keterangan_pemeriksaan_penunjang_rontgen = '';
    public $pemeriksaan_penunjang_ekg = 'Tidak Ada';
    public $keterangan_pemeriksaan_penunjang_ekg = '';
    public $pemeriksaan_penunjang_usg = 'Tidak Ada';
    public $keterangan_pemeriksaan_penunjang_usg = '';
    public $pemeriksaan_penunjang_ctscan = 'Tidak Ada';
    public $keterangan_pemeriksaan_penunjang_ctscan = '';
    public $pemeriksaan_penunjang_mri = 'Tidak Ada';
    public $keterangan_pemeriksaan_penunjang_mri = '';

    // Petugas
    public $nip_petugas_ruangan = '', $nm_petugas_ruangan = '';
    public $nip_perawat_ok = '', $nm_perawat_ok = '';

    // Lookups
    public $dokterBedahSearch = '';
    public $dokterAnestesiSearch = '';
    public $petugasRuanganSearch = '';
    public $perawatOkSearch = '';

    // UI Panel & Modal states
    public $isPanelOpen = false;
    public $isEditMode = false;
    public $showDetailModal = false;

    public function mount($no_rawat)
    {
        $this->noRawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'poliklinik', 'kamarInap.kamar.bangsal'])
            ->where('no_rawat', $this->noRawat)
            ->firstOrFail();

        $this->initializeLock($this->regPeriksa);
        $this->loadChecklists();

        $this->tanggal_date = now()->format('Y-m-d');
        $this->tanggal_time = now()->format('H:i:s');
    }

    public function loadChecklists()
    {
        $repo = new ChecklistPreOperasiRepository();
        $this->checklists = $repo->getByNoRawat($this->noRawat);
    }

    public function openPanel()
    {
        $this->reset([
            'old_tanggal', 'sncn', 'tindakan',
            'kd_dokter_bedah', 'nm_dokter_bedah', 'dokterBedahSearch',
            'kd_dokter_anestesi', 'nm_dokter_anestesi', 'dokterAnestesiSearch',
            'keterangan_persiapan_darah',
            'keterangan_pemeriksaan_penunjang_rontgen',
            'keterangan_pemeriksaan_penunjang_ekg',
            'keterangan_pemeriksaan_penunjang_usg',
            'keterangan_pemeriksaan_penunjang_ctscan',
            'keterangan_pemeriksaan_penunjang_mri',
            'nip_petugas_ruangan', 'nm_petugas_ruangan', 'petugasRuanganSearch',
            'nip_perawat_ok', 'nm_perawat_ok', 'perawatOkSearch',
        ]);

        $this->identitas = 'Ya';
        $this->keadaan_umum = 'Baik';
        $this->penandaan_area_operasi = 'Ada';
        $this->surat_ijin_bedah = 'Ada';
        $this->surat_ijin_anestesi = 'Ada';
        $this->surat_ijin_transfusi = 'Tidak Ada';
        $this->persiapan_darah = 'Tidak Ada';
        $this->perlengkapan_khusus = 'Tidak Ada';

        $this->pemeriksaan_penunjang_rontgen = 'Tidak Ada';
        $this->pemeriksaan_penunjang_ekg = 'Tidak Ada';
        $this->pemeriksaan_penunjang_usg = 'Tidak Ada';
        $this->pemeriksaan_penunjang_ctscan = 'Tidak Ada';
        $this->pemeriksaan_penunjang_mri = 'Tidak Ada';

        $this->isEditMode = false;
        $this->autoTime = true;
        $this->tanggal_date = now()->format('Y-m-d');
        $this->tanggal_time = now()->format('H:i:s');

        // Auto-fill DPJP sebagai default dokter bedah jika tersedia
        if ($this->regPeriksa->dokter) {
            $this->kd_dokter_bedah = $this->regPeriksa->dokter->kd_dokter;
            $this->nm_dokter_bedah = $this->regPeriksa->dokter->nm_dokter;
        }

        // Auto-fill petugas ruangan dari user yang login
        $loggedInUsername = auth()->user()->username ?? null;
        if ($loggedInUsername) {
            $pegawai = Pegawai::find($loggedInUsername);
            if ($pegawai) {
                $this->nip_petugas_ruangan = $pegawai->nik;
                $this->nm_petugas_ruangan = $pegawai->nama;
            }
        }

        $this->isPanelOpen = true;
        $this->dispatch('set-autotime', ['status' => true]);
    }

    public function edit(string $tanggal)
    {
        $item = collect($this->checklists)->first(fn($c) => $c['tanggal'] === $tanggal);
        if (!$item) return;

        $this->old_tanggal = $item['tanggal'];
        $dt = Carbon::parse($item['tanggal']);
        $this->tanggal_date = $dt->format('Y-m-d');
        $this->tanggal_time = $dt->format('H:i:s');
        $this->autoTime = false;

        $this->sncn = $item['sncn'] ?? '';
        $this->tindakan = $item['tindakan'] ?? '';
        $this->kd_dokter_bedah = $item['kd_dokter_bedah'] ?? '';
        $this->nm_dokter_bedah = $item['dokter_bedah']['nama'] ?? '';
        $this->kd_dokter_anestesi = $item['kd_dokter_anestesi'] ?? '';
        $this->nm_dokter_anestesi = $item['dokter_anestesi']['nama'] ?? '';

        $this->identitas = $item['identitas'] ?? 'Ya';
        $this->keadaan_umum = $item['keadaan_umum'] ?? 'Baik';
        $this->penandaan_area_operasi = $item['penandaan_area_operasi'] ?? 'Ada';
        $this->surat_ijin_bedah = $item['surat_ijin_bedah'] ?? 'Ada';
        $this->surat_ijin_anestesi = $item['surat_ijin_anestesi'] ?? 'Ada';
        $this->surat_ijin_transfusi = $item['surat_ijin_transfusi'] ?? 'Tidak Ada';
        $this->persiapan_darah = $item['persiapan_darah'] ?? 'Tidak Ada';
        $this->keterangan_persiapan_darah = $item['keterangan_persiapan_darah'] ?? '';
        $this->perlengkapan_khusus = $item['perlengkapan_khusus'] ?? 'Tidak Ada';

        $this->pemeriksaan_penunjang_rontgen = $item['pemeriksaan_penunjang_rontgen'] ?? 'Tidak Ada';
        $this->keterangan_pemeriksaan_penunjang_rontgen = $item['keterangan_pemeriksaan_penunjang_rontgen'] ?? '';
        $this->pemeriksaan_penunjang_ekg = $item['pemeriksaan_penunjang_ekg'] ?? 'Tidak Ada';
        $this->keterangan_pemeriksaan_penunjang_ekg = $item['keterangan_pemeriksaan_penunjang_ekg'] ?? '';
        $this->pemeriksaan_penunjang_usg = $item['pemeriksaan_penunjang_usg'] ?? 'Tidak Ada';
        $this->keterangan_pemeriksaan_penunjang_usg = $item['keterangan_pemeriksaan_penunjang_usg'] ?? '';
        $this->pemeriksaan_penunjang_ctscan = $item['pemeriksaan_penunjang_ctscan'] ?? 'Tidak Ada';
        $this->keterangan_pemeriksaan_penunjang_ctscan = $item['keterangan_pemeriksaan_penunjang_ctscan'] ?? '';
        $this->pemeriksaan_penunjang_mri = $item['pemeriksaan_penunjang_mri'] ?? 'Tidak Ada';
        $this->keterangan_pemeriksaan_penunjang_mri = $item['keterangan_pemeriksaan_penunjang_mri'] ?? '';

        $this->nip_petugas_ruangan = $item['nip_petugas_ruangan'] ?? '';
        $this->nm_petugas_ruangan = $item['petugas_ruangan']['nama'] ?? '';
        $this->nip_perawat_ok = $item['nip_perawat_ok'] ?? '';
        $this->nm_perawat_ok = $item['perawat_ok']['nama'] ?? '';

        $this->isEditMode = true;
        $this->isPanelOpen = true;
        $this->dispatch('set-autotime', ['status' => false]);
    }

    public function showDetail(string $tanggal)
    {
        $this->detailChecklist = collect($this->checklists)->first(fn($c) => $c['tanggal'] === $tanggal);
        $this->showDetailModal = true;
    }

    public function selectDokterBedah(string $kd, string $nama)
    {
        $this->kd_dokter_bedah = $kd;
        $this->nm_dokter_bedah = $nama;
        $this->dokterBedahSearch = '';
    }

    public function selectDokterAnestesi(string $kd, string $nama)
    {
        $this->kd_dokter_anestesi = $kd;
        $this->nm_dokter_anestesi = $nama;
        $this->dokterAnestesiSearch = '';
    }

    public function selectPetugasRuangan(string $nip, string $nama)
    {
        $this->nip_petugas_ruangan = $nip;
        $this->nm_petugas_ruangan = $nama;
        $this->petugasRuanganSearch = '';
    }

    public function selectPerawatOk(string $nip, string $nama)
    {
        $this->nip_perawat_ok = $nip;
        $this->nm_perawat_ok = $nama;
        $this->perawatOkSearch = '';
    }

    public function save(ChecklistPreOperasiRepository $repository)
    {
        try {
            $this->validate([
                'tanggal_date'       => 'required|date',
                'tanggal_time'       => 'required',
                'sncn'               => 'required|string|max:25',
                'tindakan'           => 'required|string|max:50',
                'kd_dokter_bedah'    => 'required|string',
                'kd_dokter_anestesi' => 'required|string',
            ], [
                'tanggal_date.required'       => 'Tanggal wajib diisi.',
                'tanggal_time.required'       => 'Jam wajib diisi.',
                'sncn.required'               => 'SN/CN wajib diisi.',
                'sncn.max'                    => 'SN/CN maksimal 25 karakter.',
                'tindakan.required'           => 'Tindakan operasi wajib diisi.',
                'tindakan.max'                => 'Tindakan maksimal 50 karakter.',
                'kd_dokter_bedah.required'    => 'Dokter Bedah wajib dipilih.',
                'kd_dokter_anestesi.required' => 'Dokter Anestesi wajib dipilih.',
            ]);

            $tanggalFull = $this->tanggal_date . ' ' . $this->tanggal_time;

            $data = [
                'no_rawat'                                 => $this->noRawat,
                'tanggal'                                  => $tanggalFull,
                'sncn'                                     => $this->sncn,
                'tindakan'                                 => $this->tindakan,
                'kd_dokter_bedah'                          => $this->kd_dokter_bedah,
                'kd_dokter_anestesi'                       => $this->kd_dokter_anestesi,
                'identitas'                                => $this->identitas,
                'surat_ijin_bedah'                         => $this->surat_ijin_bedah,
                'surat_ijin_anestesi'                      => $this->surat_ijin_anestesi,
                'surat_ijin_transfusi'                     => $this->surat_ijin_transfusi,
                'penandaan_area_operasi'                   => $this->penandaan_area_operasi,
                'keadaan_umum'                             => $this->keadaan_umum,
                'pemeriksaan_penunjang_rontgen'            => $this->pemeriksaan_penunjang_rontgen,
                'keterangan_pemeriksaan_penunjang_rontgen' => $this->keterangan_pemeriksaan_penunjang_rontgen ?? '',
                'pemeriksaan_penunjang_ekg'                => $this->pemeriksaan_penunjang_ekg,
                'keterangan_pemeriksaan_penunjang_ekg'     => $this->keterangan_pemeriksaan_penunjang_ekg ?? '',
                'pemeriksaan_penunjang_usg'                => $this->pemeriksaan_penunjang_usg,
                'keterangan_pemeriksaan_penunjang_usg'     => $this->keterangan_pemeriksaan_penunjang_usg ?? '',
                'pemeriksaan_penunjang_ctscan'             => $this->pemeriksaan_penunjang_ctscan,
                'keterangan_pemeriksaan_penunjang_ctscan'  => $this->keterangan_pemeriksaan_penunjang_ctscan ?? '',
                'pemeriksaan_penunjang_mri'                => $this->pemeriksaan_penunjang_mri,
                'keterangan_pemeriksaan_penunjang_mri'     => $this->keterangan_pemeriksaan_penunjang_mri ?? '',
                'persiapan_darah'                          => $this->persiapan_darah,
                'keterangan_persiapan_darah'               => $this->keterangan_persiapan_darah ?? '',
                'perlengkapan_khusus'                      => $this->perlengkapan_khusus,
                'nip_petugas_ruangan'                      => $this->nip_petugas_ruangan ?: null,
                'nip_perawat_ok'                           => $this->nip_perawat_ok ?: null,
            ];

            $this->validateLock($this->regPeriksa);

            if ($this->isEditMode) {
                $repository->update($this->noRawat, $this->old_tanggal, $data);
                $msg = 'Checklist Pre Operasi berhasil diperbarui.';
            } else {
                $repository->create($data);
                $msg = 'Checklist Pre Operasi berhasil disimpan.';
            }

            $this->initializeLock($this->regPeriksa->fresh());
            $this->loadChecklists();
            $this->isPanelOpen = false;

            $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => $msg, 'icon' => 'success']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('swal', ['title' => 'Validasi Gagal!', 'text' => 'Pastikan isian mandatory (SN/CN, Tindakan, Dokter Bedah & Anestesi) sudah terisi.', 'icon' => 'warning']);
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal Menyimpan!', 'text' => 'Terjadi kesalahan: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function delete(string $tanggal, ChecklistPreOperasiRepository $repository)
    {
        try {
            $repository->delete($this->noRawat, $tanggal);
            $this->loadChecklists();
            $this->dispatch('swal', ['title' => 'Terhapus!', 'text' => 'Checklist Pre Operasi berhasil dihapus.', 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal Menghapus!', 'text' => 'Terjadi kesalahan: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function render()
    {
        // Dokter Bedah Lookup
        $dokterBedahList = [];
        if (strlen($this->dokterBedahSearch) >= 2) {
            $dokterBedahList = Dokter::where('status', '1')
                ->where(function ($q) {
                    $q->where('nm_dokter', 'like', '%' . $this->dokterBedahSearch . '%')
                      ->orWhere('kd_dokter', 'like', '%' . $this->dokterBedahSearch . '%');
                })
                ->limit(8)
                ->get();
        }

        // Dokter Anestesi Lookup
        $dokterAnestesiList = [];
        if (strlen($this->dokterAnestesiSearch) >= 2) {
            $dokterAnestesiList = Dokter::where('status', '1')
                ->where(function ($q) {
                    $q->where('nm_dokter', 'like', '%' . $this->dokterAnestesiSearch . '%')
                      ->orWhere('kd_dokter', 'like', '%' . $this->dokterAnestesiSearch . '%');
                })
                ->limit(8)
                ->get();
        }

        // Petugas Ruangan Lookup
        $petugasRuanganList = [];
        if (strlen($this->petugasRuanganSearch) >= 2) {
            $petugasRuanganList = Petugas::where('status', '1')
                ->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->petugasRuanganSearch . '%')
                      ->orWhere('nip', 'like', '%' . $this->petugasRuanganSearch . '%');
                })
                ->limit(8)
                ->get();
        }

        // Perawat OK Lookup
        $perawatOkList = [];
        if (strlen($this->perawatOkSearch) >= 2) {
            $perawatOkList = Petugas::where('status', '1')
                ->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->perawatOkSearch . '%')
                      ->orWhere('nip', 'like', '%' . $this->perawatOkSearch . '%');
                })
                ->limit(8)
                ->get();
        }

        // Filter tabel
        $filteredChecklists = $this->checklists;
        if (!empty($this->search)) {
            $kw = strtolower($this->search);
            $filteredChecklists = array_filter($filteredChecklists, function ($item) use ($kw) {
                return str_contains(strtolower($item['tindakan'] ?? ''), $kw) ||
                       str_contains(strtolower($item['sncn'] ?? ''), $kw) ||
                       str_contains(strtolower($item['dokter_bedah']['nama'] ?? ''), $kw) ||
                       str_contains(strtolower($item['dokter_anestesi']['nama'] ?? ''), $kw) ||
                       str_contains(strtolower($item['tanggal'] ?? ''), $kw);
            });
        }

        return view('livewire.modul.rawat-inap.sub-rawat-inap.checklist-pre-operasi.index', [
            'filteredChecklists'  => $filteredChecklists,
            'dokterBedahList'     => $dokterBedahList,
            'dokterAnestesiList'  => $dokterAnestesiList,
            'petugasRuanganList'  => $petugasRuanganList,
            'perawatOkList'       => $perawatOkList,
        ])->layout('layouts.app', ['title' => 'Data Checklist Pre Operasi']);
    }
}
