# Production Image Fix for cPanel Deployment

## Problem Summary
Images were not loading on production (apploqic.my) because:
1. Image URLs were pointing to `/public/images/` instead of `/images/`
2. Images were being uploaded to the wrong directory in production
3. Missing .htaccess rules to serve static files properly

## Fixed Issues

### 1. Image Upload Path Configuration
- **File**: `app/config/config.php`
- **Fix**: Updated `getImageUploadPath()` to use root `/images/` directory for production
- **Before**: Images uploaded to `./public/images/` (non-existent in cPanel)
- **After**: Images uploaded to `./images/` (directly accessible from domain root)

### 2. Image URL Generation
- **File**: `app/config/config.php`
- **Fix**: Production image URLs now use `apploqic.my/images/` instead of `apploqic.my/public/images/`

### 3. Client-side Image Paths
- **File**: `views/client/business-details.php`
- **Fix**: Changed production image URL from `https://apploqic.my/public/images/` to `https://apploqic.my/images/`

### 4. .htaccess Rules
- **File**: `.htaccess`
- **Fix**: Added rule to serve static files (images, assets) directly without API routing

### 5. Image Directory Setup
- **Action**: Created `/images/` directory in project root
- **Action**: Copied existing images from `/public/images/` to `/images/`

## Deployment Instructions for cPanel

### Step 1: Upload Files
Upload all project files to your cPanel `public_html` directory.

### Step 2: Database Configuration
Update `app/config/database_production.php` with your cPanel database details:
```php
private $host = "localhost";
private $db_name = "username_databasename";  // cPanel format
private $username = "username_databaseuser";
private $password = "your_password";
```

### Step 3: Image Directory
Ensure the `/images/` directory exists in your domain root (same level as index.php) and has proper permissions (755).

### Step 4: Test Image Access
Test image accessibility by visiting: `https://yourdomain.com/images/your-image.jpg`

## File Structure on Production Server
```
public_html/
├── index.php
├── .htaccess
├── images/                 ← Images accessible at domain.com/images/
│   ├── image1.jpg
│   └── image2.png
├── app/
├── views/
├── public/
└── documentation/
```

## Current Image Paths
- **Localhost**: `http://localhost:8000/public/images/filename.jpg`
- **Production**: `https://apploqic.my/images/filename.jpg`

## Testing
After deployment, test image loading by:
1. Creating a new business with image upload
2. Viewing business details page
3. Checking API responses for correct `business_img_url` values
4. Direct image URL access: `https://apploqic.my/images/[filename]`