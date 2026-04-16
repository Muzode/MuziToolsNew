<?php
// app/Http/Controllers/AIChatController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIChatController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $userMessage = $request->message;
        $apiKey = config('services.gemini.api_key');

        // Knowledge base
        $responses = [
            'denda' => '⚠️ Denda keterlambatan adalah Rp 5.000 per hari. Denda akan dihitung otomatis saat pengembalian.',
            'pinjam' => '📋 Cara meminjam: Login → Pilih alat → Ajukan peminjaman → Tunggu persetujuan petugas.',
            'kembali' => '🔄 Pengembalian: Buka Riwayat → Klik "Ajukan Pengembalian" → Petugas verifikasi.',
            'status' => '📊 Status: Pending (menunggu), Disetujui (dipinjam), Ditolak, Kembali.',
            'default' => '🤔 Maaf, saya kurang paham. Coba tanyakan tentang peminjaman, pengembalian, atau denda.'
        ];

        $response = $responses['default'];
        $message = strtolower($userMessage);

        foreach ($responses as $key => $value) {
            if (strpos($message, $key) !== false) {
                $response = $value;
                break;
            }
        }

        // Jika ada API Key Gemini, gunakan AI
        if ($apiKey) {
            try {
                $geminiResponse = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => "Kamu asisten TOOLZ. Jawab singkat: {$userMessage}"
                                ]
                            ]
                        ]
                    ]
                ]);

                if ($geminiResponse->successful()) {
                    $result = $geminiResponse->json();
                    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                        $response = $result['candidates'][0]['content']['parts'][0]['text'];
                    }
                }
            } catch (\Exception $e) {
                Log::error('Gemini API Error: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'response' => $response
        ]);
    }
}
