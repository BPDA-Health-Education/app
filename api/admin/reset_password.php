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

// Prevent resetting other ADMINs unless acting admin is the configured SUPER_ADMIN_ID
if ($u['role'] === 'ADMIN' && $user['id'] !== $u['id']) {
    if (defined('SUPER_ADMIN_ID') && SUPER_ADMIN_ID) {
        if ($user['id'] !== SUPER_ADMIN_ID) {
            json_error('FORBIDDEN','Only the super-admin may reset other ADMIN passwords',403);
        }
    } else {
        // If SUPER_ADMIN_ID not configured, deny resetting other ADMINs by default
        json_error('FORBIDDEN','Resetting other ADMIN accounts is disabled. Configure SUPER_ADMIN_ID to allow.',403);
    }
}

$hash = password_hash($newPw, PASSWORD_DEFAULT);
$upd = $pdo->prepare("UPDATE users SET password_hash = :h WHERE id = :id");
$upd->execute([':h'=>$hash,':id'=>$targetId]);

// Audit log
audit('RESET_PASSWORD','user',$targetId,['by'=>$user['id']]);

json_ok(['message'=>'Password updated']);
