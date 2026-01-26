// ==== Global variables ====
const businessList = document.getElementById('businessList');
const pagination = document.getElementById('pagination');
const searchInput = document.getElementById('searchInput');      // optional
const categorySelect = document.getElementById('categorySelect'); // optional
const featuredSelect = document.getElementById('featuredSelect'); // optional
const statusSelect = document.getElementById('statusSelect');     // optional

let currentPage = 1;

// Use environment configuration from PHP (preferred) or auto-detect as fallback
const baseApiUrl = window.ENV_CONFIG ? window.ENV_CONFIG.apiBaseUrl : (() => {
  const hostname = window.location.hostname;
  const port = window.location.port;
  const protocol = window.location.protocol;
  const isLocalhost = hostname === 'localhost' || hostname === '127.0.0.1' || hostname.startsWith('localhost:');
  
  if (isLocalhost) {
    // Build full URL for localhost - use current domain with query params
    return window.location.origin + '/index.php?endpoint=business';
  } else {
    // Production apploqic.my - use index.php with query params
    return 'https://apploqic.my/index.php?endpoint=business';
  }
})();

// Get search API URL - for dedicated search endpoint
const searchApiUrl = window.ENV_CONFIG ? window.ENV_CONFIG.searchApiUrl : (() => {
  const hostname = window.location.hostname;
  const isLocalhost = hostname === 'localhost' || hostname === '127.0.0.1' || hostname.startsWith('localhost:');
  
  if (isLocalhost) {
    // Build full URL for localhost search
    return window.location.origin + '/index.php?endpoint=search';
  } else {
    // Production apploqic.my - use index.php with search endpoint
    return 'https://apploqic.my/index.php?endpoint=search';
  }
})();

console.log('Environment:', window.ENV_CONFIG?.environment || 'auto-detected');
console.log('Port:', window.location.port);
console.log('API Base URL:', baseApiUrl);
console.log('Search API URL:', searchApiUrl);

