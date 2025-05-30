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

// Get dashboard statistics
$stats_sql = "SELECT 
    (SELECT COUNT(*) FROM users WHERE role = 'shipper') as total_shippers,
    (SELECT COUNT(*) FROM orders) as total_orders,
    (SELECT COUNT(*) FROM orders WHERE shipper_id IS NOT NULL) as assigned_orders,
    (SELECT COUNT(*) FROM assignment WHERE status = 'received') as completed_orders,
    (SELECT COUNT(*) FROM assignment WHERE status = 'shipping') as shipping_orders,
    (SELECT COUNT(*) FROM assignment WHERE status = 'new') as new_orders";

$stmt = $pdo->prepare($stats_sql);
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Get recent orders
$recent_orders_sql = "SELECT o.*, u.name as shipper_name, 
    COALESCE((SELECT status FROM assignment WHERE order_id = o.id ORDER BY assigned_at DESC LIMIT 1), 'new') as status
    FROM orders o 
    LEFT JOIN users u ON o.shipper_id = u.id 
    ORDER BY o.created_at DESC 
    LIMIT 5";
$stmt = $pdo->prepare($recent_orders_sql);
$stmt->execute();
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - J&T Express</title>
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
        
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        /* Dropdown styles */
        #avatar-dropdown {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
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
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613] text-[#e30613] font-semibold" href="./admin.php">Trang chủ</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613]" href="./manager_order.php">Quản lý đơn hàng</a>
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
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-lg p-8 mb-8 text-white bg-overlay">
            <h1 class="text-3xl font-bold mb-2">Chào mừng trở lại, Admin!</h1>
            <p class="text-blue-100 text-lg">Quản lý hệ thống J&T Express một cách hiệu quả</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white bg-overlay rounded-lg shadow p-6 card-hover transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i data-lucide="users" class="h-6 w-6 text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tổng Shipper</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_shippers'] ?? 0; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-overlay rounded-lg shadow p-6 card-hover transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i data-lucide="package" class="h-6 w-6 text-green-600"></i>
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
                        <i data-lucide="truck" class="h-6 w-6 text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Đang giao</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['shipping_orders'] ?? 0; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-overlay rounded-lg shadow p-6 card-hover transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i data-lucide="check-circle" class="h-6 w-6 text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Đã hoàn thành</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['completed_orders'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Quick Actions -->
            <div class="bg-white bg-overlay rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Thao tác nhanh</h2>
                    <p class="text-sm text-gray-600">Truy cập các chức năng chính</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <a href="./manager_shipper.php" class="feature-card bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 hover:from-blue-100 hover:to-blue-200 transition-all duration-300 cursor-pointer">
                            <div class="flex items-center justify-center w-10 h-10 bg-blue-600 rounded-lg mb-3">
                                <i data-lucide="users" class="h-5 w-5 text-white"></i>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-800 mb-1">Quản lý nhân viên</h3>
                            <p class="text-xs text-gray-600">Thêm, sửa, xóa shipper</p>
                        </a>

                        <a href="./manager_order.php" class="feature-card bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 hover:from-green-100 hover:to-green-200 transition-all duration-300 cursor-pointer">
                            <div class="flex items-center justify-center w-10 h-10 bg-green-600 rounded-lg mb-3">
                                <i data-lucide="package" class="h-5 w-5 text-white"></i>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-800 mb-1">Quản lý đơn hàng</h3>
                            <p class="text-xs text-gray-600">Theo dõi và phân công</p>
                        </a>

                        <a href="./statistics.php" class="feature-card bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 hover:from-purple-100 hover:to-purple-200 transition-all duration-300 cursor-pointer">
                            <div class="flex items-center justify-center w-10 h-10 bg-purple-600 rounded-lg mb-3">
                                <i data-lucide="bar-chart" class="h-5 w-5 text-white"></i>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-800 mb-1">Thống kê</h3>
                            <p class="text-xs text-gray-600">Báo cáo và phân tích</p>
                        </a>

                        <a href="./salary.php" class="feature-card bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-4 hover:from-red-100 hover:to-red-200 transition-all duration-300 cursor-pointer">
                            <div class="flex items-center justify-center w-10 h-10 bg-red-600 rounded-lg mb-3">
                                <i data-lucide="dollar-sign" class="h-5 w-5 text-white"></i>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-800 mb-1">Tính lương</h3>
                            <p class="text-xs text-gray-600">Quản lý lương nhân viên</p>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white bg-overlay rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Đơn hàng gần đây</h2>
                    <p class="text-sm text-gray-600">5 đơn hàng mới nhất</p>
                </div>
                <div class="p-6">
                    <?php if (empty($recent_orders)): ?>
                        <div class="text-center py-8">
                            <i data-lucide="package-x" class="h-12 w-12 text-gray-400 mx-auto mb-4"></i>
                            <p class="text-gray-500">Chưa có đơn hàng nào</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($recent_orders as $order): ?>
                                <div class="border rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($order['code']); ?></h3>
                                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($order['name']); ?></p>
                                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($order['shipper_name'] ?? 'Chưa phân công'); ?></p>
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
                                            <p class="text-sm font-medium text-green-600 mt-1">
                                                <?php echo number_format($order['collection_money'], 0, ',', '.'); ?> đ
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="./manager_order.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Xem tất cả đơn hàng →
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

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
    </script>
</body>
</html> 