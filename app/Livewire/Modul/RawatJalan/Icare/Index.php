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
    public $isLoading = false;

    public function mount($no_rawat)
    {
        $this->no_rawat = $no_rawat;
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'poliklinik', 'penjab'])->find(str_replace('-', '/', $no_rawat));

        if (!$this->regPeriksa) {
            abort(404, 'Data pendaftaran tidak ditemukan.');
        }

        $this->isLoading = true;
        $this->fetchIcareUrl();
        $this->isLoading = false;
    }

    /**
     * Re-fetch URL iCare dari API BPJS.
     * Berguna karena URL iCare bersifat one-time dan bisa expire.
     */
    public function refresh(): void
    {
        $this->iCareUrl     = '';
        $this->errorMessage = '';
        $this->isLoading    = true;
        $this->fetchIcareUrl();
        $this->isLoading    = false;
    }

    /**
     * Buat header autentikasi untuk API iCare BPJS.
     * Signature: HMAC-SHA256(consid + "&" + timestamp, secretKey) -> Base64
     */
    protected function buildHeaders(string $timestamp): array
    {
        $consid    = env('ICARE_CONSID');
        $secretKey = env('ICARE_SECRET_KEY');
        $userKey   = env('ICARE_USER_KEY');

        $signature  = hash_hmac('sha256', $consid . '&' . $timestamp, $secretKey, true);
        $encodedSig = base64_encode($signature);

        return [
            'X-cons-id'    => $consid,
            'X-timestamp'  => $timestamp,
            'X-signature'  => $encodedSig,
            'user_key'     => $userKey,
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ];
    }

    /**
     * Dekripsi respon terenkripsi dari BPJS (AES-256-CBC).
     */
    protected function stringDecrypt(string $key, string $string): string|false
    {
        $encrypt_method = 'AES-256-CBC';
        $key_hash       = hex2bin(hash('sha256', $key));
        $iv             = substr(hex2bin(hash('sha256', $key)), 0, 16);

        return openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
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

            $baseUrl   = env('ICARE_BASE_URL', 'https://apijkn.bpjs-kesehatan.go.id/wsihs/api/rs/validate');
            $consid    = env('ICARE_CONSID');
            $secretKey = env('ICARE_SECRET_KEY');
            $timestamp = strval(time());

            $client = new Client([
                'timeout' => 30.0,
                'verify'  => false,
                'curl'    => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                ],
            ]);

            $payload = json_encode([
                'param'      => $no_peserta,
                'kodedokter' => intval($kd_dokter_bpjs),
            ]);

            $maxRetries    = 2;
            $lastException = null;
            $response      = null;

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    $response = $client->post($baseUrl, [
                        'headers' => $this->buildHeaders($timestamp),
                        'body'    => $payload,
                    ]);
                    $lastException = null;
                    break;
                } catch (\GuzzleHttp\Exception\ConnectException $e) {
                    $lastException = $e;
                    Log::warning("iCare percobaan ke-{$attempt} gagal: " . $e->getMessage());
                    if ($attempt < $maxRetries) {
                        sleep(1);
                    }
                }
            }

            if ($lastException !== null) {
                throw $lastException;
            }

            $rawBody = $response->getBody()->getContents();
            Log::info('iCare API Raw Response: ' . $rawBody);

            $body = json_decode($rawBody, true);

            $code    = $body['metaData']['code'] ?? $body['metadata']['code'] ?? null;
            $message = $body['metaData']['message'] ?? $body['metadata']['message'] ?? null;

            if ($code == '200') {
                $resp = $body['response'] ?? null;

                if (!empty($resp)) {
                    // 1. Dekripsi AES-256-CBC dengan key: consid + secretKey + timestamp
                    $key           = $consid . $secretKey . $timestamp;
                    $stringDecrypt = $this->stringDecrypt($key, $resp);

                    if (!empty($stringDecrypt)) {
                        // 2. Dekompresi LZString
                        $decompress = \LZCompressor\LZString::decompressFromEncodedURIComponent($stringDecrypt);
                        $riwayat    = json_decode($decompress, true);

                        if (is_array($riwayat) && isset($riwayat['url'])) {
                            $this->iCareUrl = $riwayat['url'];
                        } elseif (is_string($riwayat) && filter_var($riwayat, FILTER_VALIDATE_URL)) {
                            $this->iCareUrl = $riwayat;
                        } else {
                            Log::warning('iCare decompressed value is not a valid URL array: ' . $decompress);
                        }
                    } else {
                        Log::error('iCare stringDecrypt failed for key: ' . $key);
                    }
                }

                if (empty($this->iCareUrl)) {
                    Log::warning('iCare: code 200 tapi URL kosong setelah dekripsi. Raw: ' . $rawBody);
                    $this->errorMessage = 'URL iCare tidak ditemukan dalam respons BPJS setelah dekripsi.';
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
