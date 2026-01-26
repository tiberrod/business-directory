# Production Deployment Checklist - Images Fix

## ✅ Files to Upload/Update on cPanel

### 1. Core Configuration Files
- [ ] `app/config/config.php` (Updated image paths)
- [ ] `.htaccess` (Updated with static file rules)

### 2. Image Directory Setup
- [ ] Create `/images/` directory in `public_html` root
- [ ] Set directory permissions to 755
- [ ] Upload all image files to `/images/` directory

### 3. View Files (Updated image paths)
- [ ] `views/client/business-details.php`
- [ ] `views/client/js/client.js`

## 🔧 Post-Deployment Testing

### Test Image Access Directly
Try accessing these URLs in your browser:
- `https://apploqic.my/images/sample1.png`
- `https://apploqic.my/images/sample2.png`
- `https://apploqic.my/images/1762875674_6913591ab44c4.png`

### Test API Image URLs
- Visit: `https://apploqic.my/api/v1/business`
- Check that `business_img_url` fields show correct URLs

### Test Business Pages
- Visit main business directory
- Click on individual businesses
- Verify images load properly

## 🚨 If Images Still Don't Load

### Check File Permissions
```bash
chmod 755 images/
chmod 644 images/*
```

### Check .htaccess Rules
Ensure this rule is in your `.htaccess`:
```apache
RewriteCond %{REQUEST_FILENAME} -f
RewriteRule ^(images|assets|public|css|js)/(.*)$ $0 [L]
```

### Check Image File Existence
Verify files exist in the `/images/` directory on your server.

## 📁 Expected Server Structure
```
public_html/
├── index.php
├── .htaccess
├── images/                    ← This must exist!
│   ├── sample1.png
│   ├── sample2.png
│   ├── test_image.jpg
│   └── 1762875674_6913591ab44c4.png
├── app/
├── views/
└── ...
```

## 🔍 Debugging Commands

If images still don't work, check:

1. **File exists**: Does `https://apploqic.my/images/filename.jpg` return 404?
2. **Permissions**: Can the web server read the files?
3. **Path**: Are files in the correct directory?
4. **Case sensitivity**: Linux servers are case-sensitive!