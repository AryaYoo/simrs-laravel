<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class BpjsService
{
    protected $client;
    protected $consid;
    protected $secretKey;
    protected $userKey;
    protected $kodeRS;
    protected $baseUrl;

    public function __construct()
    {
        $this->consid = env('BPJS_CONSID');
        $this->secretKey = env('BPJS_SECRET_KEY');
        $this->userKey = env('BPJS_USER_KEY');
        $this->kodeRS = env('BPJS_KODE_RS');
        $this->baseUrl = env('BPJS_BASE_URL');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => 30.0,
            'verify'   => false, // Set to false to bypass SSL certificate issues in dev/local
        ]);
    }

    /**
     * Generate BPJS Signature Headers
     * Signature: HMAC-SHA256(consid + "&" + timestamp, secretKey) -> Base64
     */
    protected function getHeaders(): array
    {
        $timestamp       = strval(time());
        $data            = $this->consid . '&' . $timestamp;
        $signature       = hash_hmac('sha256', $data, $this->secretKey, true);
        $encodedSig      = base64_encode($signature);

        return [
            'X-cons-id'    => $this->consid,
            'X-timestamp'  => $timestamp,
            'X-signature'  => $encodedSig,
            'user_key'     => $this->userKey,
            'Content-Type' => 'text/plain',
        ];
    }

    /**
     * Generate AES Key (SHA256 of consid + secretKey + kodeRS)
     */
    protected function getAesKey()
    {
        $keyRaw = $this->consid . $this->secretKey . $this->kodeRS;
        return hash('sha256', $keyRaw, true); // 32 bytes
    }

    /**
     * Generate IV (First 16 bytes of the AES Key)
     */
    protected function getAesIv($key)
    {
        return substr($key, 0, 16);
    }

    /**
     * Encrypt and Compress Data (Gzip -> AES-256-CBC -> Base64)
     */
    public function encryptData($dataJson)
    {
        // 1. Gzip Compression
        $compressed = gzencode($dataJson, 9);

        // 2. AES-256-CBC Encryption
        $key = $this->getAesKey();
        $iv = $this->getAesIv($key);

        $encrypted = openssl_encrypt($compressed, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        // 3. Base64 Encoding
        return base64_encode($encrypted);
    }

    /**
     * Decrypt Data (Base64 -> AES-256-CBC -> Gzip Decompress)
     */
    public function decryptData($encryptedBase64)
    {
        $key = $this->getAesKey();
        $iv = $this->getAesIv($key);

        $decoded = base64_decode($encryptedBase64);
        $decrypted = openssl_decrypt($decoded, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        if ($decrypted === false) return null;

        return gzdecode($decrypted);
    }

    /**
     * POST Request to BPJS
     */
    public function post($endpoint, $payload)
    {
        try {
            $response = $this->client->post($endpoint, [
                'headers' => $this->getHeaders(),
                'body'    => json_encode($payload)
            ]);

            $rawBody = $response->getBody()->getContents();
            
            // Log raw response for debugging
            Log::info("BPJS API Raw Response: " . $rawBody);

            $body = json_decode($rawBody, true);
            
            // Decrypt response if metadata->code is 1 (Success) and response field exists
            if (isset($body['response']) && is_string($body['response'])) {
                $decryptedResponse = $this->decryptData($body['response']);
                $body['response'] = json_decode($decryptedResponse, true);
            }

            return $body;
        } catch (\Exception $e) {
            Log::error("BPJS API General Error: " . $e->getMessage());
            return [
                'metaData' => [
                    'code' => (string) $e->getCode() ?: 'ERR',
                    'message' => $e->getMessage()
                ]
            ];
        }
    }
}
