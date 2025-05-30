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
    $sql = "SELECT id, username, email, name, code, phone, status, area, address, salary 
            FROM users WHERE role = 'shipper'";
    
    // Add filter conditions
    switch ($filter) {
        case 'active':
            $sql .= " AND status = 'active'";
            break;
        case 'inactive':
            $sql .= " AND status = 'inactive'";
            break;
        case 'all':
        default:
            // No additional filter
            break;
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $shippers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $shippers
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi tải dữ liệu: ' . $e->getMessage()
    ]);
}
?> 