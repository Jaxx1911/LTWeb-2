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
    $required_fields = ['id', 'code', 'name', 'detail', 'receive_address', 'shipper_id', 'collection_money'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || $_POST[$field] === '') {
            throw new Exception("Field $field is required");
        }
    }

    // Prepare data
    $data = [
        'id' => $_POST['id'],
        'code' => $_POST['code'],
        'name' => $_POST['name'],
        'detail' => $_POST['detail'],
        'receive_address' => $_POST['receive_address'],
        'shipper_id' => $_POST['shipper_id'],
        'collection_money' => $_POST['collection_money'],
        'updated_at' => date('Y-m-d H:i:s')
    ];

    // Start transaction
    $pdo->beginTransaction();

    // Update order
    $sql = "UPDATE orders SET 
            code = :code,
            name = :name,
            detail = :detail,
            receive_address = :receive_address,
            shipper_id = :shipper_id,
            collection_money = :collection_money,
            updated_at = :updated_at
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

    // Check if shipper changed
    $sql = "SELECT user_id FROM assignment WHERE order_id = ? ORDER BY assigned_at DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['id']]);
    $current_assignment = $stmt->fetch();

    if (!$current_assignment || $current_assignment['user_id'] != $_POST['shipper_id']) {
        // Create new assignment
        $assignment_data = [
            'order_id' => $_POST['id'],
            'user_id' => $_POST['shipper_id'],
            'assigned_at' => date('Y-m-d H:i:s'),
            'status' => 'new'
        ];

        $sql = "INSERT INTO assignment (order_id, user_id, assigned_at, status) 
                VALUES (:order_id, :user_id, :assigned_at, :status)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($assignment_data);
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Cập nhật đơn hàng thành công'
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi cập nhật đơn hàng: ' . $e->getMessage()
    ]);
} 