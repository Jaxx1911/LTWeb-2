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
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý nhân viên giao hàng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
    
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
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613]" href="/manager_order.php">Quản lý đơn hàng</a>
                    <a class="px-4 py-2 text-sm font-medium transition-colors hover:text-[#e30613] text-[#e30613] font-semibold" href="/manager_shipper.php">Quản lý nhân viên</a>
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

    <main class="flex-1" style="margin-top: 64px;">
        <div class="container py-6">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold tracking-tight text-[#e30613]">Quản lý nhân viên giao hàng</h1>
                <div class="flex items-center gap-4">
                    <button onclick="openAddPersonnelModal()" class="bg-[#e30613] text-white hover:bg-[#c00510] transition-colors px-4 py-2 rounded-lg flex items-center">
                        <i data-lucide="plus" class="mr-2 h-4 w-4"></i> Thêm nhân viên mới
                    </button>
                </div>
            </div>

            <div class="mt-6">
                <div id="all" class="tab-content active space-y-6">
                    <?php include 'components/personnel-table.php'; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include 'components/add-personnel-dialog.php'; ?>
    <?php include 'components/edit-personnel-dialog.php'; ?>
    <?php include 'components/view-personnel-dialog.php'; ?>
</body>

<!-- Place all scripts at the end of body -->
<script>
    // Global functions
    function toggleDropdown() {
        const dropdown = document.getElementById('avatar-dropdown');
        dropdown.classList.toggle('hidden');
    }

    function openAddPersonnelModal() {
        const modal = document.getElementById('addPersonnelModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function openViewPersonnelModal(personnelData) {
        const modal = document.getElementById('viewPersonnelModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function openEditPersonnelModal(personnelData) {
        const modal = document.getElementById('editPersonnelModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeAddPersonnelModal() {
        const modal = document.getElementById('addPersonnelModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('addPersonnelForm').reset();
    }

    function closeViewPersonnelModal() {
        const modal = document.getElementById('viewPersonnelModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function closeEditPersonnelModal() {
        const modal = document.getElementById('editPersonnelModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Initialize everything when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        lucide.createIcons();

        // Setup dropdown click outside listener
        document.addEventListener('click', function(event) {
            const button = document.getElementById('avatar-menu-button');
            const dropdown = document.getElementById('avatar-dropdown');
            if (button && dropdown && !button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Setup tab functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tabId = button.getAttribute('data-tab');
                
                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('active', 'bg-white'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Add active class to clicked button and corresponding content
                button.classList.add('active', 'bg-white');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Setup modal functionality
        const modal = document.getElementById('addPersonnelModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeAddPersonnelModal();
                }
            });
        }

        // Setup form submission
        const addPersonnelForm = document.getElementById('addPersonnelForm');
        if (addPersonnelForm) {
            addPersonnelForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());
                
                console.log('Form submitted:', data);
                closeAddPersonnelModal();
            });
        }
    });
</script>
</html>
