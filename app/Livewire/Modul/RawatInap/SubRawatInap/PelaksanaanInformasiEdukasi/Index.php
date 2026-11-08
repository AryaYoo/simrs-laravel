<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\PelaksanaanInformasiEdukasi;

use Livewire\Component;
use App\Models\RegPeriksa;
use App\Models\Petugas;
use App\Models\Pegawai;
use App\Models\PelaksanaanInformasiEdukasi;
use App\Models\TemplatePelaksanaanInformasiEdukasi;
use App\Repositories\RawatInap\PelaksanaanInformasiEdukasiRepository;
use App\Livewire\Concerns\WithOptimisticLocking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    use WithOptimisticLocking;

    public $noRawat;
    public $regPeriksa;
    public $edukasiList = [];
    public $selectedEdukasi = null;
    public bool $isEmbedded = false;

    // Form State
    public $tanggal_date;
    public $tanggal_time;
    public $autoTime = true;
    public $nik;
    public $nmPetugas;
    public $petugasSearch = '';
    public $petugasList = [];

    public $materi_edukasi = '';
    public $keterangan = '';
    public $diberikan_pada = 'Pasien';
    public $keterangan_diberikan_pada = '';
    public $lama_edukasi = '';
    public $metode_edukasi = 'Ceramah';
    public $hasil_verifikasi = 'Sudah Mengerti';
    public $status = 'Awal';

    public $isPanelOpen = false;
    public $isEditMode = false;
    public $old_tanggal = null;

    // Template Selector State
    public $showTemplateModal = false;
    public $templateSearch = '';

    public function mount($no_rawat)
    {
        $this->noRawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'poliklinik'])
            ->where('no_rawat', $this->noRawat)
            ->firstOrFail();

        $this->initializeLock($this->regPeriksa);
        $this->initDefaultPetugas();
        $this->loadData();

        $this->tanggal_date = now()->format('Y-m-d');
        $this->tanggal_time = now()->format('H:i');
    }

    public function loadData(?PelaksanaanInformasiEdukasiRepository $repository = null)
    {
        $repository = $repository ?? new PelaksanaanInformasiEdukasiRepository();
        $this->edukasiList = $repository->getByNoRawat($this->noRawat);
        if ($this->edukasiList->isNotEmpty() && !$this->selectedEdukasi) {
            $this->selectedEdukasi = $this->edukasiList->first();
        }
    }

    protected function initDefaultPetugas()
    {
        $user = auth()->user();
        if (!$user) {
            return;
        }

        $petugas = null;

        // 1. Cek langsung tabel Petugas berdasarkan username (NIP)
        if (!empty($user->username)) {
            $petugas = Petugas::where('nip', $user->username)->first();
        }

        // 2. Cek description user jika menyimpan NIP/kode dokter
        if (!$petugas && !empty($user->description)) {
            $petugas = Petugas::where('nip', $user->description)->first();
        }

        // 3. Cek tabel Pegawai berdasarkan username (NIK), lalu cocokkan ke Petugas
        if (!$petugas && !empty($user->username)) {
            $pegawai = Pegawai::find($user->username);
            if ($pegawai) {
                $petugas = Petugas::where('nip', $pegawai->nik)->first()
                    ?? Petugas::where('nama', 'like', '%' . $pegawai->nama . '%')->first();
            }
        }

        // 4. Cek jika username adalah kode dokter di tabel Dokter, lalu cocokkan ke Petugas
        if (!$petugas && !empty($user->username)) {
            $dokter = \App\Models\Dokter::where('kd_dokter', $user->username)->first();
            if ($dokter) {
                $petugas = Petugas::where('nip', $dokter->kd_dokter)->first()
                    ?? Petugas::where('nama', 'like', '%' . $dokter->nm_dokter . '%')->first();
            }
        }

        // 5. Cek berdasarkan nama lengkap user di tabel Petugas
        if (!$petugas && !empty($user->fullname)) {
            $petugas = Petugas::where('nama', 'like', '%' . $user->fullname . '%')->first();
        }

        // 6. Fallback jika masih belum cocok, ambil petugas aktif yang memiliki nama valid
        if (!$petugas) {
            $petugas = Petugas::where('status', '1')
                ->where('nip', '!=', '-')
                ->whereNotNull('nama')
                ->where('nama', '!=', '')
                ->orderBy('nama')
                ->first();
        }

        if ($petugas) {
            $this->nik = $petugas->nip;
            $this->nmPetugas = $petugas->nama ?: ($user->fullname ?: $petugas->nip);
            $this->petugasSearch = '';
        } else {
            $this->nik = $user->username;
            $this->nmPetugas = $user->fullname ?: $user->username;
        }
    }

    public function updatedPetugasSearch($val)
    {
        if (strlen($val) < 2) {
            $this->petugasList = [];
            return;
        }

        $this->petugasList = Petugas::where('nama', 'like', "%{$val}%")
            ->orWhere('nip', 'like', "%{$val}%")
            ->limit(10)
            ->get();
    }

    public function selectPetugas($nip, $nama)
    {
        $this->nik = $nip;
        $this->nmPetugas = $nama;
        $this->petugasSearch = '';
        $this->petugasList = [];
    }

    public function clearPetugas()
    {
        $this->nik = null;
        $this->nmPetugas = null;
        $this->petugasSearch = '';
        $this->petugasList = [];
    }

    public function openPanel()
    {
        $this->resetValidation();
        $this->isEditMode = false;
        $this->old_tanggal = null;

        if ($this->autoTime) {
            $this->tanggal_date = now()->format('Y-m-d');
            $this->tanggal_time = now()->format('H:i');
        }

        $this->initDefaultPetugas();
        $this->materi_edukasi = '';
        $this->keterangan = '';
        $this->diberikan_pada = 'Pasien';
        $this->keterangan_diberikan_pada = '';
        $this->lama_edukasi = '';
        $this->metode_edukasi = 'Ceramah';
        $this->hasil_verifikasi = 'Sudah Mengerti';
        $this->status = 'Awal';

        $this->isPanelOpen = true;
    }

    public function closePanel()
    {
        $this->isPanelOpen = false;
        $this->resetValidation();
    }

    public function showDetail(string $tanggal)
    {
        $this->selectedEdukasi = PelaksanaanInformasiEdukasi::with(['pegawai', 'petugas'])
            ->where('no_rawat', $this->noRawat)
            ->where('tanggal', $tanggal)
            ->first();
    }

    public function edit(string $tanggal)
    {
        $this->resetValidation();
        $item = PelaksanaanInformasiEdukasi::with(['pegawai', 'petugas'])
            ->where('no_rawat', $this->noRawat)
            ->where('tanggal', $tanggal)
            ->firstOrFail();

        $this->isEditMode = true;
        $this->old_tanggal = $item->tanggal ? $item->tanggal->format('Y-m-d H:i:s') : $tanggal;

        if ($item->tanggal) {
            $this->tanggal_date = $item->tanggal->format('Y-m-d');
            $this->tanggal_time = $item->tanggal->format('H:i');
        }

        $this->nik = $item->nik;
        $this->nmPetugas = $item->pegawai->nama ?? ($item->petugas->nama ?? $item->nik);

        $this->materi_edukasi = $item->materi_edukasi;
        $this->keterangan = $item->keterangan;
        $this->diberikan_pada = $item->diberikan_pada ?: 'Pasien';
        $this->keterangan_diberikan_pada = $item->keterangan_diberikan_pada;
        $this->lama_edukasi = $item->lama_edukasi;
        $this->metode_edukasi = $item->metode_edukasi ?: 'Ceramah';
        $this->hasil_verifikasi = $item->hasil_verifikasi ?: 'Sudah Mengerti';
        $this->status = $item->status ?: 'Awal';

        $this->isPanelOpen = true;
    }

    public function applyTemplate($noTemplate)
    {
        $template = TemplatePelaksanaanInformasiEdukasi::find($noTemplate);
        if ($template) {
            $this->materi_edukasi = $template->materi_edukasi;
            if ($template->lama_edukasi) {
                $this->lama_edukasi = $template->lama_edukasi;
            }
            if ($template->metode_edukasi) {
                $this->metode_edukasi = $template->metode_edukasi;
            }
            $this->showTemplateModal = false;
            $this->dispatch('swal:toast', [
                'type' => 'success',
                'title' => 'Template edukasi berhasil diterapkan'
            ]);
        }
    }

    public function save(PelaksanaanInformasiEdukasiRepository $repository)
    {
        $this->validate([
            'tanggal_date' => 'required|date',
            'tanggal_time' => 'required',
            'nik'          => 'required',
            'materi_edukasi' => 'required|string|max:1000',
            'keterangan'   => 'nullable|string|max:50',
            'diberikan_pada' => 'required|in:Pasien,Keluarga,Lain-lain',
            'keterangan_diberikan_pada' => 'nullable|string|max:40',
            'lama_edukasi' => 'nullable|string|max:10',
            'metode_edukasi' => 'required|in:Ceramah,Diskusi,Demonstrasi',
            'hasil_verifikasi' => 'required|in:Sudah Mengerti,Re-Edukasi,Re-Demonstrasi',
            'status'       => 'required|in:Awal,Ulang',
        ], [
            'tanggal_date.required' => 'Tanggal harus diisi',
            'tanggal_time.required' => 'Jam harus diisi',
            'nik.required'          => 'Petugas pemeriksa / edukator harus dipilih',
            'materi_edukasi.required' => 'Materi edukasi harus diisi',
        ]);

        $fullTanggal = Carbon::parse("{$this->tanggal_date} {$this->tanggal_time}")->format('Y-m-d H:i:s');

        // Pastikan nik terdaftar di pegawai (foreign key requirement)
        $pegawaiExists = Pegawai::where('nik', $this->nik)->exists();
        if (!$pegawaiExists) {
            // Cek apakah ada di petugas
            $petugasRecord = Petugas::where('nip', $this->nik)->first();
            if ($petugasRecord) {
                Pegawai::firstOrCreate(
                    ['nik' => $this->nik],
                    [
                        'nama' => $petugasRecord->nama ?: $this->nmPetugas,
                        'jk' => 'L',
                        'jbtn' => 'Petugas',
                        'jnj_jabatan' => '-',
                        'departemen' => '-',
                        'bidang' => '-',
                        'stts_aktif' => 'AKTIF',
                    ]
                );
            }
        }

        $payload = [
            'no_rawat'                  => $this->noRawat,
            'tanggal'                   => $fullTanggal,
            'nik'                       => $this->nik,
            'materi_edukasi'            => $this->materi_edukasi,
            'keterangan'                => $this->keterangan ?: '',
            'diberikan_pada'            => $this->diberikan_pada,
            'keterangan_diberikan_pada' => $this->keterangan_diberikan_pada ?: '',
            'lama_edukasi'              => $this->lama_edukasi ?: '',
            'metode_edukasi'            => $this->metode_edukasi,
            'hasil_verifikasi'          => $this->hasil_verifikasi,
            'status'                    => $this->status,
        ];

        try {
            if ($this->isEditMode && $this->old_tanggal) {
                $repository->update($this->noRawat, $this->old_tanggal, $payload);
                $message = 'Data pelaksanaan informasi & edukasi berhasil diperbarui.';
            } else {
                $repository->store($payload);
                $message = 'Data pelaksanaan informasi & edukasi berhasil disimpan.';
            }

            $this->loadData($repository);
            $this->closePanel();

            $this->dispatch('swal:toast', [
                'type' => 'success',
                'title' => $message
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving Pelaksanaan Informasi & Edukasi: ' . $e->getMessage());
            $this->dispatch('swal:modal', [
                'icon' => 'error',
                'title' => 'Gagal Menyimpan',
                'text' => 'Terjadi kesalahan sistem saat menyimpan data: ' . $e->getMessage()
            ]);
        }
    }

    public function delete(string $tanggal, PelaksanaanInformasiEdukasiRepository $repository)
    {
        try {
            $repository->delete($this->noRawat, $tanggal);
            $this->loadData($repository);

            if ($this->selectedEdukasi && $this->selectedEdukasi->tanggal->format('Y-m-d H:i:s') === $tanggal) {
                $this->selectedEdukasi = $this->edukasiList->first();
            }

            $this->dispatch('swal:toast', [
                'type' => 'success',
                'title' => 'Data edukasi berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting Pelaksanaan Informasi & Edukasi: ' . $e->getMessage());
            $this->dispatch('swal:modal', [
                'icon' => 'error',
                'title' => 'Gagal Menghapus',
                'text' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }

    public function getTemplatesProperty()
    {
        $query = TemplatePelaksanaanInformasiEdukasi::query();
        if (!empty($this->templateSearch)) {
            $query->where('materi_edukasi', 'like', "%{$this->templateSearch}%")
                ->orWhere('no_template', 'like', "%{$this->templateSearch}%");
        }
        return $query->limit(15)->get();
    }

    public function render()
    {
        return view('livewire.modul.rawat-inap.sub-rawat-inap.pelaksanaan-informasi-edukasi.index')
            ->layout('layouts.app');
    }
}
