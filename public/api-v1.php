<?php
/**
 * API v1 Router
 * 
 * Handles all v1 API routing with proper versioning structure
 * Routes: /api/v1/{endpoint}
 */

// Enhanced CORS headers for cPanel compatibility
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

require_once __DIR__ ."/../app/config/database.php";
require_once __DIR__ ."/../app/controllers/v1/BusinessController.php";
require_once __DIR__ ."/../app/controllers/v1/DocumentationController.php";
require_once __DIR__ ."/../app/models/BusinessDetail.php";

$database = new Database();
$db = $database->getConnection();

// Parse the URL to determine the endpoint and parameters
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Handle method override for file uploads (POST with _method parameter)
if ($method === 'POST' && isset($_POST['_method'])) {
    $overrideMethod = strtoupper($_POST['_method']);
    if (in_array($overrideMethod, ['PUT', 'PATCH', 'DELETE'])) {
        $method = $overrideMethod;
        error_log("API Debug - Method override detected: POST -> " . $method);
    }
}

// Debug URL parsing (remove in production)
error_log("API Debug - Full URI: " . $uri);
error_log("API Debug - Path: " . $path);

// Handle different URL formats
$pathParts = explode('/', trim($path, '/'));
$endpoint = '';
$id = null;
$version = 'v1';

// Check if URL contains the api-v1.php file in the path
if (strpos($path, 'api-v1.php') !== false) {
    // Extract path after api-v1.php
    $apiParts = explode('api-v1.php', $path);
    if (isset($apiParts[1]) && !empty(trim($apiParts[1], '/'))) {
        $remainingPath = trim($apiParts[1], '/');
        $pathParts = explode('/', $remainingPath);
        
        // Handle /api/v1/endpoint format
        if (isset($pathParts[0]) && $pathParts[0] === 'api') {
            if (isset($pathParts[1]) && preg_match('/^v\d+$/', $pathParts[1])) {
                $version = $pathParts[1];
                $endpoint = $pathParts[2] ?? '';
                $id = $pathParts[3] ?? null;
            } else {
                $endpoint = $pathParts[1] ?? '';
                $id = $pathParts[2] ?? null;
            }
        } else {
            // Direct endpoint access
            $endpoint = $pathParts[0] ?? '';
            $id = $pathParts[1] ?? null;
        }
    }
} else {
    // Handle direct calls without api-v1.php in path (for cleaner URLs)
    // Remove common prefixes and parse
    $cleanPath = preg_replace('/^\/?(public\/)?/', '', $path);
    $pathParts = explode('/', trim($cleanPath, '/'));
    
    // Look for api/v1 pattern
    $apiIndex = array_search('api', $pathParts);
    if ($apiIndex !== false && isset($pathParts[$apiIndex + 1]) && preg_match('/^v\d+$/', $pathParts[$apiIndex + 1])) {
        $version = $pathParts[$apiIndex + 1];
        $endpoint = $pathParts[$apiIndex + 2] ?? '';
        $id = $pathParts[$apiIndex + 3] ?? null;
    }
}

// Also check query parameters for backward compatibility and v1 specific routing
if (empty($endpoint)) {
    $endpoint = $_GET['v1_endpoint'] ?? $_GET['endpoint'] ?? '';
    $id = $_GET['v1_id'] ?? $_GET['id'] ?? null;
}

// Debug output
error_log("API Debug - Final endpoint: " . $endpoint);
error_log("API Debug - Final id: " . ($id ?? 'null'));
error_log("API Debug - Method: " . $method);

// Route based on version
switch ($version) {
    case 'v1':
        routeV1($endpoint, $id, $method, $db);
        break;
    
    case 'v2':
        // Future v2 implementation
        http_response_code(501);
        echo json_encode([
            'status' => 501,
            'message' => 'API v2 is not implemented yet. Please use v1.',
            'available_versions' => ['v1']
        ]);
        break;
    
    default:
        http_response_code(400);
        echo json_encode([
            'status' => 400,
            'message' => 'Invalid API version. Supported versions: v1',
            'available_versions' => ['v1']
        ]);
        break;
}

/**
 * Route API v1 requests
 */
