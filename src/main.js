// Initialize API endpoints
const apiBase = "https://apploqic.my/index.php/api/v1/search";

let allBusinesses = [];
let currentPage = 1;
let totalPages = 1;
const perPage = 10;
let activeSearchTerm = "";

// Hero Section Elements
const heroSearchInput = document.getElementById("heroSearchInput");
const heroSearchBtn = document.getElementById("heroSearchBtn");
const heroCategoryFilter = document.getElementById("heroCategoryFilter");
const heroFeatureFilter = document.getElementById("heroFeatureFilter");
const heroStatusFilter = document.getElementById("heroStatusFilter");

// Main Section Elements (Optional secondary filters)
const businessContainer = document.getElementById("businessContainer");
const paginationContainer = document.getElementById("paginationContainer");
const categoryFilter = document.getElementById("categoryFilter");
const featuredFilter = document.getElementById("featureFilter");
const statusFilter = document.getElementById("statusFilter");

// Load Businesses (supports pagination + search)
function loadBusinesses(searchTerm = "", page = 1) {
  activeSearchTerm = searchTerm;
  currentPage = page;

  // Get filter values from hero section (primary) or main section (fallback)
  const category = (heroCategoryFilter ? heroCategoryFilter.value : "") || (categoryFilter ? categoryFilter.value : "");
  const featured = (heroFeatureFilter ? heroFeatureFilter.value : "") || (featuredFilter ? featuredFilter.value : "");
  const status = (heroStatusFilter ? heroStatusFilter.value : "") || (statusFilter ? statusFilter.value : "");

  const params = new URLSearchParams();
  params.set("q", searchTerm || "");
  params.set("page", String(page));
  params.set("per_page", String(perPage));
  if (category) params.set("category", category);
  if (featured !== "") params.set("featured_only", featured);
  if (status) params.set("status", status);

  const url = `${apiBase}?${params.toString()}`;
  console.log("Fetching:", url);

  // Scroll to results section smoothly
  if (businessContainer) {
    businessContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  fetch(url)
    .then(res => res.json())
    .then(data => {
      console.log("API response:", data);

      // API returns data.data (array) and pagination info
      const items = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);
      const resultsCount = data.results_count || items.length;
      const pagination = data.pagination || {};
      const serverPage = pagination.current_page ? Number(pagination.current_page) : page;

      allBusinesses = items;

      // If API already returns a paged slice, use it. Otherwise slice client-side.
      let displayed = items;
      if (!pagination.current_page || items.length > perPage) {
        const start = (page - 1) * perPage;
        displayed = items.slice(start, start + perPage);
      }

      // set totals for pager
      totalPages = Math.max(1, Math.ceil((data.results_count || items.length) / perPage));
      currentPage = serverPage;

      renderPage(displayed);
    })
    .catch(err => {
      console.error("Error fetching businesses:", err);
      businessContainer.innerHTML = '<div class="col-12 text-center text-danger py-4">Failed to load businesses</div>';
      paginationContainer.innerHTML = "";
    });
}

// Render businesses on screen
function renderPage(pageBusinesses) {
  businessContainer.innerHTML = "";

  if (!pageBusinesses || !pageBusinesses.length) {
    businessContainer.innerHTML = '<div class="col-12 text-center py-4">No businesses found.</div>';
    paginationContainer.innerHTML = "";
    return;
  }

  // create bootstrap columns (4 per row on lg)
  pageBusinesses.forEach(b => {
    const id = b.id || b.business_id || b._id || "";
    const name = (b.name || b.business_name || "Unnamed").replaceAll('"', '&quot;');
    const category = b.category || b.business_category || "";
    const imageBase = "https://apploqic.my/public/images/";
    const imageField = b.image_url || b.business_image || b.business_img_url || b.business_img || b.image || '';
    const image = imageField 
      ? (imageField.startsWith("http://") || imageField.startsWith("https://") 
          ? imageField 
          : imageBase + encodeURIComponent(imageField))
      : "https://placehold.co/100x100?text=No+Image";

    const col = document.createElement("div");
    col.className = "col-12 col-sm-6 col-md-4 col-lg-3";

    col.innerHTML = `
      <div class="card h-100 business-card shadow-sm">
        <img src="${image}" class="card-img-top w-100 h-100" alt="${name}">
        <div class="card-body d-flex flex-column">
          <h6 class="card-title mb-1">${name}</h6>
          <p class="text-muted small mb-3">${category}</p>
          <div class="mt-auto d-flex gap-2 justify-content-center">
            <a href="view_details.php?id=${encodeURIComponent(id)}" class="btn btn-sm btn-primary">View Details</a>
            <a href="update_business.php?id=${encodeURIComponent(id)}" class="btn btn-sm btn-outline-secondary">Edit</a>
          </div>
        </div>
      </div>
    `;

    businessContainer.appendChild(col);
  });

  renderPagination();
}

