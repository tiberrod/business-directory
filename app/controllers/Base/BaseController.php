<?php
/**
 * Base Controller
 * 
 * Provides common functionality for all API version controllers
 * Handles common operations like authentication, response formatting, etc.
 */

require_once __DIR__ . '/../../helpers/ApiResponse.php';

abstract class BaseController {
    
    protected $db;
    protected $model;
    protected $apiVersion;
    
    public function __construct($db, $apiVersion = 'v1') {
        $this->db = $db;
        $this->apiVersion = $apiVersion;
        
        // Ensure Config class is loaded for image URL generation
        if (!class_exists('Config')) {
            require_once __DIR__ . '/../../config/config.php';
        }
        
        $this->initializeModel();
    }
    
    /**
     * Initialize the model for this controller
     * Each version controller should implement this
     */
    abstract protected function initializeModel();
    
    /**
     * Get the API version
     */
    public function getApiVersion() {
        return $this->apiVersion;
    }
    
    /**
     * Check if current user is admin
     * NOTE: For frontend integration, all API endpoints are now publicly accessible
     * Authentication will be handled by the frontend application
     */
    protected function isAdmin() {
        // Always return true for frontend integration
        // Frontend will handle user roles and permissions
        return true;
    }
    
    /**
     * Check user access level
     * NOTE: For frontend integration, all access levels are granted
     */
    protected function hasAccess($requiredRole = 'user') {
        // Always allow access for frontend integration
        // Frontend will handle role-based access control
        return true;
    }
    
    /**
     * Get JSON input from request body
     */
    protected function getJsonInput() {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        
        // Don't process multipart/form-data as JSON input - it should use $_POST and $_FILES
        if (strpos($contentType, 'multipart/form-data') !== false) {
            error_log("BaseController getJsonInput - Multipart form detected, returning empty array");
            return [];
        }
        
        $rawInput = file_get_contents('php://input');
        
        error_log("BaseController getJsonInput - Content-Type: $contentType, Raw Input Length: " . strlen($rawInput));
        
        // Handle JSON content
        if (strpos($contentType, 'application/json') !== false) {
            $input = json_decode($rawInput, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                error_log("BaseController getJsonInput - JSON decoded successfully");
                return $input;
            }
            error_log("BaseController getJsonInput - JSON decode error: " . json_last_error_msg());
            return [];
        }
        
        // Handle URL-encoded content as fallback
        if (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
            parse_str($rawInput, $input);
            error_log("BaseController getJsonInput - URL-encoded parsed successfully");
            return $input ?: [];
        }
        
        // Try to decode as JSON anyway (backward compatibility)
        if (!empty($rawInput)) {
            $input = json_decode($rawInput, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                error_log("BaseController getJsonInput - Fallback JSON decoded successfully");
                return $input;
            }
        }
        
        error_log("BaseController getJsonInput - No valid input found");
        return [];
    }
    
    /**
     * Build common filters from query parameters
     */
    protected function buildFilters() {
        $filters = [];

        if (isset($_GET['category']) && !empty($_GET['category'])) {
            $filters['category'] = $_GET['category'];
        }

        if (isset($_GET['featured']) && $_GET['featured'] !== '') {
            $filters['featured'] = $_GET['featured'];
        }

        if (isset($_GET['status']) && !empty($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }

        if (isset($_GET['include_inactive']) && $_GET['include_inactive'] == '1') {
            $filters['include_inactive'] = true;
        }

        return $filters;
    }
    
    /**
     * Get pagination parameters
     */
    protected function getPagination() {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 10;
        $offset = ($page - 1) * $limit;
        
        return compact('page', 'limit', 'offset');
    }
    
    /**
     * Get full image URL
     */
    protected function getImageUrl($imagePath) {
        if (empty($imagePath)) {
            return null;
        }

        // Config class should always be loaded now
        return Config::getImageUrl($imagePath);
    }

    /**
     * Get base URL
     */
    protected function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . '://' . $host . '/';
    }
    
    /**
     * Add image URLs to business data
     */
    protected function addImageUrls(&$businesses) {
        if (is_array($businesses)) {
            if (isset($businesses['business_img'])) {
                // Single business
                $businesses['business_img_url'] = $this->getImageUrl($businesses['business_img']);
            } else {
                // Array of businesses
                foreach ($businesses as &$business) {
                    if (isset($business['business_img'])) {
                        $business['business_img_url'] = $this->getImageUrl($business['business_img']);
                    }
                }
            }
        }
    }
    
    /**
     * Send success response
     */
    protected function sendSuccess($data, $statusCode = 200) {
        // Clean any existing output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        // Simple JSON encoding without any UTF-8 manipulation
        $jsonResponse = json_encode($data);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Log the specific error and data that caused it
            error_log("JSON encoding error: " . json_last_error_msg());
            
            // Return a simple error response instead
            $errorResponse = [
                'status' => 'error',
                'error' => [
                    'message' => 'Internal server error',
                    'code' => 500
                ]
            ];
            $jsonResponse = json_encode($errorResponse);
        }
        
        echo $jsonResponse;
        
        // Force output to be sent
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } else {
            flush();
        }
        exit();
    }
    
    /**
     * Send error response
     */
    protected function sendError($statusCode, $message, $details = null) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
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

        echo json_encode($response);
        exit();
    }
    
    /**
     * Validate required fields
     */
    protected function validateRequiredFields($data, $requiredFields) {
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $missingFields[] = $field;
            }
        }
        
        if (!empty($missingFields)) {
            $this->sendError(400, 'Missing required fields: ' . implode(', ', $missingFields));
        }
    }
    
    /**
     * Log operation for audit purposes
     */
    protected function logOperation($operation, $businessId = null, $data = null) {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'api_version' => $this->apiVersion,
            'operation' => $operation,
            'business_id' => $businessId,
            'user_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'is_admin' => $this->isAdmin(),
            'data' => $data
        ];
        
        error_log("API Operation: " . json_encode($logData));
    }
}
?>