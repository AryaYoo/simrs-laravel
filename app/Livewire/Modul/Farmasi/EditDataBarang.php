<?php

namespace App\Livewire\Modul\Farmasi;

use App\Models\DataBarang;
use App\Repositories\Farmasi\DataBarangRepository;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Edit Data Obat, Alkes dan BHP'])]
class EditDataBarang extends Component
{
    public string $kode_brng = '';
    public string $nama_brng = '';
    public string $kode_industri = '';
    public string $letak_barang = '';
    public string $kode_satbesar = '';
    public float $isi = 1;
    public string $kode_sat = '';
    public float $kapasitas = 1;
    public string $kdjns = '';
    public string $kode_kategori = '';
    public string $kode_golongan = '';
    public float $dasar = 0;
    public float $h_beli = 0;
    public float $ralan = 0;
    public float $kelas1 = 0;
    public float $kelas2 = 0;
    public float $kelas3 = 0;
    public float $utama = 0;
    public float $vip = 0;
    public float $vvip = 0;
    public float $beliluar = 0;
    public float $jualbebas = 0;
    public float $karyawan = 0;
    public float $stokminimal = 0;
    public string $expire = '';
    public string $status = '1';

    protected DataBarangRepository $repository;

    public function boot(DataBarangRepository $repository): void
    {
        $this->repository = $repository;
    }

    public function mount(string $kode_brng, DataBarangRepository $repository): void
    {
        $barang = $repository->getDetail($kode_brng);
        if (!$barang) {
            abort(404, 'Data barang tidak ditemukan.');
        }

        $this->kode_brng = $barang->kode_brng;
        $this->nama_brng = $barang->nama_brng ?? '';
        $this->kode_industri = $barang->kode_industri ?? '';
        $this->letak_barang = $barang->letak_barang ?? '';
        $this->kode_satbesar = $barang->kode_satbesar ?? '';
        $this->isi = (float) ($barang->isi ?: 1);
        $this->kode_sat = $barang->kode_sat ?? '';
        $this->kapasitas = (float) $barang->kapasitas;
        $this->kdjns = $barang->kdjns ?? '';
        $this->kode_kategori = $barang->kode_kategori ?? '';
        $this->kode_golongan = $barang->kode_golongan ?? '';
        $this->dasar = (float) $barang->dasar;
        $this->h_beli = (float) $barang->h_beli;
        $this->ralan = (float) $barang->ralan;
        $this->kelas1 = (float) $barang->kelas1;
        $this->kelas2 = (float) $barang->kelas2;
        $this->kelas3 = (float) $barang->kelas3;
        $this->utama = (float) $barang->utama;
        $this->vip = (float) $barang->vip;
        $this->vvip = (float) $barang->vvip;
        $this->beliluar = (float) $barang->beliluar;
        $this->jualbebas = (float) $barang->jualbebas;
        $this->karyawan = (float) $barang->karyawan;
        $this->stokminimal = (float) $barang->stokminimal;
        $this->expire = $barang->expire ? date('Y-m-d', strtotime($barang->expire)) : '';
        $this->status = (string) $barang->status;
    }

    public function save()
    {
        $this->validate([
            'nama_brng'      => 'required|string|max:80',
            'kode_satbesar'  => 'required|string|max:4',
            'kode_sat'       => 'required|string|max:4',
            'isi'            => 'required|numeric|min:0.01',
            'kapasitas'      => 'required|numeric|min:0',
            'dasar'          => 'nullable|numeric|min:0',
            'h_beli'         => 'nullable|numeric|min:0',
            'ralan'          => 'nullable|numeric|min:0',
            'kelas1'         => 'nullable|numeric|min:0',
            'kelas2'         => 'nullable|numeric|min:0',
            'kelas3'         => 'nullable|numeric|min:0',
            'utama'          => 'nullable|numeric|min:0',
            'vip'            => 'nullable|numeric|min:0',
            'vvip'           => 'nullable|numeric|min:0',
            'beliluar'       => 'nullable|numeric|min:0',
            'jualbebas'      => 'nullable|numeric|min:0',
            'karyawan'       => 'nullable|numeric|min:0',
            'stokminimal'    => 'nullable|numeric|min:0',
        ]);

        try {
            DataBarang::where('kode_brng', $this->kode_brng)->update([
                'nama_brng'      => $this->nama_brng,
                'kode_satbesar'  => $this->kode_satbesar,
                'kode_sat'       => $this->kode_sat,
                'letak_barang'   => $this->letak_barang,
                'dasar'          => $this->dasar ?: $this->h_beli,
                'h_beli'         => $this->h_beli,
                'ralan'          => $this->ralan,
                'kelas1'         => $this->kelas1,
                'kelas2'         => $this->kelas2,
                'kelas3'         => $this->kelas3,
                'utama'          => $this->utama,
                'vip'            => $this->vip,
                'vvip'           => $this->vvip,
                'beliluar'       => $this->beliluar,
                'jualbebas'      => $this->jualbebas,
                'karyawan'       => $this->karyawan,
                'stokminimal'    => $this->stokminimal ?: 0,
                'kdjns'          => $this->kdjns ?: null,
                'isi'            => $this->isi ?: 1,
                'kapasitas'      => $this->kapasitas ?: 1,
                'expire'         => $this->expire ?: null,
                'status'         => $this->status,
                'kode_industri'  => $this->kode_industri ?: null,
                'kode_kategori'  => $this->kode_kategori ?: null,
                'kode_golongan'  => $this->kode_golongan ?: null,
            ]);

            session()->flash('swal:toast', [
                'icon'  => 'success',
                'title' => 'Perubahan data barang ' . $this->nama_brng . ' berhasil disimpan.'
            ]);

            return $this->redirect(route('modul.farmasi.data-barang'), navigate: true);
        } catch (\Throwable $e) {
            $this->dispatch('swal:toast', [
                'icon'  => 'error',
                'title' => 'Gagal menyimpan perubahan: ' . $e->getMessage()
            ]);
            return null;
        }
    }

    public function render()
    {
        return view('livewire.modul.farmasi.edit-data-barang', [
            'listSatuan'   => $this->repository->getMasterSatuan(),
            'listJenis'    => $this->repository->getMasterJenis(),
            'listKategori' => $this->repository->getMasterKategori(),
            'listGolongan' => $this->repository->getMasterGolongan(),
            'listIndustri' => $this->repository->getMasterIndustri(),
        ]);
    }
}
