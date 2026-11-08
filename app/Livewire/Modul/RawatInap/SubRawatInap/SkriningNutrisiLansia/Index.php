<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\SkriningNutrisiLansia;

use Livewire\Component;
use App\Models\RegPeriksa;
use App\Models\Pegawai;
use App\Models\Petugas;
use App\Livewire\Concerns\WithOptimisticLocking;
use App\Repositories\RawatInap\SkriningNutrisiLansiaRepository;
use Illuminate\Validation\Rule;

class Index extends Component
{
    use WithOptimisticLocking;

    public $noRawat;
    public $regPeriksa;
    public $skrinings = [];
    public $selectedSkrining = null;

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

    // MNA Lansia
    public $sg1 = 'Asupan Makan Sangat Berkurang';
    public $nilai1 = '0';
    public $sg2 = 'Penurunan Berat Badan Lebih Dari 3 Kg';
    public $nilai2 = '0';
    public $sg3 = 'Terbatas Dari Tempat Tidur Atau Kursi';
    public $nilai3 = '0';
    public $sg4 = 'Ya';
    public $nilai4 = '0';
    public $sg5 = 'Depresi Berat Atau Kepikunan Berat';
    public $nilai5 = '0';

    public $modeF = 'imt'; // 'imt' | 'lingkar_betis'
    public $sg6 = 'IMT < 19';
    public $nilai6 = '0';

    public $total_hasil = 0;
    public $skor_nutrisi = 'Malnutrisi';

    public function mount($no_rawat, SkriningNutrisiLansiaRepository $repository)
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

    public function loadData(?SkriningNutrisiLansiaRepository $repository = null)
    {
        $repository = $repository ?? new SkriningNutrisiLansiaRepository();
        $this->skrinings = $repository->getByNoRawat($this->noRawat);
        if ($this->skrinings->isNotEmpty()) {
            $this->selectedSkrining = $this->skrinings->first();
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
            $this->nip = $petugas->nip;
            $this->nmPetugas = $petugas->nama ?: ($user->fullname ?: $petugas->nip);
            $this->petugasSearch = '';
        } else {
            $this->nip = $user->username;
            $this->nmPetugas = $user->fullname ?: $user->username;
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

        $this->sg1 = 'Asupan Makan Sangat Berkurang';
        $this->nilai1 = '0';
        $this->sg2 = 'Penurunan Berat Badan Lebih Dari 3 Kg';
        $this->nilai2 = '0';
        $this->sg3 = 'Terbatas Dari Tempat Tidur Atau Kursi';
        $this->nilai3 = '0';
        $this->sg4 = 'Ya';
        $this->nilai4 = '0';
        $this->sg5 = 'Depresi Berat Atau Kepikunan Berat';
        $this->nilai5 = '0';
        $this->modeF = 'imt';
        $this->sg6 = 'IMT < 19';
        $this->nilai6 = '0';
        $this->total_hasil = 0;
        $this->skor_nutrisi = 'Malnutrisi';

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

            $this->sg1 = $skrining->sg1 ?: 'Asupan Makan Sangat Berkurang';
            $this->nilai1 = (string) ($skrining->nilai1 ?? '0');
            $this->sg2 = $skrining->sg2 ?: 'Penurunan Berat Badan Lebih Dari 3 Kg';
            $this->nilai2 = (string) ($skrining->nilai2 ?? '0');
            $this->sg3 = $skrining->sg3 ?: 'Terbatas Dari Tempat Tidur Atau Kursi';
            $this->nilai3 = (string) ($skrining->nilai3 ?? '0');
            $this->sg4 = $skrining->sg4 ?: 'Ya';
            $this->nilai4 = (string) ($skrining->nilai4 ?? '0');
            $this->sg5 = $skrining->sg5 ?: 'Depresi Berat Atau Kepikunan Berat';
            $this->nilai5 = (string) ($skrining->nilai5 ?? '0');

            $this->sg6 = $skrining->sg6 ?: 'IMT < 19';
            $this->nilai6 = (string) ($skrining->nilai6 ?? '0');
            $this->modeF = str_contains($this->sg6, 'Lingkar Betis') ? 'lingkar_betis' : 'imt';

            $this->total_hasil = (int) ($skrining->total_hasil ?? 0);
            $this->skor_nutrisi = $skrining->skor_nutrisi ?: 'Malnutrisi';

            $this->nip = $skrining->nip;
            $this->nmPetugas = $skrining->petugas->nama ?? '';

            $this->isPanelOpen = true;
            $this->dispatch('set-autotime', ['status' => false]);
        }
    }

