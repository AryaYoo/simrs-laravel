<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap;

use App\Models\RegPeriksa;
use App\Models\JnsPerawatanLab;
use App\Models\PermintaanLab;
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

    public $searchPemeriksaan = '';
    public $cart = [];
    public $diagnosa_klinis = '-';
    public $informasi_tambahan = '-';
    public $kategori = 'PK'; // Default Clinical Pathology

    protected $listeners = ['refresh' => '$refresh'];

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'kamarInap.kamar.bangsal'])->where('no_rawat', $this->no_rawat)->first();

        if (!$this->regPeriksa) {
            abort(404, 'Data pasien tidak ditemukan.');
        }

        $this->kd_dokter_perujuk = $this->regPeriksa->kd_dokter;
        $this->nm_dokter_perujuk = $this->regPeriksa->dokter->nm_dokter ?? '';

        $this->tgl_permintaan = date('Y-m-d');
        $this->syncWaktu();
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
        $this->resetPage();
    }

    public function getPemeriksaanListProperty()
    {
        $query = JnsPerawatanLab::where('status', '1')
            ->where('kategori', $this->kategori);

        if ($this->searchPemeriksaan) {
            $query->where('nm_perawatan', 'like', '%' . $this->searchPemeriksaan . '%');
        }

        // Exclude those already in cart
        $cartIds = collect($this->cart)->pluck('kd_jenis_prw')->toArray();
        if (!empty($cartIds)) {
            $query->whereNotIn('kd_jenis_prw', $cartIds);
        }

        return $query->paginate(12);
    }

    public function addToCart($kd_jenis_prw, $nm_perawatan, $total_byr)
    {
        // Prevent duplicate in cart
        if (collect($this->cart)->where('kd_jenis_prw', $kd_jenis_prw)->first()) {
            return;
        }

        $this->cart[] = [
            'kd_jenis_prw' => $kd_jenis_prw,
            'nm_perawatan' => $nm_perawatan,
            'total_byr' => $total_byr
        ];
    }

    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
    }

    public function save()
    {
        if (empty($this->cart)) {
            $this->dispatch('swal', [
                'title' => 'Gagal',
                'text' => 'Pilih setidaknya satu jenis pemeriksaan.',
                'icon' => 'error'
            ]);
            return;
        }

        try {
            DB::transaction(function () {
                $tglSekarang = Carbon::now();
                $dateStr = $tglSekarang->format('Ymd');
                $prefix = $this->kategori . $dateStr;

                // Atomic noorder generation
                $maxNoStr = DB::table('permintaan_lab')
                    ->where('noorder', 'like', $prefix . '%')
                    ->lockForUpdate()
                    ->max('noorder');

                if ($maxNoStr) {
                    $lastNo = (int) substr($maxNoStr, -4);
                    $newNo = $lastNo + 1;
                } else {
                    $newNo = 1;
                }

                $noorder = $prefix . sprintf('%04d', $newNo);

                // Build time string
                if ($this->auto_waktu) {
                    $jamF = date('H:i:s');
                } else {
                    $jamF = sprintf('%02d:%02d:%02d', $this->jam_permintaan_jam, $this->jam_permintaan_menit, $this->jam_permintaan_detik);
                }

                // Insert into permintaan_lab (header)
                DB::table('permintaan_lab')->insert([
                    'noorder' => $noorder,
                    'no_rawat' => $this->no_rawat,
                    'tgl_permintaan' => $this->tgl_permintaan,
                    'jam_permintaan' => $jamF,
                    'tgl_sampel' => '0000-00-00',
                    'jam_sampel' => '00:00:00',
                    'tgl_hasil' => '0000-00-00',
                    'jam_hasil' => '00:00:00',
                    'dokter_perujuk' => $this->kd_dokter_perujuk,
                    'status' => 'ranap',
                    'informasi_tambahan' => $this->informasi_tambahan ?: '-',
                    'diagnosa_klinis' => $this->diagnosa_klinis ?: '-'
                ]);

                // Insert into permintaan_pemeriksaan_lab (detail)
                foreach ($this->cart as $item) {
                    DB::table('permintaan_pemeriksaan_lab')->insert([
                        'noorder' => $noorder,
                        'kd_jenis_prw' => $item['kd_jenis_prw'],
                        'stts_bayar' => 'Belum'
                    ]);
                }
            });

            $this->dispatch('swal', [
                'title' => 'Berhasil',
                'text' => 'Permintaan pemeriksaan laboratorium berhasil dikirim.',
                'icon' => 'success'
            ]);

            $this->cart = [];
            $this->diagnosa_klinis = '-';
            $this->informasi_tambahan = '-';
            $this->syncWaktu(); // Update to newest current time if success

        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Gagal',
                'text' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.modul.rawat-inap.sub-rawat-inap.permintaan-laboratorium', [
            'pemeriksaanList' => $this->pemeriksaanList
        ])->layout('layouts.app', ['title' => 'Permintaan Laboratorium']);
    }
}
