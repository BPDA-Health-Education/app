<?php
/**
 * Google OAuth 2.0 & Docs API Configuration
 *
 * Reads credentials from environment variables when available. Do NOT commit secrets to source control.
 * Set GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, and optionally GOOGLE_REDIRECT_URI in your environment or .env file.
 */

// Load values from environment first (recommended)
$googleClientId = getenv('GOOGLE_CLIENT_ID') ?: '';
$googleClientSecret = getenv('GOOGLE_CLIENT_SECRET') ?: '';
$defaultRedirect = (isset($_SERVER['HTTP_HOST']) ? 'https://' . $_SERVER['HTTP_HOST'] . '/api/google/callback.php' : '');
$googleRedirect = getenv('GOOGLE_REDIRECT_URI') ?: $defaultRedirect;

define('GOOGLE_CLIENT_ID',     $googleClientId);
define('GOOGLE_CLIENT_SECRET', $googleClientSecret);
define('GOOGLE_REDIRECT_URI',  $googleRedirect);

// Google Docs template (optional)
define('GOOGLE_DOCS_TEMPLATE_URL', getenv('GOOGLE_DOCS_TEMPLATE_URL') ?: '');

// Session storage keys
define('GOOGLE_TOKEN_SESSION_KEY', 'google_oauth_token');
define('GOOGLE_REFRESH_TOKEN_KEY', 'google_refresh_token');

// Scopes required by the app
define('GOOGLE_OAUTH_SCOPES', [
    'https://www.googleapis.com/auth/drive',      // Read/write Google Drive files
    'https://www.googleapis.com/auth/documents',   // Read/write Google Docs
    'https://www.googleapis.com/auth/userinfo.email', // Get user email
]);

// Google OAuth endpoints
define('GOOGLE_OAUTH_AUTH_URL', 'https://accounts.google.com/o/oauth2/v2/auth');
define('GOOGLE_OAUTH_TOKEN_URL', 'https://oauth2.googleapis.com/token');
define('GOOGLE_OAUTH_REVOKE_URL', 'https://oauth2.googleapis.com/revoke');

// API base URLs
define('GOOGLE_DRIVE_API_URL', 'https://www.googleapis.com/drive/v3');
define('GOOGLE_DOCS_API_URL', 'https://docs.googleapis.com/v1');
define('GOOGLE_MEET_API_URL', 'https://meet.googleapis.com/v2');

// Simple runtime check helper (not fatal) - useful for debugging during setup
if (empty(GOOGLE_CLIENT_ID) || empty(GOOGLE_CLIENT_SECRET)) {
    // Do not reveal secrets in production logs — just hint in development mode
    if (getenv('APP_ENV') === 'development') {
        error_log('Google OAuth not fully configured: GOOGLE_CLIENT_ID or GOOGLE_CLIENT_SECRET missing in environment');
    }
}
