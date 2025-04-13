<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Personnel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #1e88e5;
            --secondary-color: #4527a0;
            --light-color: #ffffff;
            --dark-color: #212121;
        }
        
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: var(--primary-color);
            padding: 0.5rem 1rem;
        }

        .navbar-brand {
            color: var(--light-color) !important;
            font-weight: bold;
            font-size: 1.5rem;
        }

        .nav-link {
            color: var(--light-color) !important;
            padding: 0.5rem 1rem;
            font-size: 1rem;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: bold;
        }

        .header-section {
            background-color: var(--primary-color);
            color: var(--light-color);
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .header-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .header-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .card {
            border: none;
            border-radius: 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: var(--primary-color);
            color: var(--light-color);
            padding: 1rem;
            font-weight: bold;
        }

        .stat-card {
            background-color: var(--primary-color);
            color: var(--light-color);
            padding: 1.5rem;
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 1rem 0;
        }

        .news-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }

        .news-item:last-child {
            border-bottom: none;
        }

        .news-date {
            color: #666;
            font-size: 0.9rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.5rem 1rem;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .quick-actions {
            background-color: #f8f9fa;
            padding: 1rem;
        }

        .section-title {
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">DeliveryManager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Tổng quan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="delivery_personnel.php">Người giao hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">Đơn hàng & Lộ trình</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="schedule.php">Lịch làm việc</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">Báo cáo & Thống kê</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="feedback.php">Phản hồi khách hàng</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header Section -->
    <div class="header-section">
        <div class="container">
            <h1 class="header-title">Hệ thống Quản lý Giao hàng</h1>
            <p class="header-subtitle">Quản lý hiệu quả đội ngũ giao hàng và đơn hàng của bạn</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Statistics Section -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <h5>Tổng đơn hàng</h5>
                    <div class="stat-number">0</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h5>Đơn hoàn thành</h5>
                    <div class="stat-number">0</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h5>Đơn đang xử lý</h5>
                    <div class="stat-number">0</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h5>Tổng nhân viên</h5>
                    <div class="stat-number">0</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column -->
            <div class="col-md-8">
                <!-- Recent Orders -->
                <div class="card">
                    <div class="card-header">Đơn hàng mới nhất</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Khách hàng</th>
                                        <th>Trạng thái</th>
                                        <th>Thời gian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Order data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Active Personnel -->
                <div class="card">
                    <div class="card-header">Nhân viên hoạt động</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tên nhân viên</th>
                                        <th>Số đơn đã giao</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Personnel data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-4">
                <!-- Announcements -->
                <div class="card">
                    <div class="card-header">Thông báo</div>
                    <div class="card-body">
                        <div class="news-item">
                            <h5>Hệ thống nâng cấp bảo trì</h5>
                            <p class="news-date">Ngày: 15/03/2024</p>
                            <p>Hệ thống sẽ được nâng cấp vào ngày 20/03/2024 từ 22:00 đến 02:00.</p>
                        </div>
                        <div class="news-item">
                            <h5>Đào tạo nhân viên mới</h5>
                            <p class="news-date">Ngày: 10/03/2024</p>
                            <p>Khai giảng khóa đào tạo nhân viên mới vào ngày 25/03/2024.</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">Thao tác nhanh</div>
                    <div class="card-body quick-actions">
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Thêm đơn hàng mới
                            </button>
                            <button class="btn btn-primary">
                                <i class="bi bi-people"></i> Quản lý nhân viên
                            </button>
                            <button class="btn btn-primary">
                                <i class="bi bi-calendar"></i> Xem lịch làm việc
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 