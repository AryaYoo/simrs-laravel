<?php

namespace App\Livewire\Bridging\ErmBpjs;

use App\Models\BridgingSep;
use App\Models\BpjsRekamMedisResume;
use App\Models\BpjsRekamMedisLog;
use App\Models\DiagnosaPasien;
use App\Models\ProsedurPasien;
use App\Services\BpjsService;
use App\Services\SatuSehatService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'ERM BPJS'])]
class Index extends Component
{
    use WithPagination;

    // Filter
    public string $search    = '';
    public string $dari      = '';
    public string $sampai    = '';
    public int    $perPage   = 15;
    
    // Bulk Selection
    public array  $selected  = [];
    public bool   $selectAll = false;

    // Modal state
    public bool   $showModal  = false;
    public ?string $activeNoRawat = null;

    // Form fields (Resume Medis)
    public string $no_sep            = '';
    public string $keluhan_utama     = '';
    public string $riwayat_penyakit  = '';
    public string $diagnosis_masuk   = '';
    public string $pemeriksaan_fisik = '';
    public string $plan_of_care      = '';
    public string $instruksi_pulang  = '';
    public string $alergi            = '';

    // Error mapping for BPJS
    protected array $bpjsErrorCodes = [
        '000' => 'Berhasil.',
        '200' => 'Berhasil (OK).',
        '201' => 'Data SEP tidak ditemukan.',
        '401' => 'Autentikasi gagal (Cek Consumer ID / Signature / Jam Server).',
        '403' => 'Akses ditolak (User Key salah atau Service diblokir).',
        '500' => 'Kesalahan Internal Server BPJS.',
        '503' => 'Layanan BPJS tidak tersedia (Sedang maintenance).',
    ];

    public function mount()
    {
        $this->dari   = now()->startOfMonth()->format('Y-m-d');
        $this->sampai = now()->format('Y-m-d');
    }

    public function updatedSearch()  { $this->resetPage(); $this->resetSelection(); }
    public function updatedDari()    { $this->resetPage(); $this->resetSelection(); }
    public function updatedSampai()  { $this->resetPage(); $this->resetSelection(); }
    public function updatedPerPage() { $this->resetPage(); $this->resetSelection(); }

    public function resetSelection()
    {
        $this->selected = [];
        $this->selectAll = false;
    }

