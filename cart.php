<?php
// includes/cart.php
// Shopping cart management

require_once __DIR__ . '/../config/database.php';

class Cart {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    public function addToCart($user_id, $product_id, $quantity_kg = 1) {
        // Check product stock
        $prod_check = $this->conn->prepare("SELECT name, price, stock_kg FROM products WHERE id = ? AND is_active = 1");
        $prod_check->bind_param("i", $product_id);
        $prod_check->execute();
        $product = $prod_check->get_result()->fetch_assoc();
        
        if (!$product) {
            $prod_check->close();
            return ['success' => false, 'message' => 'Product not found'];
        }
        
        if ($product['stock_kg'] < $quantity_kg) {
            $prod_check->close();
            return ['success' => false, 'message' => 'Insufficient stock'];
        }
        $prod_check->close();
        
        // Check if already in cart
        $cart_check = $this->conn->prepare("SELECT id, quantity_kg FROM cart WHERE user_id = ? AND product_id = ?");
        $cart_check->bind_param("ii", $user_id, $product_id);
        $cart_check->execute();
        $existing = $cart_check->get_result()->fetch_assoc();
        
        if ($existing) {
            $new_qty = $existing['quantity_kg'] + $quantity_kg;
            $update = $this->conn->prepare("UPDATE cart SET quantity_kg = ? WHERE id = ?");
            $update->bind_param("di", $new_qty, $existing['id']);
            $update->execute();
            $update->close();
        } else {
            $insert = $this->conn->prepare("INSERT INTO cart (user_id, product_id, quantity_kg) VALUES (?, ?, ?)");
            $insert->bind_param("iid", $user_id, $product_id, $quantity_kg);
            $insert->execute();
            $insert->close();
        }
        
        $cart_check->close();
        return ['success' => true, 'message' => $product['name'] . ' added to cart'];
    }
    
    public function getCart($user_id) {
        $cart_items = [];
        $total = 0;
        
        $query = "SELECT c.id, c.product_id, c.quantity_kg, p.name, p.price, p.image_url 
                  FROM cart c 
                  JOIN products p ON c.product_id = p.id 
                  WHERE c.user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($item = $result->fetch_assoc()) {
            $subtotal = $item['quantity_kg'] * $item['price'];
            $total += $subtotal;
            $cart_items[] = [
                'cart_id' => $item['id'],
                'product_id' => $item['product_id'],
                'name' => $item['name'],
                'quantity_kg' => $item['quantity_kg'],
                'price' => $item['price'],
                'subtotal' => $subtotal,
                'image_url' => $item['image_url']
            ];
        }
        $stmt->close();
        
        return ['success' => true, 'cart' => $cart_items, 'total' => $total, 'item_count' => count($cart_items)];
    }
    
    public function updateCart($user_id, $cart_id, $quantity_kg) {
        if ($quantity_kg <= 0) {
            return $this->removeFromCart($user_id, $cart_id);
        }
        
        $update = $this->conn->prepare("UPDATE cart SET quantity_kg = ? WHERE id = ? AND user_id = ?");
        $update->bind_param("dii", $quantity_kg, $cart_id, $user_id);
        $success = $update->execute();
        $update->close();
        
        return ['success' => $success];
    }
    
    public function removeFromCart($user_id, $cart_id) {
        $delete = $this->conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $delete->bind_param("ii", $cart_id, $user_id);
        $success = $delete->execute();
        $delete->close();
        
        return ['success' => $success];
    }
    
    public function clearCart($user_id) {
        $delete = $this->conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $delete->bind_param("i", $user_id);
        $success = $delete->execute();
        $delete->close();
        
        return ['success' => $success];
    }
}
?>