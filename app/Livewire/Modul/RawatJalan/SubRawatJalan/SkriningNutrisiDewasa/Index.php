<?php

namespace App\Livewire\Modul\RawatJalan\SubRawatJalan\SkriningNutrisiDewasa;

use Livewire\Component;
use App\Models\RegPeriksa;
use App\Models\Pegawai;
use App\Models\Petugas;
use App\Livewire\Concerns\WithOptimisticLocking;
use App\Repositories\RawatJalan\SkriningNutrisiDewasaRepository;
use Illuminate\Validation\Rule;

class Index extends Component
{
    use WithOptimisticLocking;

    public $noRawat;
    public $regPeriksa;
    public $skrinings = [];

    // Form State
    public $isPanelOpen = false;
    public $isEditMode = false;
    public $autoTime = true;
    public $tanggal_date;
    public $tanggal_time;

    public $nip;
    public $nmPetugas;
    public $petugasSearch = '';

    public $bb = '';
    public $tbpb = '';
    public $td = '';
    public $hr = '';
    public $rr = '';
    public $suhu = '';
    public $spo2 = '';
    public $alergi = '-';

    public $sg1 = 'Tidak';
    public $nilai1 = '0';
    public $sg2 = 'Tidak';
    public $nilai2 = '0';
    public $total_hasil = 0;

