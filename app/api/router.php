<?php
/**
 * API Router v1.0.0
 * 
 * Main API router that handles versioning and routes requests to appropriate controllers
 * Supports multiple API versions with clean separation of concerns
 * 
 * Usage Examples:
 * GET /app/api/router.php?version=v1&endpoint=businesses&action=list
 * POST /app/api/router.php?version=v1&endpoint=businesses&action=create
 * PUT /app/api/router.php?version=v1&endpoint=businesses&action=update&id=1
 */

// Enhanced CORS headers for cross-origin compatibility
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-User-Role");
header("Access-Control-Max-Age: 3600");

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

class ApiRouter {
    
    private $supportedVersions = ['v1'];
    private $defaultVersion = 'v1';
    
    public function __construct() {
        // Include required files
        require_once __DIR__ . '/../config/database.php';
    }
    
    /**
     * Route the incoming request
     */
    public function route() {
        try {
            // Get request parameters
            $version = $this->getVersion();
            $endpoint = $this->getEndpoint();
            $action = $this->getAction();
            $method = $_SERVER['REQUEST_METHOD'];
            
            // Validate version
            if (!$this->isVersionSupported($version)) {
                return $this->sendError(400, "Unsupported API version: {$version}. Supported versions: " . implode(', ', $this->supportedVersions));
            }
            
            // Show API info if no endpoint specified
            if (empty($endpoint)) {
                return $this->showApiInfo($version);
            }
            
            // Route to appropriate controller
            switch ($endpoint) {
                case 'businesses':
                case 'business':
                    return $this->routeToBusinessController($version, $action, $method);
                    
                case 'documentation':
                case 'docs':
                    return $this->routeToDocumentationController($version, $action, $method);
                    
                default:
                    return $this->sendError(404, "Unknown endpoint: {$endpoint}");
            }
            
        } catch (Exception $e) {
            error_log("API Router error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }
    
    /**
     * Route to business controller based on version
     */
    private function routeToBusinessController($version, $action, $method) {
        // Load the appropriate controller for the version
        $controllerPath = __DIR__ . "/../controllers/{$version}/BusinessController.php";
        
        if (!file_exists($controllerPath)) {
            return $this->sendError(500, "Controller not found for version {$version}");
        }
        
        require_once $controllerPath;
        
        // Get controller class name based on version
        $controllerClass = 'BusinessController' . strtoupper($version);
        
        if (!class_exists($controllerClass)) {
            return $this->sendError(500, "Controller class not found: {$controllerClass}");
        }
        
        // Initialize database connection
        $database = new Database();
        $db = $database->getConnection();
        
        if (!$db) {
            return $this->sendError(500, 'Database connection failed');
        }
        
        // Create controller instance
        $controller = new $controllerClass($db, $version);
        
        // Route to appropriate action based on method and action
        return $this->executeControllerAction($controller, $action, $method);
    }
    
    /**
     * Route to documentation controller based on version
     */
    private function routeToDocumentationController($version, $action, $method) {
        // Load the appropriate documentation controller for the version
        $controllerPath = __DIR__ . "/../controllers/{$version}/DocumentationController.php";
        
        if (!file_exists($controllerPath)) {
            return $this->sendError(500, "Documentation controller not found for version {$version}");
        }
        
        require_once $controllerPath;
        
        // Get controller class name based on version
        $controllerClass = 'DocumentationController' . strtoupper($version);
        
        if (!class_exists($controllerClass)) {
            return $this->sendError(500, "Documentation controller class not found: {$controllerClass}");
        }
        
        // Initialize database connection (documentation doesn't need it, but for consistency)
        $database = new Database();
        $db = $database->getConnection();
        
        if (!$db) {
            return $this->sendError(500, 'Database connection failed');
        }
        
        // Create controller instance
        $controller = new $controllerClass($db, $version);
        
        // Route to appropriate documentation action
        return $this->executeDocumentationAction($controller, $action, $method);
    }
    
    /**
     * Execute the appropriate controller action
     */
    private function executeControllerAction($controller, $action, $method) {
        $id = $_GET['id'] ?? null;
        
        // Enhanced debugging for production environment
        error_log("=== ROUTER EXECUTING ACTION ===");
        error_log("Action: " . var_export($action, true));
        error_log("Method: " . var_export($method, true));
        error_log("ID: " . var_export($id, true));
        error_log("All GET params: " . json_encode($_GET));
        
        // Smart routing: Auto-detect action based on method and parameters if no action is specified
        if (empty($action)) {
            if ($method === 'POST' && !empty($id)) {
                $action = 'update'; // POST with ID = update
                error_log("ROUTER: Auto-detected UPDATE action (POST with ID)");
            } elseif ($method === 'PUT' && !empty($id)) {
                $action = 'update'; // PUT with ID = update
                error_log("ROUTER: Auto-detected UPDATE action (PUT with ID)");
            } elseif ($method === 'POST' && empty($id)) {
                $action = 'create'; // POST without ID = create
                error_log("ROUTER: Auto-detected CREATE action (POST without ID)");
            }
        }
        
        switch ($action) {
            case 'list':
            case '':
                if ($method !== 'GET') {
                    return $this->sendError(405, 'Method not allowed. Use GET for listing businesses.');
                }
                return $controller->index();
                
            case 'details':
            case 'show':
                if ($method !== 'GET') {
                    return $this->sendError(405, 'Method not allowed. Use GET for viewing business details.');
                }
                if (!$id) {
                    return $this->sendError(400, 'Business ID is required for details action');
                }
                return $controller->show($id);
                
            case 'search':
                if ($method !== 'GET') {
                    return $this->sendError(405, 'Method not allowed. Use GET for searching businesses.');
                }
                return $controller->search();
                
            case 'create':
                if ($method !== 'POST') {
                    return $this->sendError(405, 'Method not allowed. Use POST for creating businesses.');
                }
                return $controller->store();
                
            case 'update':
                // Allow both PUT and POST for updates (flexibility for frontend integration)
                if ($method !== 'PUT' && $method !== 'POST') {
                    return $this->sendError(405, 'Method not allowed. Use PUT or POST for updating businesses.');
                }
                error_log("ROUTER: Calling update method for business ID: " . ($_GET['id'] ?? 'unknown'));
                return $controller->update();
                
            case 'deactivate':
                if ($method !== 'PUT') {
                    return $this->sendError(405, 'Method not allowed. Use PUT for deactivating businesses.');
                }
                return $controller->deactivate();
                
            case 'reactivate':
                if ($method !== 'PUT') {
                    return $this->sendError(405, 'Method not allowed. Use PUT for reactivating businesses.');
                }
                return $controller->reactivate();
                
            case 'delete':
                if ($method !== 'DELETE') {
                    return $this->sendError(405, 'Method not allowed. Use DELETE for deleting businesses.');
                }
                return $controller->delete();
                
            case 'featured':
                if ($method !== 'GET') {
                    return $this->sendError(405, 'Method not allowed. Use GET for featured businesses.');
                }
                return $controller->featured();
                
            case 'analytics':
                if ($method !== 'GET') {
                    return $this->sendError(405, 'Method not allowed. Use GET for analytics.');
                }
                return $controller->analytics();
                
            // Enhanced v1.1.0 endpoints
            case 'set-featured':
            case 'featured-status':
                if ($method !== 'PUT') {
                    return $this->sendError(405, 'Method not allowed. Use PUT for setting featured status.');
                }
                return $controller->setFeatured();
                
            case 'update-image':
            case 'image':
                if ($method !== 'PUT') {
                    return $this->sendError(405, 'Method not allowed. Use PUT for updating image.');
                }
                return $controller->updateImage();
                
            case 'expiring-soon':
            case 'expiring':
                if ($method !== 'GET') {
                    return $this->sendError(405, 'Method not allowed. Use GET for expiring businesses.');
                }
                return $controller->expiringSoon();
                
            case 'auto-deactivate':
            case 'cleanup-expired':
                if ($method !== 'POST') {
                    return $this->sendError(405, 'Method not allowed. Use POST for auto-deactivation.');
                }
                return $controller->autoDeactivateExpired();
                
            default:
                return $this->sendError(404, "Unknown action: {$action}");
        }
    }
    
    /**
     * Execute the appropriate documentation action
     */
    private function executeDocumentationAction($controller, $action, $method) {
        // All documentation endpoints are GET only
        if ($method !== 'GET') {
            return $this->sendError(405, 'Method not allowed. Documentation endpoints only support GET requests.');
        }
        
        switch ($action) {
            case 'version':
                return $controller->version();
                
            case 'patches':
            case 'changelog':
                return $controller->patches();
                
            case 'endpoints':
                return $controller->endpoints();
                
            case 'health':
            case 'status':
                return $controller->health();
                
            case '':
            case 'docs':
            case 'documentation':
            default:
                return $controller->documentation();
        }
    }
    
    /**
     * Get API version from request
     */
    private function getVersion() {
        return $_GET['version'] ?? $_GET['v'] ?? $this->defaultVersion;
    }
    
    /**
     * Get endpoint from request
     */
    private function getEndpoint() {
        return $_GET['endpoint'] ?? $_GET['resource'] ?? '';
    }
    
    /**
     * Get action from request
     */
    private function getAction() {
        return $_GET['action'] ?? $_GET['method'] ?? '';
    }
    
    /**
     * Check if version is supported
     */
    private function isVersionSupported($version) {
        return in_array($version, $this->supportedVersions);
    }
    
    /**
     * Show API information
     */
    private function showApiInfo($version) {
        $apiInfo = [
            'api' => 'Business Directory API',
            'version' => $version,
            'status' => 'active',
            'supported_versions' => $this->supportedVersions,
            'base_url' => $this->getBaseUrl() . '/app/api/router.php',
            'documentation' => $this->getBaseUrl() . '/docs/API_V1_IMPLEMENTATION_COMPLETE.md',
            'test_suite' => $this->getBaseUrl() . '/app/api/test-v1-api.php',
            'architecture' => [
                'model' => 'Standard Base Model (supports all versions)',
                'controller' => 'Version-based Controllers',
                'versioning' => 'Query parameter based (?version=v1)',
                'structure' => 'app/models/Base/, app/controllers/v1/'
            ],
            'endpoints' => [
                'businesses' => [
                    'list' => [
                        'method' => 'GET',
                        'url' => "?version={$version}&endpoint=businesses&action=list",
                        'description' => 'Get paginated list of businesses',
                        'access' => 'Admin & User',
                        'parameters' => [
                            'page' => 'Page number (default: 1)',
                            'limit' => 'Results per page (default: 10, max: 100)',
                            'category' => 'Filter by business category',
                            'featured' => 'Filter by featured status (1/0)',
                            'status' => 'Filter by status (active/inactive)',
                            'include_inactive' => 'Include inactive businesses (1/0)'
                        ]
                    ],
                    'search' => [
                        'method' => 'GET',
                        'url' => "?version={$version}&endpoint=businesses&action=search&name={term}",
                        'description' => 'Search businesses by name',
                        'access' => 'Admin & User',
                        'parameters' => [
                            'name' => 'Search term (required)',
                            'page' => 'Page number',
                            'limit' => 'Results per page',
                            'category' => 'Filter by category',
                            'featured' => 'Filter by featured status',
                            'status' => 'Filter by status'
                        ]
                    ],
                    'details' => [
                        'method' => 'GET',
                        'url' => "?version={$version}&endpoint=businesses&action=details&id={id}",
                        'description' => 'Get detailed information for a specific business',
                        'access' => 'Admin & User',
                        'parameters' => [
                            'id' => 'Business ID (required)'
                        ]
                    ],
                    'create' => [
                        'method' => 'POST',
                        'url' => "?version={$version}&endpoint=businesses&action=create",
                        'description' => 'Create a new business',
                        'access' => 'Admin only',
                        'headers' => ['X-User-Role: admin'],
                        'required_fields' => ['business_name', 'business_contact', 'business_category'],
                        'optional_fields' => ['business_description', 'business_img', 'status', 'is_featured', 'expiry_date']
                    ],
                    'update' => [
                        'method' => 'PUT',
                        'url' => "?version={$version}&endpoint=businesses&action=update&id={id}",
                        'description' => 'Update specific details for an existing business',
                        'access' => 'Admin only',
                        'headers' => ['X-User-Role: admin'],
                        'parameters' => ['id' => 'Business ID (required)'],
                        'updatable_fields' => ['business_name', 'business_contact', 'business_description', 'business_category', 'business_img', 'status', 'is_featured', 'expiry_date']
                    ],
                    'deactivate' => [
                        'method' => 'PUT',
                        'url' => "?version={$version}&endpoint=businesses&action=deactivate&id={id}",
                        'description' => 'Deactivate business (soft delete) for non-payment',
                        'access' => 'Admin only',
                        'headers' => ['X-User-Role: admin'],
                        'parameters' => ['id' => 'Business ID (required)'],
                        'optional_fields' => ['reason' => 'Reason for deactivation']
                    ],
                    'reactivate' => [
                        'method' => 'PUT',
                        'url' => "?version={$version}&endpoint=businesses&action=reactivate&id={id}",
                        'description' => 'Reactivate business for 1 month',
                        'access' => 'Admin only',
                        'headers' => ['X-User-Role: admin'],
                        'parameters' => ['id' => 'Business ID (required)']
                    ],
                    'delete' => [
                        'method' => 'DELETE',
                        'url' => "?version={$version}&endpoint=businesses&action=delete&id={id}",
                        'description' => 'Permanently delete a business',
                        'access' => 'Admin only',
                        'headers' => ['X-User-Role: admin'],
                        'parameters' => ['id' => 'Business ID (required)']
                    ],
                    'featured' => [
                        'method' => 'GET',
                        'url' => "?version={$version}&endpoint=businesses&action=featured",
                        'description' => 'Get featured businesses with highlighting',
                        'access' => 'Admin & User',
                        'parameters' => [
                            'page' => 'Page number',
                            'limit' => 'Results per page',
                            'category' => 'Filter by category'
                        ]
                    ],
                    'analytics' => [
                        'method' => 'GET',
                        'url' => "?version={$version}&endpoint=businesses&action=analytics",
                        'description' => 'Get business analytics and statistics',
                        'access' => 'Admin only',
                        'headers' => ['X-User-Role: admin']
                    ]
                ]
            ],
            'authentication' => [
                'admin_access' => [
                    'header' => 'X-User-Role: admin',
                    'query_param' => 'admin_access=1',
                    'note' => 'For production, implement proper JWT/session-based authentication'
                ]
            ],
            'response_format' => [
                'success' => [
                    'status' => 'success',
                    'data' => '... response data ...',
                    'api_version' => $version
                ],
                'error' => [
                    'status' => 'error',
                    'error' => [
                        'code' => 'HTTP status code',
                        'message' => 'Error description'
                    ]
                ]
            ]
        ];
        
        return $this->sendSuccess($apiInfo);
    }
    
    /**
     * Get base URL
     */
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . '://' . $host;
    }
    
    /**
     * Send success response
     */
    private function sendSuccess($data, $statusCode = 200) {
        http_response_code($statusCode);
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit();
    }
    
    /**
     * Send error response
     */
    private function sendError($statusCode, $message, $details = null) {
        http_response_code($statusCode);
        
        $response = [
            'status' => 'error',
            'error' => [
                'code' => $statusCode,
                'message' => $message
            ]
        ];

        if ($details) {
            $response['error']['details'] = $details;
        }

        echo json_encode($response, JSON_PRETTY_PRINT);
        exit();
    }
}

// Initialize and route the request
try {
    $router = new ApiRouter();
    $router->route();
} catch (Exception $e) {
    error_log("Router initialization error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'error' => [
            'code' => 500,
            'message' => 'API initialization failed'
        ]
    ]);
}
?>