<?php

namespace App\Livewire\Modul\RawatJalan\SubRawatJalan\PengkajianAwalIgd;

use Livewire\Component;
use App\Models\RegPeriksa;
use App\Livewire\Concerns\WithOptimisticLocking;
use App\Repositories\RawatJalan\PengkajianAwalIgdRepository;

class Index extends Component
{
    use WithOptimisticLocking;

    public $noRawat;
    public $regPeriksa;
    public $isEditMode = false;

    // Petugas
    public $nip, $nmPetugas, $petugasSearch = '';

    // Section 1: Identitas
    public $tanggal, $informasi = 'Autoanamnesis';

    // Section 2: Riwayat Kesehatan
    public $keluhan_utama = '', $rpd = '', $rpo = '';
    public $status_kehamilan = 'Tidak Hamil', $hpht = '', $para = '', $abortus = '', $gravida = '';

    // Section 3: Pemeriksaan Fisik
    public $tekanan = 'TAK', $pupil = 'Normal', $neurosensorik = 'TAK';
    public $integumen = 'TAK', $turgor = 'Baik', $edema = 'Tidak Ada', $mukosa = 'Lembab';
    public $perdarahan = 'Tidak Ada', $jumlah_perdarahan = '', $warna_perdarahan = '';
    public $intoksikasi = 'Tidak Ada';
    public $bab = '', $xbab = '', $kbab = '', $wbab = '';
    public $bak = '', $xbak = '', $wbak = '', $lbak = '';

    // Section 4: Psikososial & Fungsional
    public $psikologis = 'Tidak Ada Masalah', $jiwa = 'Tidak';
    public $perilaku = 'Perilaku Kekerasan', $dilaporkan = '', $sebutkan = '';
    public $hubungan = 'Harmonis', $tinggal_dengan = 'Sendiri', $ket_tinggal = '';
    public $budaya = 'Tidak Ada', $ket_budaya = '';
    public $pendidikan_pj = '-', $ket_pendidikan_pj = '';
    public $edukasi = 'Pasien', $ket_edukasi = '';
    public $kemampuan = 'Mandiri', $aktifitas = 'Tirah Baring';
    public $alat_bantu = 'Tidak', $ket_bantu = '';

    // Section 5: Skala Nyeri
    public $nyeri = 'Tidak Ada Nyeri', $provokes = 'Proses Penyakit', $ket_provokes = '';
    public $quality = 'Seperti Tertusuk', $ket_quality = '', $lokasi = '';
    public $menyebar = 'Tidak', $skala_nyeri = '0', $durasi = '';
    public $nyeri_hilang = 'Istirahat', $ket_nyeri = '';
    public $pada_dokter = 'Tidak', $ket_dokter = '';

    // Section 6: Risiko Jatuh
    public $berjalan_a = 'Tidak', $berjalan_b = 'Tidak', $berjalan_c = 'Tidak';
    public $hasil = 'Tidak beresiko (tidak ditemukan a dan b)';
    public $lapor = 'Tidak', $ket_lapor = '';

    // Section 6: Masalah & Rencana Keperawatan
    public $selectedMasalah = [];
    public $selectedRencana = [];
    public $rencana = ''; // Rencana Lainnya (free text)

    // Master data
    public $masterMasalah = [];
    public $availableRencana = [];

