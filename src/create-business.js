$(document).ready(function() {
  const apiUrl = "https://apploqic.my/index.php?endpoint=business";
  const $form = $("#createBusinessForm");
  const $messageBox = $("#messageBox");

  $form.on("submit", function(e) {
    e.preventDefault();

    const name = $("#businessName").val().trim();
    const contact = $("#businessContact").val().trim();

    if (!name || !contact) {
      showMessage("❌ Please fill in both Business Name and Contact.", "error");
      return;
    }

    // Prepare FormData
    const formData = new FormData(this);

    // Ensure default values for optional fields if not selected
    if (!formData.get("status")) formData.set("status", "1");
    if (!formData.get("is_featured")) formData.set("is_featured", "0");
    // ensure also 'featured' key exists (API may expect 'featured' or 'is_featured')
    if (!formData.get("featured")) formData.set("featured", formData.get("is_featured"));

    // If a file input exists, duplicate it under common keys so backend/listing can return either name
    const fileInput = $form.find('input[type="file"]')[0];
    if (fileInput && fileInput.files && fileInput.files[0]) {
      const file = fileInput.files[0];
      if (!formData.get("business_img")) formData.set("business_img", file);
      if (!formData.get("image")) formData.set("image", file);
    }
    
    $.ajax({
      url: apiUrl,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function(result) {
        console.log("Create response:", result); // inspect keys returned by API

        if (result.status === 201) {
          showMessage(`✅ ${result.message} (ID: ${result.id})`, "success");
          $form.trigger("reset");
        } else {
          showMessage(`⚠️ ${result.message || "Failed to create business."}`, "error");
        }
      },
      error: function(xhr, status, error) {
        console.error("Error:", error);
        showMessage("❌ Network or server error occurred.", "error");
      }
    });
  });

  // Display messages
  function showMessage(text, type) {
    $messageBox
      .hide()
      .html(`<div class="alert ${type === "success" ? "alert-success" : "alert-danger"}">${text}</div>`)
      .fadeIn(300);
  }
});
