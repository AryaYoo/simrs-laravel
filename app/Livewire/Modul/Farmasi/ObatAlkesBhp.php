<?php

namespace App\Livewire\Modul\Farmasi;

use App\Models\Bangsal;
use App\Models\DataBarang;
use App\Models\GudangBarang;
use App\Models\ResepDokter;
use App\Models\ResepObat;
use Illuminate\Support\Facades\DB;
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

    // Lookup Obat Modal
    public bool $isObatModalOpen = false;
    public string $searchObatModal = '';
    public array $listObatSearch = [];
    public array $selectedObatModal = [];

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
                $mappedObat = [];

                foreach ($resep->detail as $item) {
                    $hargaSatuan = floatval($item->barang->ralan ?? ($item->barang->h_beli ?? 0));
                    $jml = floatval($item->jml ?? 1);

                    // Cek stok depo spesifik dari gudangbarang jika ada
                    $stokDepo = floatval($item->barang->stok ?? 0);
                    $gudang = GudangBarang::where('kode_brng', $item->kode_brng)
                        ->where('kd_bangsal', $this->kd_depo)
                        ->first();
                    if ($gudang) {
                        $stokDepo = floatval($gudang->stok);
                    }

                    $mappedObat[] = [
                        'kode_brng'    => $item->kode_brng,
                        'nama_brng'    => $item->barang->nama_brng ?? $item->kode_brng,
                        'jumlah'       => $jml,
                        'satuan'       => $item->barang->kode_sat ?? '-',
                        'harga'        => $hargaSatuan,
                        'jenis_obat'   => $item->barang->kategori ?? 'SPECIAL',
                        'embalase'     => 0,
                        'tuslah'       => 0,
                        'stok'         => $stokDepo,
                        'aturan_pakai' => $item->aturan_pakai ?: '-',
                        'industri'     => '-',
                        'kategori'     => $item->barang->kategori ?? '-',
                        'golongan'     => '-',
                        'no_batch'     => $gudang->no_batch ?? '-',
                        'no_faktur'    => $gudang->no_faktur ?? '-',
                        'kadaluarsa'   => '-',
                    ];
                }

                $this->listObatUmum = $mappedObat;
                $this->recalculateTotal();
            }
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function recalculateTotal(): void
    {
        $subtotal = 0;
        foreach ($this->listObatUmum as $item) {
            $h = floatval($item['harga'] ?? 0);
            $j = floatval($item['jumlah'] ?? 0);
            $emb = floatval($item['embalase'] ?? 0);
            $tsl = floatval($item['tuslah'] ?? 0);
            $subtotal += ($h * $j) + $emb + $tsl;
        }
        $this->total = $subtotal;
        $this->total_ppn = $this->total + $this->ppn;
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
        
        // Refresh stok obat yang ada sesuai depo baru
        $this->refreshStokDepo();
    }

    public function refreshStokDepo(): void
    {
        foreach ($this->listObatUmum as &$item) {
            $gudang = GudangBarang::where('kode_brng', $item['kode_brng'])
                ->where('kd_bangsal', $this->kd_depo)
                ->first();
            if ($gudang) {
                $item['stok'] = floatval($gudang->stok);
            }
        }
    }

    // Modal Lookup Obat (Tampilkan Sesuai Depo)
    public function openObatModal(): void
    {
        $this->searchObatModal = '';
        $this->selectedObatModal = [];
        $this->loadListObatSearch();
        $this->isObatModalOpen = true;
    }

    public function closeObatModal(): void
    {
        $this->isObatModalOpen = false;
    }

    public function updatedSearchObatModal(): void
    {
        $this->loadListObatSearch();
    }

    public function loadListObatSearch(): void
    {
        $query = DataBarang::query()->where('status', '1');

        if (!empty($this->searchObatModal)) {
            $s = "%{$this->searchObatModal}%";
            $query->where(function($q) use ($s) {
                $q->where('kode_brng', 'like', $s)
                  ->orWhere('nama_brng', 'like', $s)
                  ->orWhere('kategori', 'like', $s);
            });
        }

        $items = $query->orderBy('nama_brng')->limit(40)->get();
        $res = [];

        foreach ($items as $brng) {
            // Cek stok depo spesifik dari gudangbarang
            $gudang = GudangBarang::where('kode_brng', $brng->kode_brng)
                ->where('kd_bangsal', $this->kd_depo)
                ->first();

            $stokDepo = $gudang ? floatval($gudang->stok) : floatval($brng->stok ?? 0);
            $harga = floatval($brng->ralan ?? ($brng->h_beli ?? 0));

            $res[] = [
                'kode_brng' => $brng->kode_brng,
                'nama_brng' => $brng->nama_brng,
                'satuan'    => $brng->kode_sat ?? '-',
                'harga'     => $harga,
                'stok'      => $stokDepo,
                'kategori'  => $brng->kategori ?? '-',
                'no_batch'  => $gudang->no_batch ?? '-',
                'no_faktur' => $gudang->no_faktur ?? '-',
            ];
        }

        $this->listObatSearch = $res;
    }

    public function toggleSelectAllObatModal(): void
    {
        $allKodes = array_column($this->listObatSearch, 'kode_brng');
        if (count(array_intersect($this->selectedObatModal, $allKodes)) === count($allKodes)) {
            $this->selectedObatModal = array_diff($this->selectedObatModal, $allKodes);
        } else {
            $this->selectedObatModal = array_values(array_unique(array_merge($this->selectedObatModal, $allKodes)));
        }
    }

    public function addObatFromModal(string $kodeBrng): void
    {
        $this->selectedObatModal = [$kodeBrng];
        $this->addSelectedObatFromModal();
    }

    public function addSelectedObatFromModal(): void
    {
        if (empty($this->selectedObatModal)) {
            return;
        }

        $addedCount = 0;
        foreach ($this->selectedObatModal as $kodeBrng) {
            $brng = DataBarang::find($kodeBrng);
            if ($brng) {
                $gudang = GudangBarang::where('kode_brng', $kodeBrng)
                    ->where('kd_bangsal', $this->kd_depo)
                    ->first();

                $stokDepo = $gudang ? floatval($gudang->stok) : floatval($brng->stok ?? 0);
                $harga = floatval($brng->ralan ?? ($brng->h_beli ?? 0));

                $found = false;
                foreach ($this->listObatUmum as &$existing) {
                    if ($existing['kode_brng'] === $kodeBrng) {
                        $existing['jumlah'] += 1;
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $this->listObatUmum[] = [
                        'kode_brng'    => $brng->kode_brng,
                        'nama_brng'    => $brng->nama_brng,
                        'jumlah'       => 1,
                        'satuan'       => $brng->kode_sat ?? '-',
                        'harga'        => $harga,
                        'jenis_obat'   => $brng->kategori ?? 'SPECIAL',
                        'embalase'     => 0,
                        'tuslah'       => 0,
                        'stok'         => $stokDepo,
                        'aturan_pakai' => '-',
                        'industri'     => '-',
                        'kategori'     => $brng->kategori ?? '-',
                        'golongan'     => '-',
                        'no_batch'     => $gudang->no_batch ?? '-',
                        'no_faktur'    => $gudang->no_faktur ?? '-',
                        'kadaluarsa'   => '-',
                    ];
                }
                $addedCount++;
            }
        }

        $this->recalculateTotal();
        $this->selectedObatModal = [];
        $this->isObatModalOpen = false;

        $this->dispatch('swal', [
            'title' => 'Obat Ditambahkan!',
            'text'  => $addedCount . ' obat terpilih berhasil ditambahkan ke daftar.',
            'icon'  => 'success',
        ]);
    }

    public function removeObatUmum(int $index): void
    {
        if (isset($this->listObatUmum[$index])) {
            array_splice($this->listObatUmum, $index, 1);
            $this->recalculateTotal();
        }
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
