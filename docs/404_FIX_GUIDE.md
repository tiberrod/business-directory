# 🚨 404 ERROR FIX FOR apploqic.my

## PROBLEM:
Getting 404 "Not Found" when calling: `https://apploqic.my/index.php?endpoint=business`

## ROOT CAUSE:
The `index.php` file is either:
1. ❌ Not uploaded to the correct location
2. ❌ In the wrong folder structure
3. ❌ Missing the proper cPanel file structure

## 🔧 IMMEDIATE FIX:

### STEP 1: Check Your File Structure
Your `public_html/` folder should look like this:
```
public_html/
├── index.php          ← THIS FILE MUST BE HERE (not in a subfolder!)
├── .htaccess          ← CORS and routing rules
├── images/            ← For uploaded images (create if missing)
├── app/
│   ├── config/
│   │   └── database.php
│   ├── controllers/
│   │   └── BusinessController.php
│   └── models/
│       └── BusinessDetail.php
└── frontend/
    ├── demo.html
    ├── js/
    └── css/
```

### STEP 2: Verify index.php Location
The API endpoint should be accessible at:
```
https://apploqic.my/index.php?endpoint=business
```

**NOT:**
- `https://apploqic.my/public/index.php?endpoint=business`
- `https://apploqic.my/api/index.php?endpoint=business`
- `https://apploqic.my/business-directory/index.php?endpoint=business`

### STEP 3: Upload Correct index.php
Make sure you upload the `index.php` from your `public/` folder to the ROOT of `public_html/`

**Source:** `public/index.php`  
**Destination:** `public_html/index.php`

## 🧪 DIAGNOSTIC TESTS:

### Test 1: Check if index.php exists
Visit: `https://apploqic.my/index.php`
- ✅ Should show some output (not 404)
- ❌ If 404: index.php is not in the right place

### Test 2: Check basic PHP
Visit: `https://apploqic.my/test.php` (create this file)
```php
<?php
echo "PHP is working!";
phpinfo();
?>
```

### Test 3: Check API without parameters
Visit: `https://apploqic.my/index.php`
- Should show some JSON response or error (not 404)

## 📁 CORRECT UPLOAD PROCESS:

### From Your Local Files:
```
c:\xampp\htdocs\Apploqic_Business_Directory\public\index.php
                                          ↓
                                    public_html/index.php
```

### Upload Checklist:
- [ ] `public/index.php` → `public_html/index.php`
- [ ] `app/` folder → `public_html/app/`
- [ ] `frontend/` folder → `public_html/frontend/`
- [ ] Create `public_html/images/` folder
- [ ] Upload `.htaccess_cpanel` as `public_html/.htaccess`

## 🚀 QUICK FIX STEPS:

### 1. Upload Missing Files:
```bash
# In cPanel File Manager, upload these to public_html/:
- index.php (from your public/ folder)
- .htaccess (rename from .htaccess_cpanel)
```

### 2. Test API Endpoint:
```
https://apploqic.my/index.php?endpoint=business
```

### 3. If Still 404, Try This:
Create `public_html/test-api.php`:
```php
<?php
echo "API Test Working!";
echo "\nServer: " . $_SERVER['SERVER_NAME'];
echo "\nPHP Version: " . phpversion();
?>
```

Then visit: `https://apploqic.my/test-api.php`

## ⚡ EMERGENCY API TEST:

If the main API is not working, create this simple test:
`public_html/simple-api.php`:
```php
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

echo json_encode([
    'status' => 200,
    'message' => 'Simple API working!',
    'server' => $_SERVER['SERVER_NAME'],
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
```

Test with: `https://apploqic.my/simple-api.php`

## 🎯 MOST LIKELY ISSUE:

Your `index.php` is probably in the wrong location. Make sure it's in the ROOT of `public_html/`, not in a subfolder.

**Wrong:** `public_html/public/index.php`  
**Correct:** `public_html/index.php`

After fixing the file location, your API should work at:
`https://apploqic.my/index.php?endpoint=business`