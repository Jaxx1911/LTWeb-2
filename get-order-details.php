<?php
require_once 'config/database.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit();
}

try {
    $order_id = $_GET['id'];
    
    // Get order details with shipper information
    $sql = "SELECT o.*, u.name as shipper_name, 
        COALESCE((SELECT status FROM assignment WHERE order_id = o.id ORDER BY assigned_at DESC LIMIT 1), 'unassigned') as status
        FROM orders o 
        LEFT JOIN users u ON o.shipper_id = u.id 
        WHERE o.id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit();
    }
    
    echo json_encode([
        'success' => true,
        'data' => $order
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error loading order details: ' . $e->getMessage()
    ]);
}
?> 