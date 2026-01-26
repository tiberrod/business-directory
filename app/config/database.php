<?php

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;

    public $conn;

    public function __construct() {
        // Auto-detect environment and use appropriate database settings
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
        $port = $_SERVER['SERVER_PORT'] ?? '80';
        
        // Check for localhost environment (including development server)
        if ($host === 'localhost' || 
            strpos($host, '127.0.0.1') !== false || 
            strpos($host, 'xampp') !== false ||
            $port === '8080' ||
            $port === '8000' ||
            strpos($_SERVER['REQUEST_URI'] ?? '', 'localhost') !== false ||
            strpos($_SERVER['SCRIPT_NAME'] ?? '', 'xampp') !== false) {
            
            // XAMPP localhost development settings
            $this->host = "localhost";
            $this->db_name = "apploqic_business";
            $this->username = "root";
            $this->password = "";
        } else {
            // Production cPanel database settings for apploqic.my
            $this->host = "localhost";
            $this->db_name = "apploqic_business"; // Your cPanel database name
            $this->username = "apploqic_business"; // Your cPanel database user
            $this->password = "business_02>^te"; // Your database password
        }
    }

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8",
             $this->username, 
             $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // Simple error handling for online database
            error_log("Database connection error: " . $exception->getMessage());
            echo json_encode([
                "status" => 500,
                "message" => "Database connection failed. Please try again later."
            ]);
        }

        return $this->conn;
    }
}