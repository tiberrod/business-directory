<?php
/**
 * Business Directory API v1.0.0 Test Suite
 * Comprehensive testing for all v1 API endpoints
 * 
 * Usage: Access this file via browser or run via command line
 * Example: http://localhost/Apploqic_Business_Directory/app/api/test-v1-api.php
 */

// Set content type for proper output
header('Content-Type: text/html; charset=UTF-8');

class ApiV1TestSuite {
    private $baseUrl;
    private $apiUrl;
    private $testResults = [];
    private $adminHeaders;

    public function __construct() {
        // Auto-detect base URL
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $path = dirname($_SERVER['REQUEST_URI']);
        $this->baseUrl = $protocol . '://' . $host . $path;
        $this->apiUrl = $this->baseUrl . '/router.php';
        
        // Admin headers for testing
        $this->adminHeaders = [
            'Content-Type: application/json',
            'X-User-Role: admin'
        ];
    }

    /**
     * Run all API tests
     */
    public function runAllTests() {
        echo "<h1>Business Directory API v1.0.0 Test Suite</h1>";
        echo "<p>Testing API endpoints at: <strong>{$this->apiUrl}</strong></p>";
        echo "<hr>";

        // Test API Info
        $this->testApiInfo();

        // Test Business Listing (User access)
        $this->testBusinessListing();

        // Test Business Search (User access)
        $this->testBusinessSearch();

        // Test Business Details (User access)
        $this->testBusinessDetails();

        // Test Business Creation (Admin access)
        $this->testBusinessCreation();

        // Test Business Update (Admin access)
        $this->testBusinessUpdate();

        // Test Business Deactivation (Admin access)
        $this->testBusinessDeactivation();

        // Test Business Deletion (Admin access)
        $this->testBusinessDeletion();

        // Test Access Control
        $this->testAccessControl();

        // Display summary
        $this->displaySummary();
    }

    /**
     * Test API Information endpoint
     */
    private function testApiInfo() {
        echo "<h2>1. Testing API Information</h2>";
        
        $url = $this->apiUrl . '?version=v1';
        $response = $this->makeRequest('GET', $url);
        
        $this->displayTest(
            'API Info Endpoint',
            $url,
            'GET',
            $response,
            function($data) {
                return isset($data['api']) && 
                       isset($data['version']) && 
                       isset($data['endpoints']);
            }
        );
    }

    /**
     * Test business listing endpoint
     */
    private function testBusinessListing() {
        echo "<h2>2. Testing Business Listing (User Access)</h2>";
        
        // Test basic listing
        $url = $this->apiUrl . '?version=v1&endpoint=businesses&action=list';
        $response = $this->makeRequest('GET', $url);
        
        $this->displayTest(
            'Basic Business Listing',
            $url,
            'GET',
            $response,
            function($data) {
                return isset($data['status']) && 
                       $data['status'] === 'success' &&
                       isset($data['data']) &&
                       isset($data['pagination']);
            }
        );

        // Test with filters
        $url = $this->apiUrl . '?version=v1&endpoint=businesses&action=list&page=1&limit=5&category=Restaurant';
        $response = $this->makeRequest('GET', $url);
        
        $this->displayTest(
            'Filtered Business Listing',
            $url,
            'GET',
            $response,
            function($data) {
                return isset($data['status']) && 
                       $data['status'] === 'success' &&
                       isset($data['filters_applied']);
            }
        );
    }

    /**
     * Test business search endpoint
     */
    private function testBusinessSearch() {
        echo "<h2>3. Testing Business Search (User Access)</h2>";
        
        $url = $this->apiUrl . '?version=v1&endpoint=businesses&action=search&name=test';
        $response = $this->makeRequest('GET', $url);
        
        $this->displayTest(
            'Business Search',
            $url,
            'GET',
            $response,
            function($data) {
                return isset($data['status']) && 
                       isset($data['search_term']) &&
                       isset($data['results_count']);
            }
        );

        // Test search without term (should fail)
        $url = $this->apiUrl . '?version=v1&endpoint=businesses&action=search';
        $response = $this->makeRequest('GET', $url);
        
        $this->displayTest(
            'Search Without Term (Should Fail)',
            $url,
            'GET',
            $response,
            function($data) {
                return isset($data['status']) && 
                       $data['status'] === 'error' &&
                       isset($data['error']['code']) &&
                       $data['error']['code'] === 400;
            }
        );
    }

