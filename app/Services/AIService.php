<?php

namespace App\Services;

use App\Models\AdditionalSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    private const GEMINI_API_URL_2_5_FLASH = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    private const GEMINI_API_URL_2_0 = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent';
    private const GEMINI_API_URL_FALLBACK = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
    private const OPENAI_API_URL = 'https://api.openai.com/v1/chat/completions';

    private const SYSTEM_INSTRUCTION = "You are a professional HTML generator. Generate COMPLETE, valid HTML pages from <!DOCTYPE html> to </html>. Always include all required tags: <!DOCTYPE html>, <html>, <head>, <body>, and closing </html>. Never stop mid-generation. Use inline CSS in <style> tag. The HTML must be fully functional and complete.";

    /**
     * Get active AI provider
     */
    private function getActiveProvider(): ?string
    {
        $provider = AdditionalSetting::getValue('ai_provider', 'gemini');
        $geminiKey = AdditionalSetting::getValue('ai_gemini_api_key');
        $openaiKey = AdditionalSetting::getValue('ai_openai_api_key');

        if ($provider === 'gemini' && $geminiKey) {
            return 'gemini';
        }

        if ($provider === 'openai' && $openaiKey) {
            return 'openai';
        }

        return null;
    }

    /**
     * Check if AI is configured and available
     */
    public function isAvailable(): bool
    {
        return $this->getActiveProvider() !== null;
    }

    /**
     * Generate content using AI (uses configured provider)
     */
    public function generateContent(string $prompt, array $options = []): ?string
    {
        $provider = $this->getActiveProvider();

        if (!$provider) {
            Log::warning('No active AI provider configured');
            return null;
        }

        try {
            if ($provider === 'gemini') {
                $apiKey = AdditionalSetting::getValue('ai_gemini_api_key');
                return $this->callGeminiApi($apiKey, $prompt, $options);
            }

            if ($provider === 'openai') {
                return $this->callOpenAIApi($prompt, $options);
            }
        } catch (\Exception $e) {
            Log::error('AI generation failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Generate content with specific Gemini API key (for testing)
     */
    public function generateWithGeminiKey(string $apiKey, string $prompt, array $options = []): ?string
    {
        return $this->callGeminiApi($apiKey, $prompt, $options);
    }

    /**
     * Call Gemini API with fallback strategy
     */
    private function callGeminiApi(string $apiKey, string $prompt, array $options = []): ?string
    {
        if (!$apiKey) {
            return null;
        }

        $requestData = $this->buildGeminiRequestData($prompt, $options);
        $response = $this->makeGeminiRequest($apiKey, $requestData, 120);

        if (!$response['success']) {
            Log::error('Gemini API request failed', [
                'status' => $response['status'],
                'error' => $response['error']
            ]);
            return null;
        }

        $content = $response['content'];

        // Log warning if HTML is incomplete
        if ($content && strpos($content, '</html>') === false) {
            Log::warning('Gemini API response missing closing </html> tag', [
                'content_length' => strlen($content),
                'finish_reason' => $response['finish_reason'] ?? 'unknown'
            ]);
        }

        return $content;
    }

    /**
     * Build Gemini API request data
     */
    private function buildGeminiRequestData(string $prompt, array $options = []): array
    {
        $config = [
            'temperature' => $options['temperature'] ?? 0.7,
            'topP' => $options['topP'] ?? 0.95,
            'topK' => $options['topK'] ?? 40,
            'maxOutputTokens' => $options['maxOutputTokens'] ?? 8192,
            'stopSequences' => $options['stopSequences'] ?? [],
        ];

        return [
            'contents' => [
                [
                    'parts' => [['text' => $prompt]]
                ]
            ],
            'systemInstruction' => [
                'parts' => [['text' => self::SYSTEM_INSTRUCTION]]
            ],
            'generationConfig' => $config,
        ];
    }

    /**
     * Make Gemini API request with fallback strategy
     */
    private function makeGeminiRequest(string $apiKey, array $requestData, int $timeout = 120): array
    {
        $headers = [
            'Content-Type' => 'application/json',
            'X-goog-api-key' => $apiKey,
        ];

        $urls = [
            self::GEMINI_API_URL_2_5_FLASH,
            self::GEMINI_API_URL_2_0,
            self::GEMINI_API_URL_FALLBACK,
        ];

        $lastResponse = null;
        foreach ($urls as $index => $url) {
            $response = Http::timeout($timeout)->withHeaders($headers)->post($url, $requestData);
            $lastResponse = $response;

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                $finishReason = $data['candidates'][0]['finishReason'] ?? null;

                if (!$content) {
                    Log::warning('Gemini API returned empty content', ['response' => $data]);
                }

                return [
                    'success' => true,
                    'content' => $content,
                    'finish_reason' => $finishReason
                ];
            }

            // Get error message to check if it's quota related
            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['message'] ?? '';
            $isQuotaError = $this->isQuotaExceededError($errorMessage);

            // If quota exceeded, stop trying immediately (won't work with other models either)
            if ($isQuotaError) {
                break;
            }

            // Only retry on 429 or 404, not on other errors
            if ($response->status() !== 429 && $response->status() !== 404) {
                break;
            }

            if ($index < count($urls) - 1) {
                Log::warning("Gemini API request failed ({$response->status()}), trying fallback");
            }
        }

        $errorBody = $lastResponse ? $lastResponse->json() : [];
        return [
            'success' => false,
            'status' => $lastResponse ? $lastResponse->status() : 500,
            'error' => $errorBody['error']['message'] ?? 'Unknown error'
        ];
    }

    /**
     * Check if error is quota exceeded (simple check)
     */
    private function isQuotaExceededError(string $errorMessage): bool
    {
        if (empty($errorMessage)) {
            return false;
        }

        $quotaKeywords = ['quota', 'Quota exceeded', 'exceeded your current quota'];
        $lowerMessage = strtolower($errorMessage);

        foreach ($quotaKeywords as $keyword) {
            if (stripos($lowerMessage, strtolower($keyword)) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Call OpenAI API
     */
    private function callOpenAIApi(string $prompt, array $options = []): ?string
    {
        $apiKey = AdditionalSetting::getValue('ai_openai_api_key');
        $model = $options['model'] ?? AdditionalSetting::getValue('ai_openai_model', 'gpt-3.5-turbo');

        if (!$apiKey) {
            return null;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post(self::OPENAI_API_URL, [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => $options['max_tokens'] ?? 4096, // Maximum for OpenAI (can be increased if needed)
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? null;

            if (!$content) {
                Log::warning('OpenAI API returned empty content', ['response' => $data]);
            }

            return $content;
        }

        Log::error('OpenAI API request failed', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        return null;
    }

    /**
     * Test Gemini API key
     */
    public function testGeminiApiKey(string $apiKey): array
    {
        try {
            $testPrompt = 'Say "API key is working" if you can read this.';
            $requestData = $this->buildGeminiRequestData($testPrompt, ['maxOutputTokens' => 100]);

            $response = $this->makeGeminiRequest($apiKey, $requestData, 30);

            if ($response['success']) {
                return [
                    'success' => true,
                    'message' => 'API key is working correctly',
                    'response' => $response['content']
                ];
            }

            // Check if it's a quota issue (API key is valid but quota exceeded)
            if ($response['status'] === 429) {
                return [
                    'success' => true,
                    'message' => 'API key is valid but quota exceeded. The key works correctly.',
                    'quota_exceeded' => true,
                    'status' => $response['status'],
                    'details' => $response['error']
                ];
            }

            return [
                'success' => false,
                'message' => $response['error'] ?? 'Unknown error',
                'status' => $response['status']
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
