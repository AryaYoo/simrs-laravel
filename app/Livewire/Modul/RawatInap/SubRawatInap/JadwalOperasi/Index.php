<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\JadwalOperasi;

use App\Livewire\Concerns\WithOptimisticLocking;
use App\Models\BookingOperasi;
use App\Models\RegPeriksa;
use App\Models\Dokter;
use App\Models\PaketOperasi;
use App\Models\RuangOk;
use App\Repositories\RawatInap\BookingOperasiRepository;
use Carbon\Carbon;
use Exception;
use Livewire\Component;

class Index extends Component
{
    use WithOptimisticLocking;

    public $no_rawat;
    public $pasien;

    public $form = [
        'tanggal'        => '',
        'jam_mulai'      => '',
        'jam_selesai'    => '',
        'status'         => 'Menunggu',
        'kd_ruang_ok'    => '',
        'nm_ruang_ok'    => '',
        'kd_dokter'      => '',
        'nm_dokter'      => '',
        'kode_paket'     => '',
        'nm_perawatan'   => '',
    ];

    public $originalKeys = []; // To store original keys during edit
    public $isEdit = false;

    // Properties for Lookups
    public $searchDokter = '';
    public $searchPaket = '';
    public $searchRuang = '';

    protected $repository;

    public function boot(BookingOperasiRepository $repository)
    {
        $this->repository = $repository;
    }

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->pasien   = RegPeriksa::with(['pasien'])->where('no_rawat', $this->no_rawat)->firstOrFail();
        $this->resetForm();
    }

    public function resetForm()
    {
        $now = Carbon::now();
        $this->form = [
            'tanggal'        => $now->format('Y-m-d'),
            'jam_mulai'      => $now->format('H:i:00'),
            'jam_selesai'    => $now->format('H:i:00'),
            'status'         => 'Menunggu',
            'kd_ruang_ok'    => '',
            'nm_ruang_ok'    => '',
            'kd_dokter'      => '',
            'nm_dokter'      => '',
            'kode_paket'     => '',
            'nm_perawatan'   => '',
        ];
        $this->isEdit = false;
        $this->originalKeys = [];
    }

    public function prepareAttach()
    {
        $this->resetForm();
        return true;
    }

    public function prepareEdit($kode_paket, $tanggal, $jam_mulai)
    {
        $keys = [
            'no_rawat' => $this->no_rawat,
            'kode_paket' => $kode_paket,
            'tanggal' => $tanggal,
            'jam_mulai' => $jam_mulai
        ];

        $query = BookingOperasi::with(['dokter', 'paketOperasi', 'ruangOk']);
        foreach ($keys as $k => $v) {
            $query->where($k, $v);
        }
        $record = $query->first();

        if ($record) {
            $this->isEdit = true;
            $this->originalKeys = $keys;

            $this->form = [
                'tanggal'        => Carbon::parse($record->tanggal)->format('Y-m-d'),
                'jam_mulai'      => Carbon::parse($record->jam_mulai)->format('H:i:s'),
                'jam_selesai'    => $record->jam_selesai ? Carbon::parse($record->jam_selesai)->format('H:i:s') : '',
                'status'         => $record->status ?? 'Menunggu',
                'kd_ruang_ok'    => $record->kd_ruang_ok,
                'nm_ruang_ok'    => $record->ruangOk->nm_ruang_ok ?? '',
                'kd_dokter'      => $record->kd_dokter,
                'nm_dokter'      => $record->dokter->nm_dokter ?? '',
                'kode_paket'     => $record->kode_paket,
                'nm_perawatan'   => $record->paketOperasi->nm_perawatan ?? '',
            ];

            $this->initializeLock($record);
            return true;
        } else {
            $this->dispatch('sweet-alert', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Data tidak ditemukan.']);
            return false;
        }
    }

    // Lookup Selections
    public function selectDokter($kdDokter, $nmDokter)
    {
        $this->form['kd_dokter'] = $kdDokter;
        $this->form['nm_dokter'] = $nmDokter;
        $this->dispatch('close-modal-dokter');
    }

    public function selectPaket($kodePaket, $nmPerawatan)
    {
        $this->form['kode_paket']   = $kodePaket;
        $this->form['nm_perawatan'] = $nmPerawatan;
        $this->dispatch('close-modal-paket');
    }

    public function selectRuangOk($kdRuang, $nmRuang)
    {
        $this->form['kd_ruang_ok'] = $kdRuang;
        $this->form['nm_ruang_ok'] = $nmRuang;
        $this->dispatch('close-modal-ruang');
    }

    public function save()
    {
        $this->validate([
            'form.tanggal'       => 'required|date',
            'form.jam_mulai'     => 'required',
            'form.status'        => 'required',
            'form.kd_ruang_ok'   => 'required',
            'form.kd_dokter'     => 'required',
            'form.kode_paket'    => 'required',
        ], [
            'form.kd_ruang_ok.required' => 'Ruang OK harus diisi.',
            'form.kd_dokter.required'   => 'Operator harus diisi.',
            'form.kode_paket.required'  => 'Operasi (Paket) harus diisi.',
        ]);

        try {
            $data = [
                'no_rawat'    => $this->no_rawat,
                'kode_paket'  => $this->form['kode_paket'],
                'tanggal'     => $this->form['tanggal'],
                'jam_mulai'   => $this->form['jam_mulai'],
                'jam_selesai' => empty($this->form['jam_selesai']) ? null : $this->form['jam_selesai'],
                'status'      => $this->form['status'],
                'kd_dokter'   => $this->form['kd_dokter'],
                'kd_ruang_ok' => $this->form['kd_ruang_ok'],
            ];

            if ($this->isEdit) {
                // Fetch record for locking
                $query = BookingOperasi::query();
                foreach ($this->originalKeys as $k => $v) {
                    $query->where($k, $v);
                }
                $record = $query->first();

                if ($record) {
                    $this->validateLock($record);
                }

                // If keys changed, we delete old and create new to avoid composite PK issues.
                $keysChanged = false;
                foreach (['kode_paket', 'tanggal', 'jam_mulai'] as $key) {
                    if ($this->originalKeys[$key] !== $data[$key]) {
                        $keysChanged = true;
                        break;
                    }
                }

                if ($keysChanged) {
                    // Check if new keys already exist
                    if ($this->repository->exists([
                        'no_rawat' => $this->no_rawat,
                        'kode_paket' => $data['kode_paket'],
                        'tanggal' => $data['tanggal'],
                        'jam_mulai' => $data['jam_mulai'],
                    ])) {
                        throw new Exception('Jadwal operasi dengan paket, tanggal, dan jam tersebut sudah ada.');
                    }
                    $this->repository->delete($this->originalKeys);
                    $this->repository->create($data);
                } else {
                    $this->repository->update($this->originalKeys, $data);
                }

                $this->dispatch('sweet-alert', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Data Jadwal Operasi berhasil diupdate!']);
            } else {
                if ($this->repository->exists([
                    'no_rawat' => $this->no_rawat,
                    'kode_paket' => $data['kode_paket'],
                    'tanggal' => $data['tanggal'],
                    'jam_mulai' => $data['jam_mulai'],
                ])) {
                    throw new Exception('Jadwal operasi dengan paket, tanggal, dan jam tersebut sudah ada.');
                }
                $this->repository->create($data);
                $this->dispatch('sweet-alert', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Data Jadwal Operasi berhasil ditambahkan!']);
            }

            $this->dispatch('close-modal');
            $this->resetForm();

        } catch (Exception $e) {
            $this->dispatch('sweet-alert', ['icon' => 'error', 'title' => 'Gagal', 'text' => $e->getMessage()]);
        }
    }

    public function delete($kode_paket, $tanggal, $jam_mulai)
    {
        try {
            $keys = [
                'no_rawat' => $this->no_rawat,
                'kode_paket' => $kode_paket,
                'tanggal' => $tanggal,
                'jam_mulai' => $jam_mulai
            ];
            $this->repository->delete($keys);
            $this->dispatch('sweet-alert', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Data Jadwal Operasi berhasil dihapus!']);
        } catch (Exception $e) {
            $this->dispatch('sweet-alert', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function getDoktersProperty()
    {
        return Dokter::where('status', '1')
            ->when($this->searchDokter, function($q) {
                $q->where('nm_dokter', 'like', '%'.$this->searchDokter.'%')
                  ->orWhere('kd_dokter', 'like', '%'.$this->searchDokter.'%');
            })
            ->limit(50)
            ->get();
    }

    public function getPaketsProperty()
    {
        return PaketOperasi::when($this->searchPaket, function($q) {
                $q->where('nm_perawatan', 'like', '%'.$this->searchPaket.'%')
                  ->orWhere('kode_paket', 'like', '%'.$this->searchPaket.'%');
            })
            ->limit(50)
            ->get();
    }

    public function getRuangsProperty()
    {
        return RuangOk::when($this->searchRuang, function($q) {
                $q->where('nm_ruang_ok', 'like', '%'.$this->searchRuang.'%')
                  ->orWhere('kd_ruang_ok', 'like', '%'.$this->searchRuang.'%');
            })
            ->limit(50)
            ->get();
    }

    public function render()
    {
        return view('livewire.modul.rawat-inap.sub-rawat-inap.jadwal-operasi.index', [
            'dataOperasi' => $this->repository->getByNoRawat($this->no_rawat),
        ]);
    }
}
