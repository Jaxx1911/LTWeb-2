<?php
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Mã nhân viên không được cung cấp']);
    exit;
}

$id = $_GET['id'];

$sql = "UPDATE users SET status = 'inactive' WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);

if ($stmt->rowCount() > 0) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Nhân viên đã được xóa thành công']);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy nhân viên']);
}
?>