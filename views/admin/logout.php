<?php
/**
 * Admin Logout Handler
 */

require_once 'AdminAuth.php';

// Perform logout
AdminAuth::logout();

// Redirect to login page with success message
header('Location: login.php?logged_out=1');
exit;
?>