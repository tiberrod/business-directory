<?php
// Quick database test
try {
    // Try to connect without specifying a database first
    $conn = new PDO("mysql:host=localhost;charset=utf8", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ MySQL connection successful<br>";
    
    // Check if database exists
    $stmt = $conn->prepare("SHOW DATABASES LIKE 'apploqic_business'");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result) {
        echo "✅ Database 'apploqic_business' exists<br>";
        
        // Connect to the database
        $dbConn = new PDO("mysql:host=localhost;dbname=apploqic_business;charset=utf8", "root", "");
        $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if business_detail table exists
        $stmt = $dbConn->prepare("SHOW TABLES LIKE 'business_detail'");
        $stmt->execute();
        $tableResult = $stmt->fetch();
        
        if ($tableResult) {
            echo "✅ Table 'business_detail' exists<br>";
            
            // Count records
            $stmt = $dbConn->prepare("SELECT COUNT(*) as count FROM business_detail");
            $stmt->execute();
            $count = $stmt->fetch();
            echo "📊 Records in business_detail: " . $count['count'] . "<br>";
        } else {
            echo "❌ Table 'business_detail' does not exist<br>";
            echo "Creating table...<br>";
            
            $createTable = "
                CREATE TABLE business_detail (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    business_name VARCHAR(255) NOT NULL,
                    business_contact VARCHAR(255),
                    business_description TEXT,
                    business_category VARCHAR(100),
                    business_img_url VARCHAR(500),
                    is_featured TINYINT DEFAULT 0,
                    status TINYINT DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ";
            
            $dbConn->exec($createTable);
            echo "✅ Table 'business_detail' created successfully<br>";
        }
        
    } else {
        echo "❌ Database 'apploqic_business' does not exist<br>";
        echo "Creating database...<br>";
        
        $conn->exec("CREATE DATABASE apploqic_business");
        echo "✅ Database 'apploqic_business' created<br>";
        
        // Now create the table
        $dbConn = new PDO("mysql:host=localhost;dbname=apploqic_business;charset=utf8", "root", "");
        $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $createTable = "
            CREATE TABLE business_detail (
                id INT AUTO_INCREMENT PRIMARY KEY,
                business_name VARCHAR(255) NOT NULL,
                business_contact VARCHAR(255),
                business_description TEXT,
                business_category VARCHAR(100),
                business_img_url VARCHAR(500),
                is_featured TINYINT DEFAULT 0,
                status TINYINT DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ";
        
        $dbConn->exec($createTable);
        echo "✅ Table 'business_detail' created successfully<br>";
    }
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>