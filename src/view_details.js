// ==============================
// Fetch Business Details Script
// ==============================

// 1. Extract ID from URL
function getBusinessIdFromURL() {
  const params = new URLSearchParams(window.location.search);
  return params.get("id");
}

// 2. Fetch business details from API
async function getBusinessDetails(businessId) {
  const API_URL = `https://apploqic.my/api/v1/business/${businessId}`;

  try {
    const response = await fetch(API_URL);
    if (!response.ok) throw new Error("API request failed");

    const result = await response.json();
    if (result.status !== "success") throw new Error("Invalid API response");

    return result.data;
  } catch (error) {
    console.error("Error fetching business details:", error);
    return null;
  }
}

// 3. Render data into the two separate cards
function renderBusinessDetails(data) {
  if (!data) {
    document.querySelector(".profile-container").innerHTML =
      "<p>Failed to load business details.</p>";
    return;
  }

  // ==============================
  // Profile Card (left)
  // ==============================
  document.getElementById("businessId").textContent = data.id;
  document.getElementById("businessImage").src = data.business_img_url;
  document.getElementById("businessName").textContent = data.business_name;
  document.getElementById("businessCategory").textContent = data.business_category;

  // Status badge → "Active" or "Inactive"
  const statusBadge = document.getElementById("businessStatus");
  if (data.status == 1) {
    statusBadge.textContent = "Active";
    statusBadge.classList.add("active-badge");
  } else {
    statusBadge.textContent = "Inactive";
    statusBadge.classList.add("inactive-badge");
  }

  // Fallback if image fails
  document.getElementById("businessImage").onerror = function () {
    this.src = "https://placehold.co/100x100?text=No+Image";
  };

  // ==============================
  // Details Card (right)
  // ==============================
  document.getElementById("businessContact").textContent = data.business_contact || "-";

  // Featured status
  document.getElementById("businessFeatured").textContent =
    data.is_featured == 1 ? "Featured Business" : "Not Featured";

  // Created & Updated dates
  document.getElementById("businessCreated").textContent =
    data.created_at ? formatDate(data.created_at) : "-";

  document.getElementById("businessUpdated").textContent =
    data.updated_at ? formatDate(data.updated_at) : "-";

  // Description
  document.getElementById("businessDescription").textContent =
    data.business_description || "No description available.";

  // Update Edit/Delete button links (if needed later)
  document.getElementById("editBtn").href = `edit_business.php?id=${data.id}`;
  document.getElementById("deleteBtn").setAttribute("data-id", data.id);
}

// Helper: Format date (YYYY-MM-DD HH:mm → DD MMM YYYY)
function formatDate(dateStr) {
  const date = new Date(dateStr);
  return date.toLocaleDateString("en-MY", {
    year: "numeric",
    month: "short",
    day: "numeric"
  });
}

// 4. Initialize page
async function init() {
  const businessId = getBusinessIdFromURL();

  if (!businessId) {
    alert("No business ID provided in the URL.");
    return;
  }

  const details = await getBusinessDetails(businessId);
  renderBusinessDetails(details);
}

init();
