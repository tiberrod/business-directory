<?php

class ApiResponse {
    
    /**
     * Send success response
     * @param mixed $data The data to send
     * @param string $message Success message
     * @param int $status HTTP status code (default 200)
     */
    public static function success($data = null, $message = 'Success', $status = 200) {
        http_response_code($status);
        
        $response = [
            'status' => $status,
            'message' => $message
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        echo json_encode($response);
        exit;
    }
    
    /**
     * Send paginated response
     * @param array $data The data array
     * @param int $total Total number of items
     * @param int $page Current page
     * @param int $perPage Items per page
     * @param string $message Success message
     */
    public static function paginated($data, $total, $page, $perPage, $message = 'Success') {
        http_response_code(200);
        
        echo json_encode([
            'status' => 200,
            'message' => $message,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'data' => $data
        ]);
        exit;
    }
    
    /**
     * Send error response
     * @param string $message Error message
     * @param int $status HTTP status code (default 400)
     */
    public static function error($message, $status = 400) {
        http_response_code($status);
        
        echo json_encode([
            'status' => $status,
            'message' => $message
        ]);
        exit;
    }
    
    /**
     * Send validation error response
     * @param array $errors Array of validation errors
     */
    public static function validationError($errors) {
        http_response_code(422);
        
        echo json_encode([
            'status' => 422,
            'message' => 'Validation failed',
            'errors' => $errors
        ]);
        exit;
    }
    
    /**
     * Send not found response
     * @param string $message Not found message
     */
    public static function notFound($message = 'Resource not found') {
        self::error($message, 404);
    }
    
    /**
     * Send method not allowed response
     */
    public static function methodNotAllowed() {
        self::error('Method not allowed', 405);
    }
    
    /**
     * Send internal server error response
     * @param string $message Error message
     */
    public static function serverError($message = 'Internal server error') {
        self::error($message, 500);
    }
}