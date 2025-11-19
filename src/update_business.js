// ===============================
// Get Business ID from URL
// ===============================
const urlParams = new URLSearchParams(window.location.search);
const businessId = urlParams.get("id");

if (!businessId) {
  Swal.fire({
    icon: "error",
    title: "Missing Business ID",
    text: "No business ID was provided in the URL.",
  });
  throw new Error("Business ID missing in URL");
}

// ===============================
// DOM Elements
// ===============================
const businessName = document.getElementById("business_name");
const businessContact = document.getElementById("business_contact");
const businessCategory = document.getElementById("business_category");
const businessDescription = document.getElementById("business_description");
const isFeatured = document.getElementById("is_featured");
const imgInput = document.getElementById("business_img");
const imgPreview = document.getElementById("imgPreview");
const uploadPlaceholder = document.getElementById("uploadPlaceholder");

// ===============================
// Load Existing Business Data
// ===============================
async function loadBusinessData() {
  try {
    const response = await fetch(`https://apploqic.my/api/v1/business/${businessId}`);
    const result = await response.json();

    if (result.status !== "success") throw new Error("Failed to load business details.");

    const data = result.data;

    businessName.value = data.business_name || "";
    businessContact.value = data.business_contact || "";
    businessCategory.value = data.business_category || "";
    businessDescription.value = data.business_description || "";
    isFeatured.value = data.is_featured;

    if (data.business_img_url) {
      imgPreview.src = data.business_img_url;
      imgPreview.style.display = "block";
      uploadPlaceholder.style.display = "none";
    }
  } catch (error) {
    console.error("Load error:", error);
    Swal.fire({
      icon: "error",
      title: "Error Loading Business",
      text: "Unable to load business details. Please check the ID.",
    });
  }
}

loadBusinessData();

// ===============================
// Image Preview
// ===============================
imgInput.addEventListener("change", function () {
  if (this.files && this.files[0]) {
    const reader = new FileReader();
    reader.onload = function (e) {
      imgPreview.src = e.target.result;
      imgPreview.style.display = "block";
      uploadPlaceholder.style.display = "none";
    };
    reader.readAsDataURL(this.files[0]);
  }
});

// ===============================
// Submit Update Form
// ===============================
document.getElementById("createBusinessForm").addEventListener("submit", async function (e) {
  e.preventDefault();

  const formData = new FormData();

  // Append only fields that have been filled
  if (businessName.value.trim() !== "") formData.append("business_name", businessName.value);
  if (businessContact.value.trim() !== "") formData.append("business_contact", businessContact.value);
  if (businessCategory.value.trim() !== "") formData.append("business_category", businessCategory.value);
  if (businessDescription.value.trim() !== "") formData.append("business_description", businessDescription.value);
  if (isFeatured.value !== "") formData.append("is_featured", isFeatured.value);

  if (imgInput.files.length > 0) formData.append("business_img", imgInput.files[0]);

  // For PHP backends: emulate PUT using POST
  formData.append("_method", "PUT");

  try {
    const response = await fetch(`https://apploqic.my/api/v1/business/${businessId}`, {
      method: "POST", // POST for file upload
      body: formData,
    });

    let result;
    try {
      result = await response.json();
    } catch {
      const text = await response.text();
      console.error("Raw response:", text);
      Swal.fire({
        icon: "error",
        title: "Update Failed",
        text: "Server returned non-JSON response. Check console for details.",
      });
      return;
    }

    console.log("API Response:", result);

    if (result.status === "success") {
      Swal.fire({
        icon: "success",
        title: "Business Updated",
        text: "The business information has been successfully updated.",
      }).then(() => {
        window.location.href = "../src/admin_index.php";
      });
    } else {
      // Show exact API error
      const message = result.message || (result.error && result.error.message) || "Unknown error";
      Swal.fire({ icon: "error", title: "Update Failed", text: message });
    }
  } catch (error) {
    console.error("Network error:", error);
    Swal.fire({
      icon: "error",
      title: "Network Error",
      text: "Unable to update business. Please try again later.",
    });
  }
});
