# Complete Image Fix Deployment Guide

## Files Changed - Deploy All These

### 1. Configuration Files
- [ ] `app/config/config.php` (Fixed production image upload path and URLs)
- [ ] `.htaccess` (Fixed static file serving and removed redirect-all 404s)

### 2. View Files  
- [ ] `views/client/business-details.php` (Fixed fallback image paths)
- [ ] `views/admin/js/admin.js` (Fixed admin fallback image paths)

### 3. New Directories & Files to Create on Server
- [ ] `/images/` directory (root level)
- [ ] `/assets/` directory (root level)
- [ ] Copy all files from `public/images/*` to `/images/`
- [ ] Copy `public/assets/preview.png` to `/assets/preview.png`
- [ ] Upload `404.html` for proper error handling

## Directory Structure After Deployment
```
public_html/
├── index.php
├── .htaccess
├── 404.html
├── images/                    ← Business images
│   ├── sample1.png
│   ├── sample2.png
│   ├── 1762875674_6913591ab44c4.png
│   └── test.txt
├── assets/                    ← Static assets (logos, preview image)
│   ├── preview.png
│   └── APPLOQIC-LOGO-HORIZONTAL---WHITE.png
├── app/
├── views/
├── public/
└── ...
```

## Expected URL Patterns After Fix

### Production (apploqic.my):
- **Business images**: `https://apploqic.my/images/filename.jpg`
- **Preview image**: `https://apploqic.my/assets/preview.png`
- **API image URLs**: `https://apploqic.my/images/filename.jpg` (no /public/)

### Localhost:
- **Business images**: `http://localhost:8000/public/images/filename.jpg`
- **Preview image**: `http://localhost:8000/public/assets/preview.png`

## Test Checklist After Deployment

### 1. Direct File Access
- [ ] `https://apploqic.my/images/test.txt` → Shows "This is a test file..."
- [ ] `https://apploqic.my/images/sample1.png` → Shows actual image (not business directory)
- [ ] `https://apploqic.my/assets/preview.png` → Shows preview image
- [ ] `https://apploqic.my/nonexistent.jpg` → Shows 404.html (not business directory)

### 2. API Response Check  
- [ ] Visit `https://apploqic.my/api/v1/business`
- [ ] Check `business_img_url` fields → Should be `https://apploqic.my/images/filename.jpg`
- [ ] No URLs should contain `/public/images/`

### 3. Business Directory
- [ ] Main page: `https://apploqic.my/` → Business cards show images or preview.png
- [ ] Business details: Click any business → Image displays correctly
- [ ] Admin panel: Business listing shows images or preview.png

### 4. Upload Test
- [ ] Create new business with image upload
- [ ] Verify image saves to `/images/` directory
- [ ] Verify image displays correctly in both admin and client views

## Common Issues & Solutions

### "Image URLs still show /public/images/"
- Check that updated `config.php` is deployed
- Clear any server-side caches
- Verify API is using the updated config

### "Preview images not showing"
- Ensure `/assets/preview.png` exists on server
- Check file permissions (644)
- Verify .htaccess allows assets/ directory access

### "Images redirect to business directory"
- Ensure updated `.htaccess` is deployed
- Remove `ErrorDocument 404 /index.php` line
- Test direct image URL access

### "New uploads not working"
- Check `/images/` directory permissions (755)
- Verify web server can write to the directory
- Check error logs for upload failures