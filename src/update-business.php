<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Business Details</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <h1>Update Business Details</h1>

  <form id="updateBusinessForm" enctype="multipart/form-data">
    <div>
      <label for="businessId">Business ID (required)</label><br>
      <input type="number" id="businessId" name="id" placeholder="Enter existing business ID" required>
    </div>

    <div>
      <label for="businessName">New Business Name</label><br>
      <input type="text" id="businessName" name="name" placeholder="Leave blank to keep current">
    </div>

    <div>
      <label for="businessContact">New Business Contact</label><br>
      <input type="text" id="businessContact" name="contact" placeholder="Leave blank to keep current">
    </div>

    <div>
      <label for="businessDescription">New Description</label><br>
      <textarea id="businessDescription" name="description" placeholder="Leave blank to keep current"></textarea>
    </div>

    <div>
      <label for="businessImage">New Business Image (optional)</label><br>
      <input type="file" id="businessImage" name="business_img" accept="image/*">
    </div>

    <button type="submit">Update Business</button>
  </form>

  <div id="messageBox" style="margin-top:20px;"></div>

  <p><a href="index.php">⬅ Back to Dashboard</a></p>

  <script src="update-business.js"></script>
</body>
</html>