    public function mount($no_rawat, PengkajianAwalIgdRepository $repository)
    {
        $this->noRawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'poliklinik'])
            ->where('no_rawat', $this->noRawat)
            ->firstOrFail();

        $this->initializeLock($this->regPeriksa);
        $this->tanggal = now()->format('Y-m-d H:i:s');

        // Load master data
        $this->masterMasalah = $repository->getMasterMasalah()->toArray();

        // Load existing data if exists
        $existing = $repository->getByNoRawat($this->noRawat);
        if ($existing) {
            $this->isEditMode = true;
            $this->fillFromExisting($existing);
        }
    }

    private function fillFromExisting($data)
    {
        $this->tanggal = $data->tanggal;
        $this->informasi = $data->informasi;
        $this->keluhan_utama = $data->keluhan_utama;
        $this->rpd = $data->rpd;
        $this->rpo = $data->rpo;
        $this->status_kehamilan = $data->status_kehamilan;
        $this->hpht = $data->hpht ?? '';
        $this->para = $data->para ?? '';
        $this->abortus = $data->abortus ?? '';
        $this->gravida = $data->gravida ?? '';
        $this->tekanan = $data->tekanan;
        $this->pupil = $data->pupil;
        $this->neurosensorik = $data->neurosensorik;
        $this->integumen = $data->integumen;
        $this->turgor = $data->turgor;
        $this->edema = $data->edema;
        $this->mukosa = $data->mukosa;
        $this->perdarahan = $data->perdarahan;
        $this->jumlah_perdarahan = $data->jumlah_perdarahan ?? '';
        $this->warna_perdarahan = $data->warna_perdarahan ?? '';
        $this->intoksikasi = $data->intoksikasi;
        $this->bab = $data->bab ?? '';
        $this->xbab = $data->xbab ?? '';
        $this->kbab = $data->kbab ?? '';
        $this->wbab = $data->wbab ?? '';
        $this->bak = $data->bak ?? '';
        $this->xbak = $data->xbak ?? '';
        $this->wbak = $data->wbak ?? '';
        $this->lbak = $data->lbak ?? '';
        $this->psikologis = $data->psikologis;
        $this->jiwa = $data->jiwa;
        $this->perilaku = $data->perilaku;
        $this->dilaporkan = $data->dilaporkan ?? '';
        $this->sebutkan = $data->sebutkan ?? '';
        $this->hubungan = $data->hubungan;
        $this->tinggal_dengan = $data->tinggal_dengan;
        $this->ket_tinggal = $data->ket_tinggal ?? '';
        $this->budaya = $data->budaya;
        $this->ket_budaya = $data->ket_budaya ?? '';
        $this->pendidikan_pj = $data->pendidikan_pj;
        $this->ket_pendidikan_pj = $data->ket_pendidikan_pj ?? '';
        $this->edukasi = $data->edukasi;
        $this->ket_edukasi = $data->ket_edukasi ?? '';
        $this->kemampuan = $data->kemampuan;
        $this->aktifitas = $data->aktifitas;
        $this->alat_bantu = $data->alat_bantu;
        $this->ket_bantu = $data->ket_bantu ?? '';
        $this->nyeri = $data->nyeri;
        $this->provokes = $data->provokes;
        $this->ket_provokes = $data->ket_provokes ?? '';
        $this->quality = $data->quality;
        $this->ket_quality = $data->ket_quality ?? '';
        $this->lokasi = $data->lokasi ?? '';
        $this->menyebar = $data->menyebar;
        $this->skala_nyeri = $data->skala_nyeri;
        $this->durasi = $data->durasi ?? '';
        $this->nyeri_hilang = $data->nyeri_hilang;
        $this->ket_nyeri = $data->ket_nyeri ?? '';
        $this->pada_dokter = $data->pada_dokter;
        $this->ket_dokter = $data->ket_dokter ?? '';
        $this->berjalan_a = $data->berjalan_a;
        $this->berjalan_b = $data->berjalan_b;
        $this->berjalan_c = $data->berjalan_c;
        $this->hasil = $data->hasil;
        $this->lapor = $data->lapor;
        $this->ket_lapor = $data->ket_lapor ?? '';
        $this->rencana = $data->rencana ?? '';
        $this->nip = $data->nip;
        $this->nmPetugas = $data->petugas->nama ?? '';

        // Load selected masalah
        $this->selectedMasalah = $data->masalah->pluck('kode_masalah')->toArray();

        // Load selected rencana
        $this->selectedRencana = $data->rencana->pluck('kode_rencana')->toArray();

        // Load available rencana based on selected masalah
        $this->loadAvailableRencana();
    }

    public function updatedSelectedMasalah()
    {
        $this->loadAvailableRencana();
    }

    private function loadAvailableRencana()
    {
        if (empty($this->selectedMasalah)) {
            $this->availableRencana = [];
            return;
        }

        $repo = new PengkajianAwalIgdRepository();
        $this->availableRencana = $repo->getRencanaByMasalah($this->selectedMasalah)->toArray();
    }

    public function selectPetugas($nip, $nama)
    {
        $this->nip = $nip;
        $this->nmPetugas = $nama;
        $this->petugasSearch = '';
    }

    public function save(PengkajianAwalIgdRepository $repository)
    {
        $this->validate([
            'tanggal' => 'required',
            'informasi' => 'required',
            'nip' => 'required',
        ], [
            'nip.required' => 'Petugas harus dipilih.',
        ]);

        $this->validateLock($this->regPeriksa->fresh());

        try {
            $data = [
                'no_rawat' => $this->noRawat,
                'tanggal' => $this->tanggal,
                'informasi' => $this->informasi,
                'keluhan_utama' => $this->keluhan_utama ?: '-',
                'rpd' => $this->rpd ?: '-',
                'rpo' => $this->rpo ?: '-',
                'status_kehamilan' => $this->status_kehamilan,
                'gravida' => $this->gravida,
                'para' => $this->para,
                'abortus' => $this->abortus,
                'hpht' => $this->hpht,
                'tekanan' => $this->tekanan,
                'pupil' => $this->pupil,
                'neurosensorik' => $this->neurosensorik,
                'integumen' => $this->integumen,
                'turgor' => $this->turgor,
                'edema' => $this->edema,
                'mukosa' => $this->mukosa,
                'perdarahan' => $this->perdarahan,
                'jumlah_perdarahan' => $this->jumlah_perdarahan,
                'warna_perdarahan' => $this->warna_perdarahan,
                'intoksikasi' => $this->intoksikasi,
                'bab' => $this->bab,
                'xbab' => $this->xbab,
                'kbab' => $this->kbab,
                'wbab' => $this->wbab,
                'bak' => $this->bak,
                'xbak' => $this->xbak,
                'wbak' => $this->wbak,
                'lbak' => $this->lbak,
                'psikologis' => $this->psikologis,
                'jiwa' => $this->jiwa,
                'perilaku' => $this->perilaku,
                'dilaporkan' => $this->dilaporkan,
                'sebutkan' => $this->sebutkan,
                'hubungan' => $this->hubungan,
                'tinggal_dengan' => $this->tinggal_dengan,
                'ket_tinggal' => $this->ket_tinggal,
                'budaya' => $this->budaya,
                'ket_budaya' => $this->ket_budaya,
                'pendidikan_pj' => $this->pendidikan_pj,
                'ket_pendidikan_pj' => $this->ket_pendidikan_pj,
                'edukasi' => $this->edukasi,
                'ket_edukasi' => $this->ket_edukasi,
                'kemampuan' => $this->kemampuan,
                'aktifitas' => $this->aktifitas,
                'alat_bantu' => $this->alat_bantu,
                'ket_bantu' => $this->ket_bantu,
                'nyeri' => $this->nyeri,
                'provokes' => $this->provokes,
                'ket_provokes' => $this->ket_provokes,
                'quality' => $this->quality,
                'ket_quality' => $this->ket_quality,
                'lokasi' => $this->lokasi,
                'menyebar' => $this->menyebar,
                'skala_nyeri' => $this->skala_nyeri,
                'durasi' => $this->durasi,
                'nyeri_hilang' => $this->nyeri_hilang,
                'ket_nyeri' => $this->ket_nyeri,
                'pada_dokter' => $this->pada_dokter,
                'ket_dokter' => $this->ket_dokter,
                'berjalan_a' => $this->berjalan_a,
                'berjalan_b' => $this->berjalan_b,
                'berjalan_c' => $this->berjalan_c,
                'hasil' => $this->hasil,
                'lapor' => $this->lapor,
                'ket_lapor' => $this->ket_lapor,
                'rencana' => $this->rencana,
                'nip' => $this->nip,
            ];

            if ($this->isEditMode) {
                $repository->update($this->noRawat, collect($data)->except('no_rawat')->toArray(), $this->selectedMasalah, $this->selectedRencana);
                $msg = 'Pengkajian Awal Keperawatan IGD berhasil diperbarui.';
            } else {
                $repository->store($data, $this->selectedMasalah, $this->selectedRencana);
                $this->isEditMode = true;
                $msg = 'Pengkajian Awal Keperawatan IGD berhasil disimpan.';
            }

            $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => $msg, 'icon' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal!', 'text' => 'Data gagal disimpan. Pastikan semua isian sudah benar.', 'icon' => 'error']);
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

        return view('livewire.modul.rawat-jalan.sub-rawat-jalan.pengkajian-awal-igd.index', [
            'petugasList' => $petugasList,
        ])->layout('layouts.app', ['title' => 'Pengkajian Awal Keperawatan IGD']);
    }
}
