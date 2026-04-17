<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap;

use App\Models\RegPeriksa;
use App\Models\JnsPerawatanLab;
use App\Models\PermintaanLab;
use App\Models\PermintaanLabPa;
use App\Models\PermintaanPemeriksaanLabPa;
use App\Models\PermintaanPemeriksaanLab;
use App\Models\Dokter;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermintaanLaboratorium extends Component
{
    use WithPagination;

    public $no_rawat;
    public $regPeriksa;
    
    // Perujuk
    public $kd_dokter_perujuk;
    public $nm_dokter_perujuk;
    public $searchDokterModal = '';
    public $listDokter = [];
    public $isDokterModalOpen = false;

    // Time Control
    public $tgl_permintaan;
    public $jam_permintaan_jam;
    public $jam_permintaan_menit;
    public $jam_permintaan_detik;
    public $auto_waktu = true;

    // Tab & Categories
    public $kategori = 'PK'; // PK, PA, MB
    
    // Left Table (Pemeriksaan Master)
    public $searchPemeriksaan = '';
    public $selectedTests = []; // Array of kd_jenis_prw
    
    // Right Table (Detail Parameters)
    public $searchDetail = '';
    public $selectedDetails = []; // Array of id_template
    
    public $diagnosa_klinis = '-';
    public $informasi_tambahan = '-';
    public $predictedOrderNo = '';

    protected $listeners = ['refresh' => '$refresh'];

    // --- PA Form Fields ---
    public $pa_pengambilan_bahan, $pa_diperoleh_dengan, $pa_lokasi_jaringan, $pa_diawetkan_dengan;
    public $pa_pernah_dilakukan_di, $pa_tanggal_sebelumnya, $pa_nomor_sebelumnya, $pa_diagnosa_sebelumnya;

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'kamarInap.kamar.bangsal'])->where('no_rawat', $this->no_rawat)->first();

        if (!$this->regPeriksa) {
            abort(404, 'Data pasien tidak ditemukan.');
        }

        $this->kd_dokter_perujuk = $this->regPeriksa->kd_dokter;
        $this->tgl_permintaan = date('Y-m-d');
        $this->pa_pengambilan_bahan = date('Y-m-d');
        $this->pa_tanggal_sebelumnya = date('Y-m-d');
        $this->syncWaktu();
        $this->updatePredictedOrderNo();
    }

    public function updatedKategori()
    {
        $this->selectedTests = [];
        $this->selectedDetails = [];
        $this->searchPemeriksaan = '';
        $this->searchDetail = '';
        $this->updatePredictedOrderNo();
    }

    public function updatePredictedOrderNo()
    {
        $dateStr = date('Ymd');
        $prefix = $this->kategori; // Bisa PK, PA, atau MB
        $mainTable = $this->kategori === 'PA' ? 'permintaan_labpa' : 'permintaan_lab';
        
        // Cari noorder terakhir untuk hari ini dengan prefix yang sesuai
        $lastOrder = DB::table($mainTable)
            ->where('noorder', 'like', $prefix . $dateStr . '%')
            ->orderBy('noorder', 'desc')
            ->first();

        // Fallback untuk PA karena Khanza terkadang campur atau simpan di tempat berbeda
        if ($this->kategori === 'PA' && !$lastOrder) {
            $lastOrder = DB::table('permintaan_lab')
                ->where('noorder', 'like', 'PA' . $dateStr . '%')
                ->orderBy('noorder', 'desc')
                ->first();
        }

        if ($lastOrder) {
            $lastNum = (int) substr($lastOrder->noorder, -4);
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        $this->predictedOrderNo = $prefix . $dateStr . $nextNum;
    }

    public function syncWaktu()
    {
        if ($this->auto_waktu) {
            $this->jam_permintaan_jam = date('H');
            $this->jam_permintaan_menit = date('i');
            $this->jam_permintaan_detik = date('s');
        }
    }

    public function updatedAutoWaktu($value)
    {
        if ($value) {
            $this->syncWaktu();
        }
    }

    public function updatedTglPermintaan()
    {
        $this->updatePredictedOrderNo();
    }

    public function openDokterModal()
    {
        $this->searchDokterModal = '';
        $this->loadListDokter();
        $this->isDokterModalOpen = true;
    }

    public function loadListDokter()
    {
        $query = Dokter::where('status', '1');
        if (!empty($this->searchDokterModal)) {
            $query->where('nm_dokter', 'like', '%' . $this->searchDokterModal . '%');
        }
        $this->listDokter = $query->limit(20)->get()->toArray();
    }

    public function updatedSearchDokterModal()
    {
        $this->loadListDokter();
    }

    public function selectDokter($kd_dokter, $nm_dokter)
    {
        $this->kd_dokter_perujuk = $kd_dokter;
        $this->nm_dokter_perujuk = $nm_dokter;
        $this->isDokterModalOpen = false;
    }

    public function updatedSearchPemeriksaan()
    {
        $this->resetPage('masterPage');
    }

    public function updatedSelectedTests($values)
    {
        $allTemplateIds = \App\Models\TemplateLaboratorium::whereIn('kd_jenis_prw', $this->selectedTests)
            ->pluck('id_template')
            ->map(fn($id) => (string)$id)
            ->toArray();
            
        $this->selectedDetails = $allTemplateIds;
    }

    public function toggleGroup($kd_jenis_prw)
    {
        $groupIds = \App\Models\TemplateLaboratorium::where('kd_jenis_prw', $kd_jenis_prw)
            ->pluck('id_template')
            ->map(fn($id) => (string)$id)
            ->toArray();

        $isAllSelected = collect($groupIds)->every(fn($id) => in_array($id, $this->selectedDetails));

        if ($isAllSelected) {
            $this->selectedDetails = array_diff($this->selectedDetails, $groupIds);
        } else {
            $this->selectedDetails = array_unique(array_merge($this->selectedDetails, $groupIds));
        }
    }

    public function getPemeriksaanListProperty()
    {
        $query = JnsPerawatanLab::where('status', '1')
            ->where('kategori', $this->kategori);

        if ($this->searchPemeriksaan) {
            $query->where(function($q) {
                $q->where('nm_perawatan', 'like', '%' . $this->searchPemeriksaan . '%')
                  ->orWhere('kd_jenis_prw', 'like', '%' . $this->searchPemeriksaan . '%');
            });
        }

        return $query->paginate(15, ['*'], 'masterPage');
    }

    public function getDetailParametersProperty()
    {
        if (empty($this->selectedTests)) return collect([]);

        $query = \App\Models\TemplateLaboratorium::whereIn('kd_jenis_prw', $this->selectedTests);

        if ($this->searchDetail) {
            $query->where('Pemeriksaan', 'like', '%' . $this->searchDetail . '%');
        }

        return $query->orderBy('kd_jenis_prw')->orderBy('urut')->get();
    }

    public function toggleAllDetails($checked = true)
    {
        if ($checked) {
            $this->selectedDetails = $this->detailParameters->pluck('id_template')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedDetails = [];
        }
    }

    public function save()
    {
        if (empty($this->selectedTests) && empty($this->selectedDetails)) {
            $this->dispatch('swal', ['title' => 'Peringatan', 'text' => 'Pilih setidaknya satu pemeriksaan.', 'icon' => 'warning']);
            return;
        }

        try {
            DB::transaction(function () {
                // Generate Fresh No Order (Locking)
                $dateStr = date('Ymd');
                $prefix = $this->kategori; 
                $mainTable = $this->kategori === 'PA' ? 'permintaan_labpa' : 'permintaan_lab';

                $lastOrder = DB::table($mainTable)
                    ->where('noorder', 'like', $prefix . $dateStr . '%')
                    ->orderBy('noorder', 'desc')
                    ->lockForUpdate()
                    ->first();

                if ($lastOrder) {
                    $lastNum = (int) substr($lastOrder->noorder, -4);
                    $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $nextNum = '0001';
                }

                $noorder = $prefix . $dateStr . $nextNum;

                if ($this->auto_waktu) {
                    $jamF = date('H:i:s');
                } else {
                    $jamF = sprintf('%02d:%02d:%02d', $this->jam_permintaan_jam, $this->jam_permintaan_menit, $this->jam_permintaan_detik);
                }

                if ($this->kategori === 'PA') {
                    // Simpan ke Header PA
                    DB::table('permintaan_labpa')->insert([
                        'noorder' => $noorder,
                        'no_rawat' => $this->no_rawat,
                        'tgl_permintaan' => $this->tgl_permintaan,
                        'jam_permintaan' => $jamF,
                        'tgl_sampel' => '1000-01-01',
                        'jam_sampel' => '00:00:00',
                        'tgl_hasil' => '1000-01-01',
                        'jam_hasil' => '00:00:00',
                        'dokter_perujuk' => $this->kd_dokter_perujuk,
                        'status' => 'ranap',
                        'informasi_tambahan' => $this->informasi_tambahan ?: '-',
                        'diagnosa_klinis' => $this->diagnosa_klinis ?: '-',
                        'pengambilan_bahan' => $this->pa_pengambilan_bahan,
                        'diperoleh_dengan' => $this->pa_diperoleh_dengan ?: '-',
                        'lokasi_jaringan' => $this->pa_lokasi_jaringan ?: '-',
                        'diawetkan_dengan' => $this->pa_diawetkan_dengan ?: '-',
                        'pernah_dilakukan_di' => $this->pa_pernah_dilakukan_di ?: '-',
                        'tanggal_pa_sebelumnya' => $this->pa_tanggal_sebelumnya,
                        'nomor_pa_sebelumnya' => $this->pa_nomor_sebelumnya ?: '-',
                        'diagnosa_pa_sebelumnya' => $this->pa_diagnosa_sebelumnya ?: '-'
                    ]);


                    // Simpan Item Pemeriksaan PA
                    foreach ($this->selectedTests as $kd) {
                        DB::table('permintaan_pemeriksaan_labpa')->insert([
                            'noorder' => $noorder,
                            'kd_jenis_prw' => $kd,
                            'stts_bayar' => 'Belum'
                        ]);
                    }

                } else {
                    // Header PK
                    DB::table('permintaan_lab')->insert([
                        'noorder' => $noorder,
                        'no_rawat' => $this->no_rawat,
                        'tgl_permintaan' => $this->tgl_permintaan,
                        'jam_permintaan' => $jamF,
                        'tgl_sampel' => '1000-01-01',
                        'jam_sampel' => '00:00:00',
                        'tgl_hasil' => '1000-01-01',
                        'jam_hasil' => '00:00:00',
                        'dokter_perujuk' => $this->kd_dokter_perujuk,
                        'status' => 'ranap',
                        'informasi_tambahan' => $this->informasi_tambahan ?: '-',
                        'diagnosa_klinis' => $this->diagnosa_klinis ?: '-'
                    ]);

                    // Tests PK
                    $testIdsToSave = \App\Models\TemplateLaboratorium::whereIn('id_template', $this->selectedDetails)
                        ->distinct()
                        ->pluck('kd_jenis_prw');

                    foreach ($testIdsToSave as $kd) {
                        DB::table('permintaan_pemeriksaan_lab')->insert([
                            'noorder' => $noorder,
                            'kd_jenis_prw' => $kd,
                            'stts_bayar' => 'Belum'
                        ]);
                    }

                    // Parameters PK
                    foreach ($this->selectedDetails as $id_template) {
                        $template = \App\Models\TemplateLaboratorium::find($id_template);
                        DB::table('permintaan_detail_permintaan_lab')->insert([
                            'noorder' => $noorder,
                            'kd_jenis_prw' => $template->kd_jenis_prw,
                            'id_template' => $id_template,
                            'stts_bayar' => 'Belum'
                        ]);
                    }
                }
            });

            $this->dispatch('swal', ['title' => 'Berhasil', 'text' => 'Permintaan lab berhasil dikirim.', 'icon' => 'success']);
            $this->selectedTests = [];
            $this->selectedDetails = [];
            $this->diagnosa_klinis = '-';
            $this->informasi_tambahan = '-';
            $this->reset(['pa_diperoleh_dengan', 'pa_lokasi_jaringan', 'pa_diawetkan_dengan', 'pa_pernah_dilakukan_di', 'pa_nomor_sebelumnya', 'pa_diagnosa_sebelumnya']);
            $this->syncWaktu();
            $this->updatePredictedOrderNo();

        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Kesalahan: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function getPemeriksaanHistoryProperty()
    {
        $historyPK = PermintaanLab::with(['dokter', 'detailPemeriksaan.pemeriksaan'])
            ->where('no_rawat', $this->no_rawat)
            ->get()
            ->map(function($item) {
                $item->tipe = 'PK';
                return $item;
            });

        $historyPA = PermintaanLabPa::with(['dokter', 'detailPemeriksaan.pemeriksaan'])
            ->where('no_rawat', $this->no_rawat)
            ->get()
            ->map(function($item) {
                $item->tipe = 'PA';
                return $item;
            });

        return $historyPK->concat($historyPA)
            ->sortByDesc(function($item) {
                return $item->tgl_permintaan . ' ' . $item->jam_permintaan;
            })
            ->values();
    }

    public function batalPermintaan($noorder)
    {
        try {
            $isPA = str_starts_with($noorder, 'PA');
            $headerTable = $isPA ? 'permintaan_labpa' : 'permintaan_lab';
            $itemTable = $isPA ? 'permintaan_pemeriksaan_labpa' : 'permintaan_pemeriksaan_lab';
            $detailTable = 'permintaan_detail_permintaan_lab'; // Hanya untuk PK

            DB::transaction(function () use ($noorder, $isPA, $headerTable, $itemTable, $detailTable) {
                $permintaan = DB::table($headerTable)
                    ->where('noorder', $noorder)
                    ->lockForUpdate()
                    ->first();

                if (!$permintaan) {
                    throw new \Exception("Data permintaan tidak ditemukan.");
                }

                // Proteksi: Jika sudah diproses (tgl_sampel di PK, atau status lain di PA)
                // Di PA, Khanza terkadang tidak punya tgl_sampel yang sama perilakunya, 
                // tapi kita asumsikan jika tgl_permintaan sudah lama mungkin sudah diproses.
                // Namun untuk amannya gunakan logika placeholder yang sama jika kolomnya ada.
                if (isset($permintaan->tgl_sampel)) {
                    if ($permintaan->tgl_sampel != '1000-01-01' && $permintaan->tgl_sampel != '0000-00-00') {
                        throw new \Exception("Permintaan tidak dapat dibatalkan karena sudah diproses.");
                    }
                }

                // Hapus Items
                if (!$isPA) {
                    DB::table($detailTable)->where('noorder', $noorder)->delete();
                }
                DB::table($itemTable)->where('noorder', $noorder)->delete();
                
                // Hapus Header
                DB::table($headerTable)->where('noorder', $noorder)->delete();
            });

            $this->dispatch('swal', ['title' => 'Dibatalkan', 'text' => 'Permintaan lab berhasil dibatalkan.', 'icon' => 'success']);
            $this->dispatch('refresh');

        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function render()

    {
        return view('livewire.modul.rawat-inap.sub-rawat-inap.permintaan-laboratorium', [
            'pemeriksaanList' => $this->pemeriksaanList,
            'detailParameters' => $this->detailParameters
        ])->layout('layouts.app', ['title' => 'Permintaan Laboratorium']);
    }

}
