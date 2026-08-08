<?php

namespace App\Livewire\Modul\Farmasi;

use App\Models\Bangsal;
use App\Models\Pasien;
use App\Models\Petugas;
use App\Models\Rekening;
use App\Repositories\Farmasi\PenjualanRepository;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Tambah Penjualan Obat & BHP'])]
class CreatePenjualan extends Component
{
    use WithPagination;

    // ------------------------------------------------------------------------
    // Form Header State
    // ------------------------------------------------------------------------
    public $nota_jual;
    public $keterangan = '';
    public $tgl_jual;
    public $jns_jual = 'Jual Bebas';

    public $no_rkm_medis = '';
    public $nm_pasien = '';
    
    public $nip = '';
    public $nm_petugas = '';
    
    public $kd_bangsal = '';
    public $nm_bangsal = '';

    // ------------------------------------------------------------------------
    // Billing State
    // ------------------------------------------------------------------------
    public $grand_total = 0;
    public $ppn_persen = 0;
    public $ppn_rp = 0;
    public $tagihan = 0;
    public $ongkir = 0;
    public $kd_rek = '';
    public $jumlah_bayar = 0;
    public $kembali = 0;

    // ------------------------------------------------------------------------
    // Cart State
    // ------------------------------------------------------------------------
    public $searchObat = '';
    public $cart = [];
    
    // ------------------------------------------------------------------------
    // Modals State
    // ------------------------------------------------------------------------
    public $isPasienModalOpen = false;
    public $searchPasienModal = '';
    public $listPasien = [];

    public $isPetugasModalOpen = false;
    public $searchPetugasModal = '';
    public $listPetugas = [];

    public $isBangsalModalOpen = false;
    public $searchBangsalModal = '';
    public $listBangsal = [];

    public $nama_bayar = '';
    public $listAkunBayar = [];

    public function mount()
    {
        $this->tgl_jual = now()->format('Y-m-d');
        $this->generateNota();
        $this->loadListAkunBayar();
    }

    public function updatedTglJual()
    {
        $this->generateNota();
    }

    public function generateNota()
    {
        $this->nota_jual = PenjualanRepository::generateNoNota($this->tgl_jual);
    }

    public function loadListAkunBayar()
    {
        $this->listAkunBayar = \App\Models\AkunBayar::all();
        if ($this->listAkunBayar->count() > 0 && empty($this->nama_bayar)) {
            $first = $this->listAkunBayar->first();
            $this->selectAkunBayar($first->nama_bayar);
        }
    }

    public function updatedNamaBayar($val)
    {
        $this->selectAkunBayar($val);
    }

    public function selectAkunBayar($namaBayar)
    {
        $this->nama_bayar = $namaBayar;
        $akun = $this->listAkunBayar->firstWhere('nama_bayar', $namaBayar);
        if ($akun) {
            $this->kd_rek = $akun->kd_rek;
            if ($akun->ppn !== null && $akun->ppn > 0) {
                $this->ppn_persen = $akun->ppn;
            }
        }
        $this->calculateBilling();
    }

    // Reset pagination when searching obat
    public function updatedSearchObat()
    {
        $this->resetPage();
    }

    public function updatedKdBangsal()
    {
        $this->resetPage();
    }

    // ------------------------------------------------------------------------
    // Cart Logics
    // ------------------------------------------------------------------------
    public function getObatListProperty()
    {
        if (empty($this->kd_bangsal)) {
            return collect(); // Harus pilih bangsal dulu
        }
        $cartKodes = collect($this->cart)->pluck('kode_brng')->filter()->toArray();
        return PenjualanRepository::getObatList($this->kd_bangsal, $this->searchObat, $cartKodes);
    }

    public function pushToCart($kode_brng, $nama_brng, $satuan, $harga, $stok)
    {
        $exists = collect($this->cart)->where('kode_brng', $kode_brng)->first();
        if ($exists) {
            $this->dispatch('swal', [
                'title' => 'Peringatan',
                'text'  => 'Obat tersebut sudah ada di keranjang.',
                'icon'  => 'warning'
            ]);
            return;
        }

        $this->cart[] = [
            'id'           => uniqid(),
            'kode_brng'    => $kode_brng,
            'nama_brng'    => $nama_brng,
            'satuan'       => $satuan,
            'h_jual'       => $harga,
            'stok'         => $stok,
            'jml'          => 1,
            'dis_persen'   => 0,
            'dis_rp'       => 0,
            'tambahan'     => 0,
            'embalase'     => 0,
            'tuslah'       => 0,
            'aturan_pakai' => '',
            'no_batch'     => '',
            'no_faktur'    => '',
            'kadaluarsa'   => '',
        ];
        
        $this->calculateCart();
    }

    public function removeObat($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        $this->calculateCart();
    }

    public function calculateCart()
    {
        $grandTotal = 0;
        foreach ($this->cart as $key => $item) {
            $jml = (float)($item['jml'] ?: 0);
            $harga = (float)($item['h_jual'] ?: 0);
            
            // Subtotal dasar
            $subtotal_dasar = $jml * $harga;
            
            // Diskon Persen -> Rp
            $dis_persen = (float)($item['dis_persen'] ?: 0);
            $dis_rp_dari_persen = ($subtotal_dasar * $dis_persen) / 100;
            
            // User input diskon Rp (Bisa manual override)
            $dis_rp = (float)($item['dis_rp'] ?: 0);
            if ($dis_persen > 0) {
                $dis_rp = $dis_rp_dari_persen;
                $this->cart[$key]['dis_rp'] = $dis_rp;
            }

            $tambahan = (float)($item['tambahan'] ?: 0);
            $embalase = (float)($item['embalase'] ?: 0);
            $tuslah = (float)($item['tuslah'] ?: 0);
            
            // Total per item
            $total_item = ($subtotal_dasar - $dis_rp) + $tambahan + $embalase + $tuslah;
            $grandTotal += $total_item;
        }

        $this->grand_total = $grandTotal;
        $this->calculateBilling();
    }

