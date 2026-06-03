<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\ResumePasien;

use App\Models\RegPeriksa;
use App\Models\ResumePasienRanap;
use App\Models\PemeriksaanRanap;
use App\Models\Penyakit;
use App\Models\Icd9;
use App\Livewire\Concerns\WithOptimisticLocking;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Edit Resume Medis'])]
class Edit extends Component
{
    use WithPagination, WithOptimisticLocking;

    public string $no_rawat;
    public $regPeriksa;
    public $resume;

    // Form Fields
    public $kd_dokter;
    public $diagnosa_awal;
    public $alasan;
    public $keluhan_utama;
    public $pemeriksaan_fisik;
    public $jalannya_penyakit;
    public $pemeriksaan_penunjang;
    public $hasil_laborat;
    public $tindakan_dan_operasi;
    public $obat_di_rs;

    public $diagnosa_utama, $kd_diagnosa_utama;
    public $diagnosa_sekunder, $kd_diagnosa_sekunder;
    public $diagnosa_sekunder2, $kd_diagnosa_sekunder2;
    public $diagnosa_sekunder3, $kd_diagnosa_sekunder3;
    public $diagnosa_sekunder4, $kd_diagnosa_sekunder4;

    public $prosedur_utama, $kd_prosedur_utama;
    public $prosedur_sekunder, $kd_prosedur_sekunder;
    public $prosedur_sekunder2, $kd_prosedur_sekunder2;
    public $prosedur_sekunder3, $kd_prosedur_sekunder3;

    public $alergi;
    public $diet;
    public $lab_belum;
    public $edukasi;
    public $keadaan, $ket_keadaan;
    public $cara_keluar, $ket_keluar;
    public $dilanjutkan, $ket_dilanjutkan;
    public $kontrol;
    public $obat_pulang;

    // Search/Lookup State
    public $searchIcd10 = '';
    public $searchIcd9 = '';
    public $targetIcdField = '';

    // Autocomplete State
    public $activeSearchField = '';
    public $autocompleteResults = [];
    protected $isSelecting = false;

    // Attach/Select State (Alpine.js Modal)
    public $selectedKeluhan = [];
    public $selectedLab = [];
    public $selectedTindakan = [];
    public $selectedObat = [];
    public $targetAttachField = 'keluhan_utama';
    public $targetAttachColumn = 'keluhan';

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with([
            'pasien', 'dokter', 'kamarInap.kamar.bangsal',
            'diagnosaPasien.penyakit',
            'detailPeriksaLab.template',
            'pemeriksaanRanap',
            'rawatInapDr.jnsPerawatan',
            'rawatInapPr.jnsPerawatan',
            'rawatInapDrpr.jnsPerawatan',
            'detailPemberianObat.barang',
        ])->findOrFail($this->no_rawat);

        $this->resume = ResumePasienRanap::where('no_rawat', $this->no_rawat)->firstOrFail();

        // Load existing resume data
        $this->kd_dokter            = $this->resume->kd_dokter;
        $this->diagnosa_awal        = $this->resume->diagnosa_awal;
        $this->alasan               = $this->resume->alasan;
        $this->keluhan_utama        = $this->resume->keluhan_utama;
        $this->pemeriksaan_fisik    = $this->resume->pemeriksaan_fisik;
        $this->jalannya_penyakit    = $this->resume->jalannya_penyakit;
        $this->pemeriksaan_penunjang = $this->resume->pemeriksaan_penunjang;
        $this->hasil_laborat        = $this->resume->hasil_laborat;
        $this->tindakan_dan_operasi = $this->resume->tindakan_dan_operasi;
        $this->obat_di_rs           = $this->resume->obat_di_rs;

        $this->diagnosa_utama        = $this->resume->diagnosa_utama;
        $this->kd_diagnosa_utama     = $this->resume->kd_diagnosa_utama;
        $this->diagnosa_sekunder     = $this->resume->diagnosa_sekunder;
        $this->kd_diagnosa_sekunder  = $this->resume->kd_diagnosa_sekunder;
        $this->diagnosa_sekunder2    = $this->resume->diagnosa_sekunder2;
        $this->kd_diagnosa_sekunder2 = $this->resume->kd_diagnosa_sekunder2;
        $this->diagnosa_sekunder3    = $this->resume->diagnosa_sekunder3;
        $this->kd_diagnosa_sekunder3 = $this->resume->kd_diagnosa_sekunder3;
        $this->diagnosa_sekunder4    = $this->resume->diagnosa_sekunder4;
        $this->kd_diagnosa_sekunder4 = $this->resume->kd_diagnosa_sekunder4;

