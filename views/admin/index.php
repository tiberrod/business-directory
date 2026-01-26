<?php
// Require admin authentication
require_once 'AdminAuth.php';
AdminAuth::requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Apploqic Business Directory - Admin</title>
  <link rel="icon" type="image/webp" href="../../../public/assets/apploqic-logo.webp">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  
  <style>
  /* ==============================
     ADMIN DASHBOARD ENHANCEMENTS
     ============================== */

  .admin-header {
    background: #183A8A;
    color: #ffffff;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .admin-header h4 {
    margin: 0;
    font-weight: 600;
  }

  .admin-header .btn {
    border-radius: 30px;
    font-weight: 500;
    transition: all 0.2s ease;
  }

  .admin-header .btn:hover {
    transform: translateY(-1px);
  }

  /* Card hover animation */
  .card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .card-img-top {
    height: 150px; /* Set a fixed height */
    object-fit: cover; /* Maintain aspect ratio */
  }

  .business-card {
    margin-bottom: 1.5rem; /* Space between cards */
  }

  .card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
  }

  /* Filter section */
  .filters select {
    border-radius: 8px;
    transition: all 0.2s ease;
    margin-left: 0.5rem;
  }

  .filters select:focus {
    box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
    border-color: #007bff;
  }

  /* Search input */
  .input-group input {
    border-radius: 8px;
    transition: box-shadow 0.2s ease;
  }

  .input-group input:focus {
    box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
  }

  /* Pagination styling */
  .pagination .page-link {
    border-radius: 6px;
    color: #007bff;
    transition: background 0.2s, color 0.2s;
  }

  .pagination .page-item.active .page-link {
    background-color: #007bff;
    color: #fff;
    border-color: #007bff;
  }

  .pagination .page-link:hover {
    background-color: #0056b3;
    color: #fff;
  }

  /* Toast-like alert popup */
  .alert-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #007bff;
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    opacity: 0;
    transform: translateY(-20px);
    transition: opacity 0.3s ease, transform 0.3s ease;
    z-index: 1050;
  }

  .alert-toast.show {
    opacity: 1;
    transform: translateY(0);
  }

  /* Form improvements */
  .inner-container {
    background: #fff;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  }

  /* Status styling */
  .status-pill {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
  }

  .status-active {
    background-color: #d4edda;
    color: #155724;
  }

  .status-inactive {
    background-color: #f8d7da;
    color: #721c24;
  }

  /* Details container */
  .details-container {
    border-radius: 12px;
    overflow: hidden;
  }

  .meta p {
    margin-bottom: 0.5rem;
  }

  .business-description {
    background-color: #f8f9fa;
    border-radius: 8px;
  }

  /* Coming Soon Button Styling */
  .coming-soon-btn {
    position: relative;
    overflow: hidden;
    cursor: not-allowed !important;
    opacity: 0.7;
    transition: all 0.3s ease;
  }

  .coming-soon-btn:hover {
    opacity: 0.8;
    transform: translateY(-1px);
  }

  .coming-soon-btn:disabled {
    border-color: rgba(255, 255, 255, 0.5) !important;
    color: rgba(255, 255, 255, 0.8) !important;
  }

  .coming-soon-badge {
    position: absolute;
    top: -8px;
    right: 8px;
    background: linear-gradient(45deg, #ff6b6b, #ffa726);
    color: white;
    font-size: 0.7rem;
    padding: 2px 6px;
    border-radius: 8px;
    font-weight: 600;
    animation: pulse-glow 2s ease-in-out infinite;
    box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
    white-space: nowrap;
    z-index: 10;
  }

  @keyframes pulse-glow {
    0%, 100% {
      transform: scale(1);
      box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
    }
    50% {
      transform: scale(1.05);
      box-shadow: 0 4px 16px rgba(255, 107, 107, 0.5);
    }
  }

  /* Shimmer effect for coming soon button */
  .coming-soon-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
      90deg,
      transparent,
      rgba(255, 255, 255, 0.1),
      transparent
    );
    transition: left 0.8s;
  }

  .coming-soon-btn:hover::before {
    left: 100%;
  }
  </style>
