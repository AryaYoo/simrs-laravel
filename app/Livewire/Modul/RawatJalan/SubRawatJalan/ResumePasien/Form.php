<?php

namespace App\Livewire\Modul\RawatJalan\SubRawatJalan\ResumePasien;

use App\Models\RegPeriksa;
use App\Models\ResumePasien;
use App\Models\PemeriksaanRalan;
use App\Models\Penyakit;
use App\Models\Icd9;
use App\Livewire\Concerns\WithOptimisticLocking;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Form Resume Medis Pasien Rawat Jalan'])]
class Form extends Component
{
    use WithOptimisticLocking;

    public string $no_rawat;
    public string $mode = 'create';
    public $regPeriksa;

    // Fields
    public $kd_dokter;
    public $kondisi_pulang = 'Hidup';
    public $keluhan_utama = '';
    public $jalannya_penyakit = '';
    public $pemeriksaan_penunjang = '';
    public $hasil_laborat = '';
    public $obat_pulang = '';

    // Diagnosa
    public $diagnosa_utama = '';
    public $kd_diagnosa_utama = '';
    public $diagnosa_sekunder = '';
    public $kd_diagnosa_sekunder = '';
    public $diagnosa_sekunder2 = '';
    public $kd_diagnosa_sekunder2 = '';
    public $diagnosa_sekunder3 = '';
    public $kd_diagnosa_sekunder3 = '';
    public $diagnosa_sekunder4 = '';
    public $kd_diagnosa_sekunder4 = '';

    // Prosedur
    public $prosedur_utama = '';
    public $kd_prosedur_utama = '';
    public $prosedur_sekunder = '';
    public $kd_prosedur_sekunder = '';
    public $prosedur_sekunder2 = '';
    public $kd_prosedur_sekunder2 = '';
    public $prosedur_sekunder3 = '';
    public $kd_prosedur_sekunder3 = '';

    // Autocomplete state (shared — like the Ranap pattern)
    public $activeSearchField = '';
    public $autocompleteResults = [];
    protected $isSelecting = false;

    // Dokter Search State
    public $searchDokter = '';
    public $dokterResults = [];
    public $nmDokter = '';

    // ICD modal search (fallback modal)
    public $searchIcd10 = '';
    public $searchIcd9 = '';
    public $targetIcdField = '';

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter'])->findOrFail($this->no_rawat);

