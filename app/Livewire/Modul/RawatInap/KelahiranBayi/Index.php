<?php

namespace App\Livewire\Modul\RawatInap\KelahiranBayi;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Repositories\RawatInap\KelahiranBayiRepository;
use App\Models\PasienBayi;

#[Layout('layouts.app', ['title' => 'Kelahiran Bayi - SIMRS LaraLite'])]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $dari = '';
    public string $sampai = '';
    public string $jk = '';
    public int $perPage = 20;
    
    public string $sortColumn = 'tgl_daftar';
    public string $sortDirection = 'desc';

    // Detail modal
    public ?array $detailBayi = null;

    public function sortBy(string $column): void
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function updatedSearch() { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }
    public function updatedDari() { $this->resetPage(); }
    public function updatedSampai() { $this->resetPage(); }
    public function updatedJk() { $this->resetPage(); }

    public function showDetail(string $noRkmMedis): void
    {
        $bayi = PasienBayi::with(['pasien', 'pegawai'])->find($noRkmMedis);
        if ($bayi) {
            $this->detailBayi = null; // reset dulu agar Livewire re-render
            $this->detailBayi = [
                'no_rkm_medis'       => $bayi->no_rkm_medis,
                'nm_pasien'          => $bayi->pasien->nm_pasien ?? '-',
                'nm_ibu'             => $bayi->pasien->nm_ibu ?? '-',
                'jk'                 => $bayi->pasien->jk ?? '-',
                'tgl_lahir'          => $bayi->pasien->tgl_lahir ?? '-',
                'tgl_daftar'         => $bayi->pasien->tgl_daftar ?? '-',
                'alamat'             => $bayi->pasien->alamat ?? '-',
                'umur'               => $bayi->pasien->umur ?? '-',
                'umur_ibu'           => $bayi->umur_ibu,
                'nama_ayah'          => $bayi->nama_ayah,
                'umur_ayah'          => $bayi->umur_ayah,
                'panjang_badan'      => $bayi->panjang_badan,
                'berat_badan'        => $bayi->berat_badan,
                'lingkar_kepala'     => $bayi->lingkar_kepala,
                'lingkar_dada'       => $bayi->lingkar_dada,
                'lingkar_perut'      => $bayi->lingkar_perut,
                'jam_lahir'          => $bayi->jam_lahir,
                'proses_lahir'       => $bayi->proses_lahir,
                'penyulit_kehamilan' => $bayi->penyulit_kehamilan,
                'anakke'             => $bayi->anakke,
                'g'                  => $bayi->g,
                'p'                  => $bayi->p,
                'a'                  => $bayi->a,
                'penolong_nama'      => $bayi->pegawai->nama ?? '-',
                'penolong_nik'       => $bayi->penolong ?? '-',
                'diagnosa'           => $bayi->diagnosa,
                'ketuban'            => $bayi->ketuban,
                'keterangan'         => $bayi->keterangan,
                'no_skl'             => $bayi->no_skl ?? '-',
                'n1'                 => $bayi->n1,
                'n5'                 => $bayi->n5,
                'n10'                => $bayi->n10,
                'f1'=>$bayi->f1,'u1'=>$bayi->u1,'t1'=>$bayi->t1,'r1'=>$bayi->r1,'w1'=>$bayi->w1,
                'f5'=>$bayi->f5,'u5'=>$bayi->u5,'t5'=>$bayi->t5,'r5'=>$bayi->r5,'w5'=>$bayi->w5,
                'f10'=>$bayi->f10,'u10'=>$bayi->u10,'t10'=>$bayi->t10,'r10'=>$bayi->r10,'w10'=>$bayi->w10,
                'resusitas'          => $bayi->resusitas,
                'obat_diberikan'     => $bayi->obat_diberikan,
                'mikasi'             => $bayi->mikasi,
                'mikonium'           => $bayi->mikonium,
            ];
            $this->dispatch('open-detail-modal');
        }
    }

    public function closeDetail(): void
    {
        $this->detailBayi = null;
        $this->dispatch('close-detail-modal');
    }

    public function deleteData(string $noRkmMedis, KelahiranBayiRepository $repo): void
    {
        try {
            $deleted = $repo->delete($noRkmMedis);
            if ($deleted) {
                $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => 'Data kelahiran bayi berhasil dihapus.', 'icon' => 'success']);
            } else {
                $this->dispatch('swal', ['title' => 'Gagal!', 'text' => 'Data gagal dihapus atau tidak ditemukan.', 'icon' => 'error']);
            }
        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Terjadi Kesalahan!', 'text' => 'Tidak dapat menghapus data: ' . $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function render(KelahiranBayiRepository $repository)
    {
        return view('livewire.modul.rawat-inap.kelahiran-bayi.index', [
            'bayis' => $repository->getPaginatedData($this->search, $this->dari, $this->sampai, $this->jk, $this->perPage, $this->sortColumn, $this->sortDirection)
        ]);
    }
}