// ================================
//  Fetch All or Filtered Businesses
// ================================
function fetchBusinesses(page = 1) {
  console.log('=== Fetch Businesses Debug ===');
  console.log('Page:', page);
  console.log('Current hostname:', window.location.hostname);
  console.log('ENV_CONFIG:', window.ENV_CONFIG);
  
  const searchTerm = searchInput?.value.trim() || '';
  const category = categorySelect?.value || '';
  const featured = featuredSelect?.value;
  const status = statusSelect?.value;

  console.log('Search params:', { searchTerm, category, featured, status });

  const params = new URLSearchParams({ page });

  if (searchTerm) params.append('name', searchTerm);
  if (category) params.append('category', category);
  if (featured) params.append('featured', featured);
  if (status) params.append('status', status);

  // URL construction for different environments
  let url;
  
  // If we have search term, use search endpoint, otherwise use business listing
  if (searchTerm) {
    // Use dedicated search endpoint for search queries
    if (params.toString()) {
      url = `${searchApiUrl}&${params.toString()}`;
    } else {
      url = searchApiUrl;
    }
  } else {
    // Use business listing endpoint for general browsing
    if (params.toString()) {
      // Base URL already contains '?endpoint=business', so append with '&'
      url = `${baseApiUrl}&${params.toString()}`;
    } else {
      // No additional params, use base URL as-is
      url = baseApiUrl;
    }
  }
  
  console.log('Fetching from URL:', url);
  console.log('Using baseApiUrl:', baseApiUrl);
  console.log('Using searchApiUrl:', searchApiUrl);

  fetch(url)
    .then(res => {
      console.log('Response status:', res.status);
      console.log('Response headers:', res.headers);
      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}`);
      }
      return res.json();
    })
    .then(data => {
      console.log('API Response received:', data);
      const businesses = data.data || [];
      const paginationInfo = data.pagination || {};
      const total = paginationInfo.total || businesses.length;
      const perPage = paginationInfo.per_page || 10;
      const pageNum = paginationInfo.current_page || page;

      console.log('Businesses found:', businesses.length);
      console.log('Total:', total, 'Per page:', perPage, 'Page:', pageNum);

      if (businesses.length > 0) {
        renderBusinesses(businesses);
        renderPagination(total, pageNum, perPage);
      } else {
        businessList.innerHTML = '<p class="text-center">No businesses found.</p>';
        pagination.innerHTML = '';
      }
    })
    .catch(err => {
      console.error('=== API Error Details ===');
      console.error('Error fetching businesses:', err);
      console.error('URL that failed:', url);
      console.error('Base API URL:', baseApiUrl);
      console.error('Search API URL:', searchApiUrl);
      console.error('Current hostname:', window.location.hostname);
      
      businessList.innerHTML = `
        <div class="col-12">
          <div class="alert alert-danger">
            <h5>Error loading businesses</h5>
            <p><strong>Error:</strong> ${err.message}</p>
            <p><strong>URL:</strong> ${url}</p>
            <p><strong>Environment:</strong> ${window.ENV_CONFIG?.environment || 'auto-detected'}</p>
          </div>
        </div>
      `;
      pagination.innerHTML = '';
    });
}

// ================================
//  Fetch Individual Business Details
// ================================
function fetchBusinessDetails(businessId) {
  const hostname = window.location.hostname;
  const isLocalhost = hostname === 'localhost' || hostname === '127.0.0.1' || hostname.startsWith('localhost:');
  
  let url;
  if (isLocalhost) {
    // Build full URL for localhost
    url = `${window.location.origin}/index.php?endpoint=business&id=${businessId}`;
  } else {
    // Production apploqic.my
    url = `https://apploqic.my/index.php?endpoint=business&id=${businessId}`;
  }
  
  console.log('Fetching business details from:', url);
  
  return fetch(url)
    .then(res => {
      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}`);
      }
      return res.json();
    })
    .then(data => {
      console.log('Business details received:', data);
      return data.data || null;
    })
    .catch(err => {
      console.error('Error fetching business details:', err);
      throw err;
    });
}

// 🧱 Render Business Cards
function renderBusinesses(businesses) {
  // Use environment configuration from PHP
  const isLocalhost = window.ENV_CONFIG ? window.ENV_CONFIG.isLocalhost : false;
  
  let fallbackImage;
  let businessDetailsPath;
  
  if (isLocalhost) {
    // For localhost development
    fallbackImage = '../../public/assets/preview.png';
    // Use root level business-details.php router
    businessDetailsPath = 'business-details.php';
  } else {
    // For production (cPanel)
    fallbackImage = './assets/preview.png';
    businessDetailsPath = 'business-details.php';
  }
  
  console.log('Business details path:', businessDetailsPath);
  
  businessList.innerHTML = businesses.map(biz => `
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <div class="card h-100 shadow-sm border border-secondary-subtle rounded-4 d-flex flex-column">
        <img 
          src="${getBusinessImage(biz, isLocalhost)}"
          class="business-img"
          alt="${escapeHtml(biz.business_name)}"
          onerror="this.src='${fallbackImage}'; this.onerror=null;">
        <div class="card-body text-center d-flex flex-column">
          <h5 class="card-title">${escapeHtml(biz.business_name)}</h5>
          <div class="mt-auto">
            <a href="${businessDetailsPath}?id=${biz.id}" class="btn btn-primary btn-sm w-100">
              View Details
            </a>
          </div>
        </div>
      </div>
    </div>
  `).join('');
}

// Helper function to get business image with proper fallback
function getBusinessImage(business, isLocalhost) {
  // Priority: business_img_url > business_img with base path > fallback
  if (business.business_img_url && business.business_img_url.trim() !== '') {
    return business.business_img_url;
  }
  
  if (business.business_img && business.business_img.trim() !== '') {
    // Construct image URL based on environment
    if (isLocalhost) {
      return `../../public/images/${business.business_img}`;  // Localhost relative path
    } else {
      return `./images/${business.business_img}`;  // Production path
    }
  }
  
  // Return fallback handled by calling function
  return isLocalhost ? '../../public/assets/preview.png' : './assets/preview.png';
}

// Helper function to escape HTML
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
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

// ===== Event Listeners =====

// Search functionality
if (searchInput) {
  // Search on button click
  document.getElementById('searchBtn')?.addEventListener('click', () => {
    currentPage = 1;
    fetchBusinesses(currentPage);
  });

  // Search on Enter key
  searchInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
      currentPage = 1;
      fetchBusinesses(currentPage);
    }
  });
}

// Category filter
if (categorySelect) {
  categorySelect.addEventListener('change', () => {
    currentPage = 1;
    fetchBusinesses(currentPage);
  });
}

// Featured filter
if (featuredSelect) {
  featuredSelect.addEventListener('change', () => {
    currentPage = 1;
    fetchBusinesses(currentPage);
  });
}

// Status filter
if (statusSelect) {
  statusSelect.addEventListener('change', () => {
    currentPage = 1;
    fetchBusinesses(currentPage);
  });
}

// ===== Navbar scroll effect =====
let lastScrollTop = 0;
const navbar = document.querySelector('.navbar');

if (navbar) {
  window.addEventListener('scroll', function() {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (scrollTop > 100) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }

    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
  });
}

// ===== Initial fetch =====
fetchBusinesses(currentPage);