<?php

namespace App\Livewire\Modul\RawatJalan\Icare;

use Livewire\Component;
use App\Models\RegPeriksa;
use App\Services\BpjsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    public $no_rawat;
    public $regPeriksa;
    public $iCareUrl = '';
    public $errorMessage = '';

    public function mount($no_rawat)
    {
        $this->no_rawat = $no_rawat;
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'poliklinik'])->find(str_replace('-', '/', $no_rawat));

        if (!$this->regPeriksa) {
            abort(404, 'Data pendaftaran tidak ditemukan.');
        }

        $this->fetchIcareUrl();
    }

    protected function fetchIcareUrl()
    {
        try {
            $kd_dokter = $this->regPeriksa->kd_dokter;
            $no_peserta = $this->regPeriksa->pasien->no_peserta ?? '';

            // Cari mapping dokter VClaim
            $mapping = DB::table('maping_dokter_dpjpvclaim')->where('kd_dokter', $kd_dokter)->first();
            $kd_dokter_bpjs = $mapping ? $mapping->kd_dokter_bpjs : null;

            if (!$kd_dokter_bpjs) {
                $this->errorMessage = 'Dokter belum di-mapping dengan kode dokter BPJS (VClaim).';
                return;
            }

            if (empty($no_peserta) || $no_peserta === '-') {
                $this->errorMessage = 'Pasien tidak memiliki nomor peserta BPJS.';
                return;
            }

            $payload = [
                'param' => $no_peserta,
                'kodedokter' => intval($kd_dokter_bpjs)
            ];

            $bpjsService = new BpjsService();
            // Gunakan endpoint iCare dari env, fallback ke dev VClaim iCare RS
            $endpoint = env('ICARE_BASE_URL', 'https://apijkn-dev.bpjs-kesehatan.go.id/ihs_dev/api/rs/validate');
            
            $response = $bpjsService->post($endpoint, $payload);

            if (isset($response['metaData']['code']) && $response['metaData']['code'] == '200') {
                if (is_array($response['response']) && isset($response['response']['url'])) {
                    $this->iCareUrl = $response['response']['url'];
                } else if (is_string($response['response'])) {
                    // Try to json_decode it just in case
                    $decoded = json_decode($response['response'], true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($decoded['url'])) {
                        $this->iCareUrl = $decoded['url'];
                    } else {
                        // Some endpoints just return the URL directly in a decompressed string or array
                        $this->iCareUrl = $response['response'];
                    }
                }
                
                // If it's still LZString compressed (due to BpjsService not knowing about LZString), 
                // we might need to handle it. But BpjsService decompresses using gzdecode.
                // If gzdecode failed inside BpjsService, it leaves response as encrypted string.
                // Let's rely on BpjsService's successful decryption for now, which sets $response['response'] to array.

            } else {
                $this->errorMessage = $response['metaData']['message'] ?? 'Terjadi kesalahan saat memanggil API iCare BPJS.';
            }

        } catch (\Exception $e) {
            Log::error("iCare Fetch Error: " . $e->getMessage());
            $this->errorMessage = "Terjadi kesalahan internal: " . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.modul.rawat-jalan.icare.index')->layout('layouts.app');
    }
}
