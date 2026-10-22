<?php

namespace App\Livewire\Modul\Farmasi;

use App\Models\Pegawai;
use App\Models\PemeriksaanRanap;
use App\Models\RegPeriksa;
use App\Models\Visite;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Visite Pasien Rawat Inap'])]
class VisitePatient extends Component
{
    use WithPagination;

    public string $no_rawat = '';
    public ?RegPeriksa $regPeriksa = null;

    // Form Modal / Slide-in Panel State
    public bool $formModalOpen = false;
    public bool $isPanelOpen = false;
    public bool $isEditMode = false;
    public bool $autoTime = true;
    public ?int $selectedId = null;

    // Detail Modal State
    public bool $detailModalOpen = false;
    public ?array $activeDetail = null;

    // Form Fields
    public string $tgl_visite = '';
    public string $jam_visite = '';
    public string $nip = '';
    public string $currentJabatan = '';
    public string $pegawaiSearch = '';

    // SOAP
    public string $keluhan = '';
    public string $pemeriksaan = '';
    public string $penilaian = '';
    public string $rtl = '';
    public string $instruksi = '';
    public string $evaluasi = '';

    // Vital Signs
    public string $suhu_tubuh = '';
    public string $tensi = '';
    public string $berat = '';
    public string $tinggi = '';
    public string $respirasi = '';
    public string $nadi = '';
    public string $spo2 = '';
    public string $gcs = '';
    public string $kesadaran = 'Compos Mentis';
    public string $alergi = '';

    // Khusus Farmasi Visite
    public string $riwayat_pengobatan = '';
    public string $interaksi_obat = '';

    public bool $isEmbedded = false;

    // Reference
    public ?array $lastReference = null;

