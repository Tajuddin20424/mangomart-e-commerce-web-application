<?php
// includes/products.php
// Product management

require_once __DIR__ . '/../config/database.php';

class Product {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    public function getAllProducts($limit = null) {
        $sql = "SELECT id, name, variety, description, price, image_url, brix_rating, stock_kg FROM products WHERE is_active = 1";
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        $result = $this->conn->query($sql);
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }
    
    public function getProductById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
        return $product;
    }
    
    public function getBestSelling($limit = 4) {
        $query = "SELECT p.id, p.name, p.variety, p.price, p.image_url, SUM(oi.quantity_kg) as total_sold
                  FROM products p
                  JOIN order_items oi ON p.id = oi.product_id
                  GROUP BY p.id
                  ORDER BY total_sold DESC
                  LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $stmt->close();
        return $products;
    }
    
    public function getStats() {
        $stats = [];
        
        $result = $this->conn->query("SELECT COUNT(*) as total_products FROM products WHERE is_active = 1");
        $stats['total_products'] = $result->fetch_assoc()['total_products'];
        
        $result = $this->conn->query("SELECT SUM(price * stock_kg) as inventory_value FROM products WHERE is_active = 1");
        $stats['inventory_value'] = $result->fetch_assoc()['inventory_value'] ?? 0;
        
        return $stats;
    }
}
?>