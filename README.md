# Business Directory API v1.00# Business Directory API



A professional RESTful API for managing business directories with interactive documentation.A RESTful API for managing business directory information, built with PHP and MySQL.



## 🚀 Quick Start## 🚀 Features



### Local Development (XAMPP)- **CRUD Operations**: Create, Read, Update, and Delete business entries

1. Clone to your web server directory- **File Upload**: Support for business images with validation

2. Import database structure- **Pagination**: Paginated listing of businesses

3. Access: `http://localhost/Apploqic_Business_Directory/`- **Validation**: Comprehensive input validation and error handling

- **Clean API Responses**: Consistent JSON responses with proper HTTP status codes

### Production (cPanel)- **CORS Support**: Cross-origin resource sharing enabled

1. Upload files to `public_html/`- **Error Logging**: Proper error logging for debugging

2. Update database credentials in `app/config/database.php`

3. Set `images/` folder permissions to 755## 📁 Project Structure

4. Access: `https://yourdomain.com/`

```

## 📡 API EndpointsApploqic_Business_Directory/

├── app/

| Method | Endpoint | Description |│   ├── config/

|--------|----------|-------------|│   │   └── database.php          # Database configuration

| GET    | `/?endpoint=business` | Get all businesses |│   ├── controllers/

| GET    | `/?endpoint=business&id={id}` | Get single business |│   │   ├── BusinessController.php     # Original controller (fixed)

| POST   | `/?endpoint=business` | Create business |│   │   └── BusinessControllerImproved.php  # Enhanced version with better error handling

| PUT    | `/?endpoint=business` | Update business |│   ├── models/

| DELETE | `/?endpoint=business` | Delete business |│   │   └── BusinessDetail.php     # Database model for business operations

│   └── helpers/

## 📖 Documentation│       └── ApiResponse.php        # API response helper class

├── public/

- **Interactive API Docs**: `/documentation/api-docs.html`│   ├── index.php                  # Main API entry point (original, fixed)

- **Test API**: `/test.php`│   ├── index_improved.php         # Enhanced API entry point

│   ├── demo.html                  # Legacy demo page (use frontend/ instead)

## 🔧 Configuration│   └── images/                    # Directory for uploaded images

├── frontend/                      # Frontend application (NEW!)

The API automatically detects environment (development/production) and adjusts settings accordingly.│   ├── index.html                 # Main demo interface

│   ├── README.md                  # Frontend documentation

---│   ├── css/

│   │   └── style.css              # Stylesheet

**Business Directory API v1.00** - Ready for production use.│   ├── js/
│   │   └── app.js                 # JavaScript API client
│   └── assets/                    # Static assets
├── API_DOCUMENTATION.md           # Complete API documentation
├── test_connection.php            # Database connection test script
└── README.md                      # This file
```

## 🔧 Setup Instructions

### 1. Prerequisites

- **XAMPP** (or any PHP server with MySQL)
- **PHP 7.4+**
- **MySQL 5.7+**

### 2. Installation

1. **Clone/Download** the project to your XAMPP htdocs directory:
   ```
   c:\xampp\htdocs\Apploqic_Business_Directory\
   ```

2. **Start XAMPP** services:
   - Apache
   - MySQL

3. **Create Database**:
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `apploqic_business`
   - Run the following SQL to create the table:

   ```sql
   CREATE TABLE business_detail (
       id INT AUTO_INCREMENT PRIMARY KEY,
       business_name VARCHAR(255) NOT NULL,
       business_description TEXT,
       business_contact VARCHAR(100) NOT NULL,
       business_img VARCHAR(255),
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
   );
   ```

4. **Test Database Connection**:
   ```
   http://localhost/Apploqic_Business_Directory/test_connection.php
   ```

### 3. Configuration

Update database credentials in `app/config/database.php` if needed:

```php
private $host = "localhost";
private $db_name = "apploqic_business";
private $username = "root";
private $password = "";
```

## 🚀 Quick Start

### 1. Access the Frontend Demo
Open your browser and go to:
```
http://localhost/Apploqic_Business_Directory/frontend/index.html
```

### 2. Or Use the API Directly
Base URL: `http://localhost/Apploqic_Business_Directory/public/index.php`

## 🚀 Usage

### API Endpoints

**Base URL**: `http://localhost/Apploqic_Business_Directory/public/index.php`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | `/api/business` | Get all businesses (paginated) |
| GET    | `/api/business/{id}` | Get specific business |
| POST   | `/api/business` | Create new business |
| PUT    | `/api/business` | Update existing business |
| DELETE | `/api/business` | Delete business |

### Frontend Interface

**Main Website**: Professional Apploqic branding page:
```
http://localhost/Apploqic_Business_Directory/frontend/index.html
```

**API Testing Demo**: Interactive demo for developers and testing:
```
http://localhost/Apploqic_Business_Directory/frontend/demo.html
```

**Legacy Demo**: The original demo is still available at:
```
http://localhost/Apploqic_Business_Directory/public/demo.html
```

## 📖 API Documentation

See [API_DOCUMENTATION.md](API_DOCUMENTATION.md) for complete API reference with examples.

## 🔧 Development

### Using the Improved Version

For better maintainability and error handling, use the improved files:

- **Entry Point**: `public/index_improved.php`
- **Controller**: `app/controllers/BusinessControllerImproved.php`
- **Helper**: `app/helpers/ApiResponse.php`

### Key Improvements Made

1. **Fixed Syntax Errors**:
   - Corrected double dollar sign in `getAll()` method
   - Fixed parameter binding in model methods

2. **Enhanced Error Handling**:
   - Proper try-catch blocks
   - Consistent error responses
   - Input validation

3. **Better Data Structure**:
   - Consistent parameter naming
   - Proper HTTP status codes
   - Standardized JSON responses

4. **Security Improvements**:
   - File upload validation
   - SQL injection prevention
   - Input sanitization

5. **Maintainability**:
   - ApiResponse helper class
   - Better code organization
   - Comprehensive documentation

### File Upload Notes

- **Upload Directory**: `public/images/`
- **Allowed Types**: JPEG, PNG, GIF, WebP
- **Max Size**: 5MB
- **Naming**: Timestamp + unique ID to prevent conflicts

### Error Handling

The API returns consistent error responses:

```json
{
  "status": 400,
  "message": "Error description"
}
```

For validation errors:

```json
{
  "status": 422,
  "message": "Validation failed",
  "errors": ["List of validation errors"]
}
```

## 🧪 Testing

### Manual Testing

1. **Test Database Connection**:
   ```
   http://localhost/Apploqic_Business_Directory/test_connection.php
   ```

2. **Use Demo Interface**:
   ```
   http://localhost/Apploqic_Business_Directory/public/demo.html
   ```

3. **API Testing with Postman/Curl**:
   - Import the API endpoints from documentation
   - Test all CRUD operations

### Common Test Cases

1. **Create Business** (POST):
   - With all fields
   - With missing required fields
   - With file upload

2. **Get Businesses** (GET):
   - Get all with pagination
   - Get single business
   - Get non-existent business

3. **Update Business** (PUT):
   - Valid update
   - Invalid ID
   - Missing required fields

4. **Delete Business** (DELETE):
   - Valid deletion
   - Invalid ID

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Failed**:
   - Check XAMPP MySQL service is running
   - Verify database credentials
   - Ensure database exists

2. **File Upload Issues**:
   - Check `public/images/` directory exists and is writable
   - Verify file permissions (755)
   - Check PHP upload settings in `php.ini`

3. **API Not Responding**:
   - Verify Apache is running
   - Check for PHP errors in logs
   - Ensure correct file paths

4. **CORS Issues**:
   - Headers are set in `index.php`
   - For development, all origins are allowed (`*`)

### Debug Mode

Enable error reporting by adding to the top of `index.php`:

```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

## 🎨 Frontend Application

### New Organized Frontend
The project now includes a properly organized frontend application in the `frontend/` directory:

- **Modern Interface**: Clean, responsive design
- **Organized Structure**: Separate CSS, JS, and HTML files
- **Enhanced Features**: Image preview, notifications, pagination
- **Better UX**: Loading states, form validation, error handling

See [frontend/README.md](frontend/README.md) for detailed frontend documentation.

## 📱 Frontend Integration

### JavaScript Example

```javascript
// Get all businesses
const response = await fetch('http://localhost/Apploqic_Business_Directory/public/index.php/api/business');
const data = await response.json();

// Create business with image
const formData = new FormData();
formData.append('name', 'Business Name');
formData.append('contact', '123-456-7890');
formData.append('image', fileInput.files[0]);

const createResponse = await fetch('http://localhost/Apploqic_Business_Directory/public/index.php/api/business', {
    method: 'POST',
    body: formData
});
```

### React/Vue/Angular Integration

The API follows REST conventions and returns JSON, making it compatible with any frontend framework.

## 🚀 Production Deployment

### Security Considerations

1. **Environment Variables**: Move database credentials to environment variables
2. **CORS**: Restrict allowed origins
3. **File Upload**: Add more restrictive file validation
4. **Error Logging**: Implement proper logging system
5. **Rate Limiting**: Add API rate limiting
6. **Authentication**: Implement authentication system

### Performance Optimization

1. **Database Indexing**: Add indexes on frequently queried columns
2. **Image Optimization**: Implement image compression
3. **Caching**: Add response caching for GET requests
4. **Pagination**: Optimize pagination queries

## 📝 License

This project is open source and available under the [MIT License](LICENSE).

## 🤝 Contributing

1. Fork the project
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📞 Support

For support and questions:
- Create an issue in the project repository
- Review the API documentation
- Check the troubleshooting section