    public function mount(string $no_rawat, bool $isEmbedded = false)
    {
        $this->isEmbedded = $isEmbedded;
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'penjab', 'kamarInap', 'permintaanRanap'])
            ->where('no_rawat', $this->no_rawat)
            ->firstOrFail();

        $this->loadLastReference();
    }

    public function loadLastReference(): void
    {
        // Prioritaskan visite terakhir
        $lastVisite = Visite::where('no_rawat', $this->no_rawat)
            ->orderBy('tgl_visite', 'desc')
            ->orderBy('jam_visite', 'desc')
            ->first();

        if ($lastVisite) {
            $this->lastReference = [
                'type' => 'visite',
                'tanggal' => $lastVisite->tgl_visite,
                'jam' => $lastVisite->jam_visite,
                'suhu_tubuh' => $lastVisite->suhu_tubuh,
                'tensi' => $lastVisite->tensi,
                'nadi' => $lastVisite->nadi,
                'respirasi' => $lastVisite->respirasi,
                'tinggi' => $lastVisite->tinggi,
                'berat' => $lastVisite->berat,
                'spo2' => $lastVisite->spo2,
                'gcs' => $lastVisite->gcs,
                'kesadaran' => $lastVisite->kesadaran,
                'alergi' => $lastVisite->alergi,
                'keluhan' => $lastVisite->keluhan,
                'pemeriksaan' => $lastVisite->pemeriksaan,
                'penilaian' => $lastVisite->penilaian,
                'rtl' => $lastVisite->rtl,
                'instruksi' => $lastVisite->instruksi,
                'evaluasi' => $lastVisite->evaluasi,
                'riwayat_pengobatan' => $lastVisite->riwayat_pengobatan,
                'interaksi_obat' => $lastVisite->interaksi_obat,
            ];
            return;
        }

        // Fallback: dari pemeriksaan_ranap terakhir jika visite belum pernah diinput
        $lastRanap = PemeriksaanRanap::where('no_rawat', $this->no_rawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->first();

        if ($lastRanap) {
            $this->lastReference = [
                'type' => 'pemeriksaan_ranap',
                'tanggal' => $lastRanap->tgl_perawatan,
                'jam' => $lastRanap->jam_rawat,
                'suhu_tubuh' => $lastRanap->suhu_tubuh,
                'tensi' => $lastRanap->tensi,
                'nadi' => $lastRanap->nadi,
                'respirasi' => $lastRanap->respirasi,
                'tinggi' => $lastRanap->tinggi,
                'berat' => $lastRanap->berat,
                'spo2' => $lastRanap->spo2,
                'gcs' => $lastRanap->gcs,
                'kesadaran' => $lastRanap->kesadaran,
                'alergi' => $lastRanap->alergi,
                'keluhan' => $lastRanap->keluhan,
                'pemeriksaan' => $lastRanap->pemeriksaan,
                'penilaian' => $lastRanap->penilaian,
                'rtl' => $lastRanap->rtl,
                'instruksi' => $lastRanap->instruksi,
                'evaluasi' => $lastRanap->evaluasi,
                'riwayat_pengobatan' => '',
                'interaksi_obat' => '',
            ];
        }
    }

    public function updatedNip($val)
    {
        if ($val) {
            $pegawai = Pegawai::find($val);
            $this->currentJabatan = $pegawai->jbtn ?? '-';
            $this->pegawaiSearch = '';
        } else {
            $this->currentJabatan = '';
        }
    }

    public function selectPetugas(string $nip, string $nama): void
    {
        $this->nip = $nip;
        $pegawai = Pegawai::find($nip);
        $this->currentJabatan = $pegawai->jbtn ?? '-';
        $this->pegawaiSearch = '';
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->isEditMode = false;
        $this->selectedId = null;

        $this->tgl_visite = date('Y-m-d');
        $this->jam_visite = date('H:i:s');
        $this->autoTime = true;
        $this->keluhan = '';
        $this->pemeriksaan = '';
        $this->suhu_tubuh = '';
        $this->tensi = '';
        $this->berat = '';
        $this->tinggi = '';
        $this->respirasi = '';
        $this->nadi = '';
        $this->spo2 = '';
        $this->gcs = '';
        $this->kesadaran = 'Compos Mentis';
        $this->alergi = '';
        $this->penilaian = '';
        $this->rtl = '';
        $this->instruksi = '';
        $this->evaluasi = '';
        $this->riwayat_pengobatan = '';
        $this->interaksi_obat = '';

        // Auto-detect logged in user
        $username = auth()->user()?->username;
        if ($username) {
            $pegawai = Pegawai::find($username);
            if ($pegawai) {
                $this->nip = $pegawai->nik;
                $this->currentJabatan = $pegawai->jbtn ?? '-';
            }
        }

        $this->isPanelOpen = true;
        $this->formModalOpen = true;
        $this->dispatch('set-autotime', ['status' => true]);
    }

    public function openPanel(): void
    {
        $this->openCreateModal();
    }

    public function fillAutoData(): void
    {
        if ($this->lastReference) {
            $this->suhu_tubuh  = $this->lastReference['suhu_tubuh'] ?? '-';
            $this->tensi       = $this->lastReference['tensi'] ?? '-';
            $this->nadi        = $this->lastReference['nadi'] ?? '-';
            $this->respirasi   = $this->lastReference['respirasi'] ?? '-';
            $this->tinggi      = $this->lastReference['tinggi'] ?? '-';
            $this->berat       = $this->lastReference['berat'] ?? '-';
            $this->spo2        = $this->lastReference['spo2'] ?? '-';
            $this->gcs         = $this->lastReference['gcs'] ?? '-';
            $this->kesadaran   = $this->lastReference['kesadaran'] ?? 'Compos Mentis';
            $this->alergi      = $this->lastReference['alergi'] ?? '-';
            $this->keluhan     = $this->lastReference['keluhan'] ?? '-';
            $this->pemeriksaan = $this->lastReference['pemeriksaan'] ?? '-';
            $this->penilaian   = $this->lastReference['penilaian'] ?? '-';
            $this->rtl         = $this->lastReference['rtl'] ?? '-';
            $this->instruksi   = $this->lastReference['instruksi'] ?? '-';
            $this->evaluasi    = $this->lastReference['evaluasi'] ?? '-';

            if (!empty($this->lastReference['riwayat_pengobatan'])) {
                $this->riwayat_pengobatan = $this->lastReference['riwayat_pengobatan'];
            }
            if (!empty($this->lastReference['interaksi_obat'])) {
                $this->interaksi_obat = $this->lastReference['interaksi_obat'];
            }
        }

        $username = auth()->user()?->username;
        if ($username) {
            $pegawai = Pegawai::find($username);
            if ($pegawai) {
                $this->nip = $pegawai->nik;
                $this->currentJabatan = $pegawai->jbtn ?? '-';
            }
        }
    }

    public function editVisite(int $id): void
    {
        $this->resetValidation();
        $visite = Visite::findOrFail($id);

        $this->selectedId = $visite->id;
        $this->isEditMode = true;

        $this->tgl_visite = $visite->tgl_visite;
        $this->jam_visite = $visite->jam_visite;
        $this->nip = $visite->nip;
        $pegawai = Pegawai::find($visite->nip);
        $this->currentJabatan = $pegawai->jbtn ?? '-';

        $this->keluhan = $visite->keluhan ?? '';
        $this->pemeriksaan = $visite->pemeriksaan ?? '';
        $this->suhu_tubuh = $visite->suhu_tubuh ?? '';
        $this->tensi = $visite->tensi ?? '';
        $this->berat = $visite->berat ?? '';
        $this->tinggi = $visite->tinggi ?? '';
        $this->respirasi = $visite->respirasi ?? '';
        $this->nadi = $visite->nadi ?? '';
        $this->spo2 = $visite->spo2 ?? '';
        $this->gcs = $visite->gcs ?? '';
        $this->kesadaran = $visite->kesadaran ?? 'Compos Mentis';
        $this->alergi = $visite->alergi ?? '';
        $this->penilaian = $visite->penilaian ?? '';
        $this->rtl = $visite->rtl ?? '';
        $this->instruksi = $visite->instruksi ?? '';
        $this->evaluasi = $visite->evaluasi ?? '';
        $this->riwayat_pengobatan = $visite->riwayat_pengobatan ?? '';
        $this->interaksi_obat = $visite->interaksi_obat ?? '';

        $this->detailModalOpen = false;
        $this->isPanelOpen = true;
        $this->formModalOpen = true;
        $this->dispatch('set-autotime', ['status' => false]);
    }

    public function save(): void
    {
        $this->validate([
            'tgl_visite' => 'required|date',
            'jam_visite' => 'required',
            'nip' => 'required|string',
        ], [
            'tgl_visite.required' => 'Tanggal visite wajib diisi.',
            'jam_visite.required' => 'Jam visite wajib diisi.',
            'nip.required' => 'Petugas / pemeriksa wajib dipilih.',
        ]);

        $data = [
            'no_rawat' => $this->no_rawat,
            'tgl_visite' => $this->tgl_visite,
            'jam_visite' => $this->jam_visite,
            'nip' => $this->nip,
            'keluhan' => $this->keluhan,
            'pemeriksaan' => $this->pemeriksaan,
            'suhu_tubuh' => $this->suhu_tubuh,
            'tensi' => $this->tensi,
            'berat' => $this->berat,
            'tinggi' => $this->tinggi,
            'respirasi' => $this->respirasi,
            'nadi' => $this->nadi,
            'spo2' => $this->spo2,
            'gcs' => $this->gcs,
            'kesadaran' => $this->kesadaran ?: 'Compos Mentis',
            'alergi' => $this->alergi,
            'penilaian' => $this->penilaian,
            'rtl' => $this->rtl,
            'instruksi' => $this->instruksi,
            'evaluasi' => $this->evaluasi,
            'riwayat_pengobatan' => $this->riwayat_pengobatan,
            'interaksi_obat' => $this->interaksi_obat,
        ];

        if ($this->isEditMode && $this->selectedId) {
            $visite = Visite::findOrFail($this->selectedId);
            $visite->update($data);
            $message = 'Data visite berhasil diperbarui.';
        } else {
            Visite::create($data);
            $message = 'Data visite berhasil disimpan.';
        }

        $this->isPanelOpen = false;
        $this->formModalOpen = false;
        $this->loadLastReference();

        $this->dispatch('swal', [
            'title' => 'Berhasil!',
            'text' => $message,
            'icon' => 'success'
        ]);
    }

    public function deleteVisite(int $id): void
    {
        $visite = Visite::findOrFail($id);
        $visite->delete();

        $this->detailModalOpen = false;
        $this->loadLastReference();

        $this->dispatch('swal', [
            'title' => 'Terhapus!',
            'text' => 'Data visite berhasil dihapus.',
            'icon' => 'success'
        ]);
    }

    public function showDetail(int $id): void
    {
        $visite = Visite::with('pegawai')->findOrFail($id);

        $this->activeDetail = [
            'id' => $visite->id,
            'no_rawat' => $visite->no_rawat,
            'no_rkm_medis' => $this->regPeriksa->no_rkm_medis ?? '-',
            'nm_pasien' => $this->regPeriksa->pasien->nm_pasien ?? '-',
            'tgl_visite' => $visite->tgl_visite,
            'jam_visite' => $visite->jam_visite,
            'nip' => $visite->nip,
            'nm_pegawai' => $visite->pegawai->nama ?? $visite->nip,
            'jbtn' => $visite->pegawai->jbtn ?? '-',
            'suhu_tubuh' => $visite->suhu_tubuh ?: '-',
            'tensi' => $visite->tensi ?: '-',
            'nadi' => $visite->nadi ?: '-',
            'respirasi' => $visite->respirasi ?: '-',
            'tinggi' => $visite->tinggi ?: '-',
            'berat' => $visite->berat ?: '-',
            'spo2' => $visite->spo2 ?: '-',
            'gcs' => $visite->gcs ?: '-',
            'kesadaran' => $visite->kesadaran ?: 'Compos Mentis',
            'alergi' => $visite->alergi ?: '-',
            'keluhan' => $visite->keluhan ?: '-',
            'pemeriksaan' => $visite->pemeriksaan ?: '-',
            'penilaian' => $visite->penilaian ?: '-',
            'rtl' => $visite->rtl ?: '-',
            'instruksi' => $visite->instruksi ?: '-',
            'evaluasi' => $visite->evaluasi ?: '-',
            'riwayat_pengobatan' => $visite->riwayat_pengobatan ?: '-',
            'interaksi_obat' => $visite->interaksi_obat ?: '-',
        ];

        $this->detailModalOpen = true;
    }

    public function render()
    {
        $pegawaiList = collect([]);
        if (strlen($this->pegawaiSearch) >= 3) {
            $pegawaiList = Pegawai::where('nama', 'like', '%' . $this->pegawaiSearch . '%')
                ->orWhere('nik', 'like', '%' . $this->pegawaiSearch . '%')
                ->limit(10)
                ->get();
        }

        $visiteList = Visite::with('pegawai')
            ->where('no_rawat', $this->no_rawat)
            ->orderBy('tgl_visite', 'desc')
            ->orderBy('jam_visite', 'desc')
            ->paginate(10);

        return view('livewire.modul.farmasi.visite-patient', [
            'visiteList' => $visiteList,
            'pegawaiList' => $pegawaiList,
        ]);
    }
}
