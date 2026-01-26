<?php
/**
 * Client Environment Configuration
 * 
 * This file automatically detects whether the application is running
 * in development (localhost) or production (apploqic.my) environment
 * and sets appropriate API endpoints and paths.
 */

class ClientConfig {
    private static $isLocalhost = null;
    
    public static function isLocalhost() {
        if (self::$isLocalhost === null) {
            $hostname = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $port = $_SERVER['SERVER_PORT'] ?? '80';
            self::$isLocalhost = strpos($hostname, 'localhost') !== false || 
                               strpos($hostname, '127.0.0.1') !== false ||
                               strpos($hostname, 'xampp') !== false ||
                               $port === '8000' || $port === '8080';
        }
        return self::$isLocalhost;
    }
    
    public static function getApiBaseUrl() {
        if (self::isLocalhost()) {
            // For localhost development - build the full URL for fetch
            $protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            
            // If HTTP_HOST already contains port, use it as-is
            // Otherwise, get port from SERVER_PORT
            if (strpos($host, ':') === false) {
                $port = $_SERVER['SERVER_PORT'] ?? '80';
                if ($port !== '80' && $port !== '443') {
                    $host .= ':' . $port;
                }
            }
            
            // Get current directory path from script
            $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
            $basePath = ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir;
            
            // For development server, use current domain with proper path
            return "$protocol://$host$basePath/index.php?endpoint=business";
        } else {
            // Production environment (apploqic.my) - using index.php with query params
            return "https://apploqic.my/index.php?endpoint=business";
        }
    }
    
    public static function getBusinessApiUrl() {
        if (self::isLocalhost()) {
            // For localhost development - build the full URL for cURL
            $protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            
            // If HTTP_HOST already contains port, use it as-is
            // Otherwise, get port from SERVER_PORT
            if (strpos($host, ':') === false) {
                $port = $_SERVER['SERVER_PORT'] ?? '80';
                if ($port !== '80' && $port !== '443') {
                    $host .= ':' . $port;
                }
            }
            
            // Get current directory path from script
            $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
            $basePath = ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir;
            
            // For development server, use current domain with proper path
            return "$protocol://$host$basePath/index.php?endpoint=business&id=";
        } else {
            // Production environment (apploqic.my) - using index.php with query params
            return "https://apploqic.my/index.php?endpoint=business&id=";
        }
    }
    
    public static function getSearchApiUrl() {
        if (self::isLocalhost()) {
            // For localhost development - build the full URL for search
            $protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            
            // If HTTP_HOST already contains port, use it as-is
            // Otherwise, get port from SERVER_PORT
            if (strpos($host, ':') === false) {
                $port = $_SERVER['SERVER_PORT'] ?? '80';
                if ($port !== '80' && $port !== '443') {
                    $host .= ':' . $port;
                }
            }
            
            // Get current directory path from script
            $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
            $basePath = ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir;
            
            // For development server, use current domain with proper path
            return "$protocol://$host$basePath/index.php?endpoint=search";
        } else {
            // Production environment (apploqic.my) - using index.php with query params
            return "https://apploqic.my/index.php?endpoint=search";
        }
    }
    
    public static function getEnvironment() {
        return self::isLocalhost() ? 'development' : 'production';
    }
    
    public static function getAssetsPath($path) {
        if (self::isLocalhost()) {
            return "../../public/assets/" . $path; // Full path for development
        } else {
            return "./assets/" . $path; // Relative path for production
        }
    }
    
    public static function getImagesPath($imageName) {
        if (self::isLocalhost()) {
            return "../../public/images/" . $imageName; // Full path for development
        } else {
            return "./images/" . $imageName; // Relative path for production
        }
    }
    
    public static function getFallbackImagePath() {
        if (self::isLocalhost()) {
            return "../../public/assets/preview.png"; // Full path for development
        } else {
            return "./assets/preview.png"; // Relative path for production
        }
    }
}

// Global helper functions
function env_is_localhost() {
    return ClientConfig::isLocalhost();
}

function env_api_url() {
    return ClientConfig::getApiBaseUrl();
}

function env_business_api_url() {
    return ClientConfig::getBusinessApiUrl();
}

function env_search_api_url() {
    return ClientConfig::getSearchApiUrl();
}

function env_assets_path($path) {
    return ClientConfig::getAssetsPath($path);
}

function env_images_path($imageName) {
    return ClientConfig::getImagesPath($imageName);
}

function env_fallback_image() {
    return ClientConfig::getFallbackImagePath();
}
?>