    public function mount($no_rawat, SkriningNutrisiDewasaRepository $repository)
    {
        $this->noRawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'poliklinik'])
            ->where('no_rawat', $this->noRawat)
            ->firstOrFail();

        $this->initializeLock($this->regPeriksa);
        $this->loadData($repository);

        $this->tanggal_date = now()->format('Y-m-d');
        $this->tanggal_time = now()->format('H:i');
        $this->initDefaultPetugas();
    }

    public function loadData(?SkriningNutrisiDewasaRepository $repository = null)
    {
        $repository = $repository ?? new SkriningNutrisiDewasaRepository();
        $this->skrinings = $repository->getByNoRawat($this->noRawat);
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
            $petugas = Petugas::where('nip', $user->username)->where('status', '1')->first();
        }

        // 2. Cek tabel Pegawai berdasarkan username (NIK), lalu cocokkan ke Petugas
        if (!$petugas && !empty($user->username)) {
            $pegawai = Pegawai::find($user->username);
            if ($pegawai) {
                $petugas = Petugas::where('nip', $pegawai->nik)->where('status', '1')->first()
                    ?? Petugas::where('nama', 'like', '%' . $pegawai->nama . '%')->where('status', '1')->first();
            }
        }

        // 3. Cek berdasarkan nama lengkap user di tabel Petugas
        if (!$petugas && !empty($user->fullname)) {
            $petugas = Petugas::where('nama', 'like', '%' . $user->fullname . '%')->where('status', '1')->first();
        }

        // 4. Fallback jika login sebagai akun admin/pengembang
        if (!$petugas) {
            $petugas = Petugas::where('status', '1')->where('nip', '!=', '-')->orderBy('nama')->first();
        }

        if ($petugas) {
            $this->nip = $petugas->nip;
            $this->nmPetugas = $petugas->nama;
            $this->petugasSearch = '';
        }
    }

    public function openPanel()
    {
        $this->isEditMode = false;
        $this->autoTime = true;
        $this->tanggal_date = now()->format('Y-m-d');
        $this->tanggal_time = now()->format('H:i');

        $this->bb = '';
        $this->tbpb = '';
        $this->td = '';
        $this->hr = '';
        $this->rr = '';
        $this->suhu = '';
        $this->spo2 = '';
        $this->alergi = '-';

        $this->sg1 = 'Tidak';
        $this->nilai1 = '0';
        $this->sg2 = 'Tidak';
        $this->nilai2 = '0';
        $this->total_hasil = 0;

        $this->initDefaultPetugas();
        $this->isPanelOpen = true;
        $this->dispatch('set-autotime', ['status' => true]);
    }

    public function edit()
    {
        $skrining = $this->skrinings->first();
        if ($skrining) {
            $this->isEditMode = true;
            $this->autoTime = false;

            if ($skrining->tanggal) {
                $this->tanggal_date = $skrining->tanggal->format('Y-m-d');
                $this->tanggal_time = $skrining->tanggal->format('H:i');
            } else {
                $this->tanggal_date = now()->format('Y-m-d');
                $this->tanggal_time = now()->format('H:i');
            }

            $this->bb = $skrining->bb;
            $this->tbpb = $skrining->tbpb;
            $this->td = $skrining->td;
            $this->hr = $skrining->hr;
            $this->rr = $skrining->rr;
            $this->suhu = $skrining->suhu;
            $this->spo2 = $skrining->spo2;
            $this->alergi = $skrining->alergi ?: '-';

            $this->sg1 = $skrining->sg1 ?: 'Tidak';
            $this->nilai1 = (string) ($skrining->nilai1 ?? '0');
            $this->sg2 = $skrining->sg2 ?: 'Tidak';
            $this->nilai2 = (string) ($skrining->nilai2 ?? '0');
            $this->total_hasil = (int) ($skrining->total_hasil ?? 0);

            $this->nip = $skrining->nip;
            $this->nmPetugas = $skrining->petugas->nama ?? '';

            $this->isPanelOpen = true;
            $this->dispatch('set-autotime', ['status' => false]);
        }
    }

    public function selectPetugas($nip, $nama)
    {
        $this->nip = $nip;
        $this->nmPetugas = $nama;
        $this->petugasSearch = '';
    }

    public function clearPetugas()
    {
        $this->nip = null;
        $this->nmPetugas = '';
        $this->petugasSearch = '';
    }

    public function updatedSg1($value)
    {
        switch ($value) {
            case 'Tidak':
                $this->nilai1 = '0';
                break;
            case 'Tidak Yakin':
                $this->nilai1 = '2';
                break;
            case 'Ya, 1-5 Kg':
                $this->nilai1 = '1';
                break;
            case 'Ya, 6-10 Kg':
                $this->nilai1 = '2';
                break;
            case 'Ya, 11-15 Kg':
                $this->nilai1 = '3';
                break;
            case 'Ya, >15 Kg':
                $this->nilai1 = '4';
                break;
            default:
                $this->nilai1 = '0';
                break;
        }
        $this->calculateTotal();
    }

    public function updatedNilai1()
    {
        $this->calculateTotal();
    }

    public function updatedSg2($value)
    {
        if ($value === 'Ya') {
            $this->nilai2 = '1';
        } else {
            $this->nilai2 = '0';
        }
        $this->calculateTotal();
    }

    public function updatedNilai2()
    {
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total_hasil = (int) $this->nilai1 + (int) $this->nilai2;
    }

    public function save(SkriningNutrisiDewasaRepository $repository)
    {
        try {
            $this->validate([
                'tanggal_date' => 'required|date',
                'tanggal_time' => 'required',
                'nip'          => 'required',
                'bb'           => 'nullable|string|max:5',
                'tbpb'         => 'nullable|string|max:5',
                'td'           => 'nullable|string|max:8',
                'hr'           => 'nullable|string|max:5',
                'rr'           => 'nullable|string|max:5',
                'suhu'         => 'nullable|string|max:5',
                'spo2'         => 'nullable|string|max:5',
                'alergi'       => 'nullable|string|max:100',
                'sg1'          => ['required', Rule::in(['Tidak', 'Tidak Yakin', 'Ya, 1-5 Kg', 'Ya, 6-10 Kg', 'Ya, 11-15 Kg', 'Ya, >15 Kg'])],
                'nilai1'       => ['required', Rule::in(['0', '1', '2', '3', '4', 0, 1, 2, 3, 4])],
                'sg2'          => ['required', Rule::in(['Ya', 'Tidak'])],
                'nilai2'       => ['required', Rule::in(['0', '1', 0, 1])],
            ], [
                'tanggal_date.required' => 'Tanggal wajib diisi.',
                'tanggal_time.required' => 'Jam wajib diisi.',
                'nip.required'          => 'Petugas pemeriksa wajib dipilih.',
                'sg1.required'          => 'Pertanyaan 1 wajib dipilih.',
                'sg1.in'                => 'Pilihan pertanyaan 1 tidak valid.',
                'nilai1.required'       => 'Nilai 1 wajib terisi.',
                'sg2.required'          => 'Pertanyaan 2 wajib dipilih.',
                'sg2.in'                => 'Pilihan pertanyaan 2 tidak valid.',
                'nilai2.required'       => 'Nilai 2 wajib terisi.',
            ]);

            $this->validateLock($this->regPeriksa->fresh());

            $tanggalFull = $this->tanggal_date . ' ' . $this->tanggal_time . ':00';
            $this->calculateTotal();

            $data = [
                'no_rawat'    => $this->noRawat,
                'tanggal'     => $tanggalFull,
                'td'          => $this->td ?: '',
                'hr'          => $this->hr ?: '',
                'rr'          => $this->rr ?: '',
                'suhu'        => $this->suhu ?: '',
                'bb'          => $this->bb ?: '',
                'tbpb'        => $this->tbpb ?: '',
                'spo2'        => $this->spo2 ?: '',
                'alergi'      => $this->alergi ?: '-',
                'sg1'         => $this->sg1,
                'nilai1'      => (string) $this->nilai1,
                'sg2'         => $this->sg2,
                'nilai2'      => (string) $this->nilai2,
                'total_hasil' => $this->total_hasil,
                'nip'         => $this->nip,
            ];

            $repository->save($data);

            $this->initializeLock($this->regPeriksa->fresh());
            $this->isPanelOpen = false;
            $this->loadData($repository);

            $this->dispatch('swal', [
                'title' => 'Berhasil!',
                'text'  => $this->isEditMode ? 'Data skrining nutrisi dewasa berhasil diperbarui.' : 'Data skrining nutrisi dewasa berhasil disimpan.',
                'icon'  => 'success',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->validator->errors()->all())->first();
            $this->dispatch('swal', [
                'title' => 'Validasi Gagal!',
                'text'  => $firstError ?: 'Pastikan kolom wajib (*) sudah terisi dengan benar.',
                'icon'  => 'warning',
            ]);
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Gagal Menyimpan!',
                'text'  => 'Terjadi kesalahan: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }

    public function delete(SkriningNutrisiDewasaRepository $repository)
    {
        try {
            $repository->delete($this->noRawat);
            $this->loadData($repository);
            $this->dispatch('swal', [
                'title' => 'Berhasil!',
                'text'  => 'Data skrining nutrisi dewasa berhasil dihapus.',
                'icon'  => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Gagal!',
                'text'  => 'Gagal menghapus data: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }

    public function fillLatestPemeriksaan()
    {
        $pemeriksaan = \App\Models\PemeriksaanRalan::where('no_rawat', $this->noRawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->first();

        if (!$pemeriksaan) {
            $pemeriksaan = \App\Models\PemeriksaanRanap::where('no_rawat', $this->noRawat)
                ->orderBy('tgl_perawatan', 'desc')
                ->orderBy('jam_rawat', 'desc')
                ->first();
        }

        if ($pemeriksaan) {
            $this->bb = $pemeriksaan->berat ?: $this->bb;
            $this->tbpb = $pemeriksaan->tinggi ?: $this->tbpb;
            $this->td = $pemeriksaan->tensi ?: $this->td;
            $this->hr = $pemeriksaan->nadi ?: $this->hr;
            $this->rr = $pemeriksaan->respirasi ?: $this->rr;
            $this->suhu = $pemeriksaan->suhu_tubuh ?: $this->suhu;
            $this->spo2 = $pemeriksaan->spo2 ?: $this->spo2;
            $this->alergi = $pemeriksaan->alergi ?: ($this->alergi ?: '-');

            $this->dispatch('swal', [
                'title' => 'Berhasil!',
                'text'  => 'Data antropometri & tanda vital berhasil diisi otomatis dari pemeriksaan terakhir.',
                'icon'  => 'success',
            ]);
        } else {
            $this->dispatch('swal', [
                'title' => 'Informasi',
                'text'  => 'Belum ada data pemeriksaan (SOAP) untuk nomor rawat ini.',
                'icon'  => 'info',
            ]);
        }
    }

    public function render()
    {
        $petugasList = [];
        if (strlen($this->petugasSearch) >= 3) {
            $petugasList = Petugas::where('status', '1')
                ->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->petugasSearch . '%')
                      ->orWhere('nip', 'like', '%' . $this->petugasSearch . '%');
                })
                ->limit(10)
                ->get();
        }

        return view('livewire.modul.rawat-jalan.sub-rawat-jalan.skrining-nutrisi-dewasa.index', [
            'petugasList' => $petugasList,
        ])->layout('layouts.app', ['title' => 'Skrining Nutrisi Pasien Dewasa - Rawat Jalan']);
    }
}
