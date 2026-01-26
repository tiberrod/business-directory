<?php
/**
 * Documentation API Router with Authentication
 * 
 * Requires login before accessing any documentation
 * Built-in credentials: apploqic / apploqic
 */

// Start session with proper settings
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
session_start();

// Built-in authentication credentials
$AUTH_USERNAME = 'apploqic';
$AUTH_PASSWORD = 'apploqic';

// Simple authentication check
function isAuthenticated() {
    return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if ($username === $AUTH_USERNAME && $password === $AUTH_PASSWORD) {
        $_SESSION['authenticated'] = true;
        $_SESSION['auth_time'] = time();
        $_SESSION['username'] = $username;
        
        // Simple redirect to the interactive docs
        header('Location: interactive-api-docs.html');
        exit();
    } else {
        $login_error = 'Invalid credentials. Please try again.';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}

// If not authenticated, show login page
if (!isAuthenticated()) {
    showLoginPage($login_error ?? null);
    exit();
}

// If authenticated, redirect to interactive docs
header('Location: interactive-api-docs.html');
exit();

// Initialize database connection and controller only when needed
$database = null;
$db = null;
$docController = null;

function initializeController() {
    global $database, $db, $docController;
    if ($docController === null) {
        require_once __DIR__ . '/../app/config/database.php';
        require_once __DIR__ . '/../app/controllers/v1/DocumentationController.php';
        $database = new Database();
        $db = $database->getConnection();
        $docController = new DocumentationControllerV1($db);
    }
    return $docController;
}

/**
 * Display the login page
 */
function showLoginPage($error = null) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>🔐 Documentation Access - Login Required</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Noto Sans', Helvetica, Arial, sans-serif;
                background: linear-gradient(135deg, #0d1117 0%, #1e293b 100%);
                color: #f0f6fc;
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .login-container {
                background: #161b22;
                border: 1px solid #30363d;
                border-radius: 12px;
                padding: 3rem;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
                width: 100%;
                max-width: 450px;
                animation: fadeInUp 0.6s ease-out;
            }

            .login-header {
                text-align: center;
                margin-bottom: 2.5rem;
            }

            .login-header h1 {
                color: #58a6ff;
                font-size: 2rem;
                margin-bottom: 0.5rem;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
            }

            .login-header p {
                color: #8b949e;
                font-size: 1rem;
                line-height: 1.5;
            }

            .form-group {
                margin-bottom: 1.5rem;
            }

            .form-group label {
                display: block;
                margin-bottom: 0.75rem;
                color: #f0f6fc;
                font-weight: 600;
                font-size: 0.95rem;
            }

            .form-group input {
                width: 100%;
                padding: 1rem;
                background: #21262d;
                border: 2px solid #30363d;
                border-radius: 8px;
                color: #f0f6fc;
                font-size: 1rem;
                transition: all 0.3s ease;
            }

            .form-group input:focus {
                outline: none;
                border-color: #58a6ff;
                box-shadow: 0 0 0 3px rgba(88, 166, 255, 0.1);
                background: #161b22;
            }

            .login-btn {
                width: 100%;
                padding: 1rem;
                background: linear-gradient(135deg, #58a6ff, #1e3a8a);
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 1.1rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                margin-bottom: 1rem;
            }

            .login-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 15px rgba(88, 166, 255, 0.3);
                background: linear-gradient(135deg, #6eb5ff, #2563eb);
            }

            .login-btn:active {
                transform: translateY(0);
            }

            .error-message {
                background: rgba(248, 81, 73, 0.1);
                border: 2px solid rgba(248, 81, 73, 0.3);
                color: #ff6b6b;
                padding: 1rem;
                border-radius: 8px;
                margin-bottom: 1.5rem;
                text-align: center;
                font-weight: 500;
                animation: shake 0.5s ease-in-out;
            }

            .info-box {
                background: rgba(88, 166, 255, 0.1);
                border: 2px solid rgba(88, 166, 255, 0.3);
                color: #58a6ff;
                padding: 1rem;
                border-radius: 8px;
                margin-top: 1.5rem;
                text-align: center;
                font-size: 0.9rem;
            }

            .info-box strong {
                display: block;
                margin-bottom: 0.5rem;
                color: #f0f6fc;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }

            .footer {
                text-align: center;
                margin-top: 2rem;
                color: #6e7681;
                font-size: 0.85rem;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="login-header">
                <h1>🔐 Documentation Access</h1>
                <p>Please authenticate to access the Apploqic Business Directory API documentation</p>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">👤 Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        required 
                        autocomplete="username"
                        placeholder="Enter your username"
                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">🔑 Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                        placeholder="Enter your password"
                    >
                </div>
                
                <button type="submit" name="login" class="login-btn">
                    🚀 Access Documentation
                </button>
            </form>
            
            <div class="info-box">
                <strong>🔒 Secure Access Required</strong> 
                Authentication is required to protect the API documentation and maintain system security.
            </div>
            
            <div class="footer">
                Apploqic Business Directory API Documentation System v 1.0.0
            </div>
        </div>
        
        <script>
            // Simple form optimization
            document.querySelector('form').addEventListener('submit', function() {
                const btn = document.querySelector('.login-btn');
                btn.textContent = '🔄 Authenticating...';
                btn.disabled = true;
            });
        </script>
    </body>
    </html>
    <?php
}

// Parse the URL to determine the endpoint
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Remove the base path to get the endpoint
$basePath = '/Apploqic_Business_Directory/documentation/';
$endpoint = str_replace($basePath, '', $path);

// Handle query parameter routing as well
$action = $_GET['action'] ?? '';
if ($action) {
    $endpoint = $action;
}

// Route the requests for authenticated users
switch ($endpoint) {
    case '':
    case 'index':
    case 'index.php':
        // Serve the main interactive documentation
        serveInteractiveDocumentation();
        break;
        
    case 'interactive-api-docs.html':
        // Serve the interactive documentation HTML directly
        $docPath = __DIR__ . '/interactive-api-docs.html';
        if (file_exists($docPath)) {
            // Add authentication status to the HTML
            $content = file_get_contents($docPath);
            
            // Insert authentication info into the HTML
            $authInfo = '<script>
                // User is already authenticated via PHP session
                sessionStorage.setItem("apploqic_authenticated", "true");
                sessionStorage.setItem("php_authenticated", "true");
                console.log("User authenticated via PHP session");
            </script>';
            
            $content = str_replace('</head>', $authInfo . '</head>', $content);
            
            header('Content-Type: text/html; charset=UTF-8');
            echo $content;
        } else {
            http_response_code(404);
            echo json_encode(['status' => 404, 'message' => 'Documentation file not found']);
        }
        break;
        
    case 'api/auth':
    case 'auth':
        // Handle authentication API (already authenticated via PHP session)
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'Already authenticated',
            'authenticated' => true,
            'username' => $_SESSION['username'] ?? 'apploqic'
        ]);
        break;
        
    case 'api/overview':
    case 'overview':
        // Get API overview
        if ($method === 'GET') {
            $controller = initializeController();
            $controller->overview();
        } else {
            sendMethodNotAllowed('Only GET is supported for overview.');
        }
        break;
        
    case 'api/endpoints':
    case 'endpoints':
        // Get endpoint list
        if ($method === 'GET') {
            $controller = initializeController();
            $controller->endpointList();
        } else {
            sendMethodNotAllowed('Only GET is supported for endpoints.');
        }
        break;
        
    case 'api/version':
    case 'version':
        // Get version information
        if ($method === 'GET') {
            $controller = initializeController();
            $controller->version();
        } else {
            sendMethodNotAllowed('Only GET is supported for version.');
        }
        break;
        
    case 'api/patches':
    case 'patches':
        // Get patch notes
        if ($method === 'GET') {
            $controller = initializeController();
            $controller->patches();
        } else {
            sendMethodNotAllowed('Only GET is supported for patches.');
        }
        break;
        
    case 'logout':
        // Handle logout
        session_destroy();
        header('Location: index.php');
        exit;
        
    default:
        // Check if it's a static asset request
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg)$/', $endpoint)) {
            // Serve static files
            $filePath = __DIR__ . '/' . $endpoint;
            if (file_exists($filePath)) {
                $mimeTypes = [
                    'css' => 'text/css',
                    'js' => 'application/javascript',
                    'png' => 'image/png',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'ico' => 'image/x-icon',
                    'svg' => 'image/svg+xml'
                ];
                
                $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
                
                header('Content-Type: ' . $mimeType);
                header('Cache-Control: public, max-age=3600');
                readfile($filePath);
                exit;
            }
        }
        
        // Serve main documentation for unknown routes
        serveInteractiveDocumentation();
        break;
}

