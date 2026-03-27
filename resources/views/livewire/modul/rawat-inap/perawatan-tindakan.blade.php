<div class="flex h-screen overflow-hidden -m-6"
     x-data="{
        searchMenu: '',
        activeMenu: 'pemeriksaan',
        openGroups: {},
        openSubItems: {},
        toggleGroup(key) { this.openGroups[key] = !this.openGroups[key]; },
        isGroupOpen(key) { return this.openGroups[key] !== false; },
        toggleSubItem(key) { this.openSubItems[key] = !this.openSubItems[key]; },
        isSubItemOpen(key) { return this.openSubItems[key] === true; },
        menuGroups: [
            {
                key: 'rekam_medis',
                label: 'Data Rekam Medis',
                items: [
                    { key: 'pengkajian_awal', label: 'Pengkajian Awal', children: [
                        { key: 'pengkajian_awal_kep_umum', label: 'Keperawatan Umum' },
                        { key: 'pengkajian_awal_kep_kebidanan', label: 'Keperawatan Kebidanan & Kandungan' },
                        { key: 'pengkajian_awal_kep_neonatus', label: 'Keperawatan Neonatus' },
                        { key: 'pengkajian_awal_kep_anak', label: 'Keperawatan Bayi/Anak' },
                        { key: 'pengkajian_awal_medis_umum', label: 'Medis Umum' },
                        { key: 'pengkajian_bayi_baru_lahir', label: 'Pengkajian Bayi Baru Lahir' },
                        { key: 'pengkajian_awal_medis_neonatus', label: 'Medis Neonatus' },
                        { key: 'pengkajian_awal_medis_kebidanan', label: 'Medis Kebidanan & Kandungan' },
                        { key: 'pengkajian_awal_medis_psikiatri', label: 'Medis Psikiatri' },
                        { key: 'pengkajian_awal_medis_hemodialisa', label: 'Medis Hemodialisa' },
                        { key: 'pengkajian_awal_medis_jantung', label: 'Medis Jantung' },
                        { key: 'pengkajian_awal_fisioterapi', label: 'Fisioterapi' },
                    ]},
                    { key: 'rm_operasi', label: 'RM Operasi', children: [
                        { key: 'rm_op_pre_induksi', label: 'Pengkajian Pre Induksi' },
                        { key: 'rm_op_checklist_pre', label: 'Check List Pre Operasi' },
                        { key: 'rm_op_sign_in', label: 'Sign-In Sebelum Anestesi' },
                        { key: 'rm_op_time_out', label: 'Time-Out Sebelum Insisi' },
                        { key: 'rm_op_sign_out', label: 'Sign-Out Sebelum Menutup Luka' },
                        { key: 'rm_op_checklist_post', label: 'Check List Post Operasi' },
                        { key: 'rm_op_pre_operasi', label: 'Pengkajian Pre Operasi' },
                        { key: 'rm_op_anestesi', label: 'Catatan Anestesi-Sedasi' },
                        { key: 'rm_op_pre_anestesi', label: 'Pengkajian Pre Anestesi' },
                        { key: 'rm_op_kesiapan_anestesi', label: 'Check List Kesiapan Anestesi' },
                        { key: 'rm_op_aldrette', label: 'Skor Aldrette Pasca Anestesi' },
                        { key: 'rm_op_steward', label: 'Skor Steward Pasca Anestesi' },
                        { key: 'rm_op_bromage', label: 'Skor Bromage Pasca Anestesi' },
                        { key: 'rm_op_paska', label: 'Catatan Pengkajian Paska Operasi' },
                    ]},
                    { key: 'rm_hcu_icu', label: 'RM HCU, ICU, NICU & PICU', children: [
                        { key: 'rm_hcu_pre_induksi', label: 'Pengkajian Pre Induksi' },
                        { key: 'rm_hcu_checklist_pre', label: 'Check List Pre Operasi' },
                        { key: 'rm_hcu_sign_in', label: 'Sign-In Sebelum Anestesi' },
                        { key: 'rm_hcu_time_out', label: 'Time-Out Sebelum Insisi' },
                        { key: 'rm_hcu_sign_out', label: 'Sign-Out Sebelum Menutup Luka' },
                        { key: 'rm_hcu_checklist_post', label: 'Check List Post Operasi' },
                        { key: 'rm_hcu_pre_operasi', label: 'Pengkajian Pre Operasi' },
                        { key: 'rm_hcu_anestesi', label: 'Catatan Anestesi-Sedasi' },
                        { key: 'rm_hcu_pre_anestesi', label: 'Pengkajian Pre Anestesi' },
                        { key: 'rm_hcu_kesiapan_anestesi', label: 'Check List Kesiapan Anestesi' },
                        { key: 'rm_hcu_aldrette', label: 'Skor Aldrette Pasca Anestesi' },
                        { key: 'rm_hcu_steward', label: 'Skor Steward Pasca Anestesi' },
                        { key: 'rm_hcu_bromage', label: 'Skor Bromage Pasca Anestesi' },
                        { key: 'rm_hcu_paska', label: 'Catatan Pengkajian Paska Operasi' },
                        { key: 'rm_hcu_masuk_hcu', label: 'Check List Kriteria Masuk HCU' },
                        { key: 'rm_hcu_keluar_hcu', label: 'Check List Kriteria Keluar HCU' },
                        { key: 'rm_hcu_masuk_icu', label: 'Check List Kriteria Masuk ICU' },
                        { key: 'rm_hcu_keluar_icu', label: 'Check List Kriteria Keluar ICU' },
                        { key: 'rm_hcu_masuk_nicu', label: 'Check List Kriteria Masuk NICU' },
                        { key: 'rm_hcu_keluar_nicu', label: 'Check List Kriteria Keluar NICU' },
                        { key: 'rm_hcu_masuk_picu', label: 'Check List Kriteria Masuk PICU' },
                        { key: 'rm_hcu_keluar_picu', label: 'Check List Kriteria Keluar PICU' },
                    ]},
                    { key: 'uji_fungsi_kfr', label: 'Uji Fungsi/Prosedur KFR' },
                    { key: 'risiko_jatuh', label: 'Risiko Jatuh, Fungsional & Dekubitus', children: [
                        { key: 'risiko_jatuh_dewasa', label: 'Lanjutan Risiko Jatuh Dewasa' },
                        { key: 'risiko_jatuh_anak', label: 'Lanjutan Risiko Jatuh Anak' },
                        { key: 'risiko_jatuh_lansia', label: 'Lanjutan Risiko Jatuh Lansia' },
                        { key: 'risiko_jatuh_neonatus', label: 'Lanjutan Risiko Jatuh Neonatus' },
                        { key: 'risiko_jatuh_geriatri', label: 'Lanjutan Risiko Jatuh Geriatri' },
                        { key: 'risiko_jatuh_psikiatri', label: 'Lanjutan Risiko Jatuh Psikiatri' },
                        { key: 'skrining_fungsional', label: 'Lanjutan Skrining Fungsional' },
                        { key: 'risiko_dekubitus', label: 'Pengkajian Risiko Dekubitus' },
                    ]},
                    { key: 'pengkajian_lain', label: 'Pengkajian Lain', children: [
                        { key: 'pkj_lain_geriatri', label: 'Pengkajian Tambahan Geriatri' },
                        { key: 'pkj_lain_bunuh_diri', label: 'Pengkajian Tambahan Bunuh Diri' },
                        { key: 'pkj_lain_kekerasan', label: 'Pengkajian Tambahan Perilaku Kekerasan' },
                        { key: 'pkj_lain_melarikan_diri', label: 'Pengkajian Tambahan Melarikan Diri' },
                        { key: 'pkj_lain_terminal', label: 'Pengkajian Pasien Terminal' },
                        { key: 'pkj_lain_korban_kekerasan', label: 'Pengkajian Korban Kekerasan' },
                        { key: 'pkj_lain_kecemasan_anak', label: 'Pengkajian Kecemasan Pasien Anak' },
                        { key: 'pkj_lain_penyakit_menular', label: 'Pengkajian Pasien Penyakit Menular' },
                        { key: 'pkj_lain_imunitas_rendah', label: 'Pengkajian Pasien Imunitas Rendah' },
                        { key: 'pkj_lain_dehidrasi', label: 'Pengkajian Derajat Dehidrasi' },
                        { key: 'pkj_lain_psikologi', label: 'Pengkajian Psikologi' },
                        { key: 'pkj_lain_psikologi_klinis', label: 'Pengkajian Psikologi Klinis' },
                        { key: 'pkj_lain_hemodialisa', label: 'Hemodialisa' },
                        { key: 'pkj_lain_restrain', label: 'Pengkajian Restrain' },
                    ]},
                    { key: 'pemantauan_ews', label: 'Pemantauan EWS', children: [
                        { key: 'ews_anak', label: 'Anak' },
                        { key: 'ews_dewasa', label: 'Dewasa' },
                        { key: 'ews_obstetri', label: 'Obstetri' },
                        { key: 'ews_neonatus', label: 'Neonatus' },
                    ]},
                    { key: 'rm_farmasi', label: 'RM Farmasi', children: [
                        { key: 'rm_farmasi_konseling', label: 'Konseling Farmasi' },
                        { key: 'rm_farmasi_rekonsiliasi', label: 'Rekonsiliasi Obat' },
                    ]},
                    { key: 'catatan_dokumentasi', label: 'Catatan & Dokumentasi', children: [
                        { key: 'catatan_observasi', label: 'Observasi' },
                        { key: 'catatan_follow_up_dbd', label: 'Follow Up DBD' },
                        { key: 'catatan_keperawatan', label: 'Catatan Keperawatan' },
                        { key: 'catatan_gds', label: 'Cek GDS' },
                        { key: 'catatan_tranfusi', label: 'Monitoring Reaksi Tranfusi' },
                        { key: 'hasil_usg', label: 'Hasil USG' },
                        { key: 'hasil_pemeriksaan', label: 'Hasil Pemeriksaan' },
                        { key: 'hasil_endoskopi', label: 'Hasil Endoskopi' },
                        { key: 'dokumentasi_eswl', label: 'Dokumentasi Tindakan ESWL' },
                        { key: 'pengkajian_ulang_nyeri', label: 'Pengkajian Ulang Nyeri' },
                        { key: 'catatan_persalinan', label: 'Catatan Persalinan' },
                        { key: 'keseimbangan_cairan', label: 'Keseimbangan Cairan' },
                        { key: 'catatan_cairan_hemodialisa', label: 'Catatan Cairan Hemodialisa' },
                        { key: 'fibrinolitik', label: 'Checklist Pemberian Fibrinolitik' },
                        { key: 'laporan_tindakan', label: 'Laporan Tindakan' },
                    ]},
                    { key: 'diagnosa', label: 'Diagnosa' },
                    { key: 'rm_gizi', label: 'RM Gizi', children: [
                        { key: 'rm_gizi_dewasa', label: 'Skrining Nutrisi Pasien Dewasa' },
                        { key: 'rm_gizi_lansia', label: 'Skrining Nutrisi Pasien Lansia' },
                        { key: 'rm_gizi_anak', label: 'Skrining Nutrisi Pasien Anak' },
                        { key: 'rm_gizi_lanjut', label: 'Skrining Gizi Lanjut' },
                        { key: 'rm_gizi_asuhan', label: 'Asuhan Gizi' },
                        { key: 'rm_gizi_monitoring', label: 'Monitoring Gizi' },
                        { key: 'rm_gizi_adime', label: 'Catatan ADIME Gizi' },
                    ]},
                    { key: 'transfer_antar_ruang', label: 'Transfer Antar Ruang' },
                    { key: 'perencanaan_pemulangan', label: 'Perencanaan Pemulangan' },
                    { key: 'edukasi', label: 'Edukasi (Pelaksanaan Informasi & Edukasi)' },
                    { key: 'resume', label: 'Resume' },
                    { key: 'riwayat_perawatan', label: 'Riwayat Perawatan' },
                ]
            },
            {
                key: 'permintaan', label: 'Permintaan',
                items: [
                    { key: 'jadwal_operasi', label: 'Jadwal Operasi' },
                    { key: 'pemeriksaan_lab', label: 'Pemeriksaan Lab' },
                    { key: 'pemeriksaan_radiologi', label: 'Pemeriksaan Radiologi' },
                    { key: 'informasi_obat', label: 'Informasi Obat' },
                    { key: 'konsultasi_medik', label: 'Konsultasi Medik' },
                ]
            },
            {
                key: 'tindakan', label: 'Tindakan & Pemeriksaan',
                items: [
                    { key: 'tagihan_rajal', label: 'Data Tagihan/Tindakan Rawat Jalan' },
                    { key: 'tagihan_ranap', label: 'Data Tagihan/Tindakan Rawat Inap' },
                    { key: 'periksa_lab_pk', label: 'Periksa Lab PK' },
                    { key: 'periksa_lab_pa', label: 'Periksa Lab PA' },
                    { key: 'periksa_lab_mb', label: 'Periksa Lab MB' },
                    { key: 'periksa_radiologi', label: 'Periksa Radiologi' },
                    { key: 'tagihan_operasi', label: 'Tagihan Operasi/VK' },
                    { key: 'diet_pasien', label: 'Diet Pasien' },
                    { key: 'data_operasi', label: 'Data Operasi' },
                ]
            },
            {
                key: 'obat', label: 'Obat',
                items: [
                    { key: 'data_pemberian_obat', label: 'Data Pemberian Obat/BHP' },
                    { key: 'input_resep_pulang', label: 'Input Resep Pulang' },
                    { key: 'input_no_resep', label: 'Input No. Resep' },
                    { key: 'input_resep_dokter', label: 'Input Resep Dokter' },
                    { key: 'permintaan_stok', label: 'Permintaan Stok Obat Pasien' },
                    { key: 'permintaan_resep_pulang_obat', label: 'Permintaan Resep Pulang' },
                    { key: 'data_stok', label: 'Data Stok Obat Pasien' },
                    { key: 'data_resep_pulang', label: 'Data Resep Pulang' },
                    { key: 'retur_obat', label: 'Retur Obat/Barang/Alkes' },
                    { key: 'penjualan_obat', label: 'Penjualan Obat/Alkes/Barang' },
                ]
            },
            {
                key: 'billing', label: 'Billing & Deposit',
                items: [
                    { key: 'deposit_pasien', label: 'Deposit/Titipan Pasien' },
                    { key: 'billing_pasien', label: 'Billing/Pembayaran Pasien' },
                ]
            },
            {
                key: 'laporan', label: 'Laporan & Dokumen',
                items: [
                    { key: 'laporan_surat', label: 'Laporan & Surat' },
                    { key: 'label_gelang', label: 'Label & Gelang Pasien' },
                ]
            },
            {
                key: 'ranap_gabung', label: 'Ranap & Kamar',
                items: [
                    { key: 'ranap_gabung_ibu_bayi', label: 'Ranap Gabung Ibu & Bayi' },
                    { key: 'gabungkan_kamar_ibu', label: 'Gabungkan Ke Kamar Ibu' },
                ]
            },
            {
                key: 'dpjp', label: 'Dokter DPJP',
                items: [
                    { key: 'input_dokter', label: 'Input Dokter' },
                    { key: 'tampilkan_dokter', label: 'Tampilkan Dokter' },
                    { key: 'filter_dokter', label: 'Filter Dokter' },
                ]
            },
            {
                key: 'rujukan', label: 'Rujukan',
                items: [
                    { key: 'rujukan_keluar', label: 'Rujukan Keluar' },
                    { key: 'rujukan_masuk', label: 'Rujukan Masuk' },
                ]
            },
            {
                key: 'bridging', label: 'Bridging BPJS & Lainnya',
                items: [
                    { key: 'bpjs_cari_no', label: 'Cari Peserta BPJS by No. Kepesertaan' },
                    { key: 'bpjs_cari_nik', label: 'Cari Peserta BPJS by NIK/No.KTP' },
                    { key: 'sep_vclaim', label: 'Bridging SEP VClaim' },
                    { key: 'data_sep_bpjs', label: 'Data SEP BPJS' },
                    { key: 'rencana_kontrol', label: 'Rencana Kontrol BPJS' },
                    { key: 'perintah_ranap_bpjs', label: 'Perintah Rawat Inap BPJS' },
                    { key: 'suplesi_jasa_raharja', label: 'Suplesi Jasa Raharja BPJS' },
                    { key: 'data_kecelakaan', label: 'Data Induk Kecelakaan BPJS' },
                    { key: 'belum_sep', label: 'Belum Terbit SEP BPJS' },
                    { key: 'sudah_sep', label: 'Sudah Terbit SEP BPJS' },
                    { key: 'rujuk_sisrute', label: 'Rujuk Keluar Via Sisrute' },
                    { key: 'pasien_corona_kemenkes', label: 'Pasien Corona Kemenkes' },
                    { key: 'perawatan_corona_inacbg', label: 'Perawatan Pasien Corona INACBG' },
                    { key: 'tb_kemenkes', label: 'Teridentifikasi TB Kemenkes' },
                    { key: 'data_pcare', label: 'Data PCare' },
                ]
            },
        ],
        get filteredGroups() {
            const q = this.searchMenu.toLowerCase().trim();
            if (!q) return this.menuGroups;
            return this.menuGroups.map(g => {
                const filteredItems = g.items
                    .map(item => {
                        if (!item.children) {
                            return item.label.toLowerCase().includes(q) ? item : null;
                        }
                        const filteredChildren = item.children.filter(c => c.label.toLowerCase().includes(q));
                        if (filteredChildren.length > 0 || item.label.toLowerCase().includes(q)) {
                            return { ...item, children: filteredChildren.length ? filteredChildren : item.children };
                        }
                        return null;
                    })
                    .filter(Boolean);
                return filteredItems.length ? { ...g, items: filteredItems } : null;
            }).filter(Boolean);
        }
     }">

    {{-- ===== LEFT SIDEBAR ===== --}}
    <aside class="w-72 flex-shrink-0 flex flex-col h-full bg-white dark:bg-neutral-900 border-r border-neutral-200 dark:border-neutral-700 overflow-hidden">

        {{-- Sidebar Header --}}
        <div class="px-4 pt-4 pb-3 border-b border-neutral-200 dark:border-neutral-700 flex-shrink-0">
            <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate class="flex items-center gap-2 text-xs text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 mb-3 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                Kembali ke Rawat Inap
            </a>
            <div class="flex items-center gap-2.5 mb-3">
                <div class="w-8 h-8 rounded-lg bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-800 dark:text-neutral-100 truncate w-44">{{ $regPeriksa->pasien->nm_pasien ?? '-' }}</p>
                    <p class="text-[10px] font-mono text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $no_rawat }}</p>
                </div>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input x-model="searchMenu" type="text" placeholder="Cari menu..."
                    class="block w-full pl-8 pr-3 py-2 text-xs border border-neutral-200 dark:border-neutral-700 rounded-lg bg-neutral-50 dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 focus:ring-1 focus:ring-[#4C5C2D] focus:border-[#4C5C2D] placeholder-neutral-400 outline-none">
            </div>
        </div>

        {{-- Sidebar Nav --}}
        <nav class="overflow-y-auto flex-1 py-2 px-2">

            {{-- Static "Utama" section --}}
            <div class="mb-1 px-2 pt-1">
                <span class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 dark:text-neutral-500">Utama</span>
            </div>
            <button @click="activeMenu = 'penanganan_dokter_petugas'" wire:click="setActiveTab('penanganan_dokter_petugas')"
                :class="activeMenu === 'penanganan_dokter_petugas' ? 'bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/20 text-[#4C5C2D] dark:text-[#8CC7C4] font-semibold' : 'text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800'"
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-colors text-left mb-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0" /></svg>
                Penanganan Dokter & Petugas
            </button>
            <button @click="activeMenu = 'pemeriksaan'" wire:click="setActiveTab('pemeriksaan')"
                :class="activeMenu === 'pemeriksaan' ? 'bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/20 text-[#4C5C2D] dark:text-[#8CC7C4] font-semibold' : 'text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800'"
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-colors text-left mb-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                Pemeriksaan (SOAPIE)
            </button>
            <a href="{{ route('modul.rawat-inap.sub-rawat-inap.riwayat-pasien', str_replace('/', '-', $no_rawat)) }}" target="_blank"
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors mb-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Riwayat Pasien
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 ml-auto text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
            </a>

            <div class="my-2 mx-2 border-t border-neutral-200 dark:border-neutral-700"></div>

            {{-- No results --}}
            <template x-if="filteredGroups.length === 0">
                <div class="px-3 py-6 text-center text-xs text-neutral-400">
                    Menu tidak ditemukan.<br>Coba kata kunci lain.
                </div>
            </template>

            {{-- Level 1: Groups --}}
            <template x-for="group in filteredGroups" :key="group.key">
                <div class="mb-1">
                    <button @click="toggleGroup(group.key)"
                        class="w-full flex items-center gap-2 px-2 py-1.5 rounded-md text-left hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 flex-1" x-text="group.label"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-neutral-400 transition-transform duration-200 flex-shrink-0" :class="isGroupOpen(group.key) ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    </button>

                    {{-- Level 2: Items (may have children) --}}
                    <div x-show="isGroupOpen(group.key)" x-transition class="pl-1 mt-0.5 space-y-0.5">
                        <template x-for="item in group.items" :key="item.key">
                            <div>
                                {{-- Item WITH children = sub-accordion --}}
                                <template x-if="item.children && item.children.length > 0">
                                    <div>
                                        <button @click="toggleSubItem(item.key)"
                                            :class="activeMenu === item.key ? 'text-[#4C5C2D] dark:text-[#8CC7C4]' : 'text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800'"
                                            class="w-full flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] transition-colors text-left">
                                            <div class="w-1 h-1 rounded-full bg-current flex-shrink-0 opacity-50"></div>
                                            <span x-text="item.label" class="leading-tight flex-1"></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-neutral-400 transition-transform duration-200 flex-shrink-0" :class="isSubItemOpen(item.key) ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                        </button>
                                        {{-- Level 3: Children --}}
                                        <div x-show="isSubItemOpen(item.key)" x-transition class="pl-4 mt-0.5 space-y-0.5">
                                            <template x-for="child in item.children" :key="child.key">
                                                <button @click="activeMenu = child.key; $wire.setActiveTab(child.key)"
                                                    :class="activeMenu === child.key ? 'bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/20 text-[#4C5C2D] dark:text-[#8CC7C4] font-semibold' : 'text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800'"
                                                    class="w-full flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] transition-colors text-left">
                                                    <div class="w-1 h-1 rounded-full bg-current flex-shrink-0 opacity-30"></div>
                                                    <span x-text="child.label" class="leading-tight"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                {{-- Item WITHOUT children = direct action --}}
                                <template x-if="!item.children || item.children.length === 0">
                                    <button @click="activeMenu = item.key; $wire.setActiveTab(item.key)"
                                        :class="activeMenu === item.key ? 'bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/20 text-[#4C5C2D] dark:text-[#8CC7C4] font-semibold' : 'text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800'"
                                        class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-[11px] transition-colors text-left">
                                        <div class="w-1 h-1 rounded-full bg-current flex-shrink-0 opacity-50"></div>
                                        <span x-text="item.label" class="leading-tight"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

        </nav>
    </aside>

    {{-- ===== RIGHT CONTENT ===== --}}
    <main class="flex-1 flex flex-col overflow-hidden">

        {{-- Top bar --}}
        <div class="flex-shrink-0 bg-white dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-700 px-6 py-3">
            <nav class="text-xs text-neutral-400 mb-0.5">
                <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                <span class="mx-1">/</span>
                <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate class="hover:underline">Rawat Inap</a>
                <span class="mx-1">/</span>
                <span>Perawatan & Tindakan</span>
            </nav>
            <div class="flex items-center justify-between">
                <h1 class="text-base font-bold text-neutral-800 dark:text-neutral-100">Perawatan & Tindakan</h1>
                <div class="flex items-center gap-3 text-xs text-neutral-500">
                    <span class="font-mono bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 text-[#4C5C2D] dark:text-[#8CC7C4] px-2 py-0.5 rounded font-bold">{{ $no_rawat }}</span>
                    <span class="bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 px-2 py-0.5 rounded font-semibold">{{ $regPeriksa->pasien->nm_pasien ?? '-' }}</span>
                </div>
            </div>
        </div>

        {{-- Scrollable Content --}}
        <div class="flex-1 overflow-y-auto p-6">
            @if($activeTab === 'penanganan_dokter')
                @include('livewire.modul.rawat-inap.perawatan-tindakan.penanganan-dokter')
            @elseif($activeTab === 'penanganan_petugas')
                @include('livewire.modul.rawat-inap.perawatan-tindakan.penanganan-petugas')
            @elseif($activeTab === 'penanganan_dokter_petugas')
                @include('livewire.modul.rawat-inap.perawatan-tindakan.penanganan-dokter-dan-petugas')
            @elseif($activeTab === 'pemeriksaan')
                @include('livewire.modul.rawat-inap.perawatan-tindakan.pemeriksaan')
            @else
                <div class="flex flex-col items-center justify-center py-20 text-center text-neutral-400 dark:text-neutral-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mb-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" /></svg>
                    <h3 class="text-base font-semibold text-neutral-500 dark:text-neutral-400">Halaman Sedang dalam Pengembangan</h3>
                    <p class="text-sm mt-1">Menu ini belum tersedia. Hubungi administrator untuk mengaktifkannya.</p>
                </div>
            @endif
        </div>

    </main>

</div>
