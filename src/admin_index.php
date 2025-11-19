<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Apploqic Business Directory - Admin</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/admin_index.css">
  <link rel="stylesheet" href="../css/analytics.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

  <!-- ===== Admin Header ===== -->
  <header class="admin-header">
    <div class="container-fluid">
      <div class="d-flex justify-content-between align-items-center">
        <!-- Logo/Brand -->
        <div class="d-flex align-items-center">
          <img src="../images/APPLOQIC-LOGO-HORIZONTAL---WHITE.png" alt="Apploqic Logo" height="70" class="me-3 mb-0">
          <h4 class="mb-1 ms-4">Welcome, Admin</h4>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex gap-2">
          <a href="../src/create_business.php" class="btn btn-outline-light px-4">
            <i class="bi bi-plus-circle me-2"></i>Create Business
          </a>
          <a href="status-management.php" class="btn btn-outline-light disabled" tabindex="-1" aria-disabled="true">
            <i class="bi bi-gear me-2"></i>Status Management
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Hero Section (big top part or something) -->
  <section class="hero-section" style="background-image: url('../images/cyberpunk_city_3.jpg');">
    <div class="hero-overlay"></div>
    <div class="container">
      <div class="hero-content text-center text-white">
        <p class="hero-subtitle mb-3">Stop hunting through social media. Get the full picture of Sabah's local businesses—accurate info, updated menus, and real-time offers, all in one place. </p>
        <h1 class="hero-title display-3 fw-bold mb-5">Apploqic Business Directory</h1>
        
        <!-- Search Bar -->
        <div class="search-wrapper mx-auto py-3 px-4">
          <div class="d-flex gap-2 align-items-center flex-wrap">
            <input id="heroSearchInput" type="text" class="form-control flex-fill" placeholder="What are you looking for?" style="min-width: 200px;">
            <select id="heroCategoryFilter" class="form-select" style="min-width: 180px; max-width: 200px;">
              <option value="">Select Category</option>
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
            
            <select id="heroFeatureFilter" class="form-select" style="min-width: 150px; max-width: 180px;">
              <option value="">All</option>
              <option value="1">Featured</option>
              <option value="0">Not Featured</option>
            </select>
            
            <select id="heroStatusFilter" class="form-select" style="min-width: 150px; max-width: 180px;">
              <option value="">All</option>
              <option value="active">Active Only</option>
              <option value="all">Include inactive</option>
            </select>
            
            <button id="heroSearchBtn" class="btn btn-primary">
              <i class="bi bi-search"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <main class="container my-5">
    <!-- Business Grid -->
    <section>
      <!-- Quick filter tags (optional) -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">List of existing businesses: </h3>
      </div>
      <div id="businessContainer" class="row g-4"></div>
        <!-- List of businesses will be injected here. -->
      <nav id="paginationWrapper" aria-label="Business pagination" class="mt-4">
        <ul id="paginationContainer" class="pagination justify-content-center"></ul>
      </nav>
    </section>

    <!-- Will probably add section for analytics and summaries here (dashboard style) --> 
    <section class="analytics-section py-5">
      <div class="container">
        <h3 class="section-title mb-4">Summaries of the Directory</h3>
        <div id="analytics-dashboard">
          <div class="card">
            <h3>Total Businesses</h3>
            <p id="total_businesses">0</p>
          </div>
          <div class="card">
            <h3>Active Businesses</h3>
            <p id="active_businesses">0</p>
          </div>
          <div class="card">
            <h3>Inactive Businesses</h3>
            <p id="inactive_businesses">0</p>
          </div>
          <div class="card">
            <h3>Featured Businesses</h3>
            <p id="featured_businesses">0</p>
          </div>
          <div class="card">
            <h3>Activation Rate</h3>
            <p id="activation_rate">0%</p>
          </div>
          <div class="card">
            <h3>Featured Rate</h3>
            <p id="featured_rate">0%</p>
          </div>
        </div>
      </div>
    </section>


    <!-- Features Section -->
    <section class="features-section py-5">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="feature-images position-relative">
              <div class="position-absolute" style="bottom: -20px; left: -20px;">
              </div>
              <div class="position-absolute" style="top: 50%; right: -20px;">
              </div>
            </div>
          </div>
          
          <div class="col-lg-6">
            <p class="text-danger text-uppercase mb-2">What We Do</p>
            <h2 class="display-5 fw-bold mb-4">Let's Discover The Best Places in the town</h2>
            
            <div class="feature-item d-flex mb-4">
              <div class="feature-icon me-3">
                <i class="bi bi-check-circle-fill text-danger fs-4"></i>
              </div>
              <div>
                <h5>Stay connected and organized</h5>
                <p class="text-muted">Etiam consectetur augue massa sed commodo vulputate vitae lectus ornare.</p>
              </div>
            </div>

            <div class="feature-item d-flex mb-4">
              <div class="feature-icon me-3">
                <i class="bi bi-people-fill text-danger fs-4"></i>
              </div>
              <div>
                <h5>Strong Community</h5>
                <p class="text-muted">Etiam consectetur augue massa sed commodo vulputate vitae lectus ornare.</p>
              </div>
            </div>

            <div class="feature-item d-flex">
              <div class="feature-icon me-3">
                <i class="bi bi-bookmark-check-fill text-danger fs-4"></i>
              </div>
              <div>
                <h5>Authorised Listings</h5>
                <p class="text-muted">Etiam consectetur augue massa sed commodo vulputate vitae lectus ornare.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../src/main.js"></script>
  <script src="../src/analytics.js"></script>

  <!-- Sticky Header Script -->
  <script>
    // Transparent header that becomes solid on scroll
    window.addEventListener('scroll', function() {
      const header = document.querySelector('.admin-header');
      const heroSection = document.querySelector('.hero-section');
      const heroHeight = heroSection ? heroSection.offsetHeight : 600;
      
      if (window.scrollY > heroHeight - 100) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    });
  </script>
</body>
</html>
