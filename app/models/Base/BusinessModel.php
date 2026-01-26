<?php
/**
 * Standard Business Model (Base Model)
 * 
 * This is the standard model that supports all API versions
 * It implements the BusinessModelInterface and provides a stable foundation
 * that can adapt to different API version requirements
 */

require_once __DIR__ . '/../Interfaces/BusinessModelInterface.php';

class BusinessModel implements BusinessModelInterface {
    
    protected $conn;
    protected $table = "business_detail";
    
    // Database schema mapping for all supported versions
    protected $schemaVersions = [
        'v1' => [
            'id', 'business_name', 'business_img', 'business_contact', 
            'business_description', 'business_category', 'status', 
            'expiry_date', 'reactivated_at', 'is_featured', 
            'created_at', 'updated_at', 'deactivated_reason'
        ],
        'legacy' => [
            'id', 'business_name', 'business_img', 'business_contact',
            'business_description', 'business_category', 'status', 'is_featured'
        ]
    ];
    
    // Current API version context
    protected $apiVersion = 'v1';
    
    public function __construct($db, $apiVersion = 'v1') {
        $this->conn = $db;
        $this->apiVersion = $apiVersion;
    }
    
    /**
     * Set API version context for the model
     */
    public function setApiVersion($version) {
        $this->apiVersion = $version;
        return $this;
    }
    
    /**
     * Get current API version
     */
    public function getApiVersion() {
        return $this->apiVersion;
    }
    
    /**
     * Get supported columns for current API version
     */
    public function getSupportedColumns() {
        return $this->schemaVersions[$this->apiVersion] ?? $this->schemaVersions['v1'];
    }
    
    /**
     * Get table name
     */
    public function getTableName() {
        return $this->table;
    }
    
