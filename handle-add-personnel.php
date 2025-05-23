<?php
require_once 'config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Generate username and password first
    $code = $_POST['id'];
    $username = strtolower($code);
    $password = password_hash($code, PASSWORD_DEFAULT);

    $data = [
        'username' => $username,
        'password' => $password,
        'code' => $code,
        'name' => $_POST['name'],
        'phone' => $_POST['phone'],
        'email' => $_POST['email'] ?? null,
        'area' => $_POST['area'],
        'status' => $_POST['status'],
        'address' => $_POST['address'] ?? null,
        'note' => $_POST['notes'] ?? null,
        'created_at' => date('Y-m-d H:i:s')
    ];

    $sql = "INSERT INTO users (username, password, code, name, phone, email, area, status, address, note, created_at) 
            VALUES (:username, :password, :code, :name, :phone, :email, :area, :status, :address, :note, :created_at)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

    echo json_encode([
        'success' => true,
        'message' => 'Thêm nhân viên thành công'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi thêm nhân viên: ' . $e->getMessage()
    ]);
} 