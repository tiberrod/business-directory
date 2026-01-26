<?php
/**
 * cPanel Compatible API Entry Point
 * 
 * This file handles API routing for cPanel deployments where PATH_INFO
 * is not supported. It uses query parameters instead.
 * 
 * Usage Examples:
 * GET  index.php?endpoint=business - Get all businesses
 * POST index.php?endpoint=business - Create new business
 * GET  index.php?endpoint=business&id=123 - Get specific business
 * PUT  index.php?endpoint=business&id=123 - Update business
 * DELETE index.php?endpoint=business&id=123 - Delete business
 * GET  index.php?endpoint=search&name=test - Search businesses
 */

// Enhanced CORS headers for all environments
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

// Ensure output buffering is off for API responses
if (ob_get_level()) {
    ob_end_clean();
}

// Include required files
require_once __DIR__ . "/app/config/database.php";
require_once __DIR__ . "/app/controllers/v1/BusinessController.php";

try {
    // Initialize database connection
    $database = new Database();
    $db = $database->getConnection();
    $controller = new BusinessControllerV1($db);

    // Parse the request
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Get endpoint from query parameter (cPanel compatible)
    $endpoint = $_GET['endpoint'] ?? '';
    $id = $_GET['id'] ?? null;
    $version = $_GET['version'] ?? 'v1';
    
    // If no endpoint in query, try to parse from PATH_INFO (local dev fallback)
    if (empty($endpoint) && isset($_SERVER['PATH_INFO'])) {
        $path = trim($_SERVER['PATH_INFO'], '/');
        $pathParts = explode('/', $path);
        
        // Handle v1 API structure: /api/v1/endpoint or /api/v1/endpoint/id
        if (count($pathParts) >= 3 && $pathParts[0] === 'api' && $pathParts[1] === 'v1') {
            $endpoint = $pathParts[2] ?? '';
            // Only override ID if not already set in query params and exists in path
            $id = !empty($_GET['id']) ? $_GET['id'] : ($pathParts[3] ?? $id);
            $version = 'v1';
        }
        // Handle direct API structure: /api/endpoint or /api/endpoint/id  
        elseif (count($pathParts) >= 2 && $pathParts[0] === 'api') {
            $endpoint = $pathParts[1] ?? '';
            // Only override ID if not already set in query params and exists in path  
            $id = !empty($_GET['id']) ? $_GET['id'] : ($pathParts[2] ?? $id);
        }
        // Fallback for simple structure
        else {
            $endpoint = $pathParts[0] ?? '';
            $id = $pathParts[1] ?? $id;
        }
    }
    
    // Debug logging for troubleshooting
    error_log("API Debug - Endpoint: '$endpoint', Method: $method, ID: $id, Version: $version");
    error_log("API Debug - GET params: " . json_encode($_GET));
    error_log("API Debug - REQUEST_URI: " . $_SERVER['REQUEST_URI']);
    error_log("API Debug - PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'not_set'));
    
    // Default route - show client frontend if no endpoint specified
    if (empty($endpoint) && $method === 'GET') {
        include __DIR__ . '/views/client/index.php';
        exit();
    }
    
    // Set content type for API responses
    header('Content-Type: application/json; charset=utf-8');
    
    // Route to appropriate endpoint
    switch ($endpoint) {
        case '':
        case 'info':
            // API Information
            echo json_encode([
                'status' => 200,
                'message' => 'Apploqic Business Directory API - cPanel Compatible',
                'version' => '1.0.0',
                'endpoints' => [
                    'GET /api/business' => 'Get all businesses',
                    'POST /api/business' => 'Create new business',
                    'GET /api/business/{id}' => 'Get specific business',
                    'PUT /api/business/{id}' => 'Update business',
                    'DELETE /api/business/{id}' => 'Delete business',
                    'GET /api/search?name={query}' => 'Search businesses',
                    'GET index.php?endpoint=business' => 'Get all businesses (direct)',
                    'GET index.php?endpoint=business&id={id}' => 'Get specific business (direct)'
                ],
                'base_url' => 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']),
                'timestamp' => date('Y-m-d H:i:s'),
                'deployment' => 'cPanel',
                'debug' => [
                    'received_endpoint' => $endpoint,
                    'received_id' => $id,
                    'method' => $method,
                    'get_params' => $_GET,
                    'request_uri' => $_SERVER['REQUEST_URI']
                ]
            ]);
            break;

        case 'business':
            business_case: // Label for goto
            // Business endpoint
            
            // Enhanced debugging for production environment
            error_log("=== INDEX.PHP BUSINESS ROUTING ===");
            error_log("Method: $method");
            error_log("ID: " . var_export($id, true));
            error_log("Endpoint: $endpoint");
            error_log("Request URI: " . $_SERVER['REQUEST_URI']);
            error_log("GET params: " . json_encode($_GET));
            error_log("POST params: " . json_encode($_POST));
            error_log("Raw input: " . file_get_contents('php://input'));
            
            // Handle placeholder {id} in URL path - this should return an error
            if ($id === '{id}' || $id === '%7Bid%7D') {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid business ID: placeholder detected',
                    'received_id' => $id,
                    'expected' => 'Numeric ID (e.g., /api/v1/business/123)',
                    'api_version' => 'v1'
                ]);
                break;
            }
            
            if ($id && $method === 'GET') {
                // Validate ID is numeric
                if (!is_numeric($id)) {
                    http_response_code(400);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Invalid business ID format',
                        'received_id' => $id,
                        'expected' => 'Numeric ID',
                        'api_version' => 'v1'
                    ]);
                    break;
                }
                // Get specific business
                $controller->show($id);
            } elseif ($id && $method === 'PUT') {
                // Update business - set ID in $_GET for controller compatibility
                $_GET['id'] = $id;
                $_POST['id'] = $id; // Also set in POST for form compatibility
                $controller->update();
            } elseif ($id && $method === 'POST') {
                // Update business via POST (for frontend compatibility)
                error_log("INDEX.PHP: POST with ID detected - treating as UPDATE");
                $_GET['id'] = $id;
                $_POST['id'] = $id; // Also set in POST for form compatibility
                $controller->update();
            } elseif ($method === 'GET') {
                // Get all businesses (no ID provided)
                $controller->index();
            } elseif ($method === 'POST') {
                // Create new business (no ID) - check for data in multiple formats
                $hasFormData = !empty($_POST) || !empty($_FILES);
                $jsonInput = json_decode(file_get_contents('php://input'), true);
                $hasJsonData = !empty($jsonInput);
                
                if (!$hasFormData && !$hasJsonData) {
                    // No data provided at all
                    http_response_code(400);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Missing required fields for business creation',
                        'required_fields' => ['business_name', 'business_contact'],
                        'alternative_fields' => ['name', 'contact'],
                        'optional_fields' => ['business_description', 'business_category', 'business_img', 'is_featured'],
                        'example' => [
                            'business_name' => 'Business Name',
                            'business_contact' => '123-456-7890',
                            'business_description' => 'Business description',
                            'business_category' => 'retail'
                        ],
                        'formats_accepted' => ['form-data', 'application/json'],
                        'api_version' => 'v1'
                    ]);
                    break;
                }
                
                // If we have JSON data, populate $_POST for controller compatibility
                if ($hasJsonData && !$hasFormData) {
                    foreach ($jsonInput as $key => $value) {
                        $_POST[$key] = $value;
                    }
                    
                    // Handle field name mapping for backward compatibility
                    if (isset($jsonInput['name']) && !isset($_POST['business_name'])) {
                        $_POST['business_name'] = $jsonInput['name'];
                    }
                    if (isset($jsonInput['contact']) && !isset($_POST['business_contact'])) {
                        $_POST['business_contact'] = $jsonInput['contact'];
                    }
                    if (isset($jsonInput['description']) && !isset($_POST['business_description'])) {
                        $_POST['business_description'] = $jsonInput['description'];
                    }
                    if (isset($jsonInput['category']) && !isset($_POST['business_category'])) {
                        $_POST['business_category'] = $jsonInput['category'];
                    }
                }
                
                $controller->store();
            } elseif ($method === 'DELETE') {
                // DELETE /api/v1/business - ID should come from request body (according to documentation)
                $controller->delete();
            } elseif ($method === 'PUT' && !$id) {
                // PUT without ID
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Business ID is required for update operation',
                    'required_format' => 'PUT /api/v1/business/{id}',
                    'example' => 'PUT /api/v1/business/123',
                    'api_version' => 'v1'
                ]);
            } else {
                http_response_code(405);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Method not allowed for this endpoint',
                    'endpoint' => $endpoint,
                    'method' => $method,
                    'id' => $id,
                    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],
                    'examples' => [
                        'GET /api/v1/business' => 'Get all businesses',
                        'GET /api/v1/business/{id}' => 'Get specific business',
                        'POST /api/v1/business' => 'Create new business',
                        'PUT /api/v1/business/{id}' => 'Update business',
                        'DELETE /api/v1/business/{id}' => 'Delete business'
                    ]
                ]);
            }
            break;

        case 'search':
            // Search businesses
            $controller->search();
            break;

        case 'analytics':
            // Analytics endpoint
            if ($method === 'GET') {
                // Use the controller's analytics method
                $controller->analytics();
            } else {
                http_response_code(405);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Method not allowed for analytics endpoint',
                    'allowed_methods' => ['GET']
                ]);
            }
            break;

        case 'reactivate':
            // Reactivate business endpoint
            if ($method === 'PUT') {
                // Handle placeholder {id} in URL path
                if ($id === '{id}' || $id === '%7Bid%7D') {
                    $id = null; // Treat placeholder as empty
                }
                
                if (!$id) {
                    http_response_code(400);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Business ID is required for reactivation',
                        'required_format' => 'PUT /api/v1/reactivate/{id}',
                        'example' => 'PUT /api/v1/reactivate/123'
                    ]);
                    break;
                }
                
                // Set ID in $_GET for controller compatibility
                $_GET['id'] = $id;
                $controller->reactivate();
            } else {
                http_response_code(405);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Method not allowed for reactivate endpoint',
                    'allowed_methods' => ['PUT'],
                    'required_format' => 'PUT /api/v1/reactivate/{id}'
                ]);
            }
            break;

        default:
            // Check if this might be a misrouted API call
            if ($endpoint === 'api' && isset($_GET['id'])) {
                // This suggests a URL like /api?id=6 was accessed
                // Redirect to the business endpoint
                error_log("API Debug - Detected misrouted API call, redirecting to business endpoint");
                $endpoint = 'business';
                $id = $_GET['id'];
                // Fall through to business case
                goto business_case;
            }
            
            // Serve the frontend if no API endpoint is specified
            if (empty($endpoint) && $method === 'GET' && !isset($_GET['endpoint'])) {
                // Serve the main index.html
                if (file_exists(__DIR__ . '/index.html')) {
                    header('Content-Type: text/html; charset=UTF-8');
                    readfile(__DIR__ . '/index.html');
                    exit();
                }
            }
            
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Endpoint not found',
                'available_endpoints' => ['business', 'search', 'info', 'analytics', 'reactivate'],
                'received_endpoint' => $endpoint,
                'method' => $method,
                'query_params' => $_GET,
                'debug_info' => [
                    'request_uri' => $_SERVER['REQUEST_URI'],
                    'path_info' => $_SERVER['PATH_INFO'] ?? 'not_set',
                    'script_name' => $_SERVER['SCRIPT_NAME'],
                    'version' => $version,
                    'suggestion' => $endpoint === 'api' ? 'Try using index.php?endpoint=business&id=' . ($_GET['id'] ?? '{id}') : 'Use one of the available endpoints'
                ]
            ]);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error',
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>