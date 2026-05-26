<?php

namespace App\Livewire\Modul\RawatInap\KelahiranBayi;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'Input Data Kelahiran Bayi - SIMRS LaraLite'])]
#[Title('Input Data Kelahiran Bayi - SIMRS LaraLite')]
class Create extends Component
{
    // Identitas
    public string $no_rkm_medis = '';
    public string $nm_pasien = '';
    public string $nm_ibu = '';
    public string $umur_ibu = '';
    public string $nama_ayah = '';
    public string $umur_ayah = '';
    public string $alamat = '';
    public string $jk = 'L';
    public string $no_skl = '';
    public string $tgl_daftar = '';
    
    // Fisik & Waktu
    public string $panjang_badan = '';
    public string $berat_badan = '';
    public string $lingkar_dada = '';
    public string $lingkar_kepala = '';
    public string $lingkar_perut = '';
    public string $tgl_lahir = '';
    public string $jam_lahir = '00:00:00';
    public string $umur = '';

    // Persalinan
    public string $penyulit_kehamilan = '';
    public string $proses_lahir = '';
    public string $anakke = '1';
    public string $g = '';
    public string $p = '';
    public string $a = '';
    public string $penolong = ''; 
    public string $nm_penolong = ''; 
    public string $diagnosa = '';
    public string $ketuban = '';
    public string $keterangan = '';

    // Kondisi & APGAR
    public string $f1 = '0';
    public string $u1 = '0';
    public string $t1 = '0';
    public string $r1 = '0';
    public string $w1 = '0';

    public string $f5 = '0';
    public string $u5 = '0';
    public string $t5 = '0';
    public string $r5 = '0';
    public string $w5 = '0';

    public string $f10 = '0';
    public string $u10 = '0';
    public string $t10 = '0';
    public string $r10 = '0';
    public string $w10 = '0';

    public string $resusitas = '';
    public string $obat_diberikan = '';
    public string $mikasi = '';
    public string $mikonium = '';

    // Search lookups
    public string $searchPasien = '';
    public array $pasienList = [];
    public string $searchPenolong = '';
    public array $penolongList = [];
    
    // Search Ibu
    public string $searchIbu = '';
    public array $ibuList = [];

    public function mount()
    {
        $this->tgl_daftar = now()->format('Y-m-d');
        $this->tgl_lahir = now()->format('Y-m-d');
    }

    public function updatedSearchPasien()
    {
        if (strlen($this->searchPasien) >= 3) {
            $this->pasienList = \App\Models\Pasien::query()
                ->where('no_rkm_medis', 'like', "%{$this->searchPasien}%")
                ->orWhere('nm_pasien', 'like', "%{$this->searchPasien}%")
                ->limit(5)
                ->get()
                ->toArray();
        } else {
            $this->pasienList = [];
        }
    }

    public function selectPasien(string $noRkmMedis)
    {
        $pasien = \App\Models\Pasien::find($noRkmMedis);
        if ($pasien) {
            $this->no_rkm_medis = $pasien->no_rkm_medis;
            $this->nm_pasien = $pasien->nm_pasien;
            $this->nm_ibu = $pasien->nm_ibu;
            $this->jk = $pasien->jk;
            $this->tgl_lahir = $pasien->tgl_lahir;
            $this->tgl_daftar = $pasien->tgl_daftar;
            $this->alamat = $pasien->alamat;
            $this->umur = $pasien->umur;
        }
        $this->searchPasien = '';
        $this->pasienList = [];
    }

    public function updatedSearchPenolong()
    {
        if (strlen($this->searchPenolong) >= 3) {
            $this->penolongList = \App\Models\Pegawai::query()
                ->where('nik', 'like', "%{$this->searchPenolong}%")
                ->orWhere('nama', 'like', "%{$this->searchPenolong}%")
                ->limit(5)
                ->get()
                ->toArray();
        } else {
            $this->penolongList = [];
        }
    }

    public function selectPenolong(string $nik, string $nama)
    {
        $this->penolong = $nik;
        $this->nm_penolong = $nama;
        $this->searchPenolong = '';
        $this->penolongList = [];
    }

    public function updatedSearchIbu()
    {
        if (strlen($this->searchIbu) >= 3) {
            $this->ibuList = \App\Models\Pasien::query()
                ->where('jk', 'P') // Pastikan jenis kelamin perempuan
                ->where(function($q) {
                    $q->where('no_rkm_medis', 'like', "%{$this->searchIbu}%")
                      ->orWhere('nm_pasien', 'like', "%{$this->searchIbu}%");
                })
                ->limit(5)
                ->get()
                ->toArray();
        } else {
            $this->ibuList = [];
        }
    }

