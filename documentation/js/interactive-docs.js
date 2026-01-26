// Enhanced API Documentation Interactive System with Live API Testing
class ApiDocumentationSystem {
    constructor() {
        this.credentials = {
            username: 'apploqic',
            password: 'apploqic'
        };
        
        this.apiEndpoints = this.getApiEndpoints();
        this.currentVersion = 'v1';
        this.baseApiUrl = this.getBaseApiUrl();
        this.init();
    }

    init() {
        this.bindEvents();
        this.checkAuthentication();
        this.loadEndpointDetails();
    }

    getBaseApiUrl() {
        // Detect environment and set appropriate base URL
        const hostname = window.location.hostname;
        if (hostname === 'localhost' || hostname === '127.0.0.1') {
            return window.location.origin + '/Apploqic_Business_Directory/public/api-v1.php';
        }
        // For production apploqic.my, API is at root index.php
        return window.location.origin + '/index.php';
    }

    bindEvents() {
        // Login form
        document.getElementById('loginForm').addEventListener('submit', (e) => this.handleLogin(e));
        
        // Logout button
        document.getElementById('logoutBtn').addEventListener('click', () => this.handleLogout());
        
        // Version filter
        document.getElementById('versionFilter').addEventListener('change', (e) => this.handleVersionChange(e));
        
        // Endpoint navigation
        document.querySelectorAll('.endpoint-link').forEach(link => {
            link.addEventListener('click', (e) => this.handleEndpointClick(e));
        });
    }

    checkAuthentication() {
        if (sessionStorage.getItem('apploqic_authenticated') === 'true') {
            this.showMainInterface();
        }
    }

    handleLogin(e) {
        e.preventDefault();
        
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        
        if (username === this.credentials.username && password === this.credentials.password) {
            sessionStorage.setItem('apploqic_authenticated', 'true');
            this.showMainInterface();
            this.showWelcomeMessage();
        } else {
            this.showError('Invalid credentials. Please check your username and password.');
        }
    }

    handleLogout() {
        sessionStorage.removeItem('apploqic_authenticated');
        this.showLoginInterface();
    }

    handleVersionChange(e) {
        this.currentVersion = e.target.value;
        this.updateEndpointList();
        this.showEndpoint('overview');
    }

    handleEndpointClick(e) {
        e.preventDefault();
        const endpoint = e.target.closest('.endpoint-link').getAttribute('data-endpoint');
        this.showEndpoint(endpoint);
        
        // Update active state
        document.querySelectorAll('.endpoint-link').forEach(l => l.classList.remove('active'));
        e.target.closest('.endpoint-link').classList.add('active');
    }

    showMainInterface() {
        document.getElementById('loginContainer').style.display = 'none';
        document.getElementById('mainContainer').style.display = 'block';
        this.updateApiStats();
    }

    showLoginInterface() {
        document.getElementById('mainContainer').style.display = 'none';
        document.getElementById('loginContainer').style.display = 'flex';
        document.getElementById('username').value = '';
        document.getElementById('password').value = '';
        this.hideError();
    }

    showError(message) {
        const errorElement = document.getElementById('errorMessage');
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }

    hideError() {
        document.getElementById('errorMessage').style.display = 'none';
    }

    showWelcomeMessage() {
        // Add welcome animation or notification
        const welcomeMsg = document.querySelector('.welcome-message');
        welcomeMsg.style.animation = 'none';
        setTimeout(() => {
            welcomeMsg.style.animation = 'fadeInUp 0.6s ease-out';
        }, 100);
    }

    showEndpoint(endpointId) {
        // Hide all endpoint details
        document.querySelectorAll('.endpoint-details').forEach(detail => {
            detail.classList.remove('active');
        });
        
        // Show selected endpoint
        const targetEndpoint = document.getElementById(endpointId);
        if (targetEndpoint) {
            targetEndpoint.classList.add('active');
        } else {
            // Generate endpoint details if not exists
            this.generateEndpointDetail(endpointId);
        }
    }

    updateApiStats() {
        const stats = this.getApiStats();
        document.querySelector('.stat-card:nth-child(1) .stat-number').textContent = stats.endpoints;
        document.querySelector('.stat-card:nth-child(2) .stat-number').textContent = stats.version;
        document.querySelector('.stat-card:nth-child(3) .stat-number').textContent = stats.type;
        document.querySelector('.stat-card:nth-child(4) .stat-number').textContent = stats.format;
    }

