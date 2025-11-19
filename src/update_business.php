<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Update Existing Business</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/update_business.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <div class="page-container">
    <!-- Background gradient -->
    <div class="background-gradient"></div>

    <!-- Main content wrapper -->
    <div class="content-wrapper">
      <!-- Left Side - Image Upload & Description -->
      <div class="hero-card">
        <div class="hero-header">
          <h3 class="hero-title">Update Existing Business</h3>
          <p class="hero-hint">To keep an existing detail, just leave the form empty for that detail</p>
        </div>

        <div class="hero-form">
          <!-- Image Upload Section -->
          <div class="image-upload-section align-items-center py-2 mb-5">
            <label class="custom-file-upload">
              <input type="file" id="business_img" name="business_img" accept="image/*">
              <div class="upload-placeholder" id="uploadPlaceholder">
                <i class="bi bi-cloud-upload"></i>
                <span>Click to upload new business image</span>
                <small>Recommended: Square image (1:1 ratio)</small>
              </div>
              <img id="imgPreview" class="img-preview" src="#" alt="Preview" style="display:none;">
            </label>
          </div>

          <!-- Description Section -->
          <div class="form-group mt-4">
            <label class="form-label text-light fw-bold">Business Description:</label>
            <textarea id="business_description" name="business_description" class="form-control" placeholder="Enter new description.." rows="7"></textarea>
            <small class="form-text">Provide detailed information about your products, services, and what makes your business unique.</small>
          </div>
        </div>
      </div>

      <!-- Right Side - Business Details Form -->
      <div class="form-card">
        <div class="back-btn-section d-flex align-items-end mb-3">
          <a href="../src/admin_index.php" class="btn-back btn-outline-primary py-3 px-4">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
          </a>
        </div>

        <h2 class="form-title d-flex text-align-start mt-3">Business Details</h2>

        <form id="createBusinessForm" enctype="multipart/form-data">
          <div class="form-group">
            <label class="form-label fw-bold">Business Name</label>
            <input type="text" id="business_name" name="business_name" class="form-control" placeholder="Enter new business name">
          </div>

          <div class="form-group">
            <label class="form-label fw-bold">Contact Information</label>
            <input type="text" id="business_contact" name="business_contact" class="form-control" placeholder="Enter new contact details">
          </div>

          <div class="form-group">
            <label class="form-label fw-bold">Business Category</label>
            <select id="business_category" name="business_category" class="form-control">
              <option value="">-- Select a category --</option>
              <option value="Food & Beverage">Food & Beverage</option>
              <option value="Retail">Retail</option>
              <option value="Automotive">Automotive</option>
              <option value="Health & Wellness">Health & Wellness</option>
              <option value="Professional Services">Professional Services</option>
              <option value="Education">Education</option>
              <option value="Manufacturing & Industrial">Manufacturing & Industrial</option>
              <option value="Pet & Animals">Pet & Animals</option>
              <option value="Others">Others</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label fw-bold">Featured Status</label>
            <select id="is_featured" name="is_featured" class="form-control">
              <option value="0">Regular Business</option>
              <option value="1">Featured Business</option>
            </select>
            <small class="form-text text-muted">Featured businesses appear at the top of search results</small>
          </div>

          <button type="submit" class="btn-submit">
            <i class="bi bi-check-circle me-2"></i> Update Business
          </button>

          <div class="divider">
            <span>or</span>
          </div>

          <a href="admin_index.php" class="btn-secondary">
            Cancel
          </a>
        </form>
      </div>
    </div>
  </div>
  
  <script src="../src/update_business.js"></script>
</body>
</html>
