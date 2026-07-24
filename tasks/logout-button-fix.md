Title: Fix Logout Button
Estimated effort: 0.5–1 day
Blockers: session cookie vs local storage mismatch

Problem: Logout button appears not to log out users — likely front-end not calling server logout or CSS covers button so click not registered.

Steps & verification
1) Reproduce: open browser DevTools → click logout → watch network requests and console.
2) If no network request: check button element (z-index / pointer-events / overlay).
3) If request exists but session persists: check server session destroy and cookie expiration.

Server-side (PHP) endpoint example (api/auth/logout.php):

<?php
require_once __DIR__ . '/../../config/helpers.php';
session_start();
// invalidate server-side session
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'], $params['secure'], $params['httponly']
    );
}
session_destroy();
// Also clear any JWT or auth cookies
setcookie('auth_token', '', time()-3600, '/');
header('Content-Type: application/json');
echo json_encode(['ok' => true]);

Client-side (JS) quick snippet:

async function logout() {
  const res = await fetch('/api/auth/logout.php', { method: 'POST', credentials: 'include' });
  if (res.ok) window.location = '/login.php';
  else alert('Logout failed');
}

debugging CSS: check for overlays
- Use DevTools Elements panel, right-click the button area → Inspect → ensure button is topmost and pointer-events not none.

Testing
- Unit: simulate click and mock fetch to ensure navigation.
- Integration: end-to-end manual test, clear cookies and verify cannot access authenticated endpoints.

Files to change
- api/auth/logout.php (new)
- navbar component JS: attach logout() to button
- Add simple integration test that hits logout endpoint and then /api/user returns 401

Commit message suggestion: "Fix logout behavior: add server logout endpoint and client call; debug CSS overlay"