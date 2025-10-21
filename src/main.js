// Initialize apiBase and searchEndpoint
const apiBase = "https://apploqic.my/index.php?endpoint=business";
const searchEndpoint = "https://apploqic.my/index.php?endpoint=search&name=";

let allBusinesses = [];
let currentPage = 1;
const perPage = 10;

// Elements
const businessContainer = document.getElementById("businessContainer");
const paginationContainer = document.getElementById("paginationContainer");
const searchInput = document.getElementById("searchInput");
const searchBtn = document.getElementById("searchBtn");

// Modal elements
const modal = document.getElementById("detailsModal");
const modalTitle = document.getElementById("modalTitle");
const modalImage = document.getElementById("modalImage");
const modalContact = document.getElementById("modalContact");
const modalDescription = document.getElementById("modalDescription");
const modalCreated = document.getElementById("modalCreated");
const modalUpdated = document.getElementById("modalUpdated");
const modalClose = document.querySelector(".close");

// Load Businesses
function loadBusinesses(searchTerm = "", page = 1) {
  let url = "";

  if (searchTerm) {
    url = `${searchEndpoint}${encodeURIComponent(searchTerm)}&page=${page}`;
  } else {
    url = `${apiBase}&page=${page}`;
  }

  fetch(url)
    .then(res => res.json())
    .then(data => {
      console.log("API response:", data);

      // get the data array
      allBusinesses = data.businesses || data.data || [];
      currentPage = page;

      // get total pages from API (fallback if not available)
      const totalPages = data.total_pages || Math.ceil(data.total_results / (data.results_per_page || 10));
      renderPage(allBusinesses, totalPages);
    })
    .catch(err => {
      console.error("Error fetching businesses:", err);
    });
}

// Render a single page of businesses
function renderPage(pageBusinesses, totalPages = 1) {
  businessContainer.innerHTML = "";

  if (!pageBusinesses.length) {
    businessContainer.innerHTML = "<p>No businesses found.</p>";
    paginationContainer.innerHTML = "";
    return;
  }

  // Render each business card
  pageBusinesses.forEach(b => {
    const imgSrc = b.business_img
      ? `https://apploqic.my/images/${b.business_img}`
      : "https://via.placeholder.com/220x150?text=No+Image";

    const card = document.createElement("div");
    card.className = "card";
    card.innerHTML = `
      <img src="${imgSrc}" alt="${b.business_name}">
      <div class="card-body">
        <h3>${b.business_name}</h3>
        <button data-id="${b.id}">View Details</button>
      </div>
    `;
    businessContainer.appendChild(card);
  });

  // Setup pagination
  renderPagination(totalPages);
}

// Render pagination buttons
function renderPagination(totalPages) {
  paginationContainer.innerHTML = "";

  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement("button");
    btn.textContent = i;
    btn.classList.toggle("active", i === currentPage);
    btn.addEventListener("click", () => {
      const term = searchInput.value.trim();
      loadBusinesses(term, i); // fetch the next page directly from backend
    });
    paginationContainer.appendChild(btn);
  }
}


// Show Modal with Details
function showModal(business) {
  modalTitle.textContent = business.business_name;
  modalImage.src = business.business_img
    ? `https://apploqic.my/images/${business.business_img}`
    : "https://via.placeholder.com/400x200?text=No+Image";
  modalContact.textContent = business.business_contact || "-";
  modalDescription.textContent = business.business_description || "-";
  modalCreated.textContent = business.created_at || "-";
  modalUpdated.textContent = business.updated_at || "-";
  modal.style.display = "block";
}

// Close modal
modalClose.onclick = () => modal.style.display = "none";
window.onclick = (e) => { if (e.target === modal) modal.style.display = "none"; };

// 🧾 Event: View Details Button
businessContainer.addEventListener("click", (e) => {
  if (e.target.tagName === "BUTTON") {
    const id = e.target.dataset.id;
    const business = allBusinesses.find(b => b.id == id);
    if (business) showModal(business);
  }
});

// 🔍 Search button
searchBtn.addEventListener("click", () => {
  const term = searchInput.value.trim();
  loadBusinesses(term);
});

// Initial load
loadBusinesses("", 1);