    public function selectIbu(string $noRkmMedis)
    {
        $ibu = \App\Models\Pasien::find($noRkmMedis);
        if ($ibu) {
            $this->nm_ibu = $ibu->nm_pasien;
            $this->umur_ibu = preg_replace('/[^0-9]/', '', $ibu->umur); // Ambil angkanya saja
            $this->alamat = $ibu->alamat;
        }
        $this->searchIbu = '';
        $this->ibuList = [];
    }

    public function save()
    {
        $this->validate([
            'no_rkm_medis' => 'required|exists:pasien,no_rkm_medis|unique:pasien_bayi,no_rkm_medis',
            'umur_ibu' => 'required',
            'nama_ayah' => 'required',
            'umur_ayah' => 'required',
            'panjang_badan' => 'required|numeric',
            'berat_badan' => 'required|numeric',
            'lingkar_kepala' => 'required|numeric',
            'proses_lahir' => 'required',
            'anakke' => 'required|numeric',
            'jam_lahir' => 'required',
            'penolong' => 'required|exists:pegawai,nik',
        ], [
            'no_rkm_medis.required' => 'No. RM Bayi wajib dipilih.',
            'no_rkm_medis.exists' => 'No. RM Bayi tidak terdaftar di database Pasien.',
            'no_rkm_medis.unique' => 'No. RM Bayi sudah terdaftar sebagai data kelahiran bayi.',
            'umur_ibu.required' => 'Umur Ibu wajib diisi.',
            'nama_ayah.required' => 'Nama Ayah wajib diisi.',
            'umur_ayah.required' => 'Umur Ayah wajib diisi.',
            'panjang_badan.required' => 'Panjang Badan wajib diisi.',
            'berat_badan.required' => 'Berat Badan wajib diisi.',
            'lingkar_kepala.required' => 'Lingkar Kepala wajib diisi.',
            'proses_lahir.required' => 'Proses Lahir wajib diisi.',
            'anakke.required' => 'Anak Ke wajib diisi.',
            'jam_lahir.required' => 'Jam Lahir wajib diisi.',
            'penolong.required' => 'Penolong persalinan wajib dipilih.',
        ]);

        try {
            $n1Total = intval($this->f1) + intval($this->u1) + intval($this->t1) + intval($this->r1) + intval($this->w1);
            $n5Total = intval($this->f5) + intval($this->u5) + intval($this->t5) + intval($this->r5) + intval($this->w5);
            $n10Total = intval($this->f10) + intval($this->u10) + intval($this->t10) + intval($this->r10) + intval($this->w10);

            \App\Models\PasienBayi::create([
                'no_rkm_medis' => $this->no_rkm_medis,
                'umur_ibu' => $this->umur_ibu,
                'nama_ayah' => $this->nama_ayah,
                'umur_ayah' => $this->umur_ayah,
                'berat_badan' => $this->berat_badan,
                'panjang_badan' => $this->panjang_badan,
                'lingkar_kepala' => $this->lingkar_kepala,
                'proses_lahir' => $this->proses_lahir,
                'anakke' => $this->anakke,
                'jam_lahir' => $this->jam_lahir,
                'keterangan' => $this->keterangan ?: '-',
                'diagnosa' => $this->diagnosa ?: '-',
                'penyulit_kehamilan' => $this->penyulit_kehamilan ?: '-',
                'ketuban' => $this->ketuban ?: '-',
                'lingkar_perut' => $this->lingkar_perut ?: '-',
                'lingkar_dada' => $this->lingkar_dada ?: '-',
                'penolong' => $this->penolong,
                'no_skl' => $this->no_skl ?: null,
                'g' => $this->g ?: '-',
                'p' => $this->p ?: '-',
                'a' => $this->a ?: '-',
                'f1' => $this->f1,
                'u1' => $this->u1,
                't1' => $this->t1,
                'r1' => $this->r1,
                'w1' => $this->w1,
                'n1' => strval($n1Total),
                'f5' => $this->f5,
                'u5' => $this->u5,
                't5' => $this->t5,
                'r5' => $this->r5,
                'w5' => $this->w5,
                'n5' => strval($n5Total),
                'f10' => $this->f10,
                'u10' => $this->u10,
                't10' => $this->t10,
                'r10' => $this->r10,
                'w10' => $this->w10,
                'n10' => strval($n10Total),
                'resusitas' => $this->resusitas ?: '-',
                'obat_diberikan' => $this->obat_diberikan ?: '-',
                'mikasi' => $this->mikasi ?: '-',
                'mikonium' => $this->mikonium ?: '-',
            ]);

            $this->dispatch('swal', [
                'title' => 'Berhasil!',
                'text'  => 'Data pendaftaran kelahiran bayi berhasil disimpan.',
                'icon'  => 'success',
            ]);

            return $this->redirect(route('modul.rawat-inap.kelahiran-bayi'), navigate: true);

        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Gagal Menyimpan!',
                'text'  => 'Error: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.modul.rawat-inap.kelahiran-bayi.create');
    }
}
