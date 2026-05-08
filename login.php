<?php
// api/login.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../includes/auth.php';

$auth = new Auth();
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    
    $result = $auth->login($email, $password);
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>