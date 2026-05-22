<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\HasilUsgGynecologi;

use App\Livewire\Concerns\WithOptimisticLocking;
use App\Models\HasilPemeriksaanUsgGynecologi;
use App\Models\RegPeriksa;
use App\Repositories\RawatInap\HasilPemeriksaanUsgGynecologiRepository;
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
        'jam'            => '',
        'kd_dokter'      => '',
        'nm_dokter'      => '',
        'diagnosa_klinis'=> '',
        'kiriman_dari'   => '',
        'uterus'         => '',
        'parametrium'    => '',
        'ovarium'        => '',
        'doppler'        => '',
        'kesimpulan'     => '',
    ];

    public $isEdit = false;
    public $isAutoTimestamp = true;

    protected $repository;

    public function boot(HasilPemeriksaanUsgGynecologiRepository $repository)
    {
        $this->repository = $repository;
    }

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->pasien   = RegPeriksa::with(['pasien', 'dokter'])->where('no_rawat', $this->no_rawat)->firstOrFail();
        $this->resetForm();
    }

    public function resetForm()
    {
        $now = Carbon::now();
        $this->form = [
            'tanggal'        => $now->format('Y-m-d'),
            'jam'            => $now->format('H:i:s'),
            'kd_dokter'      => $this->pasien->kd_dokter ?? '',
            'nm_dokter'      => $this->pasien->dokter->nm_dokter ?? '',
            'diagnosa_klinis'=> '',
            'kiriman_dari'   => '',
            'uterus'         => '',
            'parametrium'    => '',
            'ovarium'        => '',
            'doppler'        => '',
            'kesimpulan'     => '',
        ];
        $this->isEdit = false;
    }

    public function updateTime()
    {
        if ($this->isAutoTimestamp) {
            $now = Carbon::now();
            $this->form['tanggal'] = $now->format('Y-m-d');
            $this->form['jam']     = $now->format('H:i:s');
        }
    }

    public function selectDokter($kdDokter, $nmDokter)
    {
        $this->form['kd_dokter'] = $kdDokter;
        $this->form['nm_dokter'] = $nmDokter;
    }

    public function save()
    {
        $this->validate([
            'form.tanggal'        => 'required|date',
            'form.jam'            => 'required',
            'form.kd_dokter'      => 'required',
            'form.diagnosa_klinis'=> 'nullable|string|max:50',
            'form.kiriman_dari'   => 'nullable|string|max:50',
            'form.uterus'         => 'nullable|string|max:200',
            'form.parametrium'    => 'nullable|string|max:200',
            'form.ovarium'        => 'nullable|string|max:200',
            'form.doppler'        => 'nullable|string|max:200',
            'form.kesimpulan'     => 'nullable|string|max:300',
        ], [
            'form.kd_dokter.required' => 'Dokter DPJP pemeriksa harus diisi.',
        ]);

        try {
            $data = $this->form;
            $data['no_rawat'] = $this->no_rawat;
            $data['tanggal']  = $data['tanggal'] . ' ' . $data['jam'];
            unset($data['jam'], $data['nm_dokter']);

            if ($this->isEdit) {
                $record = HasilPemeriksaanUsgGynecologi::where('no_rawat', $this->no_rawat)->first();
                if ($record) {
                    $this->validateLock($record);
                }
                $this->repository->update($this->no_rawat, $data);
                $this->dispatch('sweet-alert', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Data Hasil USG Gynecologi berhasil diupdate!']);
            } else {
                if ($this->repository->exists($this->no_rawat)) {
                    throw new Exception('Data Hasil USG Gynecologi sudah ada. Silakan gunakan fitur edit.');
                }
                $this->repository->create($data);
                $this->dispatch('sweet-alert', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Data Hasil USG Gynecologi berhasil ditambahkan!']);
            }

            $this->dispatch('close-modal');
            $this->resetForm();

        } catch (Exception $e) {
            $this->dispatch('sweet-alert', ['icon' => 'error', 'title' => 'Gagal', 'text' => $e->getMessage()]);
        }
    }

    public function edit()
    {
        $record = HasilPemeriksaanUsgGynecologi::with('dokter')->where('no_rawat', $this->no_rawat)->first();

        if ($record) {
            $this->isEdit          = true;
            $this->isAutoTimestamp = false;
            $dt = Carbon::parse($record->tanggal);

            $this->form = [
                'tanggal'        => $dt->format('Y-m-d'),
                'jam'            => $dt->format('H:i:s'),
                'kd_dokter'      => $record->kd_dokter,
                'nm_dokter'      => $record->dokter->nm_dokter ?? '',
                'diagnosa_klinis'=> $record->diagnosa_klinis ?? '',
                'kiriman_dari'   => $record->kiriman_dari ?? '',
                'uterus'         => $record->uterus ?? '',
                'parametrium'    => $record->parametrium ?? '',
                'ovarium'        => $record->ovarium ?? '',
                'doppler'        => $record->doppler ?? '',
                'kesimpulan'     => $record->kesimpulan ?? '',
            ];

            $this->initializeLock($record);
            $this->dispatch('open-modal');
        } else {
            $this->dispatch('sweet-alert', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Data tidak ditemukan.']);
        }
    }

    public function delete()
    {
        try {
            $this->repository->delete($this->no_rawat);
            $this->dispatch('sweet-alert', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Data Hasil USG Gynecologi berhasil dihapus!']);
            $this->resetForm();
        } catch (Exception $e) {
            $this->dispatch('sweet-alert', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.modul.rawat-inap.sub-rawat-inap.hasil-usg-gynecologi.index', [
            'dataUsg' => $this->repository->getByNoRawat($this->no_rawat),
        ]);
    }
}
