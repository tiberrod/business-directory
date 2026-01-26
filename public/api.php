<?php
/**
 * Main API Entry Point for Frontend Integration
 * 
 * This is the SINGLE entry point that frontend developers should use
 * URL: /public/api.php
 * 
 * Usage Examples:
 * GET  /api.php/business - Get all businesses
 * POST /api.php/business - Create new business
 * GET  /api.php/business/123 - Get specific business
 * PUT  /api.php/business/123 - Update business
 * DELETE /api.php/business/123 - Delete business
 * GET  /api.php/business/search?name=test - Search businesses
 */

// Enhanced CORS headers for all environments
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Max-Age: 3600");

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once __DIR__ . "/../app/config/database.php";
require_once __DIR__ . "/../app/controllers/v1/BusinessController.php";
require_once __DIR__ . "/../app/models/BusinessDetail.php";

try {
    // Initialize database connection
    $database = new Database();
    $db = $database->getConnection();
    $controller = new BusinessControllerV1($db);

    // Parse the request
    $method = $_SERVER['REQUEST_METHOD'];
    $path = $_SERVER['PATH_INFO'] ?? '';
    $path = trim($path, '/');
    $pathParts = explode('/', $path);
    
    // Extract endpoint and ID
    $endpoint = $pathParts[0] ?? '';
    $id = $pathParts[1] ?? null;
    $action = $pathParts[2] ?? null;

    // Route to appropriate endpoint
    switch ($endpoint) {
        case '':
        case 'info':
            // API Information
            echo json_encode([
                'status' => 'success',
                'message' => 'Apploqic Business Directory API',
                'version' => '1.0.0',
                'endpoints' => [
                    'GET /api.php/business' => 'Get all businesses',
                    'POST /api.php/business' => 'Create new business',
                    'GET /api.php/business/{id}' => 'Get specific business',
                    'PUT /api.php/business/{id}' => 'Update business',
                    'DELETE /api.php/business/{id}' => 'Delete business',
                    'GET /api.php/business/search?name={query}' => 'Search businesses'
                ],
                'base_url' => 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            break;

        case 'business':
            // Business endpoint
            if ($action === 'search') {
                // Search businesses
                $controller->search();
            } elseif ($id && $method === 'GET') {
                // Get specific business
                $_GET['id'] = $id;
                $controller->show($id);
            } elseif ($id && $method === 'PUT') {
                // Update business
                $_GET['id'] = $id;
                $controller->update();
            } elseif ($id && $method === 'DELETE') {
                // Delete business
                $_GET['id'] = $id;
                $controller->delete();
            } elseif ($method === 'GET') {
                // Get all businesses
                $controller->index();
            } elseif ($method === 'POST') {
                // Create new business
                $controller->store();
            } else {
                http_response_code(405);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Method not allowed for this endpoint'
                ]);
            }
            break;

        default:
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Endpoint not found',
                'available_endpoints' => ['business', 'info']
            ]);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error',
        'error' => $e->getMessage()
    ]);
}
?>