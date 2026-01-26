<?php
/**
 * Enhanced Business API Test v1.1.0
 * 
 * Test file for all enhanced business API features including:
 * - 30-day expiry management
 * - Reactivation tracking
 * - Featured status management
 * - Image URL management
 * - Enhanced search with category filtering
 */

// Base URL for API testing
$baseUrl = 'http://localhost/Apploqic_Business_Directory/app/api/router.php';

// Test endpoints for enhanced features
$enhancedTests = [
    'Enhanced Search (Empty)' => [
        'url' => $baseUrl . '?version=v1&endpoint=businesses&action=search',
        'description' => 'Search with empty term - should return all businesses',
        'method' => 'GET'
    ],
    'Enhanced Search (Category Filter)' => [
        'url' => $baseUrl . '?version=v1&endpoint=businesses&action=search&category=restaurant',
        'description' => 'Search businesses by category',
        'method' => 'GET'
    ],
    'Enhanced Search (Featured Only)' => [
        'url' => $baseUrl . '?version=v1&endpoint=businesses&action=search&featured=true',
        'description' => 'Search only featured businesses',
        'method' => 'GET'
    ],
    'Create Business (Enhanced)' => [
        'url' => $baseUrl . '?version=v1&endpoint=businesses&action=create',
        'description' => 'Create business with 30-day expiry and enhanced fields',
        'method' => 'POST',
        'data' => [
            'business_name' => 'Test Enhanced Business',
            'business_contact' => '123-456-7890',
            'business_description' => 'Enhanced test business with new features',
            'category' => 'restaurant',
            'image_url' => 'https://example.com/image.jpg',
            'featured' => true
        ]
    ],
    'Set Featured Status' => [
        'url' => $baseUrl . '?version=v1&endpoint=businesses&action=set-featured&id=1',
        'description' => 'Set business featured status',
        'method' => 'PUT',
        'data' => ['featured' => true]
    ],
    'Update Image URL' => [
        'url' => $baseUrl . '?version=v1&endpoint=businesses&action=update-image&id=1',
        'description' => 'Update business image URL',
        'method' => 'PUT',
        'data' => ['image_url' => 'https://example.com/new-image.jpg']
    ],
    'Reactivate Business (Enhanced)' => [
        'url' => $baseUrl . '?version=v1&endpoint=businesses&action=reactivate&id=1',
        'description' => 'Reactivate business with new 30-day period',
        'method' => 'PUT'
    ],
    'Expiring Soon' => [
        'url' => $baseUrl . '?version=v1&endpoint=businesses&action=expiring-soon&days=7',
        'description' => 'Get businesses expiring in next 7 days',
        'method' => 'GET'
    ],
    'Auto-Deactivate Expired' => [
        'url' => $baseUrl . '?version=v1&endpoint=businesses&action=auto-deactivate',
        'description' => 'Auto-deactivate expired businesses',
        'method' => 'POST'
    ],
    'API Documentation v1.1.0' => [
        'url' => $baseUrl . '?version=v1&endpoint=documentation',
        'description' => 'Get updated API documentation with v1.1.0 features',
        'method' => 'GET'
    ]
];

