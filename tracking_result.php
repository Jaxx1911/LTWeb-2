<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả tra cứu - J&T Express</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="assets/images/logo.png" alt="J&T Express" height="50">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Trang chủ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="tracking.php">Tra cứu</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-body p-5">
                            <h2 class="text-center mb-4">Kết quả tra cứu</h2>
                            
                            <?php
                            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                $trackingNumber = $_POST['trackingNumber'];
                                
                                // Sample tracking data (in a real application, this would come from a database)
                                $trackingData = [
                                    'status' => 'Đang giao hàng',
                                    'origin' => 'Hà Nội',
                                    'destination' => 'TP. Hồ Chí Minh',
                                    'estimated_delivery' => '15/04/2024',
                                    'history' => [
                                        ['date' => '14/04/2024 10:30', 'location' => 'Hà Nội', 'status' => 'Đã nhận hàng'],
                                        ['date' => '14/04/2024 15:45', 'location' => 'Hà Nội', 'status' => 'Đang vận chuyển'],
                                        ['date' => '15/04/2024 08:20', 'location' => 'TP. Hồ Chí Minh', 'status' => 'Đang giao hàng']
                                    ]
                                ];
                                ?>
                                
                                <div class="tracking-info mb-4">
                                    <h4>Mã vận đơn: <?php echo htmlspecialchars($trackingNumber); ?></h4>
                                    <div class="alert alert-info">
                                        <strong>Trạng thái:</strong> <?php echo $trackingData['status']; ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Điểm đi:</strong> <?php echo $trackingData['origin']; ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Điểm đến:</strong> <?php echo $trackingData['destination']; ?></p>
                                        </div>
                                    </div>
                                    <p><strong>Dự kiến giao hàng:</strong> <?php echo $trackingData['estimated_delivery']; ?></p>
                                </div>

                                <h5 class="mb-3">Lịch sử vận chuyển</h5>
                                <div class="timeline">
                                    <?php foreach ($trackingData['history'] as $event): ?>
                                        <div class="timeline-item mb-4">
                                            <div class="timeline-date"><?php echo $event['date']; ?></div>
                                            <div class="timeline-content">
                                                <h6><?php echo $event['status']; ?></h6>
                                                <p><?php echo $event['location']; ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="text-center mt-4">
                                    <a href="tracking.php" class="btn btn-primary">Tra cứu đơn hàng khác</a>
                                </div>
                                
                            <?php } else {
                                echo '<div class="alert alert-danger">Không tìm thấy thông tin vận đơn.</div>';
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Thông tin liên hệ</h5>
                    <p>Email: cskh@jtexpress.vn</p>
                    <p>Hotline: 1900 1088</p>
                </div>
                <div class="col-md-4">
                    <h5>Địa chỉ</h5>
                    <p>10 Mai Chí Thọ, P. Thủ Thiêm, Thành phố Thủ Đức, TP. HCM</p>
                </div>
                <div class="col-md-4">
                    <h5>Kết nối với chúng tôi</h5>
                    <div class="social-links">
                        <a href="#" class="text-white me-2">Facebook</a>
                        <a href="#" class="text-white me-2">Twitter</a>
                        <a href="#" class="text-white">Instagram</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 