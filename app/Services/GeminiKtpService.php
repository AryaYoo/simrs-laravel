<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GeminiKtpService
 *
 * Mengekstrak data KTP Indonesia dari gambar menggunakan Gemini Vision API.
 * Output: array terstruktur yang siap di-map ke field form pasien.
 */
class GeminiKtpService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = (string) (config('gemini.api_key') ?: env('GEMINI_API_KEY', ''));
        $this->model  = (string) (config('gemini.model') ?: env('GEMINI_MODEL', 'gemini-1.5-flash'));
    }

    /**
     * Periksa apakah Gemini API sudah dikonfigurasi.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Ekstrak data KTP dari string base64 gambar.
     *
     * @param  string $base64Image  Base64 gambar (tanpa header data URI)
     * @param  string $mimeType     MIME type gambar, default image/jpeg
     * @return array
     */
    public function extractFromBase64(string $base64Image, string $mimeType = 'image/jpeg'): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'data'    => null,
                'error'   => 'GEMINI_API_KEY belum dikonfigurasi di file .env server.',
            ];
        }

        $prompt = $this->buildPrompt();

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data'      => $base64Image,
                            ],
                        ],
                        [
                            'text' => $prompt,
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature'      => 0.1,
                'topP'             => 0.8,
                'topK'             => 10,
                'maxOutputTokens'  => 1024,
                'responseMimeType' => 'application/json',
            ],
        ];

        try {
            $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";

            $response = Http::withoutVerifying()
                ->timeout(45)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            if (!$response->successful()) {
                $errorBody = $response->json();
                $errorMsg  = $errorBody['error']['message'] ?? "HTTP {$response->status()}";
                Log::error('GeminiKtpService: API error', ['status' => $response->status(), 'body' => $errorBody]);

                return [
                    'success' => false,
                    'data'    => null,
                    'error'   => "Gemini API error ({$response->status()}): {$errorMsg}",
                ];
            }

            $responseData = $response->json();
            $rawText      = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($rawText)) {
                return [
                    'success' => false,
                    'data'    => null,
                    'error'   => 'Gemini tidak mengembalikan teks. Pastikan gambar KTP jelas dan terbaca.',
                ];
            }

            // Bersihkan markdown code block jika ada
            $rawText = preg_replace('/^```json\s*/i', '', trim($rawText));
            $rawText = preg_replace('/```\s*$/', '', $rawText);

            $parsed = json_decode(trim($rawText), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('GeminiKtpService: JSON parse error', ['raw' => $rawText]);
                return [
                    'success' => false,
                    'data'    => null,
                    'error'   => 'Gagal memproses respons AI. Coba foto ulang KTP dengan lebih jelas.',
                ];
            }

            $normalized = $this->normalizeData($parsed);

            return [
                'success' => true,
                'data'    => $normalized,
                'error'   => null,
            ];
        } catch (\Throwable $e) {
            Log::error('GeminiKtpService: Throwable Exception', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'data'    => null,
                'error'   => 'Koneksi ke Gemini API gagal: ' . substr($e->getMessage(), 0, 150),
            ];
        }
    }

    /**
     * Bangun prompt yang dikirim ke Gemini.
     */
    protected function buildPrompt(): string
    {
        return 'Kamu adalah sistem ekstraksi data KTP Indonesia yang sangat akurat. '
             . 'Analisis gambar KTP ini dan ekstrak semua informasi yang tersedia. '
             . 'Perhatikan struktur KTP Indonesia: '
             . '- "provinsi" ada di baris header paling atas (misal: "PROVINSI JAWA TIMUR"). '
             . '- "kabupaten" ada di baris header kedua (misal: "KABUPATEN BANYUWANGI" atau "KOTA SURABAYA"). '
             . '- "alamat" adalah baris Alamat jalan/dusun/lingkungan. '
             . '- "rt" dan "rw" adalah angka pada baris RT/RW. '
             . '- "kelurahan" adalah nama pada baris Kel/Desa. '
             . '- "kecamatan" adalah nama pada baris Kecamatan. '
             . 'Kembalikan HANYA objek JSON valid (tanpa komentar, tanpa markdown) dengan struktur berikut: '
             . '{"nik":"16 digit NIK atau null",'
             . '"nama":"Nama lengkap HURUF KAPITAL atau null",'
             . '"tempat_lahir":"Kota tempat lahir HURUF KAPITAL atau null",'
             . '"tanggal_lahir":"Format YYYY-MM-DD atau null",'
             . '"jenis_kelamin":"L untuk LAKI-LAKI P untuk PEREMPUAN atau null",'
             . '"alamat":"Alamat jalan/dusun HURUF KAPITAL atau null",'
             . '"rt":"string angka RT atau null",'
             . '"rw":"string angka RW atau null",'
             . '"kelurahan":"Nama kelurahan/desa HURUF KAPITAL atau null",'
             . '"kecamatan":"Nama kecamatan HURUF KAPITAL atau null",'
             . '"kabupaten":"Nama kota/kabupaten HURUF KAPITAL atau null",'
             . '"provinsi":"Nama provinsi HURUF KAPITAL atau null",'
             . '"agama":"Salah satu dari: ISLAM KRISTEN KATOLIK HINDU BUDHA KONGHUCU atau null",'
             . '"status_perkawinan":"Salah satu dari: BELUM MENIKAH MENIKAH CERAI HIDUP CERAI MATI atau null",'
             . '"pekerjaan":"Pekerjaan HURUF KAPITAL atau null",'
             . '"golongan_darah":"A B AB O atau null jika tidak terlihat",'
             . '"kewarganegaraan":"WNI atau WNA atau null"} '
             . 'Aturan: Jika field tidak terbaca gunakan null bukan string kosong. NIK harus 16 digit. '
             . 'Tanggal lahir: konversi DD-MM-YYYY ke YYYY-MM-DD.';
    }

    /**
     * Normalisasi dan sanitasi data dari respons Gemini.
     */
    protected function normalizeData(array $data): array
    {
        $normalized = [];

        // NIK
        $nik = preg_replace('/\D/', '', (string) ($data['nik'] ?? ''));
        $normalized['nik'] = strlen($nik) === 16 ? $nik : null;

        // Nama
        $normalized['nama'] = $this->cleanString($data['nama'] ?? null, 40);

        // Tempat lahir
        $normalized['tempat_lahir'] = $this->cleanString($data['tempat_lahir'] ?? null, 15);

        // Tanggal lahir — validasi format YYYY-MM-DD
        $tgl = (string) ($data['tanggal_lahir'] ?? '');
        $normalized['tanggal_lahir'] = preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl) ? $tgl : null;

        // Jenis kelamin
        $jk = strtoupper(trim((string) ($data['jenis_kelamin'] ?? '')));
        $normalized['jenis_kelamin'] = in_array($jk, ['L', 'P']) ? $jk : null;

        // Alamat
        $normalized['alamat'] = $this->cleanString($data['alamat'] ?? null, 200);

        // RT/RW
        $normalized['rt'] = $this->cleanString($data['rt'] ?? null, 5);
        $normalized['rw'] = $this->cleanString($data['rw'] ?? null, 5);

        // Wilayah
        $normalized['kelurahan'] = $this->cleanString($data['kelurahan'] ?? null, 50);
        $normalized['kecamatan'] = $this->cleanString($data['kecamatan'] ?? null, 50);
        $normalized['kabupaten'] = $this->cleanString($data['kabupaten'] ?? null, 50);
        $normalized['provinsi']  = $this->cleanString($data['provinsi'] ?? null, 50);

        // Agama — map ke nilai valid
        $agamaMap = [
            'ISLAM'     => 'ISLAM',
            'KRISTEN'   => 'KRISTEN',
            'KATOLIK'   => 'KATOLIK',
            'KHATOLIK'  => 'KATOLIK',
            'CATHOLIK'  => 'KATOLIK',
            'HINDU'     => 'HINDU',
            'BUDHA'     => 'BUDHA',
            'BUDDHA'    => 'BUDHA',
            'KONGHUCU'  => 'KONGHUCU',
            'KONGHUCHU' => 'KONGHUCU',
        ];
        $agamaRaw = strtoupper(trim((string) ($data['agama'] ?? '')));
        $normalized['agama'] = $agamaMap[$agamaRaw] ?? null;

        // Status perkawinan — map ke nilai valid
        $statusMap = [
            'BELUM MENIKAH' => 'BELUM MENIKAH',
            'BELUM KAWIN'   => 'BELUM MENIKAH',
            'MENIKAH'       => 'MENIKAH',
            'KAWIN'         => 'MENIKAH',
            'SUDAH MENIKAH' => 'MENIKAH',
            'CERAI HIDUP'   => 'JANDA',
            'JANDA'         => 'JANDA',
            'DUDA'          => 'DUDHA',
            'DUDHA'         => 'DUDHA',
            'CERAI MATI'    => 'JANDA',
        ];
        $statusRaw = strtoupper(trim((string) ($data['status_perkawinan'] ?? '')));
        $normalized['status_perkawinan'] = $statusMap[$statusRaw] ?? null;

        // Pekerjaan
        $normalized['pekerjaan'] = $this->cleanString($data['pekerjaan'] ?? null, 40);

        // Golongan darah
        $gd = strtoupper(trim((string) ($data['golongan_darah'] ?? '')));
        $normalized['golongan_darah'] = in_array($gd, ['A', 'B', 'AB', 'O']) ? $gd : null;

        // Kewarganegaraan
        $normalized['kewarganegaraan'] = $this->cleanString($data['kewarganegaraan'] ?? null, 10);

        return $normalized;
    }

    /**
     * Bersihkan string: trim, uppercase, batasi panjang, null jika kosong.
     */
    protected function cleanString(?string $value, int $maxLen): ?string
    {
        if (empty($value) || strtolower(trim($value)) === 'null') {
            return null;
        }
        $clean = strtoupper(trim($value));
        return strlen($clean) > 0 ? substr($clean, 0, $maxLen) : null;
    }
}
