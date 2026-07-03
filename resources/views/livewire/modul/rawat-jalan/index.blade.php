<div class="flex flex-col gap-6 pb-8" x-data="{
        menuModalOpen: false,
        searchQuery: '',
        activeSubMenu: null,
        activePatient: { noRawat: '', noRawatSlug: '', nmPasien: '' },
        isAdmin: {{ auth()->user()->role === 'admin' ? 'true' : 'false' }},
        cols: 2,
        showBpjsColor: false,
        init() {
            this.updateCols();
            window.addEventListener('resize', () => this.updateCols());
        },
        updateCols() {
            if (window.innerWidth >= 1024) this.cols = 5;
            else if (window.innerWidth >= 768) this.cols = 4;
            else if (window.innerWidth >= 640) this.cols = 3;
            else this.cols = 2;
        },
        openMenu(noRawat, nmPasien) {
            this.activePatient = { 
                noRawat: noRawat, 
                noRawatSlug: noRawat.replace(/\//g, '-'),
                nmPasien: nmPasien 
            };
            this.menuModalOpen = true;
        },
        getMenuUrl(url) {
            if (!url || url === '#') return '#';
            let template = this.routeTemplates[url] || url;
            return template.replace(/(:noRawat|%3AnoRawat)/g, this.activePatient.noRawatSlug);
        },
        routeTemplates: {
            detail: '{{ route("modul.rawat-jalan.show", ":noRawat") }}',
            action: '{{ route("modul.rawat-jalan.perawatan-tindakan", ":noRawat") }}',
            'resep-dokter': '{{ route("modul.rawat-jalan.sub-rawat-jalan.resep-dokter", ":noRawat") }}',
            'permintaan-lab': '{{ route("modul.rawat-jalan.sub-rawat-jalan.permintaan-lab", ":noRawat") }}',
            'permintaan-rawat-inap': '{{ route("modul.rawat-jalan.sub-rawat-jalan.permintaan-rawat-inap", ":noRawat") }}',
            'triase-igd': '{{ route("modul.rawat-jalan.sub-rawat-jalan.triase-igd", ":noRawat") }}',
            'catatan-keperawatan': '{{ route("modul.rawat-jalan.sub-rawat-jalan.catatan-keperawatan", ":noRawat") }}',
            'observasi-igd': '{{ route("modul.rawat-jalan.sub-rawat-jalan.observasi-igd", ":noRawat") }}',
            'pengkajian-awal-keperawatan-igd': '{{ route("modul.rawat-jalan.sub-rawat-jalan.pengkajian-awal-keperawatan-igd", ":noRawat") }}',
            'riwayat-pasien': '{{ route("modul.rawat-jalan.sub-rawat-jalan.riwayat-pasien", ":noRawat") }}',
            'resume': '{{ route("modul.rawat-jalan.sub-rawat-jalan.resume", ":noRawat") }}',
            'hasilUsgKandungan': '{{ route("modul.rawat-jalan.sub-rawat-jalan.hasil-usg-kandungan", ":noRawat") }}',
            'hasilUsgGynecologi': '{{ route("modul.rawat-inap.sub-rawat-inap.hasil-usg-gynecologi", ":noRawat") }}',
        },
        chunk(items, size) {
            const chunks = [];
            for (let i = 0; i < items.length; i += size) {
                chunks.push(items.slice(i, i + size));
            }
            return chunks;
        },
        toggleSubMenu(key) { 
            this.activeSubMenu = (this.activeSubMenu === key) ? null : key; 
        },
        isSubMenuOpen(key) { return this.activeSubMenu === key; },
        menuGroups: [
            {
                label: 'Data Rekam Medis',
                items: [
                    { label: 'Skrining', children: [
                        { label: 'Skrining Merokok Usia Sekolah & Remaja', url: '#' },
                        { label: 'Skrining Kekerasan Pada Perempuan', url: '#' },
                        { label: 'Skrining Obesitas', url: '#' },
                        { label: 'Skrining Kanker', url: '#' },
                        { label: 'Skrining Kesehatan Gigi & Mulut', url: '#' },
                        { label: 'Skrining TBC', url: '#' },
                        { label: 'Skrining PUMA', url: '#' },
                        { label: 'Skrining Pneumonia Severity Index', url: '#' },
                        { label: 'Skrining Adiksi Nikotin', url: '#' },
                        { label: 'Skrining Thalassemia', url: '#' },
                        { label: 'Skrining Istrumen', url: '#' },
                        { label: 'Skrining Diabetes Melitus', url: '#' },
                        { label: 'Skrining Anemia', url: '#' },
                        { label: 'Skrining Hipertensi', url: '#' },
                        { label: 'Skrining Kesehatan Penglihatan', url: '#' },
                        { label: 'Skrining Indra Pendengaran', url: '#' },
                        { label: 'Skrining Frailty Syndrome', url: '#' },
                    ]},
                    { label: 'RM Gawat Darurat', children: [
                        { label: 'Triase Gawat Darurat', url: 'triase-igd' },
                        { label: 'Pengkajian Awal Keperawatan IGD', url: 'pengkajian-awal-keperawatan-igd' },
                        { label: 'Pengkajian Awal Medis IGD', url: '#' },
                        { label: 'Pengkajian Awal Medis IGD Psikiatri', url: '#' },
                        { label: 'Pengkajian Pasien Keracunan', url: '#' },
                        { label: 'Pengkajian Restrain', url: '#' },
                        { label: 'Pemantauan PEWS Anak', url: '#' },
                        { label: 'Pemantauan EWS Dewasa', url: '#' },
                        { label: 'Pemantauan MEOWS Obstetri', url: '#' },
                        { label: 'Pemantauan EWS Neonatus', url: '#' },
                    ]},
                    { label: 'RM Rawat Jalan', children: [
                        { label: 'Awal Keperawatan', url: '#' },
                        { label: 'Awal Medis', url: '#' },
                        { label: 'Hasil USG', children: [
                            { label: 'Hasil USG Kandungan', url: 'hasilUsgKandungan' },
                            { label: 'Hasil USG Urologi', url: '#' },
                            { label: 'Hasil USG Neonatus', url: '#' },
                            { label: 'Hasil USG Gynecologi', url: 'hasilUsgGynecologi' },
                        ]},
                        { label: 'Hasil Endoskopi', url: '#' },
                        { label: 'Hasil Pemeriksaan EKG', url: '#' },
                        { label: 'Hasil Pemeriksaan ECHO', url: '#' },
                        { label: 'Hasil Pemeriksaan ECHO Pediatrik', url: '#' },
                        { label: 'Hasil Pemeriksaan Slit Lamp', url: '#' },
                        { label: 'Hasil Pemeriksaan OCT', url: '#' },
                        { label: 'Hasil Pemeriksaan Treadmill', url: '#' },
                        { label: 'Dokumentasi Tindakan ESWL', url: '#' },
                        { label: 'Pengkajian Psikologi', url: '#' },
                        { label: 'Pengkajian Psikologi Klinis', url: '#' },
                    ]},
                    { label: 'RM Operasi', children: [
                        { label: 'Pengkajian Pre Induksi', url: '#' },
                        { label: 'Check List Pre Operasi', url: '#' },
                        { label: 'Sign-In Sebelum Anestesi', url: '#' },
                        { label: 'Time-Out Sebelum Insisi', url: '#' },
                        { label: 'Sign-Out Sebelum Menutup Luka', url: '#' },
                        { label: 'Pengkajian Pre Operasi', url: '#' },
                        { label: 'Catatan Anestesi-Sedasi', url: '#' },
                        { label: 'Pengkajian Pre Anestesi', url: '#' },
                        { label: 'Check List Kesiapan Anestesi', url: '#' },
                        { label: 'Skor Aldrette Pasca Anestesi', url: '#' },
                        { label: 'Skor Steward Pasca Anestesi', url: '#' },
                        { label: 'Skor Bromage Pasca Anestesi', url: '#' },
                        { label: 'Catatan Pengkajian Paska Operasi', url: '#' },
                    ]},
                    { label: 'RM HCU, ICU, NICU & PICU', children: [
                        { label: 'Check List Kriteria Masuk HCU', url: '#' },
                        { label: 'Check List Kriteria Masuk ICU', url: '#' },
                        { label: 'Check List Kriteria Masuk NICU', url: '#' },
                        { label: 'Check List Kriteria Masuk PICU', url: '#' },
                    ]},
                    { label: 'RM Rehab Medik', children: [
                        { label: 'Pengkajian Awal Fisioterapi', url: '#' },
                        { label: 'Pengkajian Terapi Wicara', url: '#' },
                        { label: 'Penatalaksanaan Terapi Okupasi', url: '#' },
                        { label: 'Layanan Kedokteran Fisik & Rehabilitasi', url: '#' },
                        { label: 'Uji Fungsi/Prosedur KFR', url: '#' },
                    ]},
                    { label: 'Risiko Jatuh & Fungsional', children: [
                        { label: 'Pengkajian Lanjutan Risiko Jatuh Dewasa', url: '#' },
                        { label: 'Pengkajian Lanjutan Risiko Jatuh Anak', url: '#' },
                        { label: 'Pengkajian Lanjutan Risiko Jatuh Lansia', url: '#' },
                        { label: 'Pengkajian Lanjutan Risiko Jatuh Neonatus', url: '#' },
                        { label: 'Pengkajian Lanjutan Risiko Jatuh Geriatri', url: '#' },
                        { label: 'Pengkajian Lanjutan Risiko Jatuh Psikiatri', url: '#' },
                        { label: 'Pengkajian Lanjutan Skrining Fungsional', url: '#' },
                    ]},
                    { label: 'Pengkajian Lain-lain', children: [
                        { label: 'Pengkajian Tambahan Pasien Geriatri', url: '#' },
                        { label: 'Pengkajian Tambahan Bunuh Diri', url: '#' },
                        { label: 'Pengkajian Tambahan Perilaku Kekerasan', url: '#' },
                        { label: 'Pengkajian Tambahan Melarikan Diri', url: '#' },
                        { label: 'Pengkajian Pasien Terminal', url: '#' },
                        { label: 'Pengkajian Korban Kekerasan', url: '#' },
                        { label: 'Pengkajian Pasien Penyakit Menular', url: '#' },
                        { label: 'Pengkajian Pasien Imunitas Rendah', url: '#' },
                        { label: 'Pengkajian Derajat Dehidrasi', url: '#' },
                        { label: 'Pengkajian Medical Check Up', url: '#' },
                        { label: 'Hemodialisa', url: '#' },
                        { label: 'Pengkajian Bayi Baru Lahir', url: '#' },
                    ]},
                    { label: 'RM Farmasi', children: [
                        { label: 'Konseling Farmasi', url: '#' },
                        { label: 'Rekonsiliasi Obat', url: '#' },
                    ]},
                    { label: 'Catatan & Monitoring', children: [
                        { label: 'Catatan Cek GDS', url: '#' },
                        { label: 'Monitoring Reaksi Tranfusi', url: '#' },
                        { label: 'Pengkajian Ulang Nyeri', url: '#' },
                        { label: 'Catatan Keperawatan', url: 'catatan-keperawatan' },
                        { label: 'Catatan Persalinan', url: '#' },
                        { label: 'Keseimbangan Cairan', url: '#' },
                        { label: 'Observasi IGD', url: 'observasi-igd' },
                        { label: 'Observasi CHBP', url: '#' },
                        { label: 'Observasi Induksi Persalinan', url: '#' },
                        { label: 'Observasi Bayi', url: '#' },
                        { label: 'Observasi Hemodialisa', url: '#' },
                        { label: 'Catatan Cairan Hemodialisa', url: '#' },
                        { label: 'Check List Pemberian Fibrinolistik', url: '#' },
                        { label: 'Laporan Tindakan', url: '#' },
                    ]},
                    { label: 'Diagnosa', url: '#' },
                    { label: 'RM Gizi', children: [
                        { label: 'Skrining Nutrisi Pasien Dewasa', url: '#' },
                        { label: 'Skrining Nutrisi Pasien Lansia', url: '#' },
                        { label: 'Skrining Nutrisi Pasien Anak', url: '#' },
                        { label: 'Skrining Gizi Lanjut', url: '#' },
                        { label: 'Asuhan Gizi', url: '#' },
                        { label: 'Monitoring Gizi', url: '#' },
                        { label: 'Catatan ADIME Gizi', url: '#' },
                    ]},
                    { label: 'Transfer Antar Ruang', url: '#' },
                    { label: 'Edukasi', children: [
                        { label: 'Asesment Edukasi Pasien & Keluarga', url: '#' },
                        { label: 'Pelaksanaan Informasi dan Edukasi', url: '#' },
                    ]},
                    { label: 'Resume', url: 'resume' },
                    { label: 'Riwayat Perawatan', url: 'riwayat-pasien' },
                    { label: 'Deteksi Dini Corona', url: '#' },
                ]
            },
            {
                label: 'Permintaan',
                items: [
                    { label: 'Jadwal Operasi', url: '#' },
                    { label: 'Pemeriksaan Lab', url: 'permintaan-lab' },
                    { label: 'Pemeriksaan Radiologi', url: '#' },
                    { label: 'Rawat Inap', url: 'permintaan-rawat-inap' },
                    { label: 'Informasi Obat', url: '#' },
                    { label: 'Konsultasi Medik', url: '#' },
                ]
            },
            {
                label: 'Akses Cepat',
                items: [
                    { label: 'Perawatan/Tindakan', url: 'action', target: '_blank' },
                    { label: 'Antrian Masuk Poli', url: '#' },
                    { label: 'Kamar Inap', url: '#' },
                ]
            },
            {
                label: 'Tindakan & Pemeriksaan',
                items: [
                    { label: 'Data Tindakan Rawat Jalan', url: '#' },
                    { label: 'Periksa Lab PK', url: '#' },
                    { label: 'Periksa Lab PA', url: '#' },
                    { label: 'Periksa Lab MB', url: '#' },
                    { label: 'Periksa Radiologi', url: '#' },
                    { label: 'Tagihan Operasi/VK', url: '#' },
                    { label: 'Data Operasi', url: '#' },
                ]
            },
            {
                label: 'Obat',
                items: [
                    { label: 'Pemberian Obat', url: '#' },
                    { label: 'Input No.Resep', url: '#' },
                    { label: 'Input Resep Dokter', url: 'resep-dokter' },
                    { label: 'Total Tagihan Obat', url: '#' },
                    { label: 'Data Pemberian Obat', url: '#' },
                    { label: 'Penjualan Obat/Alkes/Barang', url: '#' },
                    { label: 'Piutang Obat/Alkes/Barang', url: '#' },
                    { label: 'Copy Resep Dokter', url: '#' },
                ]
            },
            {
                label: 'Billing/Pembayaran Pasien',
                items: [
                    { label: 'biling parsial', url: '#' },
                    { label: 'biling total', url: '#' },
                ]
            },
            {
                label: 'Rekap Data',
                items: [
                    { label: 'Rekap Harian Dokter', url: '#' },
                    { label: 'Rekap Harian Paramedis', url: '#' },
                    { label: 'Rekap Bulanan Dokter', url: '#' },
                    { label: 'Rekap Bulanan Paramedis', url: '#' },
                    { label: 'Rekap Harian Poli', url: '#' },
                    { label: 'Rekap Harian Obat', url: '#' },
                ]
            },
            {
                label: 'Label & Barcode',
                items: [
                    { label: 'Label Tracker 1', url: '#' },
                    { label: 'Label Tracker 2', url: '#' },
                    { label: 'Label Tracker 3', url: '#' },
                    { label: 'Label Tracker 4', url: '#' },
                    { label: 'Barcode Perawatan 1', url: '#' },
                    { label: 'Barcode Perawatan 2', url: '#' },
                    { label: 'Barcode RM', url: '#' },
                    { label: 'Label Rekam Medis 10', url: '#' },
                    { label: 'Gelang Pasien 1', url: '#' },
                    { label: 'Gelang Pasien 2', url: '#' },
                    { label: 'Gelang Pasien 3', url: '#' },
                    { label: 'Gelang Pasien 4', url: '#' },
                    { label: 'Gelang Pasien 5', url: '#' },
                    { label: 'Gelang Pasien 6', url: '#' },
                    { label: 'Gelang Pasien 7', url: '#' },
                ]
            },
            {
                label: 'Surat-Surat',
                items: [
                    { label: 'Surat Kontrol', url: '#' },
                    { label: 'Surat Buta Warna', url: '#' },
                    { label: 'Surat Bebas Tato', url: '#' },
                    { label: 'Surat Kewaspadaan Kesehatan', url: '#' },
                    { label: 'Surat Keterangan Bebas TBC', url: '#' },
                    { label: 'Surat Keterangan Sehat 1', url: '#' },
                    { label: 'Surat Keterangan Sehat 2', url: '#' },
                    { label: 'Surat Keterangan Sehat 3', url: '#' },
                    { label: 'Surat Keterangan Bebas Narkoba', url: '#' },
                    { label: 'Surat Cuti Sakit', url: '#' },
                    { label: 'Surat Cuti Sakit Pihak Ke 2', url: '#' },
                    { label: 'Surat Keterangan Rawat Inap', url: '#' },
                    { label: 'Surat Keterangan Hamil/Tidak Hamil', url: '#' },
                    { label: 'Surat Cuti Hamil', url: '#' },
                    { label: 'Surat Keterangan Covid', url: '#' },
                    { label: 'Surat Keterangan Layak Terbang', url: '#' },
                    { label: 'Persetujuan, Pernyataan & Penolakan', children: [
                        { label: 'Persetujuan Umum', url: '#' },
                        { label: 'Persetujuan/Penolakan Tindakan', url: '#' },
                        { label: 'Pulang Atas Permintaan Sendiri', url: '#' },
                        { label: 'Pernyataan Pasien Umum', url: '#' },
                        { label: 'Persetujuan Rawat Inap', url: '#' },
                        { label: 'Persetujuan Penundaan Pelayanan', url: '#' },
                        { label: 'Penolakan Anjuran Medis', url: '#' },
                        { label: 'Persetujuan Pemeriksaan HIV', url: '#' },
                        { label: 'Pernyataan Memilih DPJP', url: '#' },
                    ]},
                ]
            },
            {
                label: 'Rujukan',
                items: [
                    { label: 'Rujukan Keluar', url: '#' },
                    { label: 'Rujukan Poli Internal', url: '#' },
                ]
            },
            {
                label: 'Bridging',
                items: [
                    { label: 'Surat Kontrol', url: '#' },
                    { label: 'Surat Buta Warna', url: '#' },
                    { label: 'Surat Bebas Tato', url: '#' },
                    { label: 'Surat Kewaspadaan Kesehatan', url: '#' },
                    { label: 'Surat Keterangan Bebas TBC', url: '#' },
                    { label: 'Surat Keterangan Sehat 1', url: '#' },
                    { label: 'Surat Keterangan Sehat 2', url: '#' },
                    { label: 'Surat Keterangan Sehat 3', url: '#' },
                    { label: 'Surat Keterangan Bebas Narkoba', url: '#' },
                    { label: 'Surat Cuti Sakit', url: '#' },
                    { label: 'Surat Cuti Sakit Pihak Ke 2', url: '#' },
                    { label: 'Surat Keterangan Rawat Inap', url: '#' },
                    { label: 'Surat Keterangan Hamil/Tidak Hamil', url: '#' },
                    { label: 'Surat Cuti Hamil', url: '#' },
                    { label: 'Surat Keterangan Covid', url: '#' },
                    { label: 'Surat Keterangan Layak Terbang', url: '#' },
                    { label: 'Persetujuan, Pernyataan & Penolakan', url: '#' },
                ]
            },
            {
                label: 'Input Data',
                items: [
                    { label: 'Catatan Untuk Pasien', url: '#' },
                    { label: 'Berkas Digital Perawatan', url: '#' },
                    { label: 'Insiden Keselamatan Pasien', url: '#' },
                ]
            },
            {
                label: 'Set Status',
                items: [
                    { label: 'Berkas R.M. Di Ranap', url: '#' },
                    { label: 'Berkas R.M. Diterima', url: '#' },
                    { label: 'Sudah Periksa', url: '#' },
                    { label: 'Belum Periksa', url: '#' },
                    { label: 'Batal Periksa', url: '#' },
                    { label: 'Dirujuk', url: '#' },
                    { label: 'Dirawat', url: '#' },
                    { label: 'Meninggal', url: '#' },
                    { label: 'Pulang Paksa', url: '#' },
                    { label: 'Status Poli', url: '#' },
                ]
            },
            {
                label: 'Hapus Data',
                items: [
                    { label: 'Tagihan Operasi', url: '#' },
                    { label: 'Obat Operasi', url: '#' },
                    { label: 'Deposit', url: '#' },
                    { label: 'Pemberian Diet', url: '#' },
                    { label: 'Diagnosa/Penyakit', url: '#' },
                    { label: 'DPJP Ranap', url: '#' },
                    { label: 'Hemodialisa', url: '#' },
                    { label: 'Kamar Inap', url: '#' },
                    { label: 'Potongan Biaya', url: '#' },
                    { label: 'Piutang Pasien', url: '#' },
                    { label: 'Prosedur Tindakan', url: '#' },
                    { label: 'Ranap Gabung', url: '#' },
                    { label: 'Rujuk Keluar', url: '#' },
                    { label: 'Rujuk Masuk', url: '#' },
                    { label: 'Tambahan Biaya', url: '#' },
                    { label: 'Booking Operasi', url: '#' },
                    { label: 'Tindakan', children: [
                        { label: 'Ranap Dokter', url: '#' },
                        { label: 'Ranap Dokter & Paramedis', url: '#' },
                        { label: 'Ranap Paramedis', url: '#' },
                        { label: 'Ralan Dokter', url: '#' },
                        { label: 'Ralan Dokter & Paramedis', url: '#' },
                        { label: 'Ralan Paramedis', url: '#' },
                    ]},
                    { label: 'Pemeriksaan', children: [
                        { label: 'Ralan', url: '#' },
                        { label: 'Ranap', url: '#' },
                        { label: 'Laborat', url: '#' },
                        { label: 'Radiologi', url: '#' },
                    ]},
                    { label: 'Obat', children: [
                        { label: 'Aturan Pakai Obat', url: '#' },
                        { label: 'Pemberian Obat', url: '#' },
                        { label: 'Resep Obat', url: '#' },
                        { label: 'Resep Pulang', url: '#' },
                        { label: 'Retur Obat Pasien', url: '#' },
                        { label: 'Stok Obat Ranap', url: '#' },
                    ]},
                    { label: 'Semua Transaksi & Registrasi', url: '#' },
                ]
            }
        ],
        get filteredGroups() {
            const q = this.searchQuery.toLowerCase().trim();
            const isAdmin = this.isAdmin;
            // Helper: cek apakah item (atau turunannya) punya URL valid
            const hasValidUrl = (item) => {
                if (item.children && item.children.length > 0) {
                    return item.children.some(c => hasValidUrl(c));
                }
                return item.url && item.url !== '#';
            };
            const filterItem = (item) => {
                if (item.children && item.children.length > 0) {
                    const fc = item.children.filter(c => {
                        const matchQ = !q || c.label.toLowerCase().includes(q) ||
                            (c.children && c.children.some(gc => gc.label.toLowerCase().includes(q)));
                        const visible = isAdmin || hasValidUrl(c);
                        return matchQ && visible;
                    });
                    if (fc.length > 0) return { ...item, children: fc };
                    if (!q && !isAdmin) return null;
                    if (q && item.label.toLowerCase().includes(q)) return { ...item, children: fc.length ? fc : item.children };
                    return null;
                }
                const matchQ = !q || item.label.toLowerCase().includes(q);
                const visible = isAdmin || hasValidUrl(item);
                return matchQ && visible ? item : null;
            };
            return this.menuGroups.map(g => {
                const filteredItems = g.items.map(filterItem).filter(Boolean);
                return filteredItems.length ? { ...g, items: filteredItems } : null;
            }).filter(Boolean);
        }
    }">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.index') }}" wire:navigate class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
            <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
        </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <span>Rawat Jalan</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Daftar Pasien Rawat Jalan</h1>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div wire:click="setFilter('')"
            class="cursor-pointer transition-all duration-300 group rounded-xl border-2 p-3.5 flex flex-col shadow-sm {{ $filterType === '' ? 'bg-[#4C5C2D] border-[#4C5C2D] shadow-lg scale-[1.02]' : 'bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 hover:border-neutral-300' }}">
            <span
                class="text-xs font-semibold mb-0.5 transition-colors {{ $filterType === '' ? 'text-white/80' : 'text-neutral-500 dark:text-neutral-400 group-hover:text-[#4C5C2D]' }}">Total
                Pasien</span>
            <span
                class="text-xl font-bold transition-colors {{ $filterType === '' ? 'text-white' : 'text-neutral-800 dark:text-neutral-100' }}">{{ number_format($summary['total']) }}</span>
        </div>

        <div wire:click="setFilter('bpjs')"
            class="cursor-pointer transition-all duration-300 group rounded-xl border-2 p-3.5 flex flex-col shadow-sm {{ $filterType === 'bpjs' ? 'bg-[#4C5C2D] border-[#4C5C2D] shadow-lg scale-[1.02]' : 'bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 hover:border-neutral-300' }}">
            <span
                class="text-xs font-semibold mb-0.5 transition-colors {{ $filterType === 'bpjs' ? 'text-white/80' : 'text-neutral-500 dark:text-neutral-400 group-hover:text-[#4C5C2D]' }}">Pasien
                BPJS</span>
            <span
                class="text-xl font-bold transition-colors {{ $filterType === 'bpjs' ? 'text-white' : 'text-neutral-800 dark:text-neutral-100' }}">{{ number_format($summary['bpjs']) }}</span>
        </div>

        <div wire:click="setFilter('umum')"
            class="cursor-pointer transition-all duration-300 group rounded-xl border-2 p-3.5 flex flex-col shadow-sm {{ $filterType === 'umum' ? 'bg-[#4C5C2D] border-[#4C5C2D] shadow-lg scale-[1.02]' : 'bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 hover:border-neutral-300' }}">
            <span
                class="text-xs font-semibold mb-0.5 transition-colors {{ $filterType === 'umum' ? 'text-white/80' : 'text-neutral-500 dark:text-neutral-400 group-hover:text-[#4C5C2D]' }}">Pasien
                Umum</span>
            <span
                class="text-xl font-bold transition-colors {{ $filterType === 'umum' ? 'text-white' : 'text-neutral-800 dark:text-neutral-100' }}">{{ number_format($summary['umum']) }}</span>
        </div>

        <div wire:click="setFilter('lainnya')"
            class="cursor-pointer transition-all duration-300 group rounded-xl border-2 p-3.5 flex flex-col shadow-sm {{ $filterType === 'lainnya' ? 'bg-[#4C5C2D] border-[#4C5C2D] shadow-lg scale-[1.02]' : 'bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 hover:border-neutral-300' }}">
            <span
                class="text-xs font-semibold mb-0.5 transition-colors {{ $filterType === 'lainnya' ? 'text-white/80' : 'text-neutral-500 dark:text-neutral-400 group-hover:text-[#4C5C2D]' }}">Lainnya</span>
            <span
                class="text-xl font-bold transition-colors {{ $filterType === 'lainnya' ? 'text-white' : 'text-neutral-800 dark:text-neutral-100' }}">{{ number_format($summary['lainnya']) }}</span>
        </div>
    </div>

    {{-- Keterangan Kode Warna --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 mb-3 shadow-sm">
        <div class="text-[10px] font-black text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
            <flux:icon name="information-circle" class="w-4 h-4 text-neutral-400 dark:text-neutral-500" />
            <span>Keterangan Status &amp; Kode Warna Pasien</span>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="flex items-center gap-2 p-2 rounded-lg bg-neutral-50 dark:bg-neutral-900/30 border border-neutral-100 dark:border-neutral-850">
                <span class="w-3.5 h-3.5 rounded bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 block flex-shrink-0"></span>
                <span class="text-xs font-medium text-neutral-600 dark:text-neutral-300">Umum/Lainnya (Belum Bayar)</span>
            </div>
            <div class="flex items-center gap-2 p-2 rounded-lg bg-neutral-200 dark:bg-neutral-700/70 border border-neutral-300/80 dark:border-neutral-600/80">
                <span class="w-3.5 h-3.5 rounded bg-neutral-300 dark:bg-neutral-600 border border-neutral-400 dark:border-neutral-500 block flex-shrink-0"></span>
                <span class="text-xs font-semibold text-neutral-700 dark:text-neutral-200">Umum/Lainnya (Sudah Bayar)</span>
            </div>
            <div class="flex items-center justify-between gap-2 p-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/30">
                <div class="flex items-center gap-2">
                    <span class="w-3.5 h-3.5 rounded bg-emerald-250 dark:bg-emerald-800 border border-emerald-350 dark:border-emerald-750 block flex-shrink-0" x-show="showBpjsColor"></span>
                    <span class="w-3.5 h-3.5 rounded bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 block flex-shrink-0" x-show="!showBpjsColor"></span>
                    <span class="text-xs font-semibold text-emerald-800 dark:text-emerald-300">Pasien BPJS (Belum Periksa)</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer select-none">
                    <input type="checkbox" x-model="showBpjsColor" class="sr-only peer">
                    <div class="w-7 h-4 bg-neutral-300 dark:bg-neutral-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-neutral-350 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-emerald-500"></div>
                </label>
            </div>
            <div class="flex items-center gap-2 p-2 rounded-lg bg-red-50 dark:bg-red-950/30 border border-red-100 dark:border-red-900/30">
                <span class="w-3.5 h-3.5 rounded bg-red-200 dark:bg-red-800 border border-red-300 dark:border-red-700 block flex-shrink-0"></span>
                <span class="text-xs font-semibold text-red-800 dark:text-red-300">Pasien BPJS (Sudah Periksa)</span>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
        <div class="flex flex-col gap-4 mb-6">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-1">
                    <flux:input wire:model.live.debounce.300ms="search"
                        placeholder="Cari No Rawat, No RM, atau Nama Pasien..."
                        icon="magnifying-glass" />
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2 flex-shrink-0 bg-neutral-50 dark:bg-neutral-900/50 p-1 rounded-lg border border-neutral-100 dark:border-neutral-800">
                        <div class="flex items-center gap-2 px-2">
                            <label class="text-[10px] font-black uppercase tracking-tighter text-neutral-400">Periode</label>
                            <flux:input type="date" wire:model.live="dari" size="sm" class="w-36" />
                            <span class="text-xs text-neutral-400 font-bold">-</span>
                            <flux:input type="date" wire:model.live="sampai" size="sm" class="w-36" />
                        </div>
                    </div>
                    <flux:button wire:click="$refresh" icon="arrow-path" square size="sm" variant="outline" class="flex-shrink-0 !h-[38px] !w-[38px] !rounded-lg border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-700" title="Refresh Data" />
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <flux:select wire:model.live="kd_poli" placeholder="Semua Poliklinik">
                    <flux:select.option value="">-- Semua Poliklinik --</flux:select.option>
                    @foreach($polikliniks as $poli)
                        <flux:select.option value="{{ $poli->kd_poli }}">{{ $poli->nm_poli }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="kd_dokter" placeholder="Semua Dokter">
                    <flux:select.option value="">-- Semua Dokter --</flux:select.option>
                    @foreach($dokters as $dr)
                        <flux:select.option value="{{ $dr->kd_dokter }}">{{ $dr->nm_dokter }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        <flux:table :paginate="$regPeriksas">
            <flux:table.columns>
                <flux:table.column>Menu</flux:table.column>
                <flux:table.column>{{ __('Nomor Rawat') }}</flux:table.column>
                <flux:table.column>{{ __('Nomor Rekam Medis') }}</flux:table.column>
                <flux:table.column>{{ __('Nama Pasien') }}</flux:table.column>
                <flux:table.column>{{ __('Kode Poli') }}</flux:table.column>
                <flux:table.column>{{ __('Dokter') }}</flux:table.column>
                <flux:table.column>{{ __('Penanggung Jawab') }}</flux:table.column>
                <flux:table.column>{{ __('Jenis Bayar') }}</flux:table.column>
                <flux:table.column align="center">{{ __('Perawatan/Tindakan') }}</flux:table.column>
                <flux:table.column>{{ __('Action') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($regPeriksas as $reg)
                    @php
                        $isBpjs = str_contains(strtoupper($reg->penjab->png_jawab ?? ''), 'BPJS');
                        $rowClassWithGreen = '';
                        $rowClassWithoutGreen = '';
                        if ($isBpjs) {
                            if (($reg->stts ?? '') === 'Sudah') {
                                $rowClassWithGreen = 'bg-red-50 dark:bg-red-950/30';
                                $rowClassWithoutGreen = 'bg-red-50 dark:bg-red-950/30';
                            } else {
                                $rowClassWithGreen = 'bg-emerald-50 dark:bg-emerald-950/20';
                                $rowClassWithoutGreen = '';
                            }
                        } else {
                            if (($reg->status_bayar ?? '') === 'Sudah Bayar') {
                                $rowClassWithGreen = 'bg-neutral-200 dark:bg-neutral-700/70';
                                $rowClassWithoutGreen = 'bg-neutral-200 dark:bg-neutral-700/70';
                            }
                        }
                    @endphp
                    <flux:table.row :key="$reg->no_rawat" x-bind:class="showBpjsColor ? '{{ $rowClassWithGreen }}' : '{{ $rowClassWithoutGreen }}'">
                        <flux:table.cell>
                            <button type="button"
                                @click="openMenu('{{ $reg->no_rawat }}', '{{ $reg->pasien->nm_pasien ?? '' }}')"
                                class="flex items-center justify-center shrink-0 [:where(&)]:size-6 w-5 h-5 rounded-md hover:bg-neutral-100 dark:hover:bg-neutral-700 text-neutral-500 transition-colors">
                                <flux:icon name="squares-2x2" class="w-4 h-4" />
                            </button>
                        </flux:table.cell>
                        <flux:table.cell class="font-medium tracking-tight">{{ $reg->no_rawat }}</flux:table.cell>
                        <flux:table.cell>{{ $reg->no_rkm_medis }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-neutral-800 dark:text-neutral-200">{{ $reg->pasien->nm_pasien ?? '-' }}</span>
                                @if(($reg->pasien->jk ?? '') === 'L')
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/40" title="Laki-laki">
                                        <span class="w-1 h-1 rounded-full bg-blue-500"></span> L
                                    </span>
                                @elseif(($reg->pasien->jk ?? '') === 'P')
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-pink-50 dark:bg-pink-950/30 text-pink-600 dark:text-pink-400 border border-pink-100 dark:border-pink-900/40" title="Perempuan">
                                        <span class="w-1 h-1 rounded-full bg-pink-500"></span> P
                                    </span>
                                @endif
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>{{ $reg->kd_poli }}</flux:table.cell>
                        <flux:table.cell>{{ $reg->dokter->nm_dokter ?? '-' }}</flux:table.cell>
                        <flux:table.cell>{{ $reg->p_jawab }}</flux:table.cell>
                        <flux:table.cell>
                            @if($isBpjs)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-800">
                                    {{ $reg->penjab->png_jawab ?? '-' }}
                                </span>
                            @else
                                {{ $reg->penjab->png_jawab ?? '-' }}
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex justify-center text-center">
                                <flux:button icon="document-text" size="xs"
                                    :href="route('modul.rawat-jalan.perawatan-tindakan', str_replace('/', '-', $reg->no_rawat))"
                                    target="_blank" variant="ghost" />
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button icon="eye" size="xs" :href="route('modul.rawat-jalan.show', str_replace('/', '-', $reg->no_rawat))" target="_blank" variant="ghost" />
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="9">
                            <div class="flex flex-col items-center justify-center py-12 text-neutral-400 dark:text-neutral-500">
                                <flux:icon name="calendar" class="w-12 h-12 mb-3 opacity-40" />
                                <p class="text-sm font-medium">Tidak ada pasien rawat jalan di periode ini</p>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    {{-- ===== SHARED MEGA MENU MODAL (Alpine.js) - Skeleton ===== --}}
    <template x-teleport="body">
        <div x-show="menuModalOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6" style="display: none;"
            @keydown.escape.window="menuModalOpen = false">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="menuModalOpen = false"></div>

            {{-- Panel --}}
            <div x-show="menuModalOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative w-full max-w-6xl max-h-[90vh] flex flex-col bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden"
                @click.stop>

                {{-- Header (Search & Info) --}}
                <div class="flex flex-col sm:flex-row items-center gap-4 justify-between px-6 py-4 border-b border-neutral-200 dark:border-[#4C5C2D] bg-[#4C5C2D] flex-shrink-0 shadow-lg">
                    <div class="flex flex-col gap-1 text-white">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-white/20">
                                <flux:icon name="squares-2x2" class="w-5 h-5 text-white" />
                            </div>
                            <h2 class="font-bold text-white text-lg">Sub Menu Layanan Rawat Jalan</h2>
                        </div>
                        <div class="flex items-center gap-2 mt-1 text-xs opacity-90">
                            <span class="bg-white/20 px-2 py-0.5 rounded font-mono" x-text="activePatient.noRawat"></span>
                            <span class="opacity-50">•</span>
                            <span class="font-semibold uppercase tracking-wide" x-text="activePatient.nmPasien"></span>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                        {{-- Search Input --}}
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <flux:icon name="magnifying-glass" class="w-4 h-4 text-white/60" />
                            </div>
                            <input x-model="searchQuery" type="text" placeholder="Cari menu..."
                                class="block w-full pl-9 pr-3 py-2 text-sm border border-white/20 rounded-lg bg-white/10 text-white focus:ring-2 focus:ring-white/30 focus:border-white/40 transition-colors placeholder-white/50">
                        </div>

                        <button @click="menuModalOpen = false" class="hidden sm:flex p-2 rounded-lg hover:bg-white/10 text-white/70 hover:text-white transition-colors flex-shrink-0">
                            <flux:icon name="x-mark" class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                {{-- Grouped Menu Body --}}
                <div class="overflow-y-auto flex-1 p-5 bg-white dark:bg-neutral-900 space-y-6">

                    {{-- No results --}}
                    <template x-if="filteredGroups.length === 0">
                        <div class="py-16 flex flex-col items-center justify-center text-center">
                            <flux:icon name="magnifying-glass"
                                class="w-12 h-12 text-neutral-300 dark:text-neutral-700 mb-4" />
                            <h3 class="text-lg font-semibold text-neutral-700 dark:text-neutral-300">Menu tidak
                                ditemukan</h3>
                            <p class="text-sm text-neutral-500 mt-1">Gunakan kata kunci pencarian yang lain.</p>
                        </div>
                    </template>

                    {{-- Render each group --}}
                    <template x-for="group in filteredGroups" :key="group.label">
                        <div>
                            {{-- Group Header --}}
                            <div class="flex items-center gap-2 mb-3">
                                <span
                                    class="text-xs font-bold uppercase tracking-widest text-[#4C5C2D] dark:text-[#8CC7C4]"
                                    x-text="group.label"></span>
                                <div class="flex-1 h-px bg-[#4C5C2D]/20 dark:bg-[#8CC7C4]/20"></div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <template x-for="(row, ridx) in chunk(group.items, cols)" :key="ridx">
                                    <div class="w-full flex flex-col">
                                        {{-- Row Grid --}}
                                        <div class="grid grid-cols-5 gap-3">
                                            <template x-for="(item, btnIdx) in row" :key="btnIdx">
                                                <div class="h-full flex flex-col">
                                                    {{-- Item WITH children = expandable --}}
                                                    <template x-if="item.children && item.children.length > 0">
                                                        <div class="h-full flex flex-col">
                                                            <button
                                                                @click="toggleSubMenu(group.label + '_' + item.label)"
                                                                :class="isSubMenuOpen(group.label + '_' + item.label) ? 'bg-[#F1F5E9] text-[#4C5C2D] border-x-2 border-t-2 border-b-0 border-neutral-200 dark:border-neutral-700 rounded-t-2xl z-30' : 'bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 rounded-xl hover:bg-neutral-200 dark:hover:bg-neutral-700 border border-transparent'"
                                                                class="group w-full h-[72px] flex items-center gap-3 p-3 transition-all duration-300 ease-in-out text-left relative">
                                                                <div :class="isSubMenuOpen(group.label + '_' + item.label) ? 'bg-[#4C5C2D]/10 text-[#4C5C2D]' : 'bg-neutral-200 dark:bg-neutral-700 text-neutral-500'"
                                                                    class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 transition-all duration-300">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="w-4.5 h-4.5" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor"
                                                                        stroke-width="2">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                                    </svg>
                                                                </div>
                                                                <span
                                                                    class="text-[11px] font-semibold leading-tight flex-1 line-clamp-2"
                                                                    x-text="item.label"></span>
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="w-4 h-4 flex-shrink-0 transition-transform duration-200"
                                                                    :class="[isSubMenuOpen(group.label + '_' + item.label) ? 'rotate-90 text-[#4C5C2D]' : 'text-neutral-400']"
                                                                    fill="none" viewBox="0 0 24 24"
                                                                    stroke="currentColor" stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M9 5l7 7-7 7" />
                                                                </svg>

                                                                {{-- Connector Bridge --}}
                                                                <div x-show="isSubMenuOpen(group.label + '_' + item.label)"
                                                                    class="absolute -bottom-4 -left-[2px] -right-[2px] h-4 bg-[#F1F5E9] z-40 border-x-2 border-neutral-200 dark:border-neutral-700 overflow-visible">

                                                                    <svg x-show="btnIdx !== 0"
                                                                        class="absolute bottom-[-2px] -left-[17px] w-[18px] h-[18px] text-neutral-200 dark:text-neutral-700"
                                                                        viewBox="0 0 18 18" fill="none">
                                                                        <path
                                                                            d="M18 18V0C18 9.94112 9.94112 18 0 18H18Z"
                                                                            class="fill-[#F1F5E9] dark:fill-neutral-900" />
                                                                        <path d="M18 0C18 9.94112 9.94112 18 0 18"
                                                                            stroke="currentColor" stroke-width="2" />
                                                                        <rect x="17" y="0" width="2" height="18"
                                                                            class="fill-[#F1F5E9] dark:fill-neutral-900" />
                                                                    </svg>

                                                                    <svg x-show="btnIdx !== row.length - 1"
                                                                        class="absolute bottom-[-2px] -right-[17px] w-[18px] h-[18px] text-neutral-200 dark:text-neutral-700"
                                                                        viewBox="0 0 18 18" fill="none">
                                                                        <path d="M0 18V0C0 9.94112 8.05888 18 18 18H0Z"
                                                                            class="fill-[#F1F5E9] dark:fill-neutral-900" />
                                                                        <path d="M0 0C0 9.94112 8.05888 18 18 18"
                                                                            stroke="currentColor" stroke-width="2" />
                                                                        <rect x="-1" y="0" width="2" height="18"
                                                                            class="fill-[#F1F5E9] dark:fill-neutral-900" />
                                                                    </svg>
                                                                </div>
                                                                <div x-show="isSubMenuOpen(group.label + '_' + item.label)"
                                                                    class="absolute -bottom-[18px] left-[0px] right-[0px] h-[4px] bg-[#F1F5E9] z-50">
                                                                </div>
                                                            </button>
                                                        </div>
                                                    </template>

                                                    <template x-if="!item.children || item.children.length === 0">
                                                        <div class="h-full flex flex-col">
                                                            <a :href="getMenuUrl(item.url)"
                                                                :target="(!item.url || item.url === '#') ? '_self' : '_blank'"
                                                                class="group w-full h-[72px] flex items-center gap-3 p-3 rounded-xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 hover:border-[#4C5C2D] hover:bg-[#4C5C2D]/5 transition-all shadow-sm">
                                                                <div
                                                                    class="w-9 h-9 rounded-lg flex items-center justify-center bg-neutral-100 dark:bg-neutral-800 text-neutral-500 group-hover:bg-[#4C5C2D] group-hover:text-white transition-colors flex-shrink-0">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="w-4.5 h-4.5" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor"
                                                                        stroke-width="2">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                    </svg>
                                                                </div>
                                                                <span
                                                                    class="text-[11px] font-semibold leading-tight flex-1 line-clamp-2"
                                                                    x-text="item.label"></span>
                                                            </a>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>

                                        {{-- Mega Menu Panel --}}
                                        <template x-if="row.some(it => isSubMenuOpen(group.label + '_' + it.label))">
                                            <div :class="{
                                                    'rounded-tl-none': row[0] && isSubMenuOpen(group.label + '_' + row[0].label),
                                                    'rounded-tr-none': row[row.length-1] && isSubMenuOpen(group.label + '_' + row[row.length-1].label)
                                                }"
                                                class="relative mt-4 mb-4 bg-[#F1F5E9] dark:bg-neutral-800 border-2 border-neutral-200 dark:border-neutral-700 rounded-2xl p-6 z-20 w-full overflow-visible">

                                                <div :key="activeSubMenu">
                                                    <div :class="`grid grid-cols-${cols} gap-3`"
                                                        :style="`grid-template-columns: repeat(${cols}, minmax(0, 1fr))`">
                                                        <template
                                                            x-for="(child, idx) in row.find(it => isSubMenuOpen(group.label + '_' + it.label))?.children"
                                                            :key="child.label">
                                                            <div class="relative w-full h-[64px]" x-data="{ openDropdown: false }" @click.away="openDropdown = false">
                                                                {{-- Item without subchildren --}}
                                                                <template x-if="!child.children || child.children.length === 0">
                                                                    <a :href="getMenuUrl(child.url)"
                                                                        :target="(!child.url || child.url === '#') ? '_self' : '_blank'"
                                                                        class="flex items-center gap-3 p-3 h-full rounded-xl border border-neutral-100 dark:border-neutral-700 bg-white dark:bg-neutral-900 hover:border-[#4C5C2D] hover:bg-[#4C5C2D]/5 transition-all group/child">
                                                                        <div class="flex-shrink-0 w-7 h-7 rounded-lg bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-[10px] font-bold text-neutral-500 group-hover/child:bg-[#4C5C2D] group-hover/child:text-white transition-colors"
                                                                            x-text="idx + 1"></div>
                                                                        <span x-text="child.label"
                                                                            class="text-[11px] font-medium text-neutral-600 dark:text-neutral-400 group-hover/child:text-[#4C5C2D] dark:group-hover/child:text-[#8CC7C4] leading-tight line-clamp-2 flex-1"></span>
                                                                    </a>
                                                                </template>

                                                                {{-- Item with subchildren (Dropdown) --}}
                                                                <template x-if="child.children && child.children.length > 0">
                                                                    <div class="h-full">
                                                                        <button @click="openDropdown = !openDropdown"
                                                                            class="w-full flex items-center gap-3 p-3 h-full rounded-xl border border-neutral-100 dark:border-neutral-700 bg-white dark:bg-neutral-900 hover:border-[#4C5C2D] hover:bg-[#4C5C2D]/5 transition-all group/child text-left">
                                                                            <div class="flex-shrink-0 w-7 h-7 rounded-lg bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-[10px] font-bold text-neutral-500 group-hover/child:bg-[#4C5C2D] group-hover/child:text-white transition-colors"
                                                                                x-text="idx + 1"></div>
                                                                            <span x-text="child.label"
                                                                                class="text-[11px] font-medium text-neutral-600 dark:text-neutral-400 group-hover/child:text-[#4C5C2D] dark:group-hover/child:text-[#8CC7C4] leading-tight line-clamp-2 flex-1"></span>
                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-neutral-400 transition-transform" :class="openDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                                        </button>
                                                                        
                                                                        <div x-show="openDropdown" x-transition.opacity
                                                                            class="absolute left-0 top-full mt-2 w-56 bg-white dark:bg-neutral-900 rounded-xl shadow-xl border border-neutral-200 dark:border-neutral-700 py-2 z-50">
                                                                            <template x-for="sub in child.children" :key="sub.label">
                                                                                <a :href="getMenuUrl(sub.url)"
                                                                                    :target="(!sub.url || sub.url === '#') ? '_self' : '_blank'"
                                                                                    class="flex items-center gap-3 px-4 py-2 hover:bg-[#F1F5E9] dark:hover:bg-[#4C5C2D]/10 transition-colors group/sub">
                                                                                    <div class="w-1.5 h-1.5 rounded-full bg-neutral-300 dark:bg-neutral-700 group-hover/sub:bg-[#4C5C2D] dark:group-hover/sub:bg-[#8CC7C4] transition-colors"></div>
                                                                                    <span x-text="sub.label" class="text-xs font-medium text-neutral-600 dark:text-neutral-400 group-hover/sub:text-[#4C5C2D] dark:group-hover/sub:text-[#8CC7C4]"></span>
                                                                                </a>
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>
