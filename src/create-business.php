<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create New Business</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <h1>Create New Business</h1>

  <form id="createBusinessForm" enctype="multipart/form-data">
    <div>
      <label for="businessName">Business Name (required)</label><br>
      <input type="text" id="businessName" name="name" required>
    </div>

    <div>
      <label for="businessContact">Business Contact (required)</label><br>
      <input type="text" id="businessContact" name="contact" required>
    </div>

    <div>
      <label for="businessDescription">Business Description (optional)</label><br>
      <textarea id="businessDescription" name="description"></textarea>
    </div>

    <div>
      <label for="businessImage">Business Image (optional)</label><br>
      <input type="file" id="businessImage" name="business_img" accept="image/*">
    </div>

    <button type="submit">Create Business</button>
  </form>

  <div id="messageBox" style="margin-top:20px;"></div>

  <script src="create-business.js"></script>
</body>
</html>