// Migration test
$migrationTest = [
    'Database Migration' => [
        'url' => 'http://localhost/Apploqic_Business_Directory/migration_business_enhancement.php',
        'description' => 'Run database migration for enhanced features',
        'method' => 'GET'
    ]
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Business API Test v1.1.0</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        .version-badge {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            display: inline-block;
            margin-bottom: 30px;
            font-weight: bold;
        }
        .section {
            margin-bottom: 40px;
        }
        .section h2 {
            color: #667eea;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 20px;
        }
        .test-card {
            border: 2px solid #e1e8f0;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .test-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .test-header {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 15px 20px;
            font-weight: bold;
        }
        .test-content {
            padding: 20px;
        }
        .test-url {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 5px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            word-break: break-all;
            border-left: 4px solid #667eea;
        }
        .test-data {
            background: #fff3cd;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #ffc107;
        }
        .method-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            margin-right: 10px;
        }
        .method-get { background: #d4edda; color: #155724; }
        .method-post { background: #cce5ff; color: #004085; }
        .method-put { background: #fff3cd; color: #856404; }
        .method-delete { background: #f8d7da; color: #721c24; }
        .test-button {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            margin-right: 10px;
            margin-top: 10px;
            font-weight: bold;
            transition: transform 0.2s ease;
        }
        .test-button:hover {
            transform: scale(1.05);
        }
        .migration-button {
            background: linear-gradient(45deg, #dc3545, #e83e8c);
        }
        .response-area {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-top: 15px;
            max-height: 400px;
            overflow-y: auto;
            display: none;
        }
        .loading {
            color: #667eea;
            font-style: italic;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
            background: #2d3748;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 5px;
            font-size: 12px;
        }
        .features-list {
            background: #e8f5e8;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .features-list h3 {
            color: #155724;
            margin-top: 0;
        }
        .features-list ul {
            columns: 2;
            column-gap: 30px;
        }
        .test-all-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .test-all-btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: transform 0.3s ease;
        }
        .test-all-btn:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Enhanced Business API Test</h1>
        <div style="text-align: center;">
            <span class="version-badge">v1.1.0 - Enhanced Features</span>
        </div>
        
        <div class="features-list">
            <h3>🆕 New Features in v1.1.0</h3>
            <ul>
                <li>✅ 30-day automatic expiry for businesses</li>
                <li>✅ Reactivation tracking with timestamps</li>
                <li>✅ Enhanced featured status management</li>
                <li>✅ Image URL management for CRUD operations</li>
                <li>✅ Enhanced search with category filtering</li>
                <li>✅ Empty search returns all businesses</li>
                <li>✅ Expiry monitoring and auto-deactivation</li>
                <li>✅ Enhanced API documentation</li>
            </ul>
        </div>
        
        <div class="test-all-section">
            <button class="test-all-btn" onclick="runMigrationFirst()">
                🗃️ Run Migration & Test All Features
            </button>
        </div>
        
        <div class="section">
            <h2>🗄️ Database Migration</h2>
            <div class="test-grid">
                <?php foreach ($migrationTest as $testName => $testData): ?>
                <div class="test-card">
                    <div class="test-header"><?= htmlspecialchars($testName) ?></div>
                    <div class="test-content">
                        <p><strong>Description:</strong> <?= htmlspecialchars($testData['description']) ?></p>
                        <div class="test-url"><?= htmlspecialchars($testData['url']) ?></div>
                        <button class="test-button migration-button" onclick="testEndpoint('<?= addslashes($testData['url']) ?>', '<?= addslashes($testName) ?>', '<?= $testData['method'] ?>')">
                            🗃️ Run Migration
                        </button>
                        <div id="response-<?= preg_replace('/[^a-zA-Z0-9]/', '', $testName) ?>" class="response-area">
                            <div class="loading">
                                <div class="spinner"></div>
                                Running migration...
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="section">
            <h2>🔬 Enhanced API Features Testing</h2>
            <div class="test-grid">
                <?php foreach ($enhancedTests as $testName => $testData): ?>
                <div class="test-card">
                    <div class="test-header"><?= htmlspecialchars($testName) ?></div>
                    <div class="test-content">
                        <p><strong>Description:</strong> <?= htmlspecialchars($testData['description']) ?></p>
                        <span class="method-badge method-<?= strtolower($testData['method']) ?>">
                            <?= $testData['method'] ?>
                        </span>
                        <div class="test-url"><?= htmlspecialchars($testData['url']) ?></div>
                        
                        <?php if (isset($testData['data'])): ?>
                        <div class="test-data">
                            <strong>Request Data:</strong>
                            <pre><?= json_encode($testData['data'], JSON_PRETTY_PRINT) ?></pre>
                        </div>
                        <?php endif; ?>
                        
                        <button class="test-button" onclick="testEndpoint('<?= addslashes($testData['url']) ?>', '<?= addslashes($testName) ?>', '<?= $testData['method'] ?>', <?= isset($testData['data']) ? "'" . addslashes(json_encode($testData['data'])) . "'" : 'null' ?>)">
                            🧪 Test Feature
                        </button>
                        <button class="test-button" onclick="openInNewTab('<?= addslashes($testData['url']) ?>')">
                            🔗 Open in Browser
                        </button>
                        <div id="response-<?= preg_replace('/[^a-zA-Z0-9]/', '', $testName) ?>" class="response-area">
                            <div class="loading">
                                <div class="spinner"></div>
                                Testing feature...
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        function testEndpoint(url, testName, method = 'GET', data = null) {
            const responseId = 'response-' + testName.replace(/[^a-zA-Z0-9]/g, '');
            const responseDiv = document.getElementById(responseId);
            
            responseDiv.style.display = 'block';
            responseDiv.innerHTML = `
                <div class="loading">
                    <div class="spinner"></div>
                    Testing ${testName}...
                </div>
            `;
            
            const options = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-User-Role': 'admin'
                }
            };
            
            if (data && (method === 'POST' || method === 'PUT')) {
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
                    responseDiv.innerHTML = `
                        <div class="success">✅ Success! Feature working correctly</div>
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                    `;
                })
                .catch(error => {
                    responseDiv.innerHTML = `
                        <div class="error">❌ Error: ${error.message}</div>
                        <p><strong>Troubleshooting:</strong></p>
                        <ul>
                            <li>Ensure XAMPP is running</li>
                            <li>Check database connection</li>
                            <li>Run migration first</li>
                            <li>Verify API router is accessible</li>
                            <li>Check PHP error logs</li>
                        </ul>
                    `;
                });
        }
        
        function openInNewTab(url) {
            window.open(url, '_blank');
        }
        
        function runMigrationFirst() {
            // First run migration
            testEndpoint('http://localhost/Apploqic_Business_Directory/migration_business_enhancement.php', 'DatabaseMigration', 'GET');
            
            // Then test all features after a delay
            setTimeout(() => {
                <?php foreach ($enhancedTests as $testName => $testData): ?>
                setTimeout(() => {
                    testEndpoint('<?= addslashes($testData['url']) ?>', '<?= addslashes($testName) ?>', '<?= $testData['method'] ?>', <?= isset($testData['data']) ? "'" . addslashes(json_encode($testData['data'])) . "'" : 'null' ?>);
                }, <?= array_search($testName, array_keys($enhancedTests)) * 2000 ?>);
                <?php endforeach; ?>
            }, 3000);
        }
    </script>
</body>
</html>