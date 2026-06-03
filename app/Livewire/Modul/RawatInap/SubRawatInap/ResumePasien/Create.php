<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\ResumePasien;

use App\Models\RegPeriksa;
use App\Models\ResumePasienRanap;
use App\Models\PemeriksaanRanap;
use App\Repositories\RawatInap\ResumePasienRepository;
use App\Models\Penyakit;
use App\Models\Icd9;
use App\Livewire\Concerns\WithOptimisticLocking;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Buat Resume Medis'])]
class Create extends Component
{
    use WithOptimisticLocking;

    public string $no_rawat;
    public $regPeriksa;

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

    // Dokter Search State
    public $searchDokter = '';
    public $dokterResults = [];
    public $nmDokter = '';

    // Attach/Select State (Alpine.js Modal)
    public $selectedKeluhan = [];
    public $selectedLab = [];
    public $selectedTindakan = [];
    public $selectedObat = [];
    public $selectedObatPulang = [];
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

        $this->kd_dokter  = $this->regPeriksa->kd_dokter;
        $this->nmDokter   = $this->regPeriksa->dokter->nm_dokter ?? '';
        
        $resume = ResumePasienRanap::find($this->no_rawat);
        if ($resume) {
            $this->diagnosa_awal        = $resume->diagnosa_awal;
            $this->alasan               = $resume->alasan;
            $this->keluhan_utama        = $resume->keluhan_utama;
            $this->pemeriksaan_fisik    = $resume->pemeriksaan_fisik;
            $this->jalannya_penyakit    = $resume->jalannya_penyakit;
            $this->pemeriksaan_penunjang = $resume->pemeriksaan_penunjang;
            $this->hasil_laborat        = $resume->hasil_laborat;
            $this->tindakan_dan_operasi = $resume->tindakan_dan_operasi;
            $this->obat_di_rs           = $resume->obat_di_rs;

            $this->diagnosa_utama        = $resume->diagnosa_utama;
            $this->kd_diagnosa_utama     = $resume->kd_diagnosa_utama;
            $this->diagnosa_sekunder     = $resume->diagnosa_sekunder;
            $this->kd_diagnosa_sekunder  = $resume->kd_diagnosa_sekunder;
            $this->diagnosa_sekunder2    = $resume->diagnosa_sekunder2;
            $this->kd_diagnosa_sekunder2 = $resume->kd_diagnosa_sekunder2;
            $this->diagnosa_sekunder3    = $resume->diagnosa_sekunder3;
            $this->kd_diagnosa_sekunder3 = $resume->kd_diagnosa_sekunder3;
            $this->diagnosa_sekunder4    = $resume->diagnosa_sekunder4;
            $this->kd_diagnosa_sekunder4 = $resume->kd_diagnosa_sekunder4;

            $this->prosedur_utama        = $resume->prosedur_utama;
            $this->kd_prosedur_utama     = $resume->kd_prosedur_utama;
            $this->prosedur_sekunder     = $resume->prosedur_sekunder;
            $this->kd_prosedur_sekunder  = $resume->kd_prosedur_sekunder;
            $this->prosedur_sekunder2    = $resume->prosedur_sekunder2;
            $this->kd_prosedur_sekunder2 = $resume->kd_prosedur_sekunder2;
            $this->prosedur_sekunder3    = $resume->prosedur_sekunder3;
            $this->kd_prosedur_sekunder3 = $resume->kd_prosedur_sekunder3;

            $this->alergi          = $resume->alergi;
            $this->diet            = $resume->diet;
            $this->lab_belum       = $resume->lab_belum;
            $this->edukasi         = $resume->edukasi;
            $this->keadaan         = $resume->keadaan;
            $this->ket_keadaan     = $resume->ket_keadaan;
            $this->cara_keluar     = $resume->cara_keluar;
            $this->ket_keluar      = $resume->ket_keluar;
            $this->dilanjutkan     = $resume->dilanjutkan;
            $this->ket_dilanjutkan = $resume->ket_dilanjutkan;
            $this->kontrol         = $resume->kontrol;
            $this->obat_pulang     = $resume->obat_pulang;
        } else {
            // Default values for dropdowns
            $this->keadaan = 'Membaik';
            $this->cara_keluar = 'Atas Izin Dokter';
            $this->dilanjutkan = 'Kembali Ke RS';
            $this->kontrol = now()->addDays(7)->format('Y-m-d H:i:s');

            // Auto-Fill Logic on mount
            $this->autoFillData();
        }

