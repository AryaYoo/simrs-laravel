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
     * Content-Type BPJS selalu 'text/plain'.
     */
    protected function buildHeaders(): array
    {
        $consid    = env('ICARE_CONSID');
        $secretKey = env('ICARE_SECRET_KEY');
        $userKey   = env('ICARE_USER_KEY');
        $timestamp = strval(time());

        $signature  = hash_hmac('sha256', $consid . '&' . $timestamp, $secretKey, true);
        $encodedSig = base64_encode($signature);

        return [
            'X-cons-id'   => $consid,
            'X-timestamp' => $timestamp,
            'X-signature' => $encodedSig,
            'user_key'    => $userKey,
            'Content-Type' => 'text/plain',
            'Accept'       => 'application/json',
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
                // Paksa TLS 1.2 – diperlukan untuk endpoint BPJS prod
                'curl'    => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                ],
            ]);

            $payload = json_encode([
                'param'      => $no_peserta,
                'kodedokter' => intval($kd_dokter_bpjs),
            ]);

            // BPJS iCare: POST, Content-Type text/plain, body JSON
            // Retry hingga 2x untuk mengatasi cURL error 56 (connection reset) yang intermittent
            $maxRetries  = 2;
            $lastException = null;
            $response      = null;

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    $response = $client->post($baseUrl, [
                        'headers' => $this->buildHeaders(),
                        'body'    => $payload,
                    ]);
                    $lastException = null;
                    break; // sukses, keluar dari loop
                } catch (\GuzzleHttp\Exception\ConnectException $e) {
                    $lastException = $e;
                    Log::warning("iCare percobaan ke-{$attempt} gagal: " . $e->getMessage());
                    if ($attempt < $maxRetries) {
                        sleep(1); // tunggu 1 detik sebelum retry
                    }
                }
            }

            // Jika semua percobaan gagal
            if ($lastException !== null) {
                throw $lastException;
            }

            $rawBody = $response->getBody()->getContents();
            Log::info('iCare API Raw Response: ' . $rawBody);

            $body = json_decode($rawBody, true);

            // iCare API: metaData.code == '200' berarti sukses
            $code    = $body['metaData']['code'] ?? $body['metadata']['code'] ?? null;
            $message = $body['metaData']['message'] ?? $body['metadata']['message'] ?? null;

            if ($code == '200') {
                $resp = $body['response'] ?? null;

                if (is_string($resp)) {
                    // Coba parse sebagai JSON
                    $parsed = json_decode($resp, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($parsed['url'])) {
                        $this->iCareUrl = $parsed['url'];
                    } elseif (json_last_error() === JSON_ERROR_NONE && isset($parsed['URL'])) {
                        $this->iCareUrl = $parsed['URL'];
                    } else {
                        // Respons langsung berisi URL string
                        $this->iCareUrl = trim($resp, '"');
                    }
                } elseif (is_array($resp)) {
                    $this->iCareUrl = $resp['url'] ?? $resp['URL'] ?? '';
                }

                if (empty($this->iCareUrl)) {
                    Log::warning('iCare: code 200 tapi URL kosong. Raw: ' . $rawBody);
                    $this->errorMessage = 'URL iCare tidak ditemukan dalam respons BPJS.';
                }
            } else {
                $this->errorMessage = $message ?? 'Terjadi kesalahan saat memanggil API iCare BPJS.';
            }

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // HTTP 4xx
            $rawBody = $e->getResponse()->getBody()->getContents();
            Log::error('iCare Client Error (4xx): ' . $rawBody);
            $errBody = json_decode($rawBody, true);
            $this->errorMessage = $errBody['metaData']['message']
                ?? $errBody['metadata']['message']
                ?? 'Error HTTP ' . $e->getCode() . ': ' . $rawBody;

        } catch (\GuzzleHttp\Exception\ServerException $e) {
            // HTTP 5xx
            $rawBody = $e->getResponse()->getBody()->getContents();
            Log::error('iCare Server Error (5xx): ' . $rawBody);
            $this->errorMessage = 'Server BPJS mengalami gangguan (HTTP 5xx). Silakan coba beberapa saat lagi.';

        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            // Network / cURL error (seperti error 56)
            Log::error('iCare Connection Error: ' . $e->getMessage());
            $this->errorMessage = 'Gagal terhubung ke server BPJS. Pastikan server memiliki akses internet ke apijkn.bpjs-kesehatan.go.id. Detail: ' . $e->getMessage();

        } catch (\Exception $e) {
            Log::error('iCare Fetch Error: ' . $e->getMessage());
            $this->errorMessage = 'Terjadi kesalahan internal: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.modul.rawat-jalan.icare.index')->layout('layouts.app');
    }
}
