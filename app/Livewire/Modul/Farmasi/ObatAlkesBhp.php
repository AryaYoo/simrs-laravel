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
        $search = trim($this->searchObatModal);

        // Gunakan pola join lengkap sesuai FK tabel databarang
        $query = \Illuminate\Support\Facades\DB::table('databarang')
            ->leftJoin('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
            ->leftJoin('kategori_barang', 'databarang.kode_kategori', '=', 'kategori_barang.kode')
            ->leftJoin('golongan_barang', 'databarang.kode_golongan', '=', 'golongan_barang.kode')
            ->leftJoin('industrifarmasi', 'databarang.kode_industri', '=', 'industrifarmasi.kode_industri')
            ->leftJoin('jenis', 'databarang.kdjns', '=', 'jenis.kdjns')
            ->leftJoin('gudangbarang', function($join) {
                $join->on('databarang.kode_brng', '=', 'gudangbarang.kode_brng')
                     ->where('gudangbarang.kd_bangsal', '=', $this->kd_depo);
            })
            ->where('databarang.status', '1')
            ->select(
                'databarang.kode_brng',
                'databarang.nama_brng',
                'databarang.kode_sat',
                'databarang.ralan',
                'databarang.h_beli',
                'databarang.beliluar',
                'kategori_barang.nama as nama_kategori',
                'golongan_barang.nama as nama_golongan',
                'industrifarmasi.nama_industri',
                'jenis.nama as nama_jenis',
                'gudangbarang.stok as stok_depo',
                'gudangbarang.no_batch',
                'gudangbarang.no_faktur',
            )
            ->groupBy(
                'databarang.kode_brng',
                'databarang.nama_brng',
                'databarang.kode_sat',
                'databarang.ralan',
                'databarang.h_beli',
                'databarang.beliluar',
                'kategori_barang.nama',
                'golongan_barang.nama',
                'industrifarmasi.nama_industri',
                'jenis.nama',
                'gudangbarang.stok',
                'gudangbarang.no_batch',
                'gudangbarang.no_faktur',
            );

        if ($search !== '') {
            $s = "%{$search}%";
            $query->where(function($q) use ($s) {
                $q->where('databarang.kode_brng', 'like', $s)
                  ->orWhere('databarang.nama_brng', 'like', $s)
                  ->orWhere('kategori_barang.nama', 'like', $s)
                  ->orWhere('jenis.nama', 'like', $s);
            });
        }

        $items = $query->orderBy('databarang.nama_brng')->limit(50)->get();
        $res = [];

        foreach ($items as $brng) {
            $stokDepo = floatval($brng->stok_depo ?? 0);
            // Harga sesuai tarif aktif (ralan default)
            $harga = floatval($brng->ralan ?? ($brng->h_beli ?? 0));

            $res[] = [
                'kode_brng'    => $brng->kode_brng,
                'nama_brng'    => $brng->nama_brng,
                'satuan'       => $brng->kode_sat ?? '-',
                'harga'        => $harga,
                'stok'         => $stokDepo,
                'jenis_obat'   => $brng->nama_jenis ?? '-',
                'kategori'     => $brng->nama_kategori ?? '-',
                'golongan'     => $brng->nama_golongan ?? '-',
                'industri'     => $brng->nama_industri ?? '-',
                'no_batch'     => $brng->no_batch ?? '-',
                'no_faktur'    => $brng->no_faktur ?? '-',
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
            // Ambil data dengan join lengkap agar dapat nama kategori, jenis, industri, golongan
            $brngData = \Illuminate\Support\Facades\DB::table('databarang')
                ->leftJoin('kategori_barang', 'databarang.kode_kategori', '=', 'kategori_barang.kode')
                ->leftJoin('golongan_barang', 'databarang.kode_golongan', '=', 'golongan_barang.kode')
                ->leftJoin('industrifarmasi', 'databarang.kode_industri', '=', 'industrifarmasi.kode_industri')
                ->leftJoin('jenis', 'databarang.kdjns', '=', 'jenis.kdjns')
                ->where('databarang.kode_brng', $kodeBrng)
                ->select(
                    'databarang.kode_brng',
                    'databarang.nama_brng',
                    'databarang.kode_sat',
                    'databarang.ralan',
                    'databarang.h_beli',
                    'kategori_barang.nama as nama_kategori',
                    'golongan_barang.nama as nama_golongan',
                    'industrifarmasi.nama_industri',
                    'jenis.nama as nama_jenis',
                )
                ->first();

            if ($brngData) {
                $gudang = GudangBarang::where('kode_brng', $kodeBrng)
                    ->where('kd_bangsal', $this->kd_depo)
                    ->first();

                $stokDepo = $gudang ? floatval($gudang->stok) : 0;
                $harga = floatval($brngData->ralan ?? ($brngData->h_beli ?? 0));

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
                        'kode_brng'    => $brngData->kode_brng,
                        'nama_brng'    => $brngData->nama_brng,
                        'jumlah'       => 1,
                        'satuan'       => $brngData->kode_sat ?? '-',
                        'harga'        => $harga,
                        'jenis_obat'   => $brngData->nama_jenis ?? '-',
                        'embalase'     => 0,
                        'tuslah'       => 0,
                        'stok'         => $stokDepo,
                        'aturan_pakai' => '-',
                        'industri'     => $brngData->nama_industri ?? '-',
                        'kategori'     => $brngData->nama_kategori ?? '-',
                        'golongan'     => $brngData->nama_golongan ?? '-',
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
        if ($this->isObatModalOpen) {
            $this->loadListObatSearch();
        }
        if ($this->isBangsalModalOpen) {
            $this->loadListBangsal();
        }

        return view('livewire.modul.farmasi.obat-alkes-bhp');
    }
}
