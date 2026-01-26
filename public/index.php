<?php
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
require_once __DIR__ ."/../app/models/BusinessDetail.php";

$database = new Database();
$db = $database->getConnection();
$controller = new BusinessControllerV1($db);

// parse the URL to determine the endpoint and parameters
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Handle both query parameter and path-based routing
$endpoint = $_GET['endpoint'] ?? '';
$id = $_GET['id'] ?? null;

// Also check PATH_INFO for /api/business format
if (isset($_SERVER['PATH_INFO'])) {
    $pathInfo = trim($_SERVER['PATH_INFO'], '/');
    $pathParts = explode('/', $pathInfo);
    if ($pathParts[0] === 'api' && $pathParts[1] === 'business') {
        $endpoint = 'business';
        $id = $pathParts[2] ?? null;
    }
}

// Check URL path for /api/business format
$url = explode('/', trim($path, '/'));
$apiIndex = array_search('api', $url);
if ($apiIndex !== false && isset($url[$apiIndex + 1])) {
    if ($url[$apiIndex + 1] === 'business') {
        $endpoint = 'business';
        $id = $url[$apiIndex + 2] ?? null;
        
        // Check for search endpoint: /api/business/search
        if ($id === 'search') {
            $endpoint = 'search';
            $id = null;
        }
    } elseif ($url[$apiIndex + 1] === 'search') {
        // Direct search endpoint: /api/search
        $endpoint = 'search';
    }
}

// Handle search endpoint
if ($endpoint === 'search') {
    if ($method === 'GET') {
        $controller->search();
    } else {
        http_response_code(405);
        echo json_encode([
            'status'=> 405,
            'message' => 'Method Not Allowed. Only GET is supported for search.'
        ]);
    }
} elseif ($endpoint === 'analytics') {
    if ($method === 'GET') {
        $controller->analytics();
    } else {
        http_response_code(405);
        echo json_encode([
            'status'=> 405,
            'message' => 'Method Not Allowed. Only GET is supported for analytics.'
        ]);
    }
} elseif ($endpoint === 'reactivate') {
    if ($method === 'PUT' || $method === 'POST') {
        $controller->reactivate();
    } else {
        http_response_code(405);
        echo json_encode([
            'status'=> 405,
            'message' => 'Method Not Allowed. Only PUT/POST is supported for reactivate.'
        ]);
    }
} elseif ($endpoint === 'business') {

    switch($method) {
        case 'GET':
            if($id) {
                $controller->show((int)$id);
            } else {
                $controller->index();
            }
            break;
        case 'POST':
            $controller->store();
            break;
        case 'PUT':
            $controller->update();
            break;
        case 'DELETE':
            $controller->delete();
            break;
        default:
            http_response_code(405);
            echo json_encode([
                'status'=> 405,
                'message' => 'Method Not Allowed'
            ]);
            break;
    }
} else {
    http_response_code(404);
    echo json_encode([
        'status'=> 404,
        'message' => 'Endpoint Not Found'
    ]);
}


