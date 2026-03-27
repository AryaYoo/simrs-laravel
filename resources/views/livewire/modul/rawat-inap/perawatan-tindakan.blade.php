<div class="flex h-screen overflow-hidden -m-6"
     x-data="{
        searchMenu: '',
        activeMenu: 'pemeriksaan',
        openGroups: {},
        toggleGroup(key) { this.openGroups[key] = !this.openGroups[key]; },
        isGroupOpen(key) { return this.openGroups[key] !== false; },
        menuGroups: [
            {
                key: 'rekam_medis',
                label: 'Data Rekam Medis',
                icon: 'document-text',
                items: [
                    { key: 'pengkajian_awal', label: 'Pengkajian Awal Keperawatan Umum' },
                    { key: 'pengkajian_awal_kebidanan', label: 'Pengkajian Awal Keperawatan Kebidanan' },
                    { key: 'pengkajian_awal_neonatus', label: 'Pengkajian Awal Keperawatan Neonatus' },
                    { key: 'pengkajian_awal_anak', label: 'Pengkajian Awal Keperawatan Bayi/Anak' },
                    { key: 'pengkajian_awal_medis_umum', label: 'Pengkajian Awal Medis Umum' },
                    { key: 'pengkajian_bayi_baru_lahir', label: 'Pengkajian Bayi Baru Lahir' },
                    { key: 'pengkajian_awal_medis_neonatus', label: 'Pengkajian Awal Medis Neonatus' },
                    { key: 'pengkajian_awal_medis_kebidanan', label: 'Pengkajian Awal Medis Kebidanan' },
                    { key: 'pengkajian_awal_medis_psikiatri', label: 'Pengkajian Awal Medis Psikiatri' },
                    { key: 'pengkajian_awal_medis_hemodialisa', label: 'Pengkajian Awal Medis Hemodialisa' },
                    { key: 'pengkajian_awal_medis_jantung', label: 'Pengkajian Awal Medis Jantung' },
                    { key: 'pengkajian_awal_fisioterapi', label: 'Pengkajian Awal Fisioterapi' },
                    { key: 'rm_operasi', label: 'RM Operasi - Pengkajian Pre Induksi' },
                    { key: 'rm_operasi_checklist_pre', label: 'RM Operasi - Check List Pre Operasi' },
                    { key: 'rm_operasi_sign_in', label: 'RM Operasi - Sign-In Sebelum Anestesi' },
                    { key: 'rm_operasi_time_out', label: 'RM Operasi - Time-Out Sebelum Insisi' },
                    { key: 'rm_operasi_sign_out', label: 'RM Operasi - Sign-Out Sebelum Menutup Luka' },
                    { key: 'rm_operasi_checklist_post', label: 'RM Operasi - Check List Post Operasi' },
                    { key: 'rm_operasi_pre_operasi', label: 'RM Operasi - Pengkajian Pre Operasi' },
                    { key: 'rm_operasi_anestesi', label: 'RM Operasi - Catatan Anestesi-Sedasi' },
                    { key: 'rm_operasi_pre_anestesi', label: 'RM Operasi - Pengkajian Pre Anestesi' },
                    { key: 'rm_operasi_kesiapan_anestesi', label: 'RM Operasi - Check List Kesiapan Anestesi' },
                    { key: 'rm_operasi_aldrette', label: 'RM Operasi - Skor Aldrette Pasca Anestesi' },
                    { key: 'rm_operasi_steward', label: 'RM Operasi - Skor Steward Pasca Anestesi' },
                    { key: 'rm_operasi_bromage', label: 'RM Operasi - Skor Bromage Pasca Anestesi' },
                    { key: 'rm_operasi_paska', label: 'RM Operasi - Catatan Pengkajian Paska Operasi' },
                    { key: 'risiko_jatuh_dewasa', label: 'Risiko Jatuh - Lanjutan Dewasa' },
                    { key: 'risiko_jatuh_anak', label: 'Risiko Jatuh - Lanjutan Anak' },
                    { key: 'risiko_jatuh_lansia', label: 'Risiko Jatuh - Lanjutan Lansia' },
                    { key: 'risiko_jatuh_neonatus', label: 'Risiko Jatuh - Lanjutan Neonatus' },
                    { key: 'risiko_jatuh_geriatri', label: 'Risiko Jatuh - Lanjutan Geriatri' },
                    { key: 'risiko_jatuh_psikiatri', label: 'Risiko Jatuh - Lanjutan Psikiatri' },
                    { key: 'skrining_fungsional', label: 'Risiko Jatuh - Skrining Fungsional' },
                    { key: 'risiko_dekubitus', label: 'Risiko Jatuh - Pengkajian Risiko Dekubitus' },
                    { key: 'pengkajian_tambahan_geriatri', label: 'Pengkajian Lain - Tambahan Geriatri' },
                    { key: 'pengkajian_tambahan_bunuh_diri', label: 'Pengkajian Lain - Tambahan Bunuh Diri' },
                    { key: 'pengkajian_tambahan_kekerasan', label: 'Pengkajian Lain - Perilaku Kekerasan' },
                    { key: 'pengkajian_tambahan_melarikan_diri', label: 'Pengkajian Lain - Melarikan Diri' },
                    { key: 'pengkajian_pasien_terminal', label: 'Pengkajian Lain - Pasien Terminal' },
                    { key: 'pengkajian_korban_kekerasan', label: 'Pengkajian Lain - Korban Kekerasan' },
                    { key: 'pengkajian_kecemasan_anak', label: 'Pengkajian Lain - Kecemasan Anak' },
                    { key: 'pengkajian_penyakit_menular', label: 'Pengkajian Lain - Penyakit Menular' },
                    { key: 'pengkajian_imunitas_rendah', label: 'Pengkajian Lain - Imunitas Rendah' },
                    { key: 'pengkajian_dehidrasi', label: 'Pengkajian Lain - Derajat Dehidrasi' },
                    { key: 'pengkajian_psikologi', label: 'Pengkajian Lain - Psikologi' },
                    { key: 'pengkajian_psikologi_klinis', label: 'Pengkajian Lain - Psikologi Klinis' },
                    { key: 'pengkajian_hemodialisa', label: 'Pengkajian Lain - Hemodialisa' },
                    { key: 'pengkajian_restrain', label: 'Pengkajian Lain - Restrain' },
                    { key: 'ews_anak', label: 'Pemantauan EWS - Anak' },
                    { key: 'ews_dewasa', label: 'Pemantauan EWS - Dewasa' },
                    { key: 'ews_obstetri', label: 'Pemantauan EWS - Obstetri' },
                    { key: 'ews_neonatus', label: 'Pemantauan EWS - Neonatus' },
                    { key: 'rm_farmasi_konseling', label: 'RM Farmasi - Konseling Farmasi' },
                    { key: 'rm_farmasi_rekonsiliasi', label: 'RM Farmasi - Rekonsiliasi Obat' },
                    { key: 'catatan_observasi', label: 'Catatan - Observasi' },
                    { key: 'catatan_follow_up_dbd', label: 'Catatan - Follow Up DBD' },
                    { key: 'catatan_keperawatan', label: 'Catatan - Catatan Keperawatan' },
                    { key: 'catatan_gds', label: 'Catatan - Cek GDS' },
                    { key: 'catatan_tranfusi', label: 'Catatan - Monitoring Reaksi Tranfusi' },
                    { key: 'hasil_usg', label: 'Catatan - Hasil USG' },
                    { key: 'hasil_pemeriksaan', label: 'Catatan - Hasil Pemeriksaan' },
                    { key: 'hasil_endoskopi', label: 'Catatan - Hasil Endoskopi' },
                    { key: 'dokumentasi_eswl', label: 'Catatan - Dokumentasi Tindakan ESWL' },
                    { key: 'pengkajian_ulang_nyeri', label: 'Catatan - Pengkajian Ulang Nyeri' },
                    { key: 'catatan_persalinan', label: 'Catatan - Catatan Persalinan' },
                    { key: 'keseimbangan_cairan', label: 'Catatan - Keseimbangan Cairan' },
                    { key: 'catatan_cairan_hemodialisa', label: 'Catatan - Catatan Cairan Hemodialisa' },
                    { key: 'fibrinolitik', label: 'Catatan - Checklist Pemberian Fibrinolitik' },
                    { key: 'laporan_tindakan', label: 'Catatan - Laporan Tindakan' },
                    { key: 'diagnosa', label: 'Diagnosa' },
                    { key: 'rm_gizi_dewasa', label: 'RM Gizi - Skrining Nutrisi Dewasa' },
                    { key: 'rm_gizi_lansia', label: 'RM Gizi - Skrining Nutrisi Lansia' },
                    { key: 'rm_gizi_anak', label: 'RM Gizi - Skrining Nutrisi Anak' },
                    { key: 'rm_gizi_lanjut', label: 'RM Gizi - Skrining Gizi Lanjut' },
                    { key: 'rm_gizi_asuhan', label: 'RM Gizi - Asuhan Gizi' },
                    { key: 'rm_gizi_monitoring', label: 'RM Gizi - Monitoring Gizi' },
                    { key: 'rm_gizi_adime', label: 'RM Gizi - Catatan ADIME Gizi' },
                    { key: 'transfer_antar_ruang', label: 'Transfer Antar Ruang' },
                    { key: 'perencanaan_pemulangan', label: 'Perencanaan Pemulangan' },
                    { key: 'edukasi', label: 'Edukasi - Pelaksanaan Informasi & Edukasi' },
                    { key: 'resume', label: 'Resume' },
                    { key: 'riwayat_perawatan', label: 'Riwayat Perawatan' },
                ]
            },
            {
                key: 'permintaan',
                label: 'Permintaan',
                icon: 'paper-airplane',
                items: [
                    { key: 'jadwal_operasi', label: 'Jadwal Operasi' },
                    { key: 'pemeriksaan_lab', label: 'Pemeriksaan Lab' },
                    { key: 'pemeriksaan_radiologi', label: 'Pemeriksaan Radiologi' },
                    { key: 'informasi_obat', label: 'Informasi Obat' },
                    { key: 'konsultasi_medik', label: 'Konsultasi Medik' },
                ]
            },
            {
                key: 'tindakan',
                label: 'Tindakan & Pemeriksaan',
                icon: 'beaker',
                items: [
                    { key: 'tagihan_ranap', label: 'Data Tagihan/Tindakan Rawat Inap' },
                    { key: 'tagihan_rajal', label: 'Data Tagihan/Tindakan Rawat Jalan' },
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
                key: 'obat',
                label: 'Obat',
                icon: 'archive-box',
                items: [
                    { key: 'data_pemberian_obat', label: 'Data Pemberian Obat/BHP' },
                    { key: 'input_resep_pulang', label: 'Input Resep Pulang' },
                    { key: 'input_no_resep', label: 'Input No. Resep' },
                    { key: 'input_resep_dokter', label: 'Input Resep Dokter' },
                    { key: 'permintaan_stok', label: 'Permintaan Stok Obat Pasien' },
                    { key: 'permintaan_resep_pulang', label: 'Permintaan Resep Pulang' },
                    { key: 'data_stok', label: 'Data Stok Obat Pasien' },
                    { key: 'data_resep_pulang', label: 'Data Resep Pulang' },
                    { key: 'retur_obat', label: 'Retur Obat/Barang/Alkes' },
                    { key: 'penjualan_obat', label: 'Penjualan Obat/Alkes/Barang' },
                ]
            },
            {
                key: 'billing',
                label: 'Billing & Deposit',
                icon: 'banknotes',
                items: [
                    { key: 'deposit_pasien', label: 'Deposit/Titipan Pasien' },
                    { key: 'billing_pasien', label: 'Billing/Pembayaran Pasien' },
                ]
            },
            {
                key: 'laporan',
                label: 'Laporan & Dokumen',
                icon: 'printer',
                items: [
                    { key: 'laporan_surat', label: 'Laporan & Surat' },
                    { key: 'label_gelang', label: 'Label & Gelang Pasien' },
                ]
            },
            {
                key: 'ranap_gabung',
                label: 'Ranap & Kamar',
                icon: 'home',
                items: [
                    { key: 'ranap_gabung_ibu_bayi', label: 'Ranap Gabung Ibu & Bayi' },
                    { key: 'gabungkan_kamar_ibu', label: 'Gabungkan Ke Kamar Ibu' },
                ]
            },
            {
                key: 'dpjp',
                label: 'Dokter DPJP',
                icon: 'user-circle',
                items: [
                    { key: 'input_dokter', label: 'Input Dokter' },
                    { key: 'tampilkan_dokter', label: 'Tampilkan Dokter' },
                    { key: 'filter_dokter', label: 'Filter Dokter' },
                ]
            },
            {
                key: 'rujukan',
                label: 'Rujukan',
                icon: 'arrow-top-right-on-square',
                items: [
                    { key: 'rujukan_keluar', label: 'Rujukan Keluar' },
                    { key: 'rujukan_masuk', label: 'Rujukan Masuk' },
                ]
            },
            {
                key: 'bridging',
                label: 'Bridging BPJS & Lainnya',
                icon: 'link',
                items: [
                    { key: 'bpjs_cari_no', label: 'Cari Peserta BPJS by No. Kepesertaan' },
                    { key: 'bpjs_cari_nik', label: 'Cari Peserta BPJS by NIK/No.KTP' },
                    { key: 'sep_vclaim', label: 'Bridging SEP VClaim' },
                    { key: 'data_sep_bpjs', label: 'Data SEP BPJS' },
                    { key: 'rencana_kontrol', label: 'Rencana Kontrol BPJS' },
                    { key: 'perintah_ranap', label: 'Perintah Rawat Inap BPJS' },
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
            return this.menuGroups
                .map(g => ({ ...g, items: g.items.filter(i => i.label.toLowerCase().includes(q)) }))
                .filter(g => g.items.length > 0 || g.label.toLowerCase().includes(q));
        }
     }">

    {{-- ===== LEFT SIDEBAR ===== --}}
    <aside class="w-72 flex-shrink-0 flex flex-col h-full bg-white dark:bg-neutral-900 border-r border-neutral-200 dark:border-neutral-700 overflow-hidden">

        {{-- Sidebar Header --}}
        <div class="px-4 pt-4 pb-3 border-b border-neutral-200 dark:border-neutral-700 flex-shrink-0">
            <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate class="flex items-center gap-2 text-xs text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 mb-3 transition-colors">
                <flux:icon name="chevron-left" class="w-3.5 h-3.5" />
                Kembali ke Rawat Inap
            </a>
            <div class="flex items-center gap-2.5 mb-3">
                <div class="w-8 h-8 rounded-lg bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 flex items-center justify-center flex-shrink-0">
                    <flux:icon name="clipboard-document-list" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-neutral-800 dark:text-neutral-100 truncate w-44">{{ $regPeriksa->pasien->nm_pasien ?? '-' }}</p>
                    <p class="text-[10px] font-mono text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $no_rawat }}</p>
                </div>
            </div>
            {{-- Search --}}
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <flux:icon name="magnifying-glass" class="w-3.5 h-3.5 text-neutral-400" />
                </div>
                <input x-model="searchMenu" type="text" placeholder="Cari menu..."
                    class="block w-full pl-8 pr-3 py-2 text-xs border border-neutral-200 dark:border-neutral-700 rounded-lg bg-neutral-50 dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 focus:ring-1 focus:ring-[#4C5C2D] focus:border-[#4C5C2D] placeholder-neutral-400 outline-none">
            </div>
        </div>

        {{-- Sidebar Nav --}}
        <nav class="overflow-y-auto flex-1 py-2 px-2">

            {{-- Static Menu: Perawatan & Tindakan (existing tabs) --}}
            <div class="mb-1 px-2 pt-1">
                <span class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 dark:text-neutral-500">Utama</span>
            </div>
            <button @click="activeMenu = 'penanganan_dokter_petugas'" wire:click="setActiveTab('penanganan_dokter_petugas')"
                :class="activeMenu === 'penanganan_dokter_petugas' ? 'bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/20 text-[#4C5C2D] dark:text-[#8CC7C4] font-semibold' : 'text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800'"
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-colors text-left">
                <flux:icon name="user-group" class="w-4 h-4 flex-shrink-0" />
                Penanganan Dokter & Petugas
            </button>
            <button @click="activeMenu = 'pemeriksaan'" wire:click="setActiveTab('pemeriksaan')"
                :class="activeMenu === 'pemeriksaan' ? 'bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/20 text-[#4C5C2D] dark:text-[#8CC7C4] font-semibold' : 'text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800'"
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-colors text-left">
                <flux:icon name="clipboard-document-check" class="w-4 h-4 flex-shrink-0" />
                Pemeriksaan (SOAPIE)
            </button>
            <a href="{{ route('modul.rawat-inap.sub-rawat-inap.riwayat-pasien', str_replace('/', '-', $no_rawat)) }}" target="_blank"
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                <flux:icon name="clock" class="w-4 h-4 flex-shrink-0" />
                Riwayat Pasien
                <flux:icon name="arrow-top-right-on-square" class="w-3.5 h-3.5 ml-auto text-neutral-400" />
            </a>

            <div class="my-2 mx-2 border-t border-neutral-200 dark:border-neutral-700"></div>

            {{-- Dynamic grouped menus with accordion --}}
            <template x-if="filteredGroups.length === 0">
                <div class="px-3 py-6 text-center text-xs text-neutral-400">
                    Menu tidak ditemukan.<br>Coba kata kunci lain.
                </div>
            </template>

            <template x-for="group in filteredGroups" :key="group.key">
                <div class="mb-1">
                    {{-- Group Header (Accordion Toggle) --}}
                    <button @click="toggleGroup(group.key)"
                        class="w-full flex items-center gap-2 px-2 py-1.5 rounded-md text-left hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors group">
                        <div class="w-5 h-5 rounded bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center flex-shrink-0 group-hover:bg-neutral-200 dark:group-hover:bg-neutral-700 transition-colors">
                            <flux:icon :name="group.icon" class="w-3 h-3 text-neutral-500" />
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 flex-1" x-text="group.label"></span>
                        <flux:icon name="chevron-right" class="w-3 h-3 text-neutral-400 transition-transform duration-200 flex-shrink-0" :class="isGroupOpen(group.key) ? 'rotate-90' : ''" />
                    </button>

                    {{-- Group Items --}}
                    <div x-show="isGroupOpen(group.key)" x-transition class="pl-2 mt-0.5 space-y-0.5">
                        <template x-for="item in group.items" :key="item.key">
                            <button
                                @click="activeMenu = item.key; $wire.setActiveTab(item.key)"
                                :class="activeMenu === item.key ? 'bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/20 text-[#4C5C2D] dark:text-[#8CC7C4] font-semibold' : 'text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800'"
                                class="w-full flex items-center gap-2 px-3 py-1.5 rounded-lg text-[11px] transition-colors text-left leading-snug">
                                <div class="w-1 h-1 rounded-full bg-current flex-shrink-0 opacity-50"></div>
                                <span x-text="item.label" class="leading-tight"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

        </nav>
    </aside>

    {{-- ===== RIGHT CONTENT ===== --}}
    <main class="flex-1 flex flex-col overflow-hidden">

        {{-- Top Bar (Breadcrumb + Patient Info) --}}
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

            {{-- Existing tab renders driven by Livewire --}}
            @if($activeTab === 'penanganan_dokter')
                @include('livewire.modul.rawat-inap.perawatan-tindakan.penanganan-dokter')
            @elseif($activeTab === 'penanganan_petugas')
                @include('livewire.modul.rawat-inap.perawatan-tindakan.penanganan-petugas')
            @elseif($activeTab === 'penanganan_dokter_petugas')
                @include('livewire.modul.rawat-inap.perawatan-tindakan.penanganan-dokter-dan-petugas')
            @elseif($activeTab === 'pemeriksaan')
                @include('livewire.modul.rawat-inap.perawatan-tindakan.pemeriksaan')
            @else
                {{-- Placeholder for menus not yet implemented --}}
                <div class="flex flex-col items-center justify-center py-20 text-center text-neutral-400 dark:text-neutral-600">
                    <flux:icon name="wrench-screwdriver" class="w-14 h-14 mb-4 opacity-30" />
                    <h3 class="text-base font-semibold text-neutral-500 dark:text-neutral-400" x-data x-text="
                        (() => {
                            const all = @js(collect($this->menuGroups ?? []));
                            return 'Halaman sedang dalam pengembangan';
                        })()
                    ">Halaman sedang dalam pengembangan</h3>
                    <p class="text-sm mt-1">Menu ini belum tersedia. Hubungi administrator untuk mengaktifkannya.</p>
                </div>
            @endif

        </div>

    </main>

</div>
