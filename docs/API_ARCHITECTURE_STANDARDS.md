# Business Directory API Architecture Standards v1.0.0

## 📋 Overview

This document outlines the improved architecture standards implemented for the Business Directory API. The new structure provides proper separation of concerns, version support, and maintainable code that follows industry best practices.

## 🏗️ Architecture Design Principles

### **1. Separation of Concerns**
- **Models**: Handle data persistence and business logic
- **Controllers**: Manage HTTP requests and responses
- **Router**: Route requests to appropriate controllers
- **Interfaces**: Define contracts for consistency

### **2. Version Support**
- **Version-based Controllers**: Each API version has its own controller
- **Standard Base Model**: One model supports all API versions
- **Interface Contracts**: Ensure consistency across versions

### **3. Maintainability**
- **Clear File Structure**: Organized by functionality and version
- **Standard Naming**: Consistent naming conventions
- **Documentation**: Well-documented code and APIs

## 📁 New File Structure

```
app/
├── models/
│   ├── Base/
│   │   └── BusinessModel.php          # Standard model supporting all versions
│   ├── Interfaces/
│   │   └── BusinessModelInterface.php # Contract for business operations
│   ├── BusinessDetail.php             # Legacy model (preserved)
│   └── BusinessDetailV1.php          # Deprecated - replaced by Base/BusinessModel.php
├── controllers/
│   ├── Base/
│   │   └── BaseController.php         # Shared controller functionality
│   ├── v1/
│   │   └── BusinessController.php     # Version 1 specific controller
│   ├── v2/                           # Future versions
│   │   └── BusinessController.php     # Version 2 controller (when needed)
│   ├── BusinessController.php         # Legacy controller (preserved)
│   └── BusinessController_production.php # Legacy production controller
├── api/
│   ├── router.php                     # Main API router (updated)
│   ├── router-old.php                 # Old router (backup)
│   ├── test-v1-api.php               # API test suite
│   └── v1/                           # Legacy endpoints (can be removed)
│       ├── controllers/               # Deprecated
│       └── endpoints/                 # Deprecated
├── config/
│   ├── database.php                   # Database configuration
│   ├── database_production.php        # Production database config
│   └── config.php                     # General configuration
└── helpers/
    └── ApiResponse.php               # API response helpers
```

## 🔧 Architecture Components

### **1. Standard Base Model (`app/models/Base/BusinessModel.php`)**

**Purpose**: Single model that supports all API versions

**Key Features**:
- **Version-Aware**: Adapts to different API version requirements
- **Schema Mapping**: Maps database fields based on API version
- **Interface Implementation**: Implements `BusinessModelInterface`
- **Future-Proof**: Easy to extend for new API versions

**Usage**:
```php
// Initialize for v1
$model = new BusinessModel($db, 'v1');

// Initialize for future v2
$model = new BusinessModel($db, 'v2');

// Change version context
$model->setApiVersion('v2');
```

**Supported Versions**:
- **v1**: Full schema with expiry_date, reactivated_at, deactivated_reason
- **legacy**: Basic schema for backward compatibility

### **2. Business Model Interface (`app/models/Interfaces/BusinessModelInterface.php`)**

**Purpose**: Ensures consistency across all business models

**Defines**:
- Core CRUD operations (create, update, delete, getById, getAll, countAll)
- Search functionality
- Business-specific operations (deactivate, reactivate, getFeatured)
- Utility methods (columnExists, getTableName)

### **3. Base Controller (`app/controllers/Base/BaseController.php`)**

**Purpose**: Provides common functionality for all version controllers

**Features**:
- **Authentication**: Role-based access control
- **Response Handling**: Standardized success/error responses
- **Input Processing**: JSON input parsing and validation
- **Image URL Generation**: Consistent image URL handling
- **Logging**: Operation logging for audit purposes

### **4. Version-Specific Controllers**

**Current**: `app/controllers/v1/BusinessController.php`
**Future**: `app/controllers/v2/BusinessController.php`, etc.

**Features**:
- **Extends BaseController**: Inherits common functionality
- **Version-Specific Logic**: Implements version-specific business rules
- **Standard Methods**: index(), show(), store(), update(), delete(), etc.
- **Version Context**: Uses appropriate model with version context

### **5. API Router (`app/api/router.php`)**

**Purpose**: Central routing with version support

**Features**:
- **Version Detection**: Auto-detects API version from request
- **Controller Loading**: Dynamically loads appropriate version controller
- **Method Routing**: Routes to correct controller method based on action
- **Error Handling**: Comprehensive error handling and responses

## 🎯 API Usage Examples

### **Standard URL Pattern**:
```
/app/api/router.php?version={version}&endpoint={endpoint}&action={action}&id={id}
```

