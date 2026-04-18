<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\ResumePasien;

use App\Models\RegPeriksa;
use App\Models\ResumePasienRanap;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Resume Medis Pasien Rawat Inap'])]
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

    public function render()
    {
        // Fetch resumes for the patient of the current visit across ALL their visits
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
        ->orderByDesc('no_rawat') // Assuming chronological order by visit number
        ->paginate(10);

        return view('livewire.modul.rawat-inap.sub-rawat-inap.resume-pasien.index', [
            'resumes' => $resumes
        ]);
    }
}