    updateEndpointList() {
        const endpointList = document.querySelector('.endpoint-list');
        const filteredEndpoints = this.apiEndpoints.filter(endpoint => 
            endpoint.version === this.currentVersion
        );
        
        // Update endpoint list based on version
        endpointList.style.opacity = '0.7';
        setTimeout(() => {
            endpointList.style.opacity = '1';
        }, 300);
    }

    loadEndpointDetails() {
        this.apiEndpoints.forEach(endpoint => {
            this.generateEndpointDetail(endpoint.id);
        });
    }

    generateEndpointDetail(endpointId) {
        const endpoint = this.apiEndpoints.find(ep => ep.id === endpointId);
        if (!endpoint || document.getElementById(endpointId)) return;

        const mainContent = document.querySelector('.main-content');
        const detailsHtml = this.createEndpointDetailsHtml(endpoint);
        mainContent.insertAdjacentHTML('beforeend', detailsHtml);

        // Bind API test form events
        this.bindApiTestEvents(endpointId);
    }

    createEndpointDetailsHtml(endpoint) {
        const methodClass = `method-${endpoint.method.toLowerCase()}`;
        
        return `
            <div id="${endpoint.id}" class="endpoint-details">
                <div class="endpoint-header">
                    <span class="method-tag ${methodClass}">${endpoint.method}</span>
                    <h3 class="endpoint-title">${endpoint.title}</h3>
                </div>
                
                <div class="endpoint-url">${endpoint.method} ${endpoint.url}</div>
                
                <div class="section">
                    <h4>📋 Description</h4>
                    <p>${endpoint.description}</p>
                </div>

                ${this.generateParametersSection(endpoint.params)}
                ${this.generateApiTesterSection(endpoint)}
                ${this.generateResponseSection(endpoint.response)}
                ${endpoint.example ? this.generateExampleSection(endpoint.example) : ''}
            </div>
        `;
    }

    generateParametersSection(params) {
        if (!params || params.length === 0) return '';
        
        const paramsRows = params.map(param => `
            <tr>
                <td>${param.name}</td>
                <td>${param.type}</td>
                <td>${param.required ? 'Yes' : 'No'}</td>
                <td>${param.description}</td>
            </tr>
        `).join('');

        return `
            <div class="section">
                <h4>🔧 Parameters</h4>
                <table class="params-table">
                    <thead>
                        <tr>
                            <th>Parameter</th>
                            <th>Type</th>
                            <th>Required</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${paramsRows}
                    </tbody>
                </table>
            </div>
        `;
    }

    generateApiTesterSection(endpoint) {
        if (endpoint.id === 'overview') return '';

        const testFormFields = this.generateTestFormFields(endpoint);
        
        return `
            <div class="section">
                <h4>🧪 API Tester</h4>
                <div class="api-tester">
                    <h5>Test this endpoint with live data</h5>
                    <form id="test-form-${endpoint.id}" class="test-form">
                        ${testFormFields}
                        <div style="display: flex; gap: 0.5rem;">
                            <button type="submit" class="test-btn" id="test-btn-${endpoint.id}">
                                <span class="btn-text">Send Request</span>
                            </button>
                            <button type="button" class="test-btn clear-btn" onclick="apiDocs.clearTestForm('${endpoint.id}')">
                                Clear
                            </button>
                        </div>
                    </form>
                    <div id="response-container-${endpoint.id}" class="response-container" style="display: none;">
                        <div class="response-header">
                            <div>
                                <span id="response-status-${endpoint.id}" class="response-status"></span>
                            </div>
                            <div id="response-time-${endpoint.id}" class="response-time"></div>
                        </div>
                        <div id="response-body-${endpoint.id}" class="response-body"></div>
                    </div>
                </div>
            </div>
        `;
    }

