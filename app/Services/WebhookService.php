<?php

namespace App\Services;

use App\Models\Webhook;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebhookService
{
    /**
     * Send webhook event to all active webhooks that listen to this event
     */
    public function dispatch(string $eventType, array $data, ?int $userId = null): void
    {
        $query = Webhook::where('is_active', true);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $webhooks = $query->get()->filter(function ($webhook) use ($eventType) {
            return $webhook->listensTo($eventType);
        });

        foreach ($webhooks as $webhook) {
            $this->sendWebhook($webhook, $eventType, $data);
        }
    }

    /**
     * Send webhook to a specific endpoint
     */
    public function sendWebhook(Webhook $webhook, string $eventType, array $data): void
    {
        $payload = [
            'event_type' => $eventType,
            'timestamp' => now()->toIso8601String(),
            'data' => $data,
        ];

        // Add signature if secret is set
        if ($webhook->secret) {
            $payload['signature'] = $this->generateSignature($payload, $webhook->secret);
        }

        try {
            $response = Http::timeout(10)
                ->post($webhook->url, $payload);

            $statusCode = $response->status();
            $success = $statusCode >= 200 && $statusCode < 300;

            // Log the webhook attempt
            WebhookLog::create([
                'webhook_id' => $webhook->id,
                'event_type' => $eventType,
                'payload' => $payload,
                'status_code' => $statusCode,
                'response' => $response->body(),
                'success' => $success,
                'error_message' => $success ? null : "HTTP {$statusCode}: " . $response->body(),
                'sent_at' => now(),
            ]);

            if (!$success) {
                Log::warning('Webhook delivery failed', [
                    'webhook_id' => $webhook->id,
                    'url' => $webhook->url,
                    'status_code' => $statusCode,
                    'response' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            // Log the failed webhook attempt
            WebhookLog::create([
                'webhook_id' => $webhook->id,
                'event_type' => $eventType,
                'payload' => $payload,
                'status_code' => null,
                'response' => null,
                'success' => false,
                'error_message' => $e->getMessage(),
                'sent_at' => now(),
            ]);

            Log::error('Webhook delivery exception', [
                'webhook_id' => $webhook->id,
                'url' => $webhook->url,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate HMAC signature for webhook payload
     */
    protected function generateSignature(array $payload, string $secret): string
    {
        $payloadString = json_encode($payload, JSON_UNESCAPED_SLASHES);
        return hash_hmac('sha256', $payloadString, $secret);
    }

    /**
     * Verify webhook signature
     */
    public function verifySignature(array $payload, string $signature, string $secret): bool
    {
        $expectedSignature = $this->generateSignature($payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }
}
