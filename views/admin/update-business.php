<?php
// Require admin authentication
require_once 'AdminAuth.php';
AdminAuth::requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Business Details</title>
  <link rel="icon" type="image/webp" href="../../../public/assets/apploqic-logo.webp">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="css/admin.css">
</head>
<body>
  <!-- Navbar / Admin Header -->
  <header class="admin-header">
    <div class="d-flex align-items-center">
      <h4 class="mb-0">Update Business</h4>
    </div>
    <div class="d-flex gap-2 align-items-center">
      <a href="index.php" class="btn btn-light">← Back to Dashboard</a>
      <a href="logout.php" class="btn btn-outline-light" onclick="return confirm('Are you sure you want to logout?')">
        <i class="fas fa-sign-out-alt me-1"></i>
        Logout
      </a>
    </div>
  </header>

  <main class="container my-5">
    <form id="updateBusinessForm" enctype="multipart/form-data" class="inner-container">
      <h3 class="text-center mb-4 fw-bold text-primary">Update Business Details</h3>

      <div class="form-group mb-3">
        <label class="fw-bold h5" for="businessId">Business ID (required)</label>
        <input type="number" id="businessId" name="id" class="form-control" placeholder="Enter existing business ID" required>
      </div>

      <div class="form-group mb-3">
        <label class="fw-bold h5" for="businessName">New Business Name</label>
        <input type="text" id="businessName" name="name" class="form-control" placeholder="Leave blank to keep current">
      </div>

      <div class="form-group mb-3">
        <label class="fw-bold h5" for="businessContact">New Business Contact</label>
        <input type="text" id="businessContact" name="contact" class="form-control" placeholder="Leave blank to keep current">
      </div>

      <div class="form-group mb-3">
        <label class="fw-bold h5" for="businessCategory">New Category</label>
        <select id="businessCategory" name="category" class="form-select">
          <option value="">Keep current category</option>
          <option value="Restaurant">Restaurant</option>
          <option value="Retail">Retail</option>
          <option value="IT Service">IT Service</option>
          <option value="Healthcare">Healthcare</option>
          <option value="Education">Education</option>
          <option value="Entertainment">Entertainment</option>
          <option value="Professional Services">Professional Services</option>
          <option value="Automotive">Automotive</option>
          <option value="Beauty & Wellness">Beauty & Wellness</option>
          <option value="Real Estate">Real Estate</option>
        </select>
      </div>

      <div class="form-group mb-3">
        <label class="fw-bold h5" for="businessDescription">New Description</label>
        <textarea id="businessDescription" name="description" class="form-control" placeholder="Leave blank to keep current"></textarea>
      </div>

      <div class="form-group mb-3">
        <label class="fw-bold h5" for="businessImage">New Business Image (optional)</label>
        <input type="file" id="businessImage" name="business_img" class="form-control" accept="image/*">
      </div>

      <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary px-4 py-2">Update Business</button>
      </div>
    </form>

    <div id="messageBox" class="mt-4"></div>
  </main>

  <script src="js/admin-api.js"></script>
  <script src="js/admin-update.js"></script>
</body>
</html>