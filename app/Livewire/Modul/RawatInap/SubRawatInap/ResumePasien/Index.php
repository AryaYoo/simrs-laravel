<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\ResumePasien;

use App\Models\RegPeriksa;
use App\Models\ResumePasienRanap;
use App\Livewire\Concerns\WithOptimisticLocking;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Resume Medis Pasien Rawat Inap'])]
class Index extends Component
{
    use WithPagination;
    use WithOptimisticLocking;

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

        // Validate lock before deleting
        $this->validateLock($resume->fresh());

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
