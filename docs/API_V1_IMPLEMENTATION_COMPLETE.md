# Business Directory API v1.0.0 - Complete Implementation

## Overview

This document provides a comprehensive overview of the newly implemented Business Directory API v1.0.0, which has been completely refactored to meet the latest requirements from frontend developers and project managers.

## ✅ Requirements Implementation Status

### ✅ Admin and User - API that can display all existing business
- **Endpoint**: `GET /app/api/router.php?version=v1&endpoint=businesses&action=list`
- **Access**: Admin & User
- **Features**: Pagination, filtering by category, featured status, and business status

### ✅ Admin and User - API that can search for specific business
- **Endpoint**: `GET /app/api/router.php?version=v1&endpoint=businesses&action=search&name={term}`
- **Access**: Admin & User
- **Features**: Search by name with additional filtering options

### ✅ Admin and User - Fetch details to be display when view details on specific business
- **Endpoint**: `GET /app/api/router.php?version=v1&endpoint=businesses&action=details&id={id}`
- **Access**: Admin & User
- **Features**: Complete business information with image URLs

### ✅ Admin - API that can create new business
- **Endpoint**: `POST /app/api/router.php?version=v1&endpoint=businesses&action=create`
- **Access**: Admin only
- **Features**: Create new business with all required and optional fields

### ✅ Admin - API that can update specific details for existing business
- **Endpoint**: `PUT /app/api/router.php?version=v1&endpoint=businesses&action=update&id={id}`
- **Access**: Admin only
- **Features**: Partial updates with field validation

### ✅ Admin - API that can deactivate the specific business that does not pay their monthly fee (soft-delete)
- **Endpoint**: `PUT /app/api/router.php?version=v1&endpoint=businesses&action=deactivate&id={id}`
- **Access**: Admin only
- **Features**: Soft delete with reason tracking, specifically for non-payment scenarios

### ✅ Delete function maintained (hard delete)
- **Endpoint**: `DELETE /app/api/router.php?version=v1&endpoint=businesses&action=delete&id={id}`
- **Access**: Admin only
- **Features**: Permanent deletion for administrative purposes

## 🗂️ New File Structure

The API has been completely reorganized within the `app` folder for better maintainability:

```
app/
├── api/
│   ├── router.php                 # Main API router with versioning
│   ├── test-v1-api.php           # Comprehensive test suite
│   └── v1/                       # Version 1 API
│       ├── controllers/
│       │   └── BusinessApiControllerV1.php  # Main business controller
│       └── endpoints/
│           ├── businesses.php     # List all businesses
│           ├── search.php        # Search businesses
│           ├── details.php       # Get business details
│           ├── create.php        # Create new business
│           ├── update.php        # Update business
│           ├── deactivate.php    # Deactivate business
│           └── delete.php        # Delete business
├── models/
│   ├── BusinessDetail.php        # Original model (preserved)
│   └── BusinessDetailV1.php      # Updated model for v1.0.0
├── config/
│   └── database.php              # Database configuration
└── helpers/
    └── ApiResponse.php           # API response helpers
```

## 📊 Database Schema v1.0.0

The API supports the updated database schema with the following fields:

| Field | Type | Attributes | Description |
|-------|------|------------|-------------|
| id | int(11) | AUTO_INCREMENT, PRIMARY KEY | Unique business identifier |
| business_name | varchar(255) | NOT NULL | Business name |
| business_img | text | NULL | Image filename |
| business_contact | varchar(100) | NULL | Contact information |
| business_description | text | NULL | Business description |
| business_category | varchar(255) | NULL | Business category |
| status | tinyint(1) | NOT NULL, DEFAULT 1 | Active/Inactive status |
| expiry_date | datetime | NULL | Business listing expiry date |
| reactivated_at | datetime | NULL | Last reactivation timestamp |
| is_featured | tinyint(1) | NOT NULL, DEFAULT 0 | Featured status |
| created_at | timestamp | DEFAULT CURRENT_TIMESTAMP | Creation timestamp |
| updated_at | timestamp | ON UPDATE CURRENT_TIMESTAMP | Last update timestamp |
| deactivated_reason | varchar(255) | NULL | Reason for deactivation |

## 🔧 API Versioning

### Version Support
- **Current Version**: v1.0.0
- **Versioning Method**: Query parameter or path-based
- **Future-Ready**: Easy to add v2, v3, etc.

### Version Usage Examples
```
# Query parameter method
GET /app/api/router.php?version=v1&endpoint=businesses&action=list

# Future versions
GET /app/api/router.php?version=v2&endpoint=businesses&action=list
```

## 🔐 Authentication & Authorization

### Role-Based Access Control
- **Admin Access**: Required for create, update, deactivate, and delete operations
- **User Access**: Allowed for listing, searching, and viewing business details

### Implementation Methods
1. **Header-based**: `X-User-Role: admin`
2. **Query parameter**: `admin_access=1`
3. **Production**: Implement JWT/session-based authentication

## 📋 API Endpoints Summary