// Render pagination with Next/Prev
function renderPagination() {
  paginationContainer.innerHTML = "";

  const prevLi = document.createElement("li");
  prevLi.className = "page-item " + (currentPage <= 1 ? "disabled" : "");
  prevLi.innerHTML = `<button class="page-link">Previous</button>`;
  prevLi.onclick = () => { if (currentPage > 1) loadBusinesses(activeSearchTerm, currentPage - 1); };
  paginationContainer.appendChild(prevLi);

  const pageInfo = document.createElement("li");
  pageInfo.className = "page-item disabled";
  pageInfo.innerHTML = `<span class="page-link">Page ${currentPage} of ${totalPages}</span>`;
  paginationContainer.appendChild(pageInfo);

  const nextLi = document.createElement("li");
  nextLi.className = "page-item " + (currentPage >= totalPages ? "disabled" : "");
  nextLi.innerHTML = `<button class="page-link">Next</button>`;
  nextLi.onclick = () => { if (currentPage < totalPages) loadBusinesses(activeSearchTerm, currentPage + 1); };
  paginationContainer.appendChild(nextLi);
}

// Event: View Details Button (delegate)
businessContainer.addEventListener("click", e => {
  const btn = e.target.closest(".view-details");
  if (!btn) return;
  const id = btn.getAttribute("data-id");
  const found = allBusinesses.find(b => String(b.id || b.business_id) === String(id));
  if (found) showModal(found);
  else console.warn("Business not found in current list.");
});

// ===== HERO SEARCH EVENTS =====
// Hero search button
if (heroSearchBtn) {
  heroSearchBtn.addEventListener("click", () => {
    const term = heroSearchInput ? heroSearchInput.value.trim() : "";
    loadBusinesses(term, 1);
  });
}

// Hero search input - Enter key
if (heroSearchInput) {
  heroSearchInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
      const term = heroSearchInput.value.trim();
      loadBusinesses(term, 1);
    }
  });
}

// Hero filters change
[heroCategoryFilter, heroFeatureFilter, heroStatusFilter].forEach(el => {
  if (!el) return;
  el.addEventListener("change", () => {
    const term = heroSearchInput ? heroSearchInput.value.trim() : "";
    loadBusinesses(term, 1);
  });
});

// ===== MAIN SECTION FILTERS (Secondary) =====
// Sync main section filters with hero filters
[categoryFilter, featuredFilter, statusFilter].forEach(el => {
  if (!el) return;
  el.addEventListener("change", () => {
    // Sync with hero filters
    if (categoryFilter && heroCategoryFilter) heroCategoryFilter.value = categoryFilter.value;
    if (featuredFilter && heroFeatureFilter) heroFeatureFilter.value = featuredFilter.value;
    if (statusFilter && heroStatusFilter) heroStatusFilter.value = statusFilter.value;
    
    loadBusinesses(activeSearchTerm, 1);
  });
});

// Initial load - show all businesses
loadBusinesses("", 1);

// ===== SMOOTH SCROLL TO RESULTS =====
// Add smooth scroll behavior when clicking hero search
document.addEventListener('DOMContentLoaded', function() {
  // Ensure smooth scrolling works
  document.documentElement.style.scrollBehavior = 'smooth';
});