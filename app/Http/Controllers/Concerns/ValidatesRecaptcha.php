<?php

namespace App\Http\Controllers\Concerns;

use App\Models\AdditionalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

trait ValidatesRecaptcha
{
    /**
     * Validate reCAPTCHA if enabled
     */
    protected function validateRecaptcha(Request $request): void
    {
        $recaptchaVersion = AdditionalSetting::getValue('recaptcha_version', 'none');

        if ($recaptchaVersion === 'none') {
            return; // reCAPTCHA is disabled
        }

        $recaptchaResponse = $request->input('g-recaptcha-response');

        if (empty($recaptchaResponse)) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => __('messages.recaptcha_required'),
            ]);
        }

        if ($recaptchaVersion === 'v2') {
            $this->validateRecaptchaV2($recaptchaResponse);
        } elseif ($recaptchaVersion === 'v3') {
            $this->validateRecaptchaV3($recaptchaResponse, $request);
        }
    }

    /**
     * Validate reCAPTCHA V2
     */
    protected function validateRecaptchaV2(string $response): void
    {
        $secretKey = AdditionalSetting::getValue('recaptcha_v2_secret_key', '');

        if (empty($secretKey)) {
            return; // Secret key not configured, skip validation
        }

        $verifyResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secretKey,
            'response' => $response,
            'remoteip' => request()->ip(),
        ]);

        $result = $verifyResponse->json();

        if (!isset($result['success']) || $result['success'] !== true) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => __('messages.recaptcha_verification_failed'),
            ]);
        }
    }

    /**
     * Validate reCAPTCHA V3
     */
    protected function validateRecaptchaV3(string $response, Request $request): void
    {
        $secretKey = AdditionalSetting::getValue('recaptcha_v3_secret_key', '');

        if (empty($secretKey)) {
            return; // Secret key not configured, skip validation
        }

        $verifyResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secretKey,
            'response' => $response,
            'remoteip' => $request->ip(),
        ]);

        $result = $verifyResponse->json();

        if (!isset($result['success']) || $result['success'] !== true) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => __('messages.recaptcha_verification_failed'),
            ]);
        }

        // Check score for V3 (recommended minimum is 0.5)
        $score = $result['score'] ?? 0;
        if ($score < 0.5) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => __('messages.recaptcha_score_too_low'),
            ]);
        }
    }
}
