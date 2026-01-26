document.addEventListener('DOMContentLoaded', () => {
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
  const featuredContainer = document.getElementById('featuredContainer');
  const scrollLeftBtn = document.getElementById('scrollLeftBtn');
  const scrollRightBtn = document.getElementById('scrollRightBtn');

  let currentPage = 1;
  const MIN_LOADING_TIME = 800; // Minimum spinner time in milliseconds

  // ================================
  //  Navbar Scroll Effect
  // ================================
  window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    navbar.classList.toggle('scrolled', window.scrollY > 50);
  });

  // ================================
  //  Loading Helpers
  // ================================
  function showLoading() {
    businessList.innerHTML = `
      <div class="d-flex justify-content-center align-items-center w-100" style="height: 200px;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    `;
    pagination.innerHTML = "";
  }

  // ================================
  //  Fetch Featured Businesses
  // ================================
  function fetchFeaturedBusinesses() {
    if (!featuredContainer) return; // skip if not in page

    fetch(`${baseApiUrl}/api/v1/business`)
      .then(res => res.json())
      .then(data => {
        const featured = data.businesses || data.data || [];
        renderFeaturedBusinesses(featured);
      })
      .catch(err => console.error("Error fetching featured businesses:", err));
  }

  // ================================
  //  Fetch All or Filtered Businesses
  // ================================
  function fetchBusinesses(page = 1) {
  showLoading(); // show spinner immediately

  const searchTerm = searchInput?.value.trim() || "";
  const category = categorySelect?.value || "";
  let url = "";

  if (searchTerm) {
    // New search endpoint
    url = `${baseApiUrl}/api/v1/search?name=${encodeURIComponent(searchTerm)}&page=${page}`;
    if (category) url += `&category=${encodeURIComponent(category)}`;
  } else {
    // Fetch all businesses endpoint
    url = `${baseApiUrl}/api/v1/business?page=${page}`;
    if (category) url += `&category=${encodeURIComponent(category)}`;
  }

  const startTime = Date.now(); // start timer

  fetch(url)
    .then(res => res.json())
    .then(data => {
      const elapsed = Date.now() - startTime;
      const remaining = Math.max(MIN_LOADING_TIME - elapsed, 0);

      setTimeout(() => {
        // Adjust to new API structure if needed
        const businesses = data.data || data.businesses || [];
        const total = data.total || businesses.length;
        const perPage = data.per_page || 10;
        const pageNum = data.page || page;

        if (businesses.length > 0) {
          renderBusinesses(businesses);
          renderPagination(total, pageNum, perPage);
        } else {
          showNoBusinessMessage();
        }
      }, remaining);
    })
    .catch(err => {
      const elapsed = Date.now() - startTime;
      const remaining = Math.max(MIN_LOADING_TIME - elapsed, 0);

      setTimeout(() => showErrorMessage(), remaining);
      console.error('Error fetching businesses:', err);
    });
}


  // ================================
  //  Rendering Functions
  // ================================
  function renderBusinesses(businesses) {
    businessList.innerHTML = businesses.map(biz => `
      <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
        <div class="card h-100 shadow-sm border border-secondary-subtle rounded-4 d-flex flex-column">
          <img 
            src="${biz.business_img_url ? biz.business_img_url : 'images/preview.png'}"
            class="business-img" 
            alt="${biz.business_name}">
          <div class="card-body text-center d-flex flex-column">
            <h5 class="card-title mb-3">${biz.business_name}</h5>
            <a href="business-details.php?id=${biz.id}" class="btn btn-primary btn-sm mt-auto w-100">
              View Details
            </a>
          </div>
        </div>
      </div>
    `).join('');
  }

  function renderFeaturedBusinesses(featured) {
    featuredContainer.innerHTML = featured.map(biz => `
      <div class="card flex-shrink-0 shadow-sm border border-secondary-subtle rounded-4" style="width: 250px;">
        <img 
          src="${biz.business_img_url ? biz.business_img_url : 'images/preview.png'}"
          class="card-img-top"
          alt="${biz.business_name}">
        <div class="card-body text-center">
          <h6 class="card-title mb-2">${biz.business_name}</h6>
          <a href="business-details.php?id=${biz.id}" class="btn btn-sm btn-primary w-100">View</a>
        </div>
      </div>
    `).join('');
  }

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
  //  Search & Filter Listeners
  // ================================
  searchBtn.addEventListener('click', () => {
    currentPage = 1;
    fetchBusinesses();
  });

  searchInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      currentPage = 1;
      fetchBusinesses(1);
    }
  });

  categorySelect.addEventListener('change', () => {
    currentPage = 1;
    fetchBusinesses();
  });

  // ================================
  //  Horizontal Scroll (Optional)
  // ================================
  if (featuredContainer && scrollLeftBtn && scrollRightBtn) {
    scrollLeftBtn.addEventListener('click', () => {
      featuredContainer.scrollBy({ left: -300, behavior: 'smooth' });
    });

    scrollRightBtn.addEventListener('click', () => {
      featuredContainer.scrollBy({ left: 300, behavior: 'smooth' });
    });
  }

  // ================================
  //  Initial Load
  // ================================
  fetchFeaturedBusinesses();
  fetchBusinesses();
});
