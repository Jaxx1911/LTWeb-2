<?php
require_once 'config/database.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required_fields = ['code', 'name', 'receive_address', 'phone', 'receiver_name', 'collection_money'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            echo json_encode(['success' => false, 'message' => "Trường {$field} là bắt buộc"]);
            exit();
        }
    }
    
    // Check if order code already exists
    $check_sql = "SELECT id FROM orders WHERE code = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$input['code']]);
    
    if ($check_stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Mã đơn hàng đã tồn tại']);
        exit();
    }
    
    // Insert new order
    $sql = "INSERT INTO orders (code, name, detail, receive_address, phone, receiver_name, collection_money, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $input['code'],
        $input['name'],
        $input['detail'] ?? '',
        $input['receive_address'],
        $input['phone'],
        $input['receiver_name'],
        $input['collection_money']
    ]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Tạo đơn hàng thành công',
            'order_id' => $pdo->lastInsertId()
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể tạo đơn hàng']);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi tạo đơn hàng: ' . $e->getMessage()
    ]);
}
?> 