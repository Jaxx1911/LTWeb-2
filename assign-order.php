<?php
require_once 'config/database.php';
session_start();

header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$order_id = $input['order_id'] ?? null;
$shipper_id = $input['shipper_id'] ?? null;

// Validate input
if (!$order_id || !$shipper_id) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đơn hàng hoặc shipper']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Check if order exists and is not already assigned
    $stmt = $pdo->prepare("SELECT id, shipper_id FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    
    if (!$order) {
        throw new Exception('Đơn hàng không tồn tại');
    }
    
    if ($order['shipper_id']) {
        throw new Exception('Đơn hàng đã được phân công cho shipper khác');
    }
    
    // Check if shipper exists and is active
    $stmt = $pdo->prepare("SELECT id, name FROM users WHERE id = ? AND role = 'shipper' AND status = 'active'");
    $stmt->execute([$shipper_id]);
    $shipper = $stmt->fetch();
    
    if (!$shipper) {
        throw new Exception('Shipper không tồn tại hoặc không hoạt động');
    }
    
    // Update order with shipper_id
    $stmt = $pdo->prepare("UPDATE orders SET shipper_id = ? WHERE id = ?");
    $stmt->execute([$shipper_id, $order_id]);
    
    // Create assignment record
    $stmt = $pdo->prepare("INSERT INTO assignment (order_id, user_id, assigned_at, status) VALUES (?, ?, NOW(), 'new')");
    $stmt->execute([$order_id, $shipper_id]);
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Phân công đơn hàng thành công cho ' . $shipper['name']
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()]);
}
?> 