    generateTestFormFields(endpoint) {
        if (!endpoint.params || endpoint.params.length === 0) {
            return '<p style="color: var(--text-secondary); font-style: italic;">No parameters required for this endpoint.</p>';
        }

        return endpoint.params.map(param => {
            const inputId = `param-${endpoint.id}-${param.name}`;
            let inputElement;

            switch (param.type) {
                case 'boolean':
                    inputElement = `
                        <select id="${inputId}" name="${param.name}">
                            <option value="">Select...</option>
                            <option value="1">True</option>
                            <option value="0">False</option>
                        </select>
                    `;
                    break;
                case 'text':
                    inputElement = `<textarea id="${inputId}" name="${param.name}" rows="3" placeholder="${param.description}"></textarea>`;
                    break;
                case 'file':
                    inputElement = `<input type="file" id="${inputId}" name="${param.name}" accept="image/*">`;
                    break;
                default:
                    inputElement = `<input type="text" id="${inputId}" name="${param.name}" placeholder="${param.description}">`;
            }

            return `
                <div class="test-input-group">
                    <label for="${inputId}">
                        ${param.name} ${param.required ? '<span style="color: var(--accent-orange);">*</span>' : ''}
                        <small style="color: var(--text-muted);">(${param.type})</small>
                    </label>
                    ${inputElement}
                </div>
            `;
        }).join('');
    }

    bindApiTestEvents(endpointId) {
        const form = document.getElementById(`test-form-${endpointId}`);
        if (form) {
            form.addEventListener('submit', (e) => this.handleApiTest(e, endpointId));
        }
    }

    async handleApiTest(e, endpointId) {
        e.preventDefault();
        
        const endpoint = this.apiEndpoints.find(ep => ep.id === endpointId);
        if (!endpoint) return;

        const testBtn = document.getElementById(`test-btn-${endpointId}`);
        const responseContainer = document.getElementById(`response-container-${endpointId}`);
        const responseStatus = document.getElementById(`response-status-${endpointId}`);
        const responseTime = document.getElementById(`response-time-${endpointId}`);
        const responseBody = document.getElementById(`response-body-${endpointId}`);

        // Show loading state
        testBtn.disabled = true;
        testBtn.innerHTML = '<span class="loading-spinner"></span>Sending...';
        responseContainer.style.display = 'none';

        try {
            const startTime = Date.now();
            
            // Create a clean FormData with only meaningful values
            const originalForm = e.target;
            const formData = new FormData();
            let hasFile = false; // Track if any file inputs are present for proper upload
            
            // Process each form input manually - include ALL fields for partial update support
            const formInputs = originalForm.querySelectorAll('input, select, textarea');
            
            console.log('Processing all form inputs for partial update:');
            formInputs.forEach(input => {
                if (input.name) {
                    if (input.type === 'file') {
                        // Debug file input detection
                        console.log(`DEBUGGING FILE INPUT: ${input.name}`);
                        console.log(`  Files array:`, input.files);
                        console.log(`  Files count:`, input.files ? input.files.length : 'null');
                        console.log(`  First file:`, input.files && input.files[0]);
                        if (input.files && input.files[0]) {
                            console.log(`  File name:`, input.files[0].name);
                            console.log(`  File size:`, input.files[0].size);
                        }
                        
                        // Send the actual file for upload to public/images/
                        if (input.files && input.files.length > 0 && input.files[0].size > 0) {
                            formData.append(input.name, input.files[0]);
                            hasFile = true; // Mark that we have a file to upload
                            console.log(`  ${input.name}: [File] ${input.files[0].name} (${input.files[0].size} bytes) - hasFile set to TRUE`);
                        } else {
                            // Still include empty file field to maintain form structure
                            formData.append(input.name, '');
                            console.log(`  ${input.name}: [No file selected - sending empty]`);
                        }
                    } else {
                        // Include ALL non-file inputs (server will handle which to update)
                        formData.append(input.name, input.value || '');
                        console.log(`  ${input.name}: "${input.value || ''}" (type: ${input.type})`);
                    }
                }
            });
            
            // Always include ID for update operations
            const idInput = originalForm.querySelector('input[name="id"]');
            if (idInput && idInput.value) {
                if (!formData.has('id')) {
                    formData.append('id', idInput.value);
                    console.log(`  id: "${idInput.value}" (added for update operation)`);
                }
            }
            
            console.log('Final FormData entries being sent:');
            console.log('FINAL hasFile flag:', hasFile);
            for (const [key, value] of formData.entries()) {
                if (value instanceof File) {
                    console.log(`  ${key}: [File] ${value.name} (${value.size} bytes)`);
                } else {
                    console.log(`  ${key}: "${value}"`);
                }
            }

            console.log('CALLING makeApiRequest with hasFile =', hasFile);
            const response = await this.makeApiRequest(endpoint, formData, hasFile);
            const endTime = Date.now();

            // Show response
            responseContainer.style.display = 'block';
            responseStatus.textContent = `${response.status} ${response.statusText}`;
            responseStatus.className = `response-status ${response.ok ? 'status-success' : 'status-error'}`;
            responseTime.textContent = `${endTime - startTime}ms`;
            
            const responseText = await response.text();
            let formattedResponse;
            
            try {
                const jsonData = JSON.parse(responseText);
                formattedResponse = JSON.stringify(jsonData, null, 2);
            } catch {
                formattedResponse = responseText;
            }
            
            responseBody.textContent = formattedResponse;
            this.highlightJson(responseBody);

        } catch (error) {
            responseContainer.style.display = 'block';
            responseStatus.textContent = 'Network Error';
            responseStatus.className = 'response-status status-error';
            responseTime.textContent = 'Failed';
            responseBody.textContent = `Error: ${error.message}`;
        } finally {
            // Reset button state
            testBtn.disabled = false;
            testBtn.innerHTML = '<span class="btn-text">Send Request</span>';
        }
    }