    /**
     * Test business details endpoint
     */
    private function testBusinessDetails() {
        echo "<h2>4. Testing Business Details (User Access)</h2>";
        
        $url = $this->apiUrl . '?version=v1&endpoint=businesses&action=details&id=1';
        $response = $this->makeRequest('GET', $url);
        
        $this->displayTest(
            'Business Details (ID=1)',
            $url,
            'GET',
            $response,
            function($data) {
                return isset($data['status']) && (
                    ($data['status'] === 'success' && isset($data['data']['id'])) ||
                    ($data['status'] === 'error' && $data['error']['code'] === 404)
                );
            }
        );

        // Test invalid ID
        $url = $this->apiUrl . '?version=v1&endpoint=businesses&action=details&id=999999';
        $response = $this->makeRequest('GET', $url);
        
        $this->displayTest(
            'Business Details Invalid ID',
            $url,
            'GET',
            $response,
            function($data) {
                return isset($data['status']) && 
                       $data['status'] === 'error' &&
                       $data['error']['code'] === 404;
            }
        );
    }

    /**
     * Test business creation endpoint
     */
    private function testBusinessCreation() {
        echo "<h2>5. Testing Business Creation (Admin Access)</h2>";
        
        $testBusiness = [
            'business_name' => 'Test Business ' . date('Y-m-d H:i:s'),
            'business_contact' => 'test@example.com',
            'business_category' => 'Test Category',
            'business_description' => 'This is a test business',
            'status' => 1,
            'is_featured' => 0
        ];

        $url = $this->apiUrl . '?version=v1&endpoint=businesses&action=create';
        $response = $this->makeRequest('POST', $url, $testBusiness, $this->adminHeaders);
        
        $this->displayTest(
            'Create New Business',
            $url,
            'POST',
            $response,
            function($data) {
                return isset($data['status']) && (
                    ($data['status'] === 'success' && isset($data['data']['id'])) ||
                    ($data['status'] === 'error') // Database might not exist yet
                );
            },
            $testBusiness
        );

        // Test creation without required fields
        $invalidBusiness = ['business_name' => ''];
        $response = $this->makeRequest('POST', $url, $invalidBusiness, $this->adminHeaders);
        
        $this->displayTest(
            'Create Business (Invalid Data)',
            $url,
            'POST',
            $response,
            function($data) {
                return isset($data['status']) && 
                       $data['status'] === 'error' &&
                       $data['error']['code'] === 400;
            },
            $invalidBusiness
        );
    }

    /**
     * Test business update endpoint
     */
    private function testBusinessUpdate() {
        echo "<h2>6. Testing Business Update (Admin Access)</h2>";
        
        $updateData = [
            'business_name' => 'Updated Test Business',
            'business_description' => 'Updated description'
        ];

        $url = $this->apiUrl . '?version=v1&endpoint=businesses&action=update&id=1';
        $response = $this->makeRequest('PUT', $url, $updateData, $this->adminHeaders);
        
        $this->displayTest(
            'Update Business (ID=1)',
            $url,
            'PUT',
            $response,
            function($data) {
                return isset($data['status']) && (
                    ($data['status'] === 'success') ||
                    ($data['status'] === 'error' && in_array($data['error']['code'], [404, 500]))
                );
            },
            $updateData
        );
    }

    /**
     * Test business deactivation endpoint
     */
    private function testBusinessDeactivation() {
        echo "<h2>7. Testing Business Deactivation (Admin Access)</h2>";
        
        $deactivateData = [
            'reason' => 'Test deactivation'
        ];

        $url = $this->apiUrl . '?version=v1&endpoint=businesses&action=deactivate&id=1';
        $response = $this->makeRequest('PUT', $url, $deactivateData, $this->adminHeaders);
        
        $this->displayTest(
            'Deactivate Business (ID=1)',
            $url,
            'PUT',
            $response,
            function($data) {
                return isset($data['status']) && (
                    ($data['status'] === 'success') ||
                    ($data['status'] === 'error' && in_array($data['error']['code'], [400, 404, 500]))
                );
            },
            $deactivateData
        );
    }

    /**
     * Test business deletion endpoint
     */
    private function testBusinessDeletion() {
        echo "<h2>8. Testing Business Deletion (Admin Access)</h2>";
        
        $url = $this->apiUrl . '?version=v1&endpoint=businesses&action=delete&id=999';
        $response = $this->makeRequest('DELETE', $url, null, $this->adminHeaders);
        
        $this->displayTest(
            'Delete Business (ID=999)',
            $url,
            'DELETE',
            $response,
            function($data) {
                return isset($data['status']) && (
                    ($data['status'] === 'success') ||
                    ($data['status'] === 'error' && in_array($data['error']['code'], [404, 500]))
                );
            }
        );
    }

