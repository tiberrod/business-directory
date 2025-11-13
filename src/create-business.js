// =======================================
// CREATE BUSINESS MODULE
// =======================================

// API endpoint for creating business
const createApiBase = "https://apploqic.my/index.php/api/v1/business";

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

    submitBtn.disabled = true;
    submitBtn.textContent = "Submitting...";

    try {
      const formData = new FormData(createForm);

      // Optional validation
      const requiredFields = ["business_name", "business_contact", "business_description", "business_category"];
      for (const field of requiredFields) {
        if (!formData.get(field)) {
          alert(` Please fill in the ${field.replace("_", " ")} field.`);
          submitBtn.disabled = false;
          submitBtn.textContent = "Submit";
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

      if (response.ok && data.status === "success") {
        alert(" Business created successfully!");
        createForm.reset();
        if (imgPreview) imgPreview.style.display = "none";
        // Redirect or stay on page
        window.location.href = "index.php"; // optional redirect
      } else {
        alert(" Failed to create business: " + (data.message || "Unknown error"));
      }
    } catch (error) {
      console.error("Error creating business:", error);
      alert(" An error occurred. Check console for details.");
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = "Submit";
    }
  });
});
