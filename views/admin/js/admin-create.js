// =======================================
// CREATE BUSINESS MODULE - V1 API Integration
// =======================================

document.addEventListener("DOMContentLoaded", () => {
  const createForm = document.getElementById("createBusinessForm");
  if (!createForm) return;

  const imgInput = document.getElementById("business_img");
  const imgPreview = document.getElementById("imgPreview");

  // ===== Image Preview =====
  if (imgInput && imgPreview) {
    imgInput.addEventListener("change", (e) => {
      const file = e.target.files[0];
      if (file) {
        imgPreview.src = URL.createObjectURL(file);
        imgPreview.style.display = "block";
      } else {
        imgPreview.style.display = "none";
      }
    });
  }

  // ===== Handle Form Submit =====
  createForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const submitButton = createForm.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';

    try {
      const formData = new FormData(createForm);

      // Validation
      const requiredFields = [
        { field: "business_name", label: "Business Name" },
        { field: "business_contact", label: "Business Contact" },
        { field: "business_description", label: "Business Description" },
        { field: "business_category", label: "Business Category" }
      ];

      for (const { field, label } of requiredFields) {
        if (!formData.get(field) || formData.get(field).trim() === '') {
          window.adminAPI.showMessage(`Please fill in the ${label} field.`, 'error');
          submitButton.disabled = false;
          submitButton.textContent = originalText;
          return;
        }
      }

      // Create business using API
      const response = await window.adminAPI.createBusiness(formData);
      
      if (response.status === 'success') {
        window.adminAPI.showMessage(
          `✅ Business "${formData.get('business_name')}" created successfully!`, 
          'success'
        );
        
        // Reset form
        createForm.reset();
        if (imgPreview) {
          imgPreview.style.display = "none";
        }
        
        // Redirect to admin dashboard after short delay
        setTimeout(() => {
          window.location.href = 'index.php';
        }, 2000);
        
      } else {
        window.adminAPI.showMessage(
          response.message || 'Failed to create business', 
          'error'
        );
      }

    } catch (error) {
      console.error("Create business error:", error);
      window.adminAPI.showMessage(
        window.adminAPI.handleError(error, 'Creating business'), 
        'error'
      );
    } finally {
      submitButton.disabled = false;
      submitButton.textContent = originalText;
    }
  });
});