<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Business Details</title>

  <!-- Bootstrap and custom stylesheet -->
  <link rel="stylesheet" href="../css/style.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>

<body>
  <header class="admin-header">
    <h4 class="mb-0">Business Details</h4>
    <a href="../src/admin_index.php" class="btn btn-light">← Back to Directory</a>
  </header>

  <div class="container my-5">
    <div class="details-container card shadow-sm">
      <div class="row g-3 p-3">
        <div class="col-md-6">
          <img id="businessImage" src="https://placehold.co/400x200?text=Loading..." alt="Business Image" class="img-fluid rounded" />
        </div>
        <div class="col-md-6">
          <h2 id="businessName">Loading...</h2>
          <div class="meta">
            <p><strong>Category:</strong> <span id="businessCategory">-</span></p>
            <p><strong>Contact:</strong> <span id="businessContact">-</span></p>
            <p><strong>Featured:</strong> <span id="businessFeatured">-</span></p>
            <p><strong>Created:</strong> <span id="businessCreated">-</span></p>
            <p><strong>Updated:</strong> <span id="businessUpdated">-</span></p>
          </div>
          <span id="businessStatus" class="status-pill">Loading...</span>
        </div>
      </div>

      <div class="business-description mt-4 p-2">
        <h4>Description</h4>
        <p id="businessDescription">Loading business details...</p>
      </div>
    </div>
  </div>

  <script>
    const params = new URLSearchParams(window.location.search);
    const businessId = params.get('id');

    if (!businessId) {
      document.querySelector('.details-container').innerHTML =
        "<p class='text-danger'>Business ID is missing.</p>";
    } else {
      const apiUrl = `https://apploqic.my/index.php/api/v1/business/${businessId}`;

      fetch(apiUrl)
        .then(res => res.json())
        .then(data => {
          if (data.status !== "success" || !data.data) {
            document.querySelector('.details-container').innerHTML =
              "<p class='text-danger'>Business not found.</p>";
            return;
          }

          const b = data.data;

          // Fill in data
          document.getElementById("businessName").textContent = b.business_name || "-";
          document.getElementById("businessImage").src = b.business_img_url || "https://placehold.co/400x200?text=No+Image";
          document.getElementById("businessContact").textContent = b.business_contact || "-";
          document.getElementById("businessCategory").textContent = b.business_category || "-";
          document.getElementById("businessDescription").textContent = b.business_description || "-";
          document.getElementById("businessFeatured").textContent = b.is_featured == 1 ? "Yes" : "No";
          document.getElementById("businessCreated").textContent = b.created_at || "-";
          document.getElementById("businessUpdated").textContent = b.updated_at || "-";

          const statusEl = document.getElementById("businessStatus");
          if (b.status == 1) {
            statusEl.textContent = "Active";
            statusEl.classList.add("status-active");
          } else {
            statusEl.textContent = "Inactive";
            statusEl.classList.add("status-inactive");
          }
        })
        .catch(err => {
          console.error(err);
          document.querySelector('.details-container').innerHTML =
            "<p class='text-danger'>Error loading business details.</p>";
        });
    }
  </script>
</body>
</html>