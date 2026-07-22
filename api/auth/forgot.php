<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

require_method('POST');
rate_limit('forgot:'.($_SERVER['REMOTE_ADDR']??'anon'), 5, 3600);

$b = json_body();
$identifier = trim($b['identifier'] ?? '');
if (!$identifier) json_error('BAD_REQUEST','Please provide email or phone');

$pdo = db();
$field = preg_match('/^01/', $identifier) ? 'phone' : 'email';
$stmt = $pdo->prepare("SELECT id,email,phone,name FROM users WHERE $field = :id LIMIT 1");
$stmt->execute([':id'=>$identifier]);
$user = $stmt->fetch();

// Create password_resets table if not exists (safe to run repeatedly)
$pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
  id VARCHAR(36) NOT NULL PRIMARY KEY,
  user_id VARCHAR(36) NOT NULL,
  token VARCHAR(128) NOT NULL,
  expires_at DATETIME NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (user_id), INDEX (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$token = bin2hex(random_bytes(16));

if ($user){
  $expires = date('Y-m-d H:i:s', time() + 3600);
  $id = bin2hex(random_bytes(8));
  $ins = $pdo->prepare("INSERT INTO password_resets(id,user_id,token,expires_at) VALUES(:id,:uid,:token,:exp)");
  $ins->execute([':id'=>$id,':uid'=>$user['id'],':token'=>$token,':exp'=>$expires]);

  // TODO: send email/SMS with tokenized link. For now we return a success message.
  // In production, DO NOT return the token in response.
  json_ok(['message'=>'If the account exists, a password reset link has been sent.','token'=>$token]);
}

// Always return success to avoid revealing which identifiers exist
json_ok(['message'=>'If the account exists, a password reset link has been sent.']);