### **Version 1 Examples**:
```bash
# List businesses
GET /app/api/router.php?version=v1&endpoint=businesses&action=list

# Search businesses
GET /app/api/router.php?version=v1&endpoint=businesses&action=search&name=restaurant

# Get business details
GET /app/api/router.php?version=v1&endpoint=businesses&action=details&id=1

# Create business (Admin)
POST /app/api/router.php?version=v1&endpoint=businesses&action=create
Headers: X-User-Role: admin

# Update business (Admin)
PUT /app/api/router.php?version=v1&endpoint=businesses&action=update&id=1
Headers: X-User-Role: admin

# Deactivate business (Admin)
PUT /app/api/router.php?version=v1&endpoint=businesses&action=deactivate&id=1
Headers: X-User-Role: admin

# Delete business (Admin)
DELETE /app/api/router.php?version=v1&endpoint=businesses&action=delete&id=1
Headers: X-User-Role: admin
```

## 🔄 Benefits of New Architecture

### **1. Version Management**
- **Easy Versioning**: Add new versions without affecting existing ones
- **Backward Compatibility**: Legacy versions continue to work
- **Gradual Migration**: Smooth transition between versions

### **2. Code Maintainability**
- **Single Model**: One model handles all versions
- **Shared Logic**: Common functionality in base classes
- **Clear Separation**: Version-specific logic isolated in controllers

### **3. Scalability**
- **Modular Design**: Easy to add new features
- **Performance**: Optimized database queries
- **Future-Ready**: Architecture supports future enhancements

### **4. Developer Experience**
- **Consistent APIs**: Same patterns across versions
- **Better Testing**: Organized test structure
- **Clear Documentation**: Well-documented endpoints and usage

## 🚀 Migration Guide

### **From Old Structure to New Structure**

#### **Models**:
```php
// Old way (version-specific models)
$businessV1 = new BusinessDetailV1($db);

// New way (standard model with version context)
$business = new BusinessModel($db, 'v1');
```

#### **Controllers**:
```php
// Old way (single controller for all versions)
$controller = new BusinessController($db);

// New way (version-specific controllers)
$controller = new BusinessControllerV1($db, 'v1');
```

#### **API Calls**:
```bash
# Old way (mixed routing)
GET /index.php?endpoint=business
GET /api/business.php

# New way (consistent routing)
GET /app/api/router.php?version=v1&endpoint=businesses&action=list
```

## 📝 Best Practices

### **1. Adding New API Versions**

1. **Create Version Controller**:
   ```php
   // app/controllers/v2/BusinessController.php
   class BusinessControllerV2 extends BaseController {
       protected function initializeModel() {
           $this->model = new BusinessModel($this->db, 'v2');
       }
   }
   ```

2. **Update Model Schema**:
   ```php
   // Add v2 schema to BusinessModel
   protected $schemaVersions = [
       'v1' => [...],
       'v2' => [...]  // New fields for v2
   ];
   ```

3. **Update Router**:
   ```php
   // Add v2 to supported versions
   private $supportedVersions = ['v1', 'v2'];
   ```

### **2. Controller Method Standards**

All controllers should implement these standard methods:
- `index()` - List resources
- `show($id)` - Show single resource
- `search()` - Search resources
- `store()` - Create new resource
- `update()` - Update existing resource
- `delete()` - Delete resource

### **3. Response Format Standards**

**Success Response**:
```json
{
    "status": "success",
    "data": {...},
    "api_version": "v1",
    "pagination": {...}  // For list endpoints
}
```

**Error Response**:
```json
{
    "status": "error",
    "error": {
        "code": 400,
        "message": "Error description",
        "details": {...}  // Optional
    }
}
```

## 🧪 Testing

### **Test Architecture**:
- **Comprehensive Test Suite**: `/app/api/test-v1-api.php`
- **Version-Specific Tests**: Test each API version separately
- **Integration Tests**: Test router and controller integration

### **Running Tests**:
```bash
# Access via browser
http://localhost/Apploqic_Business_Directory/app/api/test-v1-api.php

# Or via command line
php app/api/test-v1-api.php
```

## 📚 Documentation

### **API Documentation**:
- **Complete Guide**: `/docs/API_V1_IMPLEMENTATION_COMPLETE.md`
- **Architecture Standards**: This document
- **Inline Documentation**: All classes and methods documented

### **API Information Endpoint**:
```bash
# Get API information and usage
GET /app/api/router.php?version=v1
```

## 🔮 Future Considerations

### **Planned Enhancements**:
1. **JWT Authentication**: Replace header-based auth with JWT tokens
2. **Rate Limiting**: Implement API rate limiting for security
3. **Caching**: Add response caching for better performance
4. **GraphQL Support**: Consider GraphQL endpoint for complex queries
5. **Webhooks**: Add webhook support for real-time updates

### **Version Roadmap**:
- **v1.0.0**: Current implementation (✅ Complete)
- **v1.1.0**: Enhanced authentication and validation
- **v2.0.0**: GraphQL support and advanced features

---

## ✅ Implementation Status

**Architecture Standards**: ✅ **COMPLETE**

- ✅ Standard Base Model created
- ✅ Business Model Interface implemented
- ✅ Base Controller with shared functionality
- ✅ Version-specific controller structure
- ✅ Updated API router with controller integration
- ✅ Comprehensive documentation
- ✅ Migration guide and best practices

**The new architecture provides a solid foundation for scalable, maintainable API development with proper version support and industry-standard practices.**