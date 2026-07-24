<?php
/**
 * Generate Google OAuth authorization URL for manual testing.
 * Outputs JSON: { url: '...' }
 */
require_once __DIR__ . '/../../config/google.php';
require_once __DIR__ . '/../../config/helpers.php';

session_start();

$state = bin2hex(random_bytes(8));
$_SESSION['google_oauth_state'] = $state;

$clientId = GOOGLE_CLIENT_ID ?? '';
$redirect = GOOGLE_REDIRECT_URI ?? '';
$scopes = implode(' ', GOOGLE_OAUTH_SCOPES);

$params = http_build_query([
    'response_type' => 'code',
    'client_id' => $clientId,
    'redirect_uri' => $redirect,
    'scope' => $scopes,
    'access_type' => 'offline',
    'prompt' => 'consent',
    'state' => $state
]);

$authUrl = GOOGLE_OAUTH_AUTH_URL . '?' . $params;
header('Content-Type: application/json');
echo json_encode(['url' => $authUrl]);
