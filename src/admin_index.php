<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Apploqic Business Directory - Admin</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

  <!-- ===== Admin Header ===== -->
  <header class="admin-header">
    <h4 class="mb-0">Apploqic Business Directory — Admin</h4>
    <div class="d-flex gap-2">
      <a href="create-business.php" class="btn btn-light">+ Create Business</a>
      <a href="status-management.php" class="btn btn-outline-light">Status Management</a>
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
  <script src="../src/main.js"></script>
</body>
</html>
