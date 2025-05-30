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

$message = '';
$message_type = '';
$selected_shipper = null;
$salary_data = null;

// Handle salary update
if ($_POST['action'] === 'update_salary' && isset($_POST['shipper_id']) && isset($_POST['new_salary'])) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET salary = ? WHERE id = ? AND role = 'shipper'");
        $stmt->execute([$_POST['new_salary'], $_POST['shipper_id']]);
        $message = "Cập nhật lương thành công!";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Lỗi cập nhật: " . $e->getMessage();
        $message_type = "error";
    }
}

// Get selected shipper
$shipper_id = $_GET['shipper_id'] ?? $_POST['shipper_id'] ?? null;

// Get all shippers for dropdown
$stmt = $pdo->query("SELECT id, name, code FROM users WHERE role = 'shipper' ORDER BY name");
$all_shippers = $stmt->fetchAll();

// Get date range from request
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Constants for salary calculation
define('BONUS_PER_ORDER', 20000); // Thưởng mỗi đơn thành công: 20,000 VNĐ
define('COLLECTION_COMMISSION_RATE', 0.01); // Hoa hồng 1% trên tổng thu

// If shipper is selected, get their salary data
if ($shipper_id) {
    $salary_sql = "
        SELECT 
            u.id,
            u.name as shipper_name,
            u.code,
            u.area,
            u.salary as base_salary,
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
            END) as collection_in_period,
            COUNT(DISTINCT CASE WHEN a.status = 'received' THEN o.id END) as total_completed_orders,
            COUNT(DISTINCT CASE WHEN a.status = 'shipping' THEN o.id END) as in_progress_orders
        FROM users u
        LEFT JOIN orders o ON o.shipper_id = u.id
        LEFT JOIN assignment a ON a.order_id = o.id
        WHERE u.id = ? AND u.role = 'shipper'
        GROUP BY u.id, u.name, u.code, u.area, u.salary";

    $stmt = $pdo->prepare($salary_sql);
    $stmt->execute([$start_date, $end_date, $start_date, $end_date, $shipper_id]);
    $salary_data = $stmt->fetch();
    
    if ($salary_data) {
        $selected_shipper = $salary_data;
        // Calculate salary components
        $salary_data['order_bonus'] = $salary_data['orders_in_period'] * BONUS_PER_ORDER;
        $salary_data['collection_commission'] = $salary_data['collection_in_period'] * COLLECTION_COMMISSION_RATE;
        $salary_data['total_salary'] = $salary_data['base_salary'] + $salary_data['order_bonus'] + $salary_data['collection_commission'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phiếu lương nhân viên - J&T Express</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
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

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Fix navigation links - remove underline but keep Tailwind colors */
        nav a {
            text-decoration: none !important;
        }

        nav a:hover {
            text-decoration: none !important;
        }

        nav a:visited {
            text-decoration: none !important;
        }

        /* Ensure Tailwind color classes work */
        .text-\[\#e30613\] {
            color: #e30613 !important;
        }

        /* Form styling to replace Bootstrap */
        .form-control {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus {
            color: #212529;
            background-color: #fff;
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        select.form-control {
            cursor: pointer;
        }

        input[type="date"].form-control,
        input[type="number"].form-control,
        select.form-control {
            appearance: none;
            background-image: none;
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Header -->
    <header class="fixed top-0 z-50 w-full border-b bg-white bg-overlay">
        <div class="container mx-auto flex h-16 items-center justify-between px-4">
            <div class="flex items-center gap-6">
                <a class="flex items-center space-x-2" href="/">
                    <img alt="J&T Express Logo" loading="lazy" width="100" height="32" decoding="async" class="h-8" src="/assets/images/logo.png">
                </a>
                <nav class="hidden md:flex items-center gap-6">
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613]" href="./admin.php">Trang chủ</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613]" href="./manager_order.php">Quản lý đơn hàng</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613]" href="./manager_shipper.php">Quản lý nhân viên</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613]" href="./statistics.php">Thống kê</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors text-[#e30613] font-semibold" href="./salary.php">Tính Lương</a>
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
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Phiếu lương nhân viên</h1>
            <p class="text-gray-600">Xem và quản lý phiếu lương của nhân viên giao hàng</p>
        </div>

        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $message_type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Selection Form -->
        <div class="bg-white bg-overlay rounded-lg shadow p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Chọn nhân viên</label>
                    <select name="shipper_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border" required>
                        <option value="">-- Chọn nhân viên --</option>
                        <?php foreach ($all_shippers as $shipper): ?>
                            <option value="<?php echo $shipper['id']; ?>" <?php echo ($shipper_id == $shipper['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($shipper['name'] . ' (' . $shipper['code'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Từ ngày</label>
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Đến ngày</label>
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                </div>
                <button type="submit" class="bg-[#e30613] text-white px-6 py-2 rounded-md hover:bg-[#c30613] transition-colors">
                    <i data-lucide="search" class="h-4 w-4 mr-2 inline"></i>
                    Xem phiếu lương
                </button>
            </form>
        </div>

        <!-- Salary Slip -->
        <?php if ($salary_data): ?>
            <div class="bg-white bg-overlay rounded-lg shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-[#e30613] text-white p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-bold">PHIẾU LƯƠNG</h2>
                            <p class="text-red-100">Kỳ lương: <?php echo date('d/m/Y', strtotime($start_date)); ?> - <?php echo date('d/m/Y', strtotime($end_date)); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-red-100">Mã NV: <?php echo htmlspecialchars($salary_data['code']); ?></p>
                            <p class="text-red-100">Ngày in: <?php echo date('d/m/Y'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Employee Info -->
                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Thông tin nhân viên</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Họ tên:</span>
                                    <span class="font-medium"><?php echo htmlspecialchars($salary_data['shipper_name']); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Mã nhân viên:</span>
                                    <span class="font-medium"><?php echo htmlspecialchars($salary_data['code']); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Khu vực:</span>
                                    <span class="font-medium"><?php echo htmlspecialchars($salary_data['area'] ?: 'Chưa phân khu vực'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Thống kê công việc</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tổng đơn hoàn thành:</span>
                                    <span class="font-medium"><?php echo number_format($salary_data['total_completed_orders']); ?> đơn</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Đơn đang giao:</span>
                                    <span class="font-medium"><?php echo number_format($salary_data['in_progress_orders']); ?> đơn</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Đơn trong kỳ:</span>
                                    <span class="font-medium text-green-600"><?php echo number_format($salary_data['orders_in_period']); ?> đơn</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Salary Details -->
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Chi tiết lương</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Lương cơ bản:</span>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium"><?php echo number_format($salary_data['base_salary']); ?> VNĐ</span>
                                    <button onclick="openEditSalaryModal()" class="text-blue-500 hover:text-blue-700">
                                        <i data-lucide="edit-2" class="h-4 w-4"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600">Số đơn hoàn thành trong kỳ:</span>
                                <span class="font-medium"><?php echo number_format($salary_data['orders_in_period']); ?> đơn</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600">Thưởng giao hàng (20,000/đơn):</span>
                                <span class="font-medium text-green-600">+<?php echo number_format($salary_data['order_bonus']); ?> VNĐ</span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600">Tổng thu hộ trong kỳ:</span>
                                <span class="font-medium"><?php echo number_format($salary_data['collection_in_period']); ?> VNĐ</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600">Hoa hồng thu hộ (1%):</span>
                                <span class="font-medium text-green-600">+<?php echo number_format($salary_data['collection_commission']); ?> VNĐ</span>
                            </div>
                            <div class="flex justify-between py-3 border-t-2 border-[#e30613] bg-red-50 px-4 rounded">
                                <span class="text-lg font-bold text-gray-900">TỔNG LƯƠNG:</span>
                                <span class="text-xl font-bold text-[#e30613]"><?php echo number_format($salary_data['total_salary']); ?> VNĐ</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 p-4 text-center text-sm text-gray-500">
                    <p>Phiếu lương được tạo tự động bởi hệ thống J&T Express</p>
                    <p>Mọi thắc mắc xin liên hệ bộ phận nhân sự</p>
                </div>
            </div>
        <?php elseif ($shipper_id): ?>
            <div class="bg-white bg-overlay rounded-lg shadow p-8 text-center">
                <i data-lucide="user-x" class="h-16 w-16 text-gray-400 mx-auto mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Không tìm thấy dữ liệu</h3>
                <p class="text-gray-500">Nhân viên được chọn không có dữ liệu lương trong kỳ này.</p>
            </div>
        <?php else: ?>
            <div class="bg-white bg-overlay rounded-lg shadow p-8 text-center">
                <i data-lucide="file-text" class="h-16 w-16 text-gray-400 mx-auto mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Chọn nhân viên để xem phiếu lương</h3>
                <p class="text-gray-500">Vui lòng chọn nhân viên từ dropdown ở trên để xem phiếu lương chi tiết.</p>
            </div>
        <?php endif; ?>
    </main>

    <!-- Edit Salary Modal -->
    <div id="editSalaryModal" class="modal">
        <div class="bg-white bg-overlay rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Sửa lương cơ bản</h3>
                <button onclick="closeEditSalaryModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_salary">
                <input type="hidden" name="shipper_id" value="<?php echo $shipper_id; ?>">
                <input type="hidden" name="start_date" value="<?php echo $start_date; ?>">
                <input type="hidden" name="end_date" value="<?php echo $end_date; ?>">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lương cơ bản mới (VNĐ)</label>
                    <input type="number" name="new_salary" value="<?php echo $salary_data['base_salary'] ?? 4000000; ?>" 
                           step="1000" min="0" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closeEditSalaryModal()" 
                            class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors">
                        Hủy
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-[#e30613] text-white px-4 py-2 rounded-md hover:bg-[#c30613] transition-colors">
                        Cập nhật
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

        // Modal functions
        function openEditSalaryModal() {
            document.getElementById('editSalaryModal').classList.add('show');
        }

        function closeEditSalaryModal() {
            document.getElementById('editSalaryModal').classList.remove('show');
        }

        // Close modal when clicking outside
        document.getElementById('editSalaryModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditSalaryModal();
            }
        });
    </script>
</body>
</html> 