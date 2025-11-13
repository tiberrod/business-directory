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
  try {
    activeSearchTerm = searchTerm;
    currentPage = page;

    // Get filter values (guard against null)
    const category = categoryFilter ? categoryFilter.value.trim() : "";
    const featured = featuredFilter ? featuredFilter.value : "";
    const status = statusFilter ? statusFilter.value : "active";

    // Build query string with new API format
    let query = `q=${encodeURIComponent(searchTerm)}&page=${page}&per_page=${perPage}`;
    
    if (category) query += `&category=${encodeURIComponent(category)}`;
    if (featured !== "") query += `&featured_only=${encodeURIComponent(featured)}`;
    if (status) query += `&status=${encodeURIComponent(status)}`;

    const url = `${apiBase}?${query}`;
    console.log("Fetching from:", url);

    fetch(url)
      .then(res => res.json())
      .then(data => {
        console.log("API response:", data);

        // New API returns data array directly
        const items = data.data || [];
        
        // Get pagination info from API response
        const pagination = data.pagination || {};
        const resultsCount = data.results_count || items.length;

        allBusinesses = items;

        // Calculate total pages from API pagination or results
        totalPages = Math.max(1, Math.ceil(resultsCount / perPage));

        renderPage(items);
      })
      .catch(err => {
        console.error("Error fetching businesses:", err);
        businessContainer.innerHTML = "<p class='text-danger'>Failed to load businesses. Check console for errors.</p>";
        paginationContainer.innerHTML = "";
      });
  } catch (err) {
    console.error("Unexpected error in loadBusinesses:", err);
  }
}

// Render businesses on screen
// Render businesses on screen
function renderPage(pageBusinesses) {
  businessContainer.innerHTML = "";

  if (!pageBusinesses || !pageBusinesses.length) {
    businessContainer.innerHTML = "<p>No businesses found.</p>";
    paginationContainer.innerHTML = "";
    return;
  }

  // Render cards
  pageBusinesses.forEach(b => {
    // image fallbacks - adjust field names based on API response
    const imgSrc =
      b.image_url ||
      b.business_image ||
      b.image ||
      b.logo ||
      "https://placehold.co/400x200?text=No+Image";

    const name = b.name || b.business_name || "Unnamed";
    const id = b.id || b.business_id || "";

    const card = document.createElement("div");
    card.className = "col-12 col-sm-6 col-md-4 col-lg-3"; // Adjusted for responsive layout
    card.innerHTML = `
      <div class="card business-card">
        <img src="${imgSrc}" alt="${name}" class="card-img-top">
        <div class="card-body">
          <h5 class="card-title">${name}</h5>
          <a href="view-details.php?id=${id}" class="btn btn-primary btn-sm">View Details</a>
        </div>
      </div>
    `;
    businessContainer.appendChild(card);
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