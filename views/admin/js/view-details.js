// =======================================
// VIEW BUSINESS DETAILS MODULE - V1 API Integration
// =======================================

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
  initializeViewDetails();
});

// Initialize view details functionality
function initializeViewDetails() {
  // Get business ID from URL parameters
  const urlParams = new URLSearchParams(window.location.search);
  const businessId = urlParams.get('id');
  
  if (!businessId) {
    showError('No business ID provided');
    setTimeout(() => {
      window.location.href = 'index.php';
    }, 2000);
    return;
  }

  // Check if API is available, wait if needed
  if (typeof window.adminAPI === 'undefined') {
    console.log('API not ready, retrying...');
    setTimeout(initializeViewDetails, 300);
    return;
  }

  // Load business details
  loadBusinessDetails(businessId);
}

// Load business details function with fallback
async function loadBusinessDetails(id) {
  try {
    console.log('Loading business details for ID:', id);
    
    // Show loading state
    showLoadingState();

    let response;
    try {
      // Try new API first
      response = await window.adminAPI.getBusiness(id);
    } catch (apiError) {
      console.warn('New API failed, trying fallback:', apiError);
      
      // Fallback to old API
      const fallbackUrl = `../../index.php?endpoint=business&id=${encodeURIComponent(id)}`;
      console.log('Fallback URL:', fallbackUrl);
      
      const fetchResponse = await fetch(fallbackUrl);
      response = await fetchResponse.json();
    }
    
    console.log('Business details response:', response);
    
    if (response.status === 'success' && response.data) {
      renderBusinessDetails(response.data);
    } else {
      throw new Error(response.message || 'Business not found');
    }
    
  } catch (error) {
    console.error("Error loading business details:", error);
    showError(`Failed to load business details: ${error.message}`);
  }
}

// Show loading state
function showLoadingState() {
  const container = document.querySelector('.details-container');
  if (container) {
    container.innerHTML = `
      <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <div class="mt-3">Loading business details...</div>
      </div>
    `;
  }
}

// Show error state
function showError(message) {
  const container = document.querySelector('.details-container');
  if (container) {
    container.innerHTML = `
      <div class="text-center py-5 text-danger">
        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
        <h4>Error</h4>
        <p>${message}</p>
        <a href="index.php" class="btn btn-primary">
          <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
      </div>
    `;
  }
}

// Render business details - optimized version
function renderBusinessDetails(business) {
  const container = document.querySelector('.details-container');
  if (!container) {
    console.error('Details container not found');
    return;
  }

  // Safely extract business data with fallbacks
  const id = business.id || business.business_id || 'N/A';
  const name = business.business_name || business.name || 'Unknown Business';
  const description = business.business_description || business.description || '';
  const category = business.business_category || business.category || 'Uncategorized';
  const address = business.business_address || business.address || '';
  const phone = business.business_phone || business.phone || '';
  const email = business.business_email || business.email || '';
  const website = business.business_website || business.website || '';
  const status = String(business.business_status || business.status || 'inactive');
  const featured = business.featured || business.is_featured || 0;
  const imageUrl = business.image_url || business.business_image || business.business_img_url || business.business_img || '';
  
  const isActive = status.toLowerCase() === 'active';
  const isFeatured = featured === '1' || featured === 1 || featured === true;

  // Update page title and header
  document.title = name + ' - Business Details';
  const headerTitle = document.querySelector('.admin-header h4');
  if (headerTitle) {
    headerTitle.textContent = 'Business Details - ' + name;
  }

  // Build HTML content
  const htmlContent = buildBusinessHTML(business, {
    id, name, description, category, address, phone, email, website,
    isActive, isFeatured, imageUrl
  });

  // Inject content and add event listeners
  container.innerHTML = htmlContent;
  
  // Add event listeners for action buttons
  setupActionButtons(id, isActive);
}

