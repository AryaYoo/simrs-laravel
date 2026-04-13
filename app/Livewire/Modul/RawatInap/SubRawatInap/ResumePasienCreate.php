<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap;

use App\Models\RegPeriksa;
use App\Models\ResumePasienRanap;
use App\Models\PemeriksaanRanap;
use App\Models\PemeriksaanRalan;
use App\Models\DetailPeriksaLab;
use App\Models\PeriksaRadiologi;
use App\Models\RawatInapDr;
use App\Models\RawatInapPr;
use App\Models\RawatInapDrpr;
use App\Models\DetailPemberianObat;
use App\Models\Penyakit;
use App\Models\Icd9;
use App\Models\Diet;
use App\Models\DetailBeriDiet;
use App\Models\PermintaanLab;
use App\Models\ResepPulang;
use App\Livewire\Concerns\WithOptimisticLocking;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Buat Resume Medis'])]
class ResumePasienCreate extends Component
{
    use WithPagination, WithOptimisticLocking;

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
    public $targetIcdField = ''; // Which field are we picking for? (e.g. 'diagnosa_utama')

    // Attach History State
    public $historyItems = [];
    public $selectedHistoryItems = [];
    public $activeAttachField = '';
    public $currentAttachType = ''; // 'SOAP', 'LAB', 'RAD', 'TINDAKAN', 'OBAT'

