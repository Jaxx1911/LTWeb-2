<?php
require_once 'config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $data = [
        'code' => $_POST['id'],
        'name' => $_POST['name'],
        'phone' => $_POST['phone'],
        'email' => $_POST['email'] ?? null,
        'area' => $_POST['area'],
        'status' => $_POST['status'],
        'address' => $_POST['address'] ?? null,
        'note' => $_POST['notes'] ?? null,
        'updated_at' => date('Y-m-d H:i:s')
    ];

    $sql = "UPDATE users SET 
            name = :name,
            phone = :phone,
            email = :email,
            area = :area,
            status = :status,
            address = :address,
            note = :note,
            updated_at = :updated_at
            WHERE code = :code";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

    echo json_encode([
            'success' => true,
            'message' => 'Cập nhật thông tin nhân viên thành công'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi cập nhật thông tin nhân viên: ' . $e->getMessage()
    ]);
} 