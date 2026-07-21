<?php

namespace App\Livewire\Modul\RawatJalan\Icare;

use Livewire\Component;
use App\Models\RegPeriksa;
use GuzzleHttp\Client;
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
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'poliklinik', 'penjab'])->find(str_replace('-', '/', $no_rawat));

        if (!$this->regPeriksa) {
            abort(404, 'Data pendaftaran tidak ditemukan.');
        }

        $this->fetchIcareUrl();
    }

    /**
     * Buat header autentikasi untuk API iCare BPJS.
     * Signature: HMAC-SHA256(consid + "&" + timestamp, secretKey) -> Base64
     */
    protected function buildHeaders(): array
    {
        $consid    = env('ICARE_CONSID');
        $secretKey = env('ICARE_SECRET_KEY');
        $userKey   = env('ICARE_USER_KEY');
        $timestamp = strval(time());

        $signature    = hash_hmac('sha256', $consid . '&' . $timestamp, $secretKey, true);
        $encodedSig   = base64_encode($signature);

        return [
            'X-cons-id'    => $consid,
            'X-timestamp'  => $timestamp,
            'X-signature'  => $encodedSig,
            'user_key'     => $userKey,
            'Content-Type' => 'application/json',
        ];
    }

    protected function fetchIcareUrl()
    {
        try {
            $kd_dokter  = $this->regPeriksa->kd_dokter;
            $no_peserta = $this->regPeriksa->pasien->no_peserta ?? '';

            // Validasi: pastikan pasien memiliki nomor peserta BPJS
            if (empty($no_peserta) || $no_peserta === '-') {
                $this->errorMessage = 'Pasien tidak memiliki nomor peserta BPJS.';
                return;
            }

            // Cari mapping kode dokter BPJS di tabel maping_dokter_dpjpvclaim
            $mapping        = DB::table('maping_dokter_dpjpvclaim')->where('kd_dokter', $kd_dokter)->first();
            $kd_dokter_bpjs = $mapping ? $mapping->kd_dokter_bpjs : null;

            if (!$kd_dokter_bpjs) {
                $this->errorMessage = 'Dokter (kode: ' . $kd_dokter . ') belum di-mapping dengan kode dokter BPJS. Silakan hubungi administrator.';
                return;
            }

            $baseUrl = env('ICARE_BASE_URL', 'https://apijkn.bpjs-kesehatan.go.id/wsihs/api/rs/validate');

            $client = new Client([
                'timeout' => 30.0,
                'verify'  => false,
            ]);

            $payload = json_encode([
                'param'       => $no_peserta,
                'kodedokter'  => intval($kd_dokter_bpjs),
            ]);

            $response    = $client->post($baseUrl, [
                'headers' => $this->buildHeaders(),
                'body'    => $payload,
            ]);

            $rawBody = $response->getBody()->getContents();
            Log::info('iCare API Raw Response: ' . $rawBody);

            $body = json_decode($rawBody, true);

            // iCare API: metaData.code == '200' berarti sukses
            $code = $body['metaData']['code'] ?? $body['metadata']['code'] ?? null;

            if ($code == '200') {
                // Respons iCare berupa URL langsung dalam field "response"
                $resp = $body['response'] ?? null;

                if (is_string($resp)) {
                    // Coba parse sebagai JSON
                    $parsed = json_decode($resp, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($parsed['url'])) {
                        $this->iCareUrl = $parsed['url'];
                    } else {
                        // Respons langsung berisi URL string
                        $this->iCareUrl = trim($resp, '"');
                    }
                } elseif (is_array($resp) && isset($resp['url'])) {
                    $this->iCareUrl = $resp['url'];
                } elseif (is_array($resp) && isset($resp['URL'])) {
                    $this->iCareUrl = $resp['URL'];
                } else {
                    $this->errorMessage = 'URL iCare tidak ditemukan dalam respons BPJS. Silakan hubungi administrator.';
                }
            } else {
                $message = $body['metaData']['message']
                    ?? $body['metadata']['message']
                    ?? 'Terjadi kesalahan saat memanggil API iCare BPJS.';
                $this->errorMessage = $message;
            }

        } catch (\Exception $e) {
            Log::error('iCare Fetch Error: ' . $e->getMessage());
            $this->errorMessage = 'Terjadi kesalahan koneksi: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.modul.rawat-jalan.icare.index')->layout('layouts.app');
    }
}
