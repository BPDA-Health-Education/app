<?php
// Simple CLI/web test for AI generate endpoint using config/ai.php directly
require_once __DIR__ . '/../../config/ai.php';

$prompt = "Write a short friendly prescription summary for an adult patient with mild hypertension.";
try {
    $res = ai_generate_text($prompt, ['max_tokens' => 150]);
    header('Content-Type: application/json');
    echo json_encode(['prompt' => $prompt, 'result' => $res], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => $e->getMessage()]);
}
