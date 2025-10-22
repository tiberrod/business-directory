// create-business.js
const form = document.getElementById("createBusinessForm");
const messageBox = document.getElementById("messageBox");
const apiUrl = "https://apploqic.my/index.php?endpoint=business";

form.addEventListener("submit", async (e) => {
  e.preventDefault();

  // Get values
  const name = document.getElementById("businessName").value.trim();
  const contact = document.getElementById("businessContact").value.trim();

  // Basic validation
  if (!name || !contact) {
    showMessage("❌ Please fill in both business name and contact.", "error");
    return;
  }

  // Prepare form data
  const formData = new FormData(form);

  try {
    const response = await fetch(apiUrl, {
      method: "POST",
      body: formData
    });

    const result = await response.json();
    console.log(result);

    if (result.status === 201) {
      showMessage(`✅ ${result.message} (ID: ${result.id})`, "success");

      // Optionally reset the form
      form.reset();

      // Optional redirect back to listing after short delay
      setTimeout(() => {
        window.location.href = "index.php";
      }, 1500);

    } else {
      showMessage(`⚠️ ${result.message || "Failed to create business."}`, "error");
    }

  } catch (err) {
    console.error("Error creating business:", err);
    showMessage("⚠️ Network or server error occurred.", "error");
  }
});

function showMessage(text, type) {
  messageBox.textContent = text;
  messageBox.style.padding = "10px";
  messageBox.style.borderRadius = "5px";
  messageBox.style.color = type === "success" ? "green" : "red";
}