    // Autocomplete State
    public $activeSearchField = '';
    public $autocompleteResults = [];
    protected $isSelecting = false;

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'kamarInap.kamar.bangsal', 'diagnosaPasien.penyakit'])->findOrFail($this->no_rawat);
        $this->kd_dokter = $this->regPeriksa->kd_dokter;
        
        // Default values for dropdowns
        $this->keadaan = 'Membaik';
        $this->cara_keluar = 'Atas Izin Dokter';
        $this->dilanjutkan = 'Kembali Ke RS';
        $this->kontrol = now()->addDays(7)->format('Y-m-d H:i:s');

        // Auto-Fill Logic
        $this->autoFillData();

        // SOP: Initialize lock for concurrency control
        $this->initializeLock($this->regPeriksa);
    }

    public function autoFillData()
    {
        // 1. Fetch Latest Clinical Record (Pemeriksaan Ranap)
        $latestPemeriksaan = PemeriksaanRanap::where('no_rawat', $this->no_rawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->first();

        if ($latestPemeriksaan) {
            $this->keluhan_utama = $latestPemeriksaan->keluhan;
            $this->pemeriksaan_fisik = $latestPemeriksaan->pemeriksaan;
            $this->alergi = $latestPemeriksaan->alergi;
        }

        // 2. Fetch Latest Ward Info for Diagnosa Awal
        $rooms = $this->regPeriksa->kamarInap;
        if ($rooms->count() > 0) {
            $this->diagnosa_awal = $rooms->first()->diagnosa_awal;
        }

        // 3. Fetch Existing Diagnoses
        $diagnoses = $this->regPeriksa->diagnosaPasien;
        
        // Primary
        $primary = $diagnoses->where('prioritas', 1)->first();
        if ($primary) {
            $this->kd_diagnosa_utama = $primary->kd_penyakit;
            $this->diagnosa_utama = $primary->penyakit->nm_penyakit;
        }

        // Secondaries
        $secondaries = $diagnoses->where('prioritas', '>', 1)->sortBy('prioritas')->values();
        for ($i = 0; $i < 4; $i++) {
            if (isset($secondaries[$i])) {
                $fieldKd = 'kd_diagnosa_sekunder' . ($i === 0 ? '' : ($i + 1));
                $fieldName = 'diagnosa_sekunder' . ($i === 0 ? '' : ($i + 1));
                $this->$fieldKd = $secondaries[$i]->kd_penyakit;
                $this->$fieldName = $secondaries[$i]->penyakit->nm_penyakit;
            }
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

    public function openAttachModal($type, $field)
    {
        $this->currentAttachType = $type;
        $this->activeAttachField = $field;
        $this->selectedHistoryItems = [];
        $this->historyItems = [];

        try {
            if ($type === 'KELUHAN') {
                // Keluhan Utama diambil dari pemeriksaan_ralan (kolom keluhan)
                $this->historyItems = PemeriksaanRalan::where('no_rawat', $this->no_rawat)
                    ->orderBy('tgl_perawatan', 'desc')
                    ->orderBy('jam_rawat', 'desc')
                    ->get()
                    ->map(fn($item) => [
                        'id' => 'ralan_kel_' . $item->tgl_perawatan . '_' . $item->jam_rawat,
                        'keluhan' => $item->keluhan,
                        'tgl_perawatan' => $item->tgl_perawatan,
                        'jam_rawat' => $item->jam_rawat,
                    ])->values()->toArray();
            } elseif ($type === 'PEMERIKSAAN') {
                // Pemeriksaan Fisik diambil dari pemeriksaan_ralan
                $this->historyItems = PemeriksaanRalan::where('no_rawat', $this->no_rawat)
                    ->orderBy('tgl_perawatan', 'desc')
                    ->orderBy('jam_rawat', 'desc')
                    ->get()
                    ->map(fn($item) => [
                        'id' => 'ralan_' . $item->tgl_perawatan . '_' . $item->jam_rawat,
                        'pemeriksaan' => $item->pemeriksaan,
                        'tgl_perawatan' => $item->tgl_perawatan,
                        'jam_rawat' => $item->jam_rawat,
                    ])->values()->toArray();
            } elseif ($type === 'SOAP') {
                $this->historyItems = PemeriksaanRanap::where('no_rawat', $this->no_rawat)
                    ->orderBy('tgl_perawatan', 'desc')
                    ->orderBy('jam_rawat', 'desc')
                    ->get()
                    ->map(fn($item) => [
                        'id' => 'ranap_soap_' . $item->tgl_perawatan . '_' . $item->jam_rawat,
                        'keluhan' => $item->keluhan,
                        'pemeriksaan' => $item->pemeriksaan,
                        'tgl_perawatan' => $item->tgl_perawatan,
                        'jam_rawat' => $item->jam_rawat,
                    ])->values()->toArray();
            } elseif ($type === 'LAB') {
                $this->historyItems = DetailPeriksaLab::with('template')
                    ->where('no_rawat', $this->no_rawat)
                    ->get()
                    ->map(function($item) {
                        $ref = [];
                        if ($item->template?->nilai_rujukan_ld) $ref[] = "L:" . $item->template->nilai_rujukan_ld;
                        if ($item->template?->nilai_rujukan_pd) $ref[] = "P:" . $item->template->nilai_rujukan_pd;
                        
                        $nilaiNormal = !empty($ref) ? implode("; ", $ref) : null;
                        
                        return [
                            'id' => $item->id_template . '_' . $item->tgl_periksa . '_' . $item->jam,
                            'name' => $item->template?->Pemeriksaan ?? '-',
                            'nilai' => $item->nilai,
                            'satuan' => $item->template?->satuan ?? '',
                            'label' => ($item->template?->Pemeriksaan ?? '-') . ': ' . $item->nilai . ' ' . ($item->template?->satuan ?? ''),
                            'nilai_normal' => $nilaiNormal,
                            'date' => $item->tgl_periksa . ' ' . $item->jam
                        ];
                    })->values()->toArray();
            } elseif ($type === 'RAD') {
                $this->historyItems = PeriksaRadiologi::with('jnsPerawatan')
                    ->where('no_rawat', $this->no_rawat)
                    ->get()
                    ->map(fn($item) => [
                        'id' => $item->kd_jenis_prw . '_' . $item->tgl_periksa . '_' . $item->jam,
                        'label' => ($item->jnsPerawatan?->nm_perawatan ?? '-') . ' (Hasil: ' . $item->hasil . ')',
                        'text' => $item->hasil,
                        'date' => $item->tgl_periksa . ' ' . $item->jam
                    ])->values()->toArray();
            } elseif ($type === 'TINDAKAN') {
                // Combine from 3 tables
                $dr = RawatInapDr::with('jnsPerawatan')->where('no_rawat', $this->no_rawat)->get();
                $pr = RawatInapPr::with('jnsPerawatan')->where('no_rawat', $this->no_rawat)->get();
                $drpr = RawatInapDrpr::with('jnsPerawatan')->where('no_rawat', $this->no_rawat)->get();

                $this->historyItems = $dr->concat($pr)->concat($drpr)
                    ->map(fn($item) => [
                        'id' => $item->kd_jenis_prw . '_' . $item->tgl_perawatan . '_' . ($item->jam_rawat ?? ''),
                        'label' => $item->jnsPerawatan?->nm_perawatan ?? '-',
                        'date' => $item->tgl_perawatan . ' ' . ($item->jam_rawat ?? '')
                    ])->values()->toArray();
            } elseif ($type === 'OBAT') {
                $this->historyItems = DetailPemberianObat::with('barang')
                    ->where('no_rawat', $this->no_rawat)
                    ->get()
                    ->groupBy('kode_brng')
                    ->map(fn(\Illuminate\Support\Collection $items, $kode) => [
                        'id' => $kode,
                        'label' => ($items->first()->barang?->nama_brng ?? '-') . ' (Total: ' . $items->sum('jml') . ' ' . ($items->first()->barang?->kode_sat ?? '') . ')',
                        'name' => $items->first()->barang?->nama_brng ?? '-'
                    ])->values()->toArray();
            } elseif ($type === 'DIET') {
                $this->historyItems = DetailBeriDiet::with('diet')
                    ->where('no_rawat', $this->no_rawat)
                    ->orderBy('tanggal', 'desc')
                    ->get()
                    ->map(fn($item) => [
                        'id' => $item->kd_diet . '_' . $item->tanggal . '_' . $item->waktu,
                        'label' => ($item->diet?->nama_diet ?? '-') . ' (' . $item->tanggal . ' ' . $item->waktu . ')',
                        'name' => $item->diet?->nama_diet ?? '-',
                        'date' => $item->tanggal . ' ' . $item->waktu
                    ])->values()->toArray();
            } elseif ($type === 'LAB_PENDING') {
                $this->historyItems = PermintaanLab::with('detailPemeriksaan.jnsPemeriksaan')
                    ->where('no_rawat', $this->no_rawat)
                    ->orderBy('tgl_permintaan', 'desc')
                    ->get()
                    ->map(fn($item) => [
                        'id' => $item->noorder,
                        'label' => 'Order: ' . $item->noorder . ' (' . $item->detailPemeriksaan->map(fn($d) => $d->jnsPemeriksaan->nm_perawatan ?? '-')->implode(', ') . ')',
                        'text' => $item->detailPemeriksaan->map(fn($d) => $d->jnsPemeriksaan->nm_perawatan ?? '-')->implode(', '),
                        'date' => $item->tgl_permintaan . ' ' . $item->jam_permintaan
                    ])->values()->toArray();
            } elseif ($type === 'OBAT_PULANG') {
                $this->historyItems = ResepPulang::with('barang')
                    ->where('no_rawat', $this->no_rawat)
                    ->orderBy('tanggal', 'desc')
                    ->get()
                    ->map(fn($item) => [
                        'id' => $item->kode_brng . '_' . $item->tanggal . '_' . $item->jam,
                        'label' => ($item->barang?->nama_brng ?? '-') . ' (' . $item->dosis . ')',
                        'text' => ($item->barang?->nama_brng ?? '-') . ' (' . $item->dosis . ')',
                        'date' => $item->tanggal . ' ' . $item->jam
                    ])->values()->toArray();
            }
        } catch (\Exception $e) {
            \Log::error('Attach History Error: ' . $e->getMessage());
            $this->dispatch('notify', variant: 'error', message: 'Gagal mengambil riwayat: ' . $e->getMessage());
        }

        $this->modal('attach-history-modal')->show();
    }

    public function confirmAttach()
    {
        if (empty($this->selectedHistoryItems)) return;

        $textToAttach = "";
        
        foreach ($this->selectedHistoryItems as $val) {
            $item = collect($this->historyItems)->firstWhere('id', $val);
            if (!$item) {
                // Special case for SOAP if ID is index
                $item = $this->historyItems[$val] ?? null;
            }

            if ($item) {
                if ($this->currentAttachType === 'SOAP') {
                    $textToAttach .= ($this->activeAttachField === 'keluhan_utama' ? $item['keluhan'] : $item['pemeriksaan']) . "\n";
                } elseif ($this->currentAttachType === 'KELUHAN') {
                    $textToAttach .= $item['keluhan'] . "\n";
                } elseif ($this->currentAttachType === 'PEMERIKSAAN') {
                    $textToAttach .= $item['pemeriksaan'] . "\n";
                } elseif ($this->currentAttachType === 'LAB') {
                    $textToAttach .= $item['label'] . ($item['nilai_normal'] ? " (NN: " . $item['nilai_normal'] . ")" : "") . "\n";
                } elseif ($this->currentAttachType === 'RAD') {
                    $textToAttach .= $item['text'] . "\n";
                } elseif ($this->currentAttachType === 'TINDAKAN') {
                    $textToAttach .= $item['label'] . "\n";
                } elseif ($this->currentAttachType === 'OBAT') {
                    $textToAttach .= $item['name'] . ", ";
                } elseif ($this->currentAttachType === 'DIET') {
                    $textToAttach .= $item['name'] . "\n";
                } elseif ($this->currentAttachType === 'LAB_PENDING') {
                    $textToAttach .= $item['text'] . "\n";
                } elseif ($this->currentAttachType === 'OBAT_PULANG') {
                    $textToAttach .= $item['text'] . "\n";
                }
            }
        }

        if ($this->currentAttachType === 'OBAT') {
            $textToAttach = rtrim($textToAttach, ", ");
        }

        $field = $this->activeAttachField;
        if (!empty($this->$field)) {
            $this->$field .= "\n" . $textToAttach;
        } else {
            $this->$field = $textToAttach;
        }

        $this->modal('attach-history-modal')->close();
        $this->selectedHistoryItems = [];
    }

    public function save()
    {
        $this->validate([
            'kd_dokter' => 'required',
            'diagnosa_utama' => 'required',
        ]);

        // SOP: Validate lock before saving
        $this->validateLock($this->regPeriksa);

        try {
            ResumePasienRanap::updateOrCreate(
                ['no_rawat' => $this->no_rawat],
                [
                    'kd_dokter' => $this->kd_dokter ?? '',
                    'diagnosa_awal' => $this->diagnosa_awal ?? '',
                    'alasan' => $this->alasan ?? '',
                    'keluhan_utama' => $this->keluhan_utama ?? '',
                    'pemeriksaan_fisik' => $this->pemeriksaan_fisik ?? '',
                    'jalannya_penyakit' => $this->jalannya_penyakit ?? '',
                    'pemeriksaan_penunjang' => $this->pemeriksaan_penunjang ?? '',
                    'hasil_laborat' => $this->hasil_laborat ?? '',
                    'tindakan_dan_operasi' => $this->tindakan_dan_operasi ?? '',
                    'obat_di_rs' => $this->obat_di_rs ?? '',
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
                    'alergi' => $this->alergi ?? '',
                    'diet' => $this->diet ?? '',
                    'lab_belum' => $this->lab_belum ?? '',
                    'edukasi' => $this->edukasi ?? '',
                    'cara_keluar' => $this->cara_keluar ?? '',
                    'ket_keluar' => $this->ket_keluar ?? '',
                    'keadaan' => $this->keadaan ?? '',
                    'ket_keadaan' => $this->ket_keadaan ?? '',
                    'dilanjutkan' => $this->dilanjutkan ?? '',
                    'ket_dilanjutkan' => $this->ket_dilanjutkan ?? '',
                    'kontrol' => $this->kontrol ?? '',
                    'obat_pulang' => $this->obat_pulang ?? '',
                ]
            );

            session()->flash('message', 'Resume medis berhasil disimpan.');
            $this->dispatch('swal', [
                'title' => 'Sukses!', 
                'text' => 'Resume medis berhasil disimpan.',
                'icon' => 'success'
            ]);
            
            return $this->redirect(route('modul.rawat-inap.sub-rawat-inap.resume', str_replace('/', '-', $this->no_rawat)), navigate: true);
            
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

        return view('livewire.modul.rawat-inap.sub-rawat-inap.resume-pasien-create', [
            'icd10List' => $icd10List,
            'icd9List' => $icd9List,
        ]);
    }
}
