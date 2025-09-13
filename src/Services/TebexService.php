<?php

namespace Azuriom\Plugin\MyPurchases\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TebexService
{
    protected $secret;
    protected $storeUrl;

    public function __construct($secret, $storeUrl)
    {
        $this->secret = $secret;
        $this->storeUrl = $storeUrl;
    }

    public function getUserPurchases($username)
    {
        if (empty($this->secret)) {
            return [];
        }

        try {
            $response = Http::withHeaders([
                'X-Tebex-Secret' => $this->secret
            ])->get("https://plugin.tebex.io/user/{$username}");

            if ($response->successful()) {
                $data = $response->json();
                $payments = $data['payments'] ?? [];

                // Filter only completed transactions
                return array_filter($payments, function($payment) {
                    $status = $payment['status'] ?? 0;
                    return $status === 1; // 1 = Complete
                });
            }

            Log::warning('Tebex API error: ' . $response->body());
            return [];

        } catch (\Exception $e) {
            Log::error('Tebex API exception: ' . $e->getMessage());
            return [];
        }
    }

    public function getPaymentDetails($transactionId)
    {
        if (empty($this->secret)) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'X-Tebex-Secret' => $this->secret
            ])->get("https://plugin.tebex.io/payments/{$transactionId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning("Tebex payment details error for {$transactionId}: " . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error("Tebex payment details exception for {$transactionId}: " . $e->getMessage());
            return null;
        }
    }

    protected function getStatusText($statusCode)
    {
        return match((int)$statusCode) {
            1 => 'Complete',
            2 => 'Refunded',
            3 => 'Chargeback',
            default => 'Unknown'
        };
    }
}
