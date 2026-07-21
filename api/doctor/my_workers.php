<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';
session_write_close();

$user = require_role('DOCTOR');
$pdo  = db();

// Fetch workers assigned to this doctor
$s = $pdo->prepare("
    SELECT w.id, w.name, w.email, w.phone, w.status 
    FROM users w
    JOIN doctor_assignments da ON w.id = da.health_worker_id
    WHERE da.doctor_id = :doctor_id
");
$s->execute([':doctor_id' => $user['id']]);
$workers = $s->fetchAll(PDO::FETCH_ASSOC);

// Map to structure expected by pgAssignments' aggregation logic
$result = array_map(fn($w) => [
    'doctor' => ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email']],
    'healthWorker' => $w
], $workers);

json_ok($result);
