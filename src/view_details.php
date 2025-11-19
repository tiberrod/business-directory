<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Business Details</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/view_details.css"/>
</head>

<body>
  <!-- Header -->
  <header class="page-header d-flex">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <div class="header-details d-flex justify-content-between align-items-center">
        <img src="../images/APPLOQIC-LOGO-HORIZONTAL---WHITE.png" alt="Apploqic Logo" height="70" class="me-4 mb-0">
        <h4 class="text-light ms-3 mb-0">Business Details</h4>
      </div>
      <div class="back-button-section">
        <a href="../src/admin_index.php" class="btn-back">
          <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <div class="container my-5">
    <div class="profile-container">
      <!-- Profile Card -->
      <div class="profile-card">
        <div class="profile-header">
          <div class="profile-image-wrapper">
            <img id="businessImage" src="https://placehold.co/100x100?text=Loading..." alt="Business" class="profile-image" />
          </div>
          <p class="business-id-label fw-bold">Business ID: 
            <span id="businessId" class="profile-id fw-light">Loading...</span>
          </p>
          <h2 id="businessName" class="profile-name">Loading...</h2>
          <p id="businessCategory" class="profile-category">Category</p>
          <span id="businessStatus" class="status-badge">Loading...</span>
        </div>
      </div>
      <!-- End Profile Card -->

      <!-- Details Card -->
      <div class="details-card d-flex flex-column">
        <div class="detail-card-header justify-content-between d-flex">
          <h5 class="card-title d-flex pt-1">Bio & Details</h5>
          <!-- Action Buttons -->
          <div class="action-buttons">
            <a href="#" id="editBtn" class="btn-action btn-edit">
              <i class="bi bi-pencil-fill"></i> Edit 
            </a>
            <a href="#" id="deleteBtn" class="btn-action btn-delete">
                <i class="bi bi-trash-fill"></i> Delete
            </a>
          </div>
        </div>
        
        <div class="detail-grid">
          <div class="detail-item">
            <label class="details-label fw-bold">Contact Number</label>
            <p id="businessContact">-</p>
          </div>

          <div class="detail-item">
            <label class="details-label fw-bold">Featured Status</label>
            <p id="businessFeatured">-</p>
          </div>

          <div class="detail-item">
            <label class="details-label fw-bold">Member Since</label>
            <p id="businessCreated">-</p>
          </div>

          <div class="detail-item">
            <label class="details-label fw-bold">Last Updated</label>
            <p id="businessUpdated">-</p>
          </div>
        </div>

        <div class="description-section">
          <h6 class="section-title">About Business</h6>
          <p id="businessDescription" class="description-text">Loading business details...</p>
        </div>
      </div>
      <!-- End Details Card -->
    </div>
  </div>

  <!-- External JavaScript -->
  <script src="../src/view_details.js"></script>
</body>
</html>