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

    // Get order history with shipper information
    $sql = "SELECT a.*, u.name as shipper_name 
            FROM assignment a
            LEFT JOIN users u ON a.user_id = u.id
            WHERE a.order_id = ?
            ORDER BY a.assigned_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$order_id]);
    $history = $stmt->fetchAll();

    // Format dates
    foreach ($history as &$item) {
        $item['assigned_at'] = date('d/m/Y H:i', strtotime($item['assigned_at']));
    }

    echo json_encode([
        'success' => true,
        'history' => $history
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi lấy lịch sử đơn hàng: ' . $e->getMessage()
    ]);
} 