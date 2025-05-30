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

// Get date range from request
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get overall statistics
$overall_stats_sql = "
    SELECT 
        COUNT(DISTINCT o.id) as total_orders,
        COUNT(CASE WHEN a.status = 'received' THEN 1 END) as completed_orders,
        COUNT(CASE WHEN a.status = 'shipping' THEN 1 END) as shipping_orders,
        COUNT(CASE WHEN a.status = 'new' THEN 1 END) as new_orders,
        SUM(o.collection_money) as total_collection,
        SUM(CASE WHEN a.status = 'received' THEN o.collection_money ELSE 0 END) as collected_money,
        COUNT(DISTINCT u.id) as active_shippers
    FROM orders o
    LEFT JOIN assignment a ON a.order_id = o.id
    LEFT JOIN users u ON o.shipper_id = u.id AND u.role = 'shipper'
    WHERE o.created_at BETWEEN ? AND ?";

$stmt = $pdo->prepare($overall_stats_sql);
$stmt->execute([$start_date, $end_date]);
$overall_stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Get statistics by shipper
$shipper_stats_sql = "
    SELECT 
        u.id,
        u.name as shipper_name,
        u.area,
        COUNT(DISTINCT o.id) as total_orders,
        COUNT(CASE WHEN a.status = 'received' THEN 1 END) as completed_orders,
        COUNT(CASE WHEN a.status = 'shipping' THEN 1 END) as in_progress_orders,
        SUM(CASE WHEN a.status = 'received' THEN o.collection_money ELSE 0 END) as total_collected,
        ROUND(AVG(CASE WHEN a.status = 'received' THEN 1 ELSE 0 END) * 100, 2) as completion_rate
    FROM users u
    LEFT JOIN orders o ON o.shipper_id = u.id
    LEFT JOIN assignment a ON a.order_id = o.id
    WHERE u.role = 'shipper'
    AND (o.created_at IS NULL OR (o.created_at BETWEEN ? AND ?))
    GROUP BY u.id, u.name, u.area
    ORDER BY total_orders DESC";

$stmt = $pdo->prepare($shipper_stats_sql);
$stmt->execute([$start_date, $end_date]);
$shipper_stats = $stmt->fetchAll();

// Get statistics by area
$area_stats_sql = "
    SELECT 
        COALESCE(u.area, 'Chưa phân công') as area,
        COUNT(DISTINCT o.id) as total_orders,
        COUNT(CASE WHEN a.status = 'received' THEN 1 END) as completed_orders,
        SUM(CASE WHEN a.status = 'received' THEN o.collection_money ELSE 0 END) as total_collected
    FROM orders o
    LEFT JOIN users u ON o.shipper_id = u.id AND u.role = 'shipper'
    LEFT JOIN assignment a ON a.order_id = o.id
    WHERE o.created_at BETWEEN ? AND ?
    GROUP BY u.area
    ORDER BY total_orders DESC";

$stmt = $pdo->prepare($area_stats_sql);
$stmt->execute([$start_date, $end_date]);
$area_stats = $stmt->fetchAll();

// Get daily statistics for chart
$daily_stats_sql = "
    SELECT 
        DATE(o.created_at) as date,
        COUNT(o.id) as orders_count,
        SUM(o.collection_money) as daily_collection
    FROM orders o
    WHERE o.created_at BETWEEN ? AND ?
    GROUP BY DATE(o.created_at)
    ORDER BY date";

$stmt = $pdo->prepare($daily_stats_sql);
$stmt->execute([$start_date, $end_date]);
$daily_stats = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê - J&T Express</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* Background overlay for better readability */
        .bg-overlay {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
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
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613]" href="./manager_order.php">Quản lý đơn hàng</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613]" href="./manager_shipper.php">Quản lý nhân viên</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613] text-[#e30613] font-semibold" href="./statistics.php">Thống kê</a>
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
                <h1 class="text-3xl font-bold text-gray-900">Thống kê</h1>
                <p class="text-gray-600 mt-2">Báo cáo và phân tích hiệu suất hoạt động</p>
            </div>
            <div class="flex items-center space-x-4">
                <form method="GET" class="flex items-center space-x-2 bg-white bg-overlay p-4 rounded-lg shadow">
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span class="text-gray-500">đến</span>
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        <i data-lucide="search" class="h-4 w-4 inline mr-1"></i>
                        Lọc
                    </button>
                </form>
            </div>
        </div>

        <!-- Overall Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white bg-overlay rounded-lg shadow p-6 card-hover transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i data-lucide="package" class="h-6 w-6 text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tổng đơn hàng</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($overall_stats['total_orders']); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-overlay rounded-lg shadow p-6 card-hover transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i data-lucide="check-circle" class="h-6 w-6 text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Đã hoàn thành</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($overall_stats['completed_orders']); ?></p>
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
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($overall_stats['shipping_orders']); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-overlay rounded-lg shadow p-6 card-hover transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <i data-lucide="dollar-sign" class="h-6 w-6 text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tổng thu hộ</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($overall_stats['collected_money']); ?> VNĐ</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Daily Orders Chart -->
            <div class="bg-white bg-overlay rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Đơn hàng theo ngày</h3>
                <canvas id="dailyOrdersChart" width="400" height="200"></canvas>
            </div>

            <!-- Area Distribution Chart -->
            <div class="bg-white bg-overlay rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Phân bố theo khu vực</h3>
                <canvas id="areaChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Performance Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Shipper Performance -->
            <div class="bg-white bg-overlay rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Hiệu suất nhân viên</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nhân viên</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng đơn</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hoàn thành</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tỷ lệ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($shipper_stats as $stat): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($stat['shipper_name']); ?>
                                    <div class="text-xs text-gray-500"><?php echo htmlspecialchars($stat['area'] ?: 'Chưa phân khu vực'); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo number_format($stat['total_orders']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo number_format($stat['completed_orders']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        <?php echo $stat['completion_rate'] >= 80 ? 'bg-green-100 text-green-800' : 
                                                   ($stat['completion_rate'] >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                        <?php echo number_format($stat['completion_rate'], 1); ?>%
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Area Statistics -->
            <div class="bg-white bg-overlay rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Thống kê theo khu vực</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khu vực</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng đơn</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hoàn thành</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thu hộ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($area_stats as $stat): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($stat['area']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo number_format($stat['total_orders']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo number_format($stat['completed_orders']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo number_format($stat['total_collected']); ?> VNĐ
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Lucide icons
            lucide.createIcons();

            // Daily Orders Chart
            const dailyCtx = document.getElementById('dailyOrdersChart').getContext('2d');
            const dailyData = <?php echo json_encode($daily_stats); ?>;
            
            new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: dailyData.map(item => item.date),
                    datasets: [{
                        label: 'Số đơn hàng',
                        data: dailyData.map(item => item.orders_count),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Area Distribution Chart
            const areaCtx = document.getElementById('areaChart').getContext('2d');
            const areaData = <?php echo json_encode($area_stats); ?>;
            
            new Chart(areaCtx, {
                type: 'doughnut',
                data: {
                    labels: areaData.map(item => item.area),
                    datasets: [{
                        data: areaData.map(item => item.total_orders),
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(139, 92, 246, 0.8)',
                            'rgba(236, 72, 153, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
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