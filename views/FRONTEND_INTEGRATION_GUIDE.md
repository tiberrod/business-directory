# 🚀 Frontend Integration Guide
## Business Directory API

This guide shows frontend developers exactly how to integrate with the Business Directory API.

## 📍 Single API Entry Point

**Use this URL for ALL API calls:**
```
http://localhost/Apploqic_Business_Directory/public/api.php
```

**For Production:**
```
https://yourdomain.com/public/api.php
```

## 🔗 API Endpoints

### 1. Get API Information
```http
GET /public/api.php
```
**Response:**
```json
{
  "status": "success",
  "message": "Apploqic Business Directory API",
  "version": "1.0.0",
  "endpoints": { ... },
  "base_url": "http://localhost/Apploqic_Business_Directory/public"
}
```

### 2. Get All Businesses
```http
GET /public/api.php/business
```
**Optional Parameters:**
- `page=1` - Page number for pagination
- `category=restaurant` - Filter by category
- `featured=1` - Show only featured businesses
- `status=active` - Filter by status

**Example:**
```http
GET /public/api.php/business?page=1&featured=1
```

### 3. Get Specific Business
```http
GET /public/api.php/business/{id}
```
**Example:**
```http
GET /public/api.php/business/123
```

### 4. Search Businesses
```http
GET /public/api.php/business/search?name={query}
```
**Example:**
```http
GET /public/api.php/business/search?name=coffee
```

### 5. Create New Business
```http
POST /public/api.php/business
```
**Content-Type:** `multipart/form-data` (for file uploads) or `application/json`

**Form Data:**
```javascript
{
  "business_name": "Coffee Shop",
  "business_contact": "123-456-7890",
  "business_email": "info@coffeeshop.com",
  "business_address": "123 Main St",
  "business_img": file, // Optional image file
  "business_category": "restaurant",
  "featured": 0 // 0 or 1
}
```

### 6. Update Business
```http
PUT /public/api.php/business/{id}
```
**Example:**
```http
PUT /public/api.php/business/123
```

### 7. Delete Business
```http
DELETE /public/api.php/business/{id}
```
**Example:**
```http
DELETE /public/api.php/business/123
```

## 📱 JavaScript Integration Examples

### Using Fetch API

#### Get All Businesses
```javascript
async function getAllBusinesses() {
  try {
    const response = await fetch('/public/api.php/business');
    const data = await response.json();
    console.log(data);
  } catch (error) {
    console.error('Error:', error);
  }
}
```

#### Create New Business
```javascript
async function createBusiness(formData) {
  try {
    const response = await fetch('/public/api.php/business', {
      method: 'POST',
      body: formData // FormData object
    });
    const data = await response.json();
    console.log(data);
  } catch (error) {
    console.error('Error:', error);
  }
}
```

#### Search Businesses
```javascript
async function searchBusinesses(query) {
  try {
    const response = await fetch(`/public/api.php/business/search?name=${encodeURIComponent(query)}`);
    const data = await response.json();
    console.log(data);
  } catch (error) {
    console.error('Error:', error);
  }
}
```

### Using Axios

#### Get All Businesses
```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: '/public/api.php'
});

async function getAllBusinesses() {
  try {
    const response = await api.get('/business');
    console.log(response.data);
  } catch (error) {
    console.error('Error:', error);
  }
}
```

#### Create with Image Upload
```javascript
async function createBusinessWithImage(businessData, imageFile) {
  const formData = new FormData();
  
  // Add business data
  Object.keys(businessData).forEach(key => {
    formData.append(key, businessData[key]);
  });
  
  // Add image if provided
  if (imageFile) {
    formData.append('business_img', imageFile);
  }
  
  try {
    const response = await api.post('/business', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    console.log(response.data);
  } catch (error) {
    console.error('Error:', error);
  }
}
```

## 🔧 Error Handling

All API responses follow this format:

**Success Response:**
```json
{
  "status": "success",
  "data": { ... },
  "message": "Operation successful"
}
```

**Error Response:**
```json
{
  "status": "error",
  "message": "Error description",
  "error": "Detailed error information"
}
```

## 🌐 CORS Headers

The API includes proper CORS headers for cross-origin requests:
- `Access-Control-Allow-Origin: *`
- `Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS`
- `Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With`

## 📋 Complete Example: Business Management Component

```javascript
class BusinessAPI {
  constructor(baseURL = '/public/api.php') {
    this.baseURL = baseURL;
  }

  async request(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`;
    const response = await fetch(url, {
      headers: {
        'Content-Type': 'application/json',
        ...options.headers
      },
      ...options
    });
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    return await response.json();
  }

  // Get all businesses
  async getBusinesses(filters = {}) {
    const params = new URLSearchParams(filters);
    return this.request(`/business?${params}`);
  }

  // Get single business
  async getBusiness(id) {
    return this.request(`/business/${id}`);
  }

  // Search businesses
  async searchBusinesses(query) {
    return this.request(`/business/search?name=${encodeURIComponent(query)}`);
  }

  // Create business
  async createBusiness(businessData, imageFile = null) {
    const formData = new FormData();
    
    Object.keys(businessData).forEach(key => {
      formData.append(key, businessData[key]);
    });
    
    if (imageFile) {
      formData.append('business_img', imageFile);
    }

    return this.request('/business', {
      method: 'POST',
      body: formData,
      headers: {} // Remove Content-Type to let browser set it for FormData
    });
  }

  // Update business
  async updateBusiness(id, updates) {
    return this.request(`/business/${id}`, {
      method: 'PUT',
      body: JSON.stringify(updates)
    });
  }

  // Delete business
  async deleteBusiness(id) {
    return this.request(`/business/${id}`, {
      method: 'DELETE'
    });
  }
}

// Usage example
const api = new BusinessAPI();

// Get all businesses
api.getBusinesses({ page: 1, featured: 1 })
  .then(data => console.log(data))
  .catch(error => console.error(error));
```

## ⚠️ Important Notes

1. **Use Only This Entry Point**: Always use `/public/api.php` - don't use other API files
2. **Image Uploads**: Use `FormData` for requests with file uploads
3. **Error Handling**: Always check the `status` field in responses
4. **CORS**: The API supports cross-origin requests from any domain
5. **Content-Type**: Use `multipart/form-data` for file uploads, `application/json` for JSON data

## 🔍 Testing the API

You can test the API using:
1. Browser: `http://localhost/Apploqic_Business_Directory/public/api.php`
2. Postman: Import the endpoints above
3. cURL commands
4. Frontend applications

This single entry point ensures consistent API access for all frontend developers.