</head>
<body>

  <!-- ===== Admin Header ===== -->
  <header class="admin-header">
    <div class="d-flex align-items-center">
      <h4 class="mb-0">Apploqic Business Directory — Admin</h4>
      <span class="ms-3 opacity-75">Welcome, <?php echo htmlspecialchars(AdminAuth::getUsername()); ?></span>
    </div>
    <div class="d-flex gap-2 align-items-center">
      <a href="create-business.php" class="btn btn-light px-4">Create Business</a>
      <button class="btn btn-outline-light coming-soon-btn" disabled>
        <i class="fas fa-cog me-1"></i>
        Status Management
        <span class="coming-soon-badge">Coming Soon</span>
      </button>
      <div class="vr text-white mx-2" style="opacity: 0.3;"></div>
      <a href="logout.php" class="btn btn-outline-light" onclick="return confirm('Are you sure you want to logout?')">
        <i class="fas fa-sign-out-alt me-1"></i>
        Logout
      </a>
    </div>
  </header>

  <main class="container my-5">
    <!-- Search + Filters -->
    <div class="card shadow-sm mb-4 p-3">
      <div class="row g-3 align-items-center">
        <div class="col-md-5">
          <div class="input-group">
            <input id="searchInput" type="search" class="form-control" placeholder="Search by name...">
            <button id="searchBtn" class="btn btn-primary">Search</button>
          </div>
        </div>
        <div class="col-md-7 d-flex justify-content-end filters">
          <select id="categoryFilter" class="form-select w-auto">
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

          <select id="featureFilter" class="form-select w-auto">
            <option value="">All</option>
            <option value="1">Featured</option>
            <option value="0">Not Featured</option>
          </select>

          <select id="statusFilter" class="form-select w-auto">
            <option value="">All</option>
            <option value="active">Active only</option>
            <option value="all">Include inactive</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Business Grid -->
    <section>
      <div id="businessContainer" class="row g-4"></div>
      <nav id="paginationWrapper" aria-label="Business pagination" class="mt-4">
        <ul id="paginationContainer" class="pagination justify-content-center"></ul>
      </nav>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/admin-api.js"></script>
  <script src="js/admin.js"></script>
  
  <script>
    // Coming Soon Button Interactive Effects
    document.addEventListener('DOMContentLoaded', function() {
      const comingSoonBtn = document.querySelector('.coming-soon-btn');
      
      if (comingSoonBtn) {
        // Add tooltip
        const tooltip = new bootstrap.Tooltip(comingSoonBtn, {
          title: 'This feature is under development and will be available soon!',
          placement: 'bottom'
        });
        
        // Add click effect
        comingSoonBtn.addEventListener('click', function(e) {
          e.preventDefault();
          
          // Create ripple effect
          const ripple = document.createElement('span');
          ripple.style.position = 'absolute';
          ripple.style.borderRadius = '50%';
          ripple.style.background = 'rgba(255, 255, 255, 0.3)';
          ripple.style.transform = 'scale(0)';
          ripple.style.animation = 'ripple 0.6s linear';
          ripple.style.left = (e.offsetX - 10) + 'px';
          ripple.style.top = (e.offsetY - 10) + 'px';
          ripple.style.width = '20px';
          ripple.style.height = '20px';
          
          this.appendChild(ripple);
          
          // Show toast notification
          showComingSoonToast();
          
          // Remove ripple after animation
          setTimeout(() => {
            ripple.remove();
          }, 600);
        });
      }
      
      function showComingSoonToast() {
        // Remove existing toast if any
        const existingToast = document.querySelector('.coming-soon-toast');
        if (existingToast) {
          existingToast.remove();
        }
        
        // Create toast
        const toast = document.createElement('div');
        toast.className = 'alert-toast coming-soon-toast';
        toast.innerHTML = `
          <i class="fas fa-clock me-2"></i>
          <strong>Coming Soon!</strong> Status Management feature is under development.
        `;
        
        document.body.appendChild(toast);
        
        // Show toast
        setTimeout(() => {
          toast.classList.add('show');
        }, 100);
        
        // Hide toast after 3 seconds
        setTimeout(() => {
          toast.classList.remove('show');
          setTimeout(() => toast.remove(), 300);
        }, 3000);
      }
    });
    
    // Add ripple animation CSS
    const style = document.createElement('style');
    style.textContent = `
      @keyframes ripple {
        to {
          transform: scale(4);
          opacity: 0;
        }
      }
      
      .coming-soon-toast {
        background: linear-gradient(45deg, #ff6b6b, #ffa726) !important;
      }
    `;
    document.head.appendChild(style);
  </script>
</body>
</html>