        // SOP: Initialize lock for concurrency control
        $this->initializeLock($this->regPeriksa);
    }

    public function autoFillData()
    {
        $data = ResumePasienRepository::getAutoFillData($this->no_rawat, $this->regPeriksa);
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    // ─────────────────────────────────────────────
    // Auto-Fill Methods (porting dari Casemix Form)
    // ─────────────────────────────────────────────

    public function autoFillSOAP($field, $column)
    {
        $allData = collect($this->regPeriksa->pemeriksaanRanap)
            ->sortBy('tgl_perawatan')
            ->map(fn($p) => $p->$column ?? null)
            ->filter(fn($val) => !empty($val) && $val !== '-')
            ->unique()
            ->implode(', ');

        if ($allData) {
            $this->$field = $allData;
            $this->dispatch('swal', [
                'title' => 'Otomatis Terisi',
                'text'  => 'Data riwayat telah dimasukkan.',
                'icon'  => 'success',
                'timer' => 1000,
            ]);
        } else {
            $this->dispatch('swal', [
                'title' => 'Data Kosong',
                'text'  => 'Tidak ada data riwayat SOAP ditemukan.',
                'icon'  => 'info',
            ]);
        }
    }

    public function autoFillObatPulang()
    {
        $allObatPulang = collect($this->regPeriksa->permintaanResepPulang)
            ->flatMap(fn($p) => $p->detailPermintaan)
            ->sortByDesc(fn($o) => $o->no_permintaan)
            ->map(fn($o) => ($o->barang->nama_brng ?? '-') . ' (' . $o->jml . ' ' . ($o->barang->kode_sat ?? '') . ', Dosis: ' . $o->dosis . ')')
            ->unique()
            ->implode(', ');

        if ($allObatPulang) {
            $this->obat_pulang = $allObatPulang;
            $this->dispatch('swal', ['title' => 'Otomatis Terisi', 'text' => 'Semua riwayat permintaan resep pulang telah dimasukkan.', 'icon' => 'success', 'timer' => 1000]);
        } else {
            $this->dispatch('swal', ['title' => 'Data Kosong', 'text' => 'Tidak ada riwayat permintaan resep pulang ditemukan.', 'icon' => 'info']);
        }
    }

    public function autoFillTindakan()
    {
        $allTindakan = collect($this->regPeriksa->rawatInapDr)
            ->concat(collect($this->regPeriksa->rawatInapPr))
            ->concat(collect($this->regPeriksa->rawatInapDrpr))
            ->map(fn($t) => $t->jnsPerawatan->nm_perawatan ?? null)
            ->filter()
            ->unique()
            ->implode(', ');

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
            ->filter()
            ->unique()
            ->implode(', ');

        if ($allObat) {
            $this->obat_di_rs = $allObat;
            $this->dispatch('swal', ['title' => 'Otomatis Terisi', 'text' => 'Semua riwayat pemberian obat telah dimasukkan.', 'icon' => 'success', 'timer' => 1000]);
        } else {
            $this->dispatch('swal', ['title' => 'Data Kosong', 'text' => 'Tidak ada riwayat pemberian obat ditemukan.', 'icon' => 'info']);
        }
    }

    // ─────────────────────────────────────────────
    // Attach (Pick from History) Methods
    // ─────────────────────────────────────────────

    public function prepareAttach($field, $column)
    {
        $this->targetAttachField  = $field;
        $this->targetAttachColumn = $column;
        $this->selectedKeluhan    = [];
        $this->selectedLab        = [];
        $this->selectedTindakan   = [];
        $this->selectedObat       = [];
        $this->selectedObatPulang = [];
    }

    public function toggleSelectAll()
    {
        if ($this->targetAttachColumn == 'lab_hasil') {
            $lab = collect($this->regPeriksa->detailPeriksaLab);
            $this->selectedLab = count($this->selectedLab) === $lab->count()
                ? [] : $lab->map(fn($l) => "{$l->tgl_periksa}|{$l->jam}|{$l->kd_jenis_prw}|{$l->id_template}")->toArray();
        } elseif ($this->targetAttachColumn == 'tindakan') {
            $all = collect($this->regPeriksa->rawatInapDr)->concat(collect($this->regPeriksa->rawatInapPr))->concat(collect($this->regPeriksa->rawatInapDrpr));
            $this->selectedTindakan = count($this->selectedTindakan) === $all->count()
                ? [] : $all->map(fn($t) => "{$t->tgl_perawatan}|{$t->jam_rawat}|{$t->kd_jenis_prw}")->toArray();
        } elseif ($this->targetAttachColumn == 'obat') {
            $obat = collect($this->regPeriksa->detailPemberianObat);
            $this->selectedObat = count($this->selectedObat) === $obat->count()
                ? [] : $obat->map(fn($o) => "{$o->tgl_perawatan}|{$o->jam}|{$o->kode_brng}")->toArray();
        } elseif ($this->targetAttachColumn == 'OBAT_PULANG') {
            $obatPulang = collect($this->regPeriksa->permintaanResepPulang)->flatMap(fn($p) => $p->detailPermintaan);
            $this->selectedObatPulang = count($this->selectedObatPulang) === $obatPulang->count()
                ? [] : $obatPulang->map(fn($o) => "{$o->no_permintaan}|{$o->kode_brng}")->toArray();
        } else {
            $keluhan = collect($this->regPeriksa->pemeriksaanRanap);
            $this->selectedKeluhan = count($this->selectedKeluhan) === $keluhan->count()
                ? [] : $keluhan->map(fn($p) => "{$p->tgl_perawatan}|{$p->jam_rawat}")->toArray();
        }
    }

    public function attachKeluhan()
    {
        if (empty($this->selectedKeluhan)) return;

        $texts = [];
        foreach ($this->selectedKeluhan as $key) {
            [$tgl, $jam] = explode('|', $key);
            $pemeriksaan = PemeriksaanRanap::where('no_rawat', $this->no_rawat)
                ->where('tgl_perawatan', $tgl)->where('jam_rawat', $jam)->first();
            $col = $this->targetAttachColumn;
            if ($pemeriksaan && !empty($pemeriksaan->$col) && $pemeriksaan->$col !== '-') {
                $texts[] = $pemeriksaan->$col;
            }
        }
        $this->applyAttachments($texts);
        $this->selectedKeluhan = [];
    }

    public function attachLab()
    {
        if (empty($this->selectedLab)) return;

        $texts = [];
        foreach ($this->selectedLab as $key) {
            [$tgl, $jam, $kd_jenis_prw, $id_template] = explode('|', $key);
            $lab = \App\Models\DetailPeriksaLab::with('template')
                ->where('no_rawat', $this->no_rawat)
                ->where('tgl_periksa', $tgl)->where('jam', $jam)
                ->where('kd_jenis_prw', $kd_jenis_prw)->where('id_template', $id_template)
                ->first();
            if ($lab) {
                $texts[] = ($lab->template->Pemeriksaan ?? '-') . ' : ' . ($lab->nilai ?? '');
            }
        }
        $this->applyAttachments($texts);
        $this->selectedLab = [];
    }

    public function attachTindakan()
    {
        if (empty($this->selectedTindakan)) return;

        $texts = [];
        foreach ($this->selectedTindakan as $id) {
            [$tgl, $jam, $kd_jenis_prw] = explode('|', $id);
            $item = \App\Models\RawatInapDr::where(['no_rawat' => $this->no_rawat, 'tgl_perawatan' => $tgl, 'jam_rawat' => $jam, 'kd_jenis_prw' => $kd_jenis_prw])->first()
                ?? \App\Models\RawatInapPr::where(['no_rawat' => $this->no_rawat, 'tgl_perawatan' => $tgl, 'jam_rawat' => $jam, 'kd_jenis_prw' => $kd_jenis_prw])->first()
                ?? \App\Models\RawatInapDrpr::where(['no_rawat' => $this->no_rawat, 'tgl_perawatan' => $tgl, 'jam_rawat' => $jam, 'kd_jenis_prw' => $kd_jenis_prw])->first();
            if ($item && $item->jnsPerawatan) {
                $texts[] = $item->jnsPerawatan->nm_perawatan;
            }
        }
        $this->applyAttachments($texts);
        $this->selectedTindakan = [];
    }

    public function attachObat()
    {
        if (empty($this->selectedObat)) return;

        $texts = [];
        foreach ($this->selectedObat as $id) {
            [$tgl, $jam, $kode_brng] = explode('|', $id);
            $obat = \App\Models\DetailPemberianObat::with('barang')
                ->where('no_rawat', $this->no_rawat)
                ->where('tgl_perawatan', $tgl)->where('jam', $jam)->where('kode_brng', $kode_brng)
                ->first();
            if ($obat && $obat->barang) {
                $texts[] = $obat->barang->nama_brng;
            }
        }
        $this->applyAttachments($texts);
        $this->selectedObat = [];
    }

    public function attachObatPulang()
    {
        if (empty($this->selectedObatPulang)) return;

        $texts = [];
        foreach ($this->selectedObatPulang as $id) {
            [$no_permintaan, $kode_brng] = explode('|', $id);
            $obat = \App\Models\DetailPermintaanResepPulang::with('barang')
                ->where('no_permintaan', $no_permintaan)
                ->where('kode_brng', $kode_brng)
                ->first();
            if ($obat && $obat->barang) {
                $texts[] = $obat->barang->nama_brng . ' (' . $obat->jml . ' ' . $obat->barang->kode_sat . ', Dosis: ' . $obat->dosis . ')';
            }
        }
        $this->applyAttachments($texts);
        $this->selectedObatPulang = [];
    }

    private function applyAttachments($texts)
    {
        if (!empty($texts)) {
            $joinedText = implode(', ', array_unique($texts));
            $field = $this->targetAttachField;
            if (empty($this->$field)) {
                $this->$field = $joinedText;
            } else {
                $this->$field .= ', ' . $joinedText;
            }
        }
    }

    public function refreshData()
    {
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

        $icd10Fields = ['diagnosa_utama', 'diagnosa_sekunder', 'diagnosa_sekunder2', 'diagnosa_sekunder3', 'diagnosa_sekunder4'];
        $icd9Fields  = ['prosedur_utama', 'prosedur_sekunder', 'prosedur_sekunder2', 'prosedur_sekunder3'];

        if (in_array($propertyName, $icd10Fields)) {
            $this->activeSearchField = $propertyName;
            $value = $this->$propertyName;
            $this->autocompleteResults = strlen($value) >= 3
                ? Penyakit::where('kd_penyakit', 'like', "%$value%")->orWhere('nm_penyakit', 'like', "%$value%")->limit(10)->get()->toArray()
                : [];
        } elseif (in_array($propertyName, $icd9Fields)) {
            $this->activeSearchField = $propertyName;
            $value = $this->$propertyName;
            $this->autocompleteResults = strlen($value) >= 3
                ? Icd9::where('kode', 'like', "%$value%")->orWhere('deskripsi_panjang', 'like', "%$value%")->limit(10)->get()->toArray()
                : [];
        } elseif ($propertyName === 'searchDokter') {
            $this->dokterResults = strlen($this->searchDokter) >= 2
                ? \App\Models\Dokter::where('kd_dokter', 'like', "%{$this->searchDokter}%")
                    ->orWhere('nm_dokter', 'like', "%{$this->searchDokter}%")
                    ->limit(10)->get()->toArray()
                : [];
        }
    }

    public function selectDokter($kd, $nm)
    {
        $this->kd_dokter    = $kd;
        $this->nmDokter     = $nm;
        $this->searchDokter = '';
        $this->dokterResults = [];
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
        $this->activeSearchField  = '';
        $this->autocompleteResults = [];
    }

    // ─────────────────────────────────────────────
    // Save
    // ─────────────────────────────────────────────

    public function save()
    {
        $this->validate([
            'kd_dokter'      => 'required',
            'diagnosa_utama' => 'required',
        ]);

        // SOP: Validate lock before saving
        $this->validateLock($this->regPeriksa->fresh());

        try {
            ResumePasienRanap::updateOrCreate(
                ['no_rawat' => $this->no_rawat],
                [
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
                ]
            );

            session()->flash('message', 'Resume medis berhasil disimpan.');
            $this->dispatch('swal', ['title' => 'Sukses!', 'text' => 'Resume medis berhasil disimpan.', 'icon' => 'success']);
            
            return $this->redirect(route('modul.rawat-inap.sub-rawat-inap.resume', str_replace('/', '-', $this->no_rawat)), navigate: true);
            
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal Menyimpan', 'text' => 'Terjadi kesalahan sistem: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    private function defaultEmpty($value)
    {
        return empty($value) ? '-' : $value;
    }

    public function render()
    {
        // Reload regPeriksa dengan semua relasi pada setiap render
        // agar data tidak hilang setelah Livewire request (prepareAttach, dll.)
        $this->regPeriksa = RegPeriksa::with([
            'pasien', 'dokter', 'kamarInap.kamar.bangsal',
            'diagnosaPasien.penyakit',
            'detailPeriksaLab.template',
            'pemeriksaanRanap',
            'rawatInapDr.jnsPerawatan',
            'rawatInapPr.jnsPerawatan',
            'rawatInapDrpr.jnsPerawatan',
            'detailPemberianObat.barang',
            'permintaanResepPulang.detailPermintaan.barang',
        ])->findOrFail($this->no_rawat);

        $icd10List = [];
        if (strlen($this->searchIcd10) >= 3) {
            $icd10List = Penyakit::where('kd_penyakit', 'like', '%' . $this->searchIcd10 . '%')
                ->orWhere('nm_penyakit', 'like', '%' . $this->searchIcd10 . '%')->limit(20)->get();
        }
        $icd9List = [];
        if (strlen($this->searchIcd9) >= 3) {
            $icd9List = Icd9::where('kode', 'like', '%' . $this->searchIcd9 . '%')
                ->orWhere('deskripsi_panjang', 'like', '%' . $this->searchIcd9 . '%')->limit(20)->get();
        }

        return view('livewire.modul.rawat-inap.sub-rawat-inap.resume-pasien.create', [
            'icd10List' => $icd10List,
            'icd9List'  => $icd9List,
        ]);
    }
}
