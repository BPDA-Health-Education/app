<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

require_method('POST');
$user = require_role('ADMIN');

$b = json_body();
$targetId = trim($b['userId'] ?? '');
$newPw = $b['password'] ?? '';

if (!$targetId) json_error('BAD_REQUEST','Missing userId');
if (!$newPw || strlen($newPw) < 6) json_error('BAD_REQUEST','Password required (min 6 chars)');

$pdo = db();
// Verify target exists and is not ADMIN (optionally allow resetting admins too)
$stmt = $pdo->prepare("SELECT id,role FROM users WHERE id = :id LIMIT 1");
$stmt->execute([':id'=>$targetId]);
$u = $stmt->fetch();
if (!$u) json_error('NOT_FOUND','User not found',404);

// Optional: prevent resetting other admins unless super-admin
if ($u['role'] === 'ADMIN' && $user['id'] !== $u['id']) {
    // Allow if the acting admin is same as target (self-reset) or implement stricter checks.
    // For now permit admin resetting other admins.
}

$hash = password_hash($newPw, PASSWORD_DEFAULT);
$upd = $pdo->prepare("UPDATE users SET password_hash = :h WHERE id = :id");
$upd->execute([':h'=>$hash,':id'=>$targetId]);

// Audit log
audit('RESET_PASSWORD','user',$targetId,['by'=>$user['id']]);

json_ok(['message'=>'Password updated']);