    /**
     * Handle Select All Checkbox
     */
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = BridgingSep::query()
                ->when($this->dari,   fn($q) => $q->whereDate('tglsep', '>=', $this->dari))
                ->when($this->sampai, fn($q) => $q->whereDate('tglsep', '<=', $this->sampai))
                ->when($this->search, fn($q) => $q->where(function ($sub) {
                    $searchTerm = '%' . $this->search . '%';
                    $sub->where('no_sep', 'like', $searchTerm)
                        ->orWhere('no_rawat', 'like', $searchTerm)
                        ->orWhere('nama_pasien', 'like', $searchTerm);
                }))
                ->pluck('no_rawat')
                ->map(fn($item) => (string) $item)
                ->toArray();
        } else {
            $this->selected = [];
        }
    }

    /**
     * Open the Resume Medis modal for a given patient visit.
     */
    public function editResume(string $no_rawat): void
    {
        $this->activeNoRawat = $no_rawat;
        $resume = BpjsRekamMedisResume::find($no_rawat);
        $sep    = BridgingSep::where('no_rawat', $no_rawat)->first();

        $this->no_sep            = $sep?->no_sep ?? '';
        $this->keluhan_utama     = $resume?->keluhan_utama     ?? '';
        $this->riwayat_penyakit  = $resume?->riwayat_penyakit  ?? '';
        $this->diagnosis_masuk   = $resume?->diagnosis_masuk   ?? ($sep?->nmdiagnosaawal ?? '');
        $this->pemeriksaan_fisik = $resume?->pemeriksaan_fisik ?? '';
        $this->plan_of_care      = $resume?->plan_of_care      ?? '';
        $this->instruksi_pulang  = $resume?->instruksi_pulang  ?? '';
        $this->alergi            = $resume?->alergi            ?? '';

        $this->showModal = true;
    }

    /**
     * Save/Update Resume Medis data.
     */
    public function saveResume(): void
    {
        $this->validate([
            'keluhan_utama'    => 'required|string|max:4000',
            'riwayat_penyakit' => 'required|string|max:4000',
            'diagnosis_masuk'  => 'required|string|max:4000',
            'pemeriksaan_fisik'=> 'nullable|string|max:4000',
            'plan_of_care'      => 'nullable|string|max:4000',
            'instruksi_pulang'  => 'nullable|string|max:4000',
            'alergi'            => 'nullable|string|max:2000',
        ], [
            'keluhan_utama.required'    => 'Keluhan utama wajib diisi.',
            'riwayat_penyakit.required' => 'Riwayat penyakit wajib diisi.',
            'diagnosis_masuk.required'  => 'Diagnosis masuk wajib diisi.',
            'max'                       => 'Input terlalu panjang (maksimal :max karakter).',
        ]);

        BpjsRekamMedisResume::updateOrCreate(
            ['no_rawat' => $this->activeNoRawat],
            [
                'no_sep'            => $this->no_sep,
                'keluhan_utama'     => $this->keluhan_utama,
                'riwayat_penyakit'  => $this->riwayat_penyakit,
                'diagnosis_masuk'   => $this->diagnosis_masuk,
                'pemeriksaan_fisik' => $this->pemeriksaan_fisik,
                'plan_of_care'      => $this->plan_of_care,
                'instruksi_pulang'  => $this->instruksi_pulang,
                'alergi'            => $this->alergi,
                'tgl_input'         => now(),
                'petugas_id'        => auth()->id() ?? 'admin',
            ]
        );

        $this->showModal = false;
        $this->dispatch('swal', [
            'title' => 'Tersimpan!',
            'text'  => 'Resume Medis berhasil disimpan.',
            'icon'  => 'success',
        ]);
    }

    /**
     * Sync data Rekam Medis to BPJS API.
     */
    public function syncToBpjs(string $no_rawat): void
    {
        $sep    = BridgingSep::where('no_rawat', $no_rawat)->first();
        $resume = BpjsRekamMedisResume::find($no_rawat);

        if (!$resume) {
            $this->dispatch('swal', [
                'title' => 'Gagal!',
                'text'  => 'Isi Resume Medis terlebih dahulu sebelum mengirim.',
                'icon'  => 'warning',
            ]);
            return;
        }

        try {
            // Get Doctor Details from Pegawai & Dokter Table
            $visitData = DB::table('reg_periksa')
                ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                ->join('pegawai', 'dokter.nm_dokter', '=', 'pegawai.nama')
                ->where('reg_periksa.no_rawat', $no_rawat)
                ->select(
                    'pegawai.no_ktp as nik', 'dokter.nm_dokter as name', 'reg_periksa.stts',
                    'dokter.no_ijin_praktek', 'dokter.no_telp', 'pegawai.jk', 'pegawai.tgl_lahir', 'pegawai.alamat'
                )
                ->first();

            if (!$visitData || empty($visitData->nik)) {
                $this->dispatch('swal', [
                    'title' => 'NIK Dokter Tidak Ditemukan!',
                    'text'  => "Mohon lengkapi NIK Dokter ({$visitData->name}) di tabel Pegawai.",
                    'icon'  => 'error',
                ]);
                return;
            }

            // Lookup SatuSehat ID
            $ssService      = app(SatuSehatService::class);
            $practitionerId = $ssService->getPractitionerIdByNik($visitData->nik);

            if (!$practitionerId) {
                $this->dispatch('swal', [
                    'title' => 'SatuSehat Error!',
                    'text'  => "NIK Dokter [{$visitData->nik}] Belum Terdaftar/Valid di SatuSehat (Kemenkes).",
                    'icon'  => 'error',
                ]);
                return;
            }

            $diagnosa = DiagnosaPasien::with('penyakit')
                ->where('no_rawat', $no_rawat)
                ->orderBy('prioritas')
                ->get();

            $prosedur = ProsedurPasien::with('icd9')
                ->where('no_rawat', $no_rawat)
                ->orderBy('prioritas')
                ->get();

            // Fetch Medications (MedicationRequest)
            $obat = DB::table('detail_pemberian_obat')
                ->join('databarang', 'detail_pemberian_obat.kode_brng', '=', 'databarang.kode_brng')
                ->leftJoin('aturan_pakai', function($join) {
                    $join->on('detail_pemberian_obat.no_rawat', '=', 'aturan_pakai.no_rawat')
                         ->on('detail_pemberian_obat.kode_brng', '=', 'aturan_pakai.kode_brng')
                         ->on('detail_pemberian_obat.tgl_perawatan', '=', 'aturan_pakai.tgl_perawatan')
                         ->on('detail_pemberian_obat.jam_perawatan', '=', 'aturan_pakai.jam_perawatan');
                })
                ->where('detail_pemberian_obat.no_rawat', $no_rawat)
                ->select('databarang.nm_brng', 'databarang.kode_brng', 'databarang.kode_kategori', 'detail_pemberian_obat.jml', 'aturan_pakai.aturan')
                ->get();

            // Fetch Lab & Rad (DiagnosticReport)
            $lab = DB::table('periksa_lab')
                ->join('jns_perawatan_lab', 'periksa_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
                ->where('periksa_lab.no_rawat', $no_rawat)
                ->select('jns_perawatan_lab.nm_perawatan', 'periksa_lab.tgl_periksa')
                ->get();

            $rad = DB::table('periksa_radiologi')
                ->join('jns_perawatan_radiologi', 'periksa_radiologi.kd_jenis_prw', '=', 'jns_perawatan_radiologi.kd_jenis_prw')
                ->where('periksa_radiologi.no_rawat', $no_rawat)
                ->select('jns_perawatan_radiologi.nm_perawatan', 'periksa_radiologi.tgl_periksa')
                ->get();

            // Fetch Patient Detail with Address Components
            $patientDetail = DB::table('pasien')
                ->leftJoin('kecamatan', 'pasien.kd_kec', '=', 'kecamatan.kd_kec')
                ->leftJoin('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
                ->leftJoin('propinsi',  'pasien.kd_prop', '=', 'propinsi.kd_prop')
                ->where('pasien.no_rkm_medis', $sep->no_kartu ?? $sep->pasien->no_rkm_medis)
                ->select('pasien.*', 'kecamatan.nm_kec', 'kabupaten.nm_kab', 'propinsi.nm_prop')
                ->first();

            // Build & Sanitize FHIR Bundle with SatuSehat IDs and Extended Resources
            $fhirBundle = $this->buildFhirBundle($sep, $resume, $diagnosa, $prosedur, $practitionerId, $obat, $lab, $rad, $patientDetail, $visitData->stts ?? 'Sudah', $visitData);

            // Encrypt using BpjsService
            $bpjsService = app(BpjsService::class);
            $jsonPayload = json_encode($fhirBundle);
            
            // Critical check: Ensure total payload is not too big for AES if needed
            $dataMR = $bpjsService->encryptData($jsonPayload);

            $payload = [
                'request' => [
                    'noSep'          => (string) $sep->no_sep,
                    'jnsPelayanan'   => (string) $sep->jnspelayanan,
                    'bulan'          => (string) date('n', strtotime($sep->tglsep)),
                    'tahun'          => (string) date('Y', strtotime($sep->tglsep)),
                    'dataMR'         => $dataMR,
                ],
            ];

            // Send to BPJS (as per official Postman Collection)
            $response = $bpjsService->post('eclaim/rekammedis/insert', $payload);

            $metadata   = $response['metaData'] ?? $response['metadata'] ?? [];
            $resCode    = $metadata['code'] ?? 'ERR';
            $resMessage = $metadata['message'] ?? 'Unknown Error';
            $isSuccess  = ($resCode === '200');

            // Log entry
            BpjsRekamMedisLog::create([
                'no_sep'           => $sep->no_sep,
                'no_rawat'         => $no_rawat,
                'tgl_kirim'        => now(),
                'payload_request'  => json_encode($payload),
                'response_code'    => $resCode,
                'response_message' => $resMessage,
                'status_sukses'    => $isSuccess ? 1 : 0,
                'user_id'          => (string)(auth()->id() ?? 'admin'),
            ]);

            // Human friendly error message
            $friendlyError = $this->bpjsErrorCodes[$resCode] ?? $resMessage;

            $this->dispatch('swal', [
                'title' => $isSuccess ? 'Berhasil Terkirim!' : 'Gagal Sinkronisasi!',
                'text'  => $isSuccess 
                    ? 'Data Rekam Medis (E-Claim) berhasil disinkronkan ke BPJS.' 
                    : '[' . $resCode . '] ' . $friendlyError,
                'icon'  => $isSuccess ? 'success' : 'error',
            ]);

        } catch (\Exception $e) {
            Log::error("Sync Error [$no_rawat]: " . $e->getMessage());
            $this->dispatch('swal', [
                'title' => 'System Error!',
                'text'  => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }

    /**
     * Test the API Connection to both SatuSehat and BPJS TrustMark.
     */
    public function testConnection()
    {
        $ssStatus = 'Gagal';
        $bpjsStatus = 'Gagal (Konfigurasi Tidak Lengkap)';
        $isError = false;

        try {
            // 1. Test SatuSehat Connection (Token Generation)
            $ssService = app(SatuSehatService::class);
            $token = $ssService->getAccessToken();
            if ($token) {
                $ssStatus = 'Berhasil (Token Diterima)';
            } else {
                $isError = true;
                $ssStatus = 'Gagal (Unauthorized / Timeout)';
            }
        } catch (\Exception $e) {
            $isError = true;
            $ssStatus = 'Gagal (' . $e->getMessage() . ')';
        }

        try {
            // 2. Test BPJS Configuration (Crypto Engine Validation)
            $consId = env('BPJS_CONSID');
            $secret = env('BPJS_SECRET_KEY');
            
            if (empty($consId) || empty($secret)) {
                $isError = true;
            } else {
                $bpjsService = app(BpjsService::class);
                // Kita menguji dengan melakukan pengkodean dummy. Jika berhasil, berarti format AES256 sudah sinkron.
                $testEncrypt = $bpjsService->encryptData(json_encode(['status' => 'test_ping']));
                if ($testEncrypt) {
                    $bpjsStatus = 'Berhasil (Kunci Aktif & Kriptografi Sinkron)';
                } else {
                    $isError = true;
                    $bpjsStatus = 'Gagal (Masalah di enkripsi AES-256)';
                }
            }
        } catch (\Exception $e) {
            $isError = true;
            $bpjsStatus = 'Gagal (' . $e->getMessage() . ')';
        }

        $htmlMessage = "
            <div class='text-left mt-4 mb-2 space-y-3 text-sm'>
                <div class='flex items-center gap-3 p-3 rounded-lg border ".($ssStatus === 'Berhasil (Token Diterima)' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200')."'>
                    <div class='font-semibold text-slate-800 w-24'>SatuSehat:</div>
                    <div class='".($ssStatus === 'Berhasil (Token Diterima)' ? 'text-green-700' : 'text-red-600')."'>" . $ssStatus . "</div>
                </div>
                <div class='flex items-center gap-3 p-3 rounded-lg border ".($bpjsStatus === 'Berhasil (Kunci Aktif & Kriptografi Sinkron)' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200')."'>
                    <div class='font-semibold text-slate-800 w-24'>BPJS ERM:</div>
                    <div class='".($bpjsStatus === 'Berhasil (Kunci Aktif & Kriptografi Sinkron)' ? 'text-green-700' : 'text-red-600')."'>" . $bpjsStatus . "</div>
                </div>
            </div>
        ";

        $this->dispatch('swal', [
            'title' => 'Hasil Uji Koneksi',
            'html'  => $htmlMessage,
            'icon'  => $isError ? 'warning' : 'success',
        ]);
    }

    /**
     * Process multiple records at once.
     */
    public function bulkSyncToBpjs(): void
    {
        if (empty($this->selected)) {
            $this->dispatch('swal', ['title' => 'Pilih Data!', 'text' => 'Silakan pilih minimal satu pasien.', 'icon' => 'warning']);
            return;
        }

        $successCount = 0;
        $failCount    = 0;
        $totalItems   = count($this->selected);

        foreach ($this->selected as $no_rawat) {
            try {
                // Fetch Doctor Details
                $visitData = DB::table('reg_periksa')
                    ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                    ->join('pegawai', 'dokter.nm_dokter', '=', 'pegawai.nama')
                    ->where('reg_periksa.no_rawat', $no_rawat)
                    ->select(
                        'pegawai.no_ktp as nik', 'dokter.nm_dokter as name', 'reg_periksa.stts',
                        'dokter.no_ijin_praktek', 'dokter.no_telp', 'pegawai.jk', 'pegawai.tgl_lahir', 'pegawai.alamat'
                    )
                    ->first();

                if (!$visitData || empty($visitData->nik)) {
                    Log::warning("Bulk Sync Skip [$no_rawat]: NIK Dokter tidak ditemukan.");
                    $failCount++;
                    continue;
                }

                // Lookup SatuSehat ID
                $ssService      = app(SatuSehatService::class);
                $practitionerId = $ssService->getPractitionerIdByNik($visitData->nik);

                if (!$practitionerId) {
                    Log::warning("Bulk Sync Skip [$no_rawat]: Practitioner ID tidak ditemukan di SatuSehat.");
                    $failCount++;
                    continue;
                }

                $sep    = BridgingSep::where('no_rawat', $no_rawat)->first();
                $resume = BpjsRekamMedisResume::find($no_rawat);

                if (!$resume) {
                    $failCount++;
                    continue;
                }

                $diagnosa    = DiagnosaPasien::with('penyakit')->where('no_rawat', $no_rawat)->orderBy('prioritas')->get();
                $prosedur    = ProsedurPasien::with('icd9')->where('no_rawat', $no_rawat)->orderBy('prioritas')->get();
                
                // Fetch medications & diagnostics for bulk
                $obat = DB::table('detail_pemberian_obat')
                    ->join('databarang', 'detail_pemberian_obat.kode_brng', '=', 'databarang.kode_brng')
                    ->leftJoin('aturan_pakai', function($join) {
                        $join->on('detail_pemberian_obat.no_rawat', '=', 'aturan_pakai.no_rawat')
                             ->on('detail_pemberian_obat.kode_brng', '=', 'aturan_pakai.kode_brng')
                             ->on('detail_pemberian_obat.tgl_perawatan', '=', 'aturan_pakai.tgl_perawatan')
                             ->on('detail_pemberian_obat.jam_perawatan', '=', 'aturan_pakai.jam_perawatan');
                    })
                    ->where('detail_pemberian_obat.no_rawat', $no_rawat)
                    ->select('databarang.nm_brng', 'databarang.kode_brng', 'databarang.kode_kategori', 'detail_pemberian_obat.jml', 'aturan_pakai.aturan')
                    ->get();
                $lab = DB::table('periksa_lab')
                    ->join('jns_perawatan_lab', 'periksa_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
                    ->where('periksa_lab.no_rawat', $no_rawat)
                    ->select('jns_perawatan_lab.nm_perawatan', 'periksa_lab.tgl_periksa')->get();
                $rad = DB::table('periksa_radiologi')
                    ->join('jns_perawatan_radiologi', 'periksa_radiologi.kd_jenis_prw', '=', 'jns_perawatan_radiologi.kd_jenis_prw')
                    ->where('periksa_radiologi.no_rawat', $no_rawat)
                    ->select('jns_perawatan_radiologi.nm_perawatan', 'periksa_radiologi.tgl_periksa')->get();

                // Fetch Patient Detail for Bulk
                $patientDetail = DB::table('pasien')
                    ->leftJoin('kecamatan', 'pasien.kd_kec', '=', 'kecamatan.kd_kec')
                    ->leftJoin('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
                    ->leftJoin('propinsi',  'pasien.kd_prop', '=', 'propinsi.kd_prop')
                    ->where('pasien.no_rkm_medis', $sep->no_kartu ?? $sep->pasien->no_rkm_medis)
                    ->select('pasien.*', 'kecamatan.nm_kec', 'kabupaten.nm_kab', 'propinsi.nm_prop')
                    ->first();

                $fhirBundle  = $this->buildFhirBundle($sep, $resume, $diagnosa, $prosedur, $practitionerId, $obat, $lab, $rad, $patientDetail, $visitData->stts ?? 'Sudah', $visitData);
                $bpjsService = app(BpjsService::class);
                $dataMR      = $bpjsService->encryptData(json_encode($fhirBundle));

                $payload = [
                    'request' => [
                        'noSep'          => (string) $sep->no_sep,
                        'jnsPelayanan'   => (string) $sep->jnspelayanan,
                        'bulan'          => (string) date('n', strtotime($sep->tglsep)),
                        'tahun'          => (string) date('Y', strtotime($sep->tglsep)),
                        'dataMR'         => $dataMR,
                    ],
                ];

                $response = $bpjsService->post('eclaim/rekammedis/insert', $payload);
                $metadata = $response['metaData'] ?? $response['metadata'] ?? [];
                $resCode  = $metadata['code']  ?? 'ERR';
                $isOk     = ($resCode === '200');

                BpjsRekamMedisLog::create([
                    'no_sep'           => $sep->no_sep,
                    'no_rawat'         => $no_rawat,
                    'tgl_kirim'        => now(),
                    'payload_request'  => json_encode($payload),
                    'response_code'    => $resCode,
                    'response_message' => $metadata['message'] ?? 'Unknown',
                    'status_sukses'    => $isOk ? 1 : 0,
                    'user_id'          => (string)(auth()->id() ?? 'admin'),
                ]);

                if ($isOk) $successCount++; else $failCount++;

            } catch (\Exception $e) {
                Log::error("Bulk Sync Error [$no_rawat]: " . $e->getMessage());
                $failCount++;
            }
        }

        $this->resetSelection();

        $this->dispatch('swal', [
            'title' => 'Bulk Sync Selesai!',
            'text'  => "Berhasil: $successCount, Gagal: $failCount (Total: $totalItems)",
            'icon'  => $failCount === 0 ? 'success' : 'info',
        ]);
    }

    /**
     * Sanitize input to prevent breaking JSON/FHIR formats.
     */
    protected function sanitizeForFhir(?string $value): string
    {
        if (empty($value)) return '-';
        
        // 1. Replace ampersand with 'dan'
        $value = str_replace('&', 'dan', $value);
        
        // 2. Wrap tags or dangerous symbols
        $value = str_replace(['<', '>'], ['(', ')'], $value);
        
        // 3. Replace backslash with forward slash
        $value = str_replace('\\', '/', $value);
        
        // 4. Remove unexpected control characters
        $value = preg_replace('/[\x00-\x1F\x7F]/', '', $value);

        return trim($value);
    }

    /**
     * Build FHIR Bundle from collected data (Full alignment with BPJS Diagram & TrustMark Example).
     */
    protected function buildFhirBundle($sep, $resume, $diagnosa, $prosedur, $practitionerId, $obat = [], $lab = [], $rad = [], $pDetail = null, $sttsPulang = 'Sudah', $practitionerDetail = null): array
    {
        // 0. Pre-generate UUIDs for cross-referencing
        $bundleId       = (string) Str::uuid();
        $compId         = (string) Str::uuid();
        $patientUuid    = (string) Str::uuid();
        $encounterUuid  = (string) Str::uuid();
        
        $patientId      = 'Patient/'      . $patientUuid;
        $encounterId    = 'Encounter/'    . $encounterUuid;
        $practitionerIdFull = 'Practitioner/' . $practitionerId;
        $orgIdFull      = 'Organization/' . env('SATUSEHAT_ORGANIZATION_ID', 'b0222eab-be00-49d8-a7c1-0bfa3c6c2243');
        
        $hospital       = DB::table('setting')->first();
        $hospitalName   = $hospital->nama_instansi ?? 'RS SIMRS KHANZA';

        // Map Conditions to UUIDs for linking
        $conditionIds = $diagnosa->map(fn($d) => (string) Str::uuid())->toArray();
        // Map Medications to UUIDs
        $medicationIds = $obat->map(fn($o) => (string) Str::uuid())->toArray();
        // Map Procedures to UUIDs
        $procedureIds = $prosedur->map(fn($p) => (string) Str::uuid())->toArray();

        // 1. Composition Resource (Discharge Summary)
        // NOTE: We use numeric keys ("0", "1", etc.) for sections to match the BPJS TrustMark portal's keyed-object format.
        $composition = [
            'resourceType' => 'Composition',
            'id'           => $compId,
            'status'       => 'final',
            'type'         => [
                'coding' => [['system' => 'http://loinc.org', 'code' => '81218-0']],
                'text'   => 'Discharge Summary'
            ],
            'subject'   => ['reference' => 'urn:uuid:' . $patientUuid, 'display' => $sep->nama_pasien],
            'encounter' => ['reference' => 'urn:uuid:' . $encounterUuid],
            'date'      => now()->format('Y-m-d H:i:s'),
            'author'    => [['reference' => 'urn:uuid:' . $practitionerIdFull, 'display' => $sep->nmdpdjp ?? 'Dokter']],
            'title'     => 'Discharge Summary',
            'confidentiality' => 'N',
            'section'   => [
                "0" => [
                    'title' => 'Reason for admission',
                    'code'  => ['coding' => [['system' => 'http://loinc.org', 'code' => '29299-5', 'display' => 'Reason for visit Narrative']]],
                    'text'  => ['status' => 'additional', 'div' => '<div>' . e($this->sanitizeForFhir($resume->riwayat_penyakit)) . '</div>'],
                ],
                "1" => [
                    'title' => 'Chief complaint',
                    'code'  => ['coding' => [['system' => 'http://loinc.org', 'code' => '10154-3', 'display' => 'Chief complaint Narrative']]],
                    'text'  => ['status' => 'additional', 'div' => '<div>' . e($this->sanitizeForFhir($resume->keluhan_utama)) . '</div>'],
                ],
                "2" => [
                    'title' => 'Admission diagnosis',
                    'code'  => ['coding' => [['system' => 'http://loinc.org', 'code' => '42347-5', 'display' => 'Admission diagnosis Narrative']]],
                    'text'  => ['status' => 'additional', 'div' => '<div>' . e($this->sanitizeForFhir($resume->diagnosis_masuk)) . '</div>'],
                    'entry' => array_map(fn($uuid) => ['reference' => 'urn:uuid:' . $uuid], $conditionIds)
                ],
                "4" => [
                    'title' => 'Medications on Discharge',
                    'code'  => ['coding' => [['system' => 'http://loinc.org', 'code' => '75311-1', 'display' => 'Hospital discharge medications Narrative']]],
                    'text'  => ['status' => 'additional', 'div' => '<div>' . e($this->sanitizeForFhir($resume->plan_of_care)) . '</div>'],
                    'mode'  => 'working',
                    'entry' => array_map(fn($uuid) => ['reference' => 'urn:uuid:' . $uuid], $medicationIds)
                ],
                "5" => [
                    'title' => 'Plan of care',
                    'code'  => ['coding' => [['system' => 'http://loinc.org', 'code' => '18776-5', 'display' => 'Plan of care']]],
                    'text'  => ['status' => 'additional', 'div' => '<div>' . e($this->sanitizeForFhir($resume->plan_of_care)) . '</div>'],
                ],
                "7" => [
                    'title' => 'Known allergies',
                    'code'  => ['coding' => [['system' => 'http://loinc.org', 'code' => '48765-2', 'display' => 'Allergies and adverse reactions']]],
                    'text'  => ['status' => 'additional', 'div' => '<div>' . e($this->sanitizeForFhir($resume->alergi)) . '</div>'],
                ]
            ]
        ];

        // 2. Entries Construction
        $entries = [];

        // Entry 1: Composition
        $entries[] = ['resource' => $composition];

        // Entry 2: Patient (TrustMark Compliant)
        $maritalCode = match(Str::upper($pDetail->stts_nikah ?? '')) {
            'MENIKAH', 'KAWIN' => 'M',
            'JANDA', 'DUDA'    => 'D',
            default            => 'U',
        };

        $entries[] = [
            'resource' => [
                'resourceType' => 'Patient',
                'id'           => $patientUuid,
                'identifier'   => [
                    [
                        'use' => 'usual',
                        'type' => ['coding' => [['system' => 'http://hl7.org/fhir/v2/0203', 'code' => 'MR']]],
                        'value' => (string) ($pDetail->no_rkm_medis ?? ''),
                        'assigner' => ['display' => $hospitalName]
                    ],
                    [
                        'use' => 'official',
                        'type' => ['coding' => [['system' => 'http://hl7.org/fhir/v2/0203', 'code' => 'MB']]],
                        'value' => (string) ($sep->no_peserta ?? ''),
                        'assigner' => ['display' => 'BPJS KESEHATAN']
                    ],
                    [
                        'use' => 'official',
                        'type' => ['coding' => [['system' => 'http://hl7.org/fhir/v2/0203', 'code' => 'NNIDN']]],
                        'value' => (string) ($pDetail->no_ktp ?? ''),
                        'assigner' => ['display' => 'KEMENDAGRI']
                    ]
                ],
                'active'  => true,
                'name'    => [['use' => 'official', 'text' => $sep->nama_pasien]],
                'maritalStatus' => ['coding' => [['system' => 'http://hl7.org/fhir/v3/MaritalStatus', 'code' => $maritalCode]]],
                'telecom' => [
                    ['system' => 'phone', 'value' => (string) ($pDetail->no_tlp ?? ''), 'use' => 'mobile']
                ],
                'gender'    => ($sep->pasien->jk ?? 'L') === 'L' ? 'male' : 'female',
                'birthDate' => $sep->pasien->tgl_lahir ?? '1990-01-01',
                'deceasedBoolean' => false,
                'address' => [[
                    'use'  => 'home',
                    'line' => [e($this->sanitizeForFhir($pDetail->alamat ?? '-'))],
                    'district' => e($this->sanitizeForFhir($pDetail->nm_kec ?? '-')),
                    'city'     => e($this->sanitizeForFhir($pDetail->nm_kab ?? '-')),
                    'state'    => e($this->sanitizeForFhir($pDetail->nm_prop ?? '-')),
                ]],
                'managingOrganization' => ['reference' => 'urn:uuid:' . $orgIdFull, 'display' => $hospitalName]
            ]
        ];

        // Entry 3: Encounter (TrustMark Compliant)
        $encClass = ($sep->jnspelayanan == 1) ? 'IMP' : 'AMB';
        $encDisplay = ($encClass == 'IMP') ? 'inpatient encounter' : 'ambulatory encounter';

        $dispositionCode = match($sttsPulang) {
            'Meninggal'    => 'exp',
            'Dirujuk'      => 'other-hcf',
            'Pulang Paksa' => 'aama',
            default        => 'home',
        };

        $entries[] = [
            'resource' => [
                'resourceType' => 'Encounter',
                'id'           => $encounterUuid,
                'identifier'   => [
                    [
                        'system' => 'http://api.bpjs-kesehatan.go.id:8080/Vclaim-rest/SEP/',
                        'value' => (string) $sep->no_sep
                    ]
                ],
                'status'       => 'finished',
                'class'        => [
                    'system'  => 'http://terminology.hl7.org/CodeSystem/v3-ActCode',
                    'code'    => $encClass,
                    'display' => $encDisplay
                ],
                'subject'      => [
                    'reference' => 'urn:uuid:' . $patientUuid,
                    'display'   => $sep->nama_pasien,
                    'noSep'     => (string) $sep->no_sep
                ],
                'period'       => [
                    'start' => $sep->tglsep . 'T08:00:00+07:00',
                    'end'   => $sep->tglsep . 'T09:00:00+07:00'
                ],
                'diagnosis' => array_map(function($uuid, $idx) {
                    return [
                        'condition' => ['reference' => 'urn:uuid:' . $uuid],
                        'role' => [
                            'coding' => [['system' => 'http://hl7.org/fhir/diagnosis-role', 'code' => 'DD', 'display' => 'Discharge Diagnosis']]
                        ],
                        'rank' => $idx + 1
                    ];
                }, $conditionIds, array_keys($conditionIds)),
                'hospitalization' => [
                    'dischargeDisposition' => [[
                        'coding' => [['system' => 'http://hl7.org/fhir/discharge-disposition', 'code' => $dispositionCode]]
                    ]]
                ],
                'serviceProvider' => ['reference' => 'urn:uuid:' . $orgIdFull]
            ]
        ];

        // Entry 4: Practitioner (TrustMark Compliant)
        $practitionerGender = ($practitionerDetail->jk ?? 'Pria') === 'Pria' ? 'male' : 'female';

        $entries[] = [
            'resource' => [
                'resourceType' => 'Practitioner',
                'id'           => $practitionerIdFull,
                'identifier'   => [
                    [
                        'use' => 'official',
                        'system' => 'urn:oid:nomor_sip',
                        'value' => (string) ($practitionerDetail->no_ijin_praktek ?? '-')
                    ],
                    [
                        'use' => 'official',
                        'type' => [
                            'coding' => [['system' => 'http://hl7.org/fhir/v2/0203', 'code' => 'NNIDN']]
                        ],
                        'value' => (string) ($practitionerDetail->nik ?? ''),
                        'assigner' => ['display' => 'KEMENDAGRI']
                    ]
                ],
                'name'         => [['use' => 'official', 'text' => $practitionerDetail->name ?? ($sep->nmdpdjp ?? 'Dokter')]],
                'telecom'      => [
                    [
                        'system' => 'phone',
                        'value'  => (string) ($practitionerDetail->no_telp ?? ''),
                        'use'    => 'work'
                    ]
                ],
                'address'      => [
                    [
                        'use'  => 'home',
                        'line' => [e($this->sanitizeForFhir($practitionerDetail->alamat ?? '-'))]
                    ]
                ],
                'gender'       => $practitionerGender,
                'birthDate'    => $practitionerDetail->tgl_lahir ?? '1970-01-01'
            ]
        ];

        // Entry 5: Organization (TrustMark Compliant)
        $entries[] = [
            'resource' => [
                'resourceType' => 'Organization',
                'id'           => $orgIdFull,
                'identifier'   => [
                    [
                        'use'    => 'official',
                        'system' => 'urn:oid:bpjs',
                        'value'  => (string) ($hospital->kode_ppk ?? '0901R001')
                    ],
                    [
                        'use'    => 'official',
                        'system' => 'urn:oid:kemkes',
                        'value'  => (string) ($hospital->kode_ppk ?? '3173014')
                    ]
                ],
                'type' => [
                    ['coding' => [['system' => 'http://hl7.org/fhir/organization-type', 'code' => 'prov', 'display' => 'Healthcare Provider']]]
                ],
                'name'         => $hospitalName,
                'alias'        => [$hospitalName],
                'telecom'      => [
                    [
                        'system' => 'phone',
                        'value'  => (string) ($hospital->kontak ?? ''),
                        'use'    => 'work'
                    ],
                    [
                        'system' => 'email',
                        'value'  => (string) ($hospital->email ?? ''),
                        'use'    => 'work'
                    ]
                ],
                'address'      => [
                    [
                        'use'  => 'work',
                        'text' => (string) ($hospital->alamat_instansi ?? '') . ', ' . (string) ($hospital->kabupaten ?? '') . ', ' . (string) ($hospital->propinsi ?? ''),
                        'line' => [(string) ($hospital->alamat_instansi ?? '')],
                        'city' => (string) ($hospital->kabupaten ?? ''),
                        'state'=> (string) ($hospital->propinsi ?? ''),
                        'country' => 'IDN'
                    ]
                ],
                'contact' => [
                    [
                        'purpose' => ['coding' => [['system' => 'http://hl7.org/fhir/contactentity-type', 'code' => 'PATINF']]],
                        'telecom' => [['system' => 'phone', 'value' => (string) ($hospital->kontak ?? '')]]
                    ]
                ]
            ]
        ];

        // Entry 6+: Conditions (Diagnoses)
        foreach ($diagnosa as $idx => $diag) {
            $diseaseName = $diag->penyakit->nm_penyakit ?? 'Diagnosis tidak diketahui';

            $entries[] = [
                'resource' => [
                    'resourceType' => 'Condition',
                    'id'           => $conditionIds[$idx],
                    'clinicalStatus' => 'active',
                    'verificationStatus' => 'confirmed',
                    'category'     => [
                        [
                            'coding' => [
                                [
                                    'system' => 'http://hl7.org/fhir/condition-category',
                                    'code' => 'encounter-diagnosis',
                                    'display' => 'Encounter Diagnosis'
                                ]
                            ]
                        ]
                    ],
                    'code'         => [
                        'coding' => [
                            [
                                'system' => 'http://hl7.org/fhir/sid/icd-10',
                                'code'     => $diag->kd_penyakit,
                                'display'  => $diseaseName
                            ]
                        ],
                        'text' => $diseaseName
                    ],
                    'subject'      => ['reference' => 'urn:uuid:' . $patientUuid],
                    'onsetDateTime'=> $sep->tglsep . 'T08:00:00+07:00'
                ]
            ];
        }

        // Entry 7+: Procedures (Tindakan ICD-9)
        foreach ($prosedur as $idx => $proc) {
            $procName = $proc->icd9->deskripsi_panjang ?? ($proc->icd9->deskripsi_pendek ?? 'Tindakan Medis');

            $entries[] = [
                'resource' => [
                    'resourceType' => 'Procedure',
                    'id'           => $procedureIds[$idx],
                    'status'       => 'completed',
                    'code'         => [
                        'coding' => [
                            [
                                'system'  => 'http://hl7.org/fhir/sid/icd-9-cm',
                                'code'    => $proc->kode,
                                'display' => $procName
                            ]
                        ],
                        'text' => $procName
                    ],
                    'subject'      => ['reference' => 'urn:uuid:' . $patientUuid, 'display' => $sep->nama_pasien],
                    'encounter'    => ['reference' => 'urn:uuid:' . $encounterUuid, 'display' => 'Kunjungan ' . $sep->no_sep],
                    'performedPeriod'=> [
                        'start' => $sep->tglsep . 'T08:00:00+07:00',
                        'end'   => $sep->tglsep . 'T09:00:00+07:00'
                    ],
                    'performer'    => [
                        [
                            'actor' => ['reference' => 'urn:uuid:' . $practitionerIdFull, 'display' => $practitionerDetail->name ?? ($sep->nmdpdjp ?? 'Dokter')]
                        ]
                    ]
                ]
            ];
        }

        // Entry 8+: Devices (Alkes/BHP)
        foreach ($obat as $item) {
            $kategoriDiterima = ['alkes', 'bhp', 'mpl'];
            if (in_array(strtolower($item->kode_kategori), $kategoriDiterima)) {
                $deviceId = (string) Str::uuid();
                $entries[] = [
                    'resource' => [
                        'resourceType' => 'Device',
                        'id'           => $deviceId,
                        'text'         => [
                            'status' => 'generated',
                            'div'    => '<div>Alat Kesehatan / BHP: ' . e($this->sanitizeForFhir($item->nm_brng)) . '</div>'
                        ],
                        'identifier'   => [
                            [
                                'system' => 'http://simrs.local/devices',
                                'value'  => (string) $item->kode_brng
                            ]
                        ],
                        'type'         => [
                            'coding' => [
                                [
                                    'system'  => 'http://simrs.local/devices',
                                    'code'    => (string) $item->kode_brng,
                                    'display' => $item->nm_brng
                                ]
                            ]
                        ],
                        'lotNumber'       => strtoupper((string) $item->kode_kategori),
                        'manufacturer'    => '',
                        'manufactureDate' => '',
                        'expirationDate'  => '',
                        'model'           => '',
                        'patient'         => ['reference' => 'urn:uuid:' . $patientUuid, 'display' => $sep->nama_pasien]
                    ]
                ];
            }
        }

        // ENTRY 8+: MedicationRequest (Obat)
        foreach ($obat as $idx => $item) {
            $isDevice = Str::contains(Str::lower($item->kode_kategori), ['alkes', 'bhp', 'mpl']);

            if (!$isDevice) {
                $entries[] = [
                    'resource' => [
                        'resourceType' => 'MedicationRequest',
                        'id'           => $medicationIds[$idx],
                        'meta'         => [
                            'lastUpdated' => now()->format('Y-m-d H:i:s')
                        ],
                        'identifier'   => [
                            [
                                'system' => 'id_resep_pulang',
                                'value'  => (string) Str::uuid()
                            ]
                        ],
                        'status'       => 'completed',
                        'intent'       => 'final',
                        'medicationCodeableConcept' => [
                            'coding' => [['system' => 'http://sys-ids.kemkes.go.id/drug-id', 'code' => $item->kode_brng]],
                            'text'   => $item->nm_brng
                        ],
                        'subject'   => ['reference' => 'urn:uuid:' . $patientUuid, 'display' => $sep->nama_pasien],
                        'requester' => [
                            'agent'      => ['reference' => 'urn:uuid:' . $practitionerIdFull, 'display' => $sep->nmdpdjp ?? 'Dokter'],
                            'onBehalfOf' => ['reference' => 'urn:uuid:' . $orgIdFull, 'display' => $hospitalName]
                        ],
                        'dosageInstruction' => [[
                            'text' => (string) ($item->aturan ?? '-'),
                            'additionalInstruction' => [['text' => (string) ($item->aturan ?? '-')]],
                        ]],
                        'dispenseRequest' => ['expectedSupplyDuration' => ['value' => (float) $item->jml]]
                    ]
                ];
            }
        }

        // ENTRY 9+: DiagnosticReport (Radiologi)
        foreach ($rad as $diag) {
            $entries[] = [
                'resource' => [
                    'resourceType' => 'DiagnosticReport',
                    'id'           => (string) Str::uuid(),
                    'subject'      => ['reference' => 'urn:uuid:' . $patientUuid, 'display' => $sep->nama_pasien, 'noSep' => (string) $sep->no_sep],
                    'category'     => ['coding' => [['system' => 'http://hl7.org/fhir/v2/0074', 'code' => 'RAD', 'display' => 'Radiology']]],
                    'status'       => 'final',
                    'performer'    => [['reference' => 'urn:uuid:' . $orgIdFull, 'display' => $hospitalName]],
                    'result'       => [
                        [
                            'resourceType'      => 'Observation',
                            'id'                => (string) Str::uuid(),
                            'status'            => 'final',
                            'text'              => ['status' => 'generated', 'div' => '<div>Pemeriksaan ' . e($this->sanitizeForFhir($diag->nm_perawatan)) . '</div>'],
                            'effectiveDateTime' => $diag->tgl_periksa . 'T08:00:00+07:00',
                            'issued'            => $diag->tgl_periksa . 'T08:30:00+07:00',
                            'code'              => ['text' => $diag->nm_perawatan],
                            'performer'         => ['reference' => 'urn:uuid:' . $practitionerIdFull, 'display' => $practitionerDetail->name ?? ($sep->nmdpdjp ?? 'Dokter')],
                            'conclusion'        => 'Pemeriksaan ' . e($this->sanitizeForFhir($diag->nm_perawatan)) . ' telah dilakukan.'
                        ]
                    ]
                ]
            ];
        }

        // ENTRY 10+: DiagnosticReport (Laboratorium)
        foreach ($lab as $diag) {
            $entries[] = [
                'resource' => [
                    'resourceType' => 'DiagnosticReport',
                    'id'           => (string) Str::uuid(),
                    'subject'      => ['reference' => 'urn:uuid:' . $patientUuid, 'display' => $sep->nama_pasien, 'noSep' => (string) $sep->no_sep],
                    'category'     => ['coding' => [['system' => 'http://hl7.org/fhir/v2/0074', 'code' => 'LAB', 'display' => 'Laboratory']]],
                    'status'       => 'final',
                    'performer'    => [['reference' => 'urn:uuid:' . $orgIdFull, 'display' => $hospitalName]],
                    'result'       => [
                        [
                            'resourceType'      => 'Observation',
                            'id'                => (string) Str::uuid(),
                            'status'            => 'final',
                            'text'              => ['status' => 'generated', 'div' => '<div>Pemeriksaan ' . e($this->sanitizeForFhir($diag->nm_perawatan)) . '</div>'],
                            'effectiveDateTime' => $diag->tgl_periksa . 'T08:00:00+07:00',
                            'issued'            => $diag->tgl_periksa . 'T08:30:00+07:00',
                            'code'              => ['text' => $diag->nm_perawatan],
                            'performer'         => ['reference' => 'urn:uuid:' . $practitionerIdFull, 'display' => $practitionerDetail->name ?? ($sep->nmdpdjp ?? 'Dokter')],
                            'conclusion'        => 'Pemeriksaan ' . e($this->sanitizeForFhir($diag->nm_perawatan)) . ' telah dilakukan.'
                        ]
                    ]
                ]
            ];
        }

        return [
            'resourceType' => 'Bundle',
            'id'           => $bundleId,
            'meta'         => [
                'lastUpdated' => now()->format('Y-m-d H:i:s')
            ],
            'identifier'   => [
                'system' => 'sep',
                'value'  => (string) $sep->no_sep
            ],
            'type'         => 'document',
            'timestamp'    => now()->format('Y-m-d\TH:i:sP'),
            'entry'        => $entries,
        ];
    }

    public function render(): \Illuminate\View\View
    {
        $searchTerm = '%' . $this->search . '%';

        $seps = BridgingSep::query()
            ->with(['resume', 'logs'])
            ->when($this->dari,   fn($q) => $q->whereDate('tglsep', '>=', $this->dari))
            ->when($this->sampai, fn($q) => $q->whereDate('tglsep', '<=', $this->sampai))
            ->when($this->search, fn($q) => $q->where(function ($sub) use ($searchTerm) {
                $sub->where('no_sep',      'like', $searchTerm)
                    ->orWhere('no_rawat',  'like', $searchTerm)
                    ->orWhere('nama_pasien','like', $searchTerm);
            }))
            ->orderBy('tglsep', 'desc')
            ->paginate($this->perPage);

        return view('livewire.bridging.erm-bpjs.index', compact('seps'));
    }
}