        $resume = ResumePasien::find($this->no_rawat);
        if ($resume) {
            $this->mode = 'edit';
            $this->initializeLock($resume);
            $this->fillData($resume);
            $this->nmDokter = $resume->dokter->nm_dokter ?? '';
        } else {
            $this->mode = 'create';
            $this->kd_dokter = $this->regPeriksa->kd_dokter;
            $this->nmDokter  = $this->regPeriksa->dokter->nm_dokter ?? '';
            $this->autoFillFromPemeriksaan();
        }
    }

    protected function fillData($resume)
    {
        $this->kd_dokter           = $resume->kd_dokter;
        $this->kondisi_pulang      = $resume->kondisi_pulang;
        $this->keluhan_utama       = $resume->keluhan_utama;
        $this->jalannya_penyakit   = $resume->jalannya_penyakit;
        $this->pemeriksaan_penunjang = $resume->pemeriksaan_penunjang;
        $this->hasil_laborat       = $resume->hasil_laborat;
        $this->obat_pulang         = $resume->obat_pulang;

        $this->diagnosa_utama       = $resume->diagnosa_utama;
        $this->kd_diagnosa_utama    = $resume->kd_diagnosa_utama;
        $this->diagnosa_sekunder    = $resume->diagnosa_sekunder;
        $this->kd_diagnosa_sekunder = $resume->kd_diagnosa_sekunder;
        $this->diagnosa_sekunder2   = $resume->diagnosa_sekunder2;
        $this->kd_diagnosa_sekunder2= $resume->kd_diagnosa_sekunder2;
        $this->diagnosa_sekunder3   = $resume->diagnosa_sekunder3;
        $this->kd_diagnosa_sekunder3= $resume->kd_diagnosa_sekunder3;
        $this->diagnosa_sekunder4   = $resume->diagnosa_sekunder4;
        $this->kd_diagnosa_sekunder4= $resume->kd_diagnosa_sekunder4;

        $this->prosedur_utama       = $resume->prosedur_utama;
        $this->kd_prosedur_utama    = $resume->kd_prosedur_utama;
        $this->prosedur_sekunder    = $resume->prosedur_sekunder;
        $this->kd_prosedur_sekunder = $resume->kd_prosedur_sekunder;
        $this->prosedur_sekunder2   = $resume->prosedur_sekunder2;
        $this->kd_prosedur_sekunder2= $resume->kd_prosedur_sekunder2;
        $this->prosedur_sekunder3   = $resume->prosedur_sekunder3;
        $this->kd_prosedur_sekunder3= $resume->kd_prosedur_sekunder3;
    }

    protected function autoFillFromPemeriksaan()
    {
        $pemeriksaan = PemeriksaanRalan::where('no_rawat', $this->no_rawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->first();

        if ($pemeriksaan) {
            $this->keluhan_utama      = $pemeriksaan->keluhan;
            $this->jalannya_penyakit  = $pemeriksaan->pemeriksaan;
            $this->pemeriksaan_penunjang = $pemeriksaan->rtl;
        }
    }

    // ─── Autocomplete (Inline Search — like Ranap pattern) ────────────────────

    public function updated($propertyName)
    {
        if ($this->isSelecting) return;

        $icd10Fields = [
            'diagnosa_utama', 'diagnosa_sekunder', 'diagnosa_sekunder2',
            'diagnosa_sekunder3', 'diagnosa_sekunder4',
        ];
        $icd9Fields = [
            'prosedur_utama', 'prosedur_sekunder', 'prosedur_sekunder2', 'prosedur_sekunder3',
        ];

        if (in_array($propertyName, $icd10Fields)) {
            $this->activeSearchField = $propertyName;
            $value = $this->$propertyName;
            $this->autocompleteResults = strlen($value) >= 3
                ? Penyakit::where('kd_penyakit', 'like', "%$value%")
                    ->orWhere('nm_penyakit', 'like', "%$value%")
                    ->limit(15)->get()->toArray()
                : [];
        } elseif (in_array($propertyName, $icd9Fields)) {
            $this->activeSearchField = $propertyName;
            $value = $this->$propertyName;
            $this->autocompleteResults = strlen($value) >= 3
                ? Icd9::where('kode', 'like', "%$value%")
                    ->orWhere('deskripsi_panjang', 'like', "%$value%")
                    ->limit(15)->get()->toArray()
                : [];
        } elseif ($propertyName === 'searchDokter') {
            $this->dokterResults = strlen($this->searchDokter) >= 2
                ? \App\Models\Dokter::where('kd_dokter', 'like', "%{$this->searchDokter}%")
                    ->orWhere('nm_dokter', 'like', "%{$this->searchDokter}%")
                    ->limit(10)->get()->toArray()
                : [];
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
        $this->activeSearchField  = '';
        $this->autocompleteResults = [];
    }

    // ICD modal select (fallback)
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

    // ─── Dokter ──────────────────────────────────────────────────────────────

    public function selectDokter($kd, $nm)
    {
        $this->kd_dokter     = $kd;
        $this->nmDokter      = $nm;
        $this->searchDokter  = '';
        $this->dokterResults = [];
    }

    // ─── Save ────────────────────────────────────────────────────────────────

    public function save()
    {
        $this->validate([
            'kd_dokter'    => 'required',
            'kondisi_pulang' => 'required|in:Hidup,Meninggal',
        ], [
            'kd_dokter.required'     => 'Dokter penanggung jawab harus dipilih.',
            'kondisi_pulang.required' => 'Kondisi pasien saat pulang wajib diisi.',
        ]);

        try {
            if ($this->mode === 'edit') {
                $resume = ResumePasien::findOrFail($this->no_rawat);
                $this->validateLock($resume->fresh());
            } else {
                $resume = new ResumePasien();
                $resume->no_rawat = $this->no_rawat;
            }

            $empty = fn($v) => empty($v) ? '' : $v;

            $resume->kd_dokter            = $this->kd_dokter;
            $resume->kondisi_pulang       = $this->kondisi_pulang;
            $resume->keluhan_utama        = $empty($this->keluhan_utama);
            $resume->jalannya_penyakit    = $empty($this->jalannya_penyakit);
            $resume->pemeriksaan_penunjang= $empty($this->pemeriksaan_penunjang);
            $resume->hasil_laborat        = $empty($this->hasil_laborat);
            $resume->obat_pulang          = $empty($this->obat_pulang);
            $resume->diagnosa_utama       = $empty($this->diagnosa_utama);
            $resume->kd_diagnosa_utama    = $empty($this->kd_diagnosa_utama);
            $resume->diagnosa_sekunder    = $empty($this->diagnosa_sekunder);
            $resume->kd_diagnosa_sekunder = $empty($this->kd_diagnosa_sekunder);
            $resume->diagnosa_sekunder2   = $empty($this->diagnosa_sekunder2);
            $resume->kd_diagnosa_sekunder2= $empty($this->kd_diagnosa_sekunder2);
            $resume->diagnosa_sekunder3   = $empty($this->diagnosa_sekunder3);
            $resume->kd_diagnosa_sekunder3= $empty($this->kd_diagnosa_sekunder3);
            $resume->diagnosa_sekunder4   = $empty($this->diagnosa_sekunder4);
            $resume->kd_diagnosa_sekunder4= $empty($this->kd_diagnosa_sekunder4);
            $resume->prosedur_utama       = $empty($this->prosedur_utama);
            $resume->kd_prosedur_utama    = $empty($this->kd_prosedur_utama);
            $resume->prosedur_sekunder    = $empty($this->prosedur_sekunder);
            $resume->kd_prosedur_sekunder = $empty($this->kd_prosedur_sekunder);
            $resume->prosedur_sekunder2   = $empty($this->prosedur_sekunder2);
            $resume->kd_prosedur_sekunder2= $empty($this->kd_prosedur_sekunder2);
            $resume->prosedur_sekunder3   = $empty($this->prosedur_sekunder3);
            $resume->kd_prosedur_sekunder3= $empty($this->kd_prosedur_sekunder3);

            $resume->save();

            if ($this->mode === 'edit') {
                $this->initializeLock($resume->fresh());
                $this->dispatch('swal', [
                    'title' => 'Berhasil!',
                    'text'  => 'Resume medis rawat jalan berhasil diupdate.',
                    'icon'  => 'success',
                    'timer' => 2000,
                ]);
            } else {
                $this->dispatch('swal', [
                    'title' => 'Berhasil!',
                    'text'  => 'Resume medis rawat jalan berhasil disimpan.',
                    'icon'  => 'success',
                    'timer' => 2000,
                ]);
            }
            
            $this->redirect(
                route('modul.rawat-jalan.sub-rawat-jalan.resume', str_replace('/', '-', $this->no_rawat)),
                navigate: true
            );
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Terjadi Kesalahan',
                'text'  => $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }

    public function render()
    {
        $icd10List = [];
        if (strlen($this->searchIcd10) >= 3) {
            $icd10List = Penyakit::where('kd_penyakit', 'like', "%{$this->searchIcd10}%")
                ->orWhere('nm_penyakit', 'like', "%{$this->searchIcd10}%")
                ->limit(20)->get();
        }

        $icd9List = [];
        if (strlen($this->searchIcd9) >= 3) {
            $icd9List = Icd9::where('kode', 'like', "%{$this->searchIcd9}%")
                ->orWhere('deskripsi_panjang', 'like', "%{$this->searchIcd9}%")
                ->limit(20)->get();
        }

        return view('livewire.modul.rawat-jalan.sub-rawat-jalan.resume-pasien.form', [
            'icd10List' => $icd10List,
            'icd9List'  => $icd9List,
        ]);
    }
}
