<?php

namespace App\Livewire\Modul\Farmasi;

use App\Models\Bangsal;
use App\Models\ResepDokter;
use App\Models\ResepObat;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Data Obat, Alkes dan BHP Medis'])]
class ObatAlkesBhp extends Component
{
    public ?string $no_resep = null;

    // Active Tab State
    public string $activeTab = 'umum'; // 'umum' or 'racikan'

    // Header Fields
    public string $no_rawat = '';
    public string $no_rkm_medis = '';
    public string $nm_pasien = '';

    public string $tgl_validasi = '';
    public string $jam_validasi = '';
    public string $menit_validasi = '';
    public string $detik_validasi = '';

    // Options Tarif sesuai rujukan: Rawat Jalan, Beli Luar, Karyawan, Utama/BPJS
    public string $tarif = 'Rawat Jalan';
    public array $optionsTarif = [
        'Rawat Jalan',
        'Beli Luar',
        'Karyawan',
        'Utama/BPJS',
    ];

    public bool $use_no_resep = true;

    public float $total = 0;
    public float $ppn = 0;
    public float $total_ppn = 0;

    // Depo / Kamar Fields
    public string $kd_depo = 'AP';
    public string $nm_depo = 'Apotek';

    // Lookup Depo / Kamar Modal
    public bool $isBangsalModalOpen = false;
    public string $searchBangsalModal = '';
    public array $listBangsal = [];

    // List Data
    public array $listObatUmum = [];
    public array $listObatRacikan = [];

    public function mount(?string $no_resep = null): void
    {
        $this->no_resep = $no_resep;
        $now = now();
        $this->tgl_validasi = $now->format('Y-m-d');
        $this->jam_validasi = $now->format('H');
        $this->menit_validasi = $now->format('i');
        $this->detik_validasi = $now->format('s');

        // Default Depo / Kamar di AP (Apotek)
        $apBangsal = Bangsal::where('kd_bangsal', 'AP')->first();
        if ($apBangsal) {
            $this->kd_depo = $apBangsal->kd_bangsal;
            $this->nm_depo = $apBangsal->nm_bangsal;
        } else {
            $this->kd_depo = 'AP';
            $this->nm_depo = 'Apotek';
        }

        if ($this->no_resep) {
            $resep = ResepObat::with([
                'regPeriksa.pasien',
                'detail.barang',
            ])->find($this->no_resep);

            if ($resep) {
                $this->no_rawat = $resep->no_rawat ?? '';
                $this->no_rkm_medis = $resep->regPeriksa->no_rkm_medis ?? '';
                $this->nm_pasien = $resep->regPeriksa->pasien->nm_pasien ?? '';

                if ($resep->status === 'ranap') {
                    $this->tarif = 'Utama/BPJS';
                } else {
                    $this->tarif = 'Rawat Jalan';
                }

                // Map list obat umum dari resep_dokter
                $totalHarga = 0;
                $mappedObat = [];

                foreach ($resep->detail as $item) {
                    $hargaSatuan = floatval($item->barang->ralan ?? ($item->barang->h_beli ?? 0));
                    $jml = floatval($item->jml ?? 1);
                    $subtotal = $hargaSatuan * $jml;
                    $totalHarga += $subtotal;

                    $mappedObat[] = [
                        'kode_brng'    => $item->kode_brng,
                        'nama_brng'    => $item->barang->nama_brng ?? $item->kode_brng,
                        'jumlah'       => $jml,
                        'satuan'       => $item->barang->kode_sat ?? '-',
                        'harga'        => $hargaSatuan,
                        'jenis_obat'   => $item->barang->kategori ?? 'SPECIAL',
                        'embalase'     => 0,
                        'tuslah'       => 0,
                        'stok'         => floatval($item->barang->stok ?? 0),
                        'aturan_pakai' => $item->aturan_pakai ?: '-',
                        'industri'     => '-',
                        'kategori'     => $item->barang->kategori ?? '-',
                        'golongan'     => '-',
                        'no_batch'     => '-',
                        'no_faktur'    => '-',
                        'kadaluarsa'   => '-',
                    ];
                }

                $this->listObatUmum = $mappedObat;
                $this->total = $totalHarga;
                $this->total_ppn = $this->total + $this->ppn;
            }
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // Modal Depo / Kamar Lookup
    public function openBangsalModal(): void
    {
        $this->searchBangsalModal = '';
        $this->loadListBangsal();
        $this->isBangsalModalOpen = true;
    }

    public function closeBangsalModal(): void
    {
        $this->isBangsalModalOpen = false;
    }

    public function updatedSearchBangsalModal(): void
    {
        $this->loadListBangsal();
    }

    public function loadListBangsal(): void
    {
        $query = Bangsal::query()->where('status', '1');
        if (!empty($this->searchBangsalModal)) {
            $s = "%{$this->searchBangsalModal}%";
            $query->where(function($q) use ($s) {
                $q->where('kd_bangsal', 'like', $s)
                  ->orWhere('nm_bangsal', 'like', $s);
            });
        }
        $this->listBangsal = $query->orderBy('nm_bangsal')->limit(50)->get()->toArray();
    }

    public function selectBangsal(string $kd, string $nm): void
    {
        $this->kd_depo = $kd;
        $this->nm_depo = $nm;
        $this->isBangsalModalOpen = false;
    }

    public function save(): void
    {
        // Placeholder function save dikosongkan terlebih dahulu
    }

    public function render()
    {
        return view('livewire.modul.farmasi.obat-alkes-bhp');
    }
}
