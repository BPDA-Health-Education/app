<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/helpers.php';
session_write_close();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../..');
$dotenv->load();

$user = require_auth();
$pdo  = db();

// Verify AI Permission
$s = $pdo->prepare("SELECT ai_enabled FROM users WHERE id = :id");
$s->execute([':id' => $user['id']]);
if (!(bool)$s->fetchColumn()) {
    json_error('FORBIDDEN', 'AI feature not enabled', 403);
}

$b = json_body();
$prompt = $b['prompt'] ?? '';
if (!$prompt) json_error('BAD_REQUEST', 'Prompt required');

$apiKey = $_ENV['GOOGLE_AI_API_KEY'];
// Use configured endpoint if present; fall back to v1 Gemini generateContent
$endpoint = $_ENV['GOOGLE_AI_API_ENDPOINT'] ?? 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent';
// Build URL with api key as query param (existing behavior). For production prefer Authorization: Bearer.
$url = $endpoint . (strpos($endpoint, '?') === false ? '?key=' : '&key=') . urlencode($apiKey);

$payload = [
    'contents' => [['parts' => [['text' => $prompt]]]]
];
$jsonPayload = json_encode($payload);

// Temporary debug logging (enabled via AI_DEBUG=1 and AI_DEBUG_LOG_PATH in .env)
$debugEnabled = (getenv('AI_DEBUG') === '1');
$logPath = getenv('AI_DEBUG_LOG_PATH') ?: __DIR__ . '/../../logs/ai_debug.log';
if ($debugEnabled) {
    // Mask API key in logs
    $maskedUrl = preg_replace('/(key=)[^&\s]+/i', '\1REDACTED', $url);
    $reqLog = "[REQUEST] url=$maskedUrl payload=" . $jsonPayload . PHP_EOL;
    @file_put_contents($logPath, $reqLog, FILE_APPEND | LOCK_EX);
}

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr = curl_error($ch);
curl_close($ch);

if ($debugEnabled) {
    $resLog = "[RESPONSE] http=$httpCode curlErr=" . ($curlErr ?: 'none') . " body=" . ($response ?? '') . PHP_EOL;
    @file_put_contents($logPath, $resLog, FILE_APPEND | LOCK_EX);
}

if ($curlErr) {
    json_error('SERVER_ERROR', 'AI Service Error (curl): ' . $curlErr, 502);
}

if ($httpCode === 200) {
    echo $response;
} else {
    json_error('SERVER_ERROR', 'AI Service Error', $httpCode);
}
