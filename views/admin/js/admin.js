// Admin Dashboard JavaScript with V1 API Integration
// Requires: admin-api.js to be loaded first

// Check if API is available
function checkAPIAvailability() {
  if (typeof window.adminAPI === 'undefined') {
    console.error('AdminAPI not loaded. Loading fallback...');
    businessContainer.innerHTML = `
      <div class="col-12 text-center py-5">
        <div class="alert alert-warning">
          <h4><i class="fas fa-exclamation-triangle"></i> API Loading Issue</h4>
          <p>There seems to be an issue loading the admin API. Please refresh the page.</p>
          <button onclick="window.location.reload()" class="btn btn-primary">
            <i class="fas fa-refresh me-2"></i>Refresh Page
          </button>
        </div>
      </div>
    `;
    return false;
  }
  return true;
}

let allBusinesses = [];
let currentPage = 1;
let totalPages = 1;
const perPage = 12;
let activeSearchTerm = "";
let currentFilters = {};

// Elements
const businessContainer = document.getElementById("businessContainer");
const paginationContainer = document.getElementById("paginationContainer");
const searchInput = document.getElementById("searchInput");
const searchBtn = document.getElementById("searchBtn");
const categoryFilter = document.getElementById("categoryFilter");
const featuredFilter = document.getElementById("featureFilter");
const statusFilter = document.getElementById("statusFilter");

// Load Businesses with API v1 integration
async function loadBusinesses(searchTerm = "", page = 1) {
  // Check API availability first
  if (!checkAPIAvailability()) {
    return;
  }

  try {
    console.log('Loading businesses...', { searchTerm, page }); // Debug logging
    
    activeSearchTerm = searchTerm;
    currentPage = page;

    // Prepare filters
    currentFilters = {
      q: searchTerm || "",
      page: page,
      per_page: perPage
    };

    if (categoryFilter && categoryFilter.value) {
      currentFilters.category = categoryFilter.value;
    }
    if (featuredFilter && featuredFilter.value !== "") {
      currentFilters.featured_only = featuredFilter.value;
    }
    if (statusFilter && statusFilter.value) {
      currentFilters.status = statusFilter.value;
    }

    // Use search endpoint if there's a search term or filters, otherwise use general business endpoint
    let data;
    try {
      data = searchTerm || Object.keys(currentFilters).length > 3 
        ? await window.adminAPI.searchBusinesses(currentFilters)
        : await window.adminAPI.getBusinesses(currentFilters);
    } catch (apiError) {
      console.warn('New API failed, trying fallback:', apiError);
      
      // Fallback to old API approach
      const params = new URLSearchParams();
      params.set("q", searchTerm || "");
      params.set("page", String(page));
      params.set("per_page", String(perPage));
      
      if (currentFilters.category) params.set("category", currentFilters.category);
      if (currentFilters.featured_only !== "") params.set("featured_only", currentFilters.featured_only);
      if (currentFilters.status) params.set("status", currentFilters.status);

      const fallbackUrl = `../../index.php?endpoint=search&${params.toString()}`;
      console.log('Fallback URL:', fallbackUrl);
      
      const response = await fetch(fallbackUrl);
      data = await response.json();
    }

    console.log("API response:", data);

    // Handle response data
    const items = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);
    const pagination = data.pagination || {};
    
    allBusinesses = items;
    totalPages = pagination.total_pages || Math.max(1, Math.ceil((data.results_count || items.length) / perPage));
    currentPage = pagination.current_page || page;

    renderPage(items);
    renderPagination();
    
  } catch (error) {
    console.error("Error loading businesses:", error);
    window.adminAPI.showMessage(
      window.adminAPI.handleError(error, 'Loading businesses'), 
      'error'
    );
    businessContainer.innerHTML = '<div class="col-12 text-center text-danger py-4">Failed to load businesses. Please try again.</div>';
    paginationContainer.innerHTML = "";
  }
}

