<?php
/**
 * Department – input validation
 */

function validateDepartment(array $data, bool $isUpdate = false): array {
    $errors = [];

    if (!$isUpdate && empty(trim($data['name'] ?? ''))) {
        $errors[] = 'Department name is required.';
    }
    if (!$isUpdate && empty(trim($data['code'] ?? ''))) {
        $errors[] = 'Department code is required.';
    } elseif (!empty($data['code']) && !preg_match('/^[A-Z]{2,10}$/i', $data['code'])) {
        $errors[] = 'Department code must be 2–10 letters (e.g. SE, CS).';
    }
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address.';
    }
    if (!empty($data['established_year'])) {
        $yr = (int) $data['established_year'];
        if ($yr < 1900 || $yr > (int) date('Y')) {
            $errors[] = 'Established year must be between 1900 and ' . date('Y') . '.';
        }
    }
    if (isset($data['total_students']) && (int) $data['total_students'] < 0) {
        $errors[] = 'Total students cannot be negative.';
    }

    return $errors;
}

function validateCourse(array $data, bool $isUpdate = false): array {
    $errors = [];

    if (!$isUpdate) {
        if (empty(trim($data['course_code'] ?? ''))) $errors[] = 'Course code is required.';
        if (empty(trim($data['course_name'] ?? ''))) $errors[] = 'Course name is required.';
        if (empty($data['department_id']))            $errors[] = 'Department ID is required.';
    }
    if (isset($data['credit_hours']) && ((int)$data['credit_hours'] < 0 || (int)$data['credit_hours'] > 10)) {
        $errors[] = 'Credit hours must be 0–10.';
    }
    if (isset($data['year']) && ((int)$data['year'] < 1 || (int)$data['year'] > 5)) {
        $errors[] = 'Year must be between 1 and 5.';
    }
    if (isset($data['semester']) && !in_array((int)$data['semester'], [1, 2])) {
        $errors[] = 'Semester must be 1 or 2.';
    }

    return $errors;
}
