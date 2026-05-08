<?php
// config/database.php
// Database configuration file

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mangomart_db');

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        
        // Create database if not exists
        $this->conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
        $this->conn->select_db(DB_NAME);
        
        // Create tables
        $this->createTables();
    }

    private function createTables() {
        $tables = [
            "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                phone VARCHAR(20),
                address TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
            "CREATE TABLE IF NOT EXISTS products (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                variety VARCHAR(100) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) NOT NULL,
                image_url VARCHAR(500),
                brix_rating DECIMAL(3,1),
                stock_kg INT DEFAULT 100,
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
            "CREATE TABLE IF NOT EXISTS cart (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                product_id INT NOT NULL,
                quantity_kg DECIMAL(5,2) NOT NULL,
                added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            )",
            
            "CREATE TABLE IF NOT EXISTS orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_number VARCHAR(50) UNIQUE NOT NULL,
                user_id INT NOT NULL,
                total_amount DECIMAL(10,2) NOT NULL,
                status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
                payment_method VARCHAR(50),
                shipping_address TEXT,
                phone VARCHAR(20),
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )",
            
            "CREATE TABLE IF NOT EXISTS order_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                product_id INT NOT NULL,
                product_name VARCHAR(100) NOT NULL,
                quantity_kg DECIMAL(5,2) NOT NULL,
                price_per_kg DECIMAL(10,2) NOT NULL,
                subtotal DECIMAL(10,2) NOT NULL,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id)
            )",
            
            "CREATE TABLE IF NOT EXISTS reviews (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                product_id INT NOT NULL,
                rating INT CHECK (rating >= 1 AND rating <= 5),
                comment TEXT,
                is_approved BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            )",
            
            "CREATE TABLE IF NOT EXISTS inquiries (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                phone VARCHAR(20),
                subject VARCHAR(200),
                message TEXT,
                status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )"
        ];
        
        foreach ($tables as $sql) {
            $this->conn->query($sql);
        }
        
        // Insert sample products if empty
        $result = $this->conn->query("SELECT COUNT(*) as count FROM products");
        $row = $result->fetch_assoc();
        if ($row['count'] == 0) {
            $this->insertSampleProducts();
        }
    }
    
    private function insertSampleProducts() {
        $products = [
            ["Himsagar", "Himsagar", "King of aroma, juicy & fiberless mango from Rajshahi", 190, "https://images.pexels.com/photos/1092730/pexels-photo-1092730.jpeg", 22.5, 500],
            ["Langra", "Langra", "Sweet tangy golden pulp mango", 170, "https://images.pexels.com/photos/8152053/pexels-photo-8152053.jpeg", 21.8, 450],
            ["Amrapali", "Amrapali", "Deep orange, rich flavor mango", 210, "https://images.pexels.com/photos/568617/pexels-photo-568617.jpeg", 23.0, 400],
            ["Fazli", "Fazli", "Giant size, unique taste mango", 250, "https://images.pexels.com/photos/2320594/pexels-photo-2320594.jpeg", 22.0, 300]
        ];
        
        $stmt = $this->conn->prepare("INSERT INTO products (name, variety, description, price, image_url, brix_rating, stock_kg) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($products as $product) {
            $stmt->bind_param("sssdssi", $product[0], $product[1], $product[2], $product[3], $product[4], $product[5], $product[6]);
            $stmt->execute();
        }
        $stmt->close();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
}

// Start session
session_start();
?>