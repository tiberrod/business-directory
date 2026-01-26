<?php

class Database {
    // Production settings - update these with your cPanel database details
    private $host = "localhost"; // Usually localhost for cPanel
    private $db_name = "apploqic_business"; // cPanel format: username_databasename
    private $username = "apploqic_business"; // cPanel format: username_databaseuser
    private $password = "business_02>^te"; // Your database password
    
    // Development settings (commented out for production)
    // private $host = "localhost";
    // private $db_name = "apploqic_business";
    // private $username = "root";
    // private $password = "";

    public $conn;

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
            // In production, don't expose database errors to users
            error_log("Database connection error: " . $exception->getMessage());
            echo json_encode([
                "status" => 500,
                "message" => "Database connection failed. Please try again later."
            ]);
            exit();
        }

        return $this->conn;
    }
}