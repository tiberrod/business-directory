# Business Directory API Documentation v1.00

## Overview
This API provides endpoints for managing business details including creating, reading, updating, and deleting business information. The API now includes featured business highlighting functionality where businesses with `is_featured=1` are displayed on top with special highlighting.

## Base URL
```
http://localhost/Apploqic_Business_Directory/index.php
```

## Authentication
Currently, no authentication is required for these endpoints.

## Content Type
All requests and responses use `application/json` content type.

## Endpoints

### 1. Get All Businesses (Paginated)
**GET** `/index.php?endpoint=business`

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `category` (optional): Filter by business category
- `featured` (optional): Filter by featured status (1 for featured only, 0 for non-featured only)
- `status` (optional): Filter by status ('active' or 'inactive')
- `include_inactive` (optional): Include inactive businesses (1 to include, default excludes)

**Response:**
```json
{
  "status": 200,
  "message": "Success",
  "total": 25,
  "page": 1,
  "per_page": 10,
  "filters_applied": {},
  "data": [
    {
      "id": 1,
      "business_name": "Example Business",
      "business_description": "A sample business",
      "business_contact": "123-456-7890",
      "business_img": "example.jpg",
      "business_img_url": "https://domain.com/images/example.jpg",
      "business_category": "Restaurant",
      "status": 1,
      "is_featured": 1,
      "created_at": "2024-01-20 14:15:00",
      "updated_at": "2024-01-20 14:15:00"
    }
  ]
}
```

### 2. Get Featured Businesses with Highlighting ⭐ **NEW**
**GET** `/index.php?endpoint=featured`  
**Alternative:** `/api/featured.php` or `/api/business/featured`

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `limit` (optional): Results per page (default: 10)
- `category` (optional): Filter by business category

**Description:**
This endpoint returns businesses with highlighting functionality where:
- Businesses with `is_featured=1` are marked as "highlighted" and displayed on top
- Businesses with `is_featured=0` are marked as "normal"
- Results are sorted by featured status first, then by creation date

**Response:**
```json
{
  "status": 200,
  "message": "Featured businesses retrieved successfully",
  "total": 25,
  "page": 1,
  "per_page": 10,
  "total_pages": 3,
  "filters_applied": {
    "category": "Restaurant"
  },
  "summary": {
    "total_businesses": 10,
    "highlighted_businesses": 3,
    "normal_businesses": 7
  },
  "data": {
    "highlighted": [
      {
        "id": 5,
        "business_name": "Premium Restaurant",
        "business_contact": "+1234567890",
        "business_description": "Fine dining experience",
        "business_img": "restaurant.jpg",
        "business_img_url": "https://domain.com/images/restaurant.jpg",
        "business_category": "Restaurant",
        "status": 1,
        "is_featured": 1,
        "is_highlighted": 1,
        "display_priority": "highlighted",
        "created_at": "2024-01-20 14:15:00",
        "updated_at": "2024-01-20 14:15:00"
      }
    ],
    "normal": [
      {
        "id": 3,
        "business_name": "Local Cafe",
        "business_contact": "+0987654321",
        "business_description": "Cozy neighborhood cafe",
        "business_img": "cafe.jpg",
        "business_img_url": "https://domain.com/images/cafe.jpg",
        "business_category": "Restaurant",
        "status": 1,
        "is_featured": 0,
        "is_highlighted": 0,
        "display_priority": "normal",
        "created_at": "2024-01-19 10:30:00",
        "updated_at": "2024-01-19 10:30:00"
      }
    ],
    "all": [
      "// Combined array with highlighted businesses first, then normal businesses"
    ]
  }
}
```

### 3. Get Single Business
**GET** `/index.php?endpoint=business&id={id}`

**Parameters:**
- `id`: Business ID (integer)

**Response:**
```json
{
  "status": 200,
  "message": "Success",
  "data": {
    "id": 1,
    "business_name": "Example Business",
    "business_description": "A sample business",
    "business_contact": "123-456-7890",
    "business_img": "example.jpg"
  }
}
```

**Error Response (404):**
```json
{
  "status": 404,
  "message": "Business not found"
}
```

### 3. Create New Business
**POST** `/api/business`

**Content-Type:** `multipart/form-data`

**Required Fields:**
- `name`: Business name (string)
- `contact`: Business contact (string)

**Optional Fields:**
- `description`: Business description (string)
- `image`: Business image file (file upload)

**Response (Success - 201):**
```json
{
  "status": 201,
  "message": "Business detail was successfully created"
}
```

**Error Response (400):**
```json
{
  "status": 400,
  "message": "Missing required fields: name and contact are required"
}
```

### 4. Update Business
**PUT** `/api/business`

**Content-Type:** `application/json`

**Request Body:**
```json
{
  "id": 1,
  "name": "Updated Business Name",
  "contact": "987-654-3210",
  "description": "Updated description",
  "img": "updated_image.jpg"
}
```

**Response (Success - 200):**
```json
{
  "status": 200,
  "message": "Business updated successfully"
}
```