        $this->prosedur_utama        = $this->resume->prosedur_utama;
        $this->kd_prosedur_utama     = $this->resume->kd_prosedur_utama;
        $this->prosedur_sekunder     = $this->resume->prosedur_sekunder;
        $this->kd_prosedur_sekunder  = $this->resume->kd_prosedur_sekunder;
        $this->prosedur_sekunder2    = $this->resume->prosedur_sekunder2;
        $this->kd_prosedur_sekunder2 = $this->resume->kd_prosedur_sekunder2;
        $this->prosedur_sekunder3    = $this->resume->prosedur_sekunder3;
        $this->kd_prosedur_sekunder3 = $this->resume->kd_prosedur_sekunder3;

        $this->alergi          = $this->resume->alergi;
        $this->diet            = $this->resume->diet;
        $this->lab_belum       = $this->resume->lab_belum;
        $this->edukasi         = $this->resume->edukasi;
        $this->keadaan         = $this->resume->keadaan;
        $this->ket_keadaan     = $this->resume->ket_keadaan;
        $this->cara_keluar     = $this->resume->cara_keluar;
        $this->ket_keluar      = $this->resume->ket_keluar;
        $this->dilanjutkan     = $this->resume->dilanjutkan;
        $this->ket_dilanjutkan = $this->resume->ket_dilanjutkan;
        $this->kontrol         = $this->resume->kontrol;
        $this->obat_pulang     = $this->resume->obat_pulang;