    async makeApiRequest(endpoint, formData, hasFile = false) {
        console.log('=== makeApiRequest called ===');
        console.log('hasFile parameter:', hasFile);
        console.log('formData type:', formData.constructor.name);
        
        let url = `${this.baseApiUrl}${endpoint.url}`;
        let options = {
            method: endpoint.method,
            headers: {}
        };

        // Handle different request methods
        if (endpoint.method === 'GET') {
            const params = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                if (value && value.trim() !== '') params.append(key, value);
            }
            if (params.toString()) {
                url += `?${params.toString()}`;
            }
        } else {
            // For PUT/PATCH operations, we need at least the ID to identify the record
            // The server will handle validation if no fields are provided for update
            const hasId = formData.has('id') && formData.get('id') && formData.get('id').trim() !== '';
            
            if ((endpoint.method === 'PUT' || endpoint.method === 'PATCH') && !hasId) {
                throw new Error('ID is required for update operations.');
            }
            
            console.log('About to check hasFile:', hasFile);
            console.log('Current formData entries:');
            for (const [key, value] of formData.entries()) {
                if (value instanceof File) {
                    console.log(`  ${key}: [File] ${value.name} (${value.size} bytes)`);
                } else {
                    console.log(`  ${key}: "${value}"`);
                }
            }
            
            if (hasFile) {
                // WORKAROUND: For PUT/PATCH with files, use POST with _method override
                // Many browsers/servers don't handle PUT + multipart/form-data correctly
                if (endpoint.method === 'PUT' || endpoint.method === 'PATCH') {
                    console.log('FILE UPLOAD WORKAROUND: Converting PUT to POST with _method override');
                    formData.append('_method', endpoint.method); // Add method override
                    options.method = 'POST'; // Change to POST for file upload
                }
                
                console.log('FILE UPLOAD MODE: Using FormData for multipart/form-data');
                console.log('Method override:', options.method);
                console.log('FormData entries before sending:');
                for (const [key, value] of formData.entries()) {
                    if (value instanceof File) {
                        console.log(`  ${key}: [File] ${value.name} (${value.size} bytes)`);
                    } else {
                        console.log(`  ${key}: "${value}"`);
                    }
                }
                options.body = formData;
                // Don't set Content-Type header - let browser set it with boundary
            } else {
                // Use JSON for regular data
                console.log('JSON MODE: Converting FormData to JSON');
                options.headers['Content-Type'] = 'application/json';
                const jsonData = {};
                
                // Debug: Log all form data entries
                console.log('Form data entries for JSON conversion:');
                for (const [key, value] of formData.entries()) {
                    console.log(`  ${key}: "${value}" (type: ${typeof value}, instanceof File: ${value instanceof File})`);
                    
                    // CRITICAL: Don't convert File objects to JSON!
                    if (value instanceof File) {
                        console.error(`ERROR: File object found in JSON conversion! This should use multipart/form-data!`);
                        throw new Error('File upload detected but hasFile is false. This is a bug.');
                    }
                    
                    // Include ALL fields (server will handle partial update logic)
                    jsonData[key] = value;
                }
                
                console.log('Final JSON data for update:', jsonData);
                options.body = JSON.stringify(jsonData);
                console.log('JSON body being sent:', options.body);
            }
        }

        // Handle URL parameters (like {id}) - check formData first
        let hasUrlParams = false;
        for (const [key, value] of formData.entries()) {
            if (value && url.includes(`{${key}}`)) {
                console.log(`Found URL parameter: ${key} = ${value}`);
                url = url.replace(`{${key}}`, value);
                hasUrlParams = true;
            }
        }
        
