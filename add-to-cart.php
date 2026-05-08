<?php
// api/add-to-cart.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/cart.php';

$auth = new Auth();
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$auth->isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    $product_id = $data['product_id'] ?? 0;
    $quantity_kg = $data['quantity_kg'] ?? 1;
    
    if (!$product_id) {
        echo json_encode(['success' => false, 'message' => 'Product ID required']);
        exit;
    }
    
    $cart = new Cart();
    $result = $cart->addToCart($user_id, $product_id, $quantity_kg);
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>