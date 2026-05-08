<?php
// api/get-cart.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/cart.php';

$auth = new Auth();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!$auth->isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login first', 'cart' => [], 'total' => 0]);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    $cart = new Cart();
    $result = $cart->getCart($user_id);
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>