        // Only remove URL parameters from body AFTER we've set the body type
        if (hasUrlParams) {
            if (hasFile) {
                // For file uploads, remove URL parameters from FormData but keep as FormData
                console.log('File upload mode: removing URL parameters from FormData');
                const newFormData = new FormData();
                for (const [key, value] of formData.entries()) {
                    if (!url.includes(key)) { // Only keep if not used in URL
                        newFormData.append(key, value);
                    }
                }
                options.body = newFormData;
            } else {
                // For JSON mode, remove URL parameters from JSON body
                console.log('JSON mode: removing URL parameters from JSON');
                if (options.body && typeof options.body === 'string') {
                    const bodyData = JSON.parse(options.body);
                    for (const [key] of formData.entries()) {
                        if (url.includes(key)) {
                            delete bodyData[key];
                        }
                    }
                    options.body = JSON.stringify(bodyData);
                }
            }
        }
        
        // For PUT/PATCH with query parameters (backward compatibility)
        if ((endpoint.method === 'PUT' || endpoint.method === 'PATCH') && url.includes('?')) {
            // URL already has query parameters, keep them
        } else if (endpoint.method === 'PUT' || endpoint.method === 'PATCH') {
            // Add id as query parameter for update endpoints
            const id = formData.get('id');
            if (id) {
                url += `?id=${id}`;
                // Remove id from body to avoid duplication
                if (options.body instanceof FormData) {
                    formData.delete('id');
                } else if (options.body && typeof options.body === 'string') {
                    const bodyData = JSON.parse(options.body);
                    delete bodyData.id;
                    options.body = JSON.stringify(bodyData);
                }
            }
        }

        console.log('Final request details:');
        console.log('URL:', url);
        console.log('Method:', options.method);
        console.log('Headers:', options.headers);
        console.log('Body type:', options.body ? options.body.constructor.name : 'none');
        if (options.body instanceof FormData) {
            console.log('FormData entries:');
            for (const [key, value] of options.body.entries()) {
                if (value instanceof File) {
                    console.log(`  ${key}: [File] ${value.name} (${value.size} bytes)`);
                } else {
                    console.log(`  ${key}: "${value}"`);
                }
            }
        }

