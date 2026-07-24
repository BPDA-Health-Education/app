<?php
/**
 * AI (Google Gemini) configuration and helper wrapper
 *
 * Requires environment variable GOOGLE_AI_API_KEY to be set.
 * Optionally set GOOGLE_AI_API_ENDPOINT to override the default model endpoint.
 *
 * This file provides a minimal wrapper `ai_generate_text($prompt, $options)` that
 * calls the configured endpoint using the API key.
 */

define('GOOGLE_AI_API_KEY', getenv('GOOGLE_AI_API_KEY') ?: '');
// Default endpoint — update in env to use a different Gemini model/endpoint
define('GOOGLE_AI_API_ENDPOINT', getenv('GOOGLE_AI_API_ENDPOINT') ?: 'https://generativelanguage.googleapis.com/v1beta/models/text-bison-001:generate');

if (empty(GOOGLE_AI_API_KEY)) {
    if (getenv('APP_ENV') === 'development') {
        error_log('GOOGLE_AI_API_KEY is not set; AI features will be disabled until configured');
    }
}

/**
 * Generate text using configured Gemini endpoint.
 * Returns associative array decoded from the provider response.
 * Throws Exception on network or API errors.
 */
function ai_generate_text(string $prompt, array $options = []) {
    if (empty(GOOGLE_AI_API_KEY)) {
        throw new Exception('AI API key not configured (GOOGLE_AI_API_KEY)');
    }

    $endpoint = $options['endpoint'] ?? GOOGLE_AI_API_ENDPOINT;
    $temperature = $options['temperature'] ?? 0.2;
    $maxTokens = $options['max_tokens'] ?? 512;

    // Use v1 request shape: prompt.text per v1 API; keep legacy-friendly keys
    $payload = [
        'prompt' => [ 'text' => $prompt ],
        'temperature' => $temperature,
        'maxOutputTokens' => $maxTokens,
    ];

    $jsonPayload = json_encode($payload, JSON_UNESCAPED_SLASHES);

    // Optional debug logging (development only). Enable by setting AI_DEBUG=1 in env.
    $debugEnabled = (getenv('APP_ENV') === 'development' && getenv('AI_DEBUG') === '1');
    if ($debugEnabled) {
        $logFile = __DIR__ . '/../.ai_debug.log';
        // Mask API key in logs
        $maskedKey = 'REDACTED';
        $logEntry = "[REQUEST] endpoint=$endpoint payload=" . $jsonPayload . PHP_EOL;
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    // First attempt: Authorization header with Bearer token (service-style)
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . GOOGLE_AI_API_KEY
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if ($debugEnabled) {
        $logFile = __DIR__ . '/../.ai_debug.log';
        $logEntry = "[RESPONSE-ATTEMPT-1] http=$httpCode body=" . ($response ?? '') . PHP_EOL;
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    // If first attempt failed, try API key as query parameter (quick connectivity test)
    if ($curlErr || $httpCode < 200 || $httpCode >= 300) {
        // Build endpoint with ?key=...
        $endpointWithKey = $endpoint . (strpos($endpoint, '?') === false ? '?key=' : '&key=') . urlencode(GOOGLE_AI_API_KEY);

        $ch2 = curl_init($endpointWithKey);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response2 = curl_exec($ch2);
        $httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
        $curlErr2 = curl_error($ch2);
        curl_close($ch2);

        if ($debugEnabled) {
            $logFile = __DIR__ . '/../.ai_debug.log';
            $logEntry = "[RESPONSE-ATTEMPT-2] http=$httpCode2 body=" . ($response2 ?? '') . PHP_EOL;
            file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        }

        if ($curlErr2) {
            throw new Exception('Curl error while contacting AI API (attempt 2): ' . $curlErr2);
        }

        if ($httpCode2 < 200 || $httpCode2 >= 300) {
            // Fail with combined info
            $body1 = $response ?? '';
            $body2 = $response2 ?? '';
            throw new Exception("AI API attempts failed. Attempt1 HTTP $httpCode: $body1 | Attempt2 HTTP $httpCode2: $body2");
        }

        $decoded = json_decode($response2, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Failed to decode AI API response (attempt 2): ' . json_last_error_msg());
        }

        return $decoded;
    }

    if ($curlErr) {
        throw new Exception('Curl error while contacting AI API: ' . $curlErr);
    }

    $decoded = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Failed to decode AI API response: ' . json_last_error_msg());
    }

    return $decoded;
}
