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
$shipper_id = $_SESSION['user_id'];

if (!$order_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Check if order exists and is not assigned
    $check_sql = "SELECT id FROM orders WHERE id = :order_id AND shipper_id IS NULL";
    $stmt = $pdo->prepare($check_sql);
    $stmt->execute(['order_id' => $order_id]);
    
    if (!$stmt->fetch()) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Đơn hàng không tồn tại hoặc đã được nhận']);
        exit();
    }
    
    // Assign order to shipper
    $update_sql = "UPDATE orders SET shipper_id = :shipper_id WHERE id = :order_id";
    $stmt = $pdo->prepare($update_sql);
    $stmt->execute(['shipper_id' => $shipper_id, 'order_id' => $order_id]);
    
    // Create assignment record
    $assignment_sql = "INSERT INTO assignment (order_id, user_id, status) VALUES (:order_id, :user_id, 'new')";
    $stmt = $pdo->prepare($assignment_sql);
    $stmt->execute(['order_id' => $order_id, 'user_id' => $shipper_id]);
    
    $pdo->commit();
    
    echo json_encode(['success' => true, 'message' => 'Đã nhận đơn hàng thành công']);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()]);
}
?> 