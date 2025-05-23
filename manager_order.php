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
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <header class="fixed top-0 z-50 w-full border-b bg-white">
        <div class="container flex h-16 items-center justify-between py-4">
            <div class="flex items-center gap-6">
                <a class="flex items-center space-x-2" href="/">
                    <img alt="J&T Express Logo" loading="lazy" width="100" height="32" decoding="async" class="h-8" src="/assets/images/logo.png">
                </a>
                <nav class="hidden md:flex items-center gap-6">
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613] " href="./admin.php">Trang chủ</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613] text-[#e30613] font-semibold" href="./manager_order.php">Quản lý đơn hàng</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613]" href="./manager_shipper.php">Quản lý nhân viên</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613]" href="/statistics.php">Thống kê</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613]" href="/salary.php">Tính Lương</a>
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
            <h1 class="text-2xl font-bold">Quản lý đơn hàng</h1>
            <button onclick="showAddOrderModal()" class="bg-[#e30613] text-white px-4 py-2 rounded-md hover:bg-[#c30613]">
                <i data-lucide="plus" class="inline-block mr-1 h-4 w-4"></i>
                Thêm đơn hàng mới
            </button>
        </div>

        <!-- Orders Table -->
        <?php include 'components/order-table.php'; ?>
        <!-- Add Order Modal -->
        <?php include 'components/add-order-dialog.php'; ?>
        <!-- Edit Order Modal -->
        <?php include 'components/edit-order-dialog.php'; ?>
        <!-- View Order Modal -->
        <?php include 'components/view-order-dialog.php'; ?>
        <!-- Assign Order Modal -->
        <?php include 'components/assign-order-dialog.php'; ?>
    </main>
    
    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Toggle dropdown
        function toggleDropdown() {
            const dropdown = document.getElementById('avatar-dropdown');
            dropdown.classList.toggle('hidden');
        }


        function closeViewOrderModal() {
            const modal = document.getElementById('viewOrderModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Modal functions
        function showAddOrderModal() {
            document.getElementById('addOrderModal').classList.remove('hidden');
            document.getElementById('addOrderModal').classList.add('flex');
        }

        function showEditOrderModal(order) {
            const modal = document.getElementById('editOrderModal');
            document.getElementById('edit_id').value = order.id;
            document.getElementById('edit_code').value = order.code;
            document.getElementById('edit_name').value = order.receiver_name;
            document.getElementById('edit_phone').value = order.phone;
            document.getElementById('edit_order_name').value = order.name;
            document.getElementById('edit_detail').value = order.detail;
            document.getElementById('edit_receive_address').value = order.receive_address;
            document.getElementById('edit_shipper_id').value = order.shipper_id;
            document.getElementById('edit_collection_money').value = order.collection_money;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function showAssignOrderModal(orderId) {
            const modal = document.getElementById('assignOrderModal');
            document.getElementById('assign_order_id').value = orderId;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Handle form submissions
        async function handleAddOrder(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            
            try {
                const response = await fetch('handle-add-order.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                if (result.success) {
                    alert('Thêm đơn hàng thành công');
                    closeModal('addOrderModal');
                    location.reload();
                } else {
                    alert(result.message || 'Có lỗi xảy ra');
                }
            } catch (error) {
                alert('Có lỗi xảy ra');
            }
        }

        async function handleEditOrder(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            
            try {
                const response = await fetch('handle-edit-order.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                if (result.success) {
                    alert('Cập nhật đơn hàng thành công');
                    closeModal('editOrderModal');
                    location.reload();
                } else {
                    alert(result.message || 'Có lỗi xảy ra');
                }
            } catch (error) {
                alert('Có lỗi xảy ra');
            }
        }

        async function handleAssignOrder(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            
            try {
                const response = await fetch('handle-assign-order.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                if (result.success) {
                    alert('Giao đơn hàng thành công');
                    closeModal('assignOrderModal');
                    location.reload();
                } else {
                    alert(result.message || 'Có lỗi xảy ra');
                }
            } catch (error) {
                alert('Có lỗi xảy ra');
            }
        }

        async function deleteOrder(orderId) {
            if (confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')) {
                try {
                    const response = await fetch(`delete-order.php?id=${orderId}`);
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Xóa đơn hàng thành công');
                        location.reload();
                    } else {
                        alert(result.message || 'Có lỗi xảy ra');
                    }
                } catch (error) {
                    alert('Có lỗi xảy ra');
                }
            }
        }
    </script>
</body>
</html> 