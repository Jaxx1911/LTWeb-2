<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

// Check if user is logged in and is a shipper
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shipper') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$shipper_id = $_SESSION['user_id'];

// Constants for salary calculation (same as salary.php)
define('BASE_SALARY', 4000000); // Lương cơ bản: 4,000,000 VNĐ
define('BONUS_PER_ORDER', 20000); // Thưởng mỗi đơn thành công: 20,000 VNĐ
define('COLLECTION_COMMISSION_RATE', 0.01); // Hoa hồng 1% trên tổng thu

try {
    // Get shipper's base salary from database (fallback to constant if not set)
    $salary_sql = "SELECT salary FROM users WHERE id = :shipper_id";
    $stmt = $pdo->prepare($salary_sql);
    $stmt->execute(['shipper_id' => $shipper_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $base_salary = $user['salary'] ?? BASE_SALARY;
    
    // Get completed orders count and total collection for this month
    $completed_sql = "SELECT 
                        COUNT(*) as completed_orders,
                        SUM(o.collection_money) as total_collection
                      FROM assignment a 
                      JOIN orders o ON a.order_id = o.id
                      WHERE a.user_id = :shipper_id 
                      AND a.status = 'received' 
                      AND MONTH(a.assigned_at) = MONTH(CURRENT_DATE()) 
                      AND YEAR(a.assigned_at) = YEAR(CURRENT_DATE())";
    $stmt = $pdo->prepare($completed_sql);
    $stmt->execute(['shipper_id' => $shipper_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $completed_orders = $result['completed_orders'] ?? 0;
    $total_collection = $result['total_collection'] ?? 0;
    
    // Calculate salary components
    $order_bonus = $completed_orders * BONUS_PER_ORDER;
    $collection_commission = $total_collection * COLLECTION_COMMISSION_RATE;
    $total_salary = $base_salary + $order_bonus + $collection_commission;
    
    echo json_encode([
        'success' => true,
        'data' => [
            'base_salary' => $base_salary,
            'completed_orders' => $completed_orders,
            'bonus_per_order' => BONUS_PER_ORDER,
            'order_bonus' => $order_bonus,
            'total_collection' => $total_collection,
            'commission_rate' => COLLECTION_COMMISSION_RATE,
            'collection_commission' => $collection_commission,
            'total_salary' => $total_salary
        ]
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()]);
}
?> 