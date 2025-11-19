<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Apploqic Business Directory</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Branding -->
        <div class="left-panel">
            <div class="branding">
                <img src="../images/APPLOQIC-LOGO-HORIZONTAL---WHITE.png" alt="Apploqic Technologies" class="logo">
                <h1 class="welcome-title">Welcome Back!</h1>
                <p class="welcome-text">
                    Access your admin dashboard to manage businesses, view analytics, and control the directory platform with powerful tools and insights.
                </p>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="right-panel">
            <div class="login-form-wrapper">
                <h2 class="form-title">Admin Login</h2>
                <p class="form-subtitle">Please sign in to your admin account</p>

                <div class="alert alert-danger d-none" role="alert" id="errorAlert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <span id="errorMessage">Invalid username or password</span>
                </div>

                <form id="loginForm">
                    <div class="mb-4">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-icon">
                                <i class="bi bi-person"></i>
                            </span>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="username" 
                                name="username" 
                                placeholder="Enter your username" 
                                required
                                autocomplete="username"
                            >
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-icon">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password" 
                                name="password" 
                                placeholder="Enter your password" 
                                required
                                autocomplete="current-password"
                            >
                        </div>
                    </div>

                    <button type="submit" class="btn-signin">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Sign In
                    </button>

                </form>
            </div>
        </div>
    </div>

    <script src="../src/login.js"></script>
</body>
</html>