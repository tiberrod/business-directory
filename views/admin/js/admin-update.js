// =======================================
// UPDATE BUSINESS MODULE - V1 API Integration
// =======================================

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("updateBusinessForm");
  const messageBox = document.getElementById("messageBox");
  if (!form) return;

  // Get business ID from URL parameters
  const urlParams = new URLSearchParams(window.location.search);
  const businessId = urlParams.get('id');
  
  // Auto-populate ID field if present
  const businessIdField = document.getElementById("businessId");
  if (businessIdField && businessId) {
    businessIdField.value = businessId;
    businessIdField.readOnly = true; // Make it read-only since it's auto-populated
    
    // Load business data for editing
    loadBusinessForEdit(businessId);
  }

  // Handle form submission
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const id = document.getElementById("businessId").value.trim();
    const name = document.getElementById("businessName").value.trim();
    const contact = document.getElementById("businessContact").value.trim();
    const category = document.getElementById("businessCategory")?.value.trim() || "";
    const description = document.getElementById("businessDescription").value.trim();
    const image = document.getElementById("businessImage").files[0];

    // Validation
    if (!id) {
      showMessage("Business ID is required.", "error");
      return;
    }

    // Check if at least one field is provided for update
    if (!name && !contact && !description && !image && !category) {
      showMessage("Please fill at least one field to update.", "error");
      return;
    }

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';

    try {
      // Prepare form data
      const formData = new FormData();
      if (name) formData.append("name", name);
      if (contact) formData.append("contact", contact);
      if (category) formData.append("category", category);
      if (description) formData.append("description", description);
      if (image) formData.append("business_img", image);

      // Update business using API
      const response = await window.adminAPI.updateBusiness(id, formData);
      
      if (response.status === "success") {
        showMessage(`✅ ${response.message}`, "success");
        
        // Redirect to admin dashboard after short delay
        setTimeout(() => {
          window.location.href = 'index.php';
        }, 2000);
      } else {
        showMessage(`⚠️ ${response.message || "Failed to update business."}`, "error");
      }
      
    } catch (error) {
      console.error("Error updating business:", error);
      showMessage(
        `⚠️ ${window.adminAPI.handleError(error, "Updating business")}`, 
        "error"
      );
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
    }
  });

  // Load business data for editing
  async function loadBusinessForEdit(id) {
    try {
      const response = await window.adminAPI.getBusiness(id);
      
      if (response.status === 'success' && response.data) {
        const business = response.data;
        
        // Populate form fields with existing data
        const nameField = document.getElementById("businessName");
        const contactField = document.getElementById("businessContact");
        const categoryField = document.getElementById("businessCategory");
        const descriptionField = document.getElementById("businessDescription");
        
        if (nameField) nameField.value = business.name || business.business_name || '';
        if (contactField) contactField.value = business.contact || business.business_contact || '';
        if (categoryField) categoryField.value = business.category || business.business_category || '';
        if (descriptionField) descriptionField.value = business.description || business.business_description || '';
        
        // Show current image if available
        const currentImage = business.image_url || business.business_image || business.business_img_url || business.business_img;
        if (currentImage) {
          const imagePreview = document.createElement('div');
          imagePreview.className = 'mb-3';
          imagePreview.innerHTML = `
            <label class="form-label">Current Image:</label><br>
            <img src="${currentImage}" alt="Current business image" style="max-width: 200px; max-height: 200px; object-fit: cover; border-radius: 8px;">
          `;
          
          const imageInput = document.getElementById("businessImage");
          if (imageInput && imageInput.parentNode) {
            imageInput.parentNode.insertBefore(imagePreview, imageInput);
          }
        }
        
        showMessage(`Loaded data for: ${business.name || business.business_name}`, 'info');
        
      } else {
        showMessage('Business not found or error loading data', 'error');
      }
      
    } catch (error) {
      console.error("Error loading business for edit:", error);
      showMessage(
        window.adminAPI.handleError(error, 'Loading business data'), 
        'error'
      );
    }
  }

  // Message display function
  function showMessage(message, type) {
    if (messageBox) {
      messageBox.className = `alert alert-${type === "error" ? "danger" : type === "success" ? "success" : "info"}`;
      messageBox.textContent = message;
      messageBox.style.display = "block";
      
      // Auto-hide after 5 seconds
      setTimeout(() => {
        messageBox.style.display = "none";
      }, 5000);
    } else {
      // Fallback to adminAPI message system
      window.adminAPI.showMessage(message, type);
    }
  }
});