const viewDetailsApiBase = "https://apploqic.my/api/v1/business";

// Get business ID from URL
const params = new URLSearchParams(window.location.search);
const businessId = params.get('id');

// Format date helper function
const formatDate = (dateStr) => {
  if (!dateStr) return "N/A";
  const date = new Date(dateStr);
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  });
};

// Load business details
function loadBusinessDetails() {
  if (!businessId) {
    showError("Business ID is missing.");
    return;
  }

  const apiUrl = `${viewDetailsApiBase}/${businessId}`; // Final form of the endpoint

  fetch(apiUrl)
    .then(res => res.json())
    .then(data => {
      if (data.status !== "success" || !data.data) {
        showError("Business not found.");
        return;
      }

      const b = data.data;
      renderBusinessDetails(b);
    })
    .catch(err => {
      console.error("Error loading business details:", err);
      showError("Error loading business details.");
    });
}

// Render business details to the page
function renderBusinessDetails(business) {
  // Basic info
  document.getElementById("businessName").textContent = business.business_name || "Unknown Business";
  document.getElementById("businessImage").src = business.business_img_url || "https://placehold.co/100x100?text=No+Image";
  document.getElementById("businessContact").textContent = business.business_contact || "Not provided";
  document.getElementById("businessEmail").textContent = business.business_email || "Not provided";
  document.getElementById("businessCategory").textContent = business.business_category || "Uncategorized";
  document.getElementById("businessDescription").textContent = business.business_description || "No description available.";
  
  // Featured status
  document.getElementById("businessFeatured").textContent = business.is_featured == 1 ? "Yes" : "No";
  
  // Dates
  document.getElementById("businessCreated").textContent = formatDate(business.created_at);
  document.getElementById("businessUpdated").textContent = formatDate(business.updated_at);

  // Status badge
  const statusEl = document.getElementById("businessStatus");
  if (business.status == 1) {
    statusEl.textContent = "Active";
    statusEl.classList.add("status-active");
  } else {
    statusEl.textContent = "Inactive";
    statusEl.classList.add("status-inactive");
  }

  // Update quick action links
  updateQuickActions(business);

  // Update action buttons
  updateActionButtons();
}

// Update quick action links
function updateQuickActions(business) {
  const callLink = document.querySelector('.social-link[title="Call Business"]');
  const emailLink = document.querySelector('.social-link[title="Email Business"]');
  
  if (callLink && business.business_contact) {
    callLink.href = `tel:${business.business_contact}`;
  }
  
  if (emailLink && business.business_email) {
    emailLink.href = `mailto:${business.business_email}`;
  }
  
  // Share functionality
  const shareLink = document.querySelector('.social-link[title="Share"]');
  if (shareLink) {
    shareLink.addEventListener('click', (e) => {
      e.preventDefault();
      shareBusinessProfile(business);
    });
  }
}

// Update action buttons
function updateActionButtons() {
  // Edit button
  document.getElementById("editBtn").href = `update_business.php?id=${businessId}`;
  
  // Delete button
  document.getElementById("deleteBtn").addEventListener('click', (e) => {
    e.preventDefault();
    handleDeleteBusiness();
  });
}

// Handle business deletion
async function handleDeleteBusiness() {
  if (!confirm('Are you sure you want to delete this business? This action cannot be undone.')) {
    return;
  }

  try {
    const response = await fetch(`${viewDetailsApiBase}/${businessId}`, {
      method: 'DELETE'
    });

    const data = await response.json();

    if (response.ok && data.status === "success") {
      alert('Business deleted successfully!');
      window.location.href = 'admin_index.php';
    } else {
      alert(data.message || 'Failed to delete business.');
    }
  } catch (error) {
    console.error('Error deleting business:', error);
    alert('An error occurred while deleting the business.');
  }
}

// Share business profile
function shareBusinessProfile(business) {
  const shareData = {
    title: business.business_name,
    text: `Check out ${business.business_name} on our business directory!`,
    url: window.location.href
  };

  // Check if Web Share API is supported
  if (navigator.share) {
    navigator.share(shareData)
      .then(() => console.log('Shared successfully'))
      .catch(err => console.log('Error sharing:', err));
  } else {
    // Fallback: Copy to clipboard
    navigator.clipboard.writeText(window.location.href)
      .then(() => alert('Link copied to clipboard!'))
      .catch(() => alert('Unable to share. Please copy the URL manually.'));
  }
}

// Show error message
function showError(message) {
  document.querySelector('.profile-container').innerHTML = `
    <div class='error-message'>
      <i class='bi bi-exclamation-circle'></i>
      <p>${message}</p>
      <a href="admin_index.php" class="btn-back" style="margin-top: 1rem; display: inline-flex;">
        <i class="bi bi-arrow-left"></i> Back to Directory
      </a>
    </div>
  `;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
  loadBusinessDetails();
});