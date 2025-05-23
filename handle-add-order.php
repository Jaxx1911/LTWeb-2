<?php
require_once 'config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Validate input
    $required_fields = ['code', 'name', 'detail', 'receive_address', 'shipper_id', 'collection_money'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field $field is required");
        }
    }

    // Prepare data
    $data = [
        'code' => $_POST['code'],
        'name' => $_POST['name'],
        'order_name' => $_POST['order_name'],
        'phone' => $_POST['phone'],
        'detail' => $_POST['detail'],
        'receive_address' => $_POST['receive_address'],
        'shipper_id' => $_POST['shipper_id'],
        'collection_money' => $_POST['collection_money'],
        'created_at' => date('Y-m-d H:i:s')
    ];

    // Start transaction
    $pdo->beginTransaction();

    // Insert order
    $sql = "INSERT INTO orders (code, name, receiver_name, phone, detail, receive_address, shipper_id, collection_money, created_at) 
            VALUES (:code, :order_name, :name, :phone, :detail, :receive_address, :shipper_id, :collection_money, :created_at)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
    $order_id = $pdo->lastInsertId();

    // Create assignment
    $assignment_data = [
        'order_id' => $order_id,
        'user_id' => $_POST['shipper_id'],
        'assigned_at' => date('Y-m-d H:i:s'),
        'status' => 'new'
    ];

    $sql = "INSERT INTO assignment (order_id, user_id, assigned_at, status) 
            VALUES (:order_id, :user_id, :assigned_at, :status)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($assignment_data);

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Thêm đơn hàng thành công',
        'data' => ['id' => $order_id]
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi thêm đơn hàng: ' . $e->getMessage()
    ]);
} 