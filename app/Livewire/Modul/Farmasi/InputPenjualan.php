<?php

namespace App\Livewire\Modul\Farmasi;

use App\Repositories\Farmasi\PenjualanRepository;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Penjualan Obat & BHP'])]
class InputPenjualan extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search  = '';

    #[Url(history: true)]
    public string $dari    = '';

    #[Url(history: true)]
    public string $sampai  = '';

    #[Url(history: true)]
    public string $nip     = '';

    #[Url(history: true)]
    public int    $perPage = 20;

    public array $listAkunBayar = [];

    public function mount(): void
    {
        // Hanya isi nilai default jika tidak ada di URL
        if (! $this->dari) {
            $this->dari = now()->format('Y-m-d');
        }
        if (! $this->sampai) {
            $this->sampai = now()->format('Y-m-d');
        }
        $this->loadListAkunBayar();
    }

    public function loadListAkunBayar(): void
    {
        $this->listAkunBayar = \App\Models\AkunBayar::all()->toArray();
    }

    public function updatedSearch()  { $this->resetPage(); }
    public function updatedDari()    { $this->resetPage(); }
    public function updatedSampai()  { $this->resetPage(); }
    public function updatedNip()     { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }

    public function deletePenjualan(string $notaJual): void
    {
        try {
            $success = PenjualanRepository::deletePenjualan($notaJual);
            if ($success) {
                $this->dispatch('swal', [
                    'title' => 'Berhasil!',
                    'text'  => "Transaksi Penjualan {$notaJual} berhasil dihapus.",
                    'icon'  => 'success',
                ]);
            } else {
                $this->dispatch('swal', [
                    'title' => 'Gagal!',
                    'text'  => "Transaksi Penjualan {$notaJual} tidak ditemukan.",
                    'icon'  => 'error',
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Gagal Hapus!',
                'text'  => 'Error: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }

    public function verifikasiPenjualan(string $notaJual, string $namaBayar = ''): void
    {
        try {
            $success = PenjualanRepository::verifikasiPenjualan($notaJual, $namaBayar);
            if ($success) {
                $this->dispatch('swal', [
                    'title' => 'Terverifikasi!',
                    'text'  => "Transaksi {$notaJual} telah diverifikasi (Sudah Dibayar).",
                    'icon'  => 'success',
                ]);
            } else {
                $this->dispatch('swal', [
                    'title' => 'Gagal!',
                    'text'  => "Transaksi Penjualan {$notaJual} tidak ditemukan.",
                    'icon'  => 'error',
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Gagal Verifikasi!',
                'text'  => 'Error: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }


    public function render()
    {
        $penjualans  = PenjualanRepository::getList(
            $this->search,
            $this->dari,
            $this->sampai,
            $this->nip,
            $this->perPage,
        );

        $petugasList = PenjualanRepository::getPetugasList();

        return view('livewire.modul.farmasi.input-penjualan', [
            'penjualans'  => $penjualans,
            'petugasList' => $petugasList,
        ]);
    }
}

