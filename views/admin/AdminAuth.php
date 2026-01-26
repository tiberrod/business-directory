<?php
/**
 * Admin Session Management
 * Handles login/logout and session authentication
 */

class AdminAuth {
    
    /**
     * Safely start session if not already started
     */
    private static function startSession() {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
            return true;
        }
        return session_status() === PHP_SESSION_ACTIVE;
    }
    
    /**
     * Check if admin is logged in
     * @return bool
     */
    public static function isLoggedIn() {
        if (!self::startSession()) {
            return false;
        }
        
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    /**
     * Require admin login - redirect to login page if not authenticated
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }
    
    /**
     * Login admin with credentials
     * @param string $username
     * @param string $password
     * @return bool
     */
    public static function login($username, $password) {
        // Hardcoded credentials
        if ($username === 'apploqic' && $password === 'apploqicapploqic') {
            if (!self::startSession()) {
                return false;
            }
            
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            $_SESSION['admin_login_time'] = time();
            
            // Regenerate session ID for security (only if session is active)
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Logout admin and destroy session
     */
    public static function logout() {
        if (!self::startSession()) {
            return;
        }
        
        // Clear all session variables
        $_SESSION = array();
        
        // Delete the session cookie if it exists
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy the session
        session_destroy();
    }
    
    /**
     * Get admin username
     * @return string|null
     */
    public static function getUsername() {
        if (!self::startSession()) {
            return null;
        }
        
        return $_SESSION['admin_username'] ?? null;
    }
    
    /**
     * Get login time
     * @return int|null
     */
    public static function getLoginTime() {
        if (!self::startSession()) {
            return null;
        }
        
        return $_SESSION['admin_login_time'] ?? null;
    }
    
    /**
     * Check if session is expired (optional - 24 hours timeout)
     * @return bool
     */
    public static function isSessionExpired($timeout = 86400) { // 24 hours default
        $loginTime = self::getLoginTime();
        
        if (!$loginTime) {
            return true;
        }
        
        return (time() - $loginTime) > $timeout;
    }
}
?>