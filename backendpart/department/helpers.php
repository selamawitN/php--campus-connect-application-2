<?php
/**
 * Department – shared response helpers
 */

function sendJSON($data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function sendError(string $message, int $status = 400, array $extra = []): void {
    sendJSON(array_merge(['success' => false, 'message' => $message], $extra), $status);
}

function sendSuccess($data = null, string $message = 'OK'): void {
    $payload = ['success' => true, 'message' => $message];
    if ($data !== null) $payload['data'] = $data;
    sendJSON($payload, 200);
}

function logAudit(mysqli $conn, ?int $deptId, string $action, ?int $userId, string $details = ''): void {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $stmt = $conn->prepare(
        "INSERT INTO department_audit_log (department_id, action, performed_by, details, ip_address)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('isiss', $deptId, $action, $userId, $details, $ip);
    $stmt->execute();
    $stmt->close();
}
