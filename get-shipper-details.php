<?php
require_once 'config/database.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check if shipper ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Shipper ID is required']);
    exit();
}

try {
    $shipper_id = $_GET['id'];
    
    // Get shipper details
    $sql = "SELECT id, username, email, name, code, phone, status, area, address, salary, note, created_at
            FROM users 
            WHERE id = ? AND role = 'shipper'";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$shipper_id]);
    $shipper = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$shipper) {
        echo json_encode(['success' => false, 'message' => 'Shipper not found']);
        exit();
    }
    
    echo json_encode([
        'success' => true,
        'data' => $shipper
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error loading shipper details: ' . $e->getMessage()
    ]);
}
?> 