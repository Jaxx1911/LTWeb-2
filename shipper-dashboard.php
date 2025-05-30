<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is a shipper
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shipper') {
    header("Location: login.php");
    exit();
}

$shipper_id = $_SESSION['user_id'];
$shipper_name = $_SESSION['name'];
$shipper_code = $_SESSION['code'];

// Get shipper statistics
$stats_sql = "SELECT 
    COUNT(CASE WHEN a.status = 'new' THEN 1 END) as new_orders,
    COUNT(CASE WHEN a.status = 'shipping' THEN 1 END) as shipping_orders,
    COUNT(CASE WHEN a.status = 'received' THEN 1 END) as completed_orders,
    COUNT(*) as total_orders
    FROM assignment a 
    WHERE a.user_id = :shipper_id";

$stmt = $pdo->prepare($stats_sql);
$stmt->execute(['shipper_id' => $shipper_id]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Get available orders (not assigned to anyone)
$available_sql = "SELECT o.* FROM orders o 
    WHERE o.shipper_id IS NULL 
    ORDER BY o.created_at DESC 
    LIMIT 10";
$stmt = $pdo->prepare($available_sql);
$stmt->execute();
$available_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get shipper's current orders
$my_orders_sql = "SELECT o.*, a.status, a.assigned_at 
    FROM orders o 
    JOIN assignment a ON o.id = a.order_id 
    WHERE a.user_id = :shipper_id 
    ORDER BY a.assigned_at DESC";
$stmt = $pdo->prepare($my_orders_sql);
$stmt->execute(['shipper_id' => $shipper_id]);
$my_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Shipper - J&T Express</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        /* Custom scrollbar styles */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Dropdown styles */
        #avatar-dropdown {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="fixed top-0 z-50 w-full border-b bg-white">
        <div class="container flex h-16 items-center justify-between py-4">
            <div class="flex items-center gap-6">
                <a class="flex items-center space-x-2" href="/">
                    <img alt="J&T Express Logo" loading="lazy" width="100" height="32" decoding="async" class="h-8" src="/assets/images/logo.png">
                </a>
                <nav class="hidden md:flex items-center gap-6">
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613] text-[#e30613] font-semibold" href="./shipper-dashboard.php">Shipper</a>
                </nav>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <button class="inline-flex items-center justify-center gap-2 text-sm font-medium transition-colors hover:bg-accent px-4 py-2 relative h-10 w-10 rounded-full" type="button" id="avatar-menu-button" onclick="toggleDropdown()">
                        <span class="relative flex shrink-0 overflow-hidden rounded-full h-10 w-10">
                        <div class="w-10 h-10 bg-[#ff0008] rounded-full flex items-center justify-center">
                            <p class="text-white text-center text-2xl"><?php echo htmlspecialchars(strtoupper($shipper_name[0]) ?? 'S'); ?></p>
                        </div>    
                        </span>
                    </button>
                    <div id="avatar-dropdown" class="hidden absolute right-0 top-12 z-50 min-w-[8rem] overflow-hidden rounded-md border bg-white p-1 shadow-md w-56">
                        <div class="px-2 py-1.5 text-sm font-semibold">
                            <div class="flex flex-col space-y-1">
                                <p class="text-sm font-medium">Shipper</p>
                                <p class="text-xs text-muted-foreground overflow-hidden text-ellipsis"><?php echo htmlspecialchars($shipper_name . ' (' . $shipper_code . ')'); ?></p>
                            </div>
                        </div>
                        <div class="my-1 h-px bg-gray-200"></div>
                        <a href="logout.php" class="flex cursor-pointer items-center gap-2 rounded-sm px-2 py-1.5 text-sm hover:bg-gray-100 text-[#e30613]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" x2="9" y1="12" y2="12"></line>
                            </svg>
                            <span>Đăng xuất</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8 mt-20">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6 card-hover transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i data-lucide="package" class="h-6 w-6 text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Đơn mới</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['new_orders'] ?? 0; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 card-hover transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <i data-lucide="truck" class="h-6 w-6 text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Đang giao</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['shipping_orders'] ?? 0; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 card-hover transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i data-lucide="check-circle" class="h-6 w-6 text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Đã giao</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['completed_orders'] ?? 0; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 card-hover transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i data-lucide="bar-chart" class="h-6 w-6 text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tổng đơn</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_orders'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Available Orders -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Đơn hàng có sẵn</h2>
                    <p class="text-sm text-gray-600">Các đơn hàng chưa được phân công</p>
                </div>
                <div class="p-6">
                    <?php if (empty($available_orders)): ?>
                        <div class="text-center py-8">
                            <i data-lucide="package-x" class="h-12 w-12 text-gray-400 mx-auto mb-4"></i>
                            <p class="text-gray-500">Không có đơn hàng mới</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($available_orders as $order): ?>
                                <div class="border rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($order['code']); ?></h3>
                                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($order['name']); ?></p>
                                            <p class="text-sm text-gray-500 mt-1"><?php echo htmlspecialchars($order['receive_address']); ?></p>
                                            <p class="text-sm font-medium text-green-600 mt-1">
                                                Thu hộ: <?php echo number_format($order['collection_money'], 0, ',', '.'); ?> đ
                                            </p>
                                        </div>
                                        <button onclick="acceptOrder(<?php echo $order['id']; ?>)" 
                                            class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                                            Nhận đơn
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- My Orders -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">Đơn hàng của tôi</h2>
                        <p class="text-sm text-gray-600">Quản lý trạng thái đơn hàng</p>
                    </div>
                    <button onclick="showSalaryModal()" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700">
                        <i data-lucide="dollar-sign" class="h-4 w-4 inline mr-2"></i>
                        Phiếu lương
                    </button>
                </div>
                
                <!-- Filter Buttons -->
                <div class="px-6 py-3 border-b border-gray-200 bg-gray-50">
                    <div class="flex space-x-2">
                        <button onclick="filterOrders('all')" id="filter-all" 
                            class="filter-btn active px-3 py-1 rounded-full text-sm font-medium bg-blue-600 text-white">
                            Tất cả
                        </button>
                        <button onclick="filterOrders('new')" id="filter-new" 
                            class="filter-btn px-3 py-1 rounded-full text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300">
                            Mới
                        </button>
                        <button onclick="filterOrders('shipping')" id="filter-shipping" 
                            class="filter-btn px-3 py-1 rounded-full text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300">
                            Đang giao
                        </button>
                        <button onclick="filterOrders('received')" id="filter-received" 
                            class="filter-btn px-3 py-1 rounded-full text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300">
                            Đã giao
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <?php if (empty($my_orders)): ?>
                        <div class="text-center py-8">
                            <i data-lucide="clipboard-list" class="h-12 w-12 text-gray-400 mx-auto mb-4"></i>
                            <p class="text-gray-500">Chưa có đơn hàng nào</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4 max-h-96 overflow-y-auto pr-2" style="scrollbar-width: thin;">
                            <?php foreach ($my_orders as $order): ?>
                                <div class="border rounded-lg p-4 order-item" data-status="<?php echo $order['status']; ?>">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1">
                                            <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($order['code']); ?></h3>
                                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($order['name']); ?></p>
                                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($order['receive_address']); ?></p>
                                        </div>
                                        <div class="text-right">
                                            <?php
                                            $status_class = '';
                                            $status_text = '';
                                            switch($order['status']) {
                                                case 'new':
                                                    $status_class = 'bg-blue-100 text-blue-800';
                                                    $status_text = 'Mới';
                                                    break;
                                                case 'shipping':
                                                    $status_class = 'bg-yellow-100 text-yellow-800';
                                                    $status_text = 'Đang giao';
                                                    break;
                                                case 'received':
                                                    $status_class = 'bg-green-100 text-green-800';
                                                    $status_text = 'Đã giao';
                                                    break;
                                            }
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $status_class; ?>">
                                                <?php echo $status_text; ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <?php if ($order['status'] !== 'received'): ?>
                                        <div class="flex space-x-2">
                                            <?php if ($order['status'] === 'new'): ?>
                                                <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'shipping')" 
                                                    class="bg-yellow-600 text-white px-3 py-1 rounded text-sm hover:bg-yellow-700">
                                                    Bắt đầu giao
                                                </button>
                                            <?php elseif ($order['status'] === 'shipping'): ?>
                                                <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'received')" 
                                                    class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                                                    Đã giao xong
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Salary Modal -->
    <div id="salaryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg max-w-lg w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Phiếu lương tháng này</h3>
                <button onclick="closeSalaryModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-6 w-6"></i>
                </button>
            </div>
            <div id="salaryContent">
                <!-- Salary content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Lucide icons
            lucide.createIcons();
        });

        // Toggle dropdown function
        function toggleDropdown() {
            const dropdown = document.getElementById('avatar-dropdown');
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('avatar-dropdown');
            const button = document.getElementById('avatar-menu-button');
            
            if (dropdown && button && !button.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Accept order function
        async function acceptOrder(orderId) {
            try {
                const response = await fetch('shipper-accept-order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ order_id: orderId })
                });

                const result = await response.json();
                if (result.success) {
                    alert('Đã nhận đơn hàng thành công!');
                    location.reload();
                } else {
                    alert('Lỗi: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi nhận đơn hàng');
            }
        }

        // Update order status function
        async function updateOrderStatus(orderId, status) {
            try {
                const response = await fetch('shipper-update-status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ order_id: orderId, status: status })
                });

                const result = await response.json();
                if (result.success) {
                    alert('Đã cập nhật trạng thái thành công!');
                    location.reload();
                } else {
                    alert('Lỗi: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi cập nhật trạng thái');
            }
        }

        // Show salary modal
        async function showSalaryModal() {
            try {
                const response = await fetch('shipper-salary.php');
                const result = await response.json();
                
                if (result.success) {
                    const salaryData = result.data;
                    document.getElementById('salaryContent').innerHTML = `
                        <div class="space-y-4">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-3 border-b pb-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Lương cơ bản</span>
                                        <span class="text-lg font-medium">${salaryData.base_salary.toLocaleString()} đ</span>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500">Đơn đã giao</p>
                                    <p class="text-lg font-bold text-blue-600">${salaryData.completed_orders}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500">Thưởng giao hàng</p>
                                    <p class="text-lg font-bold text-green-600">${salaryData.order_bonus.toLocaleString()} đ</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500">Tổng thu hộ</p>
                                    <p class="text-lg font-bold text-purple-600">${salaryData.total_collection.toLocaleString()} đ</p>
                                </div>
                                <div class="col-span-3 border-t pt-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Hoa hồng thu hộ (1%)</span>
                                        <span class="text-lg font-medium">${salaryData.collection_commission.toLocaleString()} đ</span>
                                    </div>
                                </div>
                                <div class="col-span-3 bg-green-50 p-3 rounded-lg border-t-2 border-green-500">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-semibold text-gray-900">Tổng lương</span>
                                        <span class="text-2xl font-bold text-green-600">${salaryData.total_salary.toLocaleString()} đ</span>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t pt-4">
                                <div class="text-xs text-gray-500 space-y-1">
                                    <p>• Thưởng giao hàng: ${salaryData.bonus_per_order.toLocaleString()} đ/đơn hoàn thành</p>
                                    <p>• Hoa hồng thu hộ: ${(salaryData.commission_rate * 100)}% trên tổng số tiền thu hộ</p>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    document.getElementById('salaryContent').innerHTML = `
                        <p class="text-red-600">Không thể tải thông tin lương: ${result.message}</p>
                    `;
                }
                
                document.getElementById('salaryModal').classList.remove('hidden');
                document.getElementById('salaryModal').classList.add('flex');
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi tải thông tin lương');
            }
        }

        // Close salary modal
        function closeSalaryModal() {
            document.getElementById('salaryModal').classList.add('hidden');
            document.getElementById('salaryModal').classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('salaryModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSalaryModal();
            }
        });

        // Filter orders function
        function filterOrders(status) {
            const orderItems = document.querySelectorAll('.order-item');
            const filterButtons = document.querySelectorAll('.filter-btn');
            
            // Update button states
            filterButtons.forEach(btn => {
                btn.classList.remove('active', 'bg-blue-600', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            });
            
            const activeButton = document.getElementById(`filter-${status}`);
            activeButton.classList.remove('bg-gray-200', 'text-gray-700');
            activeButton.classList.add('active', 'bg-blue-600', 'text-white');
            
            // Filter orders
            orderItems.forEach(item => {
                if (status === 'all' || item.dataset.status === status) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Check if any orders are visible
            const visibleOrders = Array.from(orderItems).filter(item => item.style.display !== 'none');
            const emptyMessage = document.getElementById('empty-orders-message');
            
            if (visibleOrders.length === 0 && orderItems.length > 0) {
                if (!emptyMessage) {
                    const container = document.querySelector('.space-y-4.max-h-96');
                    const emptyDiv = document.createElement('div');
                    emptyDiv.id = 'empty-orders-message';
                    emptyDiv.className = 'text-center py-8';
                    emptyDiv.innerHTML = `
                        <i data-lucide="search-x" class="h-12 w-12 text-gray-400 mx-auto mb-4"></i>
                        <p class="text-gray-500">Không có đơn hàng nào phù hợp</p>
                    `;
                    container.appendChild(emptyDiv);
                    lucide.createIcons();
                }
                emptyMessage.style.display = 'block';
            } else if (emptyMessage) {
                emptyMessage.style.display = 'none';
            }
        }
    </script>
</body>
</html> 