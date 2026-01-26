<?php
// Admin Dashboard Entry Point
// Redirects to the proper admin interface

// Check authentication first
require_once __DIR__ . '/views/admin/AdminAuth.php';

if (!AdminAuth::isLoggedIn()) {
    // Redirect to login page
    header('Location: views/admin/login.php');
    exit;
}

// Redirect to the proper admin dashboard
header('Location: views/admin/index.php');
exit;
?>