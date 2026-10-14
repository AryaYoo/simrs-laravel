<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Gemini API Key
    |--------------------------------------------------------------------------
    | API Key dari Google AI Studio (https://aistudio.google.com/app/apikey).
    | Digunakan untuk fitur Scan KTP otomatis menggunakan Gemini Vision.
    |
    */
    'api_key' => env('GEMINI_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Gemini Model
    |--------------------------------------------------------------------------
    | Model Gemini yang digunakan untuk analisis gambar KTP.
    | Rekomendasi: gemini-1.5-flash (cepat & murah) atau gemini-1.5-pro (lebih akurat).
    | Daftar model: https://ai.google.dev/models/gemini
    |
    */
    'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),

];
