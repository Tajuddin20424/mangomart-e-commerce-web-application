<?php
// api/logout.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once __DIR__ . '/../includes/auth.php';

$auth = new Auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $auth->logout();
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>