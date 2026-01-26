<?php
class BusinessDetail {
    private $conn;
    private $table =  "business_detail";

    // constructor with $db as database connection
    public function __construct($db) {
        $this ->conn = $db;
    }

    // Helper method to check if a column exists in the table
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

    // create business detail
    public function create($data) {
        // create the query insert data into the table
        $query = "INSERT INTO " . $this->table . " (business_name, business_img, business_contact, business_description, business_category, status, is_featured) VALUES (:name, :img, :contact, :description, :category, :status, :is_featured)";
        
        try {
            // prepare the statement to connect to the database
            $stmt = $this->conn->prepare($query);
            
            // bind the parameters
            $stmt->bindParam(':name', $data['business_name']);
            $stmt->bindParam(':img', $data['business_img']);
            $stmt->bindParam(':contact', $data['business_contact']);
            $stmt->bindParam(':description', $data['business_description']);
            $stmt->bindParam(':category', $data['business_category']);
            $stmt->bindParam(':status', $data['status'], PDO::PARAM_INT);
            $stmt->bindParam(':is_featured', $data['is_featured'], PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Return the ID of the newly created business
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Create business error: " . $e->getMessage());
            return false;
        }
    }

    // update business detail
    public function update($id, $data) {
        $query = 'UPDATE ' . $this->table . ' SET business_name = :name, business_img = :img, business_contact = :contact, business_description = :description, business_category = :category, status = :status, is_featured = :is_featured WHERE id = :id';
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $data['business_name']);
            $stmt->bindParam(':img', $data['business_img']);
            $stmt->bindParam(':contact', $data['business_contact']);
            $stmt->bindParam(':description', $data['business_description']);
            $stmt->bindParam(':category', $data['business_category']);
            $stmt->bindParam(':status', $data['status'], PDO::PARAM_INT);
            $stmt->bindParam(':is_featured', $data['is_featured'], PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Update business error: " . $e->getMessage());
            return false;
        }
    }

