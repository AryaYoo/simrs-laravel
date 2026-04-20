<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\ResepDokter;

use App\Models\RegPeriksa;
use App\Models\ResepObat;
use App\Repositories\RawatInap\ResepDokterRepository;
use App\Livewire\Concerns\WithOptimisticLocking;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithOptimisticLocking, WithPagination;

    public string $no_rawat;
    public $regPeriksa;
    public string $tab = 'input'; // 'input' or 'riwayat'

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
        $this->savedResep = ResepDokterRepository::getSavedReseps($this->no_rawat);
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
        $this->listDokter = ResepDokterRepository::getListDokter($this->searchDokterModal);
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
        $cartKodes = collect($this->cart)->pluck('kode_brng')->filter()->toArray();
        return ResepDokterRepository::getObatList($this->searchObat, $cartKodes);
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

        $this->validateLock($this->regPeriksa->fresh());

        try {
            $data = [
                'no_rawat' => $this->no_rawat,
                'kd_dokter' => $this->kd_dokter_peresep,
                'tgl_peresepan' => $this->tgl_peresepan,
                'jam' => sprintf('%02d:%02d:%02d', $this->jam_peresepan_jam, $this->jam_peresepan_menit, $this->jam_peresepan_detik),
                'auto_nomor' => $this->auto_nomor,
                'no_resep_input' => $this->no_resep_input,
                'cart' => $this->cart,
            ];

            $no_resep = ResepDokterRepository::saveResep($data);

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
        try {
            ResepDokterRepository::deleteResep($no_resep);
            $this->loadSavedResep();
            $this->dispatch('swal', [
                'title' => 'Berhasil',
                'text' => 'Resep berhasil dihapus.',
                'icon' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Gagal',
                'text' => 'Resep gagal dihapus: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.modul.rawat-inap.sub-rawat-inap.resep-dokter.index')
            ->layout('layouts.app', ['title' => 'Input Resep Dokter']);
    }
}
