<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

require_method('POST');
rate_limit('reset:'.($_SERVER['REMOTE_ADDR']??'anon'), 5, 3600);

$b = json_body();
$token = trim($b['token'] ?? '');
$newpw = $b['password'] ?? '';
if (!$token || !$newpw) json_error('BAD_REQUEST','Token and new password required');
if (strlen($newpw) < 6) json_error('BAD_REQUEST','Password must be at least 6 characters');

$pdo = db();
// Find token
$stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = :t LIMIT 1");
$stmt->execute([':t'=>$token]);
$pr = $stmt->fetch();
if (!$pr) json_error('INVALID_TOKEN','Invalid or expired token', 400);
if (strtotime($pr['expires_at']) < time()) { json_error('EXPIRED','Token expired', 400); }

// Update user password
$hash = password_hash($newpw, PASSWORD_DEFAULT);
$u = $pdo->prepare("UPDATE users SET password_hash = :h WHERE id = :id");
$u->execute([':h'=>$hash, ':id'=>$pr['user_id']]);

// Delete all tokens for this user
$d = $pdo->prepare("DELETE FROM password_resets WHERE user_id = :id");
$d->execute([':id'=>$pr['user_id']]);

json_ok(['message'=>'Password updated. You can now sign in with your new password.']);
