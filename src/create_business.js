// =======================================
// CREATE BUSINESS MODULE
// =======================================

// API endpoint for creating business
const createApiBase = "https://apploqic.my/api/v1/business";

document.addEventListener("DOMContentLoaded", () => {
  const createForm = document.getElementById("createBusinessForm");
  if (!createForm) return;

  const submitBtn = createForm.querySelector('button[type="submit"]');
  const imgInput = document.getElementById("business_img");
  const imgPreview = document.getElementById("imgPreview");
  const uploadPlaceholder = document.getElementById("uploadPlaceholder");

  // Image Preview with placeholder toggle
  if (imgInput && imgPreview && uploadPlaceholder) {
    imgInput.addEventListener("change", (e) => {
      const file = e.target.files && e.target.files[0];
      if (file) {
        // Hide placeholder, show preview
        uploadPlaceholder.style.display = "none";
        
        const objectUrl = URL.createObjectURL(file);
        imgPreview.src = objectUrl;
        imgPreview.style.display = "block";
        
        imgPreview.onload = () => {
          URL.revokeObjectURL(objectUrl); 
        };
      } else {
        // Show placeholder, hide preview
        uploadPlaceholder.style.display = "flex";
        imgPreview.src = "#";
        imgPreview.style.display = "none";
      }
    });

    // Click on preview to change image
    imgPreview.addEventListener("click", () => {
      imgInput.click();
    });
  }

  // Handle Form Submit
  createForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
    }

    try {
      const formData = new FormData();
      
      // Add all form fields to FormData
      formData.append("business_name", document.getElementById("business_name").value);
      formData.append("business_contact", document.getElementById("business_contact").value);
      formData.append("business_email", document.getElementById("business_email").value);
      formData.append("business_category", document.getElementById("business_category").value);
      formData.append("business_description", document.getElementById("business_description").value);
      formData.append("is_featured", document.getElementById("is_featured").value);
      
      // Add image if selected
      if (imgInput.files[0]) {
        formData.append("business_img", imgInput.files[0]);
      }

      // Validate required fields
      const requiredFields = [
        { field: "business_name", name: "Business Name" },
        { field: "business_contact", name: "Contact Number" },
        { field: "business_category", name: "Category" },
        { field: "business_description", name: "Description" }
      ];

      for (const field of requiredFields) {
        const value = formData.get(field.field);
        if (!value || String(value).trim() === "") {
          alert(`Please fill in the ${field.name}`);
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i> Create Business';
          }
          return;
        }
      }

      // POST request
      const response = await fetch(createApiBase, {
        method: "POST",
        body: formData
      });

      const data = await response.json();
      console.log("Create API Response:", data);

      if (response.ok && (data.status === "success" || data.status === "created")) {
        // Success - show message and redirect
        alert("Business created successfully!");
        
        // Redirect to admin dashboard after 1 second
        setTimeout(() => {
          window.location.href = "admin_index.php";
        }, 1000);
      } else {
        const msg = (data && (data.message || data.error)) ? (data.message || data.error) : "Failed to create business";
        alert(msg);
      }
    } catch (error) {
      console.error("Error creating business:", error);
      alert("An error occurred. Please check your connection and try again.");
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i> Create Business';
      }
    }
  });
});
