<?php
/**
 * Documentation API Test
 * 
 * Test file to verify all documentation API endpoints
 */

// Base URL for API testing
$baseUrl = 'http://localhost/Apploqic_Business_Directory/app/api/router.php';

// Test endpoints
$tests = [
    'API Version Information' => [
        'url' => $baseUrl . '?version=v1&endpoint=documentation&action=version',
        'description' => 'Get API version information'
    ],
    'Patch Notes' => [
        'url' => $baseUrl . '?version=v1&endpoint=documentation&action=patches',
        'description' => 'Get patch notes and changelog'
    ],
    'Endpoints Documentation' => [
        'url' => $baseUrl . '?version=v1&endpoint=documentation&action=endpoints',
        'description' => 'Get all available endpoints'
    ],
    'Complete Documentation' => [
        'url' => $baseUrl . '?version=v1&endpoint=documentation',
        'description' => 'Get complete API documentation'
    ],
    'Health Status' => [
        'url' => $baseUrl . '?version=v1&endpoint=documentation&action=health',
        'description' => 'Get API health status'
    ]
];

// HTML Output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation API Test</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .test-section {
            margin-bottom: 30px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .test-header {
            background: #007bff;
            color: white;
            padding: 15px;
            margin: 0;
            border-radius: 5px 5px 0 0;
        }
        .test-content {
            padding: 15px;
        }
        .test-url {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 3px;
            margin: 10px 0;
            font-family: monospace;
            word-break: break-all;
        }
        .test-button {
            background: #28a745;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin-right: 10px;
        }
        .test-button:hover {
            background: #218838;
        }
        .response-area {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            padding: 15px;
            margin-top: 10px;
            max-height: 400px;
            overflow-y: auto;
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
        }
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .test-all-btn {
            background: #17a2b8;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
            font-size: 16px;
        }
        .test-all-btn:hover {
            background: #138496;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📚 Documentation API Test Suite</h1>
        
        <button class="test-all-btn" onclick="testAllEndpoints()">🚀 Test All Endpoints</button>
        
        <?php foreach ($tests as $testName => $testData): ?>
        <div class="test-section">
            <h3 class="test-header"><?= htmlspecialchars($testName) ?></h3>
            <div class="test-content">
                <p><strong>Description:</strong> <?= htmlspecialchars($testData['description']) ?></p>
                <div class="test-url"><?= htmlspecialchars($testData['url']) ?></div>
                <button class="test-button" onclick="testEndpoint('<?= addslashes($testData['url']) ?>', '<?= addslashes($testName) ?>')">
                    📡 Test Endpoint
                </button>
                <button class="test-button" onclick="openInNewTab('<?= addslashes($testData['url']) ?>')">
                    🔗 Open in New Tab
                </button>
                <div id="response-<?= preg_replace('/[^a-zA-Z0-9]/', '', $testName) ?>" class="response-area" style="display: none;">
                    <div class="loading">Loading...</div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <div class="test-section">
            <h3 class="test-header">📋 Current v1 API Endpoints Summary</h3>
            <div class="test-content">
                <h4>Business Endpoints:</h4>
                <ul>
                    <li><strong>GET</strong> /businesses - List all businesses (Admin, User)</li>
                    <li><strong>GET</strong> /businesses/{id} - Get single business (Admin, User)</li>
                    <li><strong>GET</strong> /businesses/search - Search businesses (Admin, User)</li>
                    <li><strong>POST</strong> /businesses - Create business (Admin only)</li>
                    <li><strong>PUT</strong> /businesses/{id} - Update business (Admin only)</li>
                    <li><strong>POST</strong> /businesses/{id}/deactivate - Deactivate business (Admin only)</li>
                    <li><strong>POST</strong> /businesses/{id}/reactivate - Reactivate business (Admin only)</li>
                    <li><strong>DELETE</strong> /businesses/{id} - Delete business (Admin only)</li>
                    <li><strong>GET</strong> /businesses/featured - Get featured businesses (Admin, User)</li>
                    <li><strong>GET</strong> /businesses/analytics - Get analytics (Admin only)</li>
                </ul>
                
                <h4>Documentation Endpoints:</h4>
                <ul>
                    <li><strong>GET</strong> /documentation - Complete API documentation (Admin, User)</li>
                    <li><strong>GET</strong> /documentation/version - API version info (Admin, User)</li>
                    <li><strong>GET</strong> /documentation/patches - Patch notes & changelog (Admin, User)</li>
                    <li><strong>GET</strong> /documentation/endpoints - Available endpoints (Admin, User)</li>
                    <li><strong>GET</strong> /documentation/health - API health status (Admin, User)</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        function testEndpoint(url, testName) {
            const responseId = 'response-' + testName.replace(/[^a-zA-Z0-9]/g, '');
            const responseDiv = document.getElementById(responseId);
            
            responseDiv.style.display = 'block';
            responseDiv.innerHTML = '<div class="loading">🔄 Testing endpoint...</div>';
            
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    responseDiv.innerHTML = `
                        <div class="success">✅ Success!</div>
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                    `;
                })
                .catch(error => {
                    responseDiv.innerHTML = `
                        <div class="error">❌ Error: ${error.message}</div>
                        <p>Please check:</p>
                        <ul>
                            <li>XAMPP is running</li>
                            <li>Database connection is working</li>
                            <li>API router path is correct</li>
                            <li>Documentation controller is loaded</li>
                        </ul>
                    `;
                });
        }
        
        function openInNewTab(url) {
            window.open(url, '_blank');
        }
        
        function testAllEndpoints() {
            <?php foreach ($tests as $testName => $testData): ?>
            setTimeout(() => {
                testEndpoint('<?= addslashes($testData['url']) ?>', '<?= addslashes($testName) ?>');
            }, <?= array_search($testName, array_keys($tests)) * 1000 ?>);
            <?php endforeach; ?>
        }
    </script>
</body>
</html>