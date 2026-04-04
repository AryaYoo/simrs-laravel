<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SatuSehatService
{
    protected Client $client;
    protected string $authUrl;
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $organizationId;

    public function __construct()
    {
        $this->client         = new Client();
        $this->authUrl        = config('services.satusehat.auth_url', env('SATUSEHAT_AUTH_URL'));
        $this->baseUrl        = config('services.satusehat.base_url', env('SATUSEHAT_BASE_URL'));
        $this->clientId       = config('services.satusehat.client_id', env('SATUSEHAT_CLIENT_ID'));
        $this->clientSecret   = config('services.satusehat.client_secret', env('SATUSEHAT_CLIENT_SECRET'));
        $this->organizationId = config('services.satusehat.organization_id', env('SATUSEHAT_ORGANIZATION_ID'));
    }

    /**
     * Get OAuth2 Access Token with Caching
     */
    public function getAccessToken(): ?string
    {
        return Cache::remember('satusehat_token', 3000, function () {
            try {
                $response = $this->client->post($this->authUrl, [
                    'verify'      => false, // Bypass SSL for local XAMPP
                    'form_params' => [
                        'client_id'     => $this->clientId,
                        'client_secret' => $this->clientSecret,
                        'grant_type'    => 'client_credentials', // Wajib untuk API SatuSehat
                    ],
                ]);

                $data = json_decode($response->getBody()->getContents(), true);
                if (!isset($data['access_token'])) {
                    throw new \Exception("Token tidak ditemukan dalam response Kemenkes.");
                }
                
                return $data['access_token'];
            } catch (\Exception $e) {
                Log::error("SatuSehat Auth Error: " . $e->getMessage());
                Cache::forget('satusehat_token'); // Jangan cache kegagalan
                return null;
            }
        });
    }

    /**
     * Search Practitioner ID by NIK
     */
    public function getPractitionerIdByNik(string $nik): ?string
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        try {
            $response = $this->client->get($this->baseUrl . '/Practitioner', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
                'query' => [
                    'identifier' => 'https://fhir.kemkes.go.id/id/nik|' . $nik,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            // Extract UUID from the first entry
            if (isset($data['entry'][0]['resource']['id'])) {
                return $data['entry'][0]['resource']['id'];
            }

            Log::warning("SatuSehat: Practitioner with NIK $nik not found.");
            return null;

        } catch (\Exception $e) {
            Log::error("SatuSehat Practitioner Lookup Error ($nik): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Search Organization ID by SIRS (for completeness)
     */
    public function getOrganizationIdBySirs(string $sirs): ?string
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        try {
            $response = $this->client->get($this->baseUrl . '/Organization', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
                'query' => [
                    'identifier' => 'https://fhir.kemkes.go.id/id/sirs-number|' . $sirs,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['entry'][0]['resource']['id'] ?? null;

        } catch (\Exception $e) {
            Log::error("SatuSehat Organization Lookup Error ($sirs): " . $e->getMessage());
            return null;
        }
    }
}
