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
    if (empty($input['shipper_id']) || empty($input['status'])) {
        echo json_encode(['success' => false, 'message' => 'Shipper ID và status là bắt buộc']);
        exit();
    }
    
    // Validate status
    if (!in_array($input['status'], ['active', 'inactive'])) {
        echo json_encode(['success' => false, 'message' => 'Status không hợp lệ']);
        exit();
    }
    
    // Check if shipper exists
    $check_sql = "SELECT id FROM users WHERE id = ? AND role = 'shipper'";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$input['shipper_id']]);
    
    if (!$check_stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Nhân viên không tồn tại']);
        exit();
    }
    
    // Update shipper status
    $sql = "UPDATE users SET status = ? WHERE id = ? AND role = 'shipper'";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$input['status'], $input['shipper_id']]);
    
    if ($result) {
        $action = $input['status'] === 'active' ? 'kích hoạt' : 'vô hiệu hóa';
        echo json_encode([
            'success' => true,
            'message' => "Đã {$action} nhân viên thành công"
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể cập nhật trạng thái nhân viên']);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage()
    ]);
}
?> 