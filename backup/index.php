<?php 
require 'data-controller.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Local Business Directory</title>

  <!-- ✅ Bootstrap CSS -->
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
    rel="stylesheet"
  >

  <style>
    /* === Base Page === */
    body {
      font-family: Arial, Helvetica, sans-serif;
      margin: 0;
      background-color: #E0F2FE;
    }

    /* === Hero Section === */
    .hero {
      min-height: 40vh;
      padding-top: 7rem;
      position: relative;
      background: 
        linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
        url('images/bg2.jpg') center center / cover no-repeat;
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

    /* === Cards === */
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
      height: 200px;
      border-top-left-radius: 1rem;
      border-top-right-radius: 1rem;
      object-fit: fill;
    }

    /* === Navbar === */
    .navbar {
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

    /* === Glass Modal === */
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

    /* === Glass Button === */
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
  </style>
</head>

<body>

  <!-- === NAVBAR === -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top transparent-navbar">
    <div class="container">
      <!-- Logo -->
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img 
          src="images/APPLOQIC-LOGO-HORIZONTAL---WHITE.png" 
          alt="Apploqic Logo" 
          width="197" 
          height="67" 
          class="me-2"
        >
      </a>

      <!-- Toggler for Mobile -->
      <button 
        class="navbar-toggler" 
        type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#mainNav"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navbar Links -->
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

  <!-- === HERO SECTION === -->
  <section class="hero">
    <div class="container">
      <h1>Local Business Directory</h1>
      <p>Discover the best businesses in your community.</p>
    </div>
  </section>

  <!-- === MAIN CONTENT === -->
  <div class="container bg-white border border-secondary-subtle rounded-4 shadow-sm p-4 p-md-5 my-5">
    <h2 class="mb-4">Featured Businesses</h2>

    <!-- Search Bar -->
    <div class="row justify-content-center mb-4">
      <div class="col-md-8 col-lg-6">
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
          >
          <button id="searchBtn" class="btn btn-primary">
            Search
          </button>
        </div>
      </div>
    </div>

    <!-- No Results -->
    <div id="noResults" class="text-center text-muted d-none mb-4">
      No businesses match your search.
    </div>

    <!-- Business Cards -->
    <div class="row g-4">
      <?php if (!empty($businesses)): ?>
        <?php foreach ($businesses as $biz): ?>
          <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm border border-secondary-subtle rounded-4">
              <img 
                src="<?= htmlspecialchars($biz['image']) ?>" 
                alt="<?= htmlspecialchars($biz['name']) ?>" 
                class="business-img"
              >
              <div class="card-body text-center">
                <h5 class="card-title text-primary">
                  <?= htmlspecialchars($biz['name']) ?>
                </h5>
                <a 
                  href="business-details.php?id=<?= urlencode($biz['id']) ?>" 
                  class="btn btn-primary btn-sm mt-2"
                >
                  View Details
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-center">No businesses found.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- === CONTACT MODAL === -->
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

  <!-- === FOOTER === -->
  <footer class="text-center text-white py-4 mt-5" style="background: #0b2e6b;">
    &copy; <?= date('Y') ?> Apploqic Business Directory. All rights reserved.
  </footer>

  <!-- ✅ Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- ✅ Search Filter Script -->
  <script>
    // Navbar scroll background
    window.addEventListener('scroll', function() {
      const navbar = document.querySelector('.navbar');
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });

    // Search Filter
    (function() {
      const input = document.getElementById('searchInput');
      const cards = document.querySelectorAll('.card');
      const noResults = document.getElementById('noResults');

      function normalize(text) {
        return (text || '').toLowerCase().trim();
      }

      function filterList() {
        const q = normalize(input.value);
        let visibleCount = 0;

        cards.forEach(card => {
          const text = normalize(card.innerText);
          if (text.includes(q)) {
            card.parentElement.style.display = '';
            visibleCount++;
          } else {
            card.parentElement.style.display = 'none';
          }
        });

        noResults.classList.toggle('d-none', visibleCount !== 0);
      }

      input.addEventListener('input', filterList);

      // Quick search shortcut: press "/" to focus
      document.addEventListener('keydown', (e) => {
        if (e.key === '/' && document.activeElement !== input) {
          e.preventDefault();
          input.focus();
          input.select();
        }
      });
    })();
  </script>

</body>
</html>
