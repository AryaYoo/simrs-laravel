<?php

namespace App\Livewire\Modul\RegistrasiPasien;

use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Penjab;
use App\Models\Poliklinik;
use App\Models\RegPeriksa;
use App\Models\RujukMasuk;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Registrasi Pasien Baru'])]
class Create extends Component
{
    use WithPagination;

    // Form Properties
    public $no_reg;
    public $no_rawat;
    public $tgl_registrasi;
    public $jam_reg;
    public $kd_dokter;
    public $nm_dokter;
    public $no_rkm_medis;
    public $nm_pasien;
    public $kd_poli;
    public $nm_poli;
    public $kd_pj;
    public $png_jawab;
    public $p_jawab;
    public $hubunganpj;
    public $almt_pj;
    public $status = 'Baru'; // Default Baru
    public $no_ka = '-';
    public $perujuk = '-';
    public $biaya_reg = 0;

    // Options for generation
    public $auto_reg = true;
    public $auto_rawat = true;
    public $auto_waktu = true;

    // Modal Lookups
    public $isDokterModalOpen = false;
    public $isPoliModalOpen = false;
    public $isPasienModalOpen = false;
    public $isPenjabModalOpen = false;
    public $isPerujukModalOpen = false;

    // Filters for Lookups
    public $searchDokter = '';
    public $searchPoli = '';
    public $searchPasien = '';
    public $searchPenjab = '';
    public $searchPerujuk = '';

    public function mount()
    {
        $this->tgl_registrasi = Carbon::now()->format('Y-m-d');
        $this->jam_reg = Carbon::now()->format('H:i:s');
        $this->generateNoRawat();
    }

    public function updatedTglRegistrasi()
    {
        if ($this->auto_rawat) {
            $this->generateNoRawat();
        }
        if ($this->auto_reg && $this->kd_dokter) {
            $this->generateNoReg();
        }
    }

    public function updatedKdDokter()
    {
        if ($this->auto_reg) {
            $this->generateNoReg();
        }
    }

