<?php
/**
 * Simple AI generate endpoint
 * POST JSON: { "prompt": "...", "max_tokens": 256 }
 * Requires authentication (reuse require_auth from helpers)
 */

require_once __DIR__ . '/../../config/ai.php';
require_once __DIR__ . '/../../config/helpers.php';

$user = require_auth();

$body = json_body();
$prompt = $body['prompt'] ?? null;
if (!$prompt) json_error('BAD_REQUEST', 'Missing prompt', 400);

$options = [];
if (isset($body['max_tokens'])) $options['max_tokens'] = (int)$body['max_tokens'];
if (isset($body['temperature'])) $options['temperature'] = (float)$body['temperature'];

try {
    $resp = ai_generate_text($prompt, $options);
    json_ok(['ai_response' => $resp], 200);
} catch (Exception $e) {
    json_error('AI_ERROR', $e->getMessage(), 502);
}
