<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apploqic Business Directory - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css"/>
</head>
<body>
    <!-- ===== Navbar ===== -->
    <nav class="navbar">
    <div class="navbar-container">
        <h1 class="navbar-logo">Business Directory Admin</h1>
        <ul class="navbar-links">
        <li><button class="nav-btn" id="createBusinessBtn">Create Business</button></li>
        <li><button class="nav-btn" id="updateBusinessBtn">Update Business</button></li>
        </ul>
    </div>
    </nav>

    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search business by name...">
        <button id="searchBtn">Search</button>
    </div>

    <div id="businessContainer" class="grid-container"></div>

    <div id="paginationContainer" class="pagination"></div>

    <!-- Modal for business details -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>

            <h2 id="modalTitle"></h2>
            <p><strong>ID:</strong> <span id="modalId"></span></p>
            <img id="modalImage" alt="Business Image">
            <p><strong>Contact:</strong> <span id="modalContact"></span></p>
            <p><strong>Description:</strong> <span id="modalDescription"></span></p>
            <p><strong>Created at:</strong> <span id="modalCreated"></span></p>
            <p><strong>Updated at:</strong> <span id="modalUpdated"></span></p>

            <!-- Buttons Row -->
            <div class="modal-actions">
            <button id="deleteBtn" class="delete-btn">Delete Business</button>
            <button id="closeBtn" class="close-btn">Close</button>
            </div>
        </div>
    </div>
    <script src="main.js"></script>
</body>
</html>