        // SOP: Initialize lock
        $this->initializeLock($this->resume);
    }

    // ─────────────────────────────────────────────
    // Auto-Fill Methods
    // ─────────────────────────────────────────────

    public function autoFillSOAP($field, $column)
    {
        $allData = collect($this->regPeriksa->pemeriksaanRanap)
            ->sortBy('tgl_perawatan')
            ->map(fn($p) => $p->$column ?? null)
            ->filter(fn($val) => !empty($val) && $val !== '-')
            ->unique()->implode(', ');

        if ($allData) {
            $this->$field = $allData;
            $this->dispatch('swal', ['title' => 'Otomatis Terisi', 'text' => 'Data riwayat telah dimasukkan.', 'icon' => 'success', 'timer' => 1000]);
        } else {
            $this->dispatch('swal', ['title' => 'Data Kosong', 'text' => 'Tidak ada data riwayat SOAP ditemukan.', 'icon' => 'info']);
        }
    }

    public function autoFillTindakan()
    {
        $allTindakan = collect($this->regPeriksa->rawatInapDr)
            ->concat($this->regPeriksa->rawatInapPr)
            ->concat($this->regPeriksa->rawatInapDrpr)
            ->map(fn($t) => $t->jnsPerawatan->nm_perawatan ?? null)
            ->filter()->unique()->implode(', ');

        if ($allTindakan) {
            $this->tindakan_dan_operasi = $allTindakan;
            $this->dispatch('swal', ['title' => 'Otomatis Terisi', 'text' => 'Semua tindakan telah dimasukkan.', 'icon' => 'success', 'timer' => 1000]);
        } else {
            $this->dispatch('swal', ['title' => 'Data Kosong', 'text' => 'Tidak ada riwayat tindakan ditemukan.', 'icon' => 'info']);
        }
    }

    public function autoFillObat()
    {
        $allObat = collect($this->regPeriksa->detailPemberianObat)
            ->map(fn($o) => $o->barang->nama_brng ?? null)
            ->filter()->unique()->implode(', ');

        if ($allObat) {
            $this->obat_di_rs = $allObat;
            $this->dispatch('swal', ['title' => 'Otomatis Terisi', 'text' => 'Semua riwayat pemberian obat telah dimasukkan.', 'icon' => 'success', 'timer' => 1000]);
        } else {
            $this->dispatch('swal', ['title' => 'Data Kosong', 'text' => 'Tidak ada riwayat pemberian obat ditemukan.', 'icon' => 'info']);
        }
    }

    // ─────────────────────────────────────────────
    // Attach Methods
    // ─────────────────────────────────────────────

    public function prepareAttach($field, $column)
    {
        $this->targetAttachField  = $field;
        $this->targetAttachColumn = $column;
        $this->selectedKeluhan = $this->selectedLab = $this->selectedTindakan = $this->selectedObat = [];
    }

    public function toggleSelectAll()
    {
        if ($this->targetAttachColumn == 'lab_hasil') {
            $this->selectedLab = count($this->selectedLab) === count($this->regPeriksa->detailPeriksaLab)
                ? [] : $this->regPeriksa->detailPeriksaLab->map(fn($l) => "{$l->tgl_periksa}|{$l->jam}|{$l->kd_jenis_prw}|{$l->id_template}")->toArray();
        } elseif ($this->targetAttachColumn == 'tindakan') {
            $all = collect($this->regPeriksa->rawatInapDr)->concat($this->regPeriksa->rawatInapPr)->concat($this->regPeriksa->rawatInapDrpr);
            $this->selectedTindakan = count($this->selectedTindakan) === $all->count()
                ? [] : $all->map(fn($t) => "{$t->tgl_perawatan}|{$t->jam_rawat}|{$t->kd_jenis_prw}")->toArray();
        } elseif ($this->targetAttachColumn == 'obat') {
            $this->selectedObat = count($this->selectedObat) === count($this->regPeriksa->detailPemberianObat)
                ? [] : $this->regPeriksa->detailPemberianObat->map(fn($o) => "{$o->tgl_perawatan}|{$o->jam}|{$o->kode_brng}")->toArray();
        } else {
            $this->selectedKeluhan = count($this->selectedKeluhan) === count($this->regPeriksa->pemeriksaanRanap)
                ? [] : $this->regPeriksa->pemeriksaanRanap->map(fn($p) => "{$p->tgl_perawatan}|{$p->jam_rawat}")->toArray();
        }
    }

    public function attachKeluhan()
    {
        if (empty($this->selectedKeluhan)) return;
        $texts = [];
        foreach ($this->selectedKeluhan as $key) {
            [$tgl, $jam] = explode('|', $key);
            $p = PemeriksaanRanap::where('no_rawat', $this->no_rawat)->where('tgl_perawatan', $tgl)->where('jam_rawat', $jam)->first();
            $col = $this->targetAttachColumn;
            if ($p && !empty($p->$col) && $p->$col !== '-') $texts[] = $p->$col;
        }
        $this->applyAttachments($texts);
        $this->selectedKeluhan = [];
    }

    public function attachLab()
    {
        if (empty($this->selectedLab)) return;
        $texts = [];
        foreach ($this->selectedLab as $key) {
            [$tgl, $jam, $kd, $id] = explode('|', $key);
            $lab = \App\Models\DetailPeriksaLab::with('template')->where('no_rawat', $this->no_rawat)->where('tgl_periksa', $tgl)->where('jam', $jam)->where('kd_jenis_prw', $kd)->where('id_template', $id)->first();
            if ($lab) $texts[] = ($lab->template->Pemeriksaan ?? '-') . ' : ' . ($lab->nilai ?? '');
        }
        $this->applyAttachments($texts);
        $this->selectedLab = [];
    }

    public function attachTindakan()
    {
        if (empty($this->selectedTindakan)) return;
        $texts = [];
        foreach ($this->selectedTindakan as $id) {
            [$tgl, $jam, $kd] = explode('|', $id);
            $item = \App\Models\RawatInapDr::where(['no_rawat' => $this->no_rawat, 'tgl_perawatan' => $tgl, 'jam_rawat' => $jam, 'kd_jenis_prw' => $kd])->first()
                ?? \App\Models\RawatInapPr::where(['no_rawat' => $this->no_rawat, 'tgl_perawatan' => $tgl, 'jam_rawat' => $jam, 'kd_jenis_prw' => $kd])->first()
                ?? \App\Models\RawatInapDrpr::where(['no_rawat' => $this->no_rawat, 'tgl_perawatan' => $tgl, 'jam_rawat' => $jam, 'kd_jenis_prw' => $kd])->first();
            if ($item && $item->jnsPerawatan) $texts[] = $item->jnsPerawatan->nm_perawatan;
        }
        $this->applyAttachments($texts);
        $this->selectedTindakan = [];
    }

    public function attachObat()
    {
        if (empty($this->selectedObat)) return;
        $texts = [];
        foreach ($this->selectedObat as $id) {
            [$tgl, $jam, $kode] = explode('|', $id);
            $o = \App\Models\DetailPemberianObat::with('barang')->where('no_rawat', $this->no_rawat)->where('tgl_perawatan', $tgl)->where('jam', $jam)->where('kode_brng', $kode)->first();
            if ($o && $o->barang) $texts[] = $o->barang->nama_brng;
        }
        $this->applyAttachments($texts);
        $this->selectedObat = [];
    }

    private function applyAttachments($texts)
    {
        if (!empty($texts)) {
            $joined = implode(', ', array_unique($texts));
            $field = $this->targetAttachField;
            $this->$field = empty($this->$field) ? $joined : $this->$field . ', ' . $joined;
        }
    }

    public function refreshData()
    {
        $this->regPeriksa = RegPeriksa::with([
            'pasien', 'dokter', 'kamarInap.kamar.bangsal',
            'diagnosaPasien.penyakit', 'detailPeriksaLab.template',
            'pemeriksaanRanap', 'rawatInapDr.jnsPerawatan',
            'rawatInapPr.jnsPerawatan', 'rawatInapDrpr.jnsPerawatan',
            'detailPemberianObat.barang',
        ])->findOrFail($this->no_rawat);
        $this->dispatch('swal', ['title' => 'Data Diperbarui', 'icon' => 'success', 'timer' => 1000, 'showConfirmButton' => false]);
    }

    // ─────────────────────────────────────────────
    // ICD Autocomplete
    // ─────────────────────────────────────────────

    public function selectIcd10($code, $name)
    {
        $this->{'kd_' . $this->targetIcdField} = $code;
        $this->{$this->targetIcdField} = $name;
        $this->dispatch('close-modal', 'icd10-modal');
    }

    public function selectIcd9($code, $name)
    {
        $this->{'kd_' . $this->targetIcdField} = $code;
        $this->{$this->targetIcdField} = $name;
        $this->dispatch('close-modal', 'icd9-modal');
    }

    public function updated($propertyName)
    {
        if ($this->isSelecting) return;

        $icd10 = ['diagnosa_utama', 'diagnosa_sekunder', 'diagnosa_sekunder2', 'diagnosa_sekunder3', 'diagnosa_sekunder4'];
        $icd9  = ['prosedur_utama', 'prosedur_sekunder', 'prosedur_sekunder2', 'prosedur_sekunder3'];

        if (in_array($propertyName, $icd10)) {
            $this->activeSearchField = $propertyName;
            $v = $this->$propertyName;
            $this->autocompleteResults = strlen($v) >= 3
                ? Penyakit::where('kd_penyakit', 'like', "%$v%")->orWhere('nm_penyakit', 'like', "%$v%")->limit(10)->get()->toArray() : [];
        } elseif (in_array($propertyName, $icd9)) {
            $this->activeSearchField = $propertyName;
            $v = $this->$propertyName;
            $this->autocompleteResults = strlen($v) >= 3
                ? Icd9::where('kode', 'like', "%$v%")->orWhere('deskripsi_panjang', 'like', "%$v%")->limit(10)->get()->toArray() : [];
        }
    }

    public function selectAutocompleteItem($code, $name)
    {
        $this->isSelecting = true;
        $this->{'kd_' . $this->activeSearchField} = $code;
        $this->{$this->activeSearchField} = $name;
        $this->clearAutocomplete();
        $this->isSelecting = false;
    }

    public function clearAutocomplete()
    {
        $this->activeSearchField   = '';
        $this->autocompleteResults = [];
    }

    // ─────────────────────────────────────────────
    // Save & Delete
    // ─────────────────────────────────────────────

    public function save()
    {
        $this->validate(['kd_dokter' => 'required', 'diagnosa_utama' => 'required']);
        $this->validateLock($this->resume->fresh());

        try {
            $this->resume->update([
                'kd_dokter'             => $this->kd_dokter ?? '-',
                'diagnosa_awal'         => $this->defaultEmpty($this->diagnosa_awal),
                'alasan'                => $this->defaultEmpty($this->alasan),
                'keluhan_utama'         => $this->defaultEmpty($this->keluhan_utama),
                'pemeriksaan_fisik'     => $this->defaultEmpty($this->pemeriksaan_fisik),
                'jalannya_penyakit'     => $this->defaultEmpty($this->jalannya_penyakit),
                'pemeriksaan_penunjang' => $this->defaultEmpty($this->pemeriksaan_penunjang),
                'hasil_laborat'         => $this->defaultEmpty($this->hasil_laborat),
                'tindakan_dan_operasi'  => $this->defaultEmpty($this->tindakan_dan_operasi),
                'obat_di_rs'            => $this->defaultEmpty($this->obat_di_rs),
                'diagnosa_utama'        => $this->defaultEmpty($this->diagnosa_utama),
                'kd_diagnosa_utama'     => $this->defaultEmpty($this->kd_diagnosa_utama),
                'diagnosa_sekunder'     => $this->defaultEmpty($this->diagnosa_sekunder),
                'kd_diagnosa_sekunder'  => $this->defaultEmpty($this->kd_diagnosa_sekunder),
                'diagnosa_sekunder2'    => $this->defaultEmpty($this->diagnosa_sekunder2),
                'kd_diagnosa_sekunder2' => $this->defaultEmpty($this->kd_diagnosa_sekunder2),
                'diagnosa_sekunder3'    => $this->defaultEmpty($this->diagnosa_sekunder3),
                'kd_diagnosa_sekunder3' => $this->defaultEmpty($this->kd_diagnosa_sekunder3),
                'diagnosa_sekunder4'    => $this->defaultEmpty($this->diagnosa_sekunder4),
                'kd_diagnosa_sekunder4' => $this->defaultEmpty($this->kd_diagnosa_sekunder4),
                'prosedur_utama'        => $this->defaultEmpty($this->prosedur_utama),
                'kd_prosedur_utama'     => $this->defaultEmpty($this->kd_prosedur_utama),
                'prosedur_sekunder'     => $this->defaultEmpty($this->prosedur_sekunder),
                'kd_prosedur_sekunder'  => $this->defaultEmpty($this->kd_prosedur_sekunder),
                'prosedur_sekunder2'    => $this->defaultEmpty($this->prosedur_sekunder2),
                'kd_prosedur_sekunder2' => $this->defaultEmpty($this->kd_prosedur_sekunder2),
                'prosedur_sekunder3'    => $this->defaultEmpty($this->prosedur_sekunder3),
                'kd_prosedur_sekunder3' => $this->defaultEmpty($this->kd_prosedur_sekunder3),
                'alergi'                => $this->defaultEmpty($this->alergi),
                'diet'                  => $this->defaultEmpty($this->diet),
                'lab_belum'             => $this->defaultEmpty($this->lab_belum),
                'edukasi'               => $this->defaultEmpty($this->edukasi),
                'cara_keluar'           => $this->defaultEmpty($this->cara_keluar),
                'ket_keluar'            => $this->defaultEmpty($this->ket_keluar),
                'keadaan'               => $this->defaultEmpty($this->keadaan),
                'ket_keadaan'           => $this->defaultEmpty($this->ket_keadaan),
                'dilanjutkan'           => $this->defaultEmpty($this->dilanjutkan),
                'ket_dilanjutkan'       => $this->defaultEmpty($this->ket_dilanjutkan),
                'kontrol'               => $this->kontrol ?? null,
                'obat_pulang'           => $this->defaultEmpty($this->obat_pulang),
            ]);

            $this->dispatch('swal', ['title' => 'Sukses!', 'text' => 'Resume medis berhasil diupdate.', 'icon' => 'success']);
            return $this->redirect(route('modul.rawat-inap.sub-rawat-inap.resume', str_replace('/', '-', $this->no_rawat)), navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal Menyimpan', 'text' => $e->getMessage(), 'icon' => 'error']);
        }
    }

    private function defaultEmpty($value)
    {
        return empty($value) ? '-' : $value;
    }

    public function delete()
    {
        $this->validateLock($this->resume->fresh());
        try {
            $this->resume->delete();
            return $this->redirect(route('modul.rawat-inap.sub-rawat-inap.resume', str_replace('/', '-', $this->no_rawat)), navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal Menghapus', 'text' => $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function render()
    {
        $icd10List = strlen($this->searchIcd10) >= 3
            ? Penyakit::where('kd_penyakit', 'like', '%' . $this->searchIcd10 . '%')->orWhere('nm_penyakit', 'like', '%' . $this->searchIcd10 . '%')->limit(20)->get()
            : [];
        $icd9List = strlen($this->searchIcd9) >= 3
            ? Icd9::where('kode', 'like', '%' . $this->searchIcd9 . '%')->orWhere('deskripsi_panjang', 'like', '%' . $this->searchIcd9 . '%')->limit(20)->get()
            : [];

        return view('livewire.modul.rawat-inap.sub-rawat-inap.resume-pasien.edit', [
            'icd10List' => $icd10List,
            'icd9List'  => $icd9List,
        ]);
    }
}
