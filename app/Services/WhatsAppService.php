<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    private $apiKey;
    private $baseUrl = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->apiKey = env('FONNTE_KEY');
    }

    public function sendMessage($phoneNumber, $message)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey
            ])->post($this->baseUrl, [
                'target' => $phoneNumber,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Unknown error occurred'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}