    // delete business detail
    public function delete($id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Delete business error: " . $e->getMessage());
            return false;
        }
    }

    // get paginated with business details list
    public function getAll($limit, $offset, $filters = []) {
        $whereClause = '';
        $params = [];
        
        // Handle new status filter (boolean approach)
        if (isset($filters['status_filter'])) {
            if ($filters['status_filter'] === 'active') {
                $whereClause = ' WHERE status = 1';
            } elseif ($filters['status_filter'] === 'inactive') {
                $whereClause = ' WHERE status = 0';
            }
        } else {
            // Default behavior: only active businesses unless include_inactive is set
            if (!isset($filters['include_inactive']) || !$filters['include_inactive']) {
                $whereClause = ' WHERE status = 1';
            }
        }
        
        // Add category filter
        if (!empty($filters['category'])) {
            $whereClause .= ($whereClause ? ' AND' : ' WHERE') . ' business_category = :category';
            $params[':category'] = $filters['category'];
        }
        
        // Add featured filter
        if (isset($filters['featured']) && $filters['featured'] !== '') {
            $whereClause .= ($whereClause ? ' AND' : ' WHERE') . ' is_featured = :featured';
            $params[':featured'] = (int)$filters['featured'];
        }
        
        // Order by featured first, then by ID
        $orderClause = ' ORDER BY is_featured DESC, id DESC';
        
        $query = 'SELECT * FROM ' . $this->table . $whereClause . $orderClause . ' LIMIT :limit OFFSET :offset';
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Bind filter parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            error_log("Get all businesses error: " . $e->getMessage());
            return false;
        }
    }

    // count all business details
    public function countAll($filters = []) {
        $whereClause = '';
        $params = [];
        
        // Handle new status filter (boolean approach)
        if (isset($filters['status_filter'])) {
            if ($filters['status_filter'] === 'active') {
                $whereClause = ' WHERE status = 1';
            } elseif ($filters['status_filter'] === 'inactive') {
                $whereClause = ' WHERE status = 0';
            }
        } else {
            // Default behavior: only active businesses unless include_inactive is set
            if (!isset($filters['include_inactive']) || !$filters['include_inactive']) {
                $whereClause = ' WHERE status = 1';
            }
        }
        
        // Add category filter
        if (!empty($filters['category'])) {
            $whereClause .= ($whereClause ? ' AND' : ' WHERE') . ' business_category = :category';
            $params[':category'] = $filters['category'];
        }
        
        // Add featured filter
        if (isset($filters['featured']) && $filters['featured'] !== '') {
            $whereClause .= ($whereClause ? ' AND' : ' WHERE') . ' is_featured = :featured';
            $params[':featured'] = (int)$filters['featured'];
        }
        
        $query = 'SELECT COUNT(*) as total FROM ' . $this->table . $whereClause;
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Bind filter parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Count all businesses error: " . $e->getMessage());
            return 0;
        }
    }

    // get business detail by id
    public function getById($id) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id = :id';
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get business by ID error: " . $e->getMessage());
            return false;
        }
    }

    // search business by name
    public function searchByName($searchTerm, $limit, $offset, $filters = []) {
        $whereClause = 'WHERE business_name LIKE :searchTerm';
        $params = [':searchTerm' => '%' . $searchTerm . '%'];
        
        // Handle new status filter (boolean approach)
        if (isset($filters['status_filter'])) {
            if ($filters['status_filter'] === 'active') {
                $whereClause .= ' AND status = 1';
            } elseif ($filters['status_filter'] === 'inactive') {
                $whereClause .= ' AND status = 0';
            }
        } else {
            // Default behavior: only active businesses unless include_inactive is set
            if (!isset($filters['include_inactive']) || !$filters['include_inactive']) {
                $whereClause .= ' AND status = 1';
            }
        }
        
        // Add category filter
        if (!empty($filters['category'])) {
            $whereClause .= ' AND business_category = :category';
            $params[':category'] = $filters['category'];
        }
        
        // Add featured filter
        if (isset($filters['featured']) && $filters['featured'] !== '') {
            $whereClause .= ' AND is_featured = :featured';
            $params[':featured'] = (int)$filters['featured'];
        }
        
        // Order by featured first, then by ID
        $orderClause = ' ORDER BY is_featured DESC, id DESC';
        
        $query = 'SELECT * FROM ' . $this->table . ' ' . $whereClause . $orderClause . ' LIMIT :limit OFFSET :offset';
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Bind filter parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            error_log("Search businesses error: " . $e->getMessage());
            return false;
        }
    }

    // count search results by name
    public function countSearchByName($searchTerm, $filters = []) {
        $whereClause = 'WHERE business_name LIKE :searchTerm';
        $params = [':searchTerm' => '%' . $searchTerm . '%'];
        
        // Handle new status filter (boolean approach)
        if (isset($filters['status_filter'])) {
            if ($filters['status_filter'] === 'active') {
                $whereClause .= ' AND status = 1';
            } elseif ($filters['status_filter'] === 'inactive') {
                $whereClause .= ' AND status = 0';
            }
        } else {
            // Default behavior: only active businesses unless include_inactive is set
            if (!isset($filters['include_inactive']) || !$filters['include_inactive']) {
                $whereClause .= ' AND status = 1';
            }
        }
        
        // Add category filter
        if (!empty($filters['category'])) {
            $whereClause .= ' AND business_category = :category';
            $params[':category'] = $filters['category'];
        }
        
        // Add featured filter
        if (isset($filters['featured']) && $filters['featured'] !== '') {
            $whereClause .= ' AND is_featured = :featured';
            $params[':featured'] = (int)$filters['featured'];
        }
        
        $query = 'SELECT COUNT(*) as total FROM ' . $this->table . ' ' . $whereClause;
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Bind filter parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Count search businesses error: " . $e->getMessage());
            return 0;
        }
    }

    // Get analytics data
    public function getAnalytics() {
        try {
            $analytics = [];
            
            // Since columns now exist, let's use them directly
            // Get active count
            $query = 'SELECT COUNT(*) as count FROM ' . $this->table . ' WHERE status = 1';
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $analytics['active_count'] = (int)$result['count'];
            
            // Get deactivated count
            $query = 'SELECT COUNT(*) as count FROM ' . $this->table . ' WHERE status = 0';
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $analytics['deactivated_count'] = (int)$result['count'];
            
            // Get featured businesses count
            $query = 'SELECT COUNT(*) as count FROM ' . $this->table . ' WHERE is_featured = 1';
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $analytics['featured_count'] = (int)$result['count'];
            
            // Get businesses by category
            $query = 'SELECT business_category, COUNT(*) as count FROM ' . $this->table . ' 
                     WHERE business_category IS NOT NULL AND business_category != ""
                     GROUP BY business_category ORDER BY count DESC';
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $analytics['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get total businesses count
            $query = 'SELECT COUNT(*) as count FROM ' . $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $analytics['total_count'] = (int)$result['count'];
            
            // Get recent businesses (last 30 days)
            $query = 'SELECT business_name, business_category, status, is_featured, created_at 
                     FROM ' . $this->table . ' 
                     WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                     ORDER BY created_at DESC LIMIT 10';
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $analytics['recent_businesses'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get businesses that will expire soon (next 7 days)
            $query = 'SELECT business_name, expiry_date FROM ' . $this->table . ' 
                     WHERE expiry_date IS NOT NULL 
                     AND expiry_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
                     ORDER BY expiry_date ASC';
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $analytics['expiring_soon'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $analytics;
        } catch (PDOException $e) {
            error_log("Get analytics error: " . $e->getMessage());
            return false;
        }
    }
    
    // Reactivate business for 1 month
    public function reactivate($id) {
        try {
            // Since columns now exist, use them directly
            $query = 'UPDATE ' . $this->table . ' 
                     SET status = 1, 
                         reactivated_at = NOW(), 
                         expiry_date = DATE_ADD(NOW(), INTERVAL 1 MONTH)
                     WHERE id = :id';
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Reactivate business error: " . $e->getMessage());
            return false;
        }
    }

    // Get featured businesses with highlighting (featured businesses displayed on top)
    public function getFeatured($limit = null, $offset = 0, $filters = []) {
        $whereClause = '';
        $params = [];
        
        // Only get active businesses
        $whereClause = ' WHERE status = 1';
        
        // Add category filter if provided
        if (!empty($filters['category'])) {
            $whereClause .= ' AND business_category = :category';
            $params[':category'] = $filters['category'];
        }
        
        // Order by is_featured DESC (highlighted/featured businesses first), then by creation date DESC
        $orderClause = ' ORDER BY is_featured DESC, created_at DESC';
        
        // Build query with optional limit
        $query = 'SELECT *, 
                         CASE WHEN is_featured = 1 THEN "highlighted" ELSE "normal" END as display_priority,
                         is_featured as is_highlighted
                  FROM ' . $this->table . $whereClause . $orderClause;
        
        if ($limit !== null) {
            $query .= ' LIMIT :limit OFFSET :offset';
        }
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Bind filter parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            // Bind limit and offset if provided
            if ($limit !== null) {
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            error_log("Get featured businesses error: " . $e->getMessage());
            return false;
        }
    }

    // Count featured businesses
    public function countFeatured($filters = []) {
        $whereClause = ' WHERE status = 1';
        $params = [];
        
        // Add category filter if provided
        if (!empty($filters['category'])) {
            $whereClause .= ' AND business_category = :category';
            $params[':category'] = $filters['category'];
        }
        
        $query = 'SELECT COUNT(*) as total FROM ' . $this->table . $whereClause;
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Bind filter parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Count featured businesses error: " . $e->getMessage());
            return 0;
        }
    }

}