    public function showDetail()
    {
        $this->selectedSkrining = $this->skrinings->first();
        return true;
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
            case 'Asupan Makan Sangat Berkurang':
                $this->nilai1 = '0';
                break;
            case 'Asupan Makan Agak Berkurang':
                $this->nilai1 = '1';
                break;
            case 'Asupan Makan Tidak Berkurang':
                $this->nilai1 = '2';
                break;
            default:
                $this->nilai1 = '0';
                break;
        }
        $this->calculateTotal();
    }

    public function updatedSg2($value)
    {
        switch ($value) {
            case 'Penurunan Berat Badan Lebih Dari 3 Kg':
                $this->nilai2 = '0';
                break;
            case 'Tidak Tahu':
                $this->nilai2 = '1';
                break;
            case 'Penurunan Berat Badan Antara 1 Hingga 3 Kg':
                $this->nilai2 = '2';
                break;
            case 'Tidak Ada Penurunan Berat Badan':
                $this->nilai2 = '3';
                break;
            default:
                $this->nilai2 = '0';
                break;
        }
        $this->calculateTotal();
    }

    public function updatedSg3($value)
    {
        switch ($value) {
            case 'Terbatas Dari Tempat Tidur Atau Kursi':
                $this->nilai3 = '0';
                break;
            case 'Mampu Bangun Dari Tempat Tidur/Kursi Tetapi Tidak Bepergian Keluar Rumah':
                $this->nilai3 = '1';
                break;
            case 'Dapat Bepergian Keluar Rumah':
                $this->nilai3 = '2';
                break;
            default:
                $this->nilai3 = '0';
                break;
        }
        $this->calculateTotal();
    }

    public function updatedSg4($value)
    {
        if ($value === 'Tidak') {
            $this->nilai4 = '1';
        } else {
            $this->nilai4 = '0';
        }
        $this->calculateTotal();
    }

    public function updatedSg5($value)
    {
        switch ($value) {
            case 'Depresi Berat Atau Kepikunan Berat':
                $this->nilai5 = '0';
                break;
            case 'Kepikunan Ringan':
                $this->nilai5 = '1';
                break;
            case 'Tidak Ada Gangguan Psikologis':
                $this->nilai5 = '2';
                break;
            default:
                $this->nilai5 = '0';
                break;
        }
        $this->calculateTotal();
    }

    public function updatedModeF($value)
    {
        if ($value === 'lingkar_betis') {
            $this->sg6 = 'Lingkar Betis < 31';
            $this->nilai6 = '0';
        } else {
            $this->sg6 = 'IMT < 19';
            $this->nilai6 = '0';
        }
        $this->calculateTotal();
    }

    public function updatedSg6($value)
    {
        switch ($value) {
            case 'IMT < 19':
                $this->nilai6 = '0';
                break;
            case '19 Hingga < 21':
                $this->nilai6 = '1';
                break;
            case '21 Hingga < 23':
                $this->nilai6 = '2';
                break;
            case 'IMT >= 23':
                $this->nilai6 = '3';
                break;
            case 'Lingkar Betis < 31':
                $this->nilai6 = '0';
                break;
            case 'Lingkar Betis >= 31':
                $this->nilai6 = '3';
                break;
            default:
                $this->nilai6 = '0';
                break;
        }
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total_hasil = (int) $this->nilai1 + (int) $this->nilai2 + (int) $this->nilai3 + (int) $this->nilai4 + (int) $this->nilai5 + (int) $this->nilai6;

        if ($this->total_hasil >= 12) {
            $this->skor_nutrisi = 'Status Gizi Normal';
        } elseif ($this->total_hasil >= 8) {
            $this->skor_nutrisi = 'Beresiko Malnutrisi';
        } else {
            $this->skor_nutrisi = 'Malnutrisi';
        }
    }

    public function save(SkriningNutrisiLansiaRepository $repository)
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
                'sg1'          => ['required', Rule::in(['Asupan Makan Sangat Berkurang', 'Asupan Makan Agak Berkurang', 'Asupan Makan Tidak Berkurang'])],
                'nilai1'       => ['required', Rule::in(['0', '1', '2', 0, 1, 2])],
                'sg2'          => ['required', Rule::in(['Penurunan Berat Badan Lebih Dari 3 Kg', 'Tidak Tahu', 'Penurunan Berat Badan Antara 1 Hingga 3 Kg', 'Tidak Ada Penurunan Berat Badan'])],
                'nilai2'       => ['required', Rule::in(['0', '1', '2', '3', 0, 1, 2, 3])],
                'sg3'          => ['required', Rule::in(['Terbatas Dari Tempat Tidur Atau Kursi', 'Mampu Bangun Dari Tempat Tidur/Kursi Tetapi Tidak Bepergian Keluar Rumah', 'Dapat Bepergian Keluar Rumah'])],
                'nilai3'       => ['required', Rule::in(['0', '1', '2', 0, 1, 2])],
                'sg4'          => ['required', Rule::in(['Ya', 'Tidak'])],
                'nilai4'       => ['required', Rule::in(['0', '1', 0, 1])],
                'sg5'          => ['required', Rule::in(['Depresi Berat Atau Kepikunan Berat', 'Kepikunan Ringan', 'Tidak Ada Gangguan Psikologis'])],
                'nilai5'       => ['required', Rule::in(['0', '1', '2', 0, 1, 2])],
                'sg6'          => ['required', Rule::in(['IMT < 19', '19 Hingga < 21', '21 Hingga < 23', 'IMT >= 23', 'Lingkar Betis < 31', 'Lingkar Betis >= 31'])],
                'nilai6'       => ['required', Rule::in(['0', '1', '2', '3', 0, 1, 2, 3])],
            ], [
                'tanggal_date.required' => 'Tanggal wajib diisi.',
                'tanggal_time.required' => 'Jam wajib diisi.',
                'nip.required'          => 'Petugas pemeriksa wajib dipilih.',
                'sg1.required'          => 'Pertanyaan A wajib dipilih.',
                'sg2.required'          => 'Pertanyaan B wajib dipilih.',
                'sg3.required'          => 'Pertanyaan C wajib dipilih.',
                'sg4.required'          => 'Pertanyaan D wajib dipilih.',
                'sg5.required'          => 'Pertanyaan E wajib dipilih.',
                'sg6.required'          => 'Pertanyaan F wajib dipilih.',
            ]);

            $this->validateLock($this->regPeriksa->fresh());

            $tanggalFull = $this->tanggal_date . ' ' . $this->tanggal_time . ':00';
            $this->calculateTotal();

            $data = [
                'no_rawat'     => $this->noRawat,
                'tanggal'      => $tanggalFull,
                'td'           => $this->td ?: '',
                'hr'           => $this->hr ?: '',
                'rr'           => $this->rr ?: '',
                'suhu'         => $this->suhu ?: '',
                'bb'           => $this->bb ?: '',
                'tbpb'         => $this->tbpb ?: '',
                'spo2'         => $this->spo2 ?: '',
                'alergi'       => $this->alergi ?: '-',
                'sg1'          => $this->sg1,
                'nilai1'       => (string) $this->nilai1,
                'sg2'          => $this->sg2,
                'nilai2'       => (string) $this->nilai2,
                'sg3'          => $this->sg3,
                'nilai3'       => (string) $this->nilai3,
                'sg4'          => $this->sg4,
                'nilai4'       => (string) $this->nilai4,
                'sg5'          => $this->sg5,
                'nilai5'       => (string) $this->nilai5,
                'sg6'          => $this->sg6,
                'nilai6'       => (string) $this->nilai6,
                'total_hasil'  => $this->total_hasil,
                'skor_nutrisi' => $this->skor_nutrisi,
                'nip'          => $this->nip,
            ];

            $repository->save($data);

            $this->initializeLock($this->regPeriksa->fresh());
            $this->isPanelOpen = false;
            $this->loadData($repository);

            $this->dispatch('swal', [
                'title' => 'Berhasil!',
                'text'  => $this->isEditMode ? 'Data skrining nutrisi lansia berhasil diperbarui.' : 'Data skrining nutrisi lansia berhasil disimpan.',
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

    public function delete(SkriningNutrisiLansiaRepository $repository)
    {
        try {
            $repository->delete($this->noRawat);
            $this->loadData($repository);
            $this->dispatch('swal', [
                'title' => 'Berhasil!',
                'text'  => 'Data skrining nutrisi lansia berhasil dihapus.',
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
        $pemeriksaan = \App\Models\PemeriksaanRanap::where('no_rawat', $this->noRawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->first();

        if (!$pemeriksaan) {
            $pemeriksaan = \App\Models\PemeriksaanRalan::where('no_rawat', $this->noRawat)
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

        return view('livewire.modul.rawat-inap.sub-rawat-inap.skrining-nutrisi-lansia.index', [
            'petugasList' => $petugasList,
        ])->layout('layouts.app', ['title' => 'Skrining Nutrisi Pasien Lansia - Rawat Inap']);
    }
}