        return await fetch(url, options);
    }

    clearTestForm(endpointId) {
        const form = document.getElementById(`test-form-${endpointId}`);
        const responseContainer = document.getElementById(`response-container-${endpointId}`);
        
        if (form) form.reset();
        if (responseContainer) responseContainer.style.display = 'none';
    }

    highlightJson(element) {
        let content = element.textContent;
        
        // Simple JSON syntax highlighting
        content = content
            .replace(/"([^"]+)":/g, '<span class="json-key">"$1":</span>')
            .replace(/: "([^"]+)"/g, ': <span class="json-string">"$1"</span>')
            .replace(/: (\d+)/g, ': <span class="json-number">$1</span>')
            .replace(/: (true|false)/g, ': <span class="json-boolean">$1</span>')
            .replace(/: null/g, ': <span class="json-null">null</span>');
        
        element.innerHTML = content;
    }

    generateResponseSection(response) {
        if (!response) return '';
        
        return `
            <div class="section">
                <h4>📤 Example Response</h4>
                <pre class="response-example">${JSON.stringify(response, null, 2)}</pre>
            </div>
        `;
    }

    generateExampleSection(example) {
        return `
            <div class="section">
                <h4>💡 Usage Example</h4>
                <pre class="response-example">${example}</pre>
            </div>
        `;
    }

    getApiStats() {
        return {
            endpoints: this.apiEndpoints.length,
            version: 'v1.0',
            type: 'REST',
            format: 'JSON'
        };
    }

    getApiEndpoints() {
        return [
            {
                id: 'business-index',
                title: 'Get All Businesses',
                method: 'GET',
                url: '/api/v1/business',
                version: 'v1',
                description: 'Retrieve a paginated list of all business listings with optional filtering capabilities.',
                params: [
                    { name: 'page', type: 'integer', required: false, description: 'Page number for pagination (default: 1)' },
                    { name: 'category', type: 'string', required: false, description: 'Filter by business category' },
                    { name: 'featured', type: 'boolean', required: false, description: 'Filter featured businesses (1 or 0)' },
                    { name: 'status', type: 'string', required: false, description: 'Filter by status: "active" or "inactive"' }
                ],
                response: {
                    "status": "success",
                    "data": [
                        {
                            "id": 1,
                            "business_name": "Sample Restaurant",
                            "business_contact": "123-456-7890",
                            "business_description": "Great food and atmosphere",
                            "business_category": "restaurant",
                            "business_img_url": "https://example.com/images/sample.jpg",
                            "status": 1,
                            "is_featured": 1,
                            "created_at": "2024-11-01 10:00:00",
                            "expiry_date": "2024-12-01 10:00:00"
                        }
                    ],
                    "pagination": {
                        "current_page": 1,
                        "per_page": 10,
                        "total": 25,
                        "total_pages": 3
                    },
                    "filters_applied": {
                        "category": "",
                        "status": "active",
                        "featured": null
                    },
                    "api_version": "v1"
                }
            },
            {
                id: 'business-show',
                title: 'Get Business by ID',
                method: 'GET',
                url: '/api/v1/business/{id}',
                version: 'v1',
                description: 'Retrieve detailed information about a specific business listing by its unique identifier.',
                params: [
                    { name: 'id', type: 'integer', required: true, description: 'Unique business identifier in URL path' }
                ],
                response: {
                    "status": "success",
                    "data": {
                        "id": 1,
                        "business_name": "Sample Restaurant",
                        "business_contact": "123-456-7890",
                        "business_description": "Great food and atmosphere",
                        "business_category": "restaurant",
                        "business_img_url": "https://example.com/images/sample.jpg",
                        "status": 1,
                        "is_featured": 1,
                        "created_at": "2024-11-01 10:00:00",
                        "expiry_date": "2024-12-01 10:00:00",
                        "reactivated_at": null
                    },
                    "api_version": "v1"
                }
            },
            {
                id: 'business-search',
                title: 'Search Businesses',
                method: 'GET',
                url: '/api/v1/search',
                version: 'v1',
                description: 'Search and filter business listings using various criteria. Enhanced v1.1.0 with expiry tracking and empty search showing all businesses.',
                params: [
                    { name: 'q', type: 'string', required: false, description: 'Search query for business name or description' },
                    { name: 'name', type: 'string', required: false, description: 'Alternative search parameter for business name' },
                    { name: 'search', type: 'string', required: false, description: 'Alternative search parameter' },
                    { name: 'category', type: 'string', required: false, description: 'Filter by business category' },
                    { name: 'status', type: 'string', required: false, description: 'Filter by status: active or inactive (default: active)' },
                    { name: 'featured', type: 'boolean', required: false, description: 'Filter featured businesses only' },
                    { name: 'expired', type: 'boolean', required: false, description: 'Include expired businesses (default: false)' }
                ],
                response: {
                    "status": "success",
                    "data": [
                        {
                            "id": 1,
                            "business_name": "Pizza Palace",
                            "business_contact": "555-0123",
                            "business_category": "restaurant",
                            "business_img_url": "https://example.com/images/pizza.jpg",
                            "status": 1,
                            "is_featured": true,
                            "is_expired": false,
                            "days_until_expiry": 15,
                            "created_at": "2024-10-15 10:00:00"
                        }
                    ],
                    "search_criteria": {
                        "search_term": "pizza",
                        "category": "",
                        "status": "active",
                        "featured_only": null,
                        "include_expired": false
                    },
                    "results_count": 1,
                    "pagination": {
                        "current_page": 1,
                        "per_page": 10
                    },
                    "api_version": "v1",
                    "enhanced_features": {
                        "category_filtering": true,
                        "expiry_tracking": true,
                        "featured_prioritization": true,
                        "empty_search_shows_all": true
                    }
                }
            },
            {
                id: 'business-store',
                title: 'Create Business',
                method: 'POST',
                url: '/api/v1/business',
                version: 'v1',
                description: 'Create a new business listing with enhanced v1.1.0 features including 30-day expiry tracking. Supports file upload for business images.',
                params: [
                    { name: 'business_name', type: 'string', required: true, description: 'Name of the business' },
                    { name: 'business_contact', type: 'string', required: true, description: 'Business contact information' },
                    { name: 'business_description', type: 'text', required: false, description: 'Detailed business description' },
                    { name: 'business_category', type: 'string', required: false, description: 'Business category' },
                    { name: 'business_img', type: 'file', required: false, description: 'Business image file upload (JPEG, PNG, GIF, WebP - Max 5MB)' },
                    { name: 'is_featured', type: 'boolean', required: false, description: 'Mark as featured business' }
                ],
                response: {
                    "status": "success",
                    "message": "Business created successfully with 30-day expiry",
                    "business_id": 25,
                    "data": {
                        "id": 25,
                        "business_name": "New Business",
                        "business_contact": "555-9999",
                        "business_category": "retail",
                        "status": 1,
                        "is_featured": 0,
                        "created_at": "2024-11-09 10:30:00",
                        "expiry_date": "2024-12-09 10:30:00"
                    },
                    "expiry_info": {
                        "expires_in_days": 30,
                        "expiry_date": "2024-12-09 10:30:00"
                    },
                    "api_version": "v1"
                }
            },
            {
                id: 'business-update',
                title: 'Update Business',
                method: 'PUT',
                url: '/api/v1/business',
                version: 'v1',
                description: 'Update an existing business listing with new information. Supports file upload for business images.',
                params: [
                    { name: 'id', type: 'integer', required: true, description: 'Business ID to update' },
                    { name: 'business_name', type: 'string', required: false, description: 'Updated business name' },
                    { name: 'business_contact', type: 'string', required: false, description: 'Updated business contact' },
                    { name: 'business_category', type: 'string', required: false, description: 'Updated business category' },
                    { name: 'business_description', type: 'text', required: false, description: 'Updated business description' },
                    { name: 'business_img', type: 'file', required: false, description: 'Updated business image file (JPEG, PNG, GIF, WebP - Max 5MB)' },
                    { name: 'is_featured', type: 'boolean', required: false, description: 'Update featured status' }
                ],
                response: {
                    "status": "success",
                    "message": "Business updated successfully",
                    "business_id": 1,
                    "data": {
                        "id": 1,
                        "business_name": "Updated Business Name",
                        "business_contact": "555-0000",
                        "updated_at": "2024-11-09 10:35:00"
                    },
                    "api_version": "v1"
                }
            },
            {
                id: 'business-delete',
                title: 'Deactivate Business',
                method: 'DELETE',
                url: '/api/v1/business',
                version: 'v1',
                description: 'Deactivate a business listing by setting its status to inactive.',
                params: [
                    { name: 'id', type: 'integer', required: true, description: 'Business ID to deactivate' },
                    { name: 'reason', type: 'string', required: false, description: 'Reason for deactivation (default: Non-payment of monthly fee)' }
                ],
                response: {
                    "status": "success",
                    "message": "Business deactivated successfully",
                    "business_id": 1,
                    "reason": "Non-payment of monthly fee",
                    "api_version": "v1"
                }
            },
            {
                id: 'analytics',
                title: 'Get Analytics',
                method: 'GET',
                url: '/api/v1/analytics',
                version: 'v1',
                description: 'Retrieve analytics and statistics about the business directory. Admin access required.',
                params: [],
                response: {
                    "status": "success",
                    "data": {
                        "total_businesses": 150,
                        "active_businesses": 142,
                        "inactive_businesses": 8,
                        "featured_businesses": 25,
                        "activation_rate": 94.67,
                        "featured_rate": 16.67
                    },
                    "api_version": "v1",
                    "generated_at": "2024-11-09 10:40:00"
                }
            },
            {
                id: 'reactivate',
                title: 'Reactivate Business',
                method: 'PUT',
                url: '/api/v1/reactivate/{id}',
                version: 'v1',
                description: 'Reactivate a previously deactivated business listing with enhanced 30-day expiry tracking. Supports both URL path parameter and query parameter for business ID.',
                params: [
                    { name: 'id', type: 'integer', required: true, description: 'Business ID to reactivate (can be passed in URL path or as query parameter)' }
                ],
                response: {
                    "status": "success",
                    "message": "Business reactivated successfully for 30 days",
                    "business_id": 1,
                    "data": {
                        "id": 1,
                        "business_name": "Reactivated Business",
                        "business_contact": "555-1234",
                        "status": 1,
                        "reactivated_at": "2024-11-09 10:40:00",
                        "expiry_date": "2024-12-09 10:40:00"
                    },
                    "expiry_info": {
                        "reactivated_at": "2024-11-09 10:40:00",
                        "expires_at": "2024-12-09 10:40:00",
                        "days_remaining": 30
                    },
                    "api_version": "v1"
                }
            }
        ];
    }
}

// Global instance
let apiDocs;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    apiDocs = new ApiDocumentationSystem();
});