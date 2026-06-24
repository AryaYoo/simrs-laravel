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
    public bool $showOtherVisits = false;

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
        $query = ResumePasien::with([
            'regPeriksa.pasien',
            'regPeriksa.dokter',
        ]);

        if ($this->showOtherVisits) {
            $query->whereHas('regPeriksa', function($q) {
                $q->where('no_rkm_medis', $this->no_rkm_medis);
            });
        } else {
            $query->where('no_rawat', $this->no_rawat);
        }

        $resumes = $query->orderByDesc('no_rawat')->paginate(10);

        return view('livewire.modul.rawat-jalan.sub-rawat-jalan.resume-pasien.index', [
            'resumes'      => $resumes,
            'resumeExists' => ResumePasien::where('no_rawat', $this->no_rawat)->exists(),
            'formUrl'      => route('modul.rawat-jalan.sub-rawat-jalan.resume-form', str_replace('/', '-', $this->no_rawat)),
        ]);
    }
}
