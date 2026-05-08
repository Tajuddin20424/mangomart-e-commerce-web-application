<?php
// api/place-order.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/orders.php';

$auth = new Auth();
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$auth->isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    $shipping_address = $data['shipping_address'] ?? '';
    $phone = $data['phone'] ?? '';
    $payment_method = $data['payment_method'] ?? 'cod';
    $notes = $data['notes'] ?? '';
    
    if (empty($shipping_address)) {
        echo json_encode(['success' => false, 'message' => 'Shipping address required']);
        exit;
    }
    
    if (empty($phone)) {
        echo json_encode(['success' => false, 'message' => 'Phone number required']);
        exit;
    }
    
    $order = new Order();
    $result = $order->createOrder($user_id, $shipping_address, $phone, $payment_method, $notes);
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>