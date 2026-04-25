<?php

namespace App\Livewire\Modul\CasemixRawatInap\Resume;

use App\Models\RegPeriksa;
use App\Models\ResumePasienRanap;
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
        $resume = ResumePasienRanap::findOrFail($no_rawat);

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
        $resumes = ResumePasienRanap::with([
            'regPeriksa.pasien',
            'regPeriksa.dokter',
            'regPeriksa.rujukMasuk',
            'regPeriksa.kamarInap' => function($query) {
                $query->orderBy('tgl_masuk', 'desc')->orderBy('jam_masuk', 'desc');
            }
        ])
        ->whereHas('regPeriksa', function($query) {
            $query->where('no_rkm_medis', $this->no_rkm_medis);
        })
        ->orderByDesc('no_rawat')
        ->paginate(10);

        return view('livewire.modul.casemix-rawat-inap.resume.index', [
            'resumes' => $resumes
        ]);
    }
}
