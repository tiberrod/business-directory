// Initialize apiBase and searchEndpoint
const apiBase = "https://apploqic.my/index.php?endpoint=business";
const searchEndpoint = "https://apploqic.my/index.php?endpoint=search&name=";

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
// NOTE: index.php uses id="featureFilter" (no 'd'), match that here:
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

// Load Businesses (supports pagination + search)
function loadBusinesses(searchTerm = "", page = 1) {
  try {
    activeSearchTerm = searchTerm; // remember current search
    currentPage = page;

    // To get the filters' values (guard against null)
    const category = categoryFilter ? categoryFilter.value.trim() : "";
    const featured = featuredFilter ? featuredFilter.value : "";
    const includeInactive = statusFilter ? statusFilter.value : "";

    // Build query string dynamically
    let query = `name=${encodeURIComponent(searchTerm)}&page=${page}&per_page=${perPage}`;
    if (category) query += `&category=${encodeURIComponent(category)}`;
    if (featured !== "") query += `&featured=${encodeURIComponent(featured)}`;
    if (includeInactive !== "") query += `&include_inactive=${encodeURIComponent(includeInactive)}`;

    const url = `https://apploqic.my/index.php?endpoint=search&${query}`;

    fetch(url)
      .then(res => res.json())
      .then(data => {
        console.log("API response:", data);

        // Accept several common response shapes
        const items = data.businesses || data.data || data.results || [];
        // If API returns total or total_count, use it; otherwise derive from items
        const totalCount = data.total || data.total_count || items.length;

        allBusinesses = items;

        // Calculate total pages
        totalPages = Math.max(1, Math.ceil(totalCount / perPage));

        // If the API already returns a page slice (likely), items is the displayed page.
        // To be safe: if items.length <= perPage assume it's already paged; otherwise slice.
        let displayedBusinesses;
        if (items.length <= perPage) {
          displayedBusinesses = items;
        } else {
          const start = (page - 1) * perPage;
          displayedBusinesses = items.slice(start, start + perPage);
        }

        renderPage(displayedBusinesses);
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
function renderPage(pageBusinesses) {
  businessContainer.innerHTML = "";

  if (!pageBusinesses || !pageBusinesses.length) {
    businessContainer.innerHTML = "<p>No businesses found.</p>";
    paginationContainer.innerHTML = "";
    return;
  }

  // Render cards
  pageBusinesses.forEach(b => {
    // image fallbacks
    const imgSrc =
      b.business_img_url ||
      b.image_url ||
      (b.business_img ? b.business_img : (b.image ? b.image : "https://placehold.co/400x200?text=No+Image"));

    const name = b.business_name || b.name || "Unnamed";
    const id = b.id || b.business_id || b._id || "";

    const card = document.createElement("div");
    card.className = "card business-card";
    card.innerHTML = `
      <img src="${imgSrc}" alt="${name}">
      <div class="card-body">
        <h3>${name}</h3>
        <button data-id="${id}" class="btn btn-primary btn-sm view-details">View Details</button>
      </div>
    `;
    businessContainer.appendChild(card);
  });

  renderPagination();
}

// Render pagination with Next/Prev
function renderPagination() {
  paginationContainer.innerHTML = "";
  // simple prev/next and page numbers
  const prevBtn = document.createElement("button");
  prevBtn.textContent = "Prev";
  prevBtn.disabled = currentPage <= 1;
  prevBtn.onclick = () => loadBusinesses(activeSearchTerm, currentPage - 1);
  paginationContainer.appendChild(prevBtn);

  // show current page / total
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

//  Show Modal with Details
function showModal(business) {
  if (!business) return;
  modalTitle.textContent = business.business_name || business.name || "-";
  modalId.textContent = business.id || business.business_id || business._id || "-";

  modalImage.src =
    business.business_img_url ||
    business.image_url ||
    business.business_img ||
    business.image ||
    "https://placehold.co/400x200?text=No+Image";

  modalContact.textContent = business.business_contact || business.contact || "-";
  modalCategory.textContent = business.business_category || business.category || "-";
  modalDescription.textContent = business.business_description || business.description || "-";
  modalCreated.textContent = business.created_at || business.created || "-";
  modalUpdated.textContent = business.updated_at || business.updated || "-";

  const statusVal = (business.status !== undefined) ? business.status : (business.active !== undefined ? business.active : null);
  modalStatus.textContent = (Number(statusVal) === 1 || statusVal === true) ? "Active" : "Inactive";

  const featuredVal = (business.featured !== undefined) ? business.featured : (business.is_featured !== undefined ? business.is_featured : 0);
  modalFeatured.textContent = (Number(featuredVal) === 1 || featuredVal === true) ? "Yes" : "No";

  modal.style.display = "block";
}

// Close modal
modalClose && (modalClose.onclick = () => (modal.style.display = "none"));
window.onclick = e => {
  if (e.target === modal) modal.style.display = "none";
};

// Event: View Details Button (delegate)
businessContainer.addEventListener("click", e => {
  const btn = e.target.closest(".view-details");
  if (!btn) return;
  const id = btn.getAttribute("data-id");
  // Find business in current allBusinesses slice
  const found = allBusinesses.find(b => String(b.id || b.business_id || b._id) === String(id));
  if (found) showModal(found);
  else {
    // if not found, try refetching a single item (optional)
    console.warn("Business not found in current list, consider refetching single item.");
  }
});

// Search button
searchBtn && searchBtn.addEventListener("click", () => {
  const term = searchInput ? searchInput.value.trim() : "";
  loadBusinesses(term, 1);
});

// Auto-refresh when filters change (guard null)
[categoryFilter, featuredFilter, statusFilter].forEach(el => {
  if (!el) return;
  el.addEventListener("change", () => loadBusinesses(activeSearchTerm, 1));
});

// Modal Delete Button (if exists)
const deleteBtn = document.getElementById("deleteBtn");
if (deleteBtn) {
  deleteBtn.addEventListener("click", async () => {
    // implement delete logic if needed
    console.log("Delete clicked");
  });
}

// Close button closes the modal
const closeBtn = document.getElementById("closeBtn");
if (closeBtn) closeBtn.addEventListener("click", () => (modal.style.display = "none"));

// Initial load
loadBusinesses("", 1);
