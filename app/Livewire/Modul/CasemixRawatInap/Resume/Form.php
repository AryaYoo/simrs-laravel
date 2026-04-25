<?php

namespace App\Livewire\Modul\CasemixRawatInap\Resume;

use App\Models\RegPeriksa;
use App\Models\ResumePasienRanap;
use App\Models\Penyakit;
use App\Models\Icd9;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Livewire\Concerns\WithOptimisticLocking;
use App\Models\PemeriksaanRanap;

#[Layout('layouts.app', ['title' => 'Buat Resume Casemix (RANAP)'])]
class Form extends Component
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
    
    // Modal Select State
    public $selectedKeluhan = [];
    public $selectedLab = []; // New property for lab selection
    public $targetAttachField = 'keluhan_utama';
    public $targetAttachColumn = 'keluhan';

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'kamarInap.kamar.bangsal', 'diagnosaPasien.penyakit', 'detailPeriksaLab.template'])->findOrFail($this->no_rawat);
        $this->kd_dokter = $this->regPeriksa->kd_dokter;
        
        $resume = ResumePasienRanap::find($this->no_rawat);
        if ($resume) {
            $this->diagnosa_awal = $resume->diagnosa_awal;
            $this->alasan = $resume->alasan;
            $this->keluhan_utama = $resume->keluhan_utama;
            $this->pemeriksaan_fisik = $resume->pemeriksaan_fisik;
            $this->jalannya_penyakit = $resume->jalannya_penyakit;
            $this->pemeriksaan_penunjang = $resume->pemeriksaan_penunjang;
            $this->hasil_laborat = $resume->hasil_laborat;
            $this->tindakan_dan_operasi = $resume->tindakan_dan_operasi;
            $this->obat_di_rs = $resume->obat_di_rs;
            
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
            
            $this->alergi = $resume->alergi;
            $this->diet = $resume->diet;
            $this->lab_belum = $resume->lab_belum;
            $this->edukasi = $resume->edukasi;
            $this->keadaan = $resume->keadaan;
            $this->ket_keadaan = $resume->ket_keadaan;
            $this->cara_keluar = $resume->cara_keluar;
            $this->ket_keluar = $resume->ket_keluar;
            $this->dilanjutkan = $resume->dilanjutkan;
            $this->ket_dilanjutkan = $resume->ket_dilanjutkan;
            $this->kontrol = $resume->kontrol;
            $this->obat_pulang = $resume->obat_pulang;
            
            // SOP 1: Initialize lock for legacy data
            $this->initializeLock($resume);
        } else {
            $this->keadaan = 'Membaik';
            $this->cara_keluar = 'Atas Izin Dokter';
            $this->dilanjutkan = 'Kembali Ke RS';
            $this->kontrol = now()->addDays(7)->format('Y-m-d H:i:s');
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
            $texts = [];
            foreach ($this->selectedKeluhan as $key) {
                [$tgl, $jam] = explode('|', $key);
                $pemeriksaan = PemeriksaanRanap::where('no_rawat', $this->no_rawat)
                    ->where('tgl_perawatan', $tgl)
                    ->where('jam_rawat', $jam)
                    ->first();
                
                $col = $this->targetAttachColumn;
                if ($pemeriksaan && !empty($pemeriksaan->$col) && $pemeriksaan->$col !== '-') {
                    $texts[] = $pemeriksaan->$col;
                }
            }

            $this->applyAttachments($texts);
            $this->selectedKeluhan = [];
        }
    }

    public function attachLab()
    {
        if (!empty($this->selectedLab)) {
            $texts = [];
            foreach ($this->selectedLab as $key) {
                [$tgl, $jam, $kd_jenis_prw, $id_template] = explode('|', $key);
                $lab = \App\Models\DetailPeriksaLab::with('template')
                    ->where('no_rawat', $this->no_rawat)
                    ->where('tgl_periksa', $tgl)
                    ->where('jam', $jam)
                    ->where('kd_jenis_prw', $kd_jenis_prw)
                    ->where('id_template', $id_template)
                    ->first();
                
                if ($lab) {
                    $namaPemeriksaan = $lab->template->Pemeriksaan ?? '-';
                    $nilai = $lab->nilai ?? '';
                    $texts[] = "{$namaPemeriksaan} : {$nilai}";
                }
            }

            $this->applyAttachments($texts);
            $this->selectedLab = [];
        }
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

    public function prepareAttach($field, $column)
    {
        $this->targetAttachField = $field;
        $this->targetAttachColumn = $column;
        $this->selectedKeluhan = [];
        $this->selectedLab = [];
    }

    public function toggleSelectAll()
    {
        if ($this->targetAttachColumn == 'lab_hasil') {
            if (count($this->selectedLab) === count($this->regPeriksa->detailPeriksaLab)) {
                $this->selectedLab = [];
            } else {
                $this->selectedLab = $this->regPeriksa->detailPeriksaLab->map(fn($lab) => 
                    "{$lab->tgl_periksa}|{$lab->jam}|{$lab->kd_jenis_prw}|{$lab->id_template}"
                )->toArray();
            }
        } else {
            if (count($this->selectedKeluhan) === count($this->regPeriksa->pemeriksaanRanap)) {
                $this->selectedKeluhan = [];
            } else {
                $this->selectedKeluhan = $this->regPeriksa->pemeriksaanRanap->map(fn($p) => 
                    "{$p->tgl_perawatan}|{$p->jam_rawat}"
                )->toArray();
            }
        }
    }

    public function refreshData()
    {
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'kamarInap.kamar.bangsal', 'diagnosaPasien.penyakit', 'detailPeriksaLab.template', 'pemeriksaanRanap'])->findOrFail($this->no_rawat);
        $this->dispatch('swal', [
            'title' => 'Data Diperbarui',
            'icon' => 'success',
            'timer' => 1000,
            'showConfirmButton' => false
        ]);
    }

    public function attachEarliest($targetField = 'keluhan_utama', $column = 'keluhan')
    {
        $earliest = PemeriksaanRanap::where('no_rawat', $this->no_rawat)
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
            $resume = ResumePasienRanap::find($this->no_rawat);
            if ($resume) {
                $this->validateLock($resume);
            }

            ResumePasienRanap::updateOrCreate(
                ['no_rawat' => $this->no_rawat],
                [
                    'kd_dokter' => $this->kd_dokter ?? '-',
                    'diagnosa_awal' => $this->defaultEmpty($this->diagnosa_awal),
                    'alasan' => $this->defaultEmpty($this->alasan),
                    'keluhan_utama' => $this->defaultEmpty($this->keluhan_utama),
                    'pemeriksaan_fisik' => $this->defaultEmpty($this->pemeriksaan_fisik),
                    'jalannya_penyakit' => $this->defaultEmpty($this->jalannya_penyakit),
                    'pemeriksaan_penunjang' => $this->defaultEmpty($this->pemeriksaan_penunjang),
                    'hasil_laborat' => $this->defaultEmpty($this->hasil_laborat),
                    'tindakan_dan_operasi' => $this->defaultEmpty($this->tindakan_dan_operasi),
                    'obat_di_rs' => $this->defaultEmpty($this->obat_di_rs),
                    'diagnosa_utama' => $this->defaultEmpty($this->diagnosa_utama),
                    'kd_diagnosa_utama' => $this->defaultEmpty($this->kd_diagnosa_utama),
                    'diagnosa_sekunder' => $this->defaultEmpty($this->diagnosa_sekunder),
                    'kd_diagnosa_sekunder' => $this->defaultEmpty($this->kd_diagnosa_sekunder),
                    'diagnosa_sekunder2' => $this->defaultEmpty($this->diagnosa_sekunder2),
                    'kd_diagnosa_sekunder2' => $this->defaultEmpty($this->kd_diagnosa_sekunder2),
                    'diagnosa_sekunder3' => $this->defaultEmpty($this->diagnosa_sekunder3),
                    'kd_diagnosa_sekunder3' => $this->defaultEmpty($this->kd_diagnosa_sekunder3),
                    'diagnosa_sekunder4' => $this->defaultEmpty($this->diagnosa_sekunder4),
                    'kd_diagnosa_sekunder4' => $this->defaultEmpty($this->kd_diagnosa_sekunder4),
                    'prosedur_utama' => $this->defaultEmpty($this->prosedur_utama),
                    'kd_prosedur_utama' => $this->defaultEmpty($this->kd_prosedur_utama),
                    'prosedur_sekunder' => $this->defaultEmpty($this->prosedur_sekunder),
                    'kd_prosedur_sekunder' => $this->defaultEmpty($this->kd_prosedur_sekunder),
                    'prosedur_sekunder2' => $this->defaultEmpty($this->prosedur_sekunder2),
                    'kd_prosedur_sekunder2' => $this->defaultEmpty($this->kd_prosedur_sekunder2),
                    'prosedur_sekunder3' => $this->defaultEmpty($this->prosedur_sekunder3),
                    'kd_prosedur_sekunder3' => $this->defaultEmpty($this->kd_prosedur_sekunder3),
                    'alergi' => $this->defaultEmpty($this->alergi),
                    'diet' => $this->defaultEmpty($this->diet),
                    'lab_belum' => $this->defaultEmpty($this->lab_belum),
                    'edukasi' => $this->defaultEmpty($this->edukasi),
                    'cara_keluar' => $this->defaultEmpty($this->cara_keluar),
                    'ket_keluar' => $this->defaultEmpty($this->ket_keluar),
                    'keadaan' => $this->defaultEmpty($this->keadaan),
                    'ket_keadaan' => $this->defaultEmpty($this->ket_keadaan),
                    'dilanjutkan' => $this->defaultEmpty($this->dilanjutkan),
                    'ket_dilanjutkan' => $this->defaultEmpty($this->ket_dilanjutkan),
                    'kontrol' => $this->kontrol ?? null,
                    'obat_pulang' => $this->defaultEmpty($this->obat_pulang),
                ]
            );

            $this->dispatch('swal', [
                'title' => 'Sukses!', 
                'text' => 'Resume medis casemix berhasil disimpan.',
                'icon' => 'success'
            ]);
            
            return $this->redirect(route('modul.casemix-rawat-inap.resume', str_replace('/', '-', $this->no_rawat)), navigate: true);
            
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Gagal Menyimpan',
                'text' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    private function defaultEmpty($value)
    {
        return empty($value) ? '-' : $value;
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

        return view('livewire.modul.casemix-rawat-inap.resume.form', [
            'icd10List' => $icd10List,
            'icd9List' => $icd9List,
        ]);
    }
}
