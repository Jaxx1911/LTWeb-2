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
    COUNT(*) as total_shippers,
    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_shippers,
    COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive_shippers
    FROM users WHERE role = 'shipper'";
$stmt = $pdo->prepare($stats_sql);
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Get performance statistics
$performance_sql = "SELECT 
    COUNT(DISTINCT a.user_id) as working_shippers,
    COUNT(CASE WHEN a.status = 'received' THEN 1 END) as completed_orders,
    AVG(CASE WHEN a.status = 'received' THEN 1 ELSE 0 END) as completion_rate
    FROM assignment a 
    JOIN users u ON a.user_id = u.id 
    WHERE u.role = 'shipper'";
$stmt = $pdo->prepare($performance_sql);
$stmt->execute();
$performance = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý nhân viên - J&T Express</title>
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
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613]" href="./manager_order.php">Quản lý đơn hàng</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613] text-[#e30613] font-semibold" href="./manager_shipper.php">Quản lý nhân viên</a>
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
                <h1 class="text-3xl font-bold text-gray-900">Quản lý nhân viên</h1>
                <p class="text-gray-600 mt-2">Quản lý thông tin và hiệu suất nhân viên giao hàng</p>
            </div>
            <button onclick="showCreateShipperModal()" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                <i data-lucide="user-plus" class="h-5 w-5 inline mr-2"></i>
                Thêm nhân viên
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white bg-overlay rounded-lg shadow p-6 card-hover transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i data-lucide="users" class="h-6 w-6 text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tổng nhân viên</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_shippers'] ?? 0; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-overlay rounded-lg shadow p-6 card-hover transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i data-lucide="user-check" class="h-6 w-6 text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Đang hoạt động</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['active_shippers'] ?? 0; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-overlay rounded-lg shadow p-6 card-hover transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <i data-lucide="truck" class="h-6 w-6 text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Đang làm việc</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $performance['working_shippers'] ?? 0; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-overlay rounded-lg shadow p-6 card-hover transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i data-lucide="check-circle" class="h-6 w-6 text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Đơn hoàn thành</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $performance['completed_orders'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shippers Table -->
        <div class="bg-white bg-overlay rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">Danh sách nhân viên</h2>
                    <p class="text-sm text-gray-600">Quản lý thông tin nhân viên giao hàng</p>
                </div>
                <div class="flex space-x-2">
                    <button onclick="filterShippers('all')" id="filter-all" class="filter-btn active px-3 py-1 rounded-full text-sm font-medium bg-blue-600 text-white">
                        Tất cả
                    </button>
                    <button onclick="filterShippers('active')" id="filter-active" class="filter-btn px-3 py-1 rounded-full text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300">
                        Hoạt động
                    </button>
                    <button onclick="filterShippers('inactive')" id="filter-inactive" class="filter-btn px-3 py-1 rounded-full text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300">
                        Không hoạt động
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div id="shippers-container">
                    <!-- Shippers will be loaded here via AJAX -->
                    <div class="text-center py-8">
                        <i data-lucide="loader" class="h-8 w-8 text-gray-400 mx-auto mb-4 animate-spin"></i>
                        <p class="text-gray-500">Đang tải dữ liệu...</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Create Shipper Modal -->
    <div id="createShipperModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Thêm nhân viên mới</h3>
                <button onclick="closeCreateShipperModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-6 w-6"></i>
                </button>
            </div>
            <form id="createShipperForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập *</label>
                            <input type="text" name="username" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu *</label>
                            <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên *</label>
                            <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mã nhân viên *</label>
                            <input type="text" name="code" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại *</label>
                            <input type="tel" name="phone" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Khu vực</label>
                            <input type="text" name="area" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                            <textarea name="address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lương cơ bản</label>
                            <input type="number" name="salary" min="0" value="4000000" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                            <textarea name="note" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3 mt-6">
                    <button type="button" onclick="closeCreateShipperModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Hủy
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Thêm nhân viên
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Shipper Modal -->
    <div id="viewShipperModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Chi tiết nhân viên</h3>
                <button onclick="closeViewShipperModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-6 w-6"></i>
                </button>
            </div>
            <div id="shipperDetails">
                <!-- Shipper details will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Edit Shipper Modal -->
    <div id="editShipperModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Chỉnh sửa nhân viên</h3>
                <button onclick="closeEditShipperModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-6 w-6"></i>
                </button>
            </div>
            <form id="editShipperForm">
                <input type="hidden" name="shipper_id" id="edit_shipper_id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập *</label>
                            <input type="text" name="username" id="edit_username" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="email" id="edit_email" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới (để trống nếu không đổi)</label>
                            <input type="password" name="password" id="edit_password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên *</label>
                            <input type="text" name="name" id="edit_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mã nhân viên *</label>
                            <input type="text" name="code" id="edit_code" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại *</label>
                            <input type="tel" name="phone" id="edit_phone" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Khu vực</label>
                            <input type="text" name="area" id="edit_area" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                            <textarea name="address" id="edit_address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lương cơ bản</label>
                            <input type="number" name="salary" id="edit_salary" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                            <textarea name="note" id="edit_note" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3 mt-6">
                    <button type="button" onclick="closeEditShipperModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Hủy
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
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

            // Load shippers on page load
            loadShippers();
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

        // Load shippers function
        async function loadShippers(filter = 'all') {
            try {
                const response = await fetch(`get-shippers.php?filter=${filter}`);
                const result = await response.json();
                
                if (result.success) {
                    displayShippers(result.data);
                } else {
                    document.getElementById('shippers-container').innerHTML = `
                        <div class="text-center py-8">
                            <i data-lucide="alert-circle" class="h-12 w-12 text-red-400 mx-auto mb-4"></i>
                            <p class="text-red-500">Lỗi: ${result.message}</p>
                        </div>
                    `;
                    lucide.createIcons();
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('shippers-container').innerHTML = `
                    <div class="text-center py-8">
                        <i data-lucide="wifi-off" class="h-12 w-12 text-gray-400 mx-auto mb-4"></i>
                        <p class="text-gray-500">Không thể tải dữ liệu</p>
                    </div>
                `;
                lucide.createIcons();
            }
        }

        // Display shippers function
        function displayShippers(shippers) {
            const container = document.getElementById('shippers-container');
            
            if (shippers.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8">
                        <i data-lucide="users" class="h-12 w-12 text-gray-400 mx-auto mb-4"></i>
                        <p class="text-gray-500">Không có nhân viên nào</p>
                    </div>
                `;
                lucide.createIcons();
                return;
            }

            let html = '<div class="space-y-4">';
            shippers.forEach(shipper => {
                const statusClass = shipper.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                const statusText = shipper.status === 'active' ? 'Hoạt động' : 'Không hoạt động';
                
                html += `
                    <div class="border rounded-lg p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="font-medium text-gray-900">${shipper.name}</h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                                        ${statusText}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-1">Mã: ${shipper.code}</p>
                                <p class="text-sm text-gray-600 mb-1">Email: ${shipper.email}</p>
                                <p class="text-sm text-gray-600 mb-1">SĐT: ${shipper.phone || 'Chưa cập nhật'}</p>
                                <p class="text-sm text-gray-500">Khu vực: ${shipper.area || 'Chưa phân công'}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-medium text-blue-600 mb-2">
                                    ${new Intl.NumberFormat('vi-VN').format(shipper.salary || 0)} đ
                                </p>
                                <div class="space-y-2">
                                    <div class="flex space-x-2">
                                        <button onclick="viewShipperDetails(${shipper.id})" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                            <i data-lucide="eye" class="h-3 w-3 inline mr-1"></i>
                                            Xem
                                        </button>
                                        <button onclick="editShipper(${shipper.id})" class="bg-yellow-600 text-white px-3 py-1 rounded text-sm hover:bg-yellow-700">
                                            <i data-lucide="edit" class="h-3 w-3 inline mr-1"></i>
                                            Sửa
                                        </button>
                                    </div>
                                    <button onclick="toggleShipperStatus(${shipper.id}, '${shipper.status}')" class="w-full ${shipper.status === 'active' ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'} text-white px-3 py-1 rounded text-sm">
                                        ${shipper.status === 'active' ? 'Vô hiệu hóa' : 'Kích hoạt'}
                                    </button>
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

        // Filter shippers function
        function filterShippers(filter) {
            // Update button states
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-blue-600', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            });
            
            const activeButton = document.getElementById(`filter-${filter}`);
            activeButton.classList.remove('bg-gray-200', 'text-gray-700');
            activeButton.classList.add('active', 'bg-blue-600', 'text-white');
            
            // Load filtered shippers
            loadShippers(filter);
        }

        // Modal functions
        function showCreateShipperModal() {
            document.getElementById('createShipperModal').classList.remove('hidden');
            document.getElementById('createShipperModal').classList.add('flex');
        }

        function closeCreateShipperModal() {
            document.getElementById('createShipperModal').classList.add('hidden');
            document.getElementById('createShipperModal').classList.remove('flex');
            document.getElementById('createShipperForm').reset();
        }

        function closeViewShipperModal() {
            document.getElementById('viewShipperModal').classList.add('hidden');
            document.getElementById('viewShipperModal').classList.remove('flex');
        }

        function closeEditShipperModal() {
            document.getElementById('editShipperModal').classList.add('hidden');
            document.getElementById('editShipperModal').classList.remove('flex');
            document.getElementById('editShipperForm').reset();
        }

        // Create shipper form submission
        document.getElementById('createShipperForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('create-shipper.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                if (result.success) {
                    alert('Thêm nhân viên thành công!');
                    closeCreateShipperModal();
                    loadShippers(); // Reload shippers
                } else {
                    alert('Lỗi: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi thêm nhân viên');
            }
        });

        // View shipper details function
        async function viewShipperDetails(shipperId) {
            try {
                const response = await fetch(`get-shipper-details.php?id=${shipperId}`);
                const result = await response.json();
                
                if (result.success) {
                    const shipper = result.data;
                    const statusClass = shipper.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                    const statusText = shipper.status === 'active' ? 'Hoạt động' : 'Không hoạt động';
                    
                    document.getElementById('shipperDetails').innerHTML = `
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Họ tên</label>
                                    <p class="text-sm text-gray-900">${shipper.name}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                                        ${statusText}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Mã nhân viên</label>
                                    <p class="text-sm text-gray-900">${shipper.code}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tên đăng nhập</label>
                                    <p class="text-sm text-gray-900">${shipper.username}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <p class="text-sm text-gray-900">${shipper.email}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                                    <p class="text-sm text-gray-900">${shipper.phone || 'Chưa cập nhật'}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Khu vực</label>
                                    <p class="text-sm text-gray-900">${shipper.area || 'Chưa phân công'}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Lương cơ bản</label>
                                    <p class="text-sm text-gray-900">${new Intl.NumberFormat('vi-VN').format(shipper.salary || 0)} đ</p>
                                </div>
                                ${shipper.address ? `
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Địa chỉ</label>
                                    <p class="text-sm text-gray-900">${shipper.address}</p>
                                </div>
                                ` : ''}
                                ${shipper.note ? `
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Ghi chú</label>
                                    <p class="text-sm text-gray-900">${shipper.note}</p>
                                </div>
                                ` : ''}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Ngày tạo</label>
                                    <p class="text-sm text-gray-900">${new Date(shipper.created_at).toLocaleString('vi-VN')}</p>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('viewShipperModal').classList.remove('hidden');
                    document.getElementById('viewShipperModal').classList.add('flex');
                } else {
                    alert('Lỗi: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi tải chi tiết nhân viên');
            }
        }

        // Edit shipper function
        async function editShipper(shipperId) {
            try {
                const response = await fetch(`get-shipper-details.php?id=${shipperId}`);
                const result = await response.json();
                
                if (result.success) {
                    const shipper = result.data;
                    
                    // Fill form with shipper data
                    document.getElementById('edit_shipper_id').value = shipper.id;
                    document.getElementById('edit_username').value = shipper.username;
                    document.getElementById('edit_email').value = shipper.email;
                    document.getElementById('edit_name').value = shipper.name;
                    document.getElementById('edit_code').value = shipper.code;
                    document.getElementById('edit_phone').value = shipper.phone || '';
                    document.getElementById('edit_area').value = shipper.area || '';
                    document.getElementById('edit_address').value = shipper.address || '';
                    document.getElementById('edit_salary').value = shipper.salary || 0;
                    document.getElementById('edit_note').value = shipper.note || '';
                    
                    document.getElementById('editShipperModal').classList.remove('hidden');
                    document.getElementById('editShipperModal').classList.add('flex');
                } else {
                    alert('Lỗi: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi tải thông tin nhân viên');
            }
        }

        // Edit shipper form submission
        document.getElementById('editShipperForm').addEventListener('submit', async function(e) {
                e.preventDefault();
            
                const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('update-shipper.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                if (result.success) {
                    alert('Cập nhật nhân viên thành công!');
                    closeEditShipperModal();
                    loadShippers(); // Reload shippers
                } else {
                    alert('Lỗi: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi cập nhật nhân viên');
            }
        });

        // Toggle shipper status function
        async function toggleShipperStatus(shipperId, currentStatus) {
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            const action = newStatus === 'active' ? 'kích hoạt' : 'vô hiệu hóa';
            
            if (confirm(`Bạn có chắc chắn muốn ${action} nhân viên này?`)) {
                try {
                    const response = await fetch('toggle-shipper-status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            shipper_id: shipperId,
                            status: newStatus
                        })
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        alert(`${action.charAt(0).toUpperCase() + action.slice(1)} nhân viên thành công!`);
                        loadShippers(); // Reload shippers
                    } else {
                        alert('Lỗi: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert(`Có lỗi xảy ra khi ${action} nhân viên`);
                }
            }
        }
</script>
</body>
</html>
