<?php
// api/submit-review.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../includes/auth.php';

$auth = new Auth();
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$auth->isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login to submit review']);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    $product_id = $data['product_id'] ?? 0;
    $rating = $data['rating'] ?? 0;
    $comment = $data['comment'] ?? '';
    
    if (!$product_id || $rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Invalid review data']);
        exit;
    }
    
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("INSERT INTO reviews (user_id, product_id, rating, comment, is_approved) VALUES (?, ?, ?, ?, 1)");
    $stmt->bind_param("iiis", $user_id, $product_id, $rating, $comment);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Review submitted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit review']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>