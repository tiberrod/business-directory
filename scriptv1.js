// ==== Global variables ====
const businessList = document.getElementById('businessList');
const pagination = document.getElementById('pagination');
const searchInput = document.getElementById('searchInput');      // optional
const categorySelect = document.getElementById('categorySelect'); // optional
const featuredSelect = document.getElementById('featuredSelect'); // optional
const statusSelect = document.getElementById('statusSelect');     // optional

let currentPage = 1;
const baseApiUrl = 'https://apploqic.my/api/v1/business'; // adjust if needed

// ================================
//  Fetch All or Filtered Businesses
// ================================
function fetchBusinesses(page = 1) {
  const searchTerm = searchInput?.value.trim() || '';
  const category = categorySelect?.value || '';
  const featured = featuredSelect?.value;
  const status = statusSelect?.value;

  const params = new URLSearchParams({ page });

  if (searchTerm) params.append('name', searchTerm);
  if (category) params.append('category', category);
  if (featured) params.append('featured', featured);
  if (status) params.append('status', status);

  const url = `${baseApiUrl}?${params.toString()}`;

  fetch(url)
    .then(res => res.json())
    .then(data => {
      const businesses = data.data || [];
      const paginationInfo = data.pagination || {};
      const total = paginationInfo.total || businesses.length;
      const perPage = paginationInfo.per_page || 10;
      const pageNum = paginationInfo.current_page || page;

      if (businesses.length > 0) {
        renderBusinesses(businesses);
        renderPagination(total, pageNum, perPage);
      } else {
        businessList.innerHTML = '<p class="text-center">No businesses found.</p>';
        pagination.innerHTML = '';
      }
    })
    .catch(err => {
      console.error('Error fetching businesses:', err);
      businessList.innerHTML = '<p class="text-center text-danger">Error loading businesses.</p>';
      pagination.innerHTML = '';
    });
}

// 🧱 Render Business Cards
function renderBusinesses(businesses) {
  businessList.innerHTML = businesses.map(biz => `
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <div class="card h-100 shadow-sm border border-secondary-subtle rounded-4 d-flex flex-column">
        <img 
          src="${biz.business_img_url || 'images/preview.png'}"
          class="business-img"
          alt="${biz.business_name}">
        <div class="card-body text-center d-flex flex-column">
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

// 🧭 Render Pagination Controls
function renderPagination(total, page, perPage) {
  const totalPages = Math.ceil(total / perPage);
  pagination.innerHTML = '';

  // Previous Button
  pagination.innerHTML += `
    <li class="page-item ${page === 1 ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${page - 1}">&laquo; Prev</a>
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
      <a class="page-link" href="#" data-page="${page + 1}">Next &raquo;</a>
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

// ===== Initial fetch =====
fetchBusinesses(currentPage);