function routeV1($endpoint, $id, $method, $db) {
    switch ($endpoint) {
        case 'business':
            $controller = new BusinessControllerV1($db);
            handleBusinessEndpoints($controller, $id, $method);
            break;
            
        case 'businesses':
            $controller = new BusinessControllerV1($db);
            if ($method === 'GET') {
                $controller->index(); // List all businesses
            } else {
                sendMethodNotAllowed('Only GET is supported for businesses listing.');
            }
            break;
            
        case 'search':
            $controller = new BusinessControllerV1($db);
            if ($method === 'GET') {
                $controller->search();
            } else {
                sendMethodNotAllowed('Only GET is supported for search.');
            }
            break;
            
        case 'analytics':
            $controller = new BusinessControllerV1($db);
            if ($method === 'GET') {
                $controller->analytics();
            } else {
                sendMethodNotAllowed('Only GET is supported for analytics.');
            }
            break;
            
        case 'reactivate':
            $controller = new BusinessControllerV1($db);
            if ($method === 'PUT' || $method === 'POST') {
                // Handle ID from URL path parameter for reactivate endpoint
                if ($id) {
                    $_GET['id'] = $id; // Set ID for controller compatibility
                }
                $controller->reactivate();
            } else {
                sendMethodNotAllowed('Only PUT/POST is supported for reactivate.');
            }
            break;
            
        case 'docs':
        case 'documentation':
            $controller = new DocumentationControllerV1($db);
            handleDocumentationEndpoints($controller, $id, $method);
            break;
            
        case 'version':
            $controller = new DocumentationControllerV1($db);
            if ($method === 'GET') {
                $controller->version();
            } else {
                sendMethodNotAllowed('Only GET is supported for version.');
            }
            break;
            
        case 'status':
        case 'health':
            if ($method === 'GET') {
                sendHealthCheck();
            } else {
                sendMethodNotAllowed('Only GET is supported for health check.');
            }
            break;
            
        case 'debug':
            if ($method === 'GET') {
                sendDebugInfo($endpoint, $id, $method);
            } else {
                sendMethodNotAllowed('Only GET is supported for debug.');
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'Endpoint not found',
                'available_endpoints' => [
                    '/api/v1/business (GET all, POST new)',
                    '/api/v1/business/{id} (GET, PUT, DELETE specific)',
                    '/api/v1/businesses (GET all - alias)',
                    '/api/v1/search', 
                    '/api/v1/analytics',
                    '/api/v1/reactivate/{id} (PUT/POST)',
                    '/api/v1/docs',
                    '/api/v1/version',
                    '/api/v1/status',
                    '/api/v1/debug'
                ]
            ]);
            break;
    }
}

/**
 * Handle business-related endpoints
 */
function handleBusinessEndpoints($controller, $id, $method) {
    switch($method) {
        case 'GET':
            if($id) {
                if ($id === 'search') {
                    $controller->search();
                } else {
                    // Get specific business by ID
                    $controller->show((int)$id);
                }
            } else {
                // GET /api/v1/business without ID - return all businesses
                // This is now the standard behavior for listing businesses
                $controller->index();
            }
            break;
            
        case 'POST':
            $controller->store();
            break;
            
        case 'PUT':
            if($id) {
                // Set ID in $_GET for compatibility with update method
                $_GET['id'] = $id;
            }
            $controller->update();
            break;
            
        case 'DELETE':
            if($id) {
                // Set ID in $_GET for compatibility with delete method
                $_GET['id'] = $id;
            }
            $controller->delete();
            break;
            
        default:
            sendMethodNotAllowed('Supported methods: GET, POST, PUT, DELETE');
            break;
    }
}

/**
 * Handle documentation endpoints
 */
function handleDocumentationEndpoints($controller, $id, $method) {
    if ($method !== 'GET') {
        sendMethodNotAllowed('Only GET is supported for documentation.');
        return;
    }
    
    switch ($id) {
        case 'overview':
            $controller->overview();
            break;
            
        case 'endpoints':
            $controller->endpointList();
            break;
            
        case 'patches':
            $controller->patches();
            break;
            
        default:
            $controller->version();
            break;
    }
}

/**
 * Send health check response
 */
function sendHealthCheck() {
    http_response_code(200);
    echo json_encode([
        'status' => 200,
        'message' => 'API v1 is healthy',
        'version' => 'v1.0',
        'timestamp' => date('c'),
        'endpoints_available' => true
    ]);
}

/**
 * Send method not allowed response
 */
function sendMethodNotAllowed($message) {
    http_response_code(405);
    echo json_encode([
        'status' => 405,
        'message' => 'Method Not Allowed. ' . $message
    ]);
}

/**
 * Send debug information response
 */
function sendDebugInfo($endpoint, $id, $method) {
    global $uri, $path;
    
    http_response_code(200);
    echo json_encode([
        'status' => 200,
        'message' => 'Debug information',
        'request_info' => [
            'original_uri' => $_SERVER['REQUEST_URI'],
            'parsed_path' => $path,
            'method' => $method,
            'detected_endpoint' => $endpoint,
            'detected_id' => $id,
            'query_params' => $_GET,
            'path_info' => $_SERVER['PATH_INFO'] ?? 'Not set',
            'script_name' => $_SERVER['SCRIPT_NAME']
        ],
        'correct_usage' => [
            'all_businesses' => 'GET /api/v1/business (or /api/v1/businesses)',
            'specific_business' => 'GET /api/v1/business/{id}',
            'create_business' => 'POST /api/v1/business',
            'update_business' => 'PUT /api/v1/business/{id}',
            'delete_business' => 'DELETE /api/v1/business/{id}',
            'search' => 'GET /api/v1/search?name={query}',
            'analytics' => 'GET /api/v1/analytics'
        ]
    ]);
}