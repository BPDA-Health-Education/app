<?php
// Lightweight .env loader (no composer required) — used only for this temporary test script
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        list($k, $v) = explode('=', $line, 2);
        // Strip surrounding quotes
        $v = trim($v, "\"'");
        putenv(trim($k) . '=' . $v);
        $_ENV[trim($k)] = $v;
        $_SERVER[trim($k)] = $v;
    }
}

require 'C:\\Users\\Betty\\app\\config\\ai.php';

try {
    $res = ai_generate_text('Test ping from CLI: hello', ['max_tokens' => 64]);
    echo json_encode(['ok' => true, 'response' => $res]);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    exit(1);
}
