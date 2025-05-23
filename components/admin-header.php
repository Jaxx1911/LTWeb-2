<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<header class="bg-white shadow-md fixed top-0 left-0 right-0 z-50">
    <nav class="container mx-auto px-4">
        <div class="flex justify-between items-center h-20">
            <!-- Logo and Brand -->
            <div class="flex items-center">
                <a href="index.php" class="flex items-center">
                    <img src="assets/images/logo.png" alt="J&T Express" class="h-12">
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="index.php" class="text-gray-700 hover:text-[#e30613] px-3 py-2 rounded-md text-sm font-medium">
                    Quản lý đơn hàng
                </a>
                <a href="personnel.php" class="text-gray-700 hover:text-[#e30613] px-3 py-2 rounded-md text-sm font-medium">
                    Quản lý nhân viên
                </a>
                <a href="statistics.php" class="text-gray-700 hover:text-[#e30613] px-3 py-2 rounded-md text-sm font-medium">
                    Thống kê
                </a>
            </div>

            <!-- User Menu -->
            <div class="flex items-center">
                <span class="text-gray-700 mr-4"><?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?></span>
                <a href="logout.php" class="bg-[#e30613] text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-[#c30613]">
                    Đăng xuất
                </a>
            </div>
        </div>
    </nav>
</header> 