<?php

namespace App\Livewire\Modul\Farmasi;

use App\Repositories\Farmasi\ObatAlkesBhpRepository;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Data Obat, Alkes dan BHP Medis'])]
class ObatAlkesBhp extends Component
{
    public ?string $no_resep = null;

    // Active Tab State
    public string $activeTab = 'umum'; // 'umum' or 'racikan'

    // Header Fields — read-only, diisi dari resep
    public string $no_rawat = '';
    public string $no_rkm_medis = '';
    public string $nm_pasien = '';

    // Tanggal & Jam Validasi — di-tick oleh Alpine.js real-time clock
    public string $tgl_validasi = '';
    public string $jam_validasi = '';
    public string $menit_validasi = '';
    public string $detik_validasi = '';

    // Tarif
    public string $tarif = 'Rawat Jalan';
    public array $optionsTarif = [
        'Rawat Jalan',
        'Beli Luar',
        'Karyawan',
        'Utama/BPJS',
    ];
    public bool $use_no_resep = true;

    // Total
    public float $total = 0;
    public float $ppn = 0;
    public float $total_ppn = 0;

    // Depo / Kamar
    public string $kd_depo = 'AP';
    public string $nm_depo = 'Apotek';

    // State: Modal Depo / Kamar (Alpine.js murni — SOP #6)
    public bool $isBangsalModalOpen = false;
    public string $searchBangsalModal = '';
    public array $listBangsal = [];

    // State: Modal Lookup Obat (Alpine.js murni — SOP #6)
    public bool $isObatModalOpen = false;
    public string $searchObatModal = '';
    public array $listObatSearch = [];
    public array $selectedObatModal = [];

    // List data aktif
    public array $listObatUmum = [];
    public array $listObatRacikan = [];

    // ─────────────────────────────────────────────
    // LIFECYCLE
    // ─────────────────────────────────────────────

    public function mount(?string $no_resep = null): void
    {
        $this->no_resep = $no_resep;

        // Inisialisasi waktu validasi
        $now = now();
        $this->tgl_validasi  = $now->format('Y-m-d');
        $this->jam_validasi  = $now->format('H');
        $this->menit_validasi = $now->format('i');
        $this->detik_validasi = $now->format('s');

        // Default depo: AP (Apotek) via Repository
        $depo = ObatAlkesBhpRepository::getDefaultDepo('AP');
        $this->kd_depo = $depo['kd_depo'];
        $this->nm_depo = $depo['nm_depo'];

        // Load data resep jika ada no_resep
        if ($this->no_resep) {
            $header = ObatAlkesBhpRepository::getResepHeader($this->no_resep);

            if ($header) {
                $this->no_rawat    = $header['no_rawat'];
                $this->no_rkm_medis = $header['no_rkm_medis'];
                $this->nm_pasien   = $header['nm_pasien'];
                $this->tarif       = $header['status'] === 'ranap' ? 'Utama/BPJS' : 'Rawat Jalan';

                $this->listObatUmum = ObatAlkesBhpRepository::getDetailObatResep(
                    $this->no_resep,
                    $this->kd_depo
                );
                $this->recalculateTotal();
            }
        }
    }

    public function render()
    {
        // Load data modal saat modal terbuka (SOP #6: state-driven, bukan event-driven)
        if ($this->isObatModalOpen) {
            $this->listObatSearch = ObatAlkesBhpRepository::searchObat(
                trim($this->searchObatModal),
                $this->kd_depo
            );
        }
        if ($this->isBangsalModalOpen) {
            $this->listBangsal = ObatAlkesBhpRepository::searchBangsal(
                $this->searchBangsalModal
            );
        }

        return view('livewire.modul.farmasi.obat-alkes-bhp');
    }

    // ─────────────────────────────────────────────
    // UI STATE — Tab
    // ─────────────────────────────────────────────

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // ─────────────────────────────────────────────
    // KALKULASI — Total
    // ─────────────────────────────────────────────

    public function recalculateTotal(): void
    {
        $subtotal = 0;
        foreach ($this->listObatUmum as $item) {
            $h   = floatval($item['harga'] ?? 0);
            $j   = floatval($item['jumlah'] ?? 0);
            $emb = floatval($item['embalase'] ?? 0);
            $tsl = floatval($item['tuslah'] ?? 0);
            $subtotal += ($h * $j) + $emb + $tsl;
        }
        $this->total     = $subtotal;
        $this->total_ppn = $this->total + $this->ppn;
    }

    // ─────────────────────────────────────────────
    // MODAL — Depo / Kamar
    // ─────────────────────────────────────────────

