<?php

namespace App\Livewire\Modul\CasemixRawatJalan\Resume;

use App\Models\RegPeriksa;
use App\Models\ResumePasien;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Daftar Resume Casemix Pasien CASEMIX'])]
class Index extends Component
{
    use WithPagination;

    public string $no_rawat;
    public string $no_rkm_medis;
    public $regPeriksa;

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter'])->findOrFail($this->no_rawat);
        $this->no_rkm_medis = $this->regPeriksa->no_rkm_medis;
    }

    public function delete($no_rawat)
    {
        $resume = ResumePasien::findOrFail($no_rawat);

        try {
            $resume->delete();
            $this->dispatch('swal', [
                'title' => 'Berhasil!',
                'text' => 'Resume medis berhasil dihapus.',
                'icon' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Gagal Menghapus',
                'text' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function render()
    {
        $resumes = ResumePasien::with([
            'regPeriksa.pasien',
            'regPeriksa.dokter',
            'regPeriksa.poliklinik'
        ])
        ->whereHas('regPeriksa', function($query) {
            $query->where('no_rkm_medis', $this->no_rkm_medis);
        })
        ->orderByDesc('no_rawat')
        ->paginate(10);

        return view('livewire.modul.casemix-rawat-jalan.resume.index', [
            'resumes' => $resumes
        ]);
    }
}
