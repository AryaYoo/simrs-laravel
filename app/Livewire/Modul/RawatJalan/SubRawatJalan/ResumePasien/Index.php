<?php

namespace App\Livewire\Modul\RawatJalan\SubRawatJalan\ResumePasien;

use App\Models\RegPeriksa;
use App\Models\ResumePasien;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Resume Medis Pasien Rawat Jalan'])]
class Index extends Component
{
    use WithPagination;

    public string $no_rawat;
    public string $no_rkm_medis;
    public $regPeriksa;
    public $selectedResume = null;

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter'])->findOrFail($this->no_rawat);
        $this->no_rkm_medis = $this->regPeriksa->no_rkm_medis;
    }

    public function viewResume($no_rawat)
    {
        $this->selectedResume = ResumePasien::with([
            'regPeriksa.pasien',
            'regPeriksa.dokter',
            'dokter'
        ])->find($no_rawat);
        
        $this->dispatch('open-modal', 'view-resume-modal');
    }

    public function delete($no_rawat)
    {
        $resume = ResumePasien::findOrFail($no_rawat);

        try {
            $resume->delete();
            $this->dispatch('swal', [
                'title' => 'Berhasil!',
                'text' => 'Resume medis rawat jalan berhasil dihapus.',
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
        // Fetch resumes for the patient of the current visit across ALL their visits
        $resumes = ResumePasien::with([
            'regPeriksa.pasien',
            'regPeriksa.dokter',
        ])
        ->whereHas('regPeriksa', function($query) {
            $query->where('no_rkm_medis', $this->no_rkm_medis);
        })
        ->orderByDesc('no_rawat') // Chronological order by visit number
        ->paginate(10);

        return view('livewire.modul.rawat-jalan.sub-rawat-jalan.resume-pasien.index', [
            'resumes' => $resumes
        ]);
    }
}
