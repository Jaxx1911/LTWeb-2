<?php
require_once 'config/database.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    $filter = $_GET['filter'] ?? 'all';
    
    // Base query
    $sql = "SELECT o.*, u.name as shipper_name, 
        COALESCE((SELECT status FROM assignment WHERE order_id = o.id ORDER BY assigned_at DESC LIMIT 1), 'unassigned') as status
        FROM orders o 
        LEFT JOIN users u ON o.shipper_id = u.id";
    
    // Add filter conditions
    switch ($filter) {
        case 'unassigned':
            $sql .= " WHERE o.shipper_id IS NULL";
            break;
        case 'assigned':
            $sql .= " WHERE o.shipper_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM assignment WHERE order_id = o.id AND status = 'received')";
            break;
        case 'completed':
            $sql .= " WHERE EXISTS (SELECT 1 FROM assignment WHERE order_id = o.id AND status = 'received')";
            break;
        case 'all':
        default:
            // No additional filter
            break;
    }
    
    $sql .= " ORDER BY o.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $orders
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi tải dữ liệu: ' . $e->getMessage()
    ]);
}
?> 