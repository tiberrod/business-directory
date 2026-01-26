# Database Setup - Apploqic Business Directory

## 📋 Overview
This document provides complete database setup instructions for the Apploqic Business Directory project. **Database import is required before using the application.**

## 👥 Team Structure
- **Backend Developer**: 1 developer (database setup responsibility)
- **Frontend Developers**: 2 developers (consume API endpoints)

---

## 🗄️ Database Requirements

### Prerequisites
- **XAMPP** (Apache + MySQL + PHP)
- **MySQL** version 5.7 or higher
- **PHP** version 7.4 or higher

### Database Configuration
- **Database Name**: `apploqic_business`
- **Host**: `localhost`
- **Username**: `root`
- **Password**: `` (empty)
- **Port**: `3306` (default)

---

## 🚀 Setup Instructions

### Step 1: Start XAMPP Services
1. Open XAMPP Control Panel
2. Start **Apache** service
3. Start **MySQL** service

### Step 2: Create Database
1. Open browser and go to `http://localhost/phpmyadmin`
2. Click "New" to create a new database
3. Enter database name: `apploqic_business`
4. Set collation to: `utf8_general_ci`
5. Click "Create"

### Step 3: Import Database Structure
Execute the following SQL script in phpMyAdmin or MySQL command line:

```sql
-- Create database (if not already created)
CREATE DATABASE IF NOT EXISTS apploqic_business;
USE apploqic_business;

-- Create business_detail table
CREATE TABLE business_detail (
    id INT(11) NOT NULL AUTO_INCREMENT,
    business_name VARCHAR(255) NOT NULL,
    business_img VARCHAR(500) DEFAULT NULL,
    business_contact VARCHAR(100) NOT NULL,
    business_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert sample data (optional)
INSERT INTO business_detail (business_name, business_img, business_contact, business_description) VALUES
('Tech Solutions Inc', 'tech_solutions.jpg', '+1234567890', 'Leading technology solutions provider'),
('Green Garden Services', 'green_garden.jpg', '+0987654321', 'Professional landscaping and garden maintenance'),
('Digital Marketing Pro', 'digital_marketing.jpg', '+1122334455', 'Complete digital marketing solutions for businesses');
```

### Step 4: Verify Database Connection
1. Navigate to project root: `c:\xampp\htdocs\Apploqic_Business_Directory\`
2. Open browser and visit: `http://localhost/Apploqic_Business_Directory/test_db.php`
3. You should see a success message if the connection works

---

## 📊 Database Schema

### Table: `business_detail`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | INT(11) | PRIMARY KEY, AUTO_INCREMENT | Unique business identifier |
| `business_name` | VARCHAR(255) | NOT NULL | Name of the business |
| `business_img` | VARCHAR(500) | NULLABLE | Image filename or path |
| `business_contact` | VARCHAR(100) | NOT NULL | Contact information |
| `business_description` | TEXT | NULLABLE | Business description |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| `updated_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE | Last update time |

---

## 🔌 API Endpoints

### Base URL
```
http://localhost/Apploqic_Business_Directory/api/
```

### Available Endpoints

#### 1. Create Business
- **Method**: `POST`
- **Endpoint**: `createBusinessDetail.php`
- **Content-Type**: `application/json`
- **Payload**:
```json
{
    "business_name": "Business Name",
    "business_img": "image.jpg",
    "business_contact": "+1234567890",
    "business_description": "Business description"
}
```

#### 2. List All Businesses
- **Method**: `GET`
- **Endpoint**: `listBusinessDetail.php`
- **Response**: JSON array of business objects

#### 3. List Businesses (Paginated)
- **Method**: `GET`
- **Endpoint**: `listBusinessDetail.php?page=1&limit=10`
- **Parameters**:
  - `page`: Page number (default: 1)
  - `limit`: Items per page (default: 10)

#### 4. Get Business by ID
- **Method**: `GET`
- **Endpoint**: `listBusinessDetail.php?id=1`
- **Parameters**:
  - `id`: Business ID

---

## 🖼️ Image Upload Directory

### Directory Structure
```
public/
└── images/
    └── (uploaded business images)
```

### Image Upload Guidelines
- Supported formats: JPG, JPEG, PNG, GIF
- Maximum file size: 5MB
- Images are stored in `public/images/` directory
- File names should be unique to avoid conflicts

---

## 🛠️ For Backend Developer

### Database Connection File
The database connection is configured in:
```
config/database.php
```

### Model File
Business logic is handled in:
```
models/BusinessDetail.php
```

### Controller File
API logic is managed in:
```
controllers/BusinessController.php
```

### Available Methods in BusinessDetail Model
- `create($data)` - Create new business
- `update($id, $data)` - Update existing business
- `delete($id)` - Delete business
- `getAll()` - Get all businesses
- `getPaginated($page, $limit)` - Get paginated results
- `countAll()` - Count total businesses
- `getById($id)` - Get business by ID

---

## 🎨 For Frontend Developers

### API Integration Examples

#### Fetch All Businesses (JavaScript)
```javascript
fetch('http://localhost/Apploqic_Business_Directory/api/listBusinessDetail.php')
    .then(response => response.json())
    .then(data => {
        console.log('Businesses:', data);
    })
    .catch(error => console.error('Error:', error));
```

#### Create New Business (JavaScript)
```javascript
const businessData = {
    business_name: "New Business",
    business_img: "business.jpg",
    business_contact: "+1234567890",
    business_description: "Description here"
};

fetch('http://localhost/Apploqic_Business_Directory/api/createBusinessDetail.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify(businessData)
})
.then(response => response.json())
.then(data => {
    console.log('Success:', data);
})
.catch(error => console.error('Error:', error));
```

### Response Format
All API responses follow this structure:
```json
{
    "status": "success|error",
    "message": "Response message",
    "data": {} // Data object (for successful responses)
}
```

---

## 🚨 Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Ensure MySQL service is running in XAMPP
   - Check database credentials in `config/database.php`
   - Verify database `apploqic_business` exists

2. **Table Not Found**
   - Import the SQL script provided above
   - Check table name spelling in queries

3. **CORS Issues (Frontend)**
   - Add CORS headers in PHP files if needed:
   ```php
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
   header("Access-Control-Allow-Headers: Content-Type");
   ```

4. **File Upload Issues**
   - Ensure `public/images/` directory exists and is writable
   - Check PHP upload limits in `php.ini`

---

## 📝 Notes

- Always test API endpoints after database setup
- Use proper error handling in frontend applications
- Validate data before sending to API
- Consider implementing authentication for production use
- Regular database backups are recommended

---

## 📞 Support

For any issues or questions regarding database setup:
1. Check this README first
2. Verify XAMPP services are running
3. Test database connection using `test_db.php`
4. Contact the backend developer for assistance

**Last Updated**: October 7, 2025