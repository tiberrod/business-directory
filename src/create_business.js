// create_business.js

const API_URL = "https://apploqic.my/api/v1/business";

/* ------------------------------------------------------
   Image Preview Handling
--------------------------------------------------------- */
const imageInput = document.getElementById("business_img");
const imgPreview = document.getElementById("imgPreview");
const uploadPlaceholder = document.getElementById("uploadPlaceholder");

imageInput.addEventListener("change", function () {
  const file = this.files[0];

  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      uploadPlaceholder.style.display = "none";
      imgPreview.style.display = "block";
      imgPreview.src = e.target.result;
    };
    reader.readAsDataURL(file);
  } else {
    // Reset to placeholder
    imgPreview.style.display = "none";
    uploadPlaceholder.style.display = "flex";
  }
});

/* ------------------------------------------------------
   Handle Form Submission (Create Business)
--------------------------------------------------------- */
document.getElementById("createBusinessForm").addEventListener("submit", async function (e) {
  e.preventDefault();

  // Required fields
  const business_name = document.getElementById("business_name").value.trim();
  const business_contact = document.getElementById("business_contact").value.trim();
  const business_category = document.getElementById("business_category").value;
  const business_description = document.getElementById("business_description").value.trim();
  const is_featured = document.getElementById("is_featured").value;
  const business_img = document.getElementById("business_img").files[0];

  // Validate required
  if (!business_name || !business_contact || !business_category) {
    Swal.fire({
      icon: "warning",
      title: "Missing Required Fields",
      text: "Please fill in business name, contact number and category.",
    });
    return;
  }

  try {
    const formData = new FormData();
    formData.append("business_name", business_name);
    formData.append("business_contact", business_contact);
    formData.append("business_category", business_category);
    formData.append("business_description", business_description);
    formData.append("is_featured", is_featured);

    if (business_img) {
      formData.append("business_img", business_img);
    }

    // Show loading button state
    const submitBtn = document.querySelector(".btn-submit");
    submitBtn.disabled = true;
    submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Creating...`;

    const response = await fetch(API_URL, {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (response.ok && result.status === "success") {
      Swal.fire({
        icon: "success",
        title: "Business Created Successfully!",
        text: "The new business has been added to the directory.",
        timer: 2000,
        showConfirmButton: false,
      });

      setTimeout(() => {
        window.location.href = "../src/admin_index.php"; // Redirect to dashboard
      }, 2000);
    } else {
      throw new Error(result.message || "Failed to create business.");
    }

  } catch (err) {
    Swal.fire({
      icon: "error",
      title: "Error Creating Business",
      text: err.message,
    });
  } finally {
    const submitBtn = document.querySelector(".btn-submit");
    submitBtn.disabled = false;
    submitBtn.innerHTML = `<i class="bi bi-check-circle me-2"></i> Create Business`;
  }
});
