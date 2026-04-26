<?php
session_start();

/**
 * Check if user is logged in and has the required role.
 * @param array $allowed_roles
 */
function check_auth($allowed_roles = []) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../user/login.php");
        exit();
    }

    if (!empty($allowed_roles) && !in_array($_SESSION['role'], $allowed_roles)) {
        // Unauthorized role
        die("Unauthorized access. You do not have permission to view this page.");
    }
}

/**
 * Sanitize output
 */
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>
