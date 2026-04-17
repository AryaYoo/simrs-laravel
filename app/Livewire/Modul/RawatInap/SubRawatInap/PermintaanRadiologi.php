<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap;

use App\Models\RegPeriksa;
use App\Models\JnsPerawatanRadiologi;
use App\Models\PermintaanRadiologi as PermintaanRadiologiModel;
use App\Models\PermintaanPemeriksaanRadiologi;
use App\Models\Dokter;
use App\Livewire\Concerns\WithOptimisticLocking;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermintaanRadiologi extends Component
{
    use WithPagination, WithOptimisticLocking;

    public $no_rawat;
    public $regPeriksa;
    
    // Perujuk / DPJP
    public $kd_dokter_perujuk;
    public $nm_dokter_perujuk;
    public $searchDokterModal = '';
    public $listDokter = [];
    public $isDokterModalOpen = false;

    // Time & Order
    public $tgl_permintaan;
    public $jam_permintaan_jam;
    public $jam_permintaan_menit;
    public $jam_permintaan_detik;
    public $auto_waktu = true;
    public $predictedOrderNo = '';

    // Form
    public $diagnosa_klinis = '-';
    public $informasi_tambahan = '-';
    public $searchPemeriksaan = '';
    public $selectedTests = []; // Array of kd_jenis_prw

    protected $listeners = ['refresh' => '$refresh'];

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'kamarInap.kamar.bangsal'])
            ->where('no_rawat', $this->no_rawat)
            ->first();

        if (!$this->regPeriksa) {
            abort(404, 'Data pasien tidak ditemukan.');
        }

        $this->kd_dokter_perujuk = $this->regPeriksa->kd_dokter;
        $this->nm_dokter_perujuk = $this->regPeriksa->dokter->nm_dokter ?? '-';
        $this->tgl_permintaan = date('Y-m-d');
        $this->syncWaktu();
        $this->updatePredictedOrderNo();

        // SOP: Initialize Lock
        $this->initializeLock($this->regPeriksa);
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
        if ($value) $this->syncWaktu();
    }

    public function updatedTglPermintaan()
    {
        $this->updatePredictedOrderNo();
    }

    public function updatePredictedOrderNo()
    {
        $dateStr = date('Ymd');
        $prefix = 'PR'; 

        $lastOrder = DB::table('permintaan_radiologi')
            ->where('noorder', 'like', $prefix . $dateStr . '%')
            ->orderBy('noorder', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNum = (int) substr($lastOrder->noorder, -4);
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        $this->predictedOrderNo = $prefix . $dateStr . $nextNum;
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
        $this->resetPage('pemeriksaanPage');
    }

    public function getPemeriksaanListProperty()
    {
        $query = JnsPerawatanRadiologi::where('status', '1');

        if ($this->searchPemeriksaan) {
            $query->where(function($q) {
                $q->where('nm_perawatan', 'like', '%' . $this->searchPemeriksaan . '%')
                  ->orWhere('kd_jenis_prw', 'like', '%' . $this->searchPemeriksaan . '%');
            });
        }

        return $query->paginate(15, ['*'], 'pemeriksaanPage');
    }

    public function save()
    {
        if (empty($this->selectedTests)) {
            $this->dispatch('swal', ['title' => 'Peringatan', 'text' => 'Pilih setidaknya satu pemeriksaan radiologi.', 'icon' => 'warning']);
            return;
        }

        try {
            // SOP: Validate Lock
            $this->validateLock($this->regPeriksa);

            DB::transaction(function () {
                // Generate Fresh No Order (with atomic lock)
                $dateStr = date('Ymd');
                $prefix = 'PR';

                $lastOrder = DB::table('permintaan_radiologi')
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

                // Insert Header
                DB::table('permintaan_radiologi')->insert([
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

                // Insert Details
                foreach ($this->selectedTests as $kd) {
                    DB::table('permintaan_pemeriksaan_radiologi')->insert([
                        'noorder' => $noorder,
                        'kd_jenis_prw' => $kd,
                        'stts_bayar' => 'Belum'
                    ]);
                }
            });

            $this->dispatch('swal', ['title' => 'Berhasil', 'text' => 'Permintaan radiologi berhasil dikirim.', 'icon' => 'success']);
            $this->selectedTests = [];
            $this->diagnosa_klinis = '-';
            $this->informasi_tambahan = '-';
            $this->syncWaktu();
            $this->updatePredictedOrderNo();

        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Kesalahan: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function getHistoryProperty()
    {
        return PermintaanRadiologiModel::with(['dokter', 'detailPemeriksaan.pemeriksaan'])
            ->where('no_rawat', $this->no_rawat)
            ->orderBy('tgl_permintaan', 'desc')
            ->orderBy('jam_permintaan', 'desc')
            ->get();
    }

    public function batalPermintaan($noorder)
    {
        try {
            $this->validateLock($this->regPeriksa);

            DB::transaction(function () use ($noorder) {
                $permintaan = DB::table('permintaan_radiologi')
                    ->where('noorder', $noorder)
                    ->lockForUpdate()
                    ->first();

                if (!$permintaan) throw new \Exception("Data permintaan tidak ditemukan.");

                if ($permintaan->tgl_sampel != '1000-01-01' && $permintaan->tgl_sampel != '0000-00-00') {
                    throw new \Exception("Permintaan tidak dapat dibatalkan karena sudah diproses oleh unit Radiologi.");
                }

                DB::table('permintaan_pemeriksaan_radiologi')->where('noorder', $noorder)->delete();
                DB::table('permintaan_radiologi')->where('noorder', $noorder)->delete();
            });

            $this->dispatch('swal', ['title' => 'Dibatalkan', 'text' => 'Permintaan radiologi berhasil dibatalkan.', 'icon' => 'success']);
            $this->dispatch('refresh');

        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function render()
    {
        return view('livewire.modul.rawat-inap.sub-rawat-inap.permintaan-radiologi', [
            'pemeriksaanList' => $this->pemeriksaanList,
            'history' => $this->history
        ])->layout('layouts.app', ['title' => 'Permintaan Radiologi']);
    }
}
