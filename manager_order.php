<?php
require_once 'config/database.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Get statistics
$stats_sql = "SELECT 
    COUNT(*) as total_orders,
    COUNT(CASE WHEN shipper_id IS NULL THEN 1 END) as unassigned_orders,
    COUNT(CASE WHEN shipper_id IS NOT NULL THEN 1 END) as assigned_orders,
    SUM(collection_money) as total_collection
    FROM orders";
$stmt = $pdo->prepare($stats_sql);
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Get assignment status statistics
$assignment_stats_sql = "SELECT 
    COUNT(CASE WHEN status = 'new' THEN 1 END) as new_orders,
    COUNT(CASE WHEN status = 'shipping' THEN 1 END) as shipping_orders,
    COUNT(CASE WHEN status = 'received' THEN 1 END) as completed_orders
    FROM assignment";
$stmt = $pdo->prepare($assignment_stats_sql);
$stmt->execute();
$assignment_stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Get all shippers for dropdown
$stmt = $pdo->prepare("SELECT id, name FROM users WHERE role = 'shipper' AND status = 'active'");
$stmt->execute();
$shippers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng - J&T Express</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('https://khachhang.jtexpress.vn/img/login_bg.9cffbfc1.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        /* Dropdown styles */
        #avatar-dropdown {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Custom scrollbar */
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

        /* Background overlay for better readability */
        .bg-overlay {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Header -->
    <header class="fixed top-0 z-50 w-full border-b bg-white bg-overlay">
        <div class="container flex h-16 items-center justify-between py-4">
            <div class="flex items-center gap-6">
                <a class="flex items-center space-x-2" href="/">
                    <img alt="J&T Express Logo" loading="lazy" width="100" height="32" decoding="async" class="h-8" src="/assets/images/logo.png">
                </a>
                <nav class="hidden md:flex items-center gap-6">
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613]" href="./admin.php">Trang chủ</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613] text-[#e30613] font-semibold" href="./manager_order.php">Quản lý đơn hàng</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613]" href="./manager_shipper.php">Quản lý nhân viên</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613]" href="./statistics.php">Thống kê</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613]" href="./salary.php">Tính Lương</a>
                </nav>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative">
                <button class="inline-flex items-center justify-center gap-2 text-sm font-medium transition-colors hover:bg-accent px-4 py-2 relative h-10 w-10 rounded-full" type="button" id="avatar-menu-button" onclick="toggleDropdown()">
                    <span class="relative flex shrink-0 overflow-hidden rounded-full h-10 w-10">
                    <div class="w-10 h-10 bg-[#ff0008] rounded-full flex items-center justify-center">
                        <p class="text-white text-center text-2xl"><?php echo htmlspecialchars(strtoupper($_SESSION['email'][0]) ?? 'A'); ?></p>
                    </div>    
                    </span>
                </button>
                    <div id="avatar-dropdown" class="hidden absolute right-0 top-12 z-50 min-w-[8rem] overflow-hidden rounded-md border bg-white p-1 shadow-md w-56">
                    <div class="px-2 py-1.5 text-sm font-semibold">
                        <div class="flex flex-col space-y-1">
                            <p class="text-sm font-medium">Admin</p>
                            <p class="text-xs text-muted-foreground overflow-hidden text-ellipsis"><?php echo htmlspecialchars($_SESSION['email'] ?? 'admin@example.com'); ?></p>
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
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Quản lý đơn hàng</h1>
                <p class="text-gray-600 mt-2">Theo dõi và quản lý tất cả đơn hàng trong hệ thống</p>
            </div>
            <button onclick="showCreateOrderModal()" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                <i data-lucide="plus" class="h-5 w-5 inline mr-2"></i>
                Tạo đơn hàng
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white bg-overlay rounded-lg shadow p-6 card-hover transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i data-lucide="package" class="h-6 w-6 text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tổng đơn hàng</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_orders'] ?? 0; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-overlay rounded-lg shadow p-6 card-hover transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <i data-lucide="clock" class="h-6 w-6 text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Chưa phân công</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['unassigned_orders'] ?? 0; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-overlay rounded-lg shadow p-6 card-hover transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i data-lucide="truck" class="h-6 w-6 text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Đã phân công</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['assigned_orders'] ?? 0; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-overlay rounded-lg shadow p-6 card-hover transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i data-lucide="dollar-sign" class="h-6 w-6 text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tổng thu hộ</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_collection'] ?? 0, 0, ',', '.'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white bg-overlay rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">Danh sách đơn hàng</h2>
                    <p class="text-sm text-gray-600">Quản lý và theo dõi tất cả đơn hàng</p>
                </div>
                <div class="flex space-x-2">
                    <button onclick="filterOrders('all')" id="filter-all" class="filter-btn active px-3 py-1 rounded-full text-sm font-medium bg-blue-600 text-white">
                        Tất cả
                    </button>
                    <button onclick="filterOrders('unassigned')" id="filter-unassigned" class="filter-btn px-3 py-1 rounded-full text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300">
                        Chưa phân công
                    </button>
                    <button onclick="filterOrders('assigned')" id="filter-assigned" class="filter-btn px-3 py-1 rounded-full text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300">
                        Đã phân công
                    </button>
                    <button onclick="filterOrders('completed')" id="filter-completed" class="filter-btn px-3 py-1 rounded-full text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300">
                        Hoàn thành
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div id="orders-container">
                    <!-- Orders will be loaded here via AJAX -->
                    <div class="text-center py-8">
                        <i data-lucide="loader" class="h-8 w-8 text-gray-400 mx-auto mb-4 animate-spin"></i>
                        <p class="text-gray-500">Đang tải dữ liệu...</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Create Order Modal -->
    <div id="createOrderModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Tạo đơn hàng mới</h3>
                <button onclick="closeCreateOrderModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-6 w-6"></i>
                </button>
            </div>
            <form id="createOrderForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mã đơn hàng *</label>
                            <input type="text" name="code" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên hàng hóa *</label>
                            <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Chi tiết</label>
                            <textarea name="detail" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tiền thu hộ *</label>
                            <input type="number" name="collection_money" required min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên người nhận *</label>
                            <input type="text" name="receiver_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại *</label>
                            <input type="tel" name="phone" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ nhận *</label>
                            <textarea name="receive_address" required rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phân công shipper</label>
                            <select name="shipper_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Chọn shipper (tùy chọn)</option>
                                <?php foreach ($shippers as $shipper): ?>
                                    <option value="<?php echo $shipper['id']; ?>"><?php echo htmlspecialchars($shipper['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3 mt-6">
                    <button type="button" onclick="closeCreateOrderModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Hủy
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Tạo đơn hàng
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Order Modal -->
    <div id="viewOrderModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Chi tiết đơn hàng</h3>
                <button onclick="closeViewOrderModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-6 w-6"></i>
                </button>
            </div>
            <div id="orderDetails">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Edit Order Modal -->
    <div id="editOrderModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Chỉnh sửa đơn hàng</h3>
                <button onclick="closeEditOrderModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-6 w-6"></i>
                </button>
            </div>
            <form id="editOrderForm">
                <input type="hidden" name="order_id" id="edit_order_id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mã đơn hàng *</label>
                            <input type="text" name="code" id="edit_code" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên hàng hóa *</label>
                            <input type="text" name="name" id="edit_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Chi tiết</label>
                            <textarea name="detail" id="edit_detail" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tiền thu hộ *</label>
                            <input type="number" name="collection_money" id="edit_collection_money" required min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên người nhận *</label>
                            <input type="text" name="receiver_name" id="edit_receiver_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại *</label>
                            <input type="tel" name="phone" id="edit_phone" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ nhận *</label>
                            <textarea name="receive_address" id="edit_receive_address" required rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phân công shipper</label>
                            <select name="shipper_id" id="edit_shipper_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Chọn shipper (tùy chọn)</option>
                                <?php foreach ($shippers as $shipper): ?>
                                    <option value="<?php echo $shipper['id']; ?>"><?php echo htmlspecialchars($shipper['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3 mt-6">
                    <button type="button" onclick="closeEditOrderModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Hủy
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Assign Order Modal -->
    <div id="assignOrderModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white bg-overlay rounded-lg shadow-xl p-6 w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Phân công đơn hàng</h3>
                <button onclick="closeAssignOrderModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <form id="assignOrderForm">
                <input type="hidden" id="assign_order_id" name="order_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Chọn shipper</label>
                    <select id="assign_shipper_id" name="shipper_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">-- Chọn shipper --</option>
                        <?php foreach ($shippers as $shipper): ?>
                            <option value="<?php echo $shipper['id']; ?>"><?php echo htmlspecialchars($shipper['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closeAssignOrderModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Hủy
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Phân công
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        lucide.createIcons();

            // Load orders on page load
            loadOrders();
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

        // Load orders function
        async function loadOrders(filter = 'all') {
            try {
                const response = await fetch(`get-orders.php?filter=${filter}`);
                const result = await response.json();
                
                if (result.success) {
                    displayOrders(result.data);
                } else {
                    document.getElementById('orders-container').innerHTML = `
                        <div class="text-center py-8">
                            <i data-lucide="alert-circle" class="h-12 w-12 text-red-400 mx-auto mb-4"></i>
                            <p class="text-red-500">Lỗi: ${result.message}</p>
                        </div>
                    `;
                    lucide.createIcons();
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('orders-container').innerHTML = `
                    <div class="text-center py-8">
                        <i data-lucide="wifi-off" class="h-12 w-12 text-gray-400 mx-auto mb-4"></i>
                        <p class="text-gray-500">Không thể tải dữ liệu</p>
                    </div>
                `;
                lucide.createIcons();
            }
        }

        // Display orders function
        function displayOrders(orders) {
            const container = document.getElementById('orders-container');
            
            if (orders.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8">
                        <i data-lucide="package-x" class="h-12 w-12 text-gray-400 mx-auto mb-4"></i>
                        <p class="text-gray-500">Không có đơn hàng nào</p>
                    </div>
                `;
                lucide.createIcons();
                return;
            }

            let html = '<div class="space-y-4">';
            orders.forEach(order => {
                const statusClass = getStatusClass(order.status);
                const statusText = getStatusText(order.status);
                
                html += `
                    <div class="border rounded-lg p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="font-medium text-gray-900">${order.code}</h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                                        ${statusText}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-1">${order.name}</p>
                                <p class="text-sm text-gray-500 mb-1">${order.receive_address}</p>
                                <p class="text-sm text-gray-500">Người nhận: ${order.receiver_name} - ${order.phone}</p>
                                ${order.shipper_name ? `<p class="text-sm text-blue-600 mt-1">Shipper: ${order.shipper_name}</p>` : ''}
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-medium text-green-600 mb-2">
                                    ${new Intl.NumberFormat('vi-VN').format(order.collection_money)} đ
                                </p>
                                <div class="space-y-2">
                                    <div class="flex space-x-2">
                                        <button onclick="viewOrderDetails(${order.id})" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                            <i data-lucide="eye" class="h-3 w-3 inline mr-1"></i>
                                            Xem
                                        </button>
                                        <button onclick="editOrder(${order.id})" class="bg-yellow-600 text-white px-3 py-1 rounded text-sm hover:bg-yellow-700">
                                            <i data-lucide="edit" class="h-3 w-3 inline mr-1"></i>
                                            Sửa
                                        </button>
                                    </div>
                                    ${!order.shipper_id ? `
                                        <button onclick="showAssignModal(${order.id})" class="w-full bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                                            Phân công
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
            container.innerHTML = html;
            lucide.createIcons();
        }

        // Helper functions
        function getStatusClass(status) {
            switch(status) {
                case 'new': return 'bg-blue-100 text-blue-800';
                case 'shipping': return 'bg-yellow-100 text-yellow-800';
                case 'received': return 'bg-green-100 text-green-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }

        function getStatusText(status) {
            switch(status) {
                case 'new': return 'Mới';
                case 'shipping': return 'Đang giao';
                case 'received': return 'Đã giao';
                default: return 'Chưa phân công';
            }
        }

        // Filter orders function
        function filterOrders(filter) {
            // Update button states
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-blue-600', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            });
            
            const activeButton = document.getElementById(`filter-${filter}`);
            activeButton.classList.remove('bg-gray-200', 'text-gray-700');
            activeButton.classList.add('active', 'bg-blue-600', 'text-white');
            
            // Load filtered orders
            loadOrders(filter);
        }

        // Modal functions
        function showCreateOrderModal() {
            document.getElementById('createOrderModal').classList.remove('hidden');
            document.getElementById('createOrderModal').classList.add('flex');
        }

        function closeCreateOrderModal() {
            document.getElementById('createOrderModal').classList.add('hidden');
            document.getElementById('createOrderModal').classList.remove('flex');
            document.getElementById('createOrderForm').reset();
        }

        function closeViewOrderModal() {
            document.getElementById('viewOrderModal').classList.add('hidden');
            document.getElementById('viewOrderModal').classList.remove('flex');
        }

        function closeEditOrderModal() {
            document.getElementById('editOrderModal').classList.add('hidden');
            document.getElementById('editOrderModal').classList.remove('flex');
            document.getElementById('editOrderForm').reset();
        }

        // Create order form submission
        document.getElementById('createOrderForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('create-order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                if (result.success) {
                    alert('Tạo đơn hàng thành công!');
                    closeCreateOrderModal();
                    loadOrders(); // Reload orders
                } else {
                    alert('Lỗi: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi tạo đơn hàng');
            }
        });

        // View order details function
        async function viewOrderDetails(orderId) {
            try {
                const response = await fetch(`get-order-details.php?id=${orderId}`);
                const result = await response.json();
                
                if (result.success) {
                    const order = result.data;
                    document.getElementById('orderDetails').innerHTML = `
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Mã đơn hàng</label>
                                    <p class="text-sm text-gray-900">${order.code}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusClass(order.status)}">
                                        ${getStatusText(order.status)}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tên hàng hóa</label>
                                    <p class="text-sm text-gray-900">${order.name}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tiền thu hộ</label>
                                    <p class="text-sm text-gray-900">${new Intl.NumberFormat('vi-VN').format(order.collection_money)} đ</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Người nhận</label>
                                    <p class="text-sm text-gray-900">${order.receiver_name}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                                    <p class="text-sm text-gray-900">${order.phone}</p>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Địa chỉ nhận</label>
                                    <p class="text-sm text-gray-900">${order.receive_address}</p>
                                </div>
                                ${order.detail ? `
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Chi tiết</label>
                                    <p class="text-sm text-gray-900">${order.detail}</p>
                                </div>
                                ` : ''}
                                ${order.shipper_name ? `
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Shipper phụ trách</label>
                                    <p class="text-sm text-gray-900">${order.shipper_name}</p>
                                </div>
                                ` : ''}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Ngày tạo</label>
                                    <p class="text-sm text-gray-900">${new Date(order.created_at).toLocaleString('vi-VN')}</p>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('viewOrderModal').classList.remove('hidden');
                    document.getElementById('viewOrderModal').classList.add('flex');
                } else {
                    alert('Lỗi: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi tải chi tiết đơn hàng');
            }
        }

        // Edit order function
        async function editOrder(orderId) {
            try {
                const response = await fetch(`get-order-details.php?id=${orderId}`);
                const result = await response.json();
                
                if (result.success) {
                    const order = result.data;
                    
                    // Fill form with order data
                    document.getElementById('edit_order_id').value = order.id;
                    document.getElementById('edit_code').value = order.code;
                    document.getElementById('edit_name').value = order.name;
                    document.getElementById('edit_detail').value = order.detail || '';
                    document.getElementById('edit_collection_money').value = order.collection_money;
                    document.getElementById('edit_receiver_name').value = order.receiver_name;
                    document.getElementById('edit_phone').value = order.phone;
                    document.getElementById('edit_receive_address').value = order.receive_address;
                    document.getElementById('edit_shipper_id').value = order.shipper_id || '';
                    
                    document.getElementById('editOrderModal').classList.remove('hidden');
                    document.getElementById('editOrderModal').classList.add('flex');
                } else {
                    alert('Lỗi: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi tải thông tin đơn hàng');
            }
        }

        // Edit order form submission
        document.getElementById('editOrderForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('update-order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                if (result.success) {
                    alert('Cập nhật đơn hàng thành công!');
                    closeEditOrderModal();
                    loadOrders(); // Reload orders
                } else {
                    alert('Lỗi: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi cập nhật đơn hàng');
            }
        });

        // Placeholder functions for future implementation
        function showAssignModal(orderId) {
            document.getElementById('assign_order_id').value = orderId;
            document.getElementById('assignOrderModal').classList.remove('hidden');
            document.getElementById('assignOrderModal').classList.add('flex');
        }

        // Assign order form submission
        document.getElementById('assignOrderForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('assign-order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                if (result.success) {
                    alert('Phân công đơn hàng thành công!');
                    closeAssignOrderModal();
                    loadOrders(); // Reload orders
                } else {
                    alert('Lỗi: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi phân công đơn hàng');
            }
        });

        // Close assign order modal
        function closeAssignOrderModal() {
            document.getElementById('assignOrderModal').classList.add('hidden');
            document.getElementById('assignOrderModal').classList.remove('flex');
            document.getElementById('assignOrderForm').reset();
        }
    </script>
</body>
</html> 