// Render businesses on screen with enhanced actions
function renderPage(pageBusinesses) {
  businessContainer.innerHTML = "";

  if (!pageBusinesses || !pageBusinesses.length) {
    businessContainer.innerHTML = '<div class="col-12 text-center py-4">No businesses found.</div>';
    return;
  }

  // Create bootstrap columns
  pageBusinesses.forEach(b => {
    const id = b.id || b.business_id || b._id || "";
    const name = (b.name || b.business_name || "Unnamed").replaceAll('"', '&quot;');
    const category = b.category || b.business_category || "";
    const status = String(b.status || b.business_status || 'active');
    const isActive = status.toLowerCase() === 'active' || status === '1' || status === 1;
    const imageField = b.image_url || b.business_image || b.business_img_url || b.business_img || b.image || '';
    
    // Environment-aware fallback image
    const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname.includes('127.0.0.1');
    const fallbackImage = isLocalhost ? "../../public/assets/preview.png" : "../assets/preview.png";
    const image = imageField || fallbackImage;

    const col = document.createElement("div");
    col.className = "col-12 col-sm-6 col-md-4 col-lg-3";

    col.innerHTML = `
      <div class="card h-100 business-card shadow-sm ${!isActive ? 'opacity-75' : ''}">
        <img src="${image}" class="card-img-top" alt="${name}">
        <div class="card-body d-flex flex-column">
          <h6 class="card-title mb-1">${name}</h6>
          <p class="text-muted small mb-2">${category}</p>
          <span class="badge ${isActive ? 'bg-success' : 'bg-danger'} mb-3">${status}</span>
          <div class="mt-auto d-flex flex-column gap-2">
            <div class="d-flex gap-2">
              <button onclick="viewBusinessDetails('${id}')" class="btn btn-sm btn-primary flex-fill">
                <i class="fas fa-eye me-1"></i>View
              </button>
              <button onclick="editBusiness('${id}')" class="btn btn-sm btn-outline-secondary flex-fill">
                <i class="fas fa-edit me-1"></i>Edit
              </button>
            </div>
            <div class="d-flex gap-2">
              ${isActive 
                ? `<button onclick="deleteBusiness('${id}', '${name}')" class="btn btn-sm btn-outline-danger flex-fill">
                     <i class="fas fa-trash me-1"></i>Delete
                   </button>`
                : `<button onclick="reactivateBusiness('${id}', '${name}')" class="btn btn-sm btn-outline-success flex-fill">
                     <i class="fas fa-undo me-1"></i>Reactivate
                   </button>`
              }
            </div>
          </div>
        </div>
      </div>
    `;

    businessContainer.appendChild(col);
  });
}

// Business action functions
async function viewBusinessDetails(id) {
  try {
    const response = await window.adminAPI.getBusiness(id);
    if (response.status === 'success' && response.data) {
      // Redirect to view details page with the business data
      window.location.href = `view-details.php?id=${encodeURIComponent(id)}`;
    } else {
      window.adminAPI.showMessage('Business not found', 'error');
    }
  } catch (error) {
    window.adminAPI.showMessage(
      window.adminAPI.handleError(error, 'Loading business details'), 
      'error'
    );
  }
}

async function editBusiness(id) {
  // Redirect to update page with the business ID
  window.location.href = `update-business.php?id=${encodeURIComponent(id)}`;
}

async function deleteBusiness(id, name) {
  if (!confirm(`Are you sure you want to delete "${name}"? This action cannot be undone.`)) {
    return;
  }

  try {
    const response = await window.adminAPI.deleteBusiness(id);
    if (response.status === 'success') {
      window.adminAPI.showMessage(`Successfully deleted "${name}"`, 'success');
      // Reload the current page
      loadBusinesses(activeSearchTerm, currentPage);
    } else {
      window.adminAPI.showMessage(response.message || 'Failed to delete business', 'error');
    }
  } catch (error) {
    window.adminAPI.showMessage(
      window.adminAPI.handleError(error, 'Deleting business'), 
      'error'
    );
  }
}

async function reactivateBusiness(id, name) {
  if (!confirm(`Are you sure you want to reactivate "${name}"?`)) {
    return;
  }

  try {
    const response = await window.adminAPI.reactivateBusiness(id);
    if (response.status === 'success') {
      window.adminAPI.showMessage(`Successfully reactivated "${name}"`, 'success');
      // Reload the current page
      loadBusinesses(activeSearchTerm, currentPage);
    } else {
      window.adminAPI.showMessage(response.message || 'Failed to reactivate business', 'error');
    }
  } catch (error) {
    window.adminAPI.showMessage(
      window.adminAPI.handleError(error, 'Reactivating business'), 
      'error'
    );
  }
}

// Render pagination with Next/Prev
function renderPagination() {
  paginationContainer.innerHTML = "";

  const prevBtn = document.createElement("li");
  prevBtn.className = "page-item";
  if (currentPage <= 1) prevBtn.classList.add("disabled");
  prevBtn.innerHTML = '<a class="page-link" href="#">Prev</a>';
  prevBtn.onclick = () => {
    if (currentPage > 1) loadBusinesses(activeSearchTerm, currentPage - 1);
  };
  paginationContainer.appendChild(prevBtn);

  const pageInfo = document.createElement("li");
  pageInfo.className = "page-item disabled";
  pageInfo.innerHTML = `<span class="page-link">Page ${currentPage} of ${totalPages}</span>`;
  paginationContainer.appendChild(pageInfo);

  const nextBtn = document.createElement("li");
  nextBtn.className = "page-item";
  if (currentPage >= totalPages) nextBtn.classList.add("disabled");
  nextBtn.innerHTML = '<a class="page-link" href="#">Next</a>';
  nextBtn.onclick = () => {
    if (currentPage < totalPages) loadBusinesses(activeSearchTerm, currentPage + 1);
  };
  paginationContainer.appendChild(nextBtn);
}

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

// Initialize dashboard when DOM and API are ready
function initializeDashboard() {
  console.log('Initializing admin dashboard...');
  
  // Check if API is available
  if (typeof window.adminAPI !== 'undefined') {
    console.log('API ready, loading businesses...');
    loadBusinesses("", 1);
  } else {
    console.log('API not ready, retrying in 500ms...');
    setTimeout(initializeDashboard, 500);
  }
}

// Start initialization
document.addEventListener('DOMContentLoaded', () => {
  console.log('DOM loaded, starting initialization...');
  initializeDashboard();
});