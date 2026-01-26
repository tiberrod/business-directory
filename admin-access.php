<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Access - Apploqic Business Directory</title>
    <link rel="icon" type="image/webp" href="public/assets/apploqic-logo.webp">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #183A8A 0%, #2448a3 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .access-card {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            max-width: 400px;
        }
        .logo {
            max-width: 200px;
            margin-bottom: 2rem;
        }
        .btn-admin {
            background: linear-gradient(135deg, #183A8A 0%, #2448a3 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            padding: 12px 24px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(24, 58, 138, 0.3);
            color: white;
        }
    </style>
</head>
<body>
    <div class="access-card">
        <img src="public/assets/apploqic-logo.webp" alt="Apploqic Logo" class="logo">
        <h2 class="mb-3">Admin Access</h2>
        <p class="text-muted mb-4">Access the admin dashboard to manage your business directory</p>
        <a href="views/admin/login.php" class="btn btn-admin btn-lg">
            <i class="fas fa-sign-in-alt me-2"></i>
            Admin Login
        </a>
        <div class="mt-4">
            <small class="text-muted">
                <i class="fas fa-shield-alt me-1"></i>
                Secure Access Required
            </small>
        </div>
    </div>
</body>
</html>