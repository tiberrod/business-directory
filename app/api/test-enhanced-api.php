<?php
/**
 * Enhanced Business API Test Suite v1.1.0
 * 
 * Comprehensive test file for all enhanced business API features including:
 * - 30-day expiry management
 * - Reactivation tracking
 * - Enhanced search with category filtering
 * - Featured status management
 * - Image URL management
 */

// Base URL for API testing
$baseUrl = 'http://localhost/Apploqic_Business_Directory/app/api/router.php';

// Enhanced test endpoints
$tests = [
    'Enhanced Features' => [
        'Create Business with 30-day Expiry' => [
            'url' => $baseUrl . '?version=v1&endpoint=businesses&action=create',
            'method' => 'POST',
            'headers' => ['X-User-Role: admin', 'Content-Type: application/json'],
            'data' => [
                'business_name' => 'Test Enhanced Business',
                'business_contact' => '555-0123',
                'business_description' => 'Test business with enhanced features',
                'category' => 'Technology',
                'image_url' => 'https://example.com/image.jpg',
                'featured' => true
            ],
            'description' => 'Create business with automatic 30-day expiry and enhanced fields'
        ],
        
        'Enhanced Search - All Businesses' => [
            'url' => $baseUrl . '?version=v1&endpoint=businesses&action=search',
            'method' => 'GET',
            'headers' => ['X-User-Role: user'],
            'description' => 'Search with empty term (should return all businesses)'
        ],
        
        'Enhanced Search - Category Filter' => [
            'url' => $baseUrl . '?version=v1&endpoint=businesses&action=search&category=Technology&featured=true',
            'method' => 'GET',
            'headers' => ['X-User-Role: user'],
            'description' => 'Search businesses by category and featured status'
        ],
        
        'Enhanced Search - Specific Term' => [
            'url' => $baseUrl . '?version=v1&endpoint=businesses&action=search&search=Enhanced&status=active',
            'method' => 'GET',
            'headers' => ['X-User-Role: user'],
            'description' => 'Search businesses with specific term and status filter'
        ],
        
        'Set Featured Status' => [
            'url' => $baseUrl . '?version=v1&endpoint=businesses&action=set-featured&id=1',
            'method' => 'PUT',
            'headers' => ['X-User-Role: admin', 'Content-Type: application/json'],
            'data' => ['featured' => true],
            'description' => 'Set business as featured using new endpoint'
        ],
        
        'Update Image URL' => [
            'url' => $baseUrl . '?version=v1&endpoint=businesses&action=update-image&id=1',
            'method' => 'PUT',
            'headers' => ['X-User-Role: admin', 'Content-Type: application/json'],
            'data' => ['image_url' => 'https://example.com/new-image.jpg'],
            'description' => 'Update business image URL using new endpoint'
        ],
        
        'Reactivate with New Expiry' => [
            'url' => $baseUrl . '?version=v1&endpoint=businesses&action=reactivate&id=1',
            'method' => 'PUT',
            'headers' => ['X-User-Role: admin'],
            'description' => 'Reactivate business with new 30-day expiry period'
        ],
        
        'Get Expiring Businesses' => [
            'url' => $baseUrl . '?version=v1&endpoint=businesses&action=expiring-soon&days=30',
            'method' => 'GET',
            'headers' => ['X-User-Role: admin'],
            'description' => 'Get businesses expiring in next 30 days'
        ],
        
        'Auto-Deactivate Expired' => [
            'url' => $baseUrl . '?version=v1&endpoint=businesses&action=auto-deactivate',
            'method' => 'POST',
            'headers' => ['X-User-Role: admin'],
            'description' => 'Auto-deactivate businesses that have expired'
        ]
    ],
    
    'Standard API Endpoints' => [
        'List All Businesses' => [
            'url' => $baseUrl . '?version=v1&endpoint=businesses&action=list&limit=5',
            'method' => 'GET',
            'headers' => ['X-User-Role: user'],
            'description' => 'Get all businesses with enhanced expiry info'
        ],
        
        'Get Single Business' => [
            'url' => $baseUrl . '?version=v1&endpoint=businesses&action=details&id=1',
            'method' => 'GET',
            'headers' => ['X-User-Role: user'],
            'description' => 'Get single business with enhanced fields'
        ],
        
        'Get Featured Businesses' => [
            'url' => $baseUrl . '?version=v1&endpoint=businesses&action=featured',
            'method' => 'GET',
            'headers' => ['X-User-Role: user'],
            'description' => 'Get featured businesses with new featured_at field'
        ],
        
        'Get Analytics' => [
            'url' => $baseUrl . '?version=v1&endpoint=businesses&action=analytics',
            'method' => 'GET',
            'headers' => ['X-User-Role: admin'],
            'description' => 'Get business analytics with enhanced data'
        ]
    ],
    
    'Documentation API' => [
        'API Version Info' => [
            'url' => $baseUrl . '?version=v1&endpoint=documentation&action=version',
            'method' => 'GET',
            'headers' => [],
            'description' => 'Get current API version (should show v1.1.0)'
        ],
        
        'Enhanced Patch Notes' => [
            'url' => $baseUrl . '?version=v1&endpoint=documentation&action=patches&version=v1.1.0',
            'method' => 'GET',
            'headers' => [],
            'description' => 'Get v1.1.0 patch notes with new features'
        ],
        
        'Updated Endpoints List' => [
            'url' => $baseUrl . '?version=v1&endpoint=documentation&action=endpoints',
            'method' => 'GET',
            'headers' => [],
            'description' => 'Get all endpoints including new enhanced ones'
        ]
    ]
];

