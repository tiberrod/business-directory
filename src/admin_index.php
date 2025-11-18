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
          <a href="status-management.php" class="btn btn-outline-light">
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

    <!-- Category Cards -->
    <section class="category-section py-5">
      <div class="container">
        <div class="row g-4">
          <div class="col-6 col-md-4 col-lg-2">
            <div class="category-card text-center p-4 bg-white shadow-sm rounded">
              <div class="category-icon mb-3">
                <i class="bi bi-shop fs-1 text-danger"></i>
              </div>
              <h6 class="mb-0">Restaurant</h6>
            </div>
          </div>
          <div class="col-6 col-md-4 col-lg-2">
            <div class="category-card text-center p-4 bg-white shadow-sm rounded">
              <div class="category-icon mb-3">
                <i class="bi bi-cart fs-1 text-danger"></i>
              </div>
              <h6 class="mb-0">Shopping</h6>
            </div>
          </div>
          <!-- Repeat for other categories -->
        </div>
      </div>
    </section>

    <!-- Listings Section -->
    <!-- Will probably add section for analytics and summaries here (dashboard style) --> 
    <section class="listings-section py-5 bg-light">
      <div class="container">
        <div class="text-center mb-5">
          <p class="text-danger text-uppercase mb-2">Our Latest Listings</p>
          <h2 class="display-5 fw-bold">New Listings in Our Directory</h2>
          <p class="text-muted">Lorem ipsum is simply dummy text of the printing and typesetting industry.</p>
        </div>

        <div class="row g-4">
          <!-- Listing Card 1 -->
          <div class="col-md-6 col-lg-3">
            <div class="listing-card card h-100 border-0 shadow-sm">
              <div class="position-relative">
                <img src="image1.jpg" class="card-img-top" alt="Business">
                <span class="badge bg-success position-absolute top-0 start-0 m-3">Cemetery</span>
                <button class="btn btn-light btn-sm position-absolute top-0 end-0 m-3 rounded-circle">
                  <i class="bi bi-heart"></i>
                </button>
              </div>
              <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                  <img src="avatar.jpg" class="rounded-circle me-2" width="30" height="30">
                  <small class="text-muted">Allina Power</small>
                </div>
                <h5 class="card-title">Dr. Frances Sutton</h5>
                <p class="card-text small text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                
                <div class="d-flex align-items-center text-muted small mb-2">
                  <i class="bi bi-geo-alt me-1"></i>
                  <span>1845, GHA, USA (Contabilia)</span>
                </div>
                <div class="d-flex align-items-center text-muted small mb-3">
                  <i class="bi bi-clock me-1"></i>
                  <span>Sunday - Friday: 9am - 5pm</span>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                  <h4 class="text-danger mb-0">$50</h4>
                  <button class="btn btn-outline-danger btn-sm">Appointment</button>
                </div>
              </div>
              
              <!-- Hover Action Buttons -->
              <div class="card-hover-actions">
                <button class="btn btn-light btn-sm rounded-circle"><i class="bi bi-share"></i></button>
                <button class="btn btn-light btn-sm rounded-circle"><i class="bi bi-bookmark"></i></button>
                <button class="btn btn-light btn-sm rounded-circle"><i class="bi bi-eye"></i></button>
              </div>
            </div>
          </div>

          <!-- Repeat for other listings -->
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features-section py-5">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="feature-images position-relative">
              <img src="main-image.jpg" class="img-fluid rounded shadow-lg" alt="Feature">
              <div class="position-absolute" style="bottom: -20px; left: -20px;">
                <img src="small-image1.jpg" class="img-fluid rounded shadow" width="150" alt="Small">
              </div>
              <div class="position-absolute" style="top: 50%; right: -20px;">
                <img src="small-image2.jpg" class="img-fluid rounded shadow" width="120" alt="Small">
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
