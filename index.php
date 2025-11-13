<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Business Directory</title>

  <!-- Bootstrap CSS -->
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
    rel="stylesheet" 
  />

  <!-- Bootstrap Icons -->
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" 
    rel="stylesheet" 
  />

  <style>
    /* Base Page */
    body {
      font-family: Arial, Helvetica, sans-serif;
      margin: 0;
      background-color: #E0F2FE;
    }

    /* Hero Section */
    .hero {
      min-height: 40vh;
      padding-top: 7rem;
      position: relative;
      background:
        linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
        url('images/Banner2.png') center center / cover no-repeat;
      color: white;
      text-align: center;
      padding: 5rem 1rem;
      border-bottom-left-radius: 2rem;
      border-bottom-right-radius: 2rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }

    .hero h1 {
      font-weight: 700;
      font-size: 2.5rem;
      margin: 0;
    }

    .hero p {
      font-size: 1.1rem;
      opacity: 0.9;
      margin-top: 0.5rem;
      margin-bottom: 0;
    }

    .hero .container {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 1rem;
      padding: 2rem;
      display: inline-block;
      margin-top: 5rem;
      box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    }

    /* Business Cards */
    .card {
      border: none;
      border-radius: 1rem;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .business-img {
      width: 100%;
      aspect-ratio: 1 / 1;
      height: 200px;
      border-top-left-radius: 1rem;
      border-top-right-radius: 1rem;
      object-fit: cover;
    }

    /* Navbar */
    .navbar {
      transition: top 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
      top: 0; /* initial position */
      position: fixed; /* already fixed */
      width: 100%;
      z-index: 1030;
      padding-top: 0.05rem;
      padding-bottom: 0.05rem;
    }

    .transparent-navbar {
      background-color: transparent;
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .navbar.scrolled {
      background-color: #1E3A8A;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 400px) {
      .business-img {
        height: 160px;
      }
    }

    /* Glass Modal */
    .glass-modal {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 1rem;
      box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
      opacity: 0;
      transform: translateY(-20px);
      transition: opacity 0.4s ease, transform 0.4s ease;
    }

    .modal.show .glass-modal {
      opacity: 1;
      transform: translateY(0);
    }

    /* Glass Button */
    .btn-glass {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 1rem;
      border: 1px solid rgba(255, 255, 255, 0.3);
      color: #fff;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    .btn-glass:hover {
      background: rgba(255, 255, 255, 0.2);
      color: #1E3A8A;
      border-color: rgba(255, 255, 255, 0.5);
    }

    /* Highlighted Scroll Buttons */
    .scroll-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(255, 255, 255, 0.8);
      border: none;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
      transition: background 0.3s ease;
      z-index: 10;
    }

    .scroll-btn:hover {
      background: rgba(255, 255, 255, 1);
    }

    .scroll-btn.left {
      left: -15px;
    }

    .scroll-btn.right {
      right: -15px;
    }

    /* Scroll Container */
    #highlightedContainer::-webkit-scrollbar {
      height: 8px;
    }

    #highlightedContainer::-webkit-scrollbar-thumb {
      background: #ccc;
      border-radius: 4px;
    }

    #highlightedContainer::-webkit-scrollbar-thumb:hover {
      background: #999;
    }

  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top transparent-navbar">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img 
          src="images/APPLOQIC-LOGO-HORIZONTAL---WHITE.png" 
          alt="Apploqic Logo" 
          width="197" 
          height="67" 
          class="me-2"
        />
      </a>

      <button 
        class="navbar-toggler" 
        type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#mainNav"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="mainNav">
        <button 
          type="button" 
          class="btn btn-glass fw-bold" 
          data-bs-toggle="modal" 
          data-bs-target="#contactModal"
        >
          Contact
        </button>
      </div>
    </div>
  </nav>

  <!-- HERO SECTION -->
  <section class="hero">
    <div class="container">
      <h1>Apploqic Business Directory</h1>
      <p>Discover the best businesses in Sabah.</p>
    </div>
  </section>

  <!-- CONTENT SECTION -->
  <div class="container py-4">
    <div class="container bg-white border border-secondary-subtle rounded-4 shadow-sm p-4 p-md-5 my-5">

      <!-- Lists Title -->
      <h2 class="mb-4">Lists of Businesses</h2>

      <!-- Category Filter + Search Bar -->
      <div class="row justify-content-center mb-4">
        <div class="col-md-10 col-lg-8">
          <div class="row g-2">
            <!-- Category Dropdown -->
            <div class="col-md-4">
              <select id="categorySelect" class="form-select shadow-sm">
                <option value="">All Categories</option>
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

            <!-- Search Input -->
            <div class="col-md-8">
              <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0">
                  <i class="bi bi-search text-secondary"></i>
                </span>
                <input 
                  id="searchInput" 
                  type="search" 
                  class="form-control border-start-0" 
                  placeholder="Search businesses..." 
                  aria-label="Search businesses"
                />
                <button id="searchBtn" class="btn btn-primary">Search</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Business Cards -->
      <div id="businessList" class="row"></div>

      <!-- Pagination -->
      <nav>
        <ul id="pagination" class="pagination justify-content-center mt-4"></ul>
      </nav>
    </div>
  </div>

  <!-- CONTACT MODAL -->
  <div 
    class="modal fade" 
    id="contactModal" 
    tabindex="-1" 
    aria-labelledby="contactModalLabel" 
    aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content glass-modal p-4">
        <div class="modal-header border-0">
          <h5 class="modal-title text-white" id="contactModalLabel">
            Contact Us
          </h5>
          <button 
            type="button" 
            class="btn-close btn-close-white" 
            data-bs-dismiss="modal" 
            aria-label="Close"
          ></button>
        </div>

        <div class="modal-body text-white">
          <p><strong>Phone:</strong> +60 12-345 6789</p>
          <p><strong>Email:</strong> info@apploqic.my</p>
          <p><strong>Address:</strong> 123 Business Street, Kuala Lumpur, Malaysia</p>
          <p>Feel free to reach out for inquiries or collaborations!</p>
        </div>

        <div class="modal-footer border-0">
          <button 
            type="button" 
            class="btn btn-light fw-bold text-primary" 
            data-bs-dismiss="modal"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <footer class="text-center text-white py-4 mt-5" style="background: #0b2e6b;">
    &copy; <?= date('Y') ?> Apploqic Business Directory. All rights reserved.
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="scriptv1.js"></script>
</body>
</html>
