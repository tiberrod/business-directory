<?php
// Require admin authentication
require_once 'AdminAuth.php';
AdminAuth::requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Business Details</title>
  <link rel="icon" type="image/webp" href="../../../public/assets/apploqic-logo.webp">
  <!-- Bootstrap and custom stylesheet -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="css/admin.css" />
</head>

<body>
  <header class="admin-header">
    <div class="d-flex align-items-center">
      <h4 class="mb-0">Business Details</h4>
    </div>
    <div class="d-flex gap-2 align-items-center">
      <a href="../../admin.php" class="btn btn-light">← Back to Directory</a>
      <a href="logout.php" class="btn btn-outline-light" onclick="return confirm('Are you sure you want to logout?')">
        <i class="fas fa-sign-out-alt me-1"></i>
        Logout
      </a>
    </div>
  </header>

  <div class="container my-5">
    <div class="details-container card shadow-sm">
      <div class="row g-3 p-3">
        <div class="col-md-6">
          <img id="businessImage" src="../../public/assets/preview.png" alt="Business Image" class="img-fluid rounded" />
        </div>
        <div class="col-md-6">
          <h2 id="businessName">Loading...</h2>
          <div class="meta">
            <p><strong>Category:</strong> <span id="businessCategory">-</span></p>
            <p><strong>Contact:</strong> <span id="businessContact">-</span></p>
            <p><strong>Featured:</strong> <span id="businessFeatured">-</span></p>
            <p><strong>Created:</strong> <span id="businessCreated">-</span></p>
            <p><strong>Updated:</strong> <span id="businessUpdated">-</span></p>
          </div>
          <span id="businessStatus" class="status-pill">Loading...</span>
        </div>
      </div>

      <div class="business-description mt-4 p-2">
        <h4>Description</h4>
        <p id="businessDescription">Loading business details...</p>
      </div>
    </div>
  </div>

  <script src="js/admin-api.js"></script>
  <script src="js/view-details.js"></script>
</body>
</html>