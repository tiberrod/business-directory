<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Apploqic Business Directory - Create New Business</title>
  <!--CSS styling -->
  <link rel="stylesheet" href="../css/style.css">
  <!--Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!--jQuery CDN -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>
  <div class="container mt-5">
    <h1 class="mb-4 text-center">Create New Business</h1>

    <form id="createBusinessForm" enctype="multipart/form-data" class="p-4 rounded shadow-sm bg-white">
      <!-- Business Name -->
      <div class="mb-3">
        <label for="businessName" class="form-label fw-semibold">Business Name <span class="text-danger">*</span></label>
        <input type="text" id="businessName" name="name" class="form-control" required>
      </div>

      <!-- Contact -->
      <div class="mb-3">
        <label for="businessContact" class="form-label fw-semibold">Business Contact <span class="text-danger">*</span></label>
        <input type="text" id="businessContact" name="contact" class="form-control" placeholder="Phone, email, or address" required>
      </div>

      <!-- Description -->
      <div class="mb-3">
        <label for="businessDescription" class="form-label fw-semibold">Description</label>
        <textarea id="businessDescription" name="description" class="form-control" rows="3" placeholder="Optional short description..."></textarea>
      </div>

      <!-- Category -->
      <div class="mb-3">
        <label for="businessCategory" class="form-label fw-semibold">Category</label>
        <select id="businessCategory" name="category" class="form-select">
          <option value="">Select a category</option>
          <option value="Restaurant">Restaurant</option>
          <option value="Retail">Retail</option>
          <option value="IT Service">IT Service</option>
          <option value="Healthcare">Healthcare</option>
          <option value="Education">Education</option>
        </select>
      </div>

      <!-- Status -->
      <div class="mb-3">
        <label for="businessStatus" class="form-label fw-semibold">Status</label>
        <select id="businessStatus" name="status" class="form-select">
          <option value="1" selected>Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>

      <!-- Featured -->
      <div class="mb-3">
        <label for="businessFeatured" class="form-label fw-semibold">Featured</label>
        <select id="businessFeatured" name="is_featured" class="form-select">
          <option value="0" selected>Not Featured</option>
          <option value="1">Featured</option>
        </select>
      </div>

      <!-- Image -->
      <div class="mb-3">
        <label for="businessImage" class="form-label fw-semibold">Business Image</label>
        <input type="file" id="businessImage" name="business_img" class="form-control" accept="image/*">
      </div>

      <!-- Submit -->
      <div class="d-grid">
        <button type="submit" class="btn btn-primary btn-lg">Create Business</button>
      </div>
    </form>

    <div id="messageBox" class="mt-4"></div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="create-business.js"></script>
  
</body>
</html>