// Build business HTML content
function buildBusinessHTML(business, data) {
  const { id, name, description, category, address, phone, email, website, isActive, isFeatured, imageUrl } = data;
  
  return `
    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="business-image-container">
          <img src="${imageUrl || '../../public/assets/preview.png'}" 
               alt="${name}" 
               class="img-fluid rounded business-image"
               style="width: 100%; max-height: 300px; object-fit: cover; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        </div>
      </div>
      <div class="col-md-8">
        <div class="business-info">
          <h2 class="business-title mb-3" style="color: #2c3e50; font-weight: 600;">${name}</h2>
          
          <div class="row mb-3">
            <div class="col-sm-6">
              <span class="badge ${isActive ? 'bg-success' : 'bg-danger'} fs-6 mb-2">
                ${data.status || 'inactive'}
              </span>
              ${isFeatured ? '<span class="badge bg-warning text-dark ms-2">FEATURED</span>' : ''}
            </div>
            <div class="col-sm-6 text-end">
              <div class="action-buttons" id="actionButtons">
                <a href="update-business.php?id=${id}" class="btn btn-warning btn-sm me-2">
                  <i class="fas fa-edit"></i> Edit
                </a>
                ${!isActive ? 
                  `<button class="btn btn-success btn-sm me-2" data-action="reactivate" data-id="${id}">
                    <i class="fas fa-power-off"></i> Reactivate
                  </button>` : 
                  `<button class="btn btn-danger btn-sm me-2" data-action="delete" data-id="${id}">
                    <i class="fas fa-trash"></i> Delete
                  </button>`
                }
              </div>
            </div>
          </div>

          <div class="business-details">
            <div class="row">
              <div class="col-md-12 mb-3">
                <h5 style="color: #34495e;"><i class="fas fa-info-circle me-2"></i>Description</h5>
                <p class="text-muted">${description || 'No description available'}</p>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <h6 style="color: #34495e;"><i class="fas fa-tag me-2"></i>Category</h6>
                <p>${category}</p>
              </div>
              <div class="col-md-6 mb-3">
                <h6 style="color: #34495e;"><i class="fas fa-map-marker-alt me-2"></i>Address</h6>
                <p>${address || 'No address provided'}</p>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <h6 style="color: #34495e;"><i class="fas fa-phone me-2"></i>Phone</h6>
                <p>${phone ? '<a href="tel:' + phone + '">' + phone + '</a>' : 'No phone provided'}</p>
              </div>
              <div class="col-md-6 mb-3">
                <h6 style="color: #34495e;"><i class="fas fa-envelope me-2"></i>Email</h6>
                <p>${email ? '<a href="mailto:' + email + '">' + email + '</a>' : 'No email provided'}</p>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <h6 style="color: #34495e;"><i class="fas fa-globe me-2"></i>Website</h6>
                <p>${website ? '<a href="' + website + '" target="_blank">' + website + '</a>' : 'No website provided'}</p>
              </div>
              <div class="col-md-6 mb-3">
                <h6 style="color: #34495e;"><i class="fas fa-id-card me-2"></i>Business ID</h6>
                <p><code>${id}</code></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;
}

// Setup action button event listeners
function setupActionButtons(id, isActive) {
  const actionButtons = document.getElementById('actionButtons');
  if (!actionButtons) return;

  actionButtons.addEventListener('click', async (e) => {
    const button = e.target.closest('button');
    if (!button) return;

    const action = button.getAttribute('data-action');
    const businessId = button.getAttribute('data-id');

    if (action === 'delete' && isActive) {
      await handleDeleteBusiness(businessId);
    } else if (action === 'reactivate' && !isActive) {
      await handleReactivateBusiness(businessId);
    }
  });
}

// Delete business function
async function handleDeleteBusiness(id) {
  if (!confirm('Are you sure you want to delete this business?')) {
    return;
  }

  try {
    const response = await window.adminAPI.deleteBusiness(id);
    
    if (response.status === 'success') {
      window.adminAPI.showMessage('Business deleted successfully', 'success');
      setTimeout(() => {
        window.location.href = 'index.php';
      }, 1500);
    } else {
      throw new Error(response.message || 'Failed to delete business');
    }
  } catch (error) {
    console.error('Error deleting business:', error);
    window.adminAPI.showMessage('Failed to delete business', 'error');
  }
}

// Reactivate business function  
async function handleReactivateBusiness(id) {
  if (!confirm('Are you sure you want to reactivate this business?')) {
    return;
  }

  try {
    const response = await window.adminAPI.reactivateBusiness(id);
    
    if (response.status === 'success') {
      window.adminAPI.showMessage('Business reactivated successfully', 'success');
      setTimeout(() => {
        location.reload();
      }, 1500);
    } else {
      throw new Error(response.message || 'Failed to reactivate business');
    }
  } catch (error) {
    console.error('Error reactivating business:', error);
    window.adminAPI.showMessage('Failed to reactivate business', 'error');
  }
}