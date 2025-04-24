<?php
header('Content-Type: application/json');
require_once 'UpdateOrderStatus.php';

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Get and validate input
$order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
$new_status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

if (!$order_id || !$new_status) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Missing or invalid parameters'
    ]);
    exit;
}

// Update the order status
$result = UpdateOrderStatus($order_id, $new_status);

// Set appropriate status code
http_response_code($result['success'] ? 200 : 400);

// Return the result
echo json_encode($result); 