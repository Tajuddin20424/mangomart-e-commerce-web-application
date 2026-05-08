<?php
// api/get-products.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../includes/products.php';

$product = new Product();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : null;
    $products = $product->getAllProducts($limit);
    echo json_encode(['success' => true, 'products' => $products]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>