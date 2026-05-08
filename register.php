<?php
// api/register.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../includes/auth.php';

$auth = new Auth();
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $phone = $data['phone'] ?? '';
    
    $result = $auth->register($name, $email, $password, $phone);
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>