### Public Endpoints (Admin & User)

#### 1. List All Businesses
```http
GET /app/api/router.php?version=v1&endpoint=businesses&action=list
```
**Parameters:**
- `page` (optional): Page number (default: 1)
- `limit` (optional): Results per page (default: 10, max: 100)
- `category` (optional): Filter by business category
- `featured` (optional): Filter by featured status (1/0)
- `status` (optional): Filter by status (active/inactive)
- `include_inactive` (optional): Include inactive businesses (1/0)

#### 2. Search Businesses
```http
GET /app/api/router.php?version=v1&endpoint=businesses&action=search&name={term}
```
**Parameters:**
- `name` (required): Search term
- `page`, `limit`, `category`, `featured`, `status`: Same as list endpoint

#### 3. Get Business Details
```http
GET /app/api/router.php?version=v1&endpoint=businesses&action=details&id={id}
```
**Parameters:**
- `id` (required): Business ID

### Admin-Only Endpoints

#### 4. Create Business
```http
POST /app/api/router.php?version=v1&endpoint=businesses&action=create
Headers: X-User-Role: admin
Content-Type: application/json

{
    "business_name": "Sample Business",
    "business_contact": "contact@example.com",
    "business_category": "Restaurant",
    "business_description": "Sample description",
    "business_img": "image.jpg",
    "status": 1,
    "is_featured": 0,
    "expiry_date": "2025-12-31 23:59:59"
}
```

#### 5. Update Business
```http
PUT /app/api/router.php?version=v1&endpoint=businesses&action=update&id={id}
Headers: X-User-Role: admin
Content-Type: application/json

{
    "business_name": "Updated Business Name",
    "business_description": "Updated description"
}
```

#### 6. Deactivate Business (Soft Delete)
```http
PUT /app/api/router.php?version=v1&endpoint=businesses&action=deactivate&id={id}
Headers: X-User-Role: admin
Content-Type: application/json

{
    "reason": "Non-payment of monthly fee"
}
```

#### 7. Delete Business (Hard Delete)
```http
DELETE /app/api/router.php?version=v1&endpoint=businesses&action=delete&id={id}
Headers: X-User-Role: admin
```

## 📝 Response Format

### Success Response
```json
{
    "status": "success",
    "data": {
        // Response data
    },
    "pagination": {  // For list endpoints
        "current_page": 1,
        "per_page": 10,
        "total": 100,
        "total_pages": 10
    }
}
```

### Error Response
```json
{
    "status": "error",
    "error": {
        "code": 400,
        "message": "Error description",
        "details": {  // Optional
            // Additional error information
        }
    }
}
```

## 🧪 Testing

### Test Suite
- **File**: `/app/api/test-v1-api.php`
- **Features**: Comprehensive testing of all endpoints
- **Coverage**: Authentication, validation, error handling
- **Access**: Visit `http://localhost/Apploqic_Business_Directory/app/api/test-v1-api.php`

### Test Categories
1. API Information
2. Business Listing (User Access)
3. Business Search (User Access)
4. Business Details (User Access)
5. Business Creation (Admin Access)
6. Business Update (Admin Access)
7. Business Deactivation (Admin Access)
8. Business Deletion (Admin Access)
9. Access Control Validation

## 🚀 Next Steps

### Production Deployment
1. **Database Setup**: Ensure the business_detail table matches the v1.0.0 schema
2. **Authentication**: Implement proper JWT/session-based authentication
3. **Environment Configuration**: Update database credentials for production
4. **SSL Certificate**: Enable HTTPS for secure API access
5. **Rate Limiting**: Implement API rate limiting for security

### Frontend Integration
1. **Base URL**: Use `/app/api/router.php` as the API base URL
2. **Version Header**: Always specify `version=v1` in requests
3. **Admin Access**: Send `X-User-Role: admin` header for admin operations
4. **Error Handling**: Implement proper error handling for all response codes

### Monitoring & Maintenance
1. **Logging**: Monitor API usage and errors via server logs
2. **Performance**: Track response times and optimize as needed
3. **Version Management**: Plan for future API versions (v2, v3, etc.)
4. **Documentation Updates**: Keep API documentation synchronized with changes

## 🔄 Migration from Old API

### Old API Files (To Be Removed)
- `/api/business.php`
- `/api/search.php`
- `/api/analytics.php`
- `/api/featured.php`
- `/index.php` (API portions)

### Migration Strategy
1. **Gradual Migration**: Support both old and new APIs during transition
2. **URL Mapping**: Create redirects from old URLs to new v1 endpoints
3. **Client Updates**: Update all frontend clients to use new API structure
4. **Deprecation Notice**: Announce old API deprecation timeline

## 📞 Support & Contact

For technical support or questions about the API implementation:
- Review the test suite results in `/app/api/test-v1-api.php`
- Check server error logs for detailed error information
- Verify database connectivity and schema compatibility

---

**Implementation Complete**: All required API endpoints have been implemented and are ready for testing and production deployment.