<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apploqic Business Directory - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css"/>
</head>
<body>
    <h1>Apploqic Business Directory</h1>

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
        </div>
        <button id="deleteBtn" style="background:red;color:white;">Delete</button>
    </div>

    <section id="deleteBusinessSection">
    <h2>Delete Business</h2>
    <form id="deleteBusinessForm">
        <label for="deleteId">Business ID:</label>
        <input type="number" id="deleteId" name="deleteId" placeholder="Enter Business ID" required>

        <button type="submit">Delete</button>
    </form>

    <p id="deleteMessage"></p>
    </section>

    <script src="main.js"></script>
</body>
</html>