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
    $required_fields = ['username', 'email', 'password', 'name', 'code', 'phone'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            echo json_encode(['success' => false, 'message' => "Trường {$field} là bắt buộc"]);
            exit();
        }
    }
    
    // Check if username already exists
    $check_sql = "SELECT id FROM users WHERE username = ? OR email = ? OR code = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$input['username'], $input['email'], $input['code']]);
    
    if ($check_stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Tên đăng nhập, email hoặc mã nhân viên đã tồn tại']);
        exit();
    }
    
    // Hash password
    $hashed_password = password_hash($input['password'], PASSWORD_DEFAULT);
    
    // Insert new shipper
    $sql = "INSERT INTO users (username, email, password, role, name, code, phone, status, area, address, salary, created_at) 
            VALUES (?, ?, ?, 'shipper', ?, ?, ?, 'active', ?, ?, ?, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $input['username'],
        $input['email'],
        $hashed_password,
        $input['name'],
        $input['code'],
        $input['phone'],
        $input['area'] ?? '',
        $input['address'] ?? '',
        $input['salary'] ?? 0
    ]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Thêm nhân viên thành công',
            'shipper_id' => $pdo->lastInsertId()
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể thêm nhân viên']);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi thêm nhân viên: ' . $e->getMessage()
    ]);
}
?> 