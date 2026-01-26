// =======================================
// CREATE BUSINESS MODULE
// =======================================

// API endpoint for creating business
const createApiBase = "../../index.php";

document.addEventListener("DOMContentLoaded", () => {
  const createForm = document.getElementById("createBusinessForm");
  if (!createForm) return;

  const submitBtn = createForm.querySelector(".btn-submit");
  const imgInput = document.getElementById("business_img");
  const imgPreview = document.getElementById("imgPreview");

  // ===== Image Preview =====
  if (imgInput && imgPreview) {
    imgInput.addEventListener("change", (e) => {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          imgPreview.src = e.target.result;
          imgPreview.style.display = "block";
        };
        reader.readAsDataURL(file);
      } else {
        imgPreview.style.display = "none";
      }
    });
  }

  // ===== Form Submission =====
  createForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    
    try {
      // Disable submit button during submission
      const submitButton = createForm.querySelector('button[type="submit"]');
      if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = "Creating...";
      }

      const formData = new FormData(createForm);
      
      const response = await fetch(`${createApiBase}?endpoint=business`, {
        method: "POST",
        body: formData
      });

      const result = await response.json();

      if (result.success || response.ok) {
        alert("Business created successfully!");
        // Reset form
        createForm.reset();
        if (imgPreview) {
          imgPreview.style.display = "none";
        }
        // Optionally redirect to admin dashboard
        // window.location.href = "index.php";
      } else {
        throw new Error(result.message || "Failed to create business");
      }

    } catch (error) {
      console.error("Error creating business:", error);
      alert("Error creating business: " + error.message);
    } finally {
      // Re-enable submit button
      const submitButton = createForm.querySelector('button[type="submit"]');
      if (submitButton) {
        submitButton.disabled = false;
        submitButton.textContent = "Submit";
      }
    }
  });

  // ===== Form Validation =====
  const requiredFields = createForm.querySelectorAll("input[required], textarea[required]");
  requiredFields.forEach(field => {
    field.addEventListener("blur", validateField);
    field.addEventListener("input", clearValidationError);
  });

  function validateField(e) {
    const field = e.target;
    if (!field.value.trim()) {
      showFieldError(field, `${field.name.replace('_', ' ')} is required`);
    }
  }

  function clearValidationError(e) {
    const field = e.target;
    clearFieldError(field);
  }

  function showFieldError(field, message) {
    clearFieldError(field);
    field.classList.add("is-invalid");
    const errorDiv = document.createElement("div");
    errorDiv.className = "invalid-feedback";
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
  }

  function clearFieldError(field) {
    field.classList.remove("is-invalid");
    const errorFeedback = field.parentNode.querySelector(".invalid-feedback");
    if (errorFeedback) {
      errorFeedback.remove();
    }
  }
});