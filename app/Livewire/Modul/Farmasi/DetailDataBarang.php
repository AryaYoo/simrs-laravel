<?php

namespace App\Livewire\Modul\Farmasi;

use App\Models\DataBarang;
use App\Repositories\Farmasi\DataBarangRepository;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Detail Data Obat, Alkes dan BHP Medis'])]
class DetailDataBarang extends Component
{
    public string $kode_brng;
    public ?DataBarang $barang = null;

    public function mount(string $kode_brng, DataBarangRepository $repository): void
    {
        $this->kode_brng = $kode_brng;
        $this->barang = $repository->getDetail($kode_brng);

        if (!$this->barang) {
            abort(404, 'Data barang tidak ditemukan.');
        }

        // Remove from newly created tag list once viewed
        $newItems = session('newly_created_barang', []);
        if (in_array($kode_brng, $newItems)) {
            session(['newly_created_barang' => array_values(array_diff($newItems, [$kode_brng]))]);
        }
    }

    public function render()
    {
        return view('livewire.modul.farmasi.detail-data-barang');
    }
}
