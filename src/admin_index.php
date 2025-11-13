<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Apploqic Business Directory - Admin</title>

  <!-- project styles -->
  <link rel="stylesheet" href="../css/style.css">

  <!-- bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

  <style>
    /* small local tweaks to keep cards consistent with your CSS */
    .topbar { background: #fff; border-bottom: 1px solid #e6e6e6; padding: .75rem 1rem; }
    .topbar .btn { min-width: 160px; }
    .card .card-img-top { height:160px; object-fit:cover; }
    .filters { gap: .5rem; align-items:center; }
    .no-results { padding: 3rem 0; text-align:center; color:#666; }
  </style>
</head>
<body>
  <!-- topbar -->
  <header class="topbar d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-3">
      <h4 class="mb-0">Business Directory — Admin</h4>
    </div>

    <div class="d-flex gap-2">
      <a href="create-business.php" class="btn btn-primary">Create New Business</a>
      <a href="status-management.php" class="btn btn-outline-secondary">Status Management</a>
    </div>
  </header>

  <main class="container my-4">
    <!-- search + filters -->
    <div class="row mb-3">
      <div class="col-lg-6 col-md-8">
        <div class="input-group">
          <input id="searchInput" type="search" class="form-control" placeholder="Search business by name..." />
          <button id="searchBtn" class="btn btn-primary">Search</button>
        </div>
      </div>

      <div class="col-lg-6 col-md-4 d-flex justify-content-end">
        <div class="d-flex filters">
          <select id="categoryFilter" class="form-select">
            <option value="">All Categories</option>
            <option value="Restaurant">Restaurant</option>
            <option value="Retail">Retail</option>
            <option value="IT Service">IT Service</option>
            <option value="Healthcare">Healthcare</option>
            <option value="Education">Education</option>
          </select>

          <select id="featureFilter" class="form-select">
            <option value="">All</option>
            <option value="1">Featured</option>
            <option value="0">Not Featured</option>
          </select>

          <select id="statusFilter" class="form-select">
            <option value="">All</option>
            <option value="active">Active only</option>
            <option value="all">Include inactive</option>
          </select>
        </div>
      </div>
    </div>

    <!-- grid -->
    <section>
      <div id="businessContainer" class="row g-4"></div>

      <!-- pagination -->
      <nav id="paginationWrapper" aria-label="Business pagination" class="mt-4">
        <ul id="paginationContainer" class="pagination justify-content-center"></ul>
      </nav>
    </section>
  </main>

  <!-- bootstrap + small client script for fetching & rendering -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    (function () {
      const API_BASE = 'https://apploqic.my/index.php/api/v1/search';
      const perPage = 10;

      // elements
      const businessContainer = document.getElementById('businessContainer');
      const paginationContainer = document.getElementById('paginationContainer');
      const searchInput = document.getElementById('searchInput');
      const searchBtn = document.getElementById('searchBtn');
      const categoryFilter = document.getElementById('categoryFilter');
      const featureFilter = document.getElementById('featureFilter');
      const statusFilter = document.getElementById('statusFilter');

      let currentPage = 1;
      let lastResultsCount = 0;

      function buildUrl(q = '', page = 1) {
        const params = new URLSearchParams();
        params.set('q', q || '');
        params.set('page', String(page));
        params.set('per_page', String(perPage));

        if (categoryFilter && categoryFilter.value) params.set('category', categoryFilter.value);
        if (featureFilter && featureFilter.value !== '') params.set('featured_only', featureFilter.value);
        if (statusFilter && statusFilter.value) {
          // API expects status (active) or perhaps 'all' — adjust if needed
          if (statusFilter.value === 'active') params.set('status', 'active');
        }

        return API_BASE + '?' + params.toString();
      }

      async function fetchBusinesses(q = '', page = 1) {
        businessContainer.innerHTML = '<div class="col-12 text-center py-5">Loading...</div>';
        const url = buildUrl(q, page);
        try {
          const res = await fetch(url);
          if (!res.ok) throw new Error('Network response not ok: ' + res.status);
          const json = await res.json();

          // expected response: { status, data: [], results_count, pagination: { current_page, per_page } }
          const items = Array.isArray(json.data) ? json.data : [];
          lastResultsCount = json.results_count || items.length;
          currentPage = (json.pagination && json.pagination.current_page) ? json.pagination.current_page : page;

          renderCards(items);
          renderPagination(lastResultsCount, currentPage);
        } catch (err) {
          console.error(err);
          businessContainer.innerHTML = '<div class="col-12 no-results">Failed to load businesses. See console for details.</div>';
          paginationContainer.innerHTML = '';
        }
      }

      function renderCards(items) {
        businessContainer.innerHTML = '';
        if (!items.length) {
          businessContainer.innerHTML = '<div class="col-12 no-results">No businesses found.</div>';
          return;
        }

        items.forEach(b => {
          const id = b.id || b.business_id || b._id || '';
          const name = b.name || b.business_name || 'Unnamed';
          const category = b.category || b.business_category || '';
          const image = b.image_url || b.business_image || b.image || b.logo || 'https://placehold.co/400x200?text=No+Image';

          const col = document.createElement('div');
          col.className = 'col-12 col-sm-6 col-md-4 col-lg-3';

          col.innerHTML = `
            <div class="card h-100 shadow-sm">
              <img src="${escapeHtml(image)}" class="card-img-top" alt="${escapeHtml(name)}">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title mb-1">${escapeHtml(name)}</h5>
                <p class="card-text text-muted small mb-3">${escapeHtml(category)}</p>
                <div class="mt-auto d-flex justify-content-center gap-2">
                  <a href="view-details.php?id=${encodeURIComponent(id)}" class="btn btn-sm btn-primary">View Details</a>
                  <a href="update-business.php?id=${encodeURIComponent(id)}" class="btn btn-sm btn-outline-secondary">Edit</a>
                </div>
              </div>
            </div>
          `;
          businessContainer.appendChild(col);
        });
      }

      function renderPagination(totalResults, page) {
        paginationContainer.innerHTML = '';
        const totalPages = Math.max(1, Math.ceil(totalResults / perPage));
        const maxButtons = 7; // keep pagination compact

        // prev
        const prevLi = document.createElement('li');
        prevLi.className = 'page-item ' + (page <= 1 ? 'disabled' : '');
        prevLi.innerHTML = `<button class="page-link">Previous</button>`;
        prevLi.onclick = () => { if (page > 1) fetchBusinesses(searchInput.value.trim(), page - 1); };
        paginationContainer.appendChild(prevLi);

        // page numbers (simple range)
        const start = Math.max(1, page - Math.floor(maxButtons / 2));
        let end = Math.min(totalPages, start + maxButtons - 1);
        if (end - start + 1 < maxButtons) {
          // adjust start if needed
          const newStart = Math.max(1, end - maxButtons + 1);
          // no need to reassign if same
        }

        for (let i = Math.max(1, page - 3); i <= Math.min(totalPages, page + 3); i++) {
          const li = document.createElement('li');
          li.className = 'page-item ' + (i === page ? 'active' : '');
          li.innerHTML = `<button class="page-link">${i}</button>`;
          li.onclick = () => fetchBusinesses(searchInput.value.trim(), i);
          paginationContainer.appendChild(li);
        }

        // next
        const nextLi = document.createElement('li');
        nextLi.className = 'page-item ' + (page >= totalPages ? 'disabled' : '');
        nextLi.innerHTML = `<button class="page-link">Next</button>`;
        nextLi.onclick = () => { if (page < totalPages) fetchBusinesses(searchInput.value.trim(), page + 1); };
        paginationContainer.appendChild(nextLi);
      }

      // small helper to avoid XSS when inserting values
      function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
          .replaceAll('&', '&amp;')
          .replaceAll('<', '&lt;')
          .replaceAll('>', '&gt;')
          .replaceAll('"', '&quot;')
          .replaceAll("'", '&#39;');
      }

      // events
      searchBtn.addEventListener('click', () => fetchBusinesses(searchInput.value.trim(), 1));
      searchInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') fetchBusinesses(searchInput.value.trim(), 1); });
      [categoryFilter, featureFilter, statusFilter].forEach(el => { if (el) el.addEventListener('change', () => fetchBusinesses(searchInput.value.trim(), 1)); });

      // initial load
      fetchBusinesses('', 1);
    })();
  </script>
</body>
</html>