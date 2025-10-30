// ================================
//  Base Configuration
// ================================
const baseApiUrl = "https://apploqic.my/index.php";

// ================================
//  DOM Elements
// ================================
const businessList = document.getElementById('businessList');
const pagination = document.getElementById('pagination');
const searchInput = document.getElementById('searchInput');
const searchBtn = document.getElementById('searchBtn');
const categorySelect = document.getElementById('categorySelect');
const highlightContainer = document.getElementById('highlightedContainer');
const scrollLeftBtn = document.getElementById('scrollLeftBtn');
const scrollRightBtn = document.getElementById('scrollRightBtn');

let currentPage = 1;

// ================================
//  Navbar Scroll Effect
// ================================
window.addEventListener('scroll', () => {
  const navbar = document.querySelector('.navbar');
  navbar.classList.toggle('scrolled', window.scrollY > 50);
});

// ================================
//  Fetch Highlighted Businesses
// ================================
function fetchHighlightedBusinesses() {
  fetch(`${baseApiUrl}?endpoint=highlighted`)
    .then(res => res.json())
    .then(data => {
      const highlights = data.businesses || data.data || [];
      renderHighlightedBusinesses(highlights);
    })
    .catch(err => console.error("Error fetching highlighted businesses:", err));
}

// ================================
//  Fetch All or Filtered Businesses
// ================================
function fetchBusinesses(page = 1) {
  const searchTerm = searchInput.value.trim();
  const category = categorySelect?.value || "";
  let url = "";

  if (searchTerm) {
    // Search by name (and optional category)
    url = `${baseApiUrl}?endpoint=search&name=${encodeURIComponent(searchTerm)}&page=${page}`;
    if (category) url += `&category=${encodeURIComponent(category)}`;
  } else {
    // Fetch all or by category
    url = `${baseApiUrl}?endpoint=business&page=${page}`;
    if (category) url += `&category=${encodeURIComponent(category)}`;
  }

  fetch(url)
    .then(res => res.json())
    .then(data => {
      const businesses = data.businesses || data.data || [];
      const total = data.total || businesses.length;
      const perPage = data.per_page || 10;
      const pageNum = data.page || page;

      if (businesses.length > 0) {
        renderBusinesses(businesses);
        renderPagination(total, pageNum, perPage);
      } else {
        showNoBusinessMessage();
      }
    })
    .catch(err => {
      console.error('Error fetching:', err);
      showErrorMessage();
    });
}

// ================================
//  Rendering Functions
// ================================

// 🧱 Render Business Cards
function renderBusinesses(businesses) {
  businessList.innerHTML = businesses.map(biz => `
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <div class="card h-100 shadow-sm border border-secondary-subtle rounded-4 d-flex flex-column">
        <img 
          src="${biz.business_img ? biz.business_img_url : 'images/preview.png'}"
          class="business-img" 
          alt="${biz.business_name}">
        <div class="card-body text-center">
          <h5 class="card-title">${biz.business_name}</h5>
          <div class="mt-auto">
            <a href="business-details.php?id=${biz.id}" class="btn btn-primary btn-sm w-100">
              View Details
            </a>
          </div>
        </div>
      </div>
    </div>
  `).join('');
}

//  Render Highlighted Businesses
function renderHighlightedBusinesses(highlights) {
  highlightContainer.innerHTML = highlights.map(biz => `
    <div class="card flex-shrink-0 shadow-sm border border-secondary-subtle rounded-4" style="width: 250px;">
      <img 
        src="${biz.business_img ? biz.business_img_url : 'images/preview.png'}"
        class="card-img-top"
        alt="${biz.business_name}">
      <div class="card-body text-center">
        <h6 class="card-title mb-2">${biz.business_name}</h6>
        <a href="business-details.php?id=${biz.id}" class="btn btn-sm btn-primary w-100">View</a>
      </div>
    </div>
  `).join('');
}

//  Render Pagination Controls
function renderPagination(total, page, perPage) {
  const totalPages = Math.ceil(total / perPage);
  pagination.innerHTML = "";

  // Previous Button
  pagination.innerHTML += `
    <li class="page-item ${page === 1 ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${page - 1}" aria-label="Previous">&laquo; Prev</a>
    </li>
  `;

  // Page Numbers
  for (let i = 1; i <= totalPages; i++) {
    pagination.innerHTML += `
      <li class="page-item ${i === page ? 'active' : ''}">
        <a class="page-link" href="#" data-page="${i}">${i}</a>
      </li>
    `;
  }

  // Next Button
  pagination.innerHTML += `
    <li class="page-item ${page === totalPages ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${page + 1}" aria-label="Next">Next &raquo;</a>
    </li>
  `;

  // Add click listeners
  pagination.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      const pageNum = parseInt(link.getAttribute('data-page'));
      if (!isNaN(pageNum) && pageNum >= 1 && pageNum <= totalPages && pageNum !== page) {
        currentPage = pageNum;
        fetchBusinesses(pageNum);
      }
    });
  });
}

// ================================
//  Message Helpers
// ================================
function showNoBusinessMessage() {
  businessList.innerHTML = `
    <div class="text-center mt-5 fade-in">
      <img src="images/iconEncourage.png" alt="No data" class="img-fluid mb-3" style="max-width: 200px; opacity: 0.8;">
      <h5 class="text-muted">No businesses found yet.</h5>
      <p class="text-secondary">Be the first to showcase your business! 🌟</p>
      <a href="" class="btn btn-primary mt-2">Post Your Business</a>
    </div>
  `;
  pagination.innerHTML = "";
}

function showErrorMessage() {
  businessList.innerHTML = `
    <p class="text-center text-danger mt-5">
      Failed to load data.<br>
      (Server down or internet connection disrupted.)
    </p>
  `;
  pagination.innerHTML = "";
}

// ================================
//   Search & Filter
// ================================

// Search when pressing "Enter"
searchInput.addEventListener('keypress', (e) => {
  if (e.key === 'Enter') {
    e.preventDefault();
    currentPage = 1;
    fetchBusinesses(1);
  }
});

// Change category filter
categorySelect.addEventListener('change', () => {
  currentPage = 1;
  fetchBusinesses();
});

// ================================
//  Horizontal Scroll Controls
// ================================
scrollLeftBtn.addEventListener('click', () => {
  highlightContainer.scrollBy({ left: -300, behavior: 'smooth' });
});

scrollRightBtn.addEventListener('click', () => {
  highlightContainer.scrollBy({ left: 300, behavior: 'smooth' });
});

// ================================
//  Initial Load
// ================================
fetchHighlightedBusinesses();
fetchBusinesses();
