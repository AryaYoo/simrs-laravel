<?php

namespace App\Livewire\Modul\RawatJalan\SubRawatJalan\HasilUsgKandungan;

use App\Livewire\Concerns\WithOptimisticLocking;
use App\Models\HasilPemeriksaanUsg;
use App\Models\HasilPemeriksaanUsgGambar;
use App\Models\RegPeriksa;
use App\Repositories\RawatInap\HasilPemeriksaanUsgRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithOptimisticLocking, WithFileUploads;

    public $no_rawat;
    public $photoUpload = null;
    public $pasien;
    
    // Form state
    public $form = [
        'tanggal' => '',
        'jam' => '',
        'kd_dokter' => '',
        'nm_dokter' => '',
        'diagnosa_klinis' => '',
        'kiriman_dari' => '',
        'hta' => '',
        'kantong_gestasi' => '',
        'ukuran_bokongkepala' => '',
        'jenis_prestasi' => '',
        'diameter_biparietal' => '',
        'panjang_femur' => '',
        'lingkar_abdomen' => '',
        'tafsiran_berat_janin' => '',
        'usia_kehamilan' => '',
        'plasenta_berimplatansi' => '',
        'derajat_maturitas' => '0',
        'jumlah_air_ketuban' => 'Cukup',
        'indek_cairan_ketuban' => '',
        'kelainan_kongenital' => '',
        'peluang_sex' => 'Laki-laki',
        'kesimpulan' => '',
    ];

    public $isEdit = false;
    public $isAutoTimestamp = true;

    protected $repository;

    public function boot(HasilPemeriksaanUsgRepository $repository)
    {
        $this->repository = $repository;
    }

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->pasien = RegPeriksa::with(['pasien', 'dokter'])->where('no_rawat', $this->no_rawat)->firstOrFail();
        
        $this->resetForm();
    }

    public function resetForm()
    {
        $now = Carbon::now();
        $this->form = [
            'tanggal' => $now->format('Y-m-d'),
            'jam' => $now->format('H:i:s'),
            'kd_dokter' => $this->pasien->kd_dokter ?? '',
            'nm_dokter' => $this->pasien->dokter->nm_dokter ?? '',
            'diagnosa_klinis' => '',
            'kiriman_dari' => '',
            'hta' => '',
            'kantong_gestasi' => '',
            'ukuran_bokongkepala' => '',
            'jenis_prestasi' => '',
            'diameter_biparietal' => '',
            'panjang_femur' => '',
            'lingkar_abdomen' => '',
            'tafsiran_berat_janin' => '',
            'usia_kehamilan' => '',
            'plasenta_berimplatansi' => '',
            'derajat_maturitas' => '0',
            'jumlah_air_ketuban' => 'Cukup',
            'indek_cairan_ketuban' => '',
            'kelainan_kongenital' => '',
            'peluang_sex' => 'Laki-laki',
            'kesimpulan' => '',
        ];
        $this->isEdit = false;
    }

    public function updateTime()
    {
        if ($this->isAutoTimestamp) {
            $now = Carbon::now();
            $this->form['tanggal'] = $now->format('Y-m-d');
            $this->form['jam'] = $now->format('H:i:s');
        }
    }

    // Dipanggil dari UI saat memilih dokter dari modal lookup
    public function selectDokter($kdDokter, $nmDokter)
    {
        $this->form['kd_dokter'] = $kdDokter;
        $this->form['nm_dokter'] = $nmDokter;
    }

    public function save()
    {
        $this->validate([
            'form.tanggal' => 'required|date',
            'form.jam' => 'required',
            'form.kd_dokter' => 'required',
            'form.diagnosa_klinis' => 'nullable|string|max:50',
            'form.kiriman_dari' => 'nullable|string|max:50',
            'form.hta' => 'nullable|string|max:40',
            'form.kantong_gestasi' => 'nullable|string|max:6',
            'form.ukuran_bokongkepala' => 'nullable|string|max:6',
            'form.jenis_prestasi' => 'nullable|string|max:30',
            'form.diameter_biparietal' => 'nullable|string|max:6',
            'form.panjang_femur' => 'nullable|string|max:6',
            'form.lingkar_abdomen' => 'nullable|string|max:6',
            'form.tafsiran_berat_janin' => 'nullable|string|max:6',
            'form.usia_kehamilan' => 'nullable|string|max:15',
            'form.plasenta_berimplatansi' => 'nullable|string|max:50',
            'form.derajat_maturitas' => 'required|in:0,1,2,3',
            'form.jumlah_air_ketuban' => 'required|in:Cukup,Berkurang',
            'form.indek_cairan_ketuban' => 'nullable|string|max:40',
            'form.kelainan_kongenital' => 'nullable|string|max:60',
            'form.peluang_sex' => 'required|in:Laki-laki,Perempuan,-',
            'form.kesimpulan' => 'nullable|string|max:200',
        ], [
            'form.kd_dokter.required' => 'Dokter DPJP pemeriksa harus diisi.',
            'form.derajat_maturitas.in' => 'Derajat maturitas tidak valid.',
            'form.jumlah_air_ketuban.in' => 'Jumlah air ketuban tidak valid.',
            'form.peluang_sex.in' => 'Peluang sex tidak valid.',
        ]);

        try {
            $data = $this->form;
            $data['no_rawat'] = $this->no_rawat;
            $data['tanggal'] = $data['tanggal'] . ' ' . $data['jam'];
            
            // Remove helper fields
            unset($data['jam']);
            unset($data['nm_dokter']);

            if ($this->isEdit) {
                // Gunakan optimistic locking khusus jika diperlukan
                // Untuk tabel dengan PK hanya no_rawat, check_lock cukup pakai no_rawat
                $record = HasilPemeriksaanUsg::where('no_rawat', $this->no_rawat)->first();
                if ($record) {
                    $this->validateLock($record);
                }
                
                $this->repository->update($this->no_rawat, $data);
                
                $this->dispatch('sweet-alert', [
                    'icon' => 'success',
                    'title' => 'Berhasil',
                    'text' => 'Data Hasil USG Kandungan berhasil diupdate!'
                ]);
            } else {
                // Cek apakah sudah ada
                if ($this->repository->exists($this->no_rawat)) {
                    throw new Exception('Data Hasil USG Kandungan sudah ada untuk No. Rawat ini. Silakan gunakan fitur edit.');
                }
                
                $this->repository->create($data);
                
                $this->dispatch('sweet-alert', [
                    'icon' => 'success',
                    'title' => 'Berhasil',
                    'text' => 'Data Hasil USG Kandungan berhasil ditambahkan!'
                ]);
            }

            $this->dispatch('close-modal');
            $this->resetForm();

        } catch (Exception $e) {
            $this->dispatch('sweet-alert', [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => $e->getMessage()
            ]);
        }
    }

    public function edit()
    {
        $record = HasilPemeriksaanUsg::with('dokter')->where('no_rawat', $this->no_rawat)->first();

        if ($record) {
            $this->isEdit = true;
            $this->isAutoTimestamp = false;
            
            $datetime = Carbon::parse($record->tanggal);
            
            $this->form = [
                'tanggal' => $datetime->format('Y-m-d'),
                'jam' => $datetime->format('H:i:s'),
                'kd_dokter' => $record->kd_dokter,
                'nm_dokter' => $record->dokter->nm_dokter ?? '',
                'diagnosa_klinis' => $record->diagnosa_klinis ?? '',
                'kiriman_dari' => $record->kiriman_dari ?? '',
                'hta' => $record->hta ?? '',
                'kantong_gestasi' => $record->kantong_gestasi ?? '',
                'ukuran_bokongkepala' => $record->ukuran_bokongkepala ?? '',
                'jenis_prestasi' => $record->jenis_prestasi ?? '',
                'diameter_biparietal' => $record->diameter_biparietal ?? '',
                'panjang_femur' => $record->panjang_femur ?? '',
                'lingkar_abdomen' => $record->lingkar_abdomen ?? '',
                'tafsiran_berat_janin' => $record->tafsiran_berat_janin ?? '',
                'usia_kehamilan' => $record->usia_kehamilan ?? '',
                'plasenta_berimplatansi' => $record->plasenta_berimplatansi ?? '',
                'derajat_maturitas' => $record->derajat_maturitas ?? '0',
                'jumlah_air_ketuban' => $record->jumlah_air_ketuban ?? 'Cukup',
                'indek_cairan_ketuban' => $record->indek_cairan_ketuban ?? '',
                'kelainan_kongenital' => $record->kelainan_kongenital ?? '',
                'peluang_sex' => $record->peluang_sex ?? 'Laki-laki',
                'kesimpulan' => $record->kesimpulan ?? '',
            ];

            // Inisialisasi optimistic lock
            $this->initializeLock($record);
            
            // Dispatch event untuk membuka modal via Alpine
            $this->dispatch('open-modal');
        } else {
            $this->dispatch('sweet-alert', [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => 'Data tidak ditemukan.'
            ]);
        }
    }

    public function delete()
    {
        try {
            $this->repository->delete($this->no_rawat);
            
            $this->dispatch('sweet-alert', [
                'icon' => 'success',
                'title' => 'Berhasil',
                'text' => 'Data Hasil USG Kandungan berhasil dihapus!'
            ]);
            
            $this->resetForm();
            
        } catch (Exception $e) {
            $this->dispatch('sweet-alert', [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function uploadPhoto()
    {
        $this->validate([
            'photoUpload' => 'required|image|mimes:jpeg,jpg,png,webp|max:10240',
        ], [
            'photoUpload.required'  => 'Silakan pilih file gambar terlebih dahulu.',
            'photoUpload.image'     => 'File harus berupa gambar.',
            'photoUpload.mimes'     => 'Format gambar harus jpeg, jpg, png, atau webp.',
            'photoUpload.max'       => 'Ukuran gambar tidak boleh lebih dari 10MB.',
        ]);

        try {
            // Build destination path (public/uploads/usg/{no_rawat_slug}/)
            $slug   = str_replace('/', '-', $this->no_rawat);
            $dir    = public_path('uploads/usg/' . $slug);
            File::ensureDirectoryExists($dir);

            // Remove old photo if exists
            $existing = HasilPemeriksaanUsgGambar::where('no_rawat', $this->no_rawat)->first();
            if ($existing && $existing->photo) {
                $oldPath = public_path($existing->photo);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            // Store new photo
            $filename  = 'usg_' . $slug . '_' . time() . '.' . $this->photoUpload->getClientOriginalExtension();
            $this->photoUpload->move($dir, $filename);
            $relativePath = 'uploads/usg/' . $slug . '/' . $filename;

            // Upsert record in hasil_pemeriksaan_usg_gambar
            HasilPemeriksaanUsgGambar::updateOrCreate(
                ['no_rawat' => $this->no_rawat],
                ['photo'    => $relativePath]
            );

            $this->photoUpload = null;
            $this->dispatch('photo-uploaded');
            $this->dispatch('sweet-alert', [
                'icon'  => 'success',
                'title' => 'Berhasil',
                'text'  => 'Foto USG berhasil disimpan!',
            ]);

        } catch (Exception $e) {
            $this->dispatch('sweet-alert', [
                'icon'  => 'error',
                'title' => 'Gagal Upload',
                'text'  => $e->getMessage(),
            ]);
        }
    }

    public function deletePhoto()
    {
        try {
            $gambar = HasilPemeriksaanUsgGambar::where('no_rawat', $this->no_rawat)->first();

            if ($gambar) {
                $path = public_path($gambar->photo);
                if (File::exists($path)) {
                    File::delete($path);
                }
                $gambar->delete();
            }

            $this->dispatch('sweet-alert', [
                'icon'  => 'success',
                'title' => 'Berhasil',
                'text'  => 'Foto USG berhasil dihapus.',
            ]);

        } catch (Exception $e) {
            $this->dispatch('sweet-alert', [
                'icon'  => 'error',
                'title' => 'Gagal',
                'text'  => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        $data   = $this->repository->getByNoRawat($this->no_rawat);
        $gambar = HasilPemeriksaanUsgGambar::where('no_rawat', $this->no_rawat)->first();

        return view('livewire.modul.rawat-jalan.sub-rawat-jalan.hasil-usg-kandungan.index', [
            'dataUsg' => $data,
            'gambar'  => $gambar,
        ]);
    }
}
