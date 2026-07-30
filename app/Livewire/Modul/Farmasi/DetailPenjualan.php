<?php

namespace App\Livewire\Modul\Farmasi;

use App\Repositories\Farmasi\PenjualanRepository;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Detail Penjualan Obat & BHP'])]
class DetailPenjualan extends Component
{
    public string $notaJual = '';
    public ?array $detailData = null;

    public function mount(string $nota_jual): void
    {
        $this->notaJual   = urldecode($nota_jual);
        $this->detailData = PenjualanRepository::getDetail($this->notaJual);

        if (!$this->detailData || !$this->detailData['penjualan']) {
            abort(404, 'Data Penjualan tidak ditemukan.');
        }
    }

    public function render()
    {
        return view('livewire.modul.farmasi.detail-penjualan');
    }
}
