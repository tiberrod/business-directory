// Base URL
const baseApiUrl = "https://apploqic.my/index.php";

// Elements
const businessList = document.getElementById('businessList');
const pagination = document.getElementById('pagination');
const searchInput = document.getElementById('searchInput');
const searchBtn = document.getElementById('searchBtn');

let currentPage = 1;

// Navbar scroll effect
window.addEventListener('scroll', () => {
  const navbar = document.querySelector('.navbar');
  if (window.scrollY > 50) {
    navbar.classList.add('scrolled');
  } else {
    navbar.classList.remove('scrolled');
  }
});

// Fetch Businesses
function fetchBusinesses(page = 1) {
  const searchTerm = searchInput.value.trim();
  let url = searchTerm
    ? `${baseApiUrl}?endpoint=search&name=${encodeURIComponent(searchTerm)}&page=${page}`
    : `${baseApiUrl}?endpoint=business&page=${page}`;

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
        businessList.innerHTML = `
          <div class="text-center mt-5 fade-in">
            <img src="images/iconEncourage.png" 
                 alt="No data" 
                 class="img-fluid mb-3" 
                 style="max-width: 200px; opacity: 0.8;">
            <h5 class="text-muted">No businesses found yet.</h5>
            <p class="text-secondary">Be the first to showcase your business! 🌟</p>
            <a href="" class="btn btn-primary mt-2">
              Post Your Business
            </a>
          </div>
        `;
        pagination.innerHTML = "";
      }
    })
    .catch(err => {
      console.error('Error fetching:', err);
      businessList.innerHTML = `
        <p class="text-center text-danger mt-5">
          Failed to load data.<br>
          (Server down or internet connection disrupted.)
        </p>`;
      pagination.innerHTML = "";
    });
}

// Render Business Cards
function renderBusinesses(businesses) {
  businessList.innerHTML = businesses.map(biz => `
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <div class="card h-100 shadow-sm border border-secondary-subtle rounded-4 d-flex flex-column">
        <img src="${biz.business_img ? biz.business_img_url : 'images/preview.png'}" 
             class="business-img" 
             alt="${biz.business_name}">
        <div class="card-body text-center">
          <h5 class="card-title">${biz.business_name}</h5>
          <div class="mt-auto">
            <a href="business-details.php?id=${biz.id}" 
               class="btn btn-primary btn-sm w-100">
               View Details
            </a>
          </div>
        </div>
      </div>
    </div>
  `).join('');
}  

// Render Pagination
function renderPagination(total, page, perPage) {
  const totalPages = Math.ceil(total / perPage);
  pagination.innerHTML = "";

  // Previous Button
  pagination.innerHTML += `
    <li class="page-item ${page === 1 ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${page - 1}" aria-label="Previous">
        &laquo; Prev
      </a>
    </li>
  `;

  // Page Number Buttons
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
      <a class="page-link" href="#" data-page="${page + 1}" aria-label="Next">
        Next &raquo;
      </a>
    </li>
  `;

  // Attach Click Events
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

// Press "Enter" to search
searchInput.addEventListener('keypress', (e) => {
  if (e.key === 'Enter') {
    e.preventDefault();
    currentPage = 1;
    fetchBusinesses(1);
  }
});

// Horizontal Scroll Controls
const highlightContainer = document.getElementById('highlightedContainer');
const scrollLeftBtn = document.getElementById('scrollLeftBtn');
const scrollRightBtn = document.getElementById('scrollRightBtn');

scrollLeftBtn.addEventListener('click', () => {
  highlightContainer.scrollBy({ left: -300, behavior: 'smooth' });
});

scrollRightBtn.addEventListener('click', () => {
  highlightContainer.scrollBy({ left: 300, behavior: 'smooth' });
});


// Initial Load
fetchBusinesses();
