<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookConversionAPIService
{
    /**
     * Send event to Facebook Conversion API
     *
     * @param string $pixelId
     * @param string $accessToken
     * @param string $eventName
     * @param array $eventData
     * @param array $userData
     * @return bool
     */
    public function sendEvent(
        string $pixelId,
        string $accessToken,
        string $eventName,
        array $eventData = [],
        array $userData = [],
        ?string $testEventCode = null
    ): bool {
        try {
            $url = "https://graph.facebook.com/v18.0/{$pixelId}/events";

            $eventPayload = [
                'event_name' => $eventName,
                'event_time' => time(),
                'event_id' => $this->generateEventId($userData, $eventName),
                'event_source_url' => $eventData['event_source_url'] ?? request()->url(),
                'action_source' => 'website',
                'user_data' => $this->prepareUserData($userData),
                'custom_data' => $this->prepareCustomData($eventData),
            ];

            // Add test_event_code if provided
            if ($testEventCode) {
                $eventPayload['test_event_code'] = $testEventCode;
            }

            $payload = [
                'data' => [$eventPayload],
                'access_token' => $accessToken,
            ];

            $response = Http::timeout(5)->post($url, $payload);

            if ($response->successful()) {
                return true;
            } else {
                Log::error('Facebook Conversion API event failed', [
                    'pixel_id' => $pixelId,
                    'event_name' => $eventName,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Facebook Conversion API exception', [
                'pixel_id' => $pixelId,
                'event_name' => $eventName,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send Purchase event
     *
     * @param string $pixelId
     * @param string $accessToken
     * @param array $orderData
     * @param array $userData
     * @return bool
     */
    public function sendPurchaseEvent(
        string $pixelId,
        string $accessToken,
        array $orderData,
        array $userData,
        ?string $testEventCode = null
    ): bool {
        $eventData = [
            'currency' => $orderData['currency'] ?? 'EGP',
            'value' => $orderData['value'] ?? 0,
            'content_ids' => $orderData['content_ids'] ?? [],
            'content_name' => $orderData['content_name'] ?? '',
            'content_type' => $orderData['content_type'] ?? 'product',
            'num_items' => $orderData['num_items'] ?? 1,
        ];

        return $this->sendEvent($pixelId, $accessToken, 'Purchase', $eventData, $userData, $testEventCode);
    }

    /**
     * Generate unique event ID to prevent duplicates
     *
     * @param array $userData
     * @param string $eventName
     * @return string
     */
    protected function generateEventId(array $userData, string $eventName): string
    {
        $data = [
            $eventName,
            $userData['email'] ?? '',
            $userData['phone'] ?? '',
            $userData['ip'] ?? request()->ip(),
            time(),
        ];

        return md5(implode('|', $data));
    }

    /**
     * Prepare user data for Facebook Conversion API
     *
     * @param array $userData
     * @return array
     */
    protected function prepareUserData(array $userData): array
    {
        $prepared = [];

        // Email (hashed)
        if (!empty($userData['email'])) {
            $prepared['em'] = hash('sha256', strtolower(trim($userData['email'])));
        }

        // Phone (hashed)
        if (!empty($userData['phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $userData['phone']);
            if (!empty($phone)) {
                $prepared['ph'] = hash('sha256', $phone);
            }
        }

        // First name (hashed)
        if (!empty($userData['first_name'])) {
            $prepared['fn'] = hash('sha256', strtolower(trim($userData['first_name'])));
        }

        // Last name (hashed)
        if (!empty($userData['last_name'])) {
            $prepared['ln'] = hash('sha256', strtolower(trim($userData['last_name'])));
        }

        // IP address
        if (!empty($userData['ip'])) {
            $prepared['client_ip_address'] = $userData['ip'];
        }

        // User agent
        if (!empty($userData['user_agent'])) {
            $prepared['client_user_agent'] = $userData['user_agent'];
        } elseif (request()->userAgent()) {
            $prepared['client_user_agent'] = request()->userAgent();
        }

        // FBC (Facebook Click ID) - from _fbc cookie if available
        if (!empty($userData['fbc'])) {
            $prepared['fbc'] = $userData['fbc'];
        }

        // FBP (Facebook Browser ID) - from _fbp cookie if available
        if (!empty($userData['fbp'])) {
            $prepared['fbp'] = $userData['fbp'];
        }

        return $prepared;
    }

    /**
     * Prepare custom data for Facebook Conversion API
     *
     * @param array $eventData
     * @return array
     */
    protected function prepareCustomData(array $eventData): array
    {
        $prepared = [];

        if (isset($eventData['currency'])) {
            $prepared['currency'] = $eventData['currency'];
        }

        if (isset($eventData['value'])) {
            $prepared['value'] = (float) $eventData['value'];
        }

        if (isset($eventData['content_ids']) && is_array($eventData['content_ids'])) {
            $prepared['content_ids'] = $eventData['content_ids'];
        }

        if (isset($eventData['content_name'])) {
            $prepared['content_name'] = $eventData['content_name'];
        }

        if (isset($eventData['content_type'])) {
            $prepared['content_type'] = $eventData['content_type'];
        }

        if (isset($eventData['num_items'])) {
            $prepared['num_items'] = (int) $eventData['num_items'];
        }

        return $prepared;
    }
}
