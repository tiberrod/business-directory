// Initialize API endpoints
const apiBase = "https://apploqic.my/index.php/api/v1/search";

let allBusinesses = [];
let currentPage = 1;
let totalPages = 1;
const perPage = 10;
let activeSearchTerm = "";

// Elements
const businessContainer = document.getElementById("businessContainer");
const paginationContainer = document.getElementById("paginationContainer");
const searchInput = document.getElementById("searchInput");
const searchBtn = document.getElementById("searchBtn");
const categoryFilter = document.getElementById("categoryFilter");
const featuredFilter = document.getElementById("featureFilter");
const statusFilter = document.getElementById("statusFilter");

// Modal elements
const modal = document.getElementById("detailsModal");
const modalTitle = document.getElementById("modalTitle");
const modalId = document.getElementById("modalId");
const modalImage = document.getElementById("modalImage");
const modalContact = document.getElementById("modalContact");
const modalCategory = document.getElementById("modalCategory");
const modalDescription = document.getElementById("modalDescription");
const modalCreated = document.getElementById("modalCreated");
const modalUpdated = document.getElementById("modalUpdated");
const modalStatus = document.getElementById("modalStatus");
const modalFeatured = document.getElementById("modalFeatured");
const modalClose = document.querySelector(".close");
const editBtn = document.getElementById("editBtn");


// Load Businesses (supports pagination + search)
function loadBusinesses(searchTerm = "", page = 1) {
  activeSearchTerm = searchTerm;
  currentPage = page;

  const category = categoryFilter ? categoryFilter.value : "";
  const featured = featuredFilter ? featuredFilter.value : "";
  const status = statusFilter ? statusFilter.value : "";

  const params = new URLSearchParams();
  params.set("q", searchTerm || "");
  params.set("page", String(page));
  params.set("per_page", String(perPage));
  if (category) params.set("category", category);
  if (featured !== "") params.set("featured_only", featured);
  if (status) params.set("status", status);

  const url = `${apiBase}?${params.toString()}`;
  console.log("Fetching:", url);

  fetch(url)
    .then(res => res.json())
    .then(data => {
      console.log("API response:", data);

      // API returns data.data (array) and pagination info
      const items = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);
      const resultsCount = data.results_count || items.length;
      const pagination = data.pagination || {};
      const serverPerPage = pagination.per_page ? Number(pagination.per_page) : perPage;
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
    const imageBase = "https://apploqic.my/Apploqic_Business_Directory/public/images/";
    const imageField = b.image_url || b.business_image || b.business_img_url || b.business_img || b.image || '';
    const image = imageField ? (imageField.startsWith("http://") || imageField.startsWith("https://") ? imageField : imageBase + encodeURIComponent(imageField)) : "https://placehold.co/400x200?text=No+Image";

    const col = document.createElement("div");
    col.className = "col-12 col-sm-6 col-md-4 col-lg-3";

    col.innerHTML = `
      <div class="card h-100 business-card shadow-sm">
        <img src="${image}" class="card-img-top" alt="${name}">
        <div class="card-body d-flex flex-column">
          <h6 class="card-title mb-1">${name}</h6>
          <p class="text-muted small mb-3">${category}</p>
          <div class="mt-auto d-flex gap-2 justify-content-center">
            <a href="view-details.php?id=${encodeURIComponent(id)}" class="btn btn-sm btn-primary">View Details</a>
            <a href="update-business.php?id=${encodeURIComponent(id)}" class="btn btn-sm btn-outline-secondary">Edit</a>
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

  const prevBtn = document.createElement("button");
  prevBtn.textContent = "Prev";
  prevBtn.disabled = currentPage <= 1;
  prevBtn.onclick = () => loadBusinesses(activeSearchTerm, currentPage - 1);
  paginationContainer.appendChild(prevBtn);

  const pageInfo = document.createElement("span");
  pageInfo.style.padding = "8px 12px";
  pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
  paginationContainer.appendChild(pageInfo);

  const nextBtn = document.createElement("button");
  nextBtn.textContent = "Next";
  nextBtn.disabled = currentPage >= totalPages;
  nextBtn.onclick = () => loadBusinesses(activeSearchTerm, currentPage + 1);
  paginationContainer.appendChild(nextBtn);
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

// Search button
searchBtn && searchBtn.addEventListener("click", () => {
  const term = searchInput ? searchInput.value.trim() : "";
  loadBusinesses(term, 1);
});

// Enter key in search input
searchInput && searchInput.addEventListener("keypress", (e) => {
  if (e.key === "Enter") {
    const term = searchInput.value.trim();
    loadBusinesses(term, 1);
  }
});

// Auto-refresh when filters change
[categoryFilter, featuredFilter, statusFilter].forEach(el => {
  if (!el) return;
  el.addEventListener("change", () => loadBusinesses(activeSearchTerm, 1));
});

// Modal Delete Button
const deleteBtn = document.getElementById("deleteBtn");
if (deleteBtn) {
  deleteBtn.addEventListener("click", async () => {
    console.log("Delete clicked - implement delete logic");
  });
}

// Close button closes the modal
const closeBtn = document.getElementById("closeBtn");
if (closeBtn) closeBtn.addEventListener("click", () => (modal.style.display = "none"));

// Initial load - show all businesses
loadBusinesses("", 1);