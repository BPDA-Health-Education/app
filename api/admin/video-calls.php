<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';
session_write_close();

require_role('ADMIN');
$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];

// ── GET — Admin Queue (all pending/escalated calls) ──────────────────────────
if ($method === 'GET') {
    $rows = $pdo->query("
        SELECT v.*, 
               req.name AS reqName, 
               rec.name AS recName
        FROM video_call_requests v
        JOIN users req ON v.requester_id = req.id
        JOIN users rec ON v.receiver_id = rec.id
        WHERE v.status = 'PENDING'
        ORDER BY v.created_at ASC
    ")->fetchAll();
    
    json_ok($rows);
}

// ── PATCH — Route/Escalate call ──────────────────────────────────────────────
if ($method === 'PATCH') {
    $b = json_body();
    $callId = gp('id');
    $newReceiverId = $b['newReceiverId'] ?? null;
    $action = $b['action'] ?? ''; // 'ESCALATE' or 'ROUTE'

    if (!$callId) json_error('BAD_REQUEST', 'Call ID required');

    if ($action === 'ROUTE' && $newReceiverId) {
        $pdo->prepare("UPDATE video_call_requests SET receiver_id = :rec WHERE id = :id")
            ->execute([':rec' => $newReceiverId, ':id' => $callId]);
        audit('ROUTED', 'VideoCall', $callId, ['newReceiver' => $newReceiverId]);
    } elseif ($action === 'ESCALATE') {
        // Logic for escalation could involve moving to a specific admin pool
        audit('ESCALATED', 'VideoCall', $callId, []);
    } else {
        json_error('BAD_REQUEST', 'Invalid action');
    }

    json_ok(['message' => 'Call updated successfully']);
}
