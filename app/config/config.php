<?php

class Config {
    // Image upload settings - cPanel compatible paths
    const LOCALHOST_IMAGE_PATH = "./public/images/"; // For localhost development (relative to project root)
    const PRODUCTION_IMAGE_PATH = "./images/"; // For production hosting (relative to public_html)
    const BASE_URL = "https://apploqic.my/"; // Your actual domain
    
    public static function getImageUploadPath() {
        // Auto-detect environment
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
        
        if ($host === 'localhost' || strpos($host, '127.0.0.1') !== false) {
            // For XAMPP localhost development - use absolute path to prevent path resolution issues
            $projectRoot = dirname(dirname(__DIR__)); // Go up from app/config to project root
            $imagePath = $projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
            
            // Ensure the directory exists
            if (!is_dir($imagePath)) {
                if (!mkdir($imagePath, 0755, true)) {
                    error_log("Config: Failed to create image directory: " . $imagePath);
                }
            }
            
            return $imagePath;
        }
        
        // For production hosting - create images directory relative to current script location
        // Since the API files are in the root directory, images should be in ./images/
        $productionPath = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
        
        // Ensure the directory exists
        if (!is_dir($productionPath)) {
            if (!mkdir($productionPath, 0755, true)) {
                error_log("Config: Failed to create production image directory: " . $productionPath);
            }
        }
        
        return $productionPath;
    }
    
    public static function getBaseUrl() {
        // Auto-detect URL from current request
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        
        // Get host with better fallback handling
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
        $port = $_SERVER['SERVER_PORT'] ?? '80';
        
        // Check if we're on development server (port 8000)
        if ((strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) && ($port === '8000' || strpos($host, ':8000') !== false)) {
            // Development server - no project path needed
            return $protocol . '://' . $host . '/';
        }
        
        // For XAMPP localhost development, include the project path
        if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
            // Get the current script path to determine project directory
            $scriptPath = $_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '';
            $projectPath = '/Apploqic_Business_Directory/'; // Default project path
            
            // Extract project directory from script path if available
            if (strpos($scriptPath, '/Apploqic_Business_Directory/') !== false) {
                $projectPath = '/Apploqic_Business_Directory/';
            }
            
            return $protocol . '://' . $host . $projectPath;
        }
        
        // For production, use root domain
        return $protocol . '://' . $host . '/';
    }
    
    public static function getImageUrl($imageName) {
        if (!$imageName) return null;
        
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
        $port = $_SERVER['SERVER_PORT'] ?? '80';
        
        // Check for localhost environment (including port check for dev server)
        if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
            // For localhost development - images are in public/images/
            $imageUrl = self::getBaseUrl() . "public/images/" . $imageName;
            
            // Check if image file exists locally
            $projectRoot = dirname(dirname(__DIR__));
            $imagePath = $projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $imageName;
            
            if (!file_exists($imagePath)) {
                // Return null if image doesn't exist, let frontend handle fallback
                return null;
            }
            
            return $imageUrl;
        }
        
        // For production - images are directly in images/ folder from public_html root  
        return self::getBaseUrl() . "images/" . $imageName;
    }
}