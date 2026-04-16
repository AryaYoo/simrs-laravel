<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap;

use App\Models\RegPeriksa;
use App\Models\ResepObat;
use App\Models\ResepDokter as ResepDokterModel;
use App\Models\MasterAturanPakai;
use App\Models\Dokter;
use App\Livewire\Concerns\WithOptimisticLocking;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ResepDokter extends Component
{
    use WithOptimisticLocking, WithPagination;

    public string $no_rawat;
    public $regPeriksa;

    // Resep Header Status
    public $savedResep = null; 

    // Input Controls
    public $tgl_peresepan;
    public $jam_peresepan_jam;
    public $jam_peresepan_menit;
    public $jam_peresepan_detik;
    public $auto_waktu = true;
    public $auto_nomor = true;
    public $no_resep_input;
    
    // Dokter selection
    public $kd_dokter_peresep;
    public $nm_dokter_peresep;
    public $searchDokterModal = '';
    public $listDokter = [];
    public $isDokterModalOpen = false;

    // Searching left side
    public $searchObat = '';
    
    // The cart array
    // structure: [ 'id', 'kode_brng', 'nama_brng', 'satuan', 'jml', 'aturan_pakai', 'harga', 'stok' ]
    public $cart = [];

    // Reset pagination when searching
    public function updatedSearchObat()
    {
        $this->resetPage();
    }

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'kamarInap.kamar.bangsal'])->findOrFail($this->no_rawat);
        
        $this->kd_dokter_peresep = $this->regPeriksa->kd_dokter;
        $this->nm_dokter_peresep = $this->regPeriksa->dokter->nm_dokter ?? '';
        
        $this->tgl_peresepan = now()->format('Y-m-d');
        $this->jam_peresepan_jam = now()->format('H');
        $this->jam_peresepan_menit = now()->format('i');
        $this->jam_peresepan_detik = now()->format('s');

        $this->initializeLock($this->regPeriksa);
        $this->loadSavedResep();
    }

    public function loadSavedResep()
    {
        $this->savedResep = ResepObat::with(['detail.barang'])
            ->where('no_rawat', $this->no_rawat)
            ->where('status', 'ranap')
            ->orderBy('tgl_peresepan', 'desc')
            ->orderBy('jam_peresepan', 'desc')
            ->get();
    }

    public function updatedAutoWaktu($value)
    {
        if ($value) {
            $this->jam_peresepan_jam = now()->format('H');
            $this->jam_peresepan_menit = now()->format('i');
            $this->jam_peresepan_detik = now()->format('s');
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
        $this->kd_dokter_peresep = $kd_dokter;
        $this->nm_dokter_peresep = $nm_dokter;
        $this->isDokterModalOpen = false;
    }

    public function pushToCart($kode_brng, $nama_brng, $satuan, $harga, $stok)
    {
        $exists = collect($this->cart)->where('kode_brng', $kode_brng)->first();
        if ($exists) {
            $this->dispatch('swal', [
                'title' => 'Peringatan',
                'text' => 'Obat tersebut sudah ada di daftar permintaan.',
                'icon' => 'warning'
            ]);
            return;
        }

        $this->cart[] = [
            'id' => uniqid(),
            'kode_brng' => $kode_brng,
            'nama_brng' => $nama_brng,
            'satuan' => $satuan,
            'jml' => 1,
            'aturan_pakai' => '',
            'harga' => $harga,
            'stok' => $stok,
        ];
    }

    public function removeObat($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
    }

    public function getObatListProperty()
    {
        $query = DB::table('databarang')
            ->leftJoin('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
            ->leftJoin('kategori_barang', 'databarang.kode_kategori', '=', 'kategori_barang.kode')
            ->leftJoin('industrifarmasi', 'databarang.kode_industri', '=', 'industrifarmasi.kode_industri')
            ->leftJoin('gudangbarang', 'databarang.kode_brng', '=', 'gudangbarang.kode_brng')
            ->select(
                'databarang.kode_brng',
                'databarang.nama_brng',
                'databarang.kode_sat',
                'databarang.beliluar as harga',
                DB::raw("IFNULL(SUM(gudangbarang.stok), 0) as stok"),
                'kategori_barang.nama as jenis_obat',
                DB::raw("'-' as komposisi"),
                'industrifarmasi.nama_industri as industri_farmasi'
            )
            ->where('databarang.status', '1')
            ->groupBy(
                'databarang.kode_brng',
                'databarang.nama_brng',
                'databarang.kode_sat',
                'databarang.beliluar',
                'kategori_barang.nama',
                'industrifarmasi.nama_industri'
            );

        if (!empty($this->searchObat)) {
            $query->where(function($q) {
                $q->where('databarang.nama_brng', 'like', '%' . $this->searchObat . '%')
                  ->orWhere('databarang.kode_brng', 'like', '%' . $this->searchObat . '%');
            });
        }

        // Exclude obat yang sudah ada di keranjang
        $cartKodes = collect($this->cart)->pluck('kode_brng')->filter()->toArray();
        if (!empty($cartKodes)) {
            $query->whereNotIn('databarang.kode_brng', $cartKodes);
        }

        return $query->paginate(10);
    }

    public function save()
    {
        if (empty($this->cart)) {
            $this->dispatch('swal', [
                'title' => 'Gagal',
                'text' => 'Daftar obat masih kosong.',
                'icon' => 'error'
            ]);
            return;
        }

        $this->validateLock($this->regPeriksa);

        try {
            $no_resep = retry(5, function () {
                return DB::transaction(function () {
                    $tglSekarang = \Carbon\Carbon::parse($this->tgl_peresepan)->format('Ymd');
                    
                    if ($this->auto_nomor || empty($this->no_resep_input)) {
                        // Generate NO RESEP (format YYYYMMDDXXXX)
                        // Gunakan lockForUpdate untuk mencegah proses lain membaca MAX yang sama secara bersamaan
                        $maxData = ResepObat::where('no_resep', 'like', $tglSekarang . '%')
                            ->lockForUpdate()
                            ->max('no_resep');

                        if ($maxData) {
                            $lastNumber = (int) substr($maxData, -4);
                            $newNumber = $lastNumber + 1;
                        } else {
                            $newNumber = 1;
                        }
                        $generatedNo = $tglSekarang . sprintf('%04d', $newNumber);
                    } else {
                        $generatedNo = $this->no_resep_input;
                    }

                    // Sync waktu bila auto_waktu
                    if ($this->auto_waktu) {
                        $this->jam_peresepan_jam = now()->format('H');
                        $this->jam_peresepan_menit = now()->format('i');
                        $this->jam_peresepan_detik = now()->format('s');
                    }
                    $jamF = sprintf('%02d:%02d:%02d', $this->jam_peresepan_jam, $this->jam_peresepan_menit, $this->jam_peresepan_detik);

                    // Create Header
                    ResepObat::create([
                        'no_resep' => $generatedNo,
                        'tgl_perawatan' => $this->tgl_peresepan,
                        'jam' => $jamF,
                        'no_rawat' => $this->no_rawat,
                        'kd_dokter' => $this->kd_dokter_peresep,
                        'tgl_peresepan' => $this->tgl_peresepan,
                        'jam_peresepan' => $jamF,
                        'status' => 'ranap',
                        'tgl_penyerahan' => $this->tgl_peresepan,
                        'jam_penyerahan' => $jamF,
                    ]);

                    // Save details
                    foreach ($this->cart as $item) {
                        ResepDokterModel::create([
                            'no_resep' => $generatedNo,
                            'kode_brng' => $item['kode_brng'],
                            'jml' => $item['jml'],
                            'aturan_pakai' => $item['aturan_pakai'],
                        ]);
                    }

                    return $generatedNo;
                });
            }, 100); // retry 5 kali dengan jeda 100ms bila gagal karena deadlock/duplicate

            $this->cart = [];
            $this->loadSavedResep();
            
            $this->dispatch('swal', [
                'title' => 'Berhasil',
                'text' => 'Resep dokter berhasil disimpan dengan No: ' . $no_resep,
                'icon' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Gagal',
                'text' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function hapusResep($no_resep)
    {
        DB::beginTransaction();
        try {
            ResepDokterModel::where('no_resep', $no_resep)->delete();
            ResepObat::where('no_resep', $no_resep)->delete();
            DB::commit();
            $this->loadSavedResep();
            $this->dispatch('swal', [
                'title' => 'Berhasil',
                'text' => 'Resep berhasil dihapus.',
                'icon' => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', [
                'title' => 'Gagal',
                'text' => 'Resep gagal dihapus: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.modul.rawat-inap.sub-rawat-inap.resep-dokter')
            ->layout('layouts.app', ['title' => 'Input Resep Dokter']);
    }
}
