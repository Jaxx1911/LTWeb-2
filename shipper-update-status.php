<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

// Check if user is logged in and is a shipper
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shipper') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$order_id = $input['order_id'] ?? null;
$status = $input['status'] ?? null;
$shipper_id = $_SESSION['user_id'];

if (!$order_id || !$status) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Order ID and status are required']);
    exit();
}

// Validate status
$valid_statuses = ['new', 'shipping', 'received'];
if (!in_array($status, $valid_statuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

try {
    // Check if order belongs to this shipper
    $check_sql = "SELECT o.id FROM orders o 
                  JOIN assignment a ON o.id = a.order_id 
                  WHERE o.id = :order_id AND a.user_id = :shipper_id";
    $stmt = $pdo->prepare($check_sql);
    $stmt->execute(['order_id' => $order_id, 'shipper_id' => $shipper_id]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Đơn hàng không thuộc về bạn']);
        exit();
    }
    
    // Update assignment status
    $update_sql = "UPDATE assignment SET status = :status WHERE order_id = :order_id AND user_id = :shipper_id";
    $stmt = $pdo->prepare($update_sql);
    $stmt->execute(['status' => $status, 'order_id' => $order_id, 'shipper_id' => $shipper_id]);
    
    echo json_encode(['success' => true, 'message' => 'Đã cập nhật trạng thái thành công']);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()]);
}
?> 