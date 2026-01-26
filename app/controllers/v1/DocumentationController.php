<?php
/**
 * Documentation Controller v1
 * 
 * Provides API documentation, version information, and patch notes
 * Serves interactive documentation with authentication
 * No database dependency - uses built-in PHP data structures
 */

require_once __DIR__ . '/../Base/BaseController.php';

class DocumentationControllerV1 extends BaseController {
    
    private $apiInfo;
    private $authCredentials = [
        'username' => 'apploqic',
        'password' => 'apploqic'
    ];
    
    /**
     * Initialize the documentation data
     */
    protected function initializeModel() {
        // No database model needed
        $this->apiInfo = $this->getApiInformation();
    }

    /**
     * Serve the interactive documentation interface
     * Note: Documentation access requires authentication (for security)
     * API endpoints themselves are publicly accessible
     */
    public function index() {
        try {
            $docPath = __DIR__ . '/../../../documentation/interactive-api-docs.html';
            
            if (file_exists($docPath)) {
                // Set appropriate headers
                header('Content-Type: text/html; charset=UTF-8');
                header('Cache-Control: no-cache, must-revalidate');
                header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                
                // Serve the interactive documentation
                readfile($docPath);
                exit;
            } else {
                return $this->sendError(404, 'Documentation not found');
            }
            
        } catch (Exception $e) {
            error_log("Documentation serve error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }

    /**
     * Authentication endpoint for documentation access
     * POST: Verify credentials and establish session
     */
    public function authenticate() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return $this->sendError(405, 'Method not allowed');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $username = $input['username'] ?? '';
            $password = $input['password'] ?? '';

            if ($username === $this->authCredentials['username'] && 
                $password === $this->authCredentials['password']) {
                
                // Start session for authenticated access
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                $_SESSION['apploqic_authenticated'] = true;
                $_SESSION['auth_time'] = time();
                
                return $this->sendSuccess([
                    'status' => 'success',
                    'message' => 'Authentication successful',
                    'access_granted' => true,
                    'api_info' => $this->getApiOverview()
                ]);
                
            } else {
                return $this->sendError(401, 'Invalid credentials');
            }
            
        } catch (Exception $e) {
            error_log("Authentication error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }

    /**
     * Get API overview and available endpoints
     * Requires authentication
     */
    public function overview() {
        try {
            if (!$this->isAuthenticated()) {
                return $this->sendError(401, 'Authentication required');
            }

            $response = [
                'status' => 'success',
                'data' => $this->getApiOverview(),
                'api_version' => $this->apiVersion
            ];

            return $this->sendSuccess($response);
            
        } catch (Exception $e) {
            error_log("Get overview error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }

    /**
     * Get detailed endpoint information for authenticated users
     * Requires authentication
     */
    public function endpointList() {
        try {
            if (!$this->isAuthenticated()) {
                return $this->sendError(401, 'Authentication required');
            }

            $endpoints = $this->getAllEndpoints();
            
            $response = [
                'status' => 'success',
                'data' => [
                    'total_endpoints' => count($endpoints),
                    'endpoints' => $endpoints,
                    'version' => $this->apiVersion
                ],
                'api_version' => $this->apiVersion
            ];

            return $this->sendSuccess($response);
            
        } catch (Exception $e) {
            error_log("Get endpoints error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }

    /**
     * Check if user is authenticated
     */
    private function isAuthenticated() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['apploqic_authenticated']) && 
               $_SESSION['apploqic_authenticated'] === true &&
               isset($_SESSION['auth_time']) &&
               (time() - $_SESSION['auth_time']) < 3600; // 1 hour session
    }

    /**
     * Get API overview information
     */
    private function getApiOverview() {
        return [
            'api_name' => 'Apploqic Business Directory API',
            'current_version' => $this->apiInfo['current_version'],
            'release_date' => $this->apiInfo['release_date'],
            'status' => $this->apiInfo['status'],
            'total_endpoints' => 9,
            'available_methods' => ['GET', 'POST', 'PUT', 'DELETE'],
            'response_format' => 'JSON',
            'base_url' => $_SERVER['HTTP_HOST'] . '/api',
            'features' => [
                'CRUD operations for business listings',
                'Advanced search and filtering',
                'Pagination support',
                'Image URL generation',
                'Status management',
                'Featured business system',
                'Business reactivation',
                'Analytics and statistics'
            ]
        ];
    }

    /**
     * Get all available API endpoints
     */
    private function getAllEndpoints() {
        return [
            [
                'endpoint' => '/api/business',
                'method' => 'GET',
                'description' => 'Get paginated list of businesses',
                'parameters' => ['page', 'category', 'featured', 'status'],
                'authenticated' => false
            ],
            [
                'endpoint' => '/api/business/{id}',
                'method' => 'GET',
                'description' => 'Get specific business by ID',
                'parameters' => ['id'],
                'authenticated' => false
            ],
            [
                'endpoint' => '/api/search',
                'method' => 'GET',
                'description' => 'Search businesses with filters',
                'parameters' => ['q', 'category', 'location', 'featured'],
                'authenticated' => false
            ],
            [
                'endpoint' => '/api/business',
                'method' => 'POST',
                'description' => 'Create new business listing',
                'parameters' => ['business_name', 'business_category', 'business_description'],
                'authenticated' => true
            ],
            [
                'endpoint' => '/api/business',
                'method' => 'PUT',
                'description' => 'Update existing business',
                'parameters' => ['id', 'business_name', 'business_category'],
                'authenticated' => true
            ],
            [
                'endpoint' => '/api/business',
                'method' => 'DELETE',
                'description' => 'Delete business listing',
                'parameters' => ['id'],
                'authenticated' => true
            ],
            [
                'endpoint' => '/api/analytics',
                'method' => 'GET',
                'description' => 'Get business directory analytics',
                'parameters' => [],
                'authenticated' => false
            ],
            [
                'endpoint' => '/api/reactivate',
                'method' => 'PUT',
                'description' => 'Reactivate inactive business',
                'parameters' => ['id', 'duration'],
                'authenticated' => true
            ]
        ];
    }
    
    /**
     * Get API version information
     * Accessible by: Admin & User
     */
    public function version() {
        try {
            $response = [
                'status' => 'success',
                'data' => [
                    'current_version' => $this->apiInfo['current_version'],
                    'release_date' => $this->apiInfo['release_date'],
                    'status' => $this->apiInfo['status'],
                    'supported_versions' => $this->apiInfo['supported_versions'],
                    'deprecated_versions' => $this->apiInfo['deprecated_versions']
                ],
                'api_version' => $this->apiVersion
            ];

            return $this->sendSuccess($response);
            
        } catch (Exception $e) {
            error_log("Get version info error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }
    
    /**
     * Get patch notes and changelog
     * Accessible by: Admin & User
     */
    public function patches() {
        try {
            $version = $_GET['version'] ?? $this->apiInfo['current_version'];
            
            $patches = $this->getPatchNotes($version);
            
            if (empty($patches)) {
                return $this->sendError(404, "No patch information found for version: {$version}");
            }

            $response = [
                'status' => 'success',
                'data' => [
                    'version' => $version,
                    'patches' => $patches
                ],
                'api_version' => $this->apiVersion
            ];

            return $this->sendSuccess($response);
            
        } catch (Exception $e) {
            error_log("Get patch notes error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }
    
    /**
     * Get all available API endpoints
     * Accessible by: Admin & User
     */
    public function endpoints() {
        try {
            $version = $_GET['version'] ?? $this->apiInfo['current_version'];
            
            $endpoints = $this->getEndpointDocumentation($version);

            $response = [
                'status' => 'success',
                'data' => [
                    'version' => $version,
                    'base_url' => $this->getDocumentationBaseUrl(),
                    'endpoints' => $endpoints
                ],
                'api_version' => $this->apiVersion
            ];

            return $this->sendSuccess($response);
            
        } catch (Exception $e) {
            error_log("Get endpoints error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }
    
    /**
     * Get complete API documentation
     * Accessible by: Admin & User
     */
    public function documentation() {
        try {
            $version = $_GET['version'] ?? $this->apiInfo['current_version'];
            
            $response = [
                'status' => 'success',
                'data' => [
                    'api_info' => [
                        'name' => 'Apploqic Business Directory API',
                        'version' => $version,
                        'description' => 'RESTful API for managing business directory operations',
                        'release_date' => $this->apiInfo['release_date'],
                        'status' => $this->apiInfo['status']
                    ],
                    'authentication' => $this->getAuthenticationInfo(),
                    'endpoints' => $this->getEndpointDocumentation($version),
                    'response_format' => $this->getResponseFormat(),
                    'error_codes' => $this->getErrorCodes(),
                    'changelog' => $this->getPatchNotes($version)
                ],
                'api_version' => $this->apiVersion
            ];

            return $this->sendSuccess($response);
            
        } catch (Exception $e) {
            error_log("Get documentation error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }
    
    /**
     * Get API health status
     * Accessible by: Admin & User
     */
    public function health() {
        try {
            $response = [
                'status' => 'success',
                'data' => [
                    'api_status' => 'healthy',
                    'version' => $this->apiInfo['current_version'],
                    'timestamp' => date('Y-m-d H:i:s'),
                    'server_time' => time(),
                    'uptime' => $this->getUptime(),
                    'environment' => 'development' // Change to 'production' when deployed
                ],
                'api_version' => $this->apiVersion
            ];

            return $this->sendSuccess($response);
            
        } catch (Exception $e) {
            error_log("Get health status error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }
    
    /**
     * Get API information structure
     */
    private function getApiInformation() {
        return [
            'current_version' => 'v1.1.0',
            'release_date' => '2024-11-09',
            'status' => 'stable',
            'supported_versions' => ['v1'],
            'deprecated_versions' => [],
            'next_version' => 'v1.2.0'
        ];
    }
    
    /**
     * Get patch notes for specific version
     */
    private function getPatchNotes($version) {
        $patches = [
            'v1.1.0' => [
                [
                    'version' => 'v1.1.0',
                    'release_date' => '2024-11-09',
                    'type' => 'minor',
                    'changes' => [
                        'new' => [
                            '30-day active period with expiry_at field',
                            'Reactivation tracking with reactivated_at field',
                            'Enhanced featured status with featured_at datetime',
                            'Image URL management for CRUD operations',
                            'Enhanced search with category filtering',
                            'Empty search returns all businesses',
                            'Set featured status endpoint',
                            'Update image URL endpoint',
                            'Expiring businesses monitoring',
                            'Auto-deactivation of expired businesses',
                            'Expiry status in business responses'
                        ],
                        'improved' => [
                            'Search functionality with comprehensive filters',
                            'Business creation with automatic 30-day expiry',
                            'Reactivation with new 30-day period',
                            'Featured status management',
                            'Business listing with expiry information',
                            'Enhanced analytics with expiry data'
                        ],
                        'fixed' => [
                            'Featured status boolean consistency',
                            'Image URL handling in responses',
                            'Category filtering optimization'
                        ]
                    ],
                    'breaking_changes' => [
                        'Search endpoint now returns all businesses when search term is empty',
                        'Business responses include additional expiry fields',
                        'Featured status now uses datetime instead of boolean'
                    ],
                    'migration_notes' => 'Run migration_business_enhancement.php to add new database fields.'
                ]
            ],
            'v1.0.0' => [
                [
                    'version' => 'v1.0.0',
                    'release_date' => '2024-11-09',
                    'type' => 'major',
                    'changes' => [
                        'new' => [
                            'Complete API architecture redesign',
                            'Version-based controller system',
                            'Standard base model with version support',
                            'Enhanced business CRUD operations',
                            'Role-based access control (Admin/User)',
                            'Advanced search and filtering',
                            'Featured business management',
                            'Analytics endpoint for business insights',
                            'Comprehensive error handling',
                            'API documentation endpoints'
                        ],
                        'improved' => [
                            'Response format standardization',
                            'Input validation and sanitization',
                            'Database query optimization',
                            'Image URL handling',
                            'Pagination system',
                            'Logging and monitoring'
                        ],
                        'fixed' => [
                            'CORS handling for cross-origin requests',
                            'SQL injection vulnerabilities',
                            'Memory usage optimization',
                            'Error response consistency'
                        ]
                    ],
                    'breaking_changes' => [
                        'Complete API endpoint restructure',
                        'New authentication system',
                        'Response format changes'
                    ],
                    'migration_notes' => 'This is a complete rewrite. Please update all API integrations.'
                ]
            ]
        ];
        
        return $patches[$version] ?? [];
    }
    
    /**
     * Get endpoint documentation for specific version
     */
    private function getEndpointDocumentation($version) {
        $endpoints = [
            'v1' => [
                'businesses' => [
                    'GET /businesses' => [
                        'description' => 'Get all businesses with pagination and filtering',
                        'access' => 'Admin, User',
                        'parameters' => [
                            'page' => 'Page number (default: 1)',
                            'limit' => 'Items per page (default: 10)',
                            'search' => 'Search term for business name/description',
                            'category' => 'Filter by category',
                            'status' => 'Filter by status (active/inactive)',
                            'featured' => 'Filter featured businesses (true/false)'
                        ],
                        'response' => 'Business list with pagination info'
                    ],
                    'GET /businesses/{id}' => [
                        'description' => 'Get single business by ID',
                        'access' => 'Admin, User',
                        'parameters' => [
                            'id' => 'Business ID (required)'
                        ],
                        'response' => 'Single business object'
                    ],
                    'GET /businesses/search' => [
                        'description' => 'Search businesses with enhanced criteria (v1.1.0)',
                        'access' => 'Admin, User',
                        'parameters' => [
                            'q' => 'Search query (optional - empty shows all)',
                            'search' => 'Search query (alternative parameter)',
                            'category' => 'Filter by business category',
                            'status' => 'Filter by status (active/inactive)',
                            'featured' => 'Filter featured businesses (true/false)',
                            'expired' => 'Include expired businesses (true/false)'
                        ],
                        'response' => 'Enhanced business list with expiry info'
                    ],
                    'POST /businesses' => [
                        'description' => 'Create new business with 30-day expiry (v1.1.0)',
                        'access' => 'Admin only',
                        'parameters' => [
                            'business_name' => 'Business name (required)',
                            'business_contact' => 'Business contact (required)',
                            'business_description' => 'Business description',
                            'category' => 'Business category',
                            'image_url' => 'Business image URL',
                            'featured' => 'Set as featured (true/false)'
                        ],
                        'response' => 'Created business with expiry info'
                    ],
                    'PUT /businesses/{id}' => [
                        'description' => 'Update existing business',
                        'access' => 'Admin only',
                        'parameters' => [
                            'id' => 'Business ID (required)',
                            'business_name' => 'Updated business name',
                            'business_description' => 'Updated description',
                            'category' => 'Updated category',
                            'image_url' => 'Updated image URL'
                        ],
                        'response' => 'Updated business object'
                    ],
                    'POST /businesses/{id}/deactivate' => [
                        'description' => 'Deactivate business',
                        'access' => 'Admin only',
                        'parameters' => [
                            'id' => 'Business ID (required)'
                        ],
                        'response' => 'Deactivation confirmation'
                    ],
                    'POST /businesses/{id}/reactivate' => [
                        'description' => 'Reactivate business with new 30-day expiry (v1.1.0)',
                        'access' => 'Admin only',
                        'parameters' => [
                            'id' => 'Business ID (required)'
                        ],
                        'response' => 'Reactivation confirmation with expiry info'
                    ],
                    'DELETE /businesses/{id}' => [
                        'description' => 'Delete business permanently',
                        'access' => 'Admin only',
                        'parameters' => [
                            'id' => 'Business ID (required)'
                        ],
                        'response' => 'Deletion confirmation'
                    ],
                    'GET /businesses/featured' => [
                        'description' => 'Get featured businesses',
                        'access' => 'Admin, User',
                        'parameters' => [
                            'limit' => 'Number of featured businesses'
                        ],
                        'response' => 'Featured business list'
                    ],
                    'PUT /businesses/{id}/set-featured' => [
                        'description' => 'Set business featured status (v1.1.0)',
                        'access' => 'Admin only',
                        'parameters' => [
                            'id' => 'Business ID (required)',
                            'featured' => 'Featured status (true/false)'
                        ],
                        'response' => 'Featured status confirmation'
                    ],
                    'PUT /businesses/{id}/update-image' => [
                        'description' => 'Update business image URL (v1.1.0)',
                        'access' => 'Admin only',
                        'parameters' => [
                            'id' => 'Business ID (required)',
                            'image_url' => 'New image URL (required)'
                        ],
                        'response' => 'Image update confirmation'
                    ],
                    'GET /businesses/expiring-soon' => [
                        'description' => 'Get businesses expiring soon (v1.1.0)',
                        'access' => 'Admin only',
                        'parameters' => [
                            'days' => 'Days ahead to check (default: 7)'
                        ],
                        'response' => 'List of expiring businesses'
                    ],
                    'POST /businesses/auto-deactivate' => [
                        'description' => 'Auto-deactivate expired businesses (v1.1.0)',
                        'access' => 'Admin only',
                        'parameters' => [],
                        'response' => 'Deactivation summary'
                    ],
                    'GET /businesses/analytics' => [
                        'description' => 'Get business analytics',
                        'access' => 'Admin only',
                        'parameters' => [
                            'period' => 'Analytics period (day/week/month)'
                        ],
                        'response' => 'Analytics data'
                    ],
                ],
                'documentation' => [
                    'GET /documentation/version' => [
                        'description' => 'Get API version information',
                        'access' => 'Admin, User',
                        'parameters' => [],
                        'response' => 'Version details'
                    ],
                    'GET /documentation/patches' => [
                        'description' => 'Get patch notes and changelog',
                        'access' => 'Admin, User',
                        'parameters' => [
                            'version' => 'Specific version (optional)'
                        ],
                        'response' => 'Patch notes'
                    ],
                    'GET /documentation/endpoints' => [
                        'description' => 'Get all available endpoints',
                        'access' => 'Admin, User',
                        'parameters' => [
                            'version' => 'API version (optional)'
                        ],
                        'response' => 'Endpoint list'
                    ],
                    'GET /documentation' => [
                        'description' => 'Get complete API documentation',
                        'access' => 'Admin, User',
                        'parameters' => [
                            'version' => 'API version (optional)'
                        ],
                        'response' => 'Full documentation'
                    ],
                    'GET /documentation/health' => [
                        'description' => 'Get API health status',
                        'access' => 'Admin, User',
                        'parameters' => [],
                        'response' => 'Health status'
                    ]
                ]
            ]
        ];
        
        return $endpoints[$version] ?? [];
    }
    
    /**
     * Get authentication information
     */
    private function getAuthenticationInfo() {
        return [
            'type' => 'Header-based Role Authentication',
            'header' => 'X-User-Role',
            'values' => [
                'admin' => 'Full access to all endpoints',
                'user' => 'Read-only access to public endpoints'
            ],
            'note' => 'Include X-User-Role header with value "admin" or "user"'
        ];
    }
    
    /**
     * Get response format information
     */
    private function getResponseFormat() {
        return [
            'success' => [
                'format' => [
                    'status' => 'success',
                    'data' => 'Response data object/array',
                    'api_version' => 'API version used'
                ]
            ],
            'error' => [
                'format' => [
                    'status' => 'error',
                    'message' => 'Error description',
                    'code' => 'HTTP status code',
                    'api_version' => 'API version used'
                ]
            ]
        ];
    }
    
    /**
     * Get error codes documentation
     */
    private function getErrorCodes() {
        return [
            '200' => 'Success',
            '201' => 'Created',
            '400' => 'Bad Request - Invalid parameters',
            '401' => 'Unauthorized - Missing or invalid authentication',
            '403' => 'Forbidden - Insufficient permissions',
            '404' => 'Not Found - Resource not found',
            '405' => 'Method Not Allowed - HTTP method not supported',
            '500' => 'Internal Server Error - Server error occurred'
        ];
    }
    
    /**
     * Get base URL for documentation
     */
    private function getDocumentationBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $script = str_replace('/app/api/router.php', '', $_SERVER['SCRIPT_NAME']);
        return "{$protocol}://{$host}{$script}/app/api/router.php";
    }
    
    /**
     * Get server uptime (simplified)
     */
    private function getUptime() {
        return "API operational since " . $this->apiInfo['release_date'];
    }
}