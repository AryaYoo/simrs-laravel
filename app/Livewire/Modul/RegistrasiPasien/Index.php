<?php

namespace App\Livewire\Modul\RegistrasiPasien;

use App\Models\RegPeriksa;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Daftar Pasien'])]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $dari = '';
    public string $sampai = '';

    public function mount()
    {
        $this->dari   = now()->format('Y-m-d');
        $this->sampai = now()->format('Y-m-d');
    }

    public function updatedSearch()  { $this->resetPage(); }
    public function updatedDari()    { $this->resetPage(); }
    public function updatedSampai()  { $this->resetPage(); }

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        $regPeriksas = RegPeriksa::query()
            ->with(['dokter', 'pasien'])
            ->when($this->dari,    fn($q) => $q->whereDate('tgl_registrasi', '>=', $this->dari))
            ->when($this->sampai,  fn($q) => $q->whereDate('tgl_registrasi', '<=', $this->sampai))
            ->where(function ($query) use ($searchTerm) {
                $query->where('no_rawat', 'like', $searchTerm)
                    ->orWhere('no_rkm_medis', 'like', $searchTerm)
                    ->orWhereHas('pasien', function ($q) use ($searchTerm) {
                        $q->where('nm_pasien', 'like', $searchTerm);
                    });
            })
            ->orderBy('tgl_registrasi', 'desc')
            ->orderBy('jam_reg', 'desc')
            ->paginate(15);

        return view('livewire.modul.registrasi-pasien.index', [
            'regPeriksas' => $regPeriksas,
        ]);
    }
}
