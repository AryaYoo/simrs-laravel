<?php

namespace App\Livewire\Modul\Farmasi;

use App\Models\Bangsal;
use App\Models\DataBarang;
use App\Models\GudangBarang;
use App\Models\Opname;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Input Stok Opname'])]
class InputStockOpname extends Component
{
    use WithPagination;

    // Header form state
    public string $tanggal = '';
    public string $kd_bangsal = '';
    public string $nm_bangsal = '';
    public string $keterangan = '';

    // Search and filters for items table
    public string $search = '';
    public int $perPage = 20;

    // Modal Bangsal state
    public bool $isBangsalModalOpen = false;
    public string $searchBangsalModal = '';
    public array $listBangsal = [];

    // Real input values: key is unique composite key, value is float/int
    public array $realValues = [];
    public array $batchValues = [];
    public array $fakturValues = [];

    public function mount(): void
    {
        $this->tanggal = date('Y-m-d');
        $this->keterangan = 'Stok Opname ' . date('d/m/Y');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    // ------------------------------------------------------------------------
    // Lookup Bangsal (Lokasi)
    // ------------------------------------------------------------------------
    public function openBangsalModal(): void
    {
        $this->searchBangsalModal = '';
        $this->loadListBangsal();
        $this->isBangsalModalOpen = true;
    }

    public function loadListBangsal(): void
    {
        $query = Bangsal::query()->where(function ($q) {
            $q->where('status', '1')->orWhereNull('status');
        });

        if (!empty($this->searchBangsalModal)) {
            $query->where(function ($q) {
                $q->where('kd_bangsal', 'like', "%{$this->searchBangsalModal}%")
                  ->orWhere('nm_bangsal', 'like', "%{$this->searchBangsalModal}%");
            });
        }

        $this->listBangsal = $query->orderBy('nm_bangsal', 'asc')->limit(30)->get()->toArray();
    }

    public function updatedSearchBangsalModal(): void
    {
        $this->loadListBangsal();
    }

    public function selectBangsal(string $kd_bangsal, string $nm_bangsal): void
    {
        $this->kd_bangsal = $kd_bangsal;
        $this->nm_bangsal = $nm_bangsal;
        $this->isBangsalModalOpen = false;
        $this->resetPage();
        $this->realValues = [];
        $this->batchValues = [];
        $this->fakturValues = [];
    }

    public function encodeKey($kode_brng, $no_batch = '', $no_faktur = ''): string
    {
        return rtrim(strtr(base64_encode($kode_brng . '|||' . $no_batch . '|||' . $no_faktur), '+/', '-_'), '=');
    }

    public function decodeKey(string $key): array
    {
        $decoded = base64_decode(strtr($key, '-_', '+/'));
        $parts = explode('|||', $decoded);
        return [
            'kode_brng' => $parts[0] ?? '',
            'no_batch'  => $parts[1] ?? '',
            'no_faktur' => $parts[2] ?? '',
        ];
    }

    // ------------------------------------------------------------------------
    // Save Stock Opname
    // ------------------------------------------------------------------------
    public function save(): void
    {
        $this->validate([
            'tanggal'    => 'required|date',
            'kd_bangsal' => 'required|string',
            'keterangan' => 'nullable|string|max:60',
        ], [
            'tanggal.required'    => 'Tanggal opname wajib diisi.',
            'kd_bangsal.required' => 'Lokasi / Bangsal wajib dipilih.',
        ]);

        // Filter items that have real stock filled
        $filledItems = array_filter($this->realValues, function ($val) {
            return $val !== '' && $val !== null && is_numeric($val);
        });

        if (empty($filledItems)) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal Simpan',
                'text'  => 'Silakan masukkan minimal 1 jumlah Real stok barang yang di-opname.'
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            $petugas = Auth::user()->name ?? 'Admin';
            $jam = date('H:i:s');

            foreach ($filledItems as $itemKey => $realVal) {
                $decoded = $this->decodeKey($itemKey);
                $kode_brng = $decoded['kode_brng'];
                $no_batch = $this->batchValues[$itemKey] ?? $decoded['no_batch'];
                $no_faktur = $this->fakturValues[$itemKey] ?? $decoded['no_faktur'];

                if (!$kode_brng) {
                    continue;
                }

                $barang = DataBarang::where('kode_brng', $kode_brng)->first();
                $h_beli = $barang->h_beli ?? 0;

                // Current stock in gudangbarang
                $gudang = GudangBarang::where('kode_brng', $kode_brng)
                    ->where('kd_bangsal', $this->kd_bangsal)
                    ->where('no_batch', $no_batch)
                    ->where('no_faktur', $no_faktur)
                    ->first();

                $currentStok = $gudang->stok ?? 0;
                $real = (float)$realVal;

                $selisih = 0;
                $lebih = 0;
                if ($currentStok > $real) {
                    $selisih = $currentStok - $real;
                } elseif ($real > $currentStok) {
                    $lebih = $real - $currentStok;
                }

                $nomihilang = $selisih * $h_beli;
                $nomilebih = $lebih * $h_beli;

                // 1. Simpan ke tabel opname
                $existingOpname = DB::table('opname')
                    ->where('kode_brng', $kode_brng)
                    ->where('tanggal', $this->tanggal)
                    ->where('kd_bangsal', $this->kd_bangsal)
                    ->where('no_batch', $no_batch)
                    ->where('no_faktur', $no_faktur)
                    ->first();

                if ($existingOpname) {
                    DB::table('opname')
                        ->where('kode_brng', $kode_brng)
                        ->where('tanggal', $this->tanggal)
                        ->where('kd_bangsal', $this->kd_bangsal)
                        ->where('no_batch', $no_batch)
                        ->where('no_faktur', $no_faktur)
                        ->update([
                            'h_beli'     => $h_beli,
                            'stok'       => $currentStok,
                            'real'       => $real,
                            'selisih'    => $selisih,
                            'nomihilang' => $nomihilang,
                            'lebih'      => $lebih,
                            'nomilebih'  => $nomilebih,
                            'keterangan' => $this->keterangan ?: '-',
                        ]);
                } else {
                    DB::table('opname')->insert([
                        'kode_brng'  => $kode_brng,
                        'h_beli'     => $h_beli,
                        'tanggal'    => $this->tanggal,
                        'stok'       => $currentStok,
                        'real'       => $real,
                        'selisih'    => $selisih,
                        'nomihilang' => $nomihilang,
                        'lebih'      => $lebih,
                        'nomilebih'  => $nomilebih,
                        'keterangan' => $this->keterangan ?: '-',
                        'kd_bangsal' => $this->kd_bangsal,
                        'no_batch'   => $no_batch,
                        'no_faktur'  => $no_faktur,
                    ]);
                }

                // 2. Update atau Insert ke tabel gudangbarang
                $existingGudang = DB::table('gudangbarang')
                    ->where('kode_brng', $kode_brng)
                    ->where('kd_bangsal', $this->kd_bangsal)
                    ->where('no_batch', $no_batch)
                    ->where('no_faktur', $no_faktur)
                    ->first();

                if ($existingGudang) {
                    DB::table('gudangbarang')
                        ->where('kode_brng', $kode_brng)
                        ->where('kd_bangsal', $this->kd_bangsal)
                        ->where('no_batch', $no_batch)
                        ->where('no_faktur', $no_faktur)
                        ->update([
                            'stok' => $real,
                        ]);
                } else {
                    DB::table('gudangbarang')->insert([
                        'kode_brng'  => $kode_brng,
                        'kd_bangsal' => $this->kd_bangsal,
                        'stok'       => $real,
                        'no_batch'   => $no_batch,
                        'no_faktur'  => $no_faktur,
                    ]);
                }

                // 3. Catat ke tabel riwayat_barang_medis
                DB::table('riwayat_barang_medis')->insert([
                    'kode_brng'  => $kode_brng,
                    'stok_awal'  => $currentStok,
                    'masuk'      => $lebih,
                    'keluar'     => $selisih,
                    'stok_akhir' => $real,
                    'posisi'     => 'Opname',
                    'tanggal'    => $this->tanggal,
                    'jam'        => $jam,
                    'petugas'    => $petugas,
                    'kd_bangsal' => $this->kd_bangsal,
                    'status'     => 'Simpan',
                    'no_batch'   => $no_batch,
                    'no_faktur'  => $no_faktur,
                ]);
            }

            DB::commit();

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text'  => 'Data Stock Opname berhasil disimpan dan stok telah disesuaikan.'
            ]);