    public function generateNoRawat()
    {
        if (!$this->auto_rawat) return;

        $datePart = Carbon::parse($this->tgl_registrasi)->format('Y/m/d');
        $lastReg = RegPeriksa::where('no_rawat', 'like', $datePart . '%')
            ->orderBy('no_rawat', 'desc')
            ->first();

        if ($lastReg) {
            $lastNumber = intval(substr($lastReg->no_rawat, -6));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $this->no_rawat = $datePart . '/' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    public function generateNoReg()
    {
        if (!$this->auto_reg || !$this->kd_dokter) return;

        $lastReg = RegPeriksa::where('tgl_registrasi', $this->tgl_registrasi)
            ->where('kd_dokter', $this->kd_dokter)
            ->orderBy('no_reg', 'desc')
            ->first();

        if ($lastReg) {
            $lastNoReg = intval($lastReg->no_reg);
            $newNoReg = $lastNoReg + 1;
        } else {
            $newNoReg = 1;
        }

        $this->no_reg = str_pad($newNoReg, 3, '0', STR_PAD_LEFT);
    }

    // Modal Logic
    public function openDokterModal() { $this->isDokterModalOpen = true; }
    public function openPoliModal() { $this->isPoliModalOpen = true; }
    public function openPasienModal() { $this->isPasienModalOpen = true; }
    public function openPenjabModal() { $this->isPenjabModalOpen = true; }
    public function openPerujukModal() { $this->isPerujukModalOpen = true; }

    public function selectDokter($kode, $nama)
    {
        $this->kd_dokter = $kode;
        $this->nm_dokter = $nama;
        $this->isDokterModalOpen = false;
        $this->generateNoReg();
    }

    public function selectPoli($kode, $nama, $biaya)
    {
        $this->kd_poli = $kode;
        $this->nm_poli = $nama;
        $this->biaya_reg = $biaya;
        $this->isPoliModalOpen = false;
    }

    public function selectPasien($no_rm, $nama, $p_jawab, $hubunganpj, $almt_pj, $kd_pj, $png_jawab)
    {
        $this->no_rkm_medis = $no_rm;
        $this->nm_pasien = $nama;
        $this->p_jawab = $p_jawab;
        $this->hubunganpj = $hubunganpj;
        $this->almt_pj = $almt_pj;
        $this->kd_pj = $kd_pj;
        $this->png_jawab = $png_jawab;
        $this->isPasienModalOpen = false;
    }

    public function selectPenjab($kode, $nama)
    {
        $this->kd_pj = $kode;
        $this->png_jawab = $nama;
        $this->isPenjabModalOpen = false;
    }

    public function selectPerujuk($nama)
    {
        $this->perujuk = $nama;
        $this->isPerujukModalOpen = false;
    }

    public function save()
    {
        $this->validate([
            'no_rawat' => 'required|unique:reg_periksa,no_rawat',
            'no_reg' => 'required',
            'tgl_registrasi' => 'required|date',
            'kd_dokter' => 'required',
            'kd_poli' => 'required',
            'no_rkm_medis' => 'required',
            'kd_pj' => 'required',
            'status' => 'required|in:Baru,Lama',
        ]);

        DB::beginTransaction();
        try {
            // Hitung umur saat mendaftar
            $pasien = Pasien::find($this->no_rkm_medis);
            $birthDate = Carbon::parse($pasien->tgl_lahir);
            $regDate = Carbon::parse($this->tgl_registrasi);
            $age = $birthDate->diff($regDate);
            
            $umur_daftar = $age->y;
            $stts_umur = 'Th';
            if ($umur_daftar == 0) {
                $umur_daftar = $age->m;
                $stts_umur = 'Bl';
                if ($umur_daftar == 0) {
                    $umur_daftar = $age->d;
                    $stts_umur = 'Hr';
                }
            }

            RegPeriksa::create([
                'no_reg' => $this->no_reg,
                'no_rawat' => $this->no_rawat,
                'tgl_registrasi' => $this->tgl_registrasi,
                'jam_reg' => $this->auto_waktu ? Carbon::now()->format('H:i:s') : $this->jam_reg,
                'kd_dokter' => $this->kd_dokter,
                'no_rkm_medis' => $this->no_rkm_medis,
                'kd_poli' => $this->kd_poli,
                'p_jawab' => $this->p_jawab ?: '-',
                'almt_pj' => $this->almt_pj ?: '-',
                'hubunganpj' => $this->hubunganpj ?: '-',
                'biaya_reg' => $this->biaya_reg,
                'stts' => 'Belum',
                'stts_daftar' => $this->status,
                'status_lanjut' => 'Ralan',
                'kd_pj' => $this->kd_pj,
                'umur_daftar' => $umur_daftar,
                'stts_umur' => $stts_umur,
                'status_bayar' => 'Belum Bayar',
                'stts_poli' => $this->status,
            ]);

            if ($this->perujuk && $this->perujuk !== '-') {
                RujukMasuk::create([
                    'no_rawat' => $this->no_rawat,
                    'perujuk' => $this->perujuk,
                    'alamat' => '-',
                    'no_rujuk' => '-',
                    'jm_perujuk' => 0,
                    'dokter_perujuk' => '-',
                    'kd_penyakit' => '-',
                    'kategori_rujuk' => '-',
                    'keterangan' => '-',
                    'no_balasan' => '-',
                ]);
            }

            DB::commit();

            $this->dispatch('swal', [
                'title' => 'Berhasil!',
                'text'  => 'Registrasi pasien berhasil disimpan.',
                'icon'  => 'success',
            ]);

            return $this->redirect(route('modul.registrasi-pasien.index'), navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', [
                'title' => 'Gagal!',
                'text'  => 'Terjadi kesalahan: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.modul.registrasi-pasien.create', [
            'listDokter' => Dokter::where('status', '1')
                ->where('nm_dokter', 'like', '%' . $this->searchDokter . '%')
                ->orderBy('nm_dokter', 'asc')->get(),
            'listPoli' => Poliklinik::where('status', '1')
                ->where('nm_poli', 'like', '%' . $this->searchPoli . '%')
                ->orderBy('nm_poli', 'asc')->get(),
            'listPasien' => Pasien::where('nm_pasien', 'like', '%' . $this->searchPasien . '%')
                ->orWhere('no_rkm_medis', 'like', '%' . $this->searchPasien . '%')
                ->orderBy('nm_pasien', 'asc')->limit(50)->get(),
            'listPenjab' => Penjab::where('status', '1')
                ->where('png_jawab', 'like', '%' . $this->searchPenjab . '%')
                ->orderBy('png_jawab', 'asc')->get(),
            'listPerujuk' => RujukMasuk::select('perujuk')->where('perujuk', 'like', '%' . $this->searchPerujuk . '%')
                ->distinct()->orderBy('perujuk', 'asc')->get(),
        ]);
    }
}