/**
 * Serve the main interactive documentation
 */
function serveInteractiveDocumentation() {
    $docPath = __DIR__ . '/interactive-api-docs.html';
    
    if (file_exists($docPath)) {
        // Get the file content
        $content = file_get_contents($docPath);
        
        // Add PHP session authentication to JavaScript
        $authScript = '<script>
            // User is authenticated via PHP session
            sessionStorage.setItem("apploqic_authenticated", "true");
            sessionStorage.setItem("php_authenticated", "true");
            sessionStorage.setItem("auth_method", "php_session");
            
            // Add logout handler for PHP session
            function handlePhpLogout() {
                window.location.href = "?logout=1";
            }
            
            // Override the logout button when page loads
            document.addEventListener("DOMContentLoaded", function() {
                const logoutBtn = document.getElementById("logoutBtn");
                if (logoutBtn) {
                    logoutBtn.onclick = handlePhpLogout;
                }
                
                // Show main interface immediately since already authenticated
                const loginContainer = document.getElementById("loginContainer");
                const mainContainer = document.getElementById("mainContainer");
                if (loginContainer && mainContainer) {
                    loginContainer.style.display = "none";
                    mainContainer.style.display = "block";
                }
            });
        </script>';
        
        // Insert the auth script before closing head tag
        $content = str_replace('</head>', $authScript . '</head>', $content);
        
        // Set appropriate headers
        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        
        echo $content;
    } else {
        http_response_code(404);
        echo json_encode([
            'status' => 404,
            'message' => 'Documentation not found'
        ]);
    }
}

/**
 * Send method not allowed response
 */
function sendMethodNotAllowed($message) {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 405,
        'message' => 'Method Not Allowed. ' . $message
    ]);
}