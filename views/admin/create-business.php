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
  <title>Create New Business</title>
  <link rel="icon" type="image/webp" href="../../../public/assets/apploqic-logo.webp">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="css/admin.css">
</head>

<body>
  <!-- Navbar / Admin Header -->
  <header class="admin-header">
    <div class="d-flex align-items-center">
      <h4 class="mb-0">Add New Business</h4>
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
    <form id="createBusinessForm" class="inner-container" enctype="multipart/form-data">
      <h3 class="text-center mb-4 fw-bold text-primary">Create New Business</h3>

      <div class="form-row">
        <div class="form-group mb-3">
          <label class="fw-bold h5" for="business_name">Business Name</label>
          <input type="text" id="business_name" name="business_name" class="form-control" required>
        </div>

        <div class="form-group mb-3">
          <label class="fw-bold h5" for="business_contact">Contact</label>
          <input type="text" id="business_contact" name="business_contact" class="form-control" required>
        </div>
      </div>

      <div class="form-group mb-3">
        <label class="fw-bold h5" for="business_description">Description</label>
        <textarea id="business_description" name="business_description" class="form-control" required></textarea>
      </div>

      <div class="form-row">
        <div class="form-group mb-3">
          <label class="fw-bold h5" for="business_category">Category</label>
          <select id="business_category" name="business_category" class="form-select" required>
            <option value="">Select Category</option>
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
          <label class="fw-bold h5" for="is_featured">Featured</label>
          <select id="is_featured" name="is_featured" class="form-select">
            <option value="0">Not Featured</option>
            <option value="1">Featured</option>
          </select>
        </div>
      </div>

      <div class="form-group mb-3">
        <label class="fw-bold h5" for="business_img">Business Image</label>
        <input type="file" id="business_img" name="business_img" class="form-control" accept="image/*">
        <img id="imgPreview" class="img-preview mt-2 py-2" src="#" alt="Preview" style="display:none;">
      </div>

      <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary px-4 py-2">Submit</button>
      </div>
    </form>
  </main>

  <script src="js/admin-api.js"></script>
  <script src="js/admin-create.js"></script>
</body>
</html>