<?php
// ===================================
// Security & API Configuration
// ===================================

// Include environment configuration
require_once 'config.php';

// 1. Input Validation and Sanitization
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400); // Bad Request
    die('Invalid business ID provided.');
}

$id = intval($_GET['id']);

// ===================================
// Direct Database Access (Better for same-server calls)
// ===================================
try {
    // Use the same database connection as the API
    require_once __DIR__ . '/../../app/config/database.php';
    require_once __DIR__ . '/../../app/models/Base/BusinessModel.php';
    
    $database = new Database();
    $db = $database->getConnection();
    $model = new BusinessModel($db, 'v1');
    
    // Get business data directly from model instead of through controller
    $business = $model->getById($id);
    
    if (!$business) {
        http_response_code(404); // Not Found
        die('Business not found for ID: ' . $id);
    }
    
    $biz = $business;
    
    // Add business_img_url if not present (like the API does)
    if (!empty($biz['business_img']) && empty($biz['business_img_url'])) {
        if (env_is_localhost()) {
            $biz['business_img_url'] = 'http://localhost:8000/public/images/' . $biz['business_img'];
        } else {
            $biz['business_img_url'] = 'https://apploqic.my/images/' . $biz['business_img'];
        }
    }
    
} catch (Exception $e) {
    http_response_code(503); // Service Unavailable
    die("Failed to fetch business details. Error: " . $e->getMessage());
}

// 4. Environment-aware Image Fallback Logic
function getBusinessImageUrl($business, $isLocalhost) {
    // Priority: business_img_url > business_img with base path > fallback
    if (!empty($business['business_img_url'])) {
        return htmlspecialchars($business['business_img_url']);
    }
    
    if (!empty($business['business_img'])) {
        if ($isLocalhost) {
            return '../../public/images/' . htmlspecialchars($business['business_img']);
        } else {
            return './images/' . htmlspecialchars($business['business_img']);
        }
    }
    
    // Environment-aware fallback
    return $isLocalhost ? '../../public/assets/preview.png' : './assets/preview.png';
}

$businessImage = getBusinessImageUrl($biz, env_is_localhost());
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?= htmlspecialchars($biz['business_name']) ?> - Business Directory</title>
<link rel="icon" type="image/webp" href="../../../public/assets/apploqic-logo.webp">
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

<!-- Client-specific CSS -->
<?php
// Environment-aware CSS path
if (env_is_localhost()) {
    echo '<link rel="stylesheet" href="views/client/css/client.css">';
} else {
    // Production path when uploaded to apploqic.my
    echo '<link rel="stylesheet" href="./views/client/css/client.css">';
}
?>

