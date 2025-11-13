<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apploqic Business Directory - Admin Panel</title>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="../css/style.css">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <!-- ===== Navbar ===== -->
    <nav class="navbar custom-navbar">
    <div class="navbar-container">
        <h1 class="navbar-logo">Business Directory Admin</h1>
        <ul class="navbar-links">
        <li><a href="create-business.php" class="nav-btn" id="createBusinessBtn">Create Business</a></li>
        <li><button class="nav-btn" id="updateBusinessBtn">Update Business</button></li>
        </ul>
    </div>
    </nav>

    <!-- Search Bar & Filters -->
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search business by name...">
        <button id="searchBtn">Search</button>
    </div>

    <select id="categoryFilter">
        <option value="">All Categories</option>
        <option value="Restaurant">Restaurant</option>
        <option value="Retail">Retail</option>
        <option value="IT Service">IT Service</option>
        <option value="Healthcare">Healthcare</option>
        <option value="Education">Education</option>
    </select>

    <select id="featureFilter">
        <option value="">All</option>
        <option value="1">Featured</option>
        <option value="0">Not Featured</option>
    </select>

    <select id="statusFilter">
        <option value="">All</option>
        <option value="1">Active only</option>
        <option value="0">Include inactive</option>
    </select>

    <div class="container">
        <!-- Business Cards Grid -->
        <div id="businessContainer" class="grid-container"></div>

        <!-- Pagination -->
        <div id="paginationContainer" class="pagination"></div>
    </div>

    <!-- Modal for business details -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>

            <h2 id="modalTitle"></h2>
            <p><strong>ID:</strong> <span id="modalId"></span></p>

            <img id="modalImage" src="" alt="Business Image"/>

            <p><strong>Contact:</strong> <span id="modalContact"></span></p>
            <p><strong>Category:</strong> <span id="modalCategory"></span></p>
            <p><strong>Description:</strong> <span id="modalDescription"></span></p>
            <p><strong>Created at:</strong> <span id="modalCreated"></span></p>
            <p><strong>Updated at:</strong> <span id="modalUpdated"></span></p>
            <p><strong>Status:</strong> <span id="modalStatus"></span></p>
            <p><strong>Featured:</strong> <span id="modalFeatured"></span></p>

            <!-- Buttons Row -->
            <div class="modal-actions">
              <!-- Edit link: href will be set by main.js to include the business id -->
              <a id="editBtn" class="edit-link" href="#">Edit Business</a>
              <button id="deleteBtn" class="delete-btn">Delete Business</button>
              <button id="closeBtn" class="close-btn">Close</button>
            </div>
        </div>
    </div>
    <!--JS scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="main.js"></script>
</body>
</html>