    /**
     * Check if a column exists in the table
     */
    public function columnExists($columnName) {
        try {
            $query = 'SHOW COLUMNS FROM ' . $this->table . ' LIKE :columnName';
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':columnName', $columnName);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Column existence check error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create a new business
     * Adapts to different API version requirements
     */
    public function create($data) {
        try {
            // Filter data based on current API version
            $filteredData = $this->filterDataForVersion($data);
            
            // Build dynamic insert query
            $columns = array_keys($filteredData);
            $placeholders = array_map(function($col) { return ':' . $col; }, $columns);
            
            $query = "INSERT INTO " . $this->table . " 
                      (" . implode(', ', $columns) . ") 
                      VALUES (" . implode(', ', $placeholders) . ")";
            
            $stmt = $this->conn->prepare($query);
            
            // Set defaults based on API version
            $this->setVersionDefaults($filteredData);
            
            // Bind parameters
            foreach ($filteredData as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
            
        } catch (PDOException $e) {
            error_log("Create business error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update an existing business
     * Supports partial updates with version-aware field filtering
     */
    public function update($id, $data) {
        try {
            // Filter data based on current API version
            $filteredData = $this->filterDataForVersion($data);
            
            if (empty($filteredData)) {
                return false;
            }
            
            // Build dynamic update query
            $setParts = [];
            foreach (array_keys($filteredData) as $column) {
                $setParts[] = "$column = :$column";
            }
            
            $query = 'UPDATE ' . $this->table . ' 
                      SET ' . implode(', ', $setParts) . ' 
                      WHERE id = :id';
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            foreach ($filteredData as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Update business error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Soft delete (deactivate) a business
     */
    public function deactivate($id, $reason = 'Non-payment of monthly fee') {
        try {
            $updateData = ['status' => 0];
            
            // Add reason if supported in current version
            if (in_array('deactivated_reason', $this->getSupportedColumns())) {
                $updateData['deactivated_reason'] = $reason;
            }
            
            return $this->update($id, $updateData);
            
        } catch (PDOException $e) {
            error_log("Deactivate business error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reactivate a business
     */
    public function reactivate($id) {
        try {
            $updateData = ['status' => 1];
            
            // Add reactivation timestamp and clear reason if supported
            $supportedColumns = $this->getSupportedColumns();
            
            if (in_array('reactivated_at', $supportedColumns)) {
                $updateData['reactivated_at'] = date('Y-m-d H:i:s');
            }
            
            if (in_array('deactivated_reason', $supportedColumns)) {
                $updateData['deactivated_reason'] = null;
            }
            
            if (in_array('expiry_date', $supportedColumns)) {
                $updateData['expiry_date'] = date('Y-m-d H:i:s', strtotime('+1 month'));
            }
            
            return $this->update($id, $updateData);
            
        } catch (PDOException $e) {
            error_log("Reactivate business error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Hard delete a business
     */
    public function delete($id) {
        try {
            $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Delete business error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get a single business by ID
     */
    public function getById($id) {
        try {
            $query = 'SELECT * FROM ' . $this->table . ' WHERE id = :id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Filter result based on API version
            return $result ? $this->filterResultForVersion($result) : false;
            
        } catch (PDOException $e) {
            error_log("Get business by ID error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all businesses with filtering and pagination
     */
    public function getAll($limit = 10, $offset = 0, $filters = []) {
        try {
            $whereClause = $this->buildWhereClause($filters);
            $params = $this->buildWhereParams($filters);
            
            $orderClause = ' ORDER BY is_featured DESC, created_at DESC';
            
            $query = 'SELECT * FROM ' . $this->table . $whereClause . $orderClause . 
                     ' LIMIT :limit OFFSET :offset';
            
            $stmt = $this->conn->prepare($query);
            
            // Bind filter parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt;
            
        } catch (PDOException $e) {
            error_log("Get all businesses error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Count all businesses with filters
     */
    public function countAll($filters = []) {
        try {
            $whereClause = $this->buildWhereClause($filters);
            $params = $this->buildWhereParams($filters);
            
            $query = 'SELECT COUNT(*) as total FROM ' . $this->table . $whereClause;
            
            $stmt = $this->conn->prepare($query);
            
            // Bind filter parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? (int)$result['total'] : 0;
            
        } catch (PDOException $e) {
            error_log("Count businesses error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Search businesses by name
     */
    public function search($searchTerm, $filters = [], $limit = 10, $offset = 0) {
        try {
            $whereConditions = ['business_name LIKE :search'];
            $params = [':search' => '%' . $searchTerm . '%'];
            
            // Add additional filters
            $additionalWhere = $this->buildWhereClause($filters, false);
            $additionalParams = $this->buildWhereParams($filters);
            
            if ($additionalWhere) {
                $whereConditions[] = substr($additionalWhere, 7); // Remove " WHERE "
                $params = array_merge($params, $additionalParams);
            }
            
            $whereClause = ' WHERE ' . implode(' AND ', $whereConditions);
            $orderClause = ' ORDER BY is_featured DESC, business_name ASC';
            
            $query = 'SELECT * FROM ' . $this->table . $whereClause . $orderClause . 
                     ' LIMIT :limit OFFSET :offset';
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt;
            
        } catch (PDOException $e) {
            error_log("Search businesses error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get featured businesses
     */
    public function getFeatured($limit = 10, $offset = 0, $filters = []) {
        try {
            $baseFilters = array_merge($filters, ['featured' => 1]);
            $whereClause = $this->buildWhereClause($baseFilters);
            $params = $this->buildWhereParams($baseFilters);
            
            $featuredQuery = 'SELECT * FROM ' . $this->table . $whereClause . 
                           ' ORDER BY created_at DESC';
            
            $stmt = $this->conn->prepare($featuredQuery);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $featured = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get regular businesses
            $regularFilters = array_merge($filters, ['featured' => 0]);
            $regularStmt = $this->getAll($limit, $offset, $regularFilters);
            $regular = $regularStmt ? $regularStmt->fetchAll(PDO::FETCH_ASSOC) : [];
            
            return [
                'highlighted' => array_map([$this, 'filterResultForVersion'], $featured),
                'normal' => array_map([$this, 'filterResultForVersion'], $regular),
                'all' => array_merge(
                    array_map([$this, 'filterResultForVersion'], $featured),
                    array_map([$this, 'filterResultForVersion'], $regular)
                )
            ];
            
        } catch (PDOException $e) {
            error_log("Get featured businesses error: " . $e->getMessage());
            return [
                'highlighted' => [],
                'normal' => [],
                'all' => []
            ];
        }
    }
    
    /**
     * Filter data based on current API version
     */
    private function filterDataForVersion($data) {
        $supportedColumns = $this->getSupportedColumns();
        return array_intersect_key($data, array_flip($supportedColumns));
    }
    
    /**
     * Filter result based on current API version
     */
    private function filterResultForVersion($result) {
        if (!$result) return $result;
        
        $supportedColumns = $this->getSupportedColumns();
        return array_intersect_key($result, array_flip($supportedColumns));
    }
    
    /**
     * Set version-specific defaults
     */
    private function setVersionDefaults(&$data) {
        switch ($this->apiVersion) {
            case 'v1':
                if (!isset($data['status'])) $data['status'] = 1;
                if (!isset($data['is_featured'])) $data['is_featured'] = 0;
                if (!isset($data['expiry_date']) && $this->columnExists('expiry_date')) {
                    $data['expiry_date'] = date('Y-m-d H:i:s', strtotime('+1 month'));
                }
                break;
                
            case 'legacy':
                if (!isset($data['status'])) $data['status'] = 1;
                if (!isset($data['is_featured'])) $data['is_featured'] = 0;
                break;
        }
    }
    
    /**
     * Build WHERE clause for queries
     */
    private function buildWhereClause($filters, $includeWhere = true) {
        $whereConditions = [];
        
        // Default: only active businesses unless specified
        if (!isset($filters['include_inactive']) || !$filters['include_inactive']) {
            $whereConditions[] = 'status = 1';
        }
        
        // Status filter override
        if (isset($filters['status']) && $filters['status'] !== '') {
            $whereConditions = []; // Reset default status filter
            $status = strtolower(trim($filters['status']));
            if ($status === 'active' || $status === 'true' || $status === '1') {
                $whereConditions[] = 'status = 1';
            } elseif ($status === 'inactive' || $status === 'false' || $status === '0') {
                $whereConditions[] = 'status = 0';
            }
        }
        
        // Category filter
        if (!empty($filters['category'])) {
            $whereConditions[] = 'business_category = :category';
        }
        
        // Featured filter
        if (isset($filters['featured']) && $filters['featured'] !== '') {
            $whereConditions[] = 'is_featured = :featured';
        }
        
        $result = !empty($whereConditions) ? implode(' AND ', $whereConditions) : '';
        return $result && $includeWhere ? ' WHERE ' . $result : ($result ? ' ' . $result : '');
    }
    
    /**
     * Build parameters for WHERE clause
     */
    private function buildWhereParams($filters) {
        $params = [];
        
        if (!empty($filters['category'])) {
            $params[':category'] = $filters['category'];
        }
        
        if (isset($filters['featured']) && $filters['featured'] !== '') {
            $params[':featured'] = (int)$filters['featured'];
        }
        
        return $params;
    }
    
    /**
     * Create business with 30-day expiry (Enhanced v1.1.0)
     */
    public function createWithExpiry($data) {
        try {
            // Add 30-day expiry from creation using correct field name
            $data['expiry_date'] = date('Y-m-d H:i:s', strtotime('+30 days'));
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['status'] = 1; // Use integer instead of string
            
            // Handle featured status properly
            if (!isset($data['is_featured'])) {
                $data['is_featured'] = 0;
            }
            
            return $this->create($data);
            
        } catch (Exception $e) {
            error_log("Create business with expiry error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reactivate business with new 30-day period (Enhanced v1.1.0)
     */
    public function reactivateWithExpiry($id, $adminId = null) {
        try {
            $data = [
                'status' => 1, // Use integer instead of string
                'reactivated_at' => date('Y-m-d H:i:s'),
                'expiry_date' => date('Y-m-d H:i:s', strtotime('+30 days')), // Use correct field name
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            if ($adminId) {
                $data['reactivated_by'] = $adminId;
            }
            
            return $this->update($id, $data);
            
        } catch (Exception $e) {
            error_log("Reactivate business error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Set featured status (Enhanced v1.1.0)
     */
    public function setFeaturedStatus($id, $featured = true) {
        try {
            $data = [
                'is_featured' => $featured ? 1 : 0, // Use only the single featured field
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            return $this->update($id, $data);
            
        } catch (Exception $e) {
            error_log("Set featured status error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if business is expired (Enhanced v1.1.0)
     */
    public function isExpired($id) {
        try {
            $query = "SELECT expiry_date FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && $result['expiry_date']) {
                return strtotime($result['expiry_date']) < time();
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Check expiry error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get businesses expiring soon (Enhanced v1.1.0)
     */
    public function getExpiringSoon($days = 7) {
        try {
            $query = "SELECT * FROM " . $this->table . " 
                      WHERE status = 1 
                      AND expiry_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL :days DAY)
                      ORDER BY expiry_date ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':days', $days, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt;
            
        } catch (Exception $e) {
            error_log("Get expiring soon error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Auto-deactivate expired businesses (Enhanced v1.1.0)
     */
    public function deactivateExpired() {
        try {
            $query = "UPDATE " . $this->table . " 
                      SET status = 0, 
                          deactivated_reason = 'Auto-deactivated: Expired',
                          updated_at = NOW()
                      WHERE status = 1 
                      AND expiry_date < NOW()";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->rowCount();
            
        } catch (Exception $e) {
            error_log("Auto deactivate expired error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Search businesses with enhanced filtering (Enhanced v1.1.0)
     */
    public function searchEnhanced($criteria) {
        try {
            $conditions = [];
            $params = [];
            
            // Base query with all enhanced fields
            $query = "SELECT b.*, 
                             CASE WHEN b.is_featured = 1 THEN 1 ELSE 0 END as is_featured_bool,
                             CASE WHEN b.expiry_date < NOW() THEN 1 ELSE 0 END as is_expired,
                             DATEDIFF(b.expiry_date, NOW()) as days_until_expiry
                      FROM " . $this->table . " b WHERE 1=1";
            
            // Search term (business name, description)
            if (!empty($criteria['search']) && trim($criteria['search']) !== '') {
                $conditions[] = "(b.business_name LIKE :search OR b.business_description LIKE :search)";
                $params[':search'] = '%' . trim($criteria['search']) . '%';
            }
            
            // Category filter
            if (!empty($criteria['category'])) {
                $conditions[] = "b.business_category = :category";
                $params[':category'] = $criteria['category'];
            }
            
            // Status filter
            if (isset($criteria['status']) && $criteria['status'] !== null) {
                $conditions[] = "b.status = :status";
                $params[':status'] = $criteria['status'];
            }
            
            // Featured filter
            if (isset($criteria['featured'])) {
                if ($criteria['featured']) {
                    $conditions[] = "b.is_featured = 1";
                } else {
                    $conditions[] = "b.is_featured = 0";
                }
            }
            
            // Active/Expired filter
            if (isset($criteria['expired'])) {
                if ($criteria['expired']) {
                    $conditions[] = "b.expiry_date < NOW()";
                } else {
                    $conditions[] = "b.expiry_date >= NOW()";
                }
            }
            
            // Add conditions to query
            if (!empty($conditions)) {
                $query .= " AND " . implode(" AND ", $conditions);
            }
            
            // Add ordering
            $orderBy = " ORDER BY ";
            if (!empty($criteria['featured']) && $criteria['featured']) {
                $orderBy .= "b.is_featured DESC, ";
            }
            $orderBy .= "b.created_at DESC";
            
            $query .= $orderBy;
            
            // Add pagination
            if (isset($criteria['limit'])) {
                $query .= " LIMIT :limit";
                if (isset($criteria['offset'])) {
                    $query .= " OFFSET :offset";
                }
            }
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            if (isset($criteria['limit'])) {
                $stmt->bindValue(':limit', (int)$criteria['limit'], PDO::PARAM_INT);
                if (isset($criteria['offset'])) {
                    $stmt->bindValue(':offset', (int)$criteria['offset'], PDO::PARAM_INT);
                }
            }
            
            $stmt->execute();
            return $stmt;
            
        } catch (Exception $e) {
            error_log("Enhanced search error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update image URL (Enhanced v1.1.0)
     */
    public function updateImageUrl($id, $imageUrl) {
        try {
            $data = [
                'business_img' => $imageUrl, // Use only the single image field
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            return $this->update($id, $data);
            
        } catch (Exception $e) {
            error_log("Update image URL error: " . $e->getMessage());
            return false;
        }
    }
}
?>