<?php
// includes/orders.php
// Order processing

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/cart.php';

class Order {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    public function createOrder($user_id, $shipping_address, $phone, $payment_method = 'cod', $notes = '') {
        $cart = new Cart();
        $cart_data = $cart->getCart($user_id);
        
        if (empty($cart_data['cart'])) {
            return ['success' => false, 'message' => 'Cart is empty'];
        }
        
        // Calculate total and check stock
        $total = 0;
        $cart_items = [];
        
        foreach ($cart_data['cart'] as $item) {
            // Verify stock again
            $stock_check = $this->conn->prepare("SELECT stock_kg FROM products WHERE id = ?");
            $stock_check->bind_param("i", $item['product_id']);
            $stock_check->execute();
            $stock_result = $stock_check->get_result()->fetch_assoc();
            $stock_check->close();
            
            if (!$stock_result || $stock_result['stock_kg'] < $item['quantity_kg']) {
                return ['success' => false, 'message' => "Insufficient stock for {$item['name']}"];
            }
            
            $subtotal = $item['quantity_kg'] * $item['price'];
            $total += $subtotal;
            $cart_items[] = $item;
        }
        
        // Generate unique order number
        $order_number = 'MGO-' . strtoupper(uniqid()) . '-' . date('Ymd');
        
        // Start transaction
        $this->conn->begin_transaction();
        
        try {
            // Create order
            $insert_order = $this->conn->prepare("INSERT INTO orders (order_number, user_id, total_amount, payment_method, shipping_address, phone, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insert_order->bind_param("sidssss", $order_number, $user_id, $total, $payment_method, $shipping_address, $phone, $notes);
            $insert_order->execute();
            $order_id = $this->conn->insert_id;
            $insert_order->close();
            
            // Insert order items and update stock
            $insert_item = $this->conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity_kg, price_per_kg, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
            $update_stock = $this->conn->prepare("UPDATE products SET stock_kg = stock_kg - ? WHERE id = ?");
            
            foreach ($cart_items as $item) {
                $subtotal = $item['quantity_kg'] * $item['price'];
                $insert_item->bind_param("iissdd", $order_id, $item['product_id'], $item['name'], $item['quantity_kg'], $item['price'], $subtotal);
                $insert_item->execute();
                
                $update_stock->bind_param("di", $item['quantity_kg'], $item['product_id']);
                $update_stock->execute();
            }
            $insert_item->close();
            $update_stock->close();
            
            // Clear cart
            $cart->clearCart($user_id);
            
            $this->conn->commit();
            return ['success' => true, 'message' => 'Order placed successfully!', 'order_number' => $order_number, 'order_id' => $order_id];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => 'Order failed: ' . $e->getMessage()];
        }
    }
    
    public function getUserOrders($user_id, $limit = 10) {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->bind_param("ii", $user_id, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = [];
        while ($order = $result->fetch_assoc()) {
            $orders[] = $order;
        }
        $stmt->close();
        return $orders;
    }
    
    public function getOrderDetails($order_id) {
        $stmt = $this->conn->prepare("SELECT o.*, oi.product_name, oi.quantity_kg, oi.price_per_kg, oi.subtotal 
                                      FROM orders o 
                                      JOIN order_items oi ON o.id = oi.order_id 
                                      WHERE o.id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $order_details = null;
        $items = [];
        
        while ($row = $result->fetch_assoc()) {
            if (!$order_details) {
                $order_details = [
                    'order_number' => $row['order_number'],
                    'total_amount' => $row['total_amount'],
                    'status' => $row['status'],
                    'created_at' => $row['created_at'],
                    'shipping_address' => $row['shipping_address']
                ];
            }
            $items[] = [
                'product_name' => $row['product_name'],
                'quantity_kg' => $row['quantity_kg'],
                'price_per_kg' => $row['price_per_kg'],
                'subtotal' => $row['subtotal']
            ];
        }
        $stmt->close();
        
        return ['order' => $order_details, 'items' => $items];
    }
}
?>