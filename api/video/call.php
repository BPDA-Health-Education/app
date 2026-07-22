<?php
/**
 * Video Call Management & Escalation
 * Endpoints:
 * - POST   /api/video/call.php: Initiate video call
 * - GET    /api/video/call.php: List pending/active calls
 * - PATCH  /api/video/call.php: Accept/decline/end call
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/google.php';
require_once __DIR__ . '/../../config/helpers.php';

$user = require_auth();
$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];
$id = gp('id');

// Helper: Generate Google Meet URL
function generate_meet_url($calleeName) {
    $roomId = strtolower(preg_replace('/[^a-z0-9]/i', '', 'rx-' . $calleeName . '-' . bin2hex(random_bytes(4))));
    $roomId = substr($roomId, 0, 25);
    return "https://meet.google.com/$roomId";
}

// Helper: Check if user is online
function is_user_online($userId) {
    $pdo = db();
    $row = $pdo->prepare("SELECT updated_at FROM users WHERE id=:id")->execute([':id' => $userId])->fetch();
    if (!$row) return false;
    return (time() - strtotime($row['updated_at'])) < 300;
}

// Helper: Get assigned doctor for health worker
function get_assigned_doctor($hwId) {
    $pdo = db();
    $row = $pdo->prepare("SELECT doctor_id FROM doctor_assignments WHERE health_worker_id=:hw")
        ->execute([':hw' => $hwId])->fetch();
    return $row['doctor_id'] ?? null;
}

// Helper: Get first available admin for escalation
function get_available_admin() {
    $pdo = db();
    $row = $pdo->prepare("
        SELECT u.id, COUNT(vc.id) as call_count
        FROM users u
        LEFT JOIN video_call_requests vc ON vc.receiver_id=u.id AND vc.status IN ('PENDING','ACCEPTED')
        WHERE u.role='ADMIN' AND u.status='ACTIVE'
        GROUP BY u.id
        ORDER BY call_count ASC, u.created_at ASC
        LIMIT 1
    ")->execute()->fetch();
    return $row['id'] ?? null;
}

// POST: Initiate video call
if ($method === 'POST') {
    $b = json_body();
    $receiverId = $b['receiverId'] ?? null;
    $note = $b['note'] ?? '';
    
    if (!$receiverId) {
        json_error('BAD_REQUEST', 'receiverId is required');
    }
    
    $receiver = $pdo->prepare("SELECT id, role FROM users WHERE id=:id")->execute([':id' => $receiverId])->fetch();
    if (!$receiver) json_error('NOT_FOUND', 'Receiver not found');
    
    if ($user['role'] === 'HEALTH_WORKER') {
        $assignedDoctor = get_assigned_doctor($user['id']);
        if ($receiverId !== $assignedDoctor && $receiver['role'] !== 'ADMIN') {
            json_error('FORBIDDEN', 'You can only call your assigned doctor');
        }
    }
    
    try {
        $meetUrl = generate_meet_url($user['name']);
        $callId = bin2hex(random_bytes(8));
        
        $pdo->prepare("INSERT INTO video_call_requests(id, requester_id, receiver_id, note, status, created_at)
            VALUES(:id, :req, :rec, :note, 'PENDING', NOW())")
            ->execute([
                ':id'   => $callId,
                ':req'  => $user['id'],
                ':rec'  => $receiverId,
                ':note' => clean($note),
            ]);
        
        $receiverOnline = is_user_online($receiverId);
        $escalated = false;
        
        // Auto-escalate if doctor offline
        if ($user['role'] === 'HEALTH_WORKER' && $receiver['role'] === 'DOCTOR' && !$receiverOnline) {
            $adminId = get_available_admin();
            if ($adminId) {
                $escalId = bin2hex(random_bytes(8));
                $pdo->prepare("INSERT INTO video_call_escalations(id, original_call_id, health_worker_id, assigned_doctor_id, escalated_to_admin, escalated_to_id, status, created_at)
                    VALUES(:id, :call, :hw, :doc, 1, :admin, 'ESCALATED_ADMIN', NOW())")
                    ->execute([
                        ':id'    => $escalId,
                        ':call'  => $callId,
                        ':hw'    => $user['id'],
                        ':doc'   => $receiverId,
                        ':admin' => $adminId,
                    ]);
                
                $pdo->prepare("UPDATE video_call_requests SET receiver_id=:admin WHERE id=:id")
                    ->execute([':admin' => $adminId, ':id' => $callId]);
                
                $receiverId = $adminId;
                $escalated = true;
            }
        }
        
        json_ok([
            'callId'      => $callId,
            'meetUrl'     => $meetUrl,
            'receiverId'  => $receiverId,
            'status'      => 'PENDING',
            'createdAt'   => date('c'),
            'escalated'   => $escalated,
        ], 201);
        
    } catch (Exception $e) {
        json_error('INTERNAL_ERROR', 'Failed to initiate call: ' . $e->getMessage(), 500);
    }
}

// GET: List calls
if ($method === 'GET' && !$id) {
    $status = gp('status', '');
    $role = gp('role', '');
    
    $where = [];
    $params = [];
    
    if ($role === 'received') {
        $where[] = 'receiver_id=:uid';
    } elseif ($role === 'sent') {
        $where[] = 'requester_id=:uid';
    } else {
        $where[] = '(receiver_id=:uid OR requester_id=:uid)';
    }
    $params[':uid'] = $user['id'];
    
    if ($status && in_array($status, ['PENDING', 'ACCEPTED', 'DECLINED', 'COMPLETED'])) {
        $where[] = 'status=:st';
        $params[':st'] = $status;
    }
    
    $wStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    $rows = $pdo->prepare("SELECT v.*, 
        req.name AS requesterName, req.role AS requesterRole,
        rec.name AS receiverName, rec.role AS receiverRole
        FROM video_call_requests v
        JOIN users req ON req.id=v.requester_id
        JOIN users rec ON rec.id=v.receiver_id
        $wStr
        ORDER BY v.created_at DESC
        LIMIT 50")
        ->execute($params)->fetchAll();
    
    json_ok(array_map(fn($r) => [
        'id'           => $r['id'],
        'requester'    => ['id' => $r['requester_id'], 'name' => $r['requesterName'], 'role' => $r['requesterRole']],
        'receiver'     => ['id' => $r['receiver_id'], 'name' => $r['receiverName'], 'role' => $r['receiverRole']],
        'note'         => $r['note'],
        'status'       => $r['status'],
        'createdAt'    => $r['created_at'],
    ], $rows));
}

// GET: Single call details
if ($method === 'GET' && $id) {
    $row = $pdo->prepare("SELECT v.*, 
        req.name AS requesterName, req.role AS requesterRole,
        rec.name AS receiverName, rec.role AS receiverRole
        FROM video_call_requests v
        JOIN users req ON req.id=v.requester_id
        JOIN users rec ON rec.id=v.receiver_id
        WHERE v.id=:id")
        ->execute([':id' => $id])->fetch();
    
    if (!$row) json_error('NOT_FOUND', 'Call not found');
    
    json_ok([
        'id'           => $row['id'],
        'requester'    => ['id' => $row['requester_id'], 'name' => $row['requesterName']],
        'receiver'     => ['id' => $row['receiver_id'], 'name' => $row['receiverName']],
        'note'         => $row['note'],
        'status'       => $row['status'],
        'createdAt'    => $row['created_at'],
    ]);
}

// PATCH: Accept/decline/end call
if ($method === 'PATCH' && $id) {
    $b = json_body();
    $action = $b['action'] ?? null;
    
    if (!in_array($action, ['accept', 'decline', 'end'])) {
        json_error('BAD_REQUEST', "action must be 'accept', 'decline', or 'end'");
    }
    
    $row = $pdo->prepare("SELECT * FROM video_call_requests WHERE id=:id")->execute([':id' => $id])->fetch();
    if (!$row) json_error('NOT_FOUND', 'Call not found');
    
    if (($action === 'accept' || $action === 'decline') && $row['receiver_id'] !== $user['id']) {
        json_error('FORBIDDEN', 'Only receiver can ' . $action);
    }
    if ($action === 'end' && $row['receiver_id'] !== $user['id'] && $row['requester_id'] !== $user['id']) {
        json_error('FORBIDDEN', 'Only call participants can end');
    }
    
    try {
        $newStatus = $action === 'accept' ? 'ACCEPTED' : ($action === 'decline' ? 'DECLINED' : 'COMPLETED');
        
        $pdo->prepare("UPDATE video_call_requests SET status=:st, updated_at=NOW() WHERE id=:id")
            ->execute([':st' => $newStatus, ':id' => $id]);
        
        json_ok(['status' => $newStatus, 'message' => ucfirst($action) . ' successfully']);
        
    } catch (Exception $e) {
        json_error('INTERNAL_ERROR', 'Failed to update call: ' . $e->getMessage(), 500);
    }
}

json_error('METHOD_NOT_ALLOWED', 'Invalid request method');
