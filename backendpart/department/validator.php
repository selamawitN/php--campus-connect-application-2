<?php
function validateDepartment($data) {
    if (empty($data['name'])) {
        return "Name is required";
    }

    if (!empty($data['email']) &&
        !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        return "Invalid email";
    }

    return null;
}
?>
