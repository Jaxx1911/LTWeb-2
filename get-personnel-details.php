<?php
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isset($_GET['code'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Mã nhân viên không được cung cấp']);
    exit;
}

try {
    $code = $_GET['code'];
    
    // Get personnel details
    $sql = "SELECT u.*, 
            (SELECT COUNT(*) FROM orders WHERE shipper_id = u.id) as total_deliveries
            FROM users u 
            WHERE u.code = :code AND u.role = 'shipper'";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['code' => $code]);
    $personnel = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$personnel) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy nhân viên']);
        exit;
    }

    // Get recent orders
    $sql = "SELECT o.*
            FROM orders o
            WHERE o.shipper_id = :shipper_id
            ORDER BY o.created_at DESC
            LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['shipper_id' => $personnel['id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'personnel' => $personnel,
            'orders' => $orders
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi lấy thông tin: ' . $e->getMessage()
    ]);
} 