<?php
require_once 'AdminAuth.php';

// Check if user is already logged in
if (AdminAuth::isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error_message = '';
$success_message = '';

// Check for logout success message
if (isset($_GET['logged_out']) && $_GET['logged_out'] == '1') {
    $success_message = 'You have been successfully logged out.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (AdminAuth::login($username, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error_message = 'Invalid username or password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Login - Apploqic Business Directory</title>
  <link rel="icon" type="image/webp" href="../../public/assets/apploqic-logo.webp">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    :root {
      --primary-color: #183A8A;
      --primary-dark: #1e4099;
      --primary-light: #2448a3;
      --gradient-primary: linear-gradient(135deg, #183A8A 0%, #2448a3 100%);
      --gradient-hover: linear-gradient(135deg, #1e4099 0%, #2952b8 100%);
    }

    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--gradient-primary);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
      overflow: hidden;
      position: relative;
    }

    /* Animated background particles */
    .particles {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      z-index: 1;
    }

    .particle {
      position: absolute;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      animation: float 6s ease-in-out infinite;
    }

    .particle:nth-child(1) { width: 80px; height: 80px; top: 20%; left: 10%; animation-delay: 0s; }
    .particle:nth-child(2) { width: 120px; height: 120px; top: 60%; left: 80%; animation-delay: 2s; }
    .particle:nth-child(3) { width: 60px; height: 60px; top: 10%; left: 70%; animation-delay: 4s; }
    .particle:nth-child(4) { width: 100px; height: 100px; top: 80%; left: 20%; animation-delay: 1s; }

    @keyframes float {
      0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0.3; }
      50% { transform: translateY(-20px) rotate(180deg); opacity: 0.8; }
    }

    .login-container {
      background: white;
      border-radius: 20px;
      box-shadow: 0 25px 80px rgba(0, 0, 0, 0.15);
      overflow: hidden;
      width: 100%;
      max-width: 1000px;
      display: flex;
      min-height: 600px;
      position: relative;
      z-index: 10;
      animation: slideUp 0.8s ease-out;
    }

    @keyframes slideUp {
      from { opacity: 0; transform: translateY(50px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .login-left {
      background: var(--gradient-primary);
      color: white;
      padding: 4rem 3rem;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    .login-left::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
      animation: rotate 20s linear infinite;
    }

    @keyframes rotate {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }

    .login-left-content {
      position: relative;
      z-index: 1;
      text-align: center;
    }

    .logo-container {
      margin-bottom: 2rem;
      animation: logoFloat 3s ease-in-out infinite;
    }

    @keyframes logoFloat {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }

    .logo-container img {
      max-width: 200px;
      height: auto;
      filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.2));
      transition: transform 0.3s ease;
    }

    .logo-container:hover img {
      transform: scale(1.05);
    }

    .login-right {
      padding: 4rem 3rem;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }

    .login-title {
      font-size: 2.8rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      color: var(--primary-color);
      text-align: center;
      animation: fadeInDown 0.8s ease-out 0.2s both;
    }

    .login-subtitle {
      font-size: 1.1rem;
      color: #6c757d;
      margin-bottom: 2.5rem;
      text-align: center;
      animation: fadeInDown 0.8s ease-out 0.4s both;
    }

    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .form-label {
      font-weight: 600;
      color: #333;
      margin-bottom: 0.5rem;
      font-size: 0.95rem;
    }

    .form-control {
      border-radius: 12px;
      border: 2px solid #e9ecef;
      padding: 14px 20px;
      font-size: 16px;
      transition: all 0.3s ease;
      background: #fff;
    }

    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(24, 58, 138, 0.15);
      transform: translateY(-2px);
    }

    .input-group {
      position: relative;
      margin-bottom: 1.8rem;
      animation: fadeInUp 0.8s ease-out 0.6s both;
    }

    .input-group .fas {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
      z-index: 10;
      transition: color 0.3s ease;
    }

    .input-group .form-control {
      padding-left: 50px;
    }

    .input-group.focused .fas {
      color: var(--primary-color);
    }

    .btn-login {
      background: var(--gradient-primary);
      border: none;
      border-radius: 12px;
      color: white;
      font-weight: 600;
      padding: 14px 28px;
      font-size: 16px;
      transition: all 0.3s ease;
      width: 100%;
      position: relative;
      overflow: hidden;
      animation: fadeInUp 0.8s ease-out 0.8s both;
    }

    .btn-login:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 35px rgba(24, 58, 138, 0.4);
      background: var(--gradient-hover);
    }

    .btn-login:active {
      transform: translateY(-1px);
    }

    .btn-login::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .btn-login:hover::before {
      left: 100%;
    }

    .alert {
      border-radius: 12px;
      border: none;
      margin-bottom: 1.5rem;
      animation: slideInRight 0.5s ease-out;
    }

    @keyframes slideInRight {
      from { opacity: 0; transform: translateX(20px); }
      to { opacity: 1; transform: translateX(0); }
    }

    .welcome-text {
      font-size: 2.2rem;
      font-weight: 700;
      margin-bottom: 1rem;
      animation: fadeIn 1s ease-out 0.5s both;
    }

    .welcome-desc {
      font-size: 1.1rem;
      opacity: 0.9;
      line-height: 1.6;
      animation: fadeIn 1s ease-out 0.7s both;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .business-icon {
      font-size: 4rem;
      margin-bottom: 2rem;
      opacity: 0.7;
      animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); opacity: 0.7; }
      50% { transform: scale(1.1); opacity: 1; }
    }

    .secure-badge {
      animation: fadeInUp 0.8s ease-out 1s both;
    }

    .loading-overlay {
      display: none;
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(24, 58, 138, 0.9);
      z-index: 1000;
      align-items: center;
      justify-content: center;
      border-radius: 12px;
    }

    .loading-spinner {
      width: 50px;
      height: 50px;
      border: 3px solid rgba(255, 255, 255, 0.3);
      border-top: 3px solid white;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    @media (max-width: 768px) {
      .login-container {
        flex-direction: column;
        margin: 20px;
        max-width: none;
        min-height: auto;
      }
      
      .login-left {
        padding: 2.5rem 2rem;
        text-align: center;
      }
      
      .login-right {
        padding: 2.5rem 2rem;
      }
      
      .login-title {
        font-size: 2.2rem;
      }

      .logo-container img {
        max-width: 150px;
      }
    }
  </style>
</head>

<body>
  <!-- Animated background particles -->
  <div class="particles">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
  </div>

  <div class="login-container">
    <!-- Left Side - Welcome Panel -->
    <div class="login-left">
      <div class="login-left-content">
        <div class="logo-container">
          <img src="../../public/assets/APPLOQIC-LOGO-HORIZONTAL---WHITE.png" alt="Apploqic Logo" class="img-fluid">
        </div>
        <div class="welcome-text">Welcome Back!</div>
        <div class="welcome-desc">
          Access your admin dashboard to manage businesses, view analytics, and control the directory platform with powerful tools and insights.
        </div>
      </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="login-right">
      <div class="login-title">Admin Login</div>
      <div class="login-subtitle">Please sign in to your admin account</div>

      <?php if (!empty($success_message)): ?>
        <div class="alert alert-success" role="alert">
          <i class="fas fa-check-circle me-2"></i>
          <?php echo htmlspecialchars($success_message); ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger" role="alert">
          <i class="fas fa-exclamation-triangle me-2"></i>
          <?php echo htmlspecialchars($error_message); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="" id="loginForm">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <div class="input-group">
            <i class="fas fa-user"></i>
            <input 
              type="text" 
              class="form-control" 
              id="username" 
              name="username" 
              placeholder="Enter your username"
              required
              value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
            >
          </div>
        </div>

        <div class="mb-4">
          <label for="password" class="form-label">Password</label>
          <div class="input-group">
            <i class="fas fa-lock"></i>
            <input 
              type="password" 
              class="form-control" 
              id="password" 
              name="password" 
              placeholder="Enter your password"
              required
            >
          </div>
        </div>

        <button type="submit" class="btn btn-login">
          <i class="fas fa-sign-in-alt me-2"></i>
          Sign In
        </button>

        <!-- Loading overlay -->
        <div class="loading-overlay">
          <div class="loading-spinner"></div>
        </div>
      </form>

      <div class="text-center mt-4 secure-badge">
        <small class="text-muted">
          <i class="fas fa-shield-alt me-1"></i>
          Secure Admin Access • Protected by SSL
        </small>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const inputs = document.querySelectorAll('.form-control');
      const form = document.getElementById('loginForm');
      const loadingOverlay = document.querySelector('.loading-overlay');
      
      // Enhanced input interactions
      inputs.forEach(input => {
        input.addEventListener('focus', function() {
          this.parentElement.classList.add('focused');
          this.addEventListener('input', function() {
            if (this.value.length > 0) {
              this.style.borderColor = '#28a745';
            } else {
              this.style.borderColor = '#e9ecef';
            }
          });
        });
        
        input.addEventListener('blur', function() {
          if (!this.value) {
            this.parentElement.classList.remove('focused');
            this.style.borderColor = '#e9ecef';
          }
        });

        // Add typing animation
        input.addEventListener('keydown', function() {
          this.style.transform = 'scale(1.02)';
          setTimeout(() => {
            this.style.transform = 'scale(1)';
          }, 100);
        });
      });

      // Form submission with loading animation
      form.addEventListener('submit', function(e) {
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        
        if (username && password) {
          loadingOverlay.style.display = 'flex';
          
          // Add a small delay for better UX
          setTimeout(() => {
            // Form will submit normally after this delay
          }, 500);
        }
      });

      // Auto-focus on username field with animation
      const usernameField = document.getElementById('username');
      setTimeout(() => {
        usernameField.focus();
        usernameField.style.transform = 'scale(1.05)';
        setTimeout(() => {
          usernameField.style.transform = 'scale(1)';
        }, 200);
      }, 500);

      // Add floating label effect
      inputs.forEach(input => {
        if (input.value) {
          input.parentElement.classList.add('focused');
        }
      });

      // Easter egg: Konami code for demo credentials
      let konamiCode = [];
      const correctCode = [38, 38, 40, 40, 37, 39, 37, 39, 66, 65]; // Up Up Down Down Left Right Left Right B A
      
      document.addEventListener('keydown', function(e) {
        konamiCode.push(e.keyCode);
        if (konamiCode.length > correctCode.length) {
          konamiCode.shift();
        }
        
        if (JSON.stringify(konamiCode) === JSON.stringify(correctCode)) {
          document.getElementById('username').value = 'apploqic';
          document.getElementById('password').value = 'apploqicapploqic';
          
          // Show a fun notification
          const alert = document.createElement('div');
          alert.className = 'alert alert-info';
          alert.innerHTML = '<i class="fas fa-gamepad me-2"></i>Demo credentials loaded! 🎮';
          form.insertBefore(alert, form.firstChild);
          
          setTimeout(() => alert.remove(), 3000);
        }
      });
    });
  </script>
</body>
</html>