            $this->realValues = [];

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal:error', [
                'title' => 'Terjadi Kesalahan',
                'text'  => 'Gagal menyimpan data: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        $items = null;

        if (!empty($this->kd_bangsal)) {
            $query = DataBarang::with(['satuanKecil', 'jenis'])
                ->select(
                    'databarang.kode_brng',
                    'databarang.nama_brng',
                    'databarang.h_beli',
                    'databarang.kdjns',
                    'databarang.kode_sat',
                    DB::raw('COALESCE(gudangbarang.stok, 0) as stok'),
                    DB::raw("COALESCE(gudangbarang.no_batch, '') as no_batch"),
                    DB::raw("COALESCE(gudangbarang.no_faktur, '') as no_faktur")
                )
                ->leftJoin('gudangbarang', function ($join) {
                    $join->on('databarang.kode_brng', '=', 'gudangbarang.kode_brng')
                         ->where('gudangbarang.kd_bangsal', '=', $this->kd_bangsal);
                })
                ->where('databarang.status', '1');

            if (!empty($this->search)) {
                $query->where(function ($q) {
                    $q->where('databarang.kode_brng', 'like', "%{$this->search}%")
                      ->orWhere('databarang.nama_brng', 'like', "%{$this->search}%")
                      ->orWhere('gudangbarang.no_batch', 'like', "%{$this->search}%")
                      ->orWhere('gudangbarang.no_faktur', 'like', "%{$this->search}%")
                      ->orWhereHas('jenis', function ($qJns) {
                          $qJns->where('nama', 'like', "%{$this->search}%");
                      });
                });
            }

            $items = $query->orderBy('databarang.nama_brng', 'asc')->paginate($this->perPage);
        }

        return view('livewire.modul.farmasi.input-stock-opname', [
            'items' => $items,
        ]);
    }
}
