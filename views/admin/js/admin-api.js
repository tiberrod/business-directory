/**
 * Admin API Configuration
 * Centralized API endpoint configuration for admin interface
 */

class AdminAPI {
    constructor() {
        // Detect if we're running on localhost or production
        this.isLocalhost = window.location.hostname === 'localhost' || 
                          window.location.hostname === '127.0.0.1' || 
                          window.location.hostname.includes('xampp');
        
        // Set base API URL - Fixed for different access methods
        if (this.isLocalhost) {
            // Handle both XAMPP and direct PHP server access
            const currentPath = window.location.pathname;
            if (currentPath.includes('/Apploqic_Business_Directory/')) {
                // XAMPP access: localhost/Apploqic_Business_Directory/
                this.baseUrl = `${window.location.protocol}//${window.location.host}/Apploqic_Business_Directory/public/api-v1.php`;
            } else {
                // Direct PHP server or root access: localhost:8000/
                this.baseUrl = `${window.location.protocol}//${window.location.host}/public/api-v1.php`;
            }
        } else {
            // Production
            this.baseUrl = `${window.location.protocol}//${window.location.host}/public/api-v1.php`;
        }
        
        console.log('API Base URL:', this.baseUrl); // Debug logging
    }

    /**
     * Get all businesses with optional filters
     * GET /api/v1/business
     */
    async getBusinesses(params = {}) {
        const url = new URL(this.baseUrl);
        url.searchParams.append('endpoint', 'business');
        
        // Add query parameters
        Object.keys(params).forEach(key => {
            if (params[key] !== null && params[key] !== undefined && params[key] !== '') {
                url.searchParams.append(key, params[key]);
            }
        });

        const response = await fetch(url.toString(), {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    }

    /**
     * Get single business by ID
     * GET /api/v1/business/{id}
     */
    async getBusiness(id) {
        const url = new URL(this.baseUrl);
        url.searchParams.append('endpoint', 'business');
        url.searchParams.append('id', id);

        const response = await fetch(url.toString(), {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    }

    /**
     * Search businesses
     * GET /api/v1/search
     */
    async searchBusinesses(params = {}) {
        const url = new URL(this.baseUrl);
        url.searchParams.append('endpoint', 'search');
        
        // Add search parameters
        Object.keys(params).forEach(key => {
            if (params[key] !== null && params[key] !== undefined && params[key] !== '') {
                url.searchParams.append(key, params[key]);
            }
        });

        const response = await fetch(url.toString(), {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    }

    /**
     * Create new business
     * POST /api/v1/business
     */
    async createBusiness(formData) {
        const url = new URL(this.baseUrl);
        url.searchParams.append('endpoint', 'business');

        const response = await fetch(url.toString(), {
            method: 'POST',
            body: formData // FormData object
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    }

    /**
     * Update existing business
     * PUT /api/v1/business
     */
    async updateBusiness(id, formData) {
        const url = new URL(this.baseUrl);
        url.searchParams.append('endpoint', 'business');
        url.searchParams.append('id', id);

        // Add method override for PUT
        formData.append('_method', 'PUT');

        const response = await fetch(url.toString(), {
            method: 'POST', // Using POST with _method override for better compatibility
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    }

    /**
     * Delete business
     * DELETE /api/v1/business
     */
    async deleteBusiness(id) {
        const url = new URL(this.baseUrl);
        url.searchParams.append('endpoint', 'business');
        url.searchParams.append('id', id);

        const response = await fetch(url.toString(), {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    }

    /**
     * Reactivate business
     * PUT /api/v1/reactivate/{id}
     */
    async reactivateBusiness(id) {
        const url = new URL(this.baseUrl);
        url.searchParams.append('endpoint', 'reactivate');
        url.searchParams.append('id', id);

        const response = await fetch(url.toString(), {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    }

    /**
     * Handle API errors consistently
     */
    handleError(error, context = 'API operation') {
        console.error(`${context} failed:`, error);
        
        if (error.message.includes('HTTP error! status: 404')) {
            return `${context} failed: Resource not found`;
        } else if (error.message.includes('HTTP error! status: 500')) {
            return `${context} failed: Server error`;
        } else if (error.message.includes('Failed to fetch')) {
            return `${context} failed: Network error`;
        } else {
            return `${context} failed: ${error.message}`;
        }
    }

    /**
     * Show success/error messages
     */
    showMessage(message, type = 'info', duration = 5000) {
        // Remove existing messages
        const existing = document.querySelectorAll('.api-message');
        existing.forEach(msg => msg.remove());

        // Create new message
        const messageEl = document.createElement('div');
        messageEl.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} api-message`;
        messageEl.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideIn 0.3s ease-out;
        `;
        messageEl.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                <span>${message}</span>
                <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;

        document.body.appendChild(messageEl);

        // Auto remove after duration
        setTimeout(() => {
            if (messageEl.parentElement) {
                messageEl.remove();
            }
        }, duration);
    }
}

// Global instance
window.adminAPI = new AdminAPI();

// Add slide-in animation CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
`;
document.head.appendChild(style);