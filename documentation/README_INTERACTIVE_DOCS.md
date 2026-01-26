# 📚 Apploqic Business Directory API - Interactive Documentation System

## 🎉 Overview

The enhanced interactive documentation system provides a secure, user-friendly interface for exploring and testing the Apploqic Business Directory API. This system features authentication, dark mode UI, and comprehensive API documentation.

## 🔐 Authentication

**Login Credentials:**
- **Username:** `apploqic`
- **Password:** `apploqic`

## 🌟 Key Features

### ✅ Secure Authentication
- Login/logout functionality with hardcoded credentials
- Session-based authentication (1-hour expiry)
- Protected API documentation access

### 🎨 Dark Mode Interface
- Professional dark theme with navy blue accents
- Software engineering system-inspired design
- Responsive design for mobile and desktop
- Interactive animations and hover effects

### 📖 Interactive Documentation
- Complete API endpoint documentation
- Live examples and response previews
- Parameter descriptions and types
- Method-specific color coding (GET, POST, PUT, DELETE)

### 🔧 Dynamic Features
- Version filtering system (v1/v2 ready)
- Real-time API statistics
- Comprehensive endpoint explorer
- Copy-to-clipboard functionality

## 📁 File Structure

```
documentation/
├── index.php                      # Documentation router and API handler
├── interactive-api-docs.html      # Main interactive documentation interface
├── test-docs.html                 # System testing interface
├── js/
│   └── enhanced-api-docs.js       # Enhanced JavaScript functionality
├── css/
│   └── api-theme.css             # Existing API theme styles
└── assets/
    └── (logos and images)
```

## 🚀 Getting Started

### 1. Access the Documentation
Navigate to: `http://localhost/Apploqic_Business_Directory/documentation/`

### 2. Login
Use the credentials:
- Username: `apploqic`
- Password: `apploqic`

### 3. Explore APIs
After successful login, you'll see:
- Complete list of 9 available API endpoints
- Interactive endpoint explorer
- Real-time API statistics
- Comprehensive documentation for each endpoint

## 📊 Available API Endpoints

| Endpoint | Method | Description | Authentication |
|----------|--------|-------------|----------------|
| `/api/business` | GET | List all businesses (paginated) | No |
| `/api/business/{id}` | GET | Get specific business | No |
| `/api/search` | GET | Search businesses with filters | No |
| `/api/business` | POST | Create new business | Yes |
| `/api/business` | PUT | Update existing business | Yes |
| `/api/business` | DELETE | Delete business | Yes |
| `/api/analytics` | GET | Get business analytics | No |
| `/api/reactivate` | PUT | Reactivate inactive business | Yes |

## 🎨 Theme Colors

The documentation uses a professional dark theme with:
- **Primary Background:** `#0d1117` (Deep dark)
- **Secondary Background:** `#161b22` (Card background)
- **Accent Blue:** `#58a6ff` (Interactive elements)
- **Navy Primary:** `#1e3a8a` (Headers and buttons)
- **Success Green:** `#56d364`
- **Warning Orange:** `#f85149`
- **Text Primary:** `#f0f6fc`
- **Text Secondary:** `#8b949e`

## 🔧 Testing

### Quick Test
Visit: `http://localhost/Apploqic_Business_Directory/documentation/test-docs.html`

This testing interface provides:
- Authentication testing
- API endpoint testing
- Version information testing
- System overview and status

### Manual Testing
1. **Login Test:** Try logging in with correct/incorrect credentials
2. **Navigation Test:** Click through different API endpoints
3. **Version Filter Test:** Switch between v1 and v2 (v2 coming soon)
4. **Responsive Test:** Test on different screen sizes

## 📱 Responsive Design

The documentation is fully responsive:
- **Desktop:** Full sidebar and main content layout
- **Mobile:** Collapsible sidebar, stacked layout
- **Tablet:** Optimized spacing and touch-friendly buttons

## 🔄 Version Management

The system supports dynamic version filtering:
- **v1.0:** Current active version with all endpoints
- **v2.0:** Future version (placeholder, can be activated)

## 🛠️ Customization

### Adding New Endpoints
1. Update the `getApiEndpoints()` method in `enhanced-api-docs.js`
2. Add corresponding documentation in the controller
3. Update the sidebar navigation

### Modifying Theme Colors
Update the CSS custom properties in the `interactive-api-docs.html` `:root` section.

### Adding Features
The modular JavaScript architecture allows easy feature additions:
- Authentication enhancements
- New API testing tools
- Additional documentation sections

## 🚨 Security Notes

- Credentials are hardcoded for demonstration purposes
- Session expires after 1 hour of inactivity
- In production, implement proper authentication mechanisms
- CORS headers are configured for development

## 📞 Support

For issues or enhancements:
1. Check the test interface for system status
2. Review browser console for JavaScript errors
3. Verify PHP error logs for server-side issues

## 🎯 Future Enhancements

- [ ] JWT-based authentication
- [ ] API testing playground
- [ ] Export documentation to PDF
- [ ] Multi-language support
- [ ] Advanced search within documentation
- [ ] API usage analytics dashboard

---

**Last Updated:** November 2024  
**Version:** 1.0  
**Compatible with:** Apploqic Business Directory API v1.0