<?php
// Get orders with shipper information
$sql = "SELECT o.*, u.name as shipper_name, 
        COALESCE((SELECT status FROM assignment WHERE order_id = o.id ORDER BY assigned_at DESC LIMIT 1), 'new') as current_status,
        (SELECT COUNT(*) FROM assignment WHERE order_id = o.id) as has_history
        FROM orders o 
        LEFT JOIN users u ON o.shipper_id = u.id 
        ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug: Print first order
if (!empty($orders)) {
    error_log('First order data: ' . print_r($orders[0], true));
}

// Helper function to format status badge
function getStatusBadge($status) {
    switch ($status) {
        case 'new':
            return '<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-blue-100 text-blue-800">Mới</span>';
        case 'shipping':
            return '<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-yellow-100 text-yellow-800">Đang giao</span>';
        case 'received':
            return '<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-green-100 text-green-800">Đã nhận</span>';
        default:
            return '<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-gray-100 text-gray-800">Chưa giao</span>';
    }
}

// Helper function to format money
function formatMoney($amount) {
    return number_format($amount, 0, ',', '.') . ' đ';
}
?>

<div class="bg-white rounded-lg shadow-lg">
    <div class="overflow-x-auto min-w-full align-middle">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã ĐH</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người nhận</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Địa chỉ</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shipper</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thu hộ</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                        Không có đơn hàng nào
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?php echo htmlspecialchars($order['code']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($order['name']); ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                            <?php echo htmlspecialchars($order['receive_address']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo htmlspecialchars($order['shipper_name'] ?? 'Chưa phân công'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo formatMoney($order['collection_money']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo getStatusBadge($order['current_status']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium min-w-[120px]">
                            <div class="flex space-x-2">
                                <?php 
                                    // Debug: Print order data
                                    $orderJson = json_encode($order, JSON_HEX_APOS | JSON_HEX_QUOT);
                                    echo "<!-- Debug order data: " . htmlspecialchars($orderJson) . " -->";
                                ?>
                                <button onclick='showViewOrderModal(<?php echo json_encode($order); ?>)' 
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-8 w-8">
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                </button>
                                <button onclick='showEditOrderModal(<?php echo json_encode($order); ?>)' 
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-8 w-8">
                                    <i data-lucide="edit" class="h-4 w-4"></i>
                                </button>
                                <button onclick="showAssignOrderModal(<?php echo $order['id']; ?>)" 
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-8 w-8">
                                    <i data-lucide="user-plus" class="h-4 w-4"></i>
                                </button>
                                <button onclick="deleteOrder(<?php echo $order['id']; ?>)" 
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-8 w-8 text-red-500 hover:text-red-600">
                                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div> 