<?php
// ===================================
// Security & API Configuration
// ===================================
$baseApiUrl = "https://apploqic.my/api/v1/business/"; // <-- note trailing slash

// 1. Input Validation and Sanitization
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400); // Bad Request
    die('Invalid business ID provided.');
}

$id = intval($_GET['id']);
$apiUrl = $baseApiUrl . $id; // Correctly forms full endpoint

// ===================================
// Fetch API Data using cURL (Recommended)
// ===================================
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Optional timeout
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// 2. Robust Error Checking
if ($response === false || $httpCode !== 200) {
    http_response_code(503); // Service Unavailable
    die("Failed to fetch business details. API Error: " . ($curlError ?: "HTTP Code $httpCode"));
}

$data = json_decode($response, true);

// 3. Data Structure Verification
if (!isset($data['data']) || empty($data['data'])) {
    http_response_code(404); // Not Found
    die('Business not found for ID: ' . $id);
}

$biz = $data['data'];

// 4. Fallback Logic and Output Sanitization
$businessImage = !empty($biz['business_img_url']) 
    ? htmlspecialchars($biz['business_img_url']) 
    : 'images/placeholder.png';
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Business Details</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #E0F2FE;
        margin: 0;
    }

    /* Navbar */
    .navbar {
        transition: top 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
        top: 0;
        position: fixed;
        width: 100%;
        z-index: 1030;
    }
    .navbar.hide {
        top: -100px;
    }

    /* Hero Section */
    .hero {
        min-height: 40vh;
        position: relative;
        background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
                    url('images/Banner2.png') center center / cover no-repeat;
        color: white;
        text-align: center;
        padding: 5rem 1rem;
        border-bottom-left-radius: 2rem;
        border-bottom-right-radius: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    .hero .container {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 1rem;
        padding: 2rem;
        display: inline-block;
        box-shadow: 0 4px 30px rgba(0,0,0,0.1);
        margin-top: 5rem;
    }
    .hero h1 {
        font-weight: 700;
        font-size: 2.5rem;
        margin: 0;
    }
    .hero p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0.5rem 0 0 0;
    }

    /* Business Card */
    .card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        padding: 2rem;
        display: flex;
        flex-direction: column;
    }

    .business-img {
        width: 100%;
        height: auto;
        border-radius: 1rem;
        object-fit: contain;
    }

    /* Right Column Info */
    .business-info {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .business-info .info-content {
        flex-grow: 1;
    }
    .back-btn {
        margin-top: 20px;
        align-self: stretch; /* Make button full width */
    }

    /* Responsive */
    @media (max-width: 768px) {
        .business-img {
            margin-bottom: 1.5rem;
            max-height: 300px;
        }
        .card {
            padding: 1.5rem;
        }
        .hero {
            padding: 3rem 1rem;
        }
        .hero h1 {
            font-size: 2rem;
        }
        .hero p {
            font-size: 1rem;
        }
        .business-info {
            flex-direction: column;
        }
        .back-btn {
            margin-top: auto;
        }
    }
</style>
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top transparent-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="images/APPLOQIC-LOGO-HORIZONTAL---WHITE.png" alt="Apploqic Logo" width="197" height="67" class="me-2">
        </a>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1><?= htmlspecialchars($biz['business_name']) ?></h1>
        <p>Business Details</p>
    </div>
</section>

<!-- Business Details Card -->
<div class="container my-5">
    <div class="card">
        <div class="row g-4 align-items-start">

            <!-- Left Column: Business Image -->
            <div class="col-md-5 text-center mb-3 mb-md-0">
                <img src="<?= $businessImage ?>" alt="<?= htmlspecialchars($biz['business_name']) ?>" class="business-img img-fluid">
            </div>

            <!-- Right Column: Business Info -->
            <div class="col-md-7 business-info">
                <div class="info-content">
                    <h5 class="fw-bold text-primary">ID</h5>
                    <p><?= htmlspecialchars($biz['id']) ?></p>

                    <h5 class="fw-bold text-primary">Name</h5>
                    <p><?= htmlspecialchars($biz['business_name']) ?></p>

                    <h5 class="fw-bold text-primary">Description</h5>
                    <p><?= htmlspecialchars($biz['business_description']) ?></p>

                    <h5 class="fw-bold text-primary">Contact</h5>
                    <p><?= htmlspecialchars($biz['business_contact']) ?></p>
                </div>

                <a href="#" onclick="history.back(); return false;" class="btn btn-outline-primary back-btn w-100">
                    <i class="bi bi-arrow-left"></i> Back to Directory
                </a>
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
