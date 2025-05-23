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

// Constants for salary calculation
define('BASE_SALARY', 4000000); // Lương cơ bản: 4,000,000 VNĐ
define('BONUS_PER_ORDER', 20000); // Thưởng mỗi đơn thành công: 20,000 VNĐ
define('COLLECTION_COMMISSION_RATE', 0.01); // Hoa hồng 1% trên tổng thu

// Get salary statistics by shipper
$salary_stats_sql = "
    SELECT 
        u.id,
        u.name as shipper_name,
        u.area,
        COUNT(DISTINCT CASE WHEN a.status = 'received' THEN o.id END) as completed_orders,
        COUNT(DISTINCT CASE WHEN a.status = 'shipping' THEN o.id END) as in_progress_orders,
        SUM(CASE WHEN a.status = 'received' THEN o.collection_money ELSE 0 END) as total_collected,
        COUNT(DISTINCT CASE 
            WHEN a.status = 'received' 
            AND o.created_at BETWEEN ? AND ? 
            THEN o.id 
        END) as orders_in_period,
        SUM(CASE 
            WHEN a.status = 'received' 
            AND o.created_at BETWEEN ? AND ? 
            THEN o.collection_money 
            ELSE 0 
        END) as collection_in_period
    FROM users u
    LEFT JOIN orders o ON o.shipper_id = u.id
    LEFT JOIN assignment a ON a.order_id = o.id
    WHERE u.role = 'shipper'
    GROUP BY u.id, u.name, u.area
    ORDER BY completed_orders DESC";

$stmt = $pdo->prepare($salary_stats_sql);
$stmt->execute([$start_date, $end_date, $start_date, $end_date]);
$salary_stats = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tính lương nhân viên - J&T Express</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
<header class="fixed top-0 z-50 w-full border-b bg-white">
        <div class="container flex h-16 items-center justify-between py-4">
            <div class="flex items-center gap-6">
                <a class="flex items-center space-x-2" href="/">
                    <img alt="J&T Express Logo" loading="lazy" width="100" height="32" decoding="async" class="h-8" src="/assets/images/logo.png">
                </a>
                <nav class="hidden md:flex items-center gap-6">
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613] " href="./admin.php">Trang chủ</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613] " href="./manager_order.php">Quản lý đơn hàng</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613]" href="./manager_shipper.php">Quản lý nhân viên</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613]" href="/statistics.php">Thống kê</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613] text-[#e30613] font-semibold" href="/salary.php">Tính Lương</a>
                </nav>
            </div>
            <div class="flex items-center gap-4">
                <button class="inline-flex items-center justify-center gap-2 text-sm font-medium transition-colors hover:bg-accent px-4 py-2 relative h-10 w-10 rounded-full" type="button" id="avatar-menu-button" onclick="toggleDropdown()">
                    <span class="relative flex shrink-0 overflow-hidden rounded-full h-10 w-10">
                    <div class="w-10 h-10 bg-[#ff0008] rounded-full flex items-center justify-center">
                        <p class="text-white text-center text-2xl"><?php echo htmlspecialchars(strtoupper($_SESSION['email'][0]) ?? 'A'); ?></p>
                    </div>    
                    </span>
                </button>
                <div id="avatar-dropdown" class="hidden absolute right-4 top-16 z-50 min-w-[8rem] overflow-hidden rounded-md border bg-white p-1 shadow-md w-56">
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
    </header>

    <main class="container mx-auto px-4 py-8 mt-20">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Bảng tính lương nhân viên</h1>
            <form class="flex gap-4 items-center">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Từ ngày</label>
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Đến ngày</label>
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <button type="submit" class="mt-6 inline-flex justify-center rounded-md border border-transparent bg-[#e30613] py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-[#c30613]">
                    Tính lương
                </button>
            </form>
        </div>

        <!-- Salary Information Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php foreach ($salary_stats as $stat): ?>
                <?php
                    // Calculate salary components
                    $order_bonus = $stat['orders_in_period'] * BONUS_PER_ORDER;
                    $collection_commission = $stat['collection_in_period'] * COLLECTION_COMMISSION_RATE;
                    $total_salary = BASE_SALARY + $order_bonus + $collection_commission;
                ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($stat['shipper_name']); ?></h3>
                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($stat['area'] ?: 'Chưa phân khu vực'); ?></p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Hoạt động
                        </span>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Lương cơ bản:</span>
                            <span class="font-medium"><?php echo number_format(BASE_SALARY); ?> VNĐ</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Số đơn hoàn thành:</span>
                            <span class="font-medium"><?php echo number_format($stat['orders_in_period']); ?> đơn</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Thưởng giao hàng:</span>
                            <span class="font-medium"><?php echo number_format($order_bonus); ?> VNĐ</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tổng thu hộ:</span>
                            <span class="font-medium"><?php echo number_format($stat['collection_in_period']); ?> VNĐ</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Hoa hồng (1%):</span>
                            <span class="font-medium"><?php echo number_format($collection_commission); ?> VNĐ</span>
                        </div>
                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-900">Tổng lương:</span>
                                <span class="text-lg font-bold text-[#e30613]"><?php echo number_format($total_salary); ?> VNĐ</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Detailed Salary Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nhân viên</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khu vực</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đơn hoàn thành</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đơn đang giao</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng thu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng lương</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($salary_stats as $stat): ?>
                        <?php
                            $order_bonus = $stat['orders_in_period'] * BONUS_PER_ORDER;
                            $collection_commission = $stat['collection_in_period'] * COLLECTION_COMMISSION_RATE;
                            $total_salary = BASE_SALARY + $order_bonus + $collection_commission;
                        ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($stat['shipper_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo htmlspecialchars($stat['area'] ?: 'Chưa phân khu vực'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo number_format($stat['orders_in_period']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo number_format($stat['in_progress_orders']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo number_format($stat['collection_in_period']); ?> VNĐ
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#e30613]">
                                <?php echo number_format($total_salary); ?> VNĐ
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        function toggleDropdown() {
            const dropdown = document.getElementById('avatar-dropdown');
            dropdown.classList.toggle('hidden');
        }

    </script>
</body>
</html> 