    public function updated($propertyName)
    {
        if (str_starts_with($propertyName, 'cart.')) {
            $this->calculateCart();
        }
        if (in_array($propertyName, ['ppn_persen', 'ongkir', 'jumlah_bayar'])) {
            $this->calculateBilling();
        }
    }

    public function calculateBilling()
    {
        $this->ppn_rp = ($this->grand_total * ((float)$this->ppn_persen ?: 0)) / 100;
        $this->tagihan = $this->grand_total + $this->ppn_rp + ((float)$this->ongkir ?: 0);
        $this->kembali = ((float)$this->jumlah_bayar ?: 0) - $this->tagihan;
    }

    // ------------------------------------------------------------------------
    // Lookup Pasien
    // ------------------------------------------------------------------------
    public function openPasienModal()
    {
        $this->searchPasienModal = '';
        $this->loadListPasien();
        $this->isPasienModalOpen = true;
    }

    public function loadListPasien()
    {
        $query = Pasien::query();
        if (!empty($this->searchPasienModal)) {
            $query->where(function($q) {
                $q->where('no_rkm_medis', 'like', "%{$this->searchPasienModal}%")
                  ->orWhere('nm_pasien', 'like', "%{$this->searchPasienModal}%");
            });
        }
        $this->listPasien = $query->limit(20)->get()->toArray();
    }

    public function updatedSearchPasienModal()
    {
        $this->loadListPasien();
    }

    public function selectPasien($no_rkm_medis, $nm_pasien)
    {
        $this->no_rkm_medis = $no_rkm_medis;
        $this->nm_pasien = $nm_pasien;
        $this->isPasienModalOpen = false;
    }

    // ------------------------------------------------------------------------
    // Lookup Petugas
    // ------------------------------------------------------------------------
    public function openPetugasModal()
    {
        $this->searchPetugasModal = '';
        $this->loadListPetugas();
        $this->isPetugasModalOpen = true;
    }

    public function loadListPetugas()
    {
        $query = Petugas::query()->where('status', '1');
        if (!empty($this->searchPetugasModal)) {
            $query->where(function($q) {
                $q->where('nip', 'like', "%{$this->searchPetugasModal}%")
                  ->orWhere('nama', 'like', "%{$this->searchPetugasModal}%");
            });
        }
        $this->listPetugas = $query->limit(20)->get()->toArray();
    }

    public function updatedSearchPetugasModal()
    {
        $this->loadListPetugas();
    }

    public function selectPetugas($nip, $nama)
    {
        $this->nip = $nip;
        $this->nm_petugas = $nama;
        $this->isPetugasModalOpen = false;
    }

    // ------------------------------------------------------------------------
    // Lookup Bangsal (Lokasi)
    // ------------------------------------------------------------------------
    public function openBangsalModal()
    {
        $this->searchBangsalModal = '';
        $this->loadListBangsal();
        $this->isBangsalModalOpen = true;
    }

    public function loadListBangsal()
    {
        $query = Bangsal::query()->where('status', '1');
        if (!empty($this->searchBangsalModal)) {
            $query->where(function($q) {
                $q->where('kd_bangsal', 'like', "%{$this->searchBangsalModal}%")
                  ->orWhere('nm_bangsal', 'like', "%{$this->searchBangsalModal}%");
            });
        }
        $this->listBangsal = $query->limit(20)->get()->toArray();
    }

    public function updatedSearchBangsalModal()
    {
        $this->loadListBangsal();
    }

    public function selectBangsal($kd_bangsal, $nm_bangsal)
    {
        $this->kd_bangsal = $kd_bangsal;
        $this->nm_bangsal = $nm_bangsal;
        // Jika gudang berubah, reset keranjang agar tidak nyasar stoknya
        $this->cart = [];
        $this->calculateCart();
        $this->isBangsalModalOpen = false;
    }

    // ------------------------------------------------------------------------
    // Save Logics
    // ------------------------------------------------------------------------
    public function save()
    {
        // Validations
        if (empty($this->cart)) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Keranjang kosong!', 'icon' => 'error']);
            return;
        }
        if (empty($this->kd_bangsal)) {
            $this->dispatch('swal', ['title' => 'Gagal', 'text' => 'Pilih lokasi bangsal!', 'icon' => 'error']);
            return;
        }

        try {
            $data = [
                'tgl_jual'     => $this->tgl_jual,
                'nip'          => $this->nip,
                'no_rkm_medis' => $this->no_rkm_medis,
                'nm_pasien'    => $this->nm_pasien,
                'keterangan'   => $this->keterangan,
                'jns_jual'     => $this->jns_jual,
                'ongkir'       => $this->ongkir,
                'ppn'          => $this->ppn_rp,
                'kd_bangsal'   => $this->kd_bangsal,
                'kd_rek'       => $this->kd_rek,
                'nama_bayar'   => $this->nama_bayar,
                'jumlah_bayar' => $this->jumlah_bayar,
                'tagihan'      => $this->tagihan,
                'cart'         => $this->cart,
            ];


            $no_nota = PenjualanRepository::savePenjualan($data);

            $this->dispatch('swal', [
                'title' => 'Berhasil',
                'text'  => 'Transaksi berhasil disimpan dengan Nota: ' . $no_nota,
                'icon'  => 'success'
            ]);

            // Redirect back to list
            return redirect()->route('modul.farmasi.input-penjualan');

        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Gagal',
                'text'  => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'icon'  => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.modul.farmasi.create-penjualan');
    }
}