**Error Response (400):**
```json
{
  "status": 400,
  "message": "Business ID, name and contact are required"
}
```

### 5. Delete Business
**DELETE** `/api/business`

**Content-Type:** `application/json`

**Request Body:**
```json
{
  "id": 1
}
```

**Response (Success - 200):**
```json
{
  "status": 200,
  "message": "Business deleted successfully"
}
```

**Error Response (400):**
```json
{
  "status": 400,
  "message": "Business ID is required"
}
```

### 6. Search Businesses by Name and Description (Enhanced v1.1.0)
**GET** `/api/v1/search` 

**Query Parameters:**
- `name` (required): Search term for business name and description (case-insensitive partial matching)
- `page` (optional): Page number (default: 1)
- `category` (optional): Filter by business category
- `status` (optional): Filter by status ('active' or 'inactive', default: 'active')
- `featured` (optional): Filter by featured status (true/false)
- `expired` (optional): Include expired businesses (true/false, default: false)

**Example URLs:**
```
/api/v1/search?name=restaurant&page=1
/api/v1/search?name=app&category=IT Company
/api/v1/search?name=tech&status=active&featured=true
```

**Search Behavior:**
- Searches both business name and description fields
- Case-insensitive partial matching (e.g., "app" matches "Apploqic Technologies")
- Returns active businesses by default
- Supports advanced filtering by category, featured status, and expiry

**Response (Success - 200):**
```json
{
  "status": 200,
  "message": "Business search completed successfully",
  "search_term": "restaurant",
  "total_results": 5,
  "current_page": 1,
  "results_per_page": 10,
  "total_pages": 1,
  "businesses": [
    {
      "id": 1,
      "business_name": "Family Restaurant",
      "business_contact": "+1-555-0199",
      "business_description": "A cozy family restaurant serving traditional dishes",
      "business_img": "restaurant.jpg",
      "business_img_url": "http://example.com/public/images/restaurant.jpg",
      "created_at": "2024-01-15 10:30:00",
      "updated_at": "2024-01-15 10:30:00"
    }
  ]
}
```

**Error Response (400):**
```json
{
  "status": 400,
  "message": "Search term is required. Use ?name=search_term"
}
```

## HTTP Status Codes

- **200**: Success
- **201**: Created successfully
- **400**: Bad Request (validation errors)
- **404**: Not Found
- **405**: Method Not Allowed
- **500**: Internal Server Error

## File Upload

Images are uploaded to the `public/images/` directory with a timestamp prefix to avoid naming conflicts.

Supported image formats: Common web formats (jpg, png, gif, etc.)

## Error Handling

All errors return a JSON response with:
- `status`: HTTP status code
- `message`: Error description

## Database Schema

The API expects a MySQL database with the following table structure:

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

## Example Usage

### JavaScript/Fetch Examples

#### Get all businesses:
```javascript
fetch('http://localhost/Apploqic_Business_Directory/public/index.php/api/business?page=1')
  .then(response => response.json())
  .then(data => console.log(data));
```

#### Search businesses by name:
```javascript
const searchTerm = 'restaurant';
fetch(`http://localhost/Apploqic_Business_Directory/public/index.php/api/search?name=${encodeURIComponent(searchTerm)}&page=1`)
  .then(response => response.json())
  .then(data => {
    console.log(`Found ${data.total_results} businesses matching "${data.search_term}"`);
    console.log('Business details:', data.businesses);
    
    // Access individual business details
    data.businesses.forEach(business => {
      console.log(`${business.business_name}: ${business.business_contact}`);
    });
  });
```

#### Create a new business:
```javascript
const formData = new FormData();
formData.append('name', 'New Business');
formData.append('contact', '123-456-7890');
formData.append('description', 'Business description');
formData.append('image', fileInput.files[0]);

fetch('http://localhost/Apploqic_Business_Directory/public/index.php/api/business', {
  method: 'POST',
  body: formData
})
.then(response => response.json())
.then(data => console.log(data));
```

#### Update a business:
```javascript
fetch('http://localhost/Apploqic_Business_Directory/public/index.php/api/business', {
  method: 'PUT',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    id: 1,
    name: 'Updated Business Name',
    contact: '987-654-3210',
    description: 'Updated description'
  })
})
.then(response => response.json())
.then(data => console.log(data));
```

#### Delete a business:
```javascript
fetch('http://localhost/Apploqic_Business_Directory/public/index.php/api/business', {
  method: 'DELETE',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({ id: 1 })
})
.then(response => response.json())
.then(data => console.log(data));
```

## Notes for Frontend Developers

1. Always check the `status` field in the response for proper error handling
2. File uploads must use `multipart/form-data` content type
3. Update and delete operations use JSON in the request body
4. Images are stored with timestamp prefixes, so save the returned filename if needed
5. Pagination is available for the GET all businesses endpoint and search endpoint
6. Search functionality supports partial matching (case-insensitive LIKE search)
7. Both `/api/search` and `/api/business/search` endpoints work for searching
8. Always URL encode search terms to handle special characters properly
9. All responses include proper HTTP status codes for easy error handling