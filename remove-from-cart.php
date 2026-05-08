<?php
// api/remove-from-cart.php
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
    $cart_id = $data['cart_id'] ?? 0;
    
    $cart = new Cart();
    $result = $cart->removeFromCart($user_id, $cart_id);
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>