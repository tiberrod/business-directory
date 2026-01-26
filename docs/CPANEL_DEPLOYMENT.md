# 🚀 CPANEL DEPLOYMENT CHECKLIST

## BEFORE UPLOADING:

### 1. Database Setup in cPanel:
- [ ] Create MySQL database in cPanel
- [ ] Create database user with full privileges
- [ ] Note down: database name, username, password
- [ ] Import your database structure (SQL file)

### 2. Update Configuration Files:
- [ ] Copy `database_production.php` to `database.php`
- [ ] Update database credentials in `app/config/database.php`:
  ```php
  private $host = "localhost";
  private $db_name = "yourusername_dbname";
  private $username = "yourusername_dbuser";
  private $password = "your_password";
  ```

### 3. Update Image Paths in config.php:
- [ ] Set correct domain in `PROD_BASE_URL`
- [ ] Verify image upload path is correct

### 4. File Structure for Upload:
```
public_html/
├── index.php                     (from public/)
├── images/                       (create this folder)
├── frontend/                     (entire frontend folder)
├── app/                          (entire app folder)
├── .htaccess                     (copy from .htaccess_production)
└── API_DOCUMENTATION.md          (optional)
```

## UPLOAD STEPS:

### 1. File Manager Upload:
- [ ] Upload all files to public_html directory
- [ ] Set folder permissions: `images/` = 755
- [ ] Set file permissions: PHP files = 644

### 2. Database Import:
- [ ] Go to phpMyAdmin in cPanel
- [ ] Import your database SQL file
- [ ] Verify tables are created

### 3. Test URLs:
- [ ] API Base: `https://yourdomain.com/index.php?endpoint=business`
- [ ] Documentation: `https://yourdomain.com/frontend/demo.html`
- [ ] Test image upload functionality

## POST-DEPLOYMENT:

### 1. Security:
- [ ] Ensure app/ folder is not directly accessible
- [ ] Test that .htaccess is working
- [ ] Verify CORS headers are set

### 2. Functionality Tests:
- [ ] Test GET all businesses
- [ ] Test GET single business
- [ ] Test POST create business (with image)
- [ ] Test PUT update business (with/without image)
- [ ] Test DELETE business

### 3. Performance:
- [ ] Enable compression (.htaccess)
- [ ] Set proper cache headers
- [ ] Optimize images if needed

## COMMON CPANEL ISSUES & SOLUTIONS:

### PHP Version:
- Ensure PHP 7.4+ is selected in cPanel
- Enable required extensions: PDO, PDO_MySQL, GD

### File Permissions:
- Directories: 755
- PHP files: 644
- Images folder: 755 (writable)

### Database Connection:
- Use localhost as host (not IP)
- Database name format: username_databasename
- User format: username_databaseuser

### Image Upload Issues:
- Check folder permissions (755)
- Verify upload_max_filesize in PHP settings
- Ensure images/ folder exists and is writable

## ENVIRONMENT VARIABLES (Optional):
Create `.env` file in root:
```
DB_HOST=localhost
DB_NAME=yourusername_dbname
DB_USER=yourusername_dbuser
DB_PASS=your_password
BASE_URL=https://yourdomain.com/
```

## BACKUP PLAN:
- [ ] Keep local development version
- [ ] Export database before major changes
- [ ] Document any custom configurations