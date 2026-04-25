<?php

namespace App\Livewire\Modul\CasemixRawatJalan\Resume;

use App\Models\RegPeriksa;
use App\Models\ResumePasien;
use App\Models\Penyakit;
use App\Models\Icd9;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Livewire\Concerns\WithOptimisticLocking;
use App\Models\PemeriksaanRalan;

#[Layout('layouts.app', ['title' => 'Buat Resume Casemix (RAJAL)'])]
class Form extends Component
{
    use WithOptimisticLocking;
    public string $no_rawat;
    public $regPeriksa;

    // Form Fields
    public $kd_dokter;
    public $keluhan_utama;
    public $jalannya_penyakit;
    public $pemeriksaan_penunjang;
    public $hasil_laborat;
    
    public $diagnosa_utama, $kd_diagnosa_utama;
    public $diagnosa_sekunder, $kd_diagnosa_sekunder;
    public $diagnosa_sekunder2, $kd_diagnosa_sekunder2;
    public $diagnosa_sekunder3, $kd_diagnosa_sekunder3;
    public $diagnosa_sekunder4, $kd_diagnosa_sekunder4;
    
    public $prosedur_utama, $kd_prosedur_utama;
    public $prosedur_sekunder, $kd_prosedur_sekunder;
    public $prosedur_sekunder2, $kd_prosedur_sekunder2;
    public $prosedur_sekunder3, $kd_prosedur_sekunder3;
    
    public $kondisi_pulang;
    public $obat_pulang;

    // Search/Lookup State
    public $searchIcd10 = '';
    public $searchIcd9 = '';
    public $targetIcdField = '';

    // Autocomplete State
    public $activeSearchField = '';
    public $autocompleteResults = [];
    protected $isSelecting = false;

    // Modal Select State
    public $selectedKeluhan = [];
    public $targetAttachField = 'keluhan_utama';
    public $targetAttachColumn = 'keluhan';

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'poliklinik', 'diagnosaPasien.penyakit'])->findOrFail($this->no_rawat);
        $this->kd_dokter = $this->regPeriksa->kd_dokter;
        
        $resume = ResumePasien::find($this->no_rawat);
        if ($resume) {
            $this->keluhan_utama = $resume->keluhan_utama;
            $this->jalannya_penyakit = $resume->jalannya_penyakit;
            $this->pemeriksaan_penunjang = $resume->pemeriksaan_penunjang;
            $this->hasil_laborat = $resume->hasil_laborat;
            
            $this->diagnosa_utama = $resume->diagnosa_utama;
            $this->kd_diagnosa_utama = $resume->kd_diagnosa_utama;
            $this->diagnosa_sekunder = $resume->diagnosa_sekunder;
            $this->kd_diagnosa_sekunder = $resume->kd_diagnosa_sekunder;
            $this->diagnosa_sekunder2 = $resume->diagnosa_sekunder2;
            $this->kd_diagnosa_sekunder2 = $resume->kd_diagnosa_sekunder2;
            $this->diagnosa_sekunder3 = $resume->diagnosa_sekunder3;
            $this->kd_diagnosa_sekunder3 = $resume->kd_diagnosa_sekunder3;
            $this->diagnosa_sekunder4 = $resume->diagnosa_sekunder4;
            $this->kd_diagnosa_sekunder4 = $resume->kd_diagnosa_sekunder4;
            
            $this->prosedur_utama = $resume->prosedur_utama;
            $this->kd_prosedur_utama = $resume->kd_prosedur_utama;
            $this->prosedur_sekunder = $resume->prosedur_sekunder;
            $this->kd_prosedur_sekunder = $resume->kd_prosedur_sekunder;
            $this->prosedur_sekunder2 = $resume->prosedur_sekunder2;
            $this->kd_prosedur_sekunder2 = $resume->kd_prosedur_sekunder2;
            $this->prosedur_sekunder3 = $resume->prosedur_sekunder3;
            $this->kd_prosedur_sekunder3 = $resume->kd_prosedur_sekunder3;
            
            $this->kondisi_pulang = $resume->kondisi_pulang;
            $this->obat_pulang = $resume->obat_pulang;
            
            // SOP 1: Initialize lock for legacy data
            $this->initializeLock($resume);
        } else {
            $this->kondisi_pulang = 'Hidup';
        }
    }

    public function selectIcd10($code, $name)
    {
        $fieldKd = 'kd_' . $this->targetIcdField;
        $fieldName = $this->targetIcdField;
        
        $this->$fieldKd = $code;
        $this->$fieldName = $name;
        
        $this->dispatch('close-modal', 'icd10-modal');
    }

    public function selectIcd9($code, $name)
    {
        $fieldKd = 'kd_' . $this->targetIcdField;
        $fieldName = $this->targetIcdField;
        
        $this->$fieldKd = $code;
        $this->$fieldName = $name;
        
        $this->dispatch('close-modal', 'icd9-modal');
    }

    public function updated($propertyName)
    {
        if ($this->isSelecting) return;

        $icd10Fields = ['diagnosa_utama', 'diagnosa_sekunder', 'diagnosa_sekunder2', 'diagnosa_sekunder3', 'diagnosa_sekunder4'];
        $icd9Fields = ['prosedur_utama', 'prosedur_sekunder', 'prosedur_sekunder2', 'prosedur_sekunder3'];

        if (in_array($propertyName, $icd10Fields)) {
            $this->activeSearchField = $propertyName;
            $value = $this->$propertyName;
            if (strlen($value) >= 3) {
                $this->autocompleteResults = Penyakit::where('kd_penyakit', 'like', '%' . $value . '%')
                    ->orWhere('nm_penyakit', 'like', '%' . $value . '%')
                    ->limit(10)
                    ->get()
                    ->toArray();
            } else {
                $this->autocompleteResults = [];
            }
        } elseif (in_array($propertyName, $icd9Fields)) {
            $this->activeSearchField = $propertyName;
            $value = $this->$propertyName;
            if (strlen($value) >= 3) {
                $this->autocompleteResults = Icd9::where('kode', 'like', '%' . $value . '%')
                    ->orWhere('deskripsi_panjang', 'like', '%' . $value . '%')
                    ->limit(10)
                    ->get()
                    ->toArray();
            } else {
                $this->autocompleteResults = [];
            }
        }
    }

    public function selectAutocompleteItem($code, $name)
    {
        $this->isSelecting = true;
        
        $fieldKd = 'kd_' . $this->activeSearchField;
        $fieldName = $this->activeSearchField;

        $this->$fieldKd = $code;
        $this->$fieldName = $name;

        $this->clearAutocomplete();
        $this->isSelecting = false;
    }

    public function clearAutocomplete()
    {
        $this->activeSearchField = '';
        $this->autocompleteResults = [];
    }

    public function attachKeluhan()
    {
        if (!empty($this->selectedKeluhan)) {
            $keluhanTexts = [];
            foreach ($this->selectedKeluhan as $key) {
                [$tgl, $jam] = explode('|', $key);
                $pemeriksaan = PemeriksaanRalan::where('no_rawat', $this->no_rawat)
                    ->where('tgl_perawatan', $tgl)
                    ->where('jam_rawat', $jam)
                    ->first();
                
                $col = $this->targetAttachColumn;
                if ($pemeriksaan && !empty($pemeriksaan->$col) && $pemeriksaan->$col !== '-') {
                    $keluhanTexts[] = $pemeriksaan->$col;
                }
            }

            if (!empty($keluhanTexts)) {
                $keluhanText = implode(', ', array_unique($keluhanTexts));
                $field = $this->targetAttachField;
                
                if (empty($this->$field)) {
                    $this->$field = $keluhanText;
                } else {
                    $this->$field .= ', ' . $keluhanText;
                }
            }
            
            $this->selectedKeluhan = [];
            $this->dispatch('close-modal', 'modal-keluhan-rajal');
        }
    }

    public function prepareAttach($field, $column)
    {
        $this->targetAttachField = $field;
        $this->targetAttachColumn = $column;
        $this->selectedKeluhan = [];
    }

    public function attachEarliest($targetField = 'keluhan_utama', $column = 'keluhan')
    {
        $earliest = PemeriksaanRalan::where('no_rawat', $this->no_rawat)
            ->orderBy('tgl_perawatan', 'asc')
            ->orderBy('jam_rawat', 'asc')
            ->first();

        if ($earliest && !empty($earliest->$column)) {
            $value = $earliest->$column;
            if (empty($this->$targetField)) {
                $this->$targetField = $value;
            } else {
                if (!str_contains($this->$targetField, $value)) {
                    $this->$targetField .= ', ' . $value;
                }
            }
            
            $this->dispatch('swal', [
                'title' => 'Berhasil!',
                'text' => 'Data awal berhasil ditambahkan.',
                'icon' => 'success',
                'timer' => 1500
            ]);
        }
    }

    public function save()
    {
        $this->validate([
            'kd_dokter' => 'required',
            'diagnosa_utama' => 'required',
        ]);

        try {
            // SOP 1: Validate lock before save if editing
            $resume = ResumePasien::find($this->no_rawat);
            if ($resume) {
                $this->validateLock($resume);
            }

            ResumePasien::updateOrCreate(
                ['no_rawat' => $this->no_rawat],
                [
                    'kd_dokter' => $this->kd_dokter ?? '',
                    'keluhan_utama' => $this->keluhan_utama ?? '',
                    'jalannya_penyakit' => $this->jalannya_penyakit ?? '',
                    'pemeriksaan_penunjang' => $this->pemeriksaan_penunjang ?? '',
                    'hasil_laborat' => $this->hasil_laborat ?? '',
                    
                    'diagnosa_utama' => $this->diagnosa_utama ?? '',
                    'kd_diagnosa_utama' => $this->kd_diagnosa_utama ?? '',
                    'diagnosa_sekunder' => $this->diagnosa_sekunder ?? '',
                    'kd_diagnosa_sekunder' => $this->kd_diagnosa_sekunder ?? '',
                    'diagnosa_sekunder2' => $this->diagnosa_sekunder2 ?? '',
                    'kd_diagnosa_sekunder2' => $this->kd_diagnosa_sekunder2 ?? '',
                    'diagnosa_sekunder3' => $this->diagnosa_sekunder3 ?? '',
                    'kd_diagnosa_sekunder3' => $this->kd_diagnosa_sekunder3 ?? '',
                    'diagnosa_sekunder4' => $this->diagnosa_sekunder4 ?? '',
                    'kd_diagnosa_sekunder4' => $this->kd_diagnosa_sekunder4 ?? '',
                    
                    'prosedur_utama' => $this->prosedur_utama ?? '',
                    'kd_prosedur_utama' => $this->kd_prosedur_utama ?? '',
                    'prosedur_sekunder' => $this->prosedur_sekunder ?? '',
                    'kd_prosedur_sekunder' => $this->kd_prosedur_sekunder ?? '',
                    'prosedur_sekunder2' => $this->prosedur_sekunder2 ?? '',
                    'kd_prosedur_sekunder2' => $this->kd_prosedur_sekunder2 ?? '',
                    'prosedur_sekunder3' => $this->prosedur_sekunder3 ?? '',
                    'kd_prosedur_sekunder3' => $this->kd_prosedur_sekunder3 ?? '',
                    
                    'kondisi_pulang' => $this->kondisi_pulang ?? '',
                    'obat_pulang' => $this->obat_pulang ?? '',
                ]
            );

            $this->dispatch('swal', [
                'title' => 'Sukses!', 
                'text' => 'Resume medis casemix rawat jalan berhasil disimpan.',
                'icon' => 'success'
            ]);
            
            return $this->redirect(route('modul.casemix-rawat-jalan.resume', str_replace('/', '-', $this->no_rawat)), navigate: true);
            
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Gagal Menyimpan',
                'text' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function render()
    {
        $icd10List = [];
        if (strlen($this->searchIcd10) >= 3) {
            $icd10List = Penyakit::where('kd_penyakit', 'like', '%' . $this->searchIcd10 . '%')
                ->orWhere('nm_penyakit', 'like', '%' . $this->searchIcd10 . '%')
                ->limit(20)
                ->get();
        }

        $icd9List = [];
        if (strlen($this->searchIcd9) >= 3) {
            $icd9List = Icd9::where('kode', 'like', '%' . $this->searchIcd9 . '%')
                ->orWhere('deskripsi_panjang', 'like', '%' . $this->searchIcd9 . '%')
                ->limit(20)
                ->get();
        }

        return view('livewire.modul.casemix-rawat-jalan.resume.form', [
            'icd10List' => $icd10List,
            'icd9List' => $icd9List,
        ]);
    }
}
