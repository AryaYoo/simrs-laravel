<?php

namespace App\Livewire\Modul\Farmasi;

use App\Models\DataBarang as DataBarangModel;
use App\Repositories\Farmasi\DataBarangRepository;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Data Obat, Alkes dan BHP'])]
class DataBarang extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $kdjns = '';

    #[Url(except: '')]
    public string $kode_kategori = '';

    #[Url(except: '')]
    public string $kode_golongan = '';

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: '')]
    public string $stok_filter = '';

    #[Url(except: 15)]
    public int $perPage = 15;

    // Modal Detail State
    public bool $showDetailModal = false;
    public ?DataBarangModel $selectedBarang = null;

    // Modal Form Edit / Create State
    public bool $showFormModal = false;
    public bool $isEditing = false;

    public string $kode_brng = '';
    public string $nama_brng = '';
    public string $kode_satbesar = '';
    public string $kode_sat = '';
    public string $letak_barang = '';
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
    public string $form_kdjns = '';
    public float $isi = 1;
    public float $kapasitas = 0;
    public string $form_status = '1';
    public string $kode_industri = '';
    public string $form_kode_kategori = '';
    public string $form_kode_golongan = '';

    protected DataBarangRepository $repository;

    public function boot(DataBarangRepository $repository): void
    {
        $this->repository = $repository;
    }

    public function updatedSearch(): void        { $this->resetPage(); }
    public function updatedKdjns(): void         { $this->resetPage(); }
    public function updatedKodeKategori(): void  { $this->resetPage(); }
    public function updatedKodeGolongan(): void  { $this->resetPage(); }
    public function updatedStatus(): void        { $this->resetPage(); }
    public function updatedStokFilter(): void    { $this->resetPage(); }
    public function updatedPerPage(): void       { $this->resetPage(); }

    public function resetFilter(): void
    {
        $this->reset(['search', 'kdjns', 'kode_kategori', 'kode_golongan', 'status', 'stok_filter']);
        $this->perPage = 15;
        $this->resetPage();
    }

    public function openDetail(string $kode_brng): void
    {
        $this->selectedBarang = $this->repository->getDetail($kode_brng);
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedBarang = null;
    }

    public function openCreate(): void
    {
        $this->isEditing = false;
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openEdit(string $kode_brng): void
    {
        $barang = $this->repository->getDetail($kode_brng);
        if (!$barang) {
            $this->dispatch('swal:toast', [
                'icon' => 'error',
                'title' => 'Data barang tidak ditemukan'
            ]);
            return;
        }

        $this->isEditing = true;
        $this->kode_brng = $barang->kode_brng;
        $this->nama_brng = $barang->nama_brng ?? '';
        $this->kode_satbesar = $barang->kode_satbesar ?? '';
        $this->kode_sat = $barang->kode_sat ?? '';
        $this->letak_barang = $barang->letak_barang ?? '';
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
        $this->form_kdjns = $barang->kdjns ?? '';
        $this->isi = (float) ($barang->isi ?: 1);
        $this->kapasitas = (float) $barang->kapasitas;
        $this->form_status = (string) $barang->status;
        $this->kode_industri = $barang->kode_industri ?? '';
        $this->form_kode_kategori = $barang->kode_kategori ?? '';
        $this->form_kode_golongan = $barang->kode_golongan ?? '';

        $this->showFormModal = true;
    }

    public function closeForm(): void
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'kode_brng',
            'nama_brng',
            'kode_satbesar',
            'kode_sat',
            'letak_barang',
            'h_beli',
            'ralan',
            'kelas1',
            'kelas2',
            'kelas3',
            'utama',
            'vip',
            'vvip',
            'beliluar',
            'jualbebas',
            'karyawan',
            'stokminimal',
            'form_kdjns',
            'isi',
            'kapasitas',
            'form_status',
            'kode_industri',
            'form_kode_kategori',
            'form_kode_golongan',
        ]);
        $this->isi = 1;
        $this->form_status = '1';
    }

    public function saveBarang(): void
    {
        $rules = [
            'nama_brng'      => 'required|string|max:80',
            'kode_satbesar'  => 'required|string|max:4',
            'kode_sat'       => 'required|string|max:4',
            'kapasitas'      => 'nullable|numeric',
            'h_beli'         => 'nullable|numeric',
            'ralan'          => 'nullable|numeric',
        ];

        if (!$this->isEditing) {
            $rules['kode_brng'] = 'required|string|max:15|unique:databarang,kode_brng';
        }

        $this->validate($rules);

        try {
            $data = [
                'nama_brng'      => $this->nama_brng,
                'kode_satbesar'  => $this->kode_satbesar,
                'kode_sat'       => $this->kode_sat,
                'letak_barang'   => $this->letak_barang,
                'dasar'          => $this->h_beli,
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
                'stokminimal'    => $this->stokminimal,
                'kdjns'          => $this->form_kdjns ?: null,
                'isi'            => $this->isi ?: 1,
                'kapasitas'      => $this->kapasitas ?: 0,
                'status'         => $this->form_status,
                'kode_industri'  => $this->kode_industri ?: null,
                'kode_kategori'  => $this->form_kode_kategori ?: null,
                'kode_golongan'  => $this->form_kode_golongan ?: null,
            ];

            if ($this->isEditing) {
                DataBarangModel::where('kode_brng', $this->kode_brng)->update($data);
                $msg = 'Data barang berhasil diperbarui.';
            } else {
                $data['kode_brng'] = $this->kode_brng;
                DataBarangModel::create($data);
                $msg = 'Data barang baru berhasil ditambahkan.';
            }

            if (!$this->isEditing) {
                $newlyCreated = session('newly_created_barang', []);
                $newlyCreated[] = $this->kode_brng;
                session(['newly_created_barang' => array_values(array_unique($newlyCreated))]);
            }

            $this->closeForm();
            $this->dispatch('swal:toast', [
                'icon'  => 'success',
                'title' => $msg
            ]);
        } catch (\Throwable $e) {
            $this->dispatch('swal:toast', [
                'icon'  => 'error',
                'title' => 'Gagal menyimpan: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteBarang(string $kode_brng): void
    {
        $deleted = $this->repository->delete($kode_brng);
        if ($deleted) {
            $this->dispatch('swal:toast', [
                'icon'  => 'success',
                'title' => 'Data barang berhasil dihapus'
            ]);
        } else {
            $this->dispatch('swal:toast', [
                'icon'  => 'error',
                'title' => 'Gagal menghapus data barang'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.modul.farmasi.data-barang', [
            'barangList'   => $this->repository->getList(
                $this->search,
                $this->kdjns,
                $this->kode_kategori,
                $this->kode_golongan,
                $this->status,
                $this->stok_filter,
                $this->perPage
            ),
            'listJenis'    => $this->repository->getMasterJenis(),
            'listKategori' => $this->repository->getMasterKategori(),
            'listGolongan' => $this->repository->getMasterGolongan(),
            'listSatuan'   => $this->repository->getMasterSatuan(),
            'listIndustri' => $this->repository->getMasterIndustri(),
        ]);
    }
}
