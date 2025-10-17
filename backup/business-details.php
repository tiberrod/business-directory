<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid business ID.');
}

$id = intval($_GET['id']);
$apiUrl = "https://apploqic.my/index.php?endpoint=business&id=" . $id;

$response = @file_get_contents($apiUrl);
$data = json_decode($response, true);

if (!$data || !isset($data['data']) || empty($data['data'])) {
    die('Business not found.');
}

$biz = $data['data'];

// Check if image exists, else use placeholder
$businessImage = !empty($biz['business_img_url']) 
    ? htmlspecialchars($biz['business_img_url']) 
    : 'images/placeholder.png';  // <-- add a default placeholder image in your project
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($biz['business_name']) ?> - Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #E0F2FE; margin: 0; }

        /* Navbar */
        .navbar { padding-top: 0.05rem; padding-bottom: 0.05rem; transition: background-color 0.3s ease, box-shadow 0.3s ease; }
        .transparent-navbar { background-color: transparent; }
        .navbar.scrolled { background-color: #1E3A8A; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }

        /* Hero */
        .hero {
            min-height: 40vh;
            position: relative;
            background: 
                linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
                url('images/bg2.jpg') center center / cover no-repeat;
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
        }
        .hero h1 { font-weight: 700; font-size: 2.5rem; margin: 0; }
        .hero p { font-size: 1.1rem; opacity: 0.9; margin-top: 0.5rem; margin-bottom: 0; }

        /* Card */
        .card { border: none; border-radius: 1rem; box-shadow: 0 4px 8px rgba(0,0,0,0.05); padding: 2rem; }

        /* Image */
        .business-img { width: 100%; height: auto; border-radius: 1rem; object-fit: contain; }

        /* Back button */
        .back-btn { margin-top: 20px; }

        @media (max-width: 768px) {
            .business-img { margin-bottom: 1.5rem; }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top transparent-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <img src="images/APPLOQIC-LOGO-HORIZONTAL---WHITE.png" alt="Apploqic Logo" height="50">
        </a>
    </div>
</nav>

<!-- Hero -->
<section class="hero">
    <div class="container">
        <h1><?= htmlspecialchars($biz['business_name']) ?></h1>
        <p>Business Details</p>
    </div>
</section>

<!-- Business Details -->
<div class="container my-5">
    <div class="card">
        <div class="row g-4 align-items-center">
            <!-- Left Column: Image -->
            <div class="col-md-5 text-center">
                <img src="<?= $businessImage ?>" 
                    alt="<?= htmlspecialchars($biz['business_name']) ?>" 
                    class="business-img img-fluid">
            </div>

            <!-- Right Column: Info -->
            <div class="col-md-7">
                <h5 class="fw-bold text-primary">Name</h5>
                <p><?= htmlspecialchars($biz['business_name']) ?></p>

                <h5 class="fw-bold text-primary">Description</h5>
                <p><?= htmlspecialchars($biz['business_description']) ?></p>

                <h5 class="fw-bold text-primary">Contact</h5>
                <p><?= htmlspecialchars($biz['business_contact']) ?></p>

                <a href="index.php" class="btn btn-outline-primary back-btn">
                    <i class="bi bi-arrow-left"></i> Back to Directory
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) navbar.classList.add('scrolled');
        else navbar.classList.remove('scrolled');
    });
</script>

</body>
</html>