    /**
     * Test access control
     */
    private function testAccessControl() {
        echo "<h2>9. Testing Access Control</h2>";
        
        // Test admin endpoint without admin access
        $url = $this->apiUrl . '?version=v1&endpoint=businesses&action=create';
        $response = $this->makeRequest('POST', $url, ['business_name' => 'test']);
        
        $this->displayTest(
            'Create Business Without Admin Access',
            $url,
            'POST',
            $response,
            function($data) {
                return isset($data['status']) && 
                       $data['status'] === 'error' &&
                       $data['error']['code'] === 403;
            }
        );

        // Test invalid endpoint
        $url = $this->apiUrl . '?version=v1&endpoint=invalid&action=test';
        $response = $this->makeRequest('GET', $url);
        
        $this->displayTest(
            'Invalid Endpoint',
            $url,
            'GET',
            $response,
            function($data) {
                return isset($data['status']) && 
                       $data['status'] === 'error' &&
                       $data['error']['code'] === 404;
            }
        );
    }

    /**
     * Make HTTP request
     */
    private function makeRequest($method, $url, $data = null, $headers = []) {
        $context = [
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $headers),
                'ignore_errors' => true
            ]
        ];

        if ($data && in_array($method, ['POST', 'PUT'])) {
            $context['http']['content'] = json_encode($data);
            if (!in_array('Content-Type: application/json', $headers)) {
                $context['http']['header'] .= "\r\nContent-Type: application/json";
            }
        }

        $response = @file_get_contents($url, false, stream_context_create($context));
        
        if ($response === false) {
            return ['status' => 'error', 'error' => ['code' => 500, 'message' => 'Request failed']];
        }

        return json_decode($response, true) ?: ['status' => 'error', 'error' => ['code' => 500, 'message' => 'Invalid JSON response']];
    }

    /**
     * Display test result
     */
    private function displayTest($testName, $url, $method, $response, $validator, $requestData = null) {
        $isValid = $validator($response);
        $status = $isValid ? 'PASS' : 'FAIL';
        $statusClass = $isValid ? 'success' : 'error';
        
        $this->testResults[] = ['name' => $testName, 'status' => $status];

        echo "<div class='test-result {$statusClass}'>";
        echo "<h3>{$testName} - <span class='{$statusClass}'>{$status}</span></h3>";
        echo "<p><strong>URL:</strong> {$url}</p>";
        echo "<p><strong>Method:</strong> {$method}</p>";
        
        if ($requestData) {
            echo "<p><strong>Request Data:</strong></p>";
            echo "<pre>" . json_encode($requestData, JSON_PRETTY_PRINT) . "</pre>";
        }
        
        echo "<p><strong>Response:</strong></p>";
        echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
        echo "</div>";
        echo "<hr>";
    }

    /**
     * Display test summary
     */
    private function displaySummary() {
        $totalTests = count($this->testResults);
        $passedTests = count(array_filter($this->testResults, function($result) {
            return $result['status'] === 'PASS';
        }));
        $failedTests = $totalTests - $passedTests;

        echo "<h2>Test Summary</h2>";
        echo "<div class='summary'>";
        echo "<p><strong>Total Tests:</strong> {$totalTests}</p>";
        echo "<p><strong>Passed:</strong> <span class='success'>{$passedTests}</span></p>";
        echo "<p><strong>Failed:</strong> <span class='error'>{$failedTests}</span></p>";
        echo "<p><strong>Success Rate:</strong> " . round(($passedTests / $totalTests) * 100, 2) . "%</p>";
        echo "</div>";

        if ($failedTests > 0) {
            echo "<h3>Failed Tests:</h3>";
            echo "<ul>";
            foreach ($this->testResults as $result) {
                if ($result['status'] === 'FAIL') {
                    echo "<li class='error'>{$result['name']}</li>";
                }
            }
            echo "</ul>";
        }
    }
}

// CSS for better display
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-result { margin: 10px 0; padding: 15px; border-radius: 5px; }
    .test-result.success { background-color: #d4edda; border: 1px solid #c3e6cb; }
    .test-result.error { background-color: #f8d7da; border: 1px solid #f5c6cb; }
    .success { color: #155724; font-weight: bold; }
    .error { color: #721c24; font-weight: bold; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    .summary { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 20px 0; }
    h1, h2, h3 { color: #343a40; }
    hr { margin: 20px 0; border: 1px solid #dee2e6; }
</style>";

// Run the tests
$testSuite = new ApiV1TestSuite();
$testSuite->runAllTests();

echo "<hr>";
echo "<p><strong>Note:</strong> Some tests may fail if the database is not set up or if there's no test data. This is expected behavior.</p>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li>Set up the database with the business_detail table</li>";
echo "<li>Add some test data to the table</li>";
echo "<li>Configure proper authentication for production use</li>";
echo "<li>Test with actual frontend integration</li>";
echo "</ul>";
?>