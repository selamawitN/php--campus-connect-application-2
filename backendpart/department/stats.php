<?php
/**
 * GET /backendpart/department/stats.php?id=1
 *
 * Returns rich statistics for a department:
 * course count by year, credit hour totals, audit trail, etc.
 */

require_once '../config/db.php';
require_once 'helpers.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') sendError('GET only.', 405);

$id = (int)($_GET['id'] ?? 0);
if (!$id) sendError('Department ID required.', 400);

// ── department ─────────────────────────────────────────────
$s = $conn->prepare('SELECT id, name, code, head, total_students, established_year FROM departments WHERE id=?');
$s->bind_param('i', $id);
$s->execute();
$dept = $s->get_result()->fetch_assoc();
$s->close();
if (!$dept) sendError('Department not found.', 404);

// ── courses by year ────────────────────────────────────────
$s2 = $conn->prepare(
    'SELECT year, semester,
            COUNT(*) AS course_count,
            SUM(credit_hours) AS total_credits,
            SUM(is_elective) AS elective_count
     FROM courses WHERE department_id=?
     GROUP BY year, semester ORDER BY year, semester'
);
$s2->bind_param('i', $id);
$s2->execute();
$byYear = $s2->get_result()->fetch_all(MYSQLI_ASSOC);
$s2->close();

// ── total courses & credits ────────────────────────────────
$s3 = $conn->prepare(
    'SELECT COUNT(*) AS total_courses, SUM(credit_hours) AS total_credits
     FROM courses WHERE department_id=?'
);
$s3->bind_param('i', $id);
$s3->execute();
$totals = $s3->get_result()->fetch_assoc();
$s3->close();

// ── recent audit log ───────────────────────────────────────
$s4 = $conn->prepare(
    'SELECT dal.*, u.fullname AS actor
     FROM department_audit_log dal
     LEFT JOIN users u ON u.id = dal.performed_by
     WHERE dal.department_id = ?
     ORDER BY dal.created_at DESC LIMIT 10'
);
$s4->bind_param('i', $id);
$s4->execute();
$auditLog = $s4->get_result()->fetch_all(MYSQLI_ASSOC);
$s4->close();

sendSuccess([
    'department'    => $dept,
    'totals'        => $totals,
    'by_year'       => $byYear,
    'audit_log'     => $auditLog,
], 'Department stats fetched');
