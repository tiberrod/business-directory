const form = document.getElementById("updateBusinessForm");
const messageBox = document.getElementById("messageBox");
const apiUrl = "../../index.php?endpoint=business";

form.addEventListener("submit", async (e) => {
  e.preventDefault();

  const id = document.getElementById("businessId").value.trim();
  const name = document.getElementById("businessName").value.trim();
  const contact = document.getElementById("businessContact").value.trim();
  const description = document.getElementById("businessDescription").value.trim();
  const image = document.getElementById("businessImage").files[0];

  // Step 1: Validate ID
  if (!id) {
    showMessage("Please enter a business ID.", "error");
    return;
  }

  // Step 2: Check if at least one field is provided
  if (!name && !contact && !description && !image) {
    showMessage("Please fill at least one field to update.", "error");
    return;
  }

  // Step 3: Prepare form data
  const formData = new FormData();
  formData.append("_method", "PUT");
  formData.append("id", id);

  if (name) formData.append("name", name);
  if (contact) formData.append("contact", contact);
  if (description) formData.append("description", description);
  if (image) formData.append("business_img", image);

  try {
    // Disable submit button during submission
    const submitButton = form.querySelector('button[type="submit"]');
    if (submitButton) {
      submitButton.disabled = true;
      submitButton.textContent = "Updating...";
    }

    const response = await fetch(apiUrl, {
      method: "POST",
      body: formData
    });

    const result = await response.json();

    if (result.success || response.ok) {
      showMessage("Business updated successfully!", "success");
      // Reset form
      form.reset();
    } else {
      throw new Error(result.message || "Failed to update business");
    }

  } catch (error) {
    console.error("Error updating business:", error);
    showMessage("Error updating business: " + error.message, "error");
  } finally {
    // Re-enable submit button
    const submitButton = form.querySelector('button[type="submit"]');
    if (submitButton) {
      submitButton.disabled = false;
      submitButton.textContent = "Update Business";
    }
  }
});

function showMessage(msg, type) {
  messageBox.innerHTML = "";
  const alertClass = type === "success" ? "alert-success" : "alert-danger";
  messageBox.innerHTML = `<div class="alert ${alertClass}">${msg}</div>`;
  
  // Auto-hide message after 5 seconds
  setTimeout(() => {
    messageBox.innerHTML = "";
  }, 5000);
}