<style>
.hero-content {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 1rem;
    padding: 2rem;
    display: inline-block;
    margin-top: 2rem;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.card {
    transition: transform 0.2s ease-in-out;
    border: none !important;
}

.card:hover {
    transform: translateY(-5px);
}

.business-img {
    transition: none;
    max-height: 300px;
    width: 100%;
    object-fit: contain;
    border-radius: 0.5rem;
    background-color: #f8f9fa;
}

.business-img:hover {
    transform: none;
}

.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.navbar {
    transition: all 0.3s ease;
    background: rgba(30, 58, 138, 0.95) !important;
    backdrop-filter: blur(10px);
}

.navbar.hide {
    transform: translateY(-100%);
}

/* Responsive image adjustments */
@media (max-width: 768px) {
    .business-img {
        max-height: 250px;
        margin-bottom: 1rem;
    }
}

@media (min-width: 769px) {
    .business-img {
        max-height: 300px;
    }
}
</style>
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top transparent-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="../../../public/assets/APPLOQIC-LOGO-HORIZONTAL---WHITE.png" alt="Apploqic Logo" width="197" height="67" class="me-2">
        </a>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero" style="background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('../../../public/assets/Banner2.png') center center / cover no-repeat;">
    <div class="container">
        <div class="hero-content">
            <div class="d-flex align-items-center justify-content-center mb-3">
                <i class="bi bi-building text-white me-2" style="font-size: 2rem;"></i>
                <h1 class="mb-0"><?= htmlspecialchars($biz['business_name']) ?></h1>
            </div>
            <p class="fs-5 opacity-90">Business Details & Information</p>
            <?php if (!empty($biz['business_category'])): ?>
                <div class="mt-3">
                    <span class="badge bg-light text-dark fs-6 px-3 py-2"><?= htmlspecialchars($biz['business_category']) ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Business Details Card -->
<div class="container my-5">
    <div class="card shadow-lg border-0">
        <div class="card-body p-4">
            <div class="row g-4 align-items-start">

                <!-- Left Column: Business Image -->
                <div class="col-md-5 text-center mb-3 mb-md-0">
                    <img src="<?= $businessImage ?>" alt="<?= htmlspecialchars($biz['business_name']) ?>" class="business-img img-fluid rounded shadow-sm">
                    <?php if (!empty($biz['business_category'])): ?>
                        <div class="mt-3">
                            <span class="badge bg-primary fs-6"><?= htmlspecialchars($biz['business_category']) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($biz['is_featured'] == 1): ?>
                        <div class="mt-2">
                            <span class="badge bg-warning text-dark fs-6"><i class="bi bi-star-fill me-1"></i>Featured</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right Column: Business Info -->
                <div class="col-md-7 business-info">
                    <div class="info-content">
                        <div class="mb-4">
                            <h2 class="text-primary fw-bold mb-3"><?= htmlspecialchars($biz['business_name']) ?></h2>
                            
                            <?php if (!empty($biz['business_description'])): ?>
                                <div class="mb-4">
                                    <h5 class="fw-bold text-secondary mb-2"><i class="bi bi-info-circle me-2"></i>Description</h5>
                                    <p class="lead"><?= nl2br(htmlspecialchars($biz['business_description'])) ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($biz['business_contact'])): ?>
                                <div class="mb-4">
                                    <h5 class="fw-bold text-secondary mb-2"><i class="bi bi-telephone me-2"></i>Contact</h5>
                                    <p class="fs-5">
                                        <?php if (filter_var($biz['business_contact'], FILTER_VALIDATE_EMAIL)): ?>
                                            <a href="mailto:<?= htmlspecialchars($biz['business_contact']) ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($biz['business_contact']) ?>
                                            </a>
                                        <?php elseif (preg_match('/^\+?[0-9\s\-\(\)]+$/', $biz['business_contact'])): ?>
                                            <a href="tel:<?= preg_replace('/[^\+0-9]/', '', $biz['business_contact']) ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($biz['business_contact']) ?>
                                            </a>
                                        <?php else: ?>
                                            <?= htmlspecialchars($biz['business_contact']) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <h6 class="fw-bold text-muted">Business ID</h6>
                                    <p class="mb-0">#<?= htmlspecialchars($biz['id']) ?></p>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <h6 class="fw-bold text-muted">Status</h6>
                                    <p class="mb-0">
                                        <span class="badge <?= $biz['status'] == 1 ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= $biz['status'] == 1 ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </p>
                                </div>
                                <?php if (!empty($biz['created_at'])): ?>
                                    <div class="col-sm-6 mb-3">
                                        <h6 class="fw-bold text-muted">Joined</h6>
                                        <p class="mb-0"><?= date('M d, Y', strtotime($biz['created_at'])) ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($biz['expiry_date']) && $biz['expiry_date'] !== '0000-00-00 00:00:00'): ?>
                                    <div class="col-sm-6 mb-3">
                                        <h6 class="fw-bold text-muted">Expires</h6>
                                        <p class="mb-0"><?= date('M d, Y', strtotime($biz['expiry_date'])) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="index.php" class="btn btn-outline-primary flex-fill">
                            <i class="bi bi-arrow-left me-1"></i> Back to Directory
                        </a>
                        <?php if (!empty($biz['business_contact']) && filter_var($biz['business_contact'], FILTER_VALIDATE_EMAIL)): ?>
                            <a href="mailto:<?= htmlspecialchars($biz['business_contact']) ?>" class="btn btn-primary">
                                <i class="bi bi-envelope me-1"></i> Contact
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- JS: Hide Navbar on Scroll -->
<script>
let lastScrollTop = 0;
const navbar = document.querySelector('.navbar');

window.addEventListener('scroll', function() {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (scrollTop > lastScrollTop && scrollTop > 50) {
        navbar.classList.add('hide'); // Hide on scroll down
    } else {
        navbar.classList.remove('hide'); // Show on scroll up
    }

    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
});
</script>

</body>
</html>