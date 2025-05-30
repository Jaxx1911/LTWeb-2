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
    $required_fields = ['order_id', 'code', 'name', 'receive_address', 'phone', 'receiver_name', 'collection_money'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            echo json_encode(['success' => false, 'message' => "Trường {$field} là bắt buộc"]);
            exit();
        }
    }
    
    // Check if order exists
    $check_sql = "SELECT id FROM orders WHERE id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$input['order_id']]);
    
    if (!$check_stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Đơn hàng không tồn tại']);
        exit();
    }
    
    // Check if order code already exists for other orders
    $check_code_sql = "SELECT id FROM orders WHERE code = ? AND id != ?";
    $check_code_stmt = $pdo->prepare($check_code_sql);
    $check_code_stmt->execute([$input['code'], $input['order_id']]);
    
    if ($check_code_stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Mã đơn hàng đã tồn tại']);
        exit();
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    try {
        // Update order
        $sql = "UPDATE orders SET 
                code = ?, 
                name = ?, 
                detail = ?, 
                receive_address = ?, 
                phone = ?, 
                receiver_name = ?, 
                collection_money = ?, 
                shipper_id = ?
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $input['code'],
            $input['name'],
            $input['detail'] ?? '',
            $input['receive_address'],
            $input['phone'],
            $input['receiver_name'],
            $input['collection_money'],
            !empty($input['shipper_id']) ? $input['shipper_id'] : null,
            $input['order_id']
        ]);
        
        // If shipper is assigned and no assignment exists, create one
        if (!empty($input['shipper_id'])) {
            $assignment_check_sql = "SELECT order_id FROM assignment WHERE order_id = ?";
            $assignment_check_stmt = $pdo->prepare($assignment_check_sql);
            $assignment_check_stmt->execute([$input['order_id']]);
            
            if (!$assignment_check_stmt->fetch()) {
                $assignment_sql = "INSERT INTO assignment (order_id, user_id, assigned_at, status) VALUES (?, ?, NOW(), 'new')";
                $assignment_stmt = $pdo->prepare($assignment_sql);
                $assignment_stmt->execute([$input['order_id'], $input['shipper_id']]);
            }
        }
        
        $pdo->commit();
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Cập nhật đơn hàng thành công'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể cập nhật đơn hàng']);
        }
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi cập nhật đơn hàng: ' . $e->getMessage()
    ]);
}
?> 