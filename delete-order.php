<?php
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID đơn hàng không được cung cấp']);
    exit;
}

try {
    $order_id = $_GET['id'];

    // Start transaction
    $pdo->beginTransaction();

    // Delete assignments first (due to foreign key constraint)
    $sql = "DELETE FROM assignment WHERE order_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$order_id]);

    // Delete order
    $sql = "DELETE FROM orders WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$order_id]);

    // Check if order was actually deleted
    if ($stmt->rowCount() === 0) {
        throw new Exception("Không tìm thấy đơn hàng");
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Xóa đơn hàng thành công'
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi xóa đơn hàng: ' . $e->getMessage()
    ]);
} 