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
    $required_fields = ['shipper_id', 'username', 'email', 'name', 'code', 'phone'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            echo json_encode(['success' => false, 'message' => "Trường {$field} là bắt buộc"]);
            exit();
        }
    }
    
    // Check if shipper exists
    $check_sql = "SELECT id FROM users WHERE id = ? AND role = 'shipper'";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$input['shipper_id']]);
    
    if (!$check_stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Nhân viên không tồn tại']);
        exit();
    }
    
    // Check if username, email, or code already exists for other users
    $check_duplicate_sql = "SELECT id FROM users WHERE (username = ? OR email = ? OR code = ?) AND id != ?";
    $check_duplicate_stmt = $pdo->prepare($check_duplicate_sql);
    $check_duplicate_stmt->execute([$input['username'], $input['email'], $input['code'], $input['shipper_id']]);
    
    if ($check_duplicate_stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Tên đăng nhập, email hoặc mã nhân viên đã tồn tại']);
        exit();
    }
    
    // Prepare update query
    $sql = "UPDATE users SET 
            username = ?, 
            email = ?, 
            name = ?, 
            code = ?, 
            phone = ?, 
            area = ?, 
            address = ?, 
            salary = ?, 
            note = ?";
    
    $params = [
        $input['username'],
        $input['email'],
        $input['name'],
        $input['code'],
        $input['phone'],
        $input['area'] ?? '',
        $input['address'] ?? '',
        $input['salary'] ?? 0,
        $input['note'] ?? ''
    ];
    
    // Add password update if provided
    if (!empty($input['password'])) {
        $sql .= ", password = ?";
        $params[] = password_hash($input['password'], PASSWORD_DEFAULT);
    }
    
    $sql .= " WHERE id = ? AND role = 'shipper'";
    $params[] = $input['shipper_id'];
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật nhân viên thành công'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể cập nhật nhân viên']);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi cập nhật nhân viên: ' . $e->getMessage()
    ]);
}
?> 