    public function openBangsalModal(): void
    {
        $this->searchBangsalModal = '';
        $this->isBangsalModalOpen = true;
    }

    public function closeBangsalModal(): void
    {
        $this->isBangsalModalOpen = false;
    }

    public function updatedSearchBangsalModal(): void
    {
        // render() sudah menangani reload listBangsal secara otomatis
    }

    public function selectBangsal(string $kd, string $nm): void
    {
        $this->kd_depo = $kd;
        $this->nm_depo = $nm;
        $this->isBangsalModalOpen = false;

        // Refresh stok seluruh item list sesuai depo baru
        $this->listObatUmum = ObatAlkesBhpRepository::refreshStokDepoForList(
            $this->listObatUmum,
            $this->kd_depo
        );
    }

    // ─────────────────────────────────────────────
    // MODAL — Lookup Tambah Obat (Multiple Select)
    // ─────────────────────────────────────────────

    public function openObatModal(): void
    {
        $this->searchObatModal  = '';
        $this->selectedObatModal = [];
        $this->isObatModalOpen  = true;
    }

    public function closeObatModal(): void
    {
        $this->isObatModalOpen = false;
    }

    public function updatedSearchObatModal(): void
    {
        // render() sudah menangani reload listObatSearch secara otomatis
    }

    public function toggleSelectAllObatModal(): void
    {
        $allKodes = array_column($this->listObatSearch, 'kode_brng');

        if (count(array_intersect($this->selectedObatModal, $allKodes)) === count($allKodes)) {
            $this->selectedObatModal = array_diff($this->selectedObatModal, $allKodes);
        } else {
            $this->selectedObatModal = array_values(
                array_unique(array_merge($this->selectedObatModal, $allKodes))
            );
        }
    }

    /**
     * Shortcut: pilih & tambah satu obat langsung (tombol "+ Pilih" per baris).
     */
    public function addObatFromModal(string $kodeBrng): void
    {
        $this->selectedObatModal = [$kodeBrng];
        $this->addSelectedObatFromModal();
    }

    /**
     * Tambahkan semua obat yang dicentang (selectedObatModal) ke listObatUmum.
     */
    public function addSelectedObatFromModal(): void
    {
        if (empty($this->selectedObatModal)) {
            return;
        }

        $addedCount = 0;

        foreach ($this->selectedObatModal as $kodeBrng) {
            $detail = ObatAlkesBhpRepository::getObatDetail($kodeBrng, $this->kd_depo);

            if ($detail === null) {
                continue;
            }

            // Jika sudah ada di list, tambah jumlah saja
            $found = false;
            foreach ($this->listObatUmum as &$existing) {
                if ($existing['kode_brng'] === $kodeBrng) {
                    $existing['jumlah'] += 1;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $this->listObatUmum[] = [
                    'kode_brng'    => $detail['kode_brng'],
                    'nama_brng'    => $detail['nama_brng'],
                    'jumlah'       => 1,
                    'satuan'       => $detail['satuan'],
                    'harga'        => $detail['harga'],
                    'jenis_obat'   => $detail['jenis_obat'],
                    'embalase'     => 0,
                    'tuslah'       => 0,
                    'stok'         => $detail['stok'],
                    'aturan_pakai' => '-',
                    'industri'     => $detail['industri'],
                    'kategori'     => $detail['kategori'],
                    'golongan'     => $detail['golongan'],
                    'no_batch'     => $detail['no_batch'],
                    'no_faktur'    => $detail['no_faktur'],
                    'kadaluarsa'   => '-',
                ];
            }

            $addedCount++;
        }

        $this->recalculateTotal();
        $this->selectedObatModal = [];
        $this->isObatModalOpen   = false;

        $this->dispatch('swal', [
            'title' => 'Obat Ditambahkan!',
            'text'  => $addedCount . ' obat terpilih berhasil ditambahkan ke daftar.',
            'icon'  => 'success',
        ]);
    }

    // ─────────────────────────────────────────────
    // LIST OBAT UMUM — Aksi
    // ─────────────────────────────────────────────

    public function removeObatUmum(int $index): void
    {
        if (isset($this->listObatUmum[$index])) {
            array_splice($this->listObatUmum, $index, 1);
            $this->recalculateTotal();
        }
    }

    // ─────────────────────────────────────────────
    // SIMPAN — Placeholder
    // ─────────────────────────────────────────────

    public function save(): void
    {
        // TODO: Implementasi penyimpanan data validasi farmasi
    }
}
