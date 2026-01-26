<?php
/**
 * Business Details Router
 * 
 * This file handles business details requests from the root level
 * and routes them to the proper views/client/business-details.php file
 */

// Validate that an ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    die('Invalid business ID provided.');
}

// Include the actual business details page
include_once __DIR__ . '/views/client/business-details.php';
?>