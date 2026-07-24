<?php
/**
 * Video Call Escalation Tracking
 * Endpoint: GET /api/video/escalations.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

$user = require_auth();
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_error('METHOD_NOT_ALLOWED', 'Only GET allowed');
}

// Admin can view all escalations, doctor can view their own, health workers their initiated
$where = [];
$params = [];

if ($user['role'] === 'ADMIN') {
    // Admins see all escalations
} elseif ($user['role'] === 'DOCTOR') {
    // Doctors see escalations where they were originally assigned
    $where[] = 'assigned_doctor_id=:uid';
    $params[':uid'] = $user['id'];
} elseif ($user['role'] === 'HEALTH_WORKER') {
    // Health workers see escalations they initiated
    $where[] = 'health_worker_id=:uid';
    $params[':uid'] = $user['id'];
}

$wStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$rows = $pdo->prepare("SELECT e.*,
    hw.name AS healthWorkerName,
    doc.name AS doctorName,
    admin.name AS adminName
    FROM video_call_escalations e
    JOIN users hw ON hw.id=e.health_worker_id
    JOIN users doc ON doc.id=e.assigned_doctor_id
    LEFT JOIN users admin ON admin.id=e.escalated_to_id
    $wStr
    ORDER BY e.created_at DESC
    LIMIT 100")
    ->execute($params)->fetchAll();

json_ok(array_map(fn($r) => [
    'id'             => $r['id'],
    'originalCallId' => $r['original_call_id'],
    'healthWorker'   => ['id' => $r['health_worker_id'], 'name' => $r['healthWorkerName']],
    'assignedDoctor' => ['id' => $r['assigned_doctor_id'], 'name' => $r['doctorName']],
    'escalatedTo'    => $r['escalated_to_id'] ? ['id' => $r['escalated_to_id'], 'name' => $r['adminName']] : null,
    'reason'         => $r['escalation_reason'],
    'status'         => $r['status'],
    'createdAt'      => $r['created_at'],
], $rows));