// HTML Output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Business API Test Suite v1.1.0</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        .version-badge {
            background: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            display: inline-block;
            margin-bottom: 20px;
        }
        .feature-highlight {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        .category {
            margin-bottom: 40px;
        }
        .category-title {
            background: #007bff;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            font-size: 1.3em;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .test-section {
            margin-bottom: 25px;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .test-header {
            background: #f8f9fa;
            color: #495057;
            padding: 15px 20px;
            margin: 0;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .test-method {
            background: #6c757d;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.8em;
            font-weight: bold;
        }
        .method-POST { background: #28a745; }
        .method-PUT { background: #ffc107; color: #212529; }
        .method-DELETE { background: #dc3545; }
        .method-GET { background: #17a2b8; }
        
        .test-content {
            padding: 20px;
        }
        .test-url {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 5px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            word-break: break-all;
            font-size: 0.9em;
        }
        .test-button {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
            font-weight: bold;
        }
        .test-button:hover {
            background: #218838;
        }
        .enhanced-button {
            background: #e91e63;
            color: white;
        }
        .enhanced-button:hover {
            background: #c2185b;
        }
        .response-area {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-top: 15px;
            max-height: 500px;
            overflow-y: auto;
            display: none;
        }
        .loading {
            color: #007bff;
            font-style: italic;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .test-all-btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-bottom: 30px;
            font-size: 18px;
            font-weight: bold;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .test-all-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .data-preview {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            border-radius: 3px;
            margin: 8px 0;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Enhanced Business API Test Suite</h1>
        <div class="version-badge">v1.1.0 - Enhanced Features</div>
        
        <div class="feature-highlight">
            <h3>🌟 New Enhanced Features in v1.1.0:</h3>
            <ul>
                <li><strong>30-Day Active Period:</strong> Automatic expiry tracking with <code>expiry_at</code> field</li>
                <li><strong>Reactivation Tracking:</strong> Track reactivations with <code>reactivated_at</code> field</li>
                <li><strong>Enhanced Featured Status:</strong> DateTime-based featured tracking with <code>featured_at</code></li>
                <li><strong>Image URL Management:</strong> Dedicated <code>image_url</code> field for better image handling</li>
                <li><strong>Enhanced Search:</strong> Category filtering, empty search shows all businesses</li>
                <li><strong>New Endpoints:</strong> Featured management, image updates, expiry monitoring</li>
            </ul>
        </div>
        
        <button class="test-all-btn" onclick="testAllEndpoints()">🧪 Test All Enhanced Features</button>
        
        <?php foreach ($tests as $categoryName => $categoryTests): ?>
        <div class="category">
            <div class="category-title"><?= htmlspecialchars($categoryName) ?></div>
            
            <?php foreach ($categoryTests as $testName => $testData): ?>
            <div class="test-section">
                <div class="test-header">
                    <span><?= htmlspecialchars($testName) ?></span>
                    <span class="test-method method-<?= $testData['method'] ?>"><?= $testData['method'] ?></span>
                </div>
                <div class="test-content">
                    <p><strong>Description:</strong> <?= htmlspecialchars($testData['description']) ?></p>
                    <div class="test-url"><?= htmlspecialchars($testData['url']) ?></div>
                    
                    <?php if (!empty($testData['data'])): ?>
                    <div class="data-preview">
                        <strong>Request Data:</strong> <code><?= htmlspecialchars(json_encode($testData['data'])) ?></code>
                    </div>
                    <?php endif; ?>
                    
                    <button class="test-button <?= $categoryName === 'Enhanced Features' ? 'enhanced-button' : '' ?>" 
                            onclick="testEnhancedEndpoint('<?= addslashes($testData['url']) ?>', '<?= addslashes($testName) ?>', '<?= $testData['method'] ?>', <?= isset($testData['data']) ? "'" . addslashes(json_encode($testData['data'])) . "'" : 'null' ?>, <?= json_encode($testData['headers'] ?? []) ?>)">
                        🔬 Test Enhanced Endpoint
                    </button>
                    
                    <div id="response-<?= preg_replace('/[^a-zA-Z0-9]/', '', $testName . $categoryName) ?>" class="response-area">
                        <div class="loading">Loading...</div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
        
        <div class="feature-highlight">
            <h3>📋 Complete Enhanced API Endpoints Summary:</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <h4>🏢 Business Endpoints (Enhanced):</h4>
                    <ul>
                        <li><strong>GET</strong> /businesses - List with expiry info</li>
                        <li><strong>GET</strong> /businesses/{id} - Get with enhanced fields</li>
                        <li><strong>GET</strong> /businesses/search - Enhanced search with category filter</li>
                        <li><strong>POST</strong> /businesses - Create with 30-day expiry</li>
                        <li><strong>PUT</strong> /businesses/{id} - Update business</li>
                        <li><strong>PUT</strong> /businesses/reactivate - Reactivate with new expiry</li>
                        <li><strong>DELETE</strong> /businesses/{id} - Delete business</li>
                        <li><strong>GET</strong> /businesses/featured - Get featured businesses</li>
                        <li><strong>GET</strong> /businesses/analytics - Enhanced analytics</li>
                    </ul>
                </div>
                <div>
                    <h4>✨ New Enhanced Endpoints:</h4>
                    <ul>
                        <li><strong>PUT</strong> /businesses/set-featured - Set featured status</li>
                        <li><strong>PUT</strong> /businesses/update-image - Update image URL</li>
                        <li><strong>GET</strong> /businesses/expiring-soon - Get expiring businesses</li>
                        <li><strong>POST</strong> /businesses/auto-deactivate - Auto-deactivate expired</li>
                    </ul>
                    
                    <h4>📚 Documentation Endpoints:</h4>
                    <ul>
                        <li><strong>GET</strong> /documentation - Complete docs</li>
                        <li><strong>GET</strong> /documentation/version - Version info</li>
                        <li><strong>GET</strong> /documentation/patches - Changelog</li>
                        <li><strong>GET</strong> /documentation/endpoints - Endpoint list</li>
                        <li><strong>GET</strong> /documentation/health - Health status</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        function testEnhancedEndpoint(url, testName, method, data, headers) {
            const responseId = 'response-' + testName.replace(/[^a-zA-Z0-9]/g, '') + (testName.includes('Enhanced') ? 'Enhanced' : '');
            const responseDiv = document.getElementById(responseId);
            
            responseDiv.style.display = 'block';
            responseDiv.innerHTML = '<div class="loading">🔄 Testing enhanced endpoint...</div>';
            
            const options = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    ...Object.fromEntries(headers.map(h => h.split(': ')))
                }
            };
            
            if (data && method !== 'GET') {
                options.body = data;
            }
            
            fetch(url, options)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    let resultClass = 'success';
                    let resultIcon = '✅';
                    
                    if (data.status === 'error') {
                        resultClass = 'error';
                        resultIcon = '❌';
                    }
                    
                    responseDiv.innerHTML = `
                        <div class="${resultClass}">${resultIcon} ${data.status === 'success' ? 'Enhanced Feature Success!' : 'Error'}</div>
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                    `;
                })
                .catch(error => {
                    responseDiv.innerHTML = `
                        <div class="error">❌ Error: ${error.message}</div>
                        <p><strong>Troubleshooting:</strong></p>
                        <ul>
                            <li>Ensure XAMPP is running</li>
                            <li>Check database migration completed</li>
                            <li>Verify API router path is correct</li>
                            <li>Check if enhanced controller methods exist</li>
                        </ul>
                    `;
                });
        }
        
        function testAllEndpoints() {
            const buttons = document.querySelectorAll('.test-button');
            buttons.forEach((button, index) => {
                setTimeout(() => {
                    button.click();
                }, index * 2000); // 2 second intervals
            });
        }
    </script>
</body>
</html>