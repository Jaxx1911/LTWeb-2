<?php
require_once 'config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Validate input
    if (!isset($_POST['order_id']) || !isset($_POST['user_id'])) {
        throw new Exception("Order ID and User ID are required");
    }

    // Check if order exists
    $stmt = $pdo->prepare("SELECT id FROM orders WHERE id = ?");
    $stmt->execute([$_POST['order_id']]);
    if (!$stmt->fetch()) {
        throw new Exception("Đơn hàng không tồn tại");
    }

    // Check if user exists and is a shipper
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'shipper' AND status = 'active'");
    $stmt->execute([$_POST['user_id']]);
    if (!$stmt->fetch()) {
        throw new Exception("Shipper không tồn tại hoặc không hoạt động");
    }

    // Start transaction
    $pdo->beginTransaction();

    // Update order's shipper
    $sql = "UPDATE orders SET shipper_id = ?, updated_at = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['user_id'], date('Y-m-d H:i:s'), $_POST['order_id']]);

    // Create new assignment
    $assignment_data = [
        'order_id' => $_POST['order_id'],
        'user_id' => $_POST['user_id'],
        'assigned_at' => date('Y-m-d H:i:s'),
        'status' => 'new'
    ];

    $sql = "INSERT INTO assignment (order_id, user_id, assigned_at, status) 
            VALUES (:order_id, :user_id, :assigned_at, :status)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($assignment_data);

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Giao đơn hàng thành công'
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi giao đơn hàng: ' . $e->getMessage()
    ]);
} 