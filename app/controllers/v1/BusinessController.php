<?php
/**
 * Business Controller v1
 * 
 * Handles all business-related operations for API v1
 * Implements version-specific business logic while using the standard base model
 */

require_once __DIR__ . '/../Base/BaseController.php';
require_once __DIR__ . '/../../models/Base/BusinessModel.php';

class BusinessControllerV1 extends BaseController {
    
    /**
     * Initialize the model for v1
     */
    protected function initializeModel() {
        $this->model = new BusinessModel($this->db, 'v1');
    }
    
    /**
     * Get all businesses
     * Access Level: Client (open access)
     */
    public function index() {
        try {
            $pagination = $this->getPagination();
            $filters = $this->buildFilters();

            $stmt = $this->model->getAll($pagination['limit'], $pagination['offset'], $filters);
            $total = $this->model->countAll($filters);

            if ($stmt === false) {
                return $this->sendError(500, 'Failed to retrieve businesses');
            }

            $businesses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->addImageUrls($businesses);

            $response = [
                'status' => 'success',
                'data' => $businesses,
                'pagination' => [
                    'current_page' => $pagination['page'],
                    'per_page' => $pagination['limit'],
                    'total' => $total,
                    'total_pages' => ceil($total / $pagination['limit'])
                ],
                'filters_applied' => $filters,
                'api_version' => $this->apiVersion
            ];

            $this->logOperation('list_businesses');
            return $this->sendSuccess($response);
            
        } catch (Exception $e) {
            error_log("Get all businesses error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }
    
    /**
     * Get a single business by ID
     * Access Level: Client (open access)
     */
    public function show($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return $this->sendError(400, 'Invalid business ID');
            }

            $business = $this->model->getById($id);

            if (!$business) {
                return $this->sendError(404, 'Business not found');
            }

            $this->addImageUrls($business);

            $response = [
                'status' => 'success',
                'data' => $business,
                'api_version' => $this->apiVersion
            ];

            $this->logOperation('view_business', $id);
            return $this->sendSuccess($response);
            
        } catch (Exception $e) {
            error_log("Get business by ID error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }
    
    /**
     * Search for businesses (Enhanced v1.1.0)
     * Access Level: Client (open access)
     */
    public function search() {
        try {
            $searchTerm = $_GET['name'] ?? $_GET['q'] ?? $_GET['search'] ?? '';
            
            // Enhanced v1.1.0: Empty search shows all businesses
            $pagination = $this->getPagination();
            
            // Build enhanced search criteria
            $statusParam = $_GET['status'] ?? 'active';
            $statusValue = null;
            
            // Convert status string to integer for database query
            if ($statusParam === 'active') {
                $statusValue = 1;
            } elseif ($statusParam === 'inactive') {
                $statusValue = 0;
            } elseif (is_numeric($statusParam)) {
                $statusValue = (int)$statusParam;
            }
            
            $criteria = [
                'search' => trim($searchTerm),
                'category' => $_GET['category'] ?? '',
                'status' => $statusValue,
                'featured' => isset($_GET['featured']) ? (bool)$_GET['featured'] : null,
                'expired' => isset($_GET['expired']) ? (bool)$_GET['expired'] : false, // Default: exclude expired
                'limit' => $pagination['limit'],
                'offset' => $pagination['offset']
            ];
            
            // Use enhanced search method
            $stmt = $this->model->searchEnhanced($criteria);
            
            if ($stmt === false) {
                return $this->sendError(500, 'Search failed');
            }

            $businesses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->addImageUrls($businesses);
            
            // Add enhanced info to each business
            foreach ($businesses as &$business) {
                $business['is_featured'] = (bool)$business['is_featured_bool'];
                $business['is_expired'] = (bool)$business['is_expired'];
                $business['days_until_expiry'] = (int)$business['days_until_expiry'];
                
                // Clean up helper fields
                unset($business['is_featured_bool']);
            }

            $response = [
                'status' => 'success',
                'data' => $businesses,
                'search_criteria' => [
                    'search_term' => $searchTerm,
                    'category' => $criteria['category'],
                    'status' => $statusParam,
                    'featured_only' => $criteria['featured'],
                    'include_expired' => $criteria['expired']
                ],
                'results_count' => count($businesses),
                'pagination' => [
                    'current_page' => $pagination['page'],
                    'per_page' => $pagination['limit']
                ],
                'api_version' => $this->apiVersion,
                'enhanced_features' => [
                    'category_filtering' => true,
                    'expiry_tracking' => true,
                    'featured_prioritization' => true,
                    'empty_search_shows_all' => true
                ]
            ];

            $this->logOperation('search_businesses', null, ['search_term' => $searchTerm, 'category' => $criteria['category']]);
            return $this->sendSuccess($response);
            
        } catch (Exception $e) {
            error_log("Enhanced search businesses error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }
    
    /**
     * Create new business (Enhanced v1.1.0)
     * Access Level: Public (open access for frontend integration)
     */
    public function store() {
        try {
            // Handle both form data (with file uploads) and JSON data
            $businessData = [];
            $imageName = null;
            
            if (!empty($_FILES) && isset($_POST['business_name'])) {
                // Handle multipart form data with file upload
                $requiredFields = ['business_name', 'business_contact'];
                foreach ($requiredFields as $field) {
                    if (empty($_POST[$field])) {
                        return $this->sendError(400, "Missing required field: $field");
                    }
                }
                
                // Handle image upload
                if (isset($_FILES['business_img']) && $_FILES['business_img']['error'] === UPLOAD_ERR_OK) {
                    try {
                        $imageName = $this->handleImageUpload();
                    } catch (Exception $e) {
                        return $this->sendError(500, $e->getMessage());
                    }
                }
                
                $businessData = [
                    'business_name' => trim($_POST['business_name']),
                    'business_contact' => trim($_POST['business_contact']),
                    'business_description' => trim($_POST['business_description'] ?? ''),
                    'business_category' => trim($_POST['business_category'] ?? ''),
                    'business_img' => $imageName,
                    'is_featured' => isset($_POST['is_featured']) ? (int)$_POST['is_featured'] : 0
                ];
                
            } else {
                // Handle JSON input (for backward compatibility)
                $input = $this->getJsonInput();
                
                $requiredFields = ['business_name', 'business_contact'];
                $this->validateRequiredFields($input, $requiredFields);

                // Simple image validation if provided - ABSOLUTELY NO BINARY DATA
                $imageData = trim($input['business_img'] ?? '');
                if (!empty($imageData)) {
                    // STRICT: Reject anything that looks like binary data  
                    if (strlen($imageData) > 255) {
                        error_log("CREATE: Rejected - Image data too long (" . strlen($imageData) . " chars)");
                        return $this->sendError(400, 'Image filename too long. Please use actual filenames, not binary data.');
                    }
                    
                    // STRICT: Reject anything with PNG/JPEG signatures or null bytes
                    if (strpos($imageData, "\x89PNG") !== false || 
                        strpos($imageData, "\xFF\xD8\xFF") !== false || 
                        strpos($imageData, "\0") !== false ||
                        !ctype_print(str_replace(["\r", "\n", "\t"], '', $imageData))) {
                        error_log("CREATE: Rejected - Binary data detected in image field");
                        return $this->sendError(400, 'Binary data not allowed. Use filename only or upload via file upload.');
                    }
                    
                    // Only check file extension for valid filenames
                    $extension = strtolower(pathinfo($imageData, PATHINFO_EXTENSION));
                    if (!empty($extension)) {
                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'bmp', 'tiff', 'svg'];
                        if (!in_array($extension, $allowedExtensions)) {
                            return $this->sendError(400, 'Invalid image format. Allowed formats: JPG, JPEG, PNG, WEBP, BMP, TIFF, SVG (GIF not supported).');
                        }
                    }
                }

                $businessData = [
                    'business_name' => trim($input['business_name']),
                    'business_contact' => trim($input['business_contact']),
                    'business_description' => trim($input['business_description'] ?? ''),
                    'business_category' => trim($input['business_category'] ?? ''),
                    'business_img' => $imageData, // Only accept validated filename
                    'is_featured' => isset($input['is_featured']) ? (int)$input['is_featured'] : 0
                ];
            }

            // Use enhanced create method with 30-day expiry
            $businessId = $this->model->createWithExpiry($businessData);

            if (!$businessId) {
                return $this->sendError(500, 'Failed to create business');
            }

            // Get the created business
            $business = $this->model->getById($businessId);
            $this->addImageUrls($business);

            $response = [
                'status' => 'success',
                'message' => 'Business created successfully',
                'data' => $business,
                'api_version' => $this->apiVersion
            ];

            $this->logOperation('create_business', $businessId, $businessData);
            return $this->sendSuccess($response, 201);
            
        } catch (Exception $e) {
            error_log("Create business error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }
    
    /**
     * Update existing business
     * Access Level: Public (open access for frontend integration)
     */
    public function update() {
        try {
            // Enhanced debugging for production environment
            error_log("=== UPDATE METHOD CALLED ===");
            error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
            error_log("REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'unknown'));
            error_log("QUERY_STRING: " . ($_SERVER['QUERY_STRING'] ?? 'unknown'));
            error_log("CONTENT_TYPE: " . ($_SERVER['CONTENT_TYPE'] ?? 'unknown'));
            error_log("GET params: " . json_encode($_GET));
            error_log("POST params: " . json_encode($_POST));
            error_log("FILES: " . json_encode($_FILES));
            error_log("Raw input length: " . strlen(file_get_contents('php://input')));
            
            $id = $_GET['id'] ?? null;
            
            if (!is_numeric($id) || $id <= 0) {
                error_log("UPDATE: Invalid ID provided: " . var_export($id, true));
                return $this->sendError(400, 'Invalid business ID');
            }

            // Check if business exists
            $existingBusiness = $this->model->getById($id);
            if (!$existingBusiness) {
                return $this->sendError(404, 'Business not found');
            }

            $updateData = [];
            $imageName = $existingBusiness['business_img']; // Keep existing image by default
            
            // Enhanced input handling: Try multiple sources
            $inputData = [];
            
            // 1. Check for multipart form data (file uploads)
            if (!empty($_FILES) || !empty($_POST)) {
                error_log("UPDATE: Processing form data - FILES: " . (!empty($_FILES) ? 'YES' : 'NO') . ", POST: " . (!empty($_POST) ? 'YES' : 'NO'));
                error_log("UPDATE: FILES content: " . json_encode($_FILES));
                error_log("UPDATE: POST content: " . json_encode($_POST));
                
                // Merge POST data (exclude _method parameter used for routing)
                $postData = $_POST;
                unset($postData['_method']); // Remove method override parameter
                $inputData = array_merge($inputData, $postData);
            }
            
            // 2. Check for multipart form data when $_POST is empty
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'multipart/form-data') !== false && empty($_POST)) {
                error_log("UPDATE: Parsing multipart form data manually");
                $parsedData = $this->parseMultipartFormData();
                if (is_array($parsedData)) {
                    $inputData = array_merge($inputData, $parsedData);
                }
            }
            
            // 3. Check for JSON input
            $jsonInput = $this->getJsonInput();
            if (!empty($jsonInput)) {
                error_log("UPDATE: Processing JSON input: " . json_encode($jsonInput));
                $inputData = array_merge($inputData, $jsonInput);
            }
            
            // 4. Check for URL-encoded body (fallback)
            if (empty($inputData)) {
                $rawInput = file_get_contents('php://input');
                if (!empty($rawInput) && strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
                    parse_str($rawInput, $parsedInput);
                    if (!empty($parsedInput)) {
                        error_log("UPDATE: Processing URL-encoded input: " . json_encode($parsedInput));
                        $inputData = $parsedInput;
                    }
                }
            }
            
            // Debug: Log all available input sources
            error_log("UPDATE: All input sources - GET: " . json_encode($_GET) . ", POST: " . json_encode($_POST) . ", JSON: " . json_encode($jsonInput) . ", Final: " . json_encode($inputData));
            
            // Allow empty inputData - we'll validate after processing allowed fields
            // This enables true partial updates where empty fields are ignored

            // V1 specific allowed update fields - Only include fields with actual values for partial updates
            $allowedFields = [
                'business_name', 'business_contact', 'business_description',
                'business_category', 'status', 'is_featured', 'expiry_date'
            ];
            
            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $inputData)) {
                    $value = $inputData[$field];
                    // Only include fields that have actual values (not empty strings) for true partial updates
                    // But allow some special cases for clearing fields intentionally
                    if ($value !== null && trim((string)$value) !== '') {
                        $updateData[$field] = trim($value);
                    }
                    // Special case: if field is explicitly set to "CLEAR" or similar, allow clearing
                    elseif ($value === 'CLEAR_FIELD' || $value === '__CLEAR__') {
                        $updateData[$field] = '';
                    }
                    // Allow explicitly setting values to 0 for numeric fields
                    elseif ($value === '0' || $value === 0) {
                        $updateData[$field] = $value;
                    }
                }
            }
            
            // Handle image separately
            $imageUploaded = false;
            
            // First priority: Handle actual file upload
            if (isset($_FILES['business_img']) && $_FILES['business_img']['error'] === UPLOAD_ERR_OK) {
                try {
                    $imageName = $this->handleImageUpload($existingBusiness['business_img']);
                    $updateData['business_img'] = $imageName;
                    $imageUploaded = true;
                    error_log("UPDATE: File upload successful, new image: " . $imageName);
                } catch (Exception $e) {
                    error_log("UPDATE: File upload failed: " . $e->getMessage());
                    return $this->sendError(500, $e->getMessage());
                }
            } 
            // Second priority: Handle text input only if no file was uploaded
            elseif (!$imageUploaded && isset($inputData['business_img']) && $inputData['business_img'] !== null && $inputData['business_img'] !== '') {
                // Simple image filename handling for text input
                $providedImage = trim($inputData['business_img']);
                
                // DEBUG: Log what we're receiving  
                error_log("UPDATE: Received image filename via text input: '" . $providedImage . "'");
                
                // Basic validation for text filename input
                if (strlen($providedImage) > 255) {
                    error_log("UPDATE: Rejected - Filename too long (" . strlen($providedImage) . " chars)");
                    return $this->sendError(400, 'Image filename too long (' . strlen($providedImage) . ' chars).');
                }
                
                // Only check file extension for valid filenames
                $extension = strtolower(pathinfo($providedImage, PATHINFO_EXTENSION));
                if (!empty($extension)) {
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                    if (!in_array($extension, $allowedExtensions)) {
                        return $this->sendError(400, 'Invalid image format. Allowed formats: JPG, JPEG, PNG, WEBP, GIF.');
                    }
                }
                
                // Store the filename
                $updateData['business_img'] = $providedImage;
                error_log("UPDATE: Accepted text filename input: " . $providedImage);
            }

            if (empty($updateData)) {
                // More lenient error message for empty updates
                return $this->sendError(400, 'No valid fields provided for update. At least one field must have a non-empty value. To upload images, use multipart/form-data.');
            }
            
            // Debug: Log what fields will be updated
            error_log("UPDATE: Fields to be updated: " . json_encode($updateData));

            $success = $this->model->update($id, $updateData);

            if (!$success) {
                return $this->sendError(500, 'Failed to update business');
            }

            // Get updated business
            $business = $this->model->getById($id);
            $this->addImageUrls($business);

            $response = [
                'status' => 'success',
                'message' => 'Business updated successfully',
                'data' => $business,
                'api_version' => $this->apiVersion
            ];

            $this->logOperation('update_business', $id, $updateData);
            return $this->sendSuccess($response);
            
        } catch (Exception $e) {
            error_log("Update business error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }
    
    /**
     * Deactivate business (soft delete)
     * Access Level: Public (open access for frontend integration)
     */
    public function deactivate() {
        try {
            $id = $_GET['id'] ?? null;
            
            if (!is_numeric($id) || $id <= 0) {
                return $this->sendError(400, 'Invalid business ID');
            }

            // Check if business exists
            $existingBusiness = $this->model->getById($id);
            if (!$existingBusiness) {
                return $this->sendError(404, 'Business not found');
            }

            if ($existingBusiness['status'] == 0) {
                return $this->sendError(400, 'Business is already deactivated');
            }

            $input = $this->getJsonInput();
            $reason = $input['reason'] ?? 'Non-payment of monthly fee';

            $success = $this->model->deactivate($id, $reason);

            if (!$success) {
                return $this->sendError(500, 'Failed to deactivate business');
            }

            $response = [
                'status' => 'success',
                'message' => 'Business deactivated successfully',
                'business_id' => $id,
                'reason' => $reason,
                'api_version' => $this->apiVersion
            ];

            $this->logOperation('deactivate_business', $id, ['reason' => $reason]);
            return $this->sendSuccess($response);
            
        } catch (Exception $e) {
            error_log("Deactivate business error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }
    
    /**
     * Reactivate business
     * Access Level: Public (open access for frontend integration)
     */
    public function reactivate() {
        try {
            // Get ID from query parameter (set by router from URL path)
            $id = $_GET['id'] ?? null;
            
            // Also check for ID in JSON body as fallback
            if (!$id) {
                $input = json_decode(file_get_contents('php://input'), true);
                $id = $input['id'] ?? null;
            }
            
            if (!is_numeric($id) || $id <= 0) {
                return $this->sendError(400, 'Invalid business ID. Please provide a valid business ID.');
            }

            // Check if business exists
            $existingBusiness = $this->model->getById($id);
            if (!$existingBusiness) {
                return $this->sendError(404, 'Business not found');
            }

            if ($existingBusiness['status'] == 1) {
                return $this->sendError(400, 'Business is already active');
            }

            // Use enhanced reactivation with 30-day expiry and tracking
            $adminId = $_SERVER['HTTP_X_ADMIN_ID'] ?? null; // Optional admin tracking
            $success = $this->model->reactivateWithExpiry($id, $adminId);

            if (!$success) {
                return $this->sendError(500, 'Failed to reactivate business');
            }

            // Get updated business info
            $business = $this->model->getById($id);
            $this->addImageUrls($business);

            $response = [
                'status' => 'success',
                'message' => 'Business reactivated successfully for 30 days',
                'business_id' => $id,
                'data' => $business,
                'expiry_info' => [
                    'reactivated_at' => $business['reactivated_at'],
                    'expires_at' => $business['expiry_at'],
                    'days_remaining' => $business['expiry_at'] ? max(0, ceil((strtotime($business['expiry_at']) - time()) / 86400)) : null
                ],
                'api_version' => $this->apiVersion
            ];

            $this->logOperation('reactivate_business', $id);
            return $this->sendSuccess($response);
            
        } catch (Exception $e) {
            error_log("Reactivate business error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }
    
    /**
     * Deactivate business (soft delete)
     * Access Level: Public (open access for frontend integration)
     */
    public function delete() {
        try {
            // Get ID from both URL parameter and request body
            $id = $_GET['id'] ?? null;
            
            // If no ID in URL, check request body
            if (!$id) {
                $input = $this->getJsonInput();
                $id = $input['id'] ?? null;
            }
            
            if (!is_numeric($id) || $id <= 0) {
                return $this->sendError(400, 'Invalid business ID');
            }

            // Check if business exists
            $existingBusiness = $this->model->getById($id);
            if (!$existingBusiness) {
                return $this->sendError(404, 'Business not found');
            }

            if ($existingBusiness['status'] == 0) {
                return $this->sendError(400, 'Business is already deactivated');
            }

            // Get deactivation reason from request body if provided
            $input = $this->getJsonInput();
            $reason = $input['reason'] ?? 'Non-payment of monthly fee';

            // Use deactivate method (soft delete) instead of hard delete
            $success = $this->model->deactivate($id, $reason);

            if (!$success) {
                return $this->sendError(500, 'Failed to deactivate business');
            }

            $response = [
                'status' => 'success',
                'message' => 'Business deactivated successfully',
                'business_id' => $id,
                'reason' => $reason,
                'api_version' => $this->apiVersion
            ];

            $this->logOperation('deactivate_business', $id);
            return $this->sendSuccess($response);
            
        } catch (Exception $e) {
            error_log("Delete business error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }
    
    /**
     * Get featured businesses (v1 specific functionality)
     * Access Level: Client (open access)
     */
    public function featured() {
        try {
            $pagination = $this->getPagination();
            $filters = $this->buildFilters();
            
            $result = $this->model->getFeatured($pagination['limit'], $pagination['offset'], $filters);
            
            if ($result === false) {
                return $this->sendError(500, 'Failed to retrieve featured businesses');
            }

            // Add image URLs to all business arrays
            $this->addImageUrls($result['highlighted']);
            $this->addImageUrls($result['normal']);
            $this->addImageUrls($result['all']);

            $response = [
                'status' => 'success',
                'data' => $result,
                'pagination' => [
                    'current_page' => $pagination['page'],
                    'per_page' => $pagination['limit']
                ],
                'filters_applied' => $filters,
                'api_version' => $this->apiVersion
            ];

            $this->logOperation('featured_businesses');
            return $this->sendSuccess($response);
            
        } catch (Exception $e) {
            error_log("Get featured businesses error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }
    
    /**
     * Get analytics (v1 specific functionality)
     * Access Level: Public (open access for frontend integration)
     */
    public function analytics() {
        try {
            // V1 specific analytics
            $totalActive = $this->model->countAll(['status' => 'active']);
            $totalInactive = $this->model->countAll(['status' => 'inactive']);
            $totalFeatured = $this->model->countAll(['featured' => 1]);
            $total = $this->model->countAll(['include_inactive' => true]);

            $response = [
                'status' => 'success',
                'data' => [
                    'total_businesses' => $total,
                    'active_businesses' => $totalActive,
                    'inactive_businesses' => $totalInactive,
                    'featured_businesses' => $totalFeatured,
                    'activation_rate' => $total > 0 ? round(($totalActive / $total) * 100, 2) : 0,
                    'featured_rate' => $total > 0 ? round(($totalFeatured / $total) * 100, 2) : 0
                ],
                'api_version' => $this->apiVersion,
                'generated_at' => date('Y-m-d H:i:s')
            ];

            $this->logOperation('analytics');
            return $this->sendSuccess($response);
            
        } catch (Exception $e) {
            error_log("Get analytics error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }
    
    /**
     * Set featured status (Enhanced v1.1.0)
     * Access Level: Public (open access for frontend integration)
     */
    public function setFeatured() {
        try {
            $id = $_GET['id'] ?? null;
            $input = $this->getJsonInput();
            $featured = isset($input['featured']) ? (bool)$input['featured'] : true;
            
            if (!is_numeric($id) || $id <= 0) {
                return $this->sendError(400, 'Invalid business ID');
            }

            $existingBusiness = $this->model->getById($id);
            if (!$existingBusiness) {
                return $this->sendError(404, 'Business not found');
            }

            $success = $this->model->setFeaturedStatus($id, $featured);

            if (!$success) {
                return $this->sendError(500, 'Failed to update featured status');
            }

            $response = [
                'status' => 'success',
                'message' => $featured ? 'Business set as featured' : 'Business removed from featured',
                'business_id' => $id,
                'featured' => $featured,
                'api_version' => $this->apiVersion
            ];

            $this->logOperation('set_featured', $id, ['featured' => $featured]);
            return $this->sendSuccess($response);
            
        } catch (Exception $e) {
            error_log("Set featured status error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }
    
    /**
     * Update business image URL (Enhanced v1.1.0)
     * Access Level: Public (open access for frontend integration)
     */
    public function updateImage() {
        try {
            $id = $_GET['id'] ?? null;
            $input = $this->getJsonInput();
            
            if (!is_numeric($id) || $id <= 0) {
                return $this->sendError(400, 'Invalid business ID');
            }

            if (empty($input['image_url'])) {
                return $this->sendError(400, 'Image URL is required');
            }

            $existingBusiness = $this->model->getById($id);
            if (!$existingBusiness) {
                return $this->sendError(404, 'Business not found');
            }

            $success = $this->model->updateImageUrl($id, $input['image_url']);

            if (!$success) {
                return $this->sendError(500, 'Failed to update image URL');
            }

            $response = [
                'status' => 'success',
                'message' => 'Business image URL updated successfully',
                'business_id' => $id,
                'image_url' => $input['image_url'],
                'api_version' => $this->apiVersion
            ];

            $this->logOperation('update_image', $id, ['image_url' => $input['image_url']]);
            return $this->sendSuccess($response);
            
        } catch (Exception $e) {
            error_log("Update image URL error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }
    
    /**
     * Get businesses expiring soon (Enhanced v1.1.0)
     * Accessible by: Admin only
     */
    public function expiringSoon() {
        try {
            if (!$this->hasAccess('admin')) {
                return $this->sendError(403, 'Admin access required');
            }

            $days = $_GET['days'] ?? 7;
            
            if (!is_numeric($days) || $days < 1) {
                return $this->sendError(400, 'Invalid days parameter');
            }

            $stmt = $this->model->getExpiringSoon($days);

            if ($stmt === false) {
                return $this->sendError(500, 'Failed to retrieve expiring businesses');
            }

            $businesses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->addImageUrls($businesses);

            $response = [
                'status' => 'success',
                'data' => $businesses,
                'criteria' => [
                    'days_ahead' => (int)$days,
                    'expiring_count' => count($businesses)
                ],
                'api_version' => $this->apiVersion
            ];

            $this->logOperation('expiring_soon', null, ['days' => $days]);
            return $this->sendSuccess($response);
            
        } catch (Exception $e) {
            error_log("Get expiring soon error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }
    
    /**
     * Auto-deactivate expired businesses (Enhanced v1.1.0)
     * Accessible by: Admin only
     */
    public function autoDeactivateExpired() {
        try {
            if (!$this->hasAccess('admin')) {
                return $this->sendError(403, 'Admin access required');
            }

            $deactivatedCount = $this->model->deactivateExpired();

            if ($deactivatedCount === false) {
                return $this->sendError(500, 'Failed to auto-deactivate expired businesses');
            }

            $response = [
                'status' => 'success',
                'message' => 'Expired businesses auto-deactivated',
                'deactivated_count' => $deactivatedCount,
                'processed_at' => date('Y-m-d H:i:s'),
                'api_version' => $this->apiVersion
            ];

            $this->logOperation('auto_deactivate_expired', null, ['count' => $deactivatedCount]);
            return $this->sendSuccess($response);
            
        } catch (Exception $e) {
            error_log("Auto deactivate expired error: " . $e->getMessage());
            return $this->sendError(500, 'Internal server error');
        }
    }
    
    /**
     * Handle file upload for business images
     */
    private function handleImageUpload($existingImage = null) {
        if (!class_exists('Config')) {
            require_once __DIR__ . '/../../config/config.php';
        }
        
        $uploadPath = Config::getImageUploadPath();
        error_log("handleImageUpload: Upload path: " . $uploadPath);
        
        if (!isset($_FILES['business_img']) || $_FILES['business_img']['error'] !== UPLOAD_ERR_OK) {
            error_log("handleImageUpload: No file uploaded or upload error. Error code: " . ($_FILES['business_img']['error'] ?? 'N/A'));
            return $existingImage; // Return existing image if no new image uploaded
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = $_FILES['business_img']['type'];
        error_log("handleImageUpload: File type: " . $fileType);
        
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.");
        }

        // Validate file size (5MB max)
        $maxSize = 5 * 1024 * 1024; // 5MB in bytes
        if ($_FILES['business_img']['size'] > $maxSize) {
            error_log("handleImageUpload: File size too large: " . $_FILES['business_img']['size'] . " bytes");
            throw new Exception("File size too large. Maximum size is 5MB.");
        }

        // Create upload directory if it doesn't exist
        if (!is_dir($uploadPath)) {
            if (!mkdir($uploadPath, 0755, true)) {
                error_log("handleImageUpload: Failed to create upload directory: " . $uploadPath);
                throw new Exception("Failed to create upload directory.");
            }
            error_log("handleImageUpload: Created upload directory: " . $uploadPath);
        }

        // Delete old image if it exists
        if ($existingImage) {
            $deleteResult = $this->deleteImageFile($existingImage);
            error_log("handleImageUpload: Delete old image '$existingImage' result: " . ($deleteResult ? 'success' : 'failed'));
        }

        // Generate unique filename
        $extension = pathinfo($_FILES['business_img']['name'], PATHINFO_EXTENSION);
        $imageName = time() . "_" . uniqid() . "." . $extension;
        $targetFile = $uploadPath . $imageName;
        
        error_log("handleImageUpload: Target file: " . $targetFile);
        error_log("handleImageUpload: Image name: " . $imageName);

        if (!move_uploaded_file($_FILES['business_img']['tmp_name'], $targetFile)) {
            error_log("handleImageUpload: Failed to move uploaded file from " . $_FILES['business_img']['tmp_name'] . " to " . $targetFile);
            throw new Exception("Failed to upload image.");
        }

        error_log("handleImageUpload: Successfully uploaded image: " . $imageName);
        return $imageName;
    }

    /**
     * Delete image file from upload directory
     */
    private function deleteImageFile($imageName) {
        if (!$imageName) {
            return false;
        }
        
        if (!class_exists('Config')) {
            require_once __DIR__ . '/../../config/config.php';
        }
        
        $uploadPath = Config::getImageUploadPath();
        $imageFile = $uploadPath . $imageName;
        
        if (file_exists($imageFile)) {
            return unlink($imageFile);
        }
        
        return false;
    }
    
    /**
     * Parse multipart form data manually when $_POST is empty
     */
    private function parseMultipartFormData() {
        $rawInput = file_get_contents('php://input');
        $boundary = '';
        $data = [];
        
        // Extract boundary from Content-Type header
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (preg_match('/boundary=(.+)$/', $contentType, $matches)) {
            $boundary = $matches[1];
        }
        
        if (empty($boundary) || empty($rawInput)) {
            error_log("parseMultipartFormData: No boundary or input found");
            return [];
        }
        
        // Split the raw input by boundary
        $parts = array_slice(explode("--$boundary", $rawInput), 1);
        
        foreach ($parts as $part) {
            // Skip empty parts and end marker
            if (trim($part) === "--" || trim($part) === "") {
                continue;
            }
            
            // Split headers and content
            $sections = explode("\r\n\r\n", $part, 2);
            if (count($sections) !== 2) {
                continue;
            }
            
            $headers = $sections[0];
            $content = rtrim($sections[1], "\r\n");
            
            // Extract field name from Content-Disposition header
            if (preg_match('/name="([^"]+)"/', $headers, $matches)) {
                $fieldName = $matches[1];
                
                // Skip file uploads with no filename or empty files
                if (strpos($headers, 'filename=""') !== false) {
                    continue;
                }
                
                $data[$fieldName] = $content;
                error_log("parseMultipartFormData: Extracted $fieldName = '$content'");
            }
        }
        
        return $data;
    }
}
?>