# Business Directory API v1.00

A complete RESTful API for managing business directory with interactive documentation interface.

## 🚀 Features

- **Complete CRUD Operations** (Create, Read, Update, Delete)
- **Business Search Functionality** with partial name matching
- **Image Upload Support** with automatic file management
- **Flexible Update System** (only ID required, other fields optional)
- **Interactive API Documentation** (like Postman)
- **Auto Environment Detection** (Development/Production)
- **cPanel Ready** with proper CORS handling

## 📁 Project Structure

```
Business_Directory_API_v1.00/
├── index.php                    # Main API endpoint
├── .htaccess                    # Server configuration
├── images/                      # Image uploads folder
├── app/
│   ├── config/
│   │   └── database.php         # Database configuration
│   ├── controllers/
│   │   └── BusinessController.php
│   └── models/
│       └── BusinessDetail.php
├── documentation/
│   ├── api-docs.html           # Interactive API documentation
│   ├── css/
│   │   └── api-theme.css       # Documentation styling
│   └── js/
│       ├── api-client.js       # API client & testing
│       └── config.js           # Environment configuration
├── README.md
└── API_DOCUMENTATION.md
```

## 🔧 Installation

### Development (XAMPP/Local)
1. Clone to your web server directory
2. Import database structure
3. Access: `http://localhost/Business_Directory_API_v1.00/`

### Production (cPanel)
1. Upload all files to `public_html/`
2. Update database credentials in `app/config/database.php`
3. Set folder permissions: `images/` = 755
4. Access: `https://yourdomain.com/`

## 🌐 API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | `/index.php?endpoint=business` | Get all businesses (paginated) |
| GET    | `/index.php?endpoint=business&id={id}` | Get single business |
| GET    | `/api/search?name={searchTerm}` | Search businesses by name |
| GET    | `/api/business/search?name={searchTerm}` | Alternative search endpoint |
| POST   | `/index.php?endpoint=business` | Create new business |
| PUT    | `/index.php?endpoint=business` | Update business (flexible) |
| DELETE | `/index.php?endpoint=business` | Delete business |

## 📖 Documentation

- **Interactive Docs**: `/documentation/api-docs.html`
- **API Reference**: `API_DOCUMENTATION.md`

## 🎯 Version 1.00 Features

- ✅ Complete RESTful API
- ✅ Business search functionality
- ✅ Image upload/management
- ✅ Flexible update system
- ✅ Interactive documentation
- ✅ cPanel deployment ready
- ✅ Auto environment detection
- ✅ Comprehensive error handling

## 🔗 Quick Links

- **Test API**: `https://yourdomain.com/index.php?endpoint=business`
- **Documentation**: `https://yourdomain.com/documentation/api-docs.html`

---

**Business Directory API v1.00** - Professional RESTful API with interactive documentation