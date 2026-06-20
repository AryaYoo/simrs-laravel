<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\Diagnosa;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Penyakit;
use App\Models\Icd9;
use App\Models\DiagnosaPasien;
use App\Models\ProsedurPasien;
use App\Models\RegPeriksa;
use Illuminate\Support\Facades\DB;
use Exception;

class Index extends Component
{
    use WithPagination;

    public $no_rawat;
    public $activeTab = 'input_data';

    // Status dropdown (Ralan / Ranap)
    public $statusPilihan = 'Ranap';

    // Search properties
    public $searchDiagnosa = '';
    public $searchProsedur = '';

    // Selected items (Cart)
    public $cartDiagnosa = [];
    public $cartProsedur = [];

    // Patient info
    public $pasien;

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->pasien = RegPeriksa::with(['pasien'])->where('no_rawat', $this->no_rawat)->first();

        // Default status sesuai status pasien saat ini (Rawat Inap = Ranap)
        $this->statusPilihan = 'Ranap';
        $this->loadCarts();
    }

    public function updatedStatusPilihan()
    {
        $this->loadCarts();
    }

    public function loadCarts()
    {
        $this->cartDiagnosa = [];
        $savedDiagnosa = DiagnosaPasien::with('penyakit.kategoriPenyakit')->where('no_rawat', $this->no_rawat)->where('status', $this->statusPilihan)->orderBy('prioritas')->get();
        foreach ($savedDiagnosa as $d) {
            $this->cartDiagnosa[] = [
                'kd_penyakit' => $d->kd_penyakit,
                'nm_penyakit' => $d->penyakit->nm_penyakit ?? '-',
                'vc' => $d->penyakit->validcode ?? '',
                'ap' => $d->penyakit->accpdx ?? '',
                'ast' => $d->penyakit->asterisk ?? '',
                'im' => $d->penyakit->im ?? '',
                'urut' => $d->prioritas,
            ];
        }

        $this->cartProsedur = [];
        $savedProsedur = ProsedurPasien::with('icd9')->where('no_rawat', $this->no_rawat)->where('status', $this->statusPilihan)->orderBy('prioritas')->get();
        foreach ($savedProsedur as $p) {
            $this->cartProsedur[] = [
                'kode' => $p->kode,
                'deskripsi_panjang' => $p->icd9->deskripsi_panjang ?? '-',
                'jml' => $p->jumlah ?? 1,
                'vc' => '', // icd9 table might not have vc,ap,im mapped directly if it doesn't exist, we will fallback
                'ap' => '',
                'im' => '',
                'urut' => $p->prioritas,
            ];
        }
    }

    public function updatingSearchDiagnosa()
    {
        $this->resetPage('diagnosaPage');
    }

    public function updatingSearchProsedur()
    {
        $this->resetPage('prosedurPage');
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function pushToCartDiagnosa($kd_penyakit, $nm_penyakit, $vc, $ap, $ast, $im)
    {
        // Check if already in cart
        $exists = collect($this->cartDiagnosa)->where('kd_penyakit', $kd_penyakit)->first();
        if ($exists) {
            $this->dispatch('swal', ['icon' => 'warning', 'title' => 'Peringatan', 'text' => 'Diagnosa ini sudah ada di daftar.']);
            return;
        }

        // Auto-increment urut
        $maxUrut = collect($this->cartDiagnosa)->max('urut') ?? 0;

        $this->cartDiagnosa[] = [
            'kd_penyakit' => $kd_penyakit,
            'nm_penyakit' => $nm_penyakit,
            'vc' => $vc,
            'ap' => $ap,
            'ast' => $ast,
            'im' => $im,
            'urut' => $maxUrut + 1,
        ];
    }

    public function removeFromCartDiagnosa($index)
    {
        if (isset($this->cartDiagnosa[$index])) {
            unset($this->cartDiagnosa[$index]);
            $this->cartDiagnosa = array_values($this->cartDiagnosa); // Re-index array
        }
    }

    public function pushToCartProsedur($kode, $deskripsi_panjang)
    {
        $exists = collect($this->cartProsedur)->where('kode', $kode)->first();
        if ($exists) {
            $this->dispatch('swal', ['icon' => 'warning', 'title' => 'Peringatan', 'text' => 'Prosedur ini sudah ada di daftar.']);
            return;
        }

        $maxUrut = collect($this->cartProsedur)->max('urut') ?? 0;

        $this->cartProsedur[] = [
            'kode' => $kode,
            'deskripsi_panjang' => $deskripsi_panjang,
            'jml' => 1,
            'vc' => '',
            'ap' => '',
            'im' => '',
            'urut' => $maxUrut + 1,
        ];
    }

    public function removeFromCartProsedur($index)
    {
        if (isset($this->cartProsedur[$index])) {
            unset($this->cartProsedur[$index]);
            $this->cartProsedur = array_values($this->cartProsedur); // Re-index array
        }
    }

    public function simpanDiagnosa()
    {
        if (empty($this->cartDiagnosa)) {
            $this->dispatch('swal', ['icon' => 'warning', 'title' => 'Peringatan', 'text' => 'Daftar diagnosa kosong!']);
            return;
        }

        try {
            DB::beginTransaction();

            // Full Sync: Delete all existing for this patient + status
            DiagnosaPasien::where('no_rawat', $this->no_rawat)
                          ->where('status', $this->statusPilihan)
                          ->delete();

            // Insert from cart
            foreach ($this->cartDiagnosa as $item) {
                DiagnosaPasien::create([
                    'no_rawat' => $this->no_rawat,
                    'kd_penyakit' => $item['kd_penyakit'],
                    'status' => $this->statusPilihan,
                    'prioritas' => empty($item['urut']) ? 1 : $item['urut'],
                    'status_penyakit' => 'Lama',
                ]);
            }

            DB::commit();

            $this->loadCarts();
            $this->dispatch('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Data diagnosa berhasil disimpan!']);

        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function simpanProsedur()
    {
        if (empty($this->cartProsedur)) {
            $this->dispatch('swal', ['icon' => 'warning', 'title' => 'Peringatan', 'text' => 'Daftar prosedur kosong!']);
            return;
        }

        try {
            DB::beginTransaction();

            // Full Sync: Delete all existing for this patient + status
            ProsedurPasien::where('no_rawat', $this->no_rawat)
                          ->where('status', $this->statusPilihan)
                          ->delete();

            // Insert from cart
            foreach ($this->cartProsedur as $item) {
                ProsedurPasien::create([
                    'no_rawat' => $this->no_rawat,
                    'kode' => $item['kode'],
                    'status' => $this->statusPilihan,
                    'prioritas' => empty($item['urut']) ? 1 : $item['urut'],
                    'jumlah' => empty($item['jml']) ? '1' : (string)max(1, (int)$item['jml']),
                ]);
            }

            DB::commit();

            $this->loadCarts();
            $this->dispatch('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Data prosedur berhasil disimpan!']);

        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function hapusDiagnosa($kd_penyakit)
    {
        try {
            DiagnosaPasien::where('no_rawat', $this->no_rawat)
                          ->where('kd_penyakit', $kd_penyakit)
                          ->where('status', $this->statusPilihan)
                          ->delete();

            $this->dispatch('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Data diagnosa berhasil dihapus!']);
        } catch (Exception $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function hapusProsedur($kode)
    {
        try {
            ProsedurPasien::where('no_rawat', $this->no_rawat)
                          ->where('kode', $kode)
                          ->where('status', $this->statusPilihan)
                          ->delete();

            $this->dispatch('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Data prosedur berhasil dihapus!']);
        } catch (Exception $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        // Fetch Master Diagnosa with kategori relation
        $masterDiagnosa = Penyakit::with('kategoriPenyakit')
            ->when($this->searchDiagnosa, function($q) {
                $q->where('nm_penyakit', 'like', '%'.$this->searchDiagnosa.'%')
                  ->orWhere('kd_penyakit', 'like', '%'.$this->searchDiagnosa.'%');
            })->paginate(10, ['*'], 'diagnosaPage')->onEachSide(1);

        // Fetch Master Prosedur
        $masterProsedur = Icd9::when($this->searchProsedur, function($q) {
            $q->where('deskripsi_panjang', 'like', '%'.$this->searchProsedur.'%')
              ->orWhere('deskripsi_pendek', 'like', '%'.$this->searchProsedur.'%')
              ->orWhere('kode', 'like', '%'.$this->searchProsedur.'%');
        })->paginate(10, ['*'], 'prosedurPage')->onEachSide(1);

        // Fetch Saved Diagnosa
        $savedDiagnosa = DiagnosaPasien::with('penyakit.kategoriPenyakit')
            ->where('no_rawat', $this->no_rawat)
            ->where('status', $this->statusPilihan)
            ->orderBy('prioritas')
            ->get();

        // Fetch Saved Prosedur
        $savedProsedur = ProsedurPasien::with('icd9')
            ->where('no_rawat', $this->no_rawat)
            ->where('status', $this->statusPilihan)
            ->orderBy('prioritas')
            ->get();

        return view('livewire.modul.rawat-inap.sub-rawat-inap.diagnosa.index', [
            'masterDiagnosa' => $masterDiagnosa,
            'masterProsedur' => $masterProsedur,
            'savedDiagnosa' => $savedDiagnosa,
            'savedProsedur' => $savedProsedur,
        ]);
    }
}
