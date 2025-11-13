<?php
// create-business.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create New Business</title>
  <link rel="stylesheet" href="../css/style.css" />
</head>

<body>
  <!-- ===== Navbar ===== -->
  <nav class="custom-navbar">
    <div class="navbar-container">
      <h1 class="navbar-logo">Business Directory</h1>
      <ul class="navbar-links">
        <li><a href="../src/admin_index.php" class="nav-btn">Back to homepage</a></li>
      </ul>
    </div>
  </nav>

  <!-- ===== Create Business Form ===== -->
  <section>
    <form id="createBusinessForm" enctype="multipart/form-data">
      <h2 style="text-align:center; margin-bottom:15px;">Create New Business</h2>

      <div class="form-row">
        <div class="form-group">
          <label for="business_name">Business Name</label>
          <input type="text" id="business_name" name="business_name" required>
        </div>

        <div class="form-group">
          <label for="business_contact">Contact</label>
          <input type="text" id="business_contact" name="business_contact" required>
        </div>
      </div>

      <div class="form-group">
        <label for="business_description">Description</label>
        <textarea id="business_description" name="business_description" required></textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="business_category">Category</label>
          <input type="text" id="business_category" name="business_category" required>
        </div>

        <div class="form-group">
          <label for="is_featured">Featured</label>
          <select id="is_featured" name="is_featured">
            <option value="0">Not Featured</option>
            <option value="1">Featured</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="business_img">Business Image</label>
        <input type="file" id="business_img" name="business_img" accept="image/*">
        <img id="imgPreview" class="img-preview" src="#" alt="Preview" style="display:none;">
      </div>

      <button type="submit" class="btn-submit">Submit</button>
    </form>
  </section>

  <!-- ===== Scripts ===== -->
  <script src="../src/create-business.js"></script>
</body>
</html>
