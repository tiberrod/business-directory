const form = document.getElementById("updateBusinessForm");
const messageBox = document.getElementById("messageBox");
const apiUrl = "https://apploqic.my/index.php?endpoint=business";

form.addEventListener("submit", async (e) => {
  e.preventDefault();

  const id = document.getElementById("businessId").value.trim();
  const name = document.getElementById("businessName").value.trim();
  const contact = document.getElementById("businessContact").value.trim();
  const description = document.getElementById("businessDescription").value.trim();
  const image = document.getElementById("businessImage").files[0];

  // Step 1: Validate ID
  if (!id) {
    showMessage(" Please enter a business ID.", "error");
    return;
  }

  // Step 2: Check if at least one field is provided
  if (!name && !contact && !description && !image) {
    showMessage(" Please fill at least one field to update.", "error");
    return;
  }

    // Step 3: Prepare form data
    // Instead of: method: "PUT"
    const formData = new FormData();
    formData.append("_method", "PUT");
    formData.append("id", id);
    if (name) formData.append("name", name);
    if (contact) formData.append("contact", contact);
    if (description) formData.append("description", description);
    if (image) formData.append("business_img", image);

    try {
    const response = await fetch(apiUrl, {
        method: "POST",
        body: formData,
    });

    const result = await response.json();
    console.log("Update response:", result);

    if (result.status === 200) {
        showMessage(`✅ ${result.message}`, "success");
    } else {
        showMessage(`⚠️ ${result.message || "Failed to update business."}`, "error");
    }
    } catch (error) {
    console.error("Error updating business:", error);
    showMessage("⚠️ Network or server error occurred.", "error");
    }
});

function showMessage(text, type) {
  messageBox.textContent = text;
  messageBox.style.padding = "10px";
  messageBox.style.borderRadius = "5px";
  messageBox.style.color = type